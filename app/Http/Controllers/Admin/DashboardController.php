<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\BasketballMatch;
use App\Models\TechnicalStaff;
use App\Models\Sponsor;
use App\Models\MediaArticle;
use App\Models\BotLog;
use App\Models\TicketPoint;
use App\Models\Newsletter;

class DashboardController extends Controller
{
    public function index()
    {
        $playerCount = Player::count();
        $matchCount = BasketballMatch::count();
        $staffCount = TechnicalStaff::count();
        $sponsorCount = Sponsor::count();
        $ticketPointCount = TicketPoint::count();
        $articleCount = MediaArticle::count();
        $newsletterCount = Newsletter::where('status', 'active')->count();
        $recentLogs = BotLog::latest()->take(5)->get();
        $upcomingMatch = BasketballMatch::where('status', 'upcoming')->orderBy('match_date', 'asc')->first();

        return view('admin.dashboard', compact(
            'playerCount',
            'matchCount',
            'staffCount',
            'sponsorCount',
            'ticketPointCount',
            'articleCount',
            'newsletterCount',
            'recentLogs',
            'upcomingMatch'
        ));
    }
}
