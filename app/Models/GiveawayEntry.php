<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiveawayEntry extends Model
{
    protected $fillable = [
        'giveaway_id', 'username', 'comment_text', 'comment_count', 'mention_count',
        'first_commented_at', 'registered_at', 'youtube_handle', 'registration_ip_hash',
        'is_eligible', 'ineligible_reason', 'source',
    ];

    protected $casts = [
        'is_eligible' => 'boolean',
        'first_commented_at' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function getYoutubeUrlAttribute(): ?string
    {
        if (!$this->youtube_handle) {
            return null;
        }
        return str_starts_with($this->youtube_handle, 'UC')
            ? 'https://www.youtube.com/channel/' . rawurlencode($this->youtube_handle)
            : 'https://www.youtube.com/@' . rawurlencode(ltrim($this->youtube_handle, '@'));
    }

    /** "@kanal", "youtube.com/@kanal" veya "youtube.com/channel/UC..." → "kanal" / "UC..." */
    public static function normalizeYoutube(?string $value): ?string
    {
        $v = trim((string) $value);
        if ($v === '') {
            return null;
        }
        if (preg_match('#youtube\.com/channel/(UC[A-Za-z0-9_-]{20,})#', $v, $m)) {
            return $m[1];
        }
        $v = preg_replace('#^https?://(www\.|m\.)?youtube\.com/#', '', $v);
        $v = ltrim(trim($v, '/ '), '@');
        return preg_match('/^[A-Za-z0-9._-]{3,100}$/', $v) ? $v : null;
    }

    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function getProfileUrlAttribute(): string
    {
        return 'https://www.instagram.com/' . rawurlencode($this->username) . '/';
    }

    /** Instagram kullanıcı adı kuralları: harf, rakam, nokta, alt çizgi; en fazla 30 karakter. */
    public static function normalizeUsername(?string $value): ?string
    {
        $u = strtolower(trim((string) $value));
        $u = ltrim($u, '@');
        $u = preg_replace('#^https?://(www\.)?instagram\.com/#', '', $u);
        $u = trim($u, '/ ');
        return preg_match('/^[a-z0-9._]{1,30}$/', $u) ? $u : null;
    }
}
