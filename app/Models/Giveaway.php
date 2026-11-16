<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Giveaway extends Model
{
    public const STATUS_LABELS = [
        'draft' => 'Taslak',
        'active' => 'Katılım Açık',
        'drawn' => 'Çekildi · Onay Bekliyor',
        'completed' => 'Kazananlar Duyuruldu',
    ];

    /**
     * Açılıp kapatılabilen sabit şartlar.
     * check: auto   → katılımcı listesinde otomatik doğrulanır (geçerli/geçersiz)
     *        manual → Instagram bu bilgiyi paylaşmadığı için kazanan seçildikten sonra admin kontrol eder
     */
    public const REQUIREMENTS = [
        'site_registration' => ['label' => 'Sitedeki forma Instagram kullanıcı adını yaz', 'check' => 'auto', 'default' => true,
            'help' => 'Katılımcı çekiliş sayfasındaki forma kullanıcı adını yazar; yazmayan çekilişe giremez.'],
        'instagram_comment' => ['label' => 'Gönderiye yorum yap ve arkadaşlarını etiketle', 'check' => 'auto', 'default' => true,
            'help' => 'Yorumlar "Instagram → Site" yer imiyle aktarılır; yorum ve etiket sayısı otomatik kontrol edilir.'],
        'instagram_follow' => ['label' => 'Instagram hesabımızı takip et', 'check' => 'manual', 'default' => true,
            'help' => 'Kazananın takipçilerimiz arasında olduğu kazanan ekranında kontrol edilir.'],
        'instagram_like' => ['label' => 'Çekiliş gönderisini beğen', 'check' => 'manual', 'default' => true,
            'help' => 'Kazanan, gönderinin "beğenenler" listesinde aranır.'],
        'app_download' => ['label' => 'Mobil uygulamamızı indir ve çekilişe uygulamadan katıl', 'check' => 'auto', 'default' => false,
            'help' => 'Katılımcı kullanıcı adını mobil uygulamadaki çekiliş ekranından gönderir. Sadece uygulamadan gelen kayıtlar bu şartı sağlar; bir cihazdan tek hesapla katılınabilir.'],
    ];

    protected $fillable = [
        'title', 'slug', 'prize', 'description', 'rules', 'requirements', 'instagram_post_url', 'instagram_media_id', 'image_path',
        'required_mentions', 'excluded_usernames', 'ends_at', 'winner_count', 'reserve_count', 'status',
        'eligible_count_at_draw', 'drawn_at', 'published_at', 'comments_synced_at',
    ];

    protected $casts = [
        'rules' => 'array',
        'requirements' => 'array',
        'ends_at' => 'datetime',
        'drawn_at' => 'datetime',
        'published_at' => 'datetime',
        'comments_synced_at' => 'datetime',
    ];

    /** Şart açık mı? Kaydedilmemişse varsayılan değer geçerli; listeden kaldırılmış şartlar hiç uygulanmaz. */
    public function requires(string $key): bool
    {
        if (!isset(self::REQUIREMENTS[$key])) {
            return false;
        }
        $saved = $this->requirements ?? [];
        return array_key_exists($key, $saved)
            ? (bool) $saved[$key]
            : (bool) (self::REQUIREMENTS[$key]['default'] ?? false);
    }

    /** Kazanan onayında elle bakılacak şartlar: key => label */
    public function manualChecks(): array
    {
        return collect(self::REQUIREMENTS)
            ->filter(fn($r, $k) => $r['check'] === 'manual' && $this->requires($k))
            ->map(fn($r) => $r['label'])->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function entries(): HasMany
    {
        return $this->hasMany(GiveawayEntry::class);
    }

    public function picks(): HasMany
    {
        return $this->hasMany(GiveawayPick::class)->orderBy('position');
    }

    /** Herkese açık sayfada görünür mü */
    public function scopePublic($query)
    {
        return $query->whereIn('status', ['active', 'drawn', 'completed']);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'active' && (!$this->ends_at || $this->ends_at->gt(self::nowLocal()));
    }

    /** Türkiye saatindeki "şimdi"; bitiş tarihi Türkiye saatiyle kaydedilir. */
    public static function nowLocal(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse(now(\App\Services\GiveawayService::TZ)->format('Y-m-d H:i:s'));
    }

    /** JavaScript geri sayımı için saat dilimli ISO tarih. */
    public function getEndsAtIsoAttribute(): ?string
    {
        return $this->ends_at
            ? \Carbon\Carbon::parse($this->ends_at->format('Y-m-d H:i:s'), \App\Services\GiveawayService::TZ)->toIso8601String()
            : null;
    }

    /** Hariç tutulan kullanıcı adları (küçük harf, @ olmadan) */
    public function getExcludedListAttribute(): array
    {
        return collect(preg_split('/[\s,;]+/', (string) $this->excluded_usernames))
            ->map(fn($u) => GiveawayEntry::normalizeUsername($u))
            ->filter()->unique()->values()->all();
    }

    /**
     * Kazanan listesi: reddedilmemiş seçimlerden sırayla ilk "winner_count" kadarı.
     * Reddedilen bir kazananın yerine sıradaki yedek kendiliğinden geçer.
     */
    public function currentWinners(): Collection
    {
        return $this->picks->where('status', '!=', 'rejected')->take($this->winner_count)->values();
    }

    public function currentReserves(): Collection
    {
        return $this->picks->where('status', '!=', 'rejected')->slice($this->winner_count)->values();
    }

    public function canPublish(): bool
    {
        $winners = $this->currentWinners();
        return $this->status === 'drawn'
            && $winners->count() === (int) $this->winner_count
            && $winners->every(fn($p) => $p->status === 'confirmed');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path && is_file(public_path($this->image_path)) ? asset($this->image_path) : null;
    }

    public function getInstagramProfileUrlAttribute(): ?string
    {
        return SiteSetting::get('social_instagram') ?: null;
    }
}
