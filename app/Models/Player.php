<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'tbf_id',
        'first_name',
        'last_name',
        'jersey_number',
        'position',
        'height',
        'weight',
        'birth_date',
        'nationality',
        'image_url',
        'tbf_profile_url',
        'is_active',
        'order',
    ];

    public function stats()
    {
        return $this->hasOne(PlayerStat::class, 'player_id')->latestOfMany();
    }

    public function allStats()
    {
        return $this->hasMany(PlayerStat::class, 'player_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getImageAttribute(): string
    {
        if ($this->image_url && file_exists(public_path($this->image_url))) {
            return asset($this->image_url);
        }
        if ($this->image_url && (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://'))) {
            return $this->image_url;
        }
        if ($this->image_url) {
            return asset($this->image_url);
        }
        return asset('assets/images/yalova-logo.jpg');
    }
}
