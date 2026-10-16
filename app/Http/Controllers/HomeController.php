<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\BasketballMatch;
use App\Models\Standing;
use App\Models\Player;
use App\Models\TechnicalStaff;
use App\Models\Sponsor;
use App\Models\MediaArticle;
use App\Models\TicketPoint;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Load all settings into an associative array
        $settings = SiteSetting::pluck('value', 'key')->toArray();

        // Standing / Sezon İstatistiği
        $standing = Standing::first() ?? new Standing([
            'rank' => 2, 'played' => 4, 'won' => 3, 'lost' => 1, 'points' => 7, 'points_for' => 353, 'points_against' => 294
        ]);

        // Matches
        $nextMatch = BasketballMatch::where('is_next_match', true)->first()
            ?? BasketballMatch::where('status', 'upcoming')->orderBy('match_date', 'asc')->first();

        $upcomingMatches = BasketballMatch::where('status', 'upcoming')->orderBy('match_date', 'asc')->get();
        $pastMatches = BasketballMatch::where('status', 'played')->orderBy('match_date', 'desc')->get();

        // Calculate dynamic records if not in standing
        $winCount = BasketballMatch::where('status', 'played')->get()->filter(fn($m) => $m->is_winner)->count();
        $lossCount = BasketballMatch::where('status', 'played')->get()->filter(fn($m) => $m->is_winner === false)->count();

        // Players with their full statistics
        $players = Player::with('stats')->where('is_active', true)->orderBy('order', 'asc')->get();

        // Technical Staff
        $staff = TechnicalStaff::where('is_active', true)->orderBy('order', 'asc')->get();

        // Sponsors
        $sponsors = Sponsor::where('is_active', true)->orderBy('order', 'asc')->get();

        // Bilet Satış Noktaları
        $ticketPoints = TicketPoint::where('is_active', true)->orderBy('order', 'asc')->get();

        // Media Articles
        $blogs = MediaArticle::where('type', 'blog')->where('is_active', true)->orderBy('order', 'asc')->get();
        $press = MediaArticle::where('type', 'press')->where('is_active', true)->orderBy('order', 'asc')->get();

        return view('home', compact(
            'settings',
            'standing',
            'nextMatch',
            'upcomingMatches',
            'pastMatches',
            'players',
            'staff',
            'sponsors',
            'ticketPoints',
            'blogs',
            'press',
            'winCount',
            'lossCount'
        ));
    }

    /**
     * Optional JSON endpoint for player detail popup (AJAX or direct embedded)
     */
    public function playerDetail($id)
    {
        $player = Player::with('stats')->findOrFail($id);
        return response()->json([
            'id' => $player->id,
            'name' => $player->full_name,
            'first_name' => $player->first_name,
            'last_name' => $player->last_name,
            'jersey_number' => $player->jersey_number,
            'position' => $player->position,
            'height' => $player->height,
            'weight' => $player->weight,
            'birth_date' => $player->birth_date,
            'nationality' => $player->nationality,
            'image' => $player->image,
            'stats' => $player->stats,
        ]);
    }
}
