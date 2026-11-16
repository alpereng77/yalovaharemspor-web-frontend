<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketballMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'tbf_match_id',
        'round',
        'match_date',
        'home_team',
        'away_team',
        'home_score',
        'away_score',
        'is_home',
        'venue',
        'status',
        'is_next_match',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'is_home' => 'boolean',
        'is_next_match' => 'boolean',
    ];

    public function getFormattedDateAttribute(): string
    {
        if (!$this->match_date) {
            return 'Açıklanacak';
        }
        // TBF saati belli olmayan maçları 00:00 olarak verir.
        return $this->has_time
            ? $this->match_date->translatedFormat('d F Y, l H:i')
            : $this->match_date->translatedFormat('d F Y, l') . ' · Saat açıklanacak';
    }

    public function getHasTimeAttribute(): bool
    {
        return $this->match_date !== null && $this->match_date->format('H:i') !== '00:00';
    }

    public function getOpponentAttribute(): string
    {
        return $this->is_home ? $this->away_team : $this->home_team;
    }

    public function getIsWinnerAttribute(): ?bool
    {
        if ($this->home_score === null || $this->away_score === null) {
            return null;
        }
        if ($this->is_home) {
            return $this->home_score > $this->away_score;
        }
        return $this->away_score > $this->home_score;
    }
}
