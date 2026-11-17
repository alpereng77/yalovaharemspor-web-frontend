<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BasketballMatch;
use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use App\Models\MediaArticle;
use App\Models\Player;
use App\Models\SiteSetting;
use App\Models\Sponsor;
use App\Models\Standing;
use App\Models\TeamLogo;
use App\Models\TechnicalStaff;
use App\Models\TicketPoint;
use App\Services\GiveawayService;
use Carbon\Carbon;

/**
 * Mobil uygulama API'sinin JSON biçimleri tek yerde.
 * Tarihler Türkiye saatiyle girildiği için +03:00 ile ISO 8601 olarak verilir.
 */
class Presenter
{
    /** API'ye açılan site ayarları. Bot anahtarları, çerezler vb. asla buraya eklenmez. */
    public const PUBLIC_SETTINGS = [
        'club_name', 'city_name', 'site_title', 'meta_description',
        'hero_title_1', 'hero_title_2', 'hero_description', 'hero_image',
        'about_title', 'about_image_1', 'about_image_2',
        'identity_green_title', 'identity_green_text', 'identity_red_title', 'identity_red_text',
        'identity_mascot_title', 'identity_mascot_text',
        'social_instagram', 'social_x', 'social_youtube',
        'footer_hashtag', 'footer_copyright', 'footer_credit_name', 'footer_credit_url',
        'app_store_url', 'play_store_url',
    ];

