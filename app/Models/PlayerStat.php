<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    use HasFactory;

    protected $table = 'player_stats';

    protected $fillable = [
        'player_id',
        'season',
        'games_played',
        'minutes_avg',
        'points_avg',
        'rebounds_avg',
        'assists_avg',
        'steals_avg',
        'blocks_avg',
        'turnovers_avg',
        'fouls_avg',
        'fg_percentage',
        'three_point_percentage',
        'ft_percentage',
        'total_points',
        'total_rebounds',
        'total_assists',
        'efficiency_avg',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
