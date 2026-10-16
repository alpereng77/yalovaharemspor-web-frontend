<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'logo_url',
        'website_url',
        'order',
        'is_active',
    ];

    public function getLogoAttribute(): string
    {
        if ($this->logo_url && file_exists(public_path($this->logo_url))) {
            return asset($this->logo_url);
        }
        if ($this->logo_url && (str_starts_with($this->logo_url, 'http://') || str_starts_with($this->logo_url, 'https://'))) {
            return $this->logo_url;
        }
        return asset('assets/images/yalova-logo.jpg');
    }
}