    private const IMAGE_SETTINGS = ['hero_image', 'about_image_1', 'about_image_2'];

    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return preg_match('#^https?://#i', $path) ? $path : asset(ltrim($path, '/'));
    }

    public static function date(?Carbon $value, bool $dateOnly = false): ?string
    {
        if (!$value) {
            return null;
        }
        $local = Carbon::parse($value->format('Y-m-d H:i:s'), GiveawayService::TZ);
        return $dateOnly ? $local->format('Y-m-d') : $local->toIso8601String();
    }

    public static function club(): array
    {
        $raw = SiteSetting::whereIn('key', self::PUBLIC_SETTINGS)->pluck('value', 'key');
        $out = [];
        foreach (self::PUBLIC_SETTINGS as $key) {
            $value = $raw[$key] ?? null;
            if (is_string($value)) {
                $value = str_replace('\n', "\n", $value);
            }
            $out[$key] = in_array($key, self::IMAGE_SETTINGS, true) ? self::url($value) : ($value === '' ? null : $value);
        }
        $out['logo'] = asset('assets/images/yalova-logo.jpg');
        $out['website'] = url('/');
        return $out;
    }

    public static function standing(?Standing $s): ?array
    {
        if (!$s) {
            return null;
        }
        return [
            'season' => $s->season,
            'rank' => (int) $s->rank,
            'played' => (int) $s->played,
            'won' => (int) $s->won,
            'lost' => (int) $s->lost,
            'points' => (int) $s->points,
            'points_for' => (int) $s->points_for,
            'points_against' => (int) $s->points_against,
            'diff' => (int) $s->points_for - (int) $s->points_against,
        ];
    }

    /** Rakip logoları istek başına bir kez yüklenir: [tbf_team_id => url|null] */
    private static ?array $logos = null;

    private static function logo(?int $teamId): ?string
    {
        if (!$teamId) {
            return null;
        }
        self::$logos ??= TeamLogo::all()->mapWithKeys(fn($l) => [$l->tbf_team_id => $l->url])->all();
        return self::$logos[$teamId] ?? null;
    }

    public static function match(BasketballMatch $m): array
    {
        $win = $m->is_winner;
        return [
            'id' => $m->id,
            'round' => $m->round,
            'date' => self::date($m->match_date),
            'has_time' => $m->has_time,
            'home_team' => $m->home_team,
            'away_team' => $m->away_team,
            // Gerçek TBF logoları; yoksa null (uygulama logo uydurmaz)
            'home_logo' => self::logo($m->home_tbf_team_id),
            'away_logo' => self::logo($m->away_tbf_team_id),
            'home_score' => $m->home_score,
            'away_score' => $m->away_score,
            'is_home' => (bool) $m->is_home,
            'opponent' => $m->opponent,
            'venue' => $m->venue,
            'status' => $m->status,
            'is_next' => (bool) $m->is_next_match,
            'result' => $win === null ? null : ($win ? 'W' : 'L'),
        ];
    }

    public static function player(Player $p, bool $withStats = true): array
    {
        $out = [
            'id' => $p->id,
            'name' => $p->full_name,
            'first_name' => $p->first_name,
            'last_name' => $p->last_name,
            'number' => $p->jersey_number,
            'position' => $p->position,
            'height' => $p->height,
            'weight' => $p->weight,
            'birth_date' => $p->birth_date,
            'nationality' => $p->nationality,
            'image' => $p->image,
        ];
        if ($withStats) {
            $s = $p->stats;
            $out['stats'] = $s ? [
                'season' => $s->season,
                'games' => (int) $s->games_played,
                'minutes' => (float) $s->minutes_avg,
                'points' => (float) $s->points_avg,
                'rebounds' => (float) $s->rebounds_avg,
                'assists' => (float) $s->assists_avg,
                'steals' => (float) $s->steals_avg,
                'blocks' => (float) $s->blocks_avg,
                'turnovers' => (float) $s->turnovers_avg,
                'fouls' => (float) $s->fouls_avg,
                'efficiency' => (float) $s->efficiency_avg,
                'fg_pct' => $s->fg_percentage,
                'three_pct' => $s->three_point_percentage,
                'ft_pct' => $s->ft_percentage,
                'total_points' => (int) $s->total_points,
                'total_rebounds' => (int) $s->total_rebounds,
                'total_assists' => (int) $s->total_assists,
            ] : null;
        }
        return $out;
    }

    public static function staff(TechnicalStaff $s): array
    {
        return ['id' => $s->id, 'name' => $s->name, 'role' => $s->role, 'nationality' => $s->nationality, 'image' => $s->image];
    }

    public static function article(MediaArticle $a, bool $full = false): array
    {
        $out = [
            'id' => $a->id,
            'type' => $a->type,
            'category' => $a->category,
            'title' => $a->title,
            'date' => self::date($a->publish_date, true),
            'summary' => $a->summary,
            'image' => $a->image,
            'source_title' => $a->source_title,
            'source_url' => $a->source_url,
        ];
        if ($full) {
            $out['content'] = $a->content;
        }
        return $out;
    }

    public static function sponsor(Sponsor $s): array
    {
        return ['id' => $s->id, 'name' => $s->name, 'category' => $s->category, 'logo' => $s->logo, 'website' => $s->website_url];
    }

    public static function ticketPoint(TicketPoint $t): array
    {
        return [
            'id' => $t->id, 'name' => $t->name, 'logo' => $t->logo, 'address' => $t->address,
            'maps_url' => $t->maps_url, 'phone' => $t->phone, 'working_hours' => $t->working_hours,
        ];
    }

    public static function giveaway(Giveaway $g, bool $full = false): array
    {
        $statusText = match (true) {
            $g->status === 'completed' => 'Kazananlar açıklandı',
            $g->status === 'drawn' => 'Çekiliş yapıldı',
            $g->is_open => 'Katılım açık',
            default => 'Katılım sona erdi',
        };
        $out = [
            'slug' => $g->slug,
            'title' => $g->title,
            'prize' => $g->prize,
            'image' => $g->image_url,
            'status' => $g->status,
            'status_text' => $statusText,
            'is_open' => $g->is_open,
            'ends_at' => $g->ends_at_iso,
            'winner_count' => (int) $g->winner_count,
            'web_url' => route('giveaways.show', $g),
            // Uygulamadan katılım: uygulama şartı varsa zorunlu, sadece site kaydı varsa uygulama da form yerine geçer
            'app_join' => $g->is_open && ($g->requires('app_download') || $g->requires('site_registration')),
            'requires_app' => $g->requires('app_download'),
        ];
        if (!$full) {
            return $out;
        }

        $ig = SiteSetting::get('social_instagram');
        $igHandle = $ig ? GiveawayEntry::normalizeUsername(preg_replace('#[?].*$#', '', $ig)) : null;
        $steps = [];
        if ($g->requires('instagram_follow')) {
            $steps[] = ['key' => 'instagram_follow', 'text' => 'Instagram\'da ' . ($igHandle ? '@' . $igHandle : 'hesabımızı') . ' takip et', 'action' => $ig ? ['label' => 'Takip et', 'url' => $ig] : null];
        }
        if ($g->requires('instagram_like')) {
            $steps[] = ['key' => 'instagram_like', 'text' => 'Çekiliş gönderisini beğen', 'action' => $g->instagram_post_url ? ['label' => 'Gönderiyi aç', 'url' => $g->instagram_post_url] : null];
        }
        if ($g->requires('instagram_comment')) {
            $steps[] = ['key' => 'instagram_comment', 'text' => $g->required_mentions > 0 ? "Gönderiye yorum yap ve {$g->required_mentions} arkadaşını etiketle" : 'Gönderiye yorum yap', 'action' => $g->instagram_post_url ? ['label' => 'Yorum yap', 'url' => $g->instagram_post_url] : null];
        }
        if ($g->requires('app_download') || $g->requires('site_registration')) {
            $steps[] = ['key' => 'app_join', 'text' => 'Instagram kullanıcı adını aşağıdan gönder', 'action' => null];
        }
        foreach ($g->rules ?? [] as $extra) {
            $steps[] = ['key' => 'extra', 'text' => $extra, 'action' => null];
        }

        $out += [
            'description' => $g->description,
            'instagram_post_url' => $g->instagram_post_url,
            'required_mentions' => (int) $g->required_mentions,
            'steps' => $steps,
            'drawn_at' => self::date($g->drawn_at),
            'eligible_count_at_draw' => $g->eligible_count_at_draw,
            'winners' => $g->status === 'completed'
                ? $g->currentWinners()->map(fn($p) => ['username' => $p->entry->username, 'profile_url' => $p->entry->profile_url])->values()
                : [],
        ];
        return $out;
    }

    /** Katılımcının kendi kaydı: uygulamada "katıldın" ekranı için. Elenme nedeni katılımcıya sade dille anlatılır. */
    public static function myEntry(Giveaway $g, GiveawayEntry $e): array
    {
        $hint = null;
        if (!$e->is_eligible) {
            $hint = match (true) {
                str_starts_with((string) $e->ineligible_reason, 'Instagram yorumu') => 'Yorumun henüz görünmüyor. Yorumlar çekilişten önce Instagram\'dan aktarılıp kontrol edilir.',
                str_starts_with((string) $e->ineligible_reason, 'Yetersiz etiket') => "Yorumunda en az {$g->required_mentions} arkadaşını etiketlemelisin.",
                str_starts_with((string) $e->ineligible_reason, 'Süre bittikten') => 'Yorumun katılım süresi bittikten sonra yapılmış.',
                default => $e->ineligible_reason,
            };
        }
        return [
            'username' => $e->username,
            'joined_at' => self::date($e->app_verified_at ?? $e->registered_at),
            'app_verified' => (bool) $e->app_verified_at,
            'is_eligible' => (bool) $e->is_eligible,
            'hint' => $hint,
        ];
    }
}
