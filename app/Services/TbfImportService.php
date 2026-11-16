<?php

namespace App\Services;

use App\Models\BasketballMatch;
use App\Models\BotLog;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Standing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * TBF verisini içe aktarır.
 *
 * Veri, yöneticinin kendi tarayıcısında açtığı tbf.org.tr sayfasından
 * yer imi (bookmarklet) ile alınıp buraya gönderilir. Sunucu TBF'ye istek atmaz.
 *
 * Beklenen yük: TBF API yanıtlarının "data" alanları
 *   standings → /api/League/get-standings-table
 *   matches   → /api/Team/get-team-detail-matches-by-season-and-league
 *   stats     → /api/Team/get-team-detail-statistic
 *   players   → /api/Team/get-team-active-player-list-by-season-and-league
 */
class TbfImportService
{
    public const SEASON = '2026-2027';
    public const STANDING_SEASON = 'TBL 26/27';

    public function __construct(private int $teamId) {}

    public function import(array $payload): array
    {
        $summary = DB::transaction(function () use ($payload) {
            return [
                'standing' => $this->importStanding($payload['standings'] ?? null),
                'matches' => $this->importMatches($payload['matches']['maclar'] ?? []),
                'players' => $this->importPlayers(
                    $payload['stats']['playersTotal'] ?? [],
                    $payload['players'] ?? []
                ),
            ];
        });

        $s = $summary['standing'];
        $message = sprintf(
            'TBF içe aktarma: %s, %d maç (%d yeni, %d mükerrer silindi), %d oyuncu istatistiği (%d yeni oyuncu).',
            $s ? "{$s['rank']}. sıra {$s['won']}G-{$s['lost']}M" : 'puan durumu yok',
            $summary['matches']['updated'] + $summary['matches']['created'],
            $summary['matches']['created'],
            $summary['matches']['duplicates_removed'],
            $summary['players']['updated'] + $summary['players']['created'],
            $summary['players']['created']
        );

        BotLog::create([
            'action' => 'tbf_browser_import',
            'status' => 'success',
            'message' => $message,
            'items_count' => $summary['matches']['updated'] + $summary['matches']['created'] + $summary['players']['updated'] + $summary['players']['created'],
        ]);

        $summary['photos_needed'] = $summary['players']['photos_needed'];
        unset($summary['players']['photos_needed']);

        return ['message' => $message] + $summary;
    }

    // ---------------------------------------------------------------- Standing

    private function importStanding($standingsData): ?array
    {
        $row = null;
        foreach ((array) $standingsData as $group) {
            foreach ($group['standings'] ?? [] as $r) {
                if ((int) ($r['takimIslemId'] ?? 0) === $this->teamId) {
                    $row = $r;
                    break 2;
                }
            }
        }
        if (!$row) {
            return null;
        }

        $values = [
            'season' => self::STANDING_SEASON,
            'rank' => (int) $row['sira'],
            'played' => (int) $row['o'],
            'won' => (int) $row['g'],
            'lost' => (int) $row['m'],
            'points' => (int) $row['puan'],
            'points_for' => (int) $row['a'],
            'points_against' => (int) $row['y'],
        ];

        // Ana sayfa ve admin en son kaydı okur; tek kayıt kalsın.
        $standing = Standing::latest('id')->first() ?? new Standing();
        $standing->fill($values)->save();
        Standing::where('id', '!=', $standing->id)->delete();

        return $values;
    }

    // ----------------------------------------------------------------- Matches

    private function importMatches(array $games): array
    {
        $result = ['updated' => 0, 'created' => 0, 'duplicates_removed' => 0];
        if (!$games) {
            return $result;
        }

        $claimed = [];

        foreach ($games as $g) {
            if (empty($g['matchId']) || empty($g['tarih'])) {
                continue;
            }

            $date = Carbon::parse($g['tarih']);
            $isHome = (int) ($g['takimAId'] ?? 0) === $this->teamId;
            $played = !empty($g['isPlayed']) && $g['skorA'] !== '' && $g['skorB'] !== '';

            $match = BasketballMatch::where('tbf_match_id', (string) $g['matchId'])->first()
                ?? $this->findUnlinkedMatch($date, $isHome, $claimed);

            $values = [
                'tbf_match_id' => (string) $g['matchId'],
                'round' => (int) $g['formattedWeek'] ?: null,
                'match_date' => $date,
                'is_home' => $isHome,
                'venue' => $g['salon'] ?? null,
                'status' => $played ? 'played' : 'upcoming',
                'home_score' => $played ? (int) $g['skorA'] : null,
                'away_score' => $played ? (int) $g['skorB'] : null,
            ];

            if ($match) {
                // Takım adları yöneticinin düzenlediği haliyle kalır.
                $match->fill($values)->save();
                $result['updated']++;
            } else {
                $match = BasketballMatch::create($values + [
                    'home_team' => $this->teamName($g['takimA'], $isHome),
                    'away_team' => $this->teamName($g['takimB'], !$isHome),
                    'is_next_match' => false,
                ]);
                $result['created']++;
            }
            $claimed[] = $match->id;
        }

        // TBF'ye bağlanamayan ve bir TBF maçıyla aynı günlerde kalan kayıtlar mükerrerdir.
        $linked = BasketballMatch::whereIn('id', $claimed)->get(['match_date']);
        foreach (BasketballMatch::whereNull('tbf_match_id')->get() as $orphan) {
            $dup = $linked->contains(fn($m) => abs($m->match_date->diffInHours($orphan->match_date, false)) <= 72);
            if ($dup) {
                $orphan->delete();
                $result['duplicates_removed']++;
            }
        }

        // Sıradaki maç: oynanmamış en yakın maç.
        BasketballMatch::query()->update(['is_next_match' => false]);
        BasketballMatch::where('status', 'upcoming')->orderBy('match_date')->limit(1)->update(['is_next_match' => true]);

        return $result;
    }

