<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiveawayPick extends Model
{
    protected $fillable = ['giveaway_id', 'giveaway_entry_id', 'position', 'status', 'checks', 'note', 'decided_at'];

    protected $casts = ['decided_at' => 'datetime', 'checks' => 'array'];

    /** Elle kontrol sonucu: 'yes' | 'no' | null */
    public function check(string $key): ?string
    {
        return ($this->checks ?? [])[$key] ?? null;
    }

    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(GiveawayEntry::class, 'giveaway_entry_id');
    }
}
