<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    use HasFactory;

    protected $fillable = [
        'season',
        'rank',
        'played',
        'won',
        'lost',
        'points',
        'points_for',
        'points_against',
    ];
}
