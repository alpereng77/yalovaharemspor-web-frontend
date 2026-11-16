<?php

namespace App\Services;

use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use App\Models\GiveawayPick;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Random\Randomizer;
use RuntimeException;

class GiveawayService
{
    /** Sitedeki tarihler (maçlar, çekiliş bitişi) Türkiye saatiyle girilir. */
    public const TZ = 'Europe/Istanbul';

    // ------------------------------------------------------- Katılımcı aktarma

    /** @return array{added:int, updated:int, comments:int, skipped:int} */
    public function importComments(Giveaway $giveaway, array $comments, string $source, bool $replace = false): array
    {
        $this->ensureNotDrawn($giveaway);
        $stats = ['added' => 0, 'updated' => 0, 'comments' => 0, 'skipped' => 0];

        $byUser = [];
        foreach ($comments as $c) {
            $u = GiveawayEntry::normalizeUsername($c['username'] ?? null);
            if (!$u) {
                $stats['skipped']++;
                continue;
            }
            $stats['comments']++;
            $time = null;
            if (!empty($c['time'])) {
                try {
                    // Admin tarihleri Türkiye saatiyle girer; yorum saatleri de aynı saate çevrilir.
                    $time = Carbon::parse($c['time'], self::TZ)->setTimezone(self::TZ);
                    $time = Carbon::parse($time->format('Y-m-d H:i:s'));
                } catch (\Throwable) {
                }
            }
            $byUser[$u]['texts'][] = trim((string) $c['text']);
            if ($time && (empty($byUser[$u]['time']) || $time->lt($byUser[$u]['time']))) {
                $byUser[$u]['time'] = $time;
            }
        }

        DB::transaction(function () use ($giveaway, $byUser, $source, $replace, &$stats) {
            if ($replace) {
                // Sadece yorum verisi yenilenir; sitedeki ve uygulamadaki kayıtlar korunur.
                $giveaway->entries()->whereNull('registered_at')->delete();
                $giveaway->entries()->update(['comment_text' => null, 'comment_count' => 0, 'mention_count' => 0, 'first_commented_at' => null]);
            }
            foreach ($byUser as $username => $data) {
                $entry = $giveaway->entries()->firstOrNew(['username' => $username]);
                $texts = $entry->exists ? array_merge(explode("\n", (string) $entry->comment_text), $data['texts']) : $data['texts'];
                $texts = array_values(array_unique(array_filter($texts, fn($t) => $t !== '')));
                $entry->fill([
                    'comment_text' => mb_substr(implode("\n", $texts), 0, 5000),
                    'comment_count' => count($texts),
                ]);
                if (!$entry->exists) {
                    $entry->source = $source;
                }
                $time = $data['time'] ?? null;
                if ($time && (!$entry->first_commented_at || $time->lt($entry->first_commented_at))) {
                    $entry->first_commented_at = $time;
                }
                $entry->exists ? $stats['updated']++ : $stats['added']++;
                $this->evaluate($giveaway, $entry);
                $entry->save();
            }
            if ($replace) {
                // Yeni listede yorumu olmayan (site/uygulama) kayıtları da yeniden değerlendir
                $giveaway->entries()->whereNotIn('username', array_keys($byUser) ?: [''])->each(function (GiveawayEntry $e) use ($giveaway) {
                    $this->evaluate($giveaway, $e);
                    $e->save();
                });
            }
        });

        return $stats;
    }

    /** Şartlara göre katılımcıyı değerlendirir (kaydetmez). */
    public function evaluate(Giveaway $giveaway, GiveawayEntry $entry): void
    {
        $excluded = $giveaway->excluded_list;
        $clubAccount = $this->clubInstagramUsername();
        if ($clubAccount) {
            $excluded[] = $clubAccount;
        }

        // Etiketlenen farklı hesaplar (kendisi, kulüp ve hariç tutulanlar sayılmaz)
        preg_match_all('/@([A-Za-z0-9._]{1,30})/', (string) $entry->comment_text, $m);
        $mentions = collect($m[1])->map(fn($u) => rtrim(strtolower($u), '.'))
            ->reject(fn($u) => $u === $entry->username || in_array($u, $excluded, true))
            ->unique();
        $entry->mention_count = $mentions->count();

        // Sadece otomatik kontrol edilebilen şartlar burada; diğerleri kazanan onayında.
        $hasComment = trim((string) $entry->comment_text) !== '';
        $reason = match (true) {
            in_array($entry->username, $excluded, true) => 'Hariç tutulan hesap',
            $giveaway->requires('site_registration') && !$entry->registered_at => 'Sitede kayıt yok',
            $giveaway->requires('instagram_comment') && !$hasComment => 'Instagram yorumu bulunamadı',
            $giveaway->requires('instagram_comment') && $entry->mention_count < $giveaway->required_mentions
                => "Yetersiz etiket ({$entry->mention_count}/{$giveaway->required_mentions})",
            $giveaway->requires('instagram_comment') && $giveaway->ends_at && $entry->first_commented_at && $entry->first_commented_at->gt($giveaway->ends_at)
                => 'Süre bittikten sonra yorum',
            $giveaway->requires('youtube_subscribe') && $giveaway->requires('site_registration') && !$entry->youtube_handle
                => 'YouTube kanal adı yok',
            default => null,
        };

        $entry->is_eligible = $reason === null;
        $entry->ineligible_reason = $reason;
    }

