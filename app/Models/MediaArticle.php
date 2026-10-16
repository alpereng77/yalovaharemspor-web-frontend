<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'title',
        'publish_date',
        'summary',
        'content',
        'image_url',
        'source_title',
        'source_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getImageAttribute(): string
    {
        if ($this->image_url && file_exists(public_path($this->image_url))) {
            return asset($this->image_url);
        }
        if ($this->image_url && (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://'))) {
            return $this->image_url;
        }
        return asset('assets/images/birlik.jpg');
    }
}
