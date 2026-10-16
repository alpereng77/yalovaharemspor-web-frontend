<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
        'address',
        'maps_url',
        'phone',
        'working_hours',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Logo URL accessor with fallback
     */
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