    /** TBF kimliği olmayan, aynı iç/dış saha durumundaki en yakın tarihli maç (±3 gün). */
    private function findUnlinkedMatch(Carbon $date, bool $isHome, array $claimed): ?BasketballMatch
    {
        return BasketballMatch::whereNull('tbf_match_id')
            ->where('is_home', $isHome)
            ->whereNotIn('id', $claimed ?: [0])
            ->whereBetween('match_date', [$date->copy()->subDays(3), $date->copy()->addDays(3)])
            ->get()
            ->sortBy(fn($m) => [abs($m->match_date->diffInMinutes($date, false)), $m->id])
            ->first();
    }

    private function teamName(string $tbfName, bool $isOurs): string
    {
        return $isOurs ? 'Yalova Harem Spor' : $this->turkishTitle($tbfName);
    }

    // ----------------------------------------------------------------- Players

    private function importPlayers(array $totals, array $activeList): array
    {
        $result = ['updated' => 0, 'created' => 0, 'photos_needed' => []];
        $profiles = collect($activeList)->keyBy(fn($p) => (string) $p['personId']);
        $nextOrder = (int) Player::max('order') + 1;

        foreach ($totals as $t) {
            $tbfId = (string) $t['code'];
            $profile = $profiles->get($tbfId);
            $player = $this->findPlayer($tbfId, (string) $t['oyuncuNo'], $t['firstName'], $t['lastName']);

            if ($player) {
                $player->update(['tbf_id' => $tbfId, 'jersey_number' => (string) $t['oyuncuNo']]);
                $result['updated']++;
            } else {
                $player = Player::create([
                    'tbf_id' => $tbfId,
                    'first_name' => $t['firstName'],
                    'last_name' => $t['lastName'],
                    'jersey_number' => (string) $t['oyuncuNo'],
                    'height' => !empty($profile['height']) ? number_format($profile['height'] / 100, 2) . ' m' : null,
                    'birth_date' => !empty($profile['birthDay']) ? substr($profile['birthDay'], 0, 4) : null,
                    'nationality' => !empty($profile['country']) ? $this->turkishTitle($profile['country']) : null,
                    'image_url' => null, // fotoğraf ayrı adımda yerel olarak kaydedilir
                    'is_active' => $profile !== null,
                    'order' => $nextOrder++,
                ]);
                $result['created']++;
            }

            $this->saveStats($player, $t);

            $photo = $profile['playerPhoto'] ?? ($t['resim'] ?? null);
            if ($photo && $this->needsPhoto($player, $photo)) {
                $result['photos_needed'][] = ['tbf_id' => $tbfId, 'url' => $photo];
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------ Photos

    /** Herkese ortak gri silüet görseli (yer tutucu). */
    private const PLACEHOLDER_MD5 = '891f030ef370200f1bcd6a97eab5bac6';

    /**
     * Yöneticinin yüklediği gerçek fotoğraflar korunur. Sadece fotoğrafı olmayan,
     * silüet kullanan, uzak bağlantılı veya TBF fotoğrafı değişmiş oyuncular güncellenir.
     */
    public function needsPhoto(Player $player, string $photoUrl): bool
    {
        $current = (string) $player->image_url;
        if ($current === '' || preg_match('#^https?://#', $current)) {
            return true;
        }
        $file = public_path($current);
        if (!is_file($file) || md5_file($file) === self::PLACEHOLDER_MD5) {
            return true;
        }
        $tbfPrefix = 'assets/players/tbf_' . $player->tbf_id . '_';
        return str_starts_with($current, $tbfPrefix) && $current !== $this->photoPath($player->tbf_id, $photoUrl, pathinfo($current, PATHINFO_EXTENSION));
    }

    public function photoPath(string $tbfId, string $photoUrl, string $ext): string
    {
        return 'assets/players/tbf_' . $tbfId . '_' . substr(md5($photoUrl), 0, 10) . '.' . $ext;
    }

    /** Tarayıcıdan gelen fotoğrafı doğrular, kaydeder ve oyuncuya bağlar. */
    public function savePhoto(string $tbfId, string $photoUrl, string $binary): string
    {
        $player = Player::where('tbf_id', $tbfId)->firstOrFail();
        if (!$this->needsPhoto($player, $photoUrl)) {
            return $player->image_url;
        }

        $info = @getimagesizefromstring($binary);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$info['mime'] ?? ''] ?? null;
        if (!$info || !$ext || strlen($binary) > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('Geçersiz görsel.');
        }

        $path = $this->photoPath($tbfId, $photoUrl, $ext);
        file_put_contents(public_path($path), $binary);

        $old = (string) $player->image_url;
        $player->update(['image_url' => $path]);
        // Önceki TBF fotoğrafının eski sürümünü temizle (yönetici fotoğraflarına dokunma).
        if (str_starts_with($old, 'assets/players/tbf_' . $tbfId . '_') && $old !== $path && is_file(public_path($old))) {
            @unlink(public_path($old));
        }

        return $path;
    }

    private function findPlayer(string $tbfId, string $jersey, string $first, string $last): ?Player
    {
        if ($p = Player::where('tbf_id', $tbfId)->first()) {
            return $p;
        }
        $lastKey = $this->fold($last);
        $fullKey = $this->fold("$first $last");
        $candidates = Player::all();

        return $candidates->first(fn($p) => $p->jersey_number === $jersey && $this->fold($p->last_name) === $lastKey)
            ?? $candidates->first(fn($p) => $this->fold($p->full_name) === $fullKey)
            ?? $candidates->first(fn($p) => $this->fold($p->last_name) === $lastKey
                && str_contains($this->fold($p->first_name), $this->fold($first)));
    }

    private function saveStats(Player $player, array $t): void
    {
        $gp = max(1, (int) $t['macSayisi']);
        $avg = fn($key) => round(((float) ($t[$key] ?? 0)) / $gp, 2);
        $pct = fn($key) => isset($t[$key]) && $t[$key] !== '' ? number_format((float) $t[$key], 1) . '%' : null;

        PlayerStat::updateOrCreate(
            ['player_id' => $player->id, 'season' => self::SEASON],
            [
                'games_played' => (int) $t['macSayisi'],
                'minutes_avg' => round($this->seconds($t['sureToplam'] ?? '0:0:0') / 60 / $gp, 2),
                'points_avg' => $avg('sayiToplam'),
                'rebounds_avg' => $avg('trToplam'),
                'assists_avg' => $avg('asistToplam'),
                'steals_avg' => $avg('topCalmaToplam'),
                'blocks_avg' => $avg('blokToplam'),
                'turnovers_avg' => $avg('topKaybiToplam'),
                'fouls_avg' => $avg('faulToplam'),
                'efficiency_avg' => $avg('vpToplam'),
                'fg_percentage' => $pct('ikilikYuzdesi'),
                'three_point_percentage' => $pct('uclukYuzdesi'),
                'ft_percentage' => $pct('serbestAtisYuzdesi'),
                'total_points' => (int) $t['sayiToplam'],
                'total_rebounds' => (int) $t['trToplam'],
                'total_assists' => (int) $t['asistToplam'],
            ]
        );
    }

    // ----------------------------------------------------------------- Helpers

    private function seconds(string $hms): int
    {
        $parts = array_map('intval', explode(':', $hms));
        while (count($parts) < 3) {
            array_unshift($parts, 0);
        }
        return $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
    }

    /** Karşılaştırma için: küçük harf, Türkçe karakterler sadeleştirilmiş. */
    private function fold(string $s): string
    {
        $s = strtr($s, ['İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ş' => 's', 'ş' => 's', 'Ğ' => 'g', 'ğ' => 'g',
            'Ü' => 'u', 'ü' => 'u', 'Ö' => 'o', 'ö' => 'o', 'Ç' => 'c', 'ç' => 'c']);
        return trim(preg_replace('/\s+/', ' ', mb_strtolower($s)));
    }

    private function turkishTitle(string $s): string
    {
        $lower = mb_strtolower(strtr($s, ['I' => 'ı', 'İ' => 'i']));
        return preg_replace_callback('/(^|[\s\.\-\/])(\p{L})/u', function ($m) {
            $up = $m[2] === 'i' ? 'İ' : mb_strtoupper($m[2]);
            return $m[1] . $up;
        }, $lower);
    }
}
