<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** TBF takım kimliğine göre rakip takım logosu (sitede saklanan gerçek dosya). */
class TeamLogo extends Model
{
    protected $fillable = ['tbf_team_id', 'name', 'source_url', 'image_path'];

    /** Dosya gerçekten varsa tam adres, yoksa null (logo uydurulmaz). */
    public function getUrlAttribute(): ?string
    {
        return $this->image_path && is_file(public_path($this->image_path)) ? asset($this->image_path) : null;
    }

    /** Birden çok takımın logosu tek sorguda: [tbf_team_id => url|null] */
    public static function urlsFor(array $teamIds): array
    {
        $ids = array_values(array_unique(array_filter($teamIds)));
        if (!$ids) {
            return [];
        }
        return static::whereIn('tbf_team_id', $ids)->get()->mapWithKeys(fn($l) => [$l->tbf_team_id => $l->url])->all();
    }
}