    /** Şartlar değiştiğinde tüm katılımcıları yeniden değerlendirir. */
    public function reevaluateAll(Giveaway $giveaway): void
    {
        if ($giveaway->picks()->exists()) {
            return; // Çekilişten sonra sonuçlar sabit kalır
        }
        $giveaway->entries()->each(function (GiveawayEntry $e) use ($giveaway) {
            $this->evaluate($giveaway, $e);
            $e->save();
        });
    }

    // ------------------------------------------------------------------ Site kaydı

    /**
     * Sitedeki form ile katılım. Aynı kullanıcı adı tekrar yazarsa kaydı güncellenir.
     * @return array{entry: GiveawayEntry, created: bool}
     */
    public function register(Giveaway $giveaway, string $username, ?string $youtube, string $ip): array
    {
        $this->ensureOpen($giveaway);
        $ipHash = hash('sha256', $ip . '|' . config('app.key'));

        $entry = $giveaway->entries()->firstOrNew(['username' => $username]);
        $created = !$entry->registered_at;

        // Aynı bağlantıdan çok sayıda farklı hesapla katılımı sınırla
        if ($created) {
            $sameIp = $giveaway->entries()->where('registration_ip_hash', $ipHash)->count();
            if ($sameIp >= self::MAX_REGISTRATIONS_PER_IP) {
                throw new RuntimeException('Bu bağlantıdan çok sayıda katılım yapıldı. Lütfen daha sonra tekrar deneyin.');
            }
            $entry->registered_at = now(self::TZ)->format('Y-m-d H:i:s');
            $entry->registration_ip_hash = $ipHash;
            if (!$entry->exists) {
                $entry->source = 'site';
            }
        }
        if ($youtube) {
            $entry->youtube_handle = $youtube;
        }
        $this->evaluate($giveaway, $entry);
        $entry->save();

        return ['entry' => $entry, 'created' => $created];
    }

    public const MAX_REGISTRATIONS_PER_IP = 5;

    private function ensureOpen(Giveaway $giveaway): void
    {
        if (!$giveaway->is_open || $giveaway->picks()->exists()) {
            throw new RuntimeException('Bu çekilişin katılım süresi sona erdi.');
        }
    }

    public function clubInstagramUsername(): ?string
    {
        $url = (string) SiteSetting::get('social_instagram', '');
        return GiveawayEntry::normalizeUsername(preg_replace('#[?].*$#', '', $url));
    }

    // ------------------------------------------------------------------ Çekiliş

    /** Geçerli katılımcılar arasından kazanan + yedekleri kriptografik rastgelelikle seçer. */
    public function draw(Giveaway $giveaway): void
    {
        $this->ensureNotDrawn($giveaway);

        $eligible = $giveaway->entries()->where('is_eligible', true)->pluck('id')->all();
        if (count($eligible) === 0) {
            throw new RuntimeException('Şartları sağlayan katılımcı yok.');
        }

        $order = (new Randomizer())->shuffleArray($eligible); // varsayılan motor: Secure (CSPRNG)
        $take = array_slice($order, 0, $giveaway->winner_count + $giveaway->reserve_count);

        DB::transaction(function () use ($giveaway, $take, $eligible) {
            foreach ($take as $i => $entryId) {
                GiveawayPick::create([
                    'giveaway_id' => $giveaway->id,
                    'giveaway_entry_id' => $entryId,
                    'position' => $i + 1,
                ]);
            }
            $giveaway->update([
                'status' => 'drawn',
                'drawn_at' => now(),
                'eligible_count_at_draw' => count($eligible),
            ]);
        });
    }

    /** Yedekler tükenince kalan katılımcılar arasından bir yedek daha çeker. */
    public function drawExtraReserve(Giveaway $giveaway): GiveawayPick
    {
        if ($giveaway->status !== 'drawn') {
            throw new RuntimeException('Ek yedek sadece onay aşamasında çekilebilir.');
        }
        $picked = $giveaway->picks()->pluck('giveaway_entry_id')->all();
        $pool = $giveaway->entries()->where('is_eligible', true)->whereNotIn('id', $picked ?: [0])->pluck('id')->all();
        if (!$pool) {
            throw new RuntimeException('Çekilecek başka geçerli katılımcı kalmadı.');
        }
        $entryId = $pool[(new Randomizer())->getInt(0, count($pool) - 1)];

        return GiveawayPick::create([
            'giveaway_id' => $giveaway->id,
            'giveaway_entry_id' => $entryId,
            'position' => (int) $giveaway->picks()->max('position') + 1,
        ]);
    }

