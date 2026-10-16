<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'status',
        'message',
        'items_count',
    ];
}
