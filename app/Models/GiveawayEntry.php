<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiveawayEntry extends Model
{
    protected $fillable = [
        'giveaway_id', 'username', 'comment_text', 'comment_count', 'mention_count',
        'first_commented_at', 'registered_at', 'registration_ip_hash',
        'app_verified_at', 'app_device_hash',
        'is_eligible', 'ineligible_reason', 'source',
    ];

    protected $hidden = ['registration_ip_hash', 'app_device_hash'];

    protected $casts = [
        'is_eligible' => 'boolean',
        'first_commented_at' => 'datetime',
        'registered_at' => 'datetime',
        'app_verified_at' => 'datetime',
    ];

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