    /**
     * Kazananın tek bir elle kontrol şartını işaretler. Bir şart "hayır" ise kazanan elenir
     * (sıradaki yedek geçer); tüm şartlar "evet" olunca kazanan otomatik onaylanır.
     */
    public function setCheck(GiveawayPick $pick, string $key, ?string $value): void
    {
        $giveaway = $pick->giveaway;
        if ($giveaway->status !== 'drawn') {
            throw new RuntimeException('Duyurulan çekilişte değişiklik yapılamaz. Önce yayından kaldırın.');
        }
        $manual = $giveaway->manualChecks();
        if (!array_key_exists($key, $manual)) {
            throw new RuntimeException('Bu şart bu çekilişte elle kontrol edilmiyor.');
        }

        $checks = $pick->checks ?? [];
        $checks[$key] = in_array($value, ['yes', 'no'], true) ? $value : null;
        $failed = collect($manual)->filter(fn($label, $k) => ($checks[$k] ?? null) === 'no');
        $allYes = collect($manual)->keys()->every(fn($k) => ($checks[$k] ?? null) === 'yes');

        $pick->update([
            'checks' => $checks,
            'status' => $failed->isNotEmpty() ? 'rejected' : ($allYes ? 'confirmed' : 'pending'),
            'note' => $failed->isNotEmpty() ? 'Sağlanmayan şart: ' . $failed->implode(', ') : null,
            'decided_at' => ($failed->isNotEmpty() || $allYes) ? now() : null,
        ]);
    }

    public function decide(GiveawayPick $pick, string $status, ?string $note = null): void
    {
        if ($pick->giveaway->status !== 'drawn') {
            throw new RuntimeException('Duyurulan çekilişte değişiklik yapılamaz. Önce yayından kaldırın.');
        }
        $pick->update([
            'checks' => $status === 'pending' ? null : $pick->checks,
            'status' => $status,
            'note' => $note ?: ($status === 'rejected' ? 'Takip şartını sağlamıyor' : null),
            'decided_at' => $status === 'pending' ? null : now(),
        ]);
    }

    public function publish(Giveaway $giveaway): void
    {
        $giveaway->load('picks');
        if (!$giveaway->canPublish()) {
            throw new RuntimeException('Duyurmak için tüm kazananların takip kontrolü onaylanmış olmalı.');
        }
        $giveaway->update(['status' => 'completed', 'published_at' => now()]);
    }

    public function unpublish(Giveaway $giveaway): void
    {
        if ($giveaway->status === 'completed') {
            $giveaway->update(['status' => 'drawn', 'published_at' => null]);
        }
    }

    /** Çekilişi geri alır; katılımcılar korunur. Duyurulmuş çekilişte kullanılamaz. */
    public function resetDraw(Giveaway $giveaway): void
    {
        if ($giveaway->status === 'completed') {
            throw new RuntimeException('Duyurulmuş çekiliş sıfırlanamaz. Önce yayından kaldırın.');
        }
        DB::transaction(function () use ($giveaway) {
            $giveaway->picks()->delete();
            $giveaway->update(['status' => 'active', 'drawn_at' => null, 'eligible_count_at_draw' => null]);
        });
    }

    /** Instagram gönderi görselini yerel olarak saklar (Instagram bağlantıları süreli). Yalnızca Instagram CDN'inden indirir. */
    public function storeImage(Giveaway $giveaway, ?string $url): void
    {
        $host = parse_url((string) $url, PHP_URL_HOST) ?: '';
        if (!$url || !str_starts_with($url, 'https://') || !preg_match('/(^|\.)(cdninstagram\.com|fbcdn\.net)$/', $host)) {
            return;
        }
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(15)->get($url);
            $body = $res->successful() ? $res->body() : '';
            $info = $body !== '' ? @getimagesizefromstring($body) : false;
            $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$info['mime'] ?? ''] ?? null;
            if (!$ext || strlen($body) > 8 * 1024 * 1024) {
                return;
            }
            $dir = public_path('assets/giveaways');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $path = 'assets/giveaways/' . $giveaway->slug . '.' . $ext;
            file_put_contents(public_path($path), $body);
            $giveaway->update(['image_path' => $path]);
        } catch (\Throwable) {
            // Görsel olmadan da çekiliş çalışır
        }
    }

    private function ensureNotDrawn(Giveaway $giveaway): void
    {
        if ($giveaway->picks()->exists()) {
            throw new RuntimeException('Çekiliş yapıldıktan sonra katılımcı listesi değiştirilemez. Gerekirse çekilişi sıfırlayın.');
        }
    }
}
