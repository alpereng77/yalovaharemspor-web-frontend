<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalStaff extends Model
{
    use HasFactory;

    protected $table = 'technical_staff';

    protected $fillable = [
        'name',
        'role',
        'image_url',
        'nationality',
        'is_active',
        'order',
    ];

    public function getImageAttribute(): string
    {
        if ($this->image_url && file_exists(public_path($this->image_url))) {
            return asset($this->image_url);
        }
        if ($this->image_url && (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://'))) {
            return $this->image_url;
        }
        return asset('assets/images/yalova-logo.jpg');
    }
}
