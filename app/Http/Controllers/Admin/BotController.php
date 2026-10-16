<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TbfScraperService;
use App\Models\SiteSetting;
use App\Models\BotLog;
use Illuminate\Http\Request;

class BotController extends Controller
{
    protected TbfScraperService $scraper;

    public function __construct(TbfScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key')->toArray();
        $logs = BotLog::latest()->take(30)->get();

        return view('admin.bot', compact('settings', 'logs'));
    }

    public function syncOfficial()
    {
        try {
            $count = $this->scraper->syncOfficialRoster();
            return redirect()->route('admin.bot.index')
                ->with('success', "⚡ Resmi TBF & Eurobasket Kadrosu Başarıyla Eşitlendi! {$count} oyuncu ve tüm istatistikleri güncellendi.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.bot.index')->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function syncAll()
    {
        try {
            $res = $this->scraper->syncAllOfficialData();
            return redirect()->back()
                ->with('success', "🤖 EUROBASKET & TBF BOTU BAŞARIYLA ÇALIŞTIRILDI! {$res['matches']} Maç ve Fikstür, {$res['players']} Oyuncu ve Tüm İstatistikler çekilerek veritabanına işlendi.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function scrape(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            switch ($type) {
                case 'players':
                    $result = $this->scraper->scrapePlayers();
                    break;
                case 'staff':
                    $result = $this->scraper->scrapeStaff();
                    break;
                case 'stats':
                    $result = $this->scraper->scrapeStats();
                    break;
                case 'matches':
                    $result = $this->scraper->scrapeMatches();
                    break;
                case 'all':
                default:
                    $result = $this->scraper->scrapeAll();
                    return redirect()->route('admin.bot.index')
                        ->with('info', 'TBF bot tüm bölümleri taradı. Detayları aşağıdaki log kayıtlarından inceleyebilirsiniz.');
            }

            if (!empty($result['success'])) {
                return redirect()->route('admin.bot.index')->with('success', $result['message']);
            } else {
                return redirect()->route('admin.bot.index')->with('warning', $result['message']);
            }
        } catch (\Throwable $e) {
            BotLog::create([
                'action' => 'scrape_' . $type,
                'status' => 'error',
                'message' => 'Hata oluştu: ' . $e->getMessage(),
                'items_count' => 0
            ]);
            return redirect()->route('admin.bot.index')->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function parseManualHtml(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:players,staff,stats,matches',
            'html_content' => 'required|string|min:20',
        ]);

        $type = $request->input('target_type');
        $html = $request->input('html_content');

        try {
            $count = 0;
            switch ($type) {
                case 'players':
                    $count = $this->scraper->parsePlayersHtml($html);
                    $msg = "Yapıştırılan HTML'den {$count} oyuncu başarıyla ayrıştırıldı ve veritabanına aktarıldı.";
                    break;
                case 'staff':
                    $count = $this->scraper->parseStaffHtml($html);
                    $msg = "Yapıştırılan HTML'den {$count} teknik kadro personeli başarıyla ayrıştırıldı.";
                    break;
                case 'stats':
                    $count = $this->scraper->parseStatsHtml($html);
                    $msg = "Yapıştırılan HTML'den {$count} oyuncunun istatistikleri başarıyla güncellendi.";
                    break;
                case 'matches':
                    $count = $this->scraper->parseMatchesHtml($html);
                    $msg = "Yapıştırılan HTML'den {$count} maç verisi başarıyla aktarıldı.";
                    break;
            }

            return redirect()->route('admin.bot.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('admin.bot.index')->with('error', 'Ayrıştırma hatası: ' . $e->getMessage());
        }
    }

    public function syncFromBrowser(Request $request)
    {
        $type = $request->input('type');
        $html = $request->input('html');
        $url = $request->input('url', '');

        if (!$type) {
            if (str_contains($url, 'basketbolcu')) $type = 'players';
            elseif (str_contains($url, 'teknik')) $type = 'staff';
            elseif (str_contains($url, 'istatistik')) $type = 'stats';
            elseif (str_contains($url, 'maclar')) $type = 'matches';
            else $type = 'players';
        }

        $count = 0;
        $msg = '';

        try {
            switch ($type) {
                case 'players':
                    $count = $this->scraper->parsePlayersHtml($html);
                    $msg = "Tarayıcıdan aktarılan {$count} oyuncu başarıyla güncellendi.";
                    break;
                case 'staff':
                    $count = $this->scraper->parseStaffHtml($html);
                    $msg = "Tarayıcıdan aktarılan {$count} teknik kadro personeli güncellendi.";
                    break;
                case 'stats':
                    $count = $this->scraper->parseStatsHtml($html);
                    $msg = "Tarayıcıdan aktarılan {$count} oyuncu istatistiği güncellendi.";
                    break;
                case 'matches':
                    $count = $this->scraper->parseMatchesHtml($html);
                    $msg = "Tarayıcıdan aktarılan {$count} maç verisi güncellendi.";
                    break;
            }

            return response()->json([
                'success' => $count > 0,
                'count' => $count,
                'message' => $msg
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'tbf_url_overview' => 'nullable|url',
            'tbf_url_stats' => 'nullable|url',
            'tbf_url_matches' => 'nullable|url',
            'tbf_url_players' => 'nullable|url',
            'tbf_url_staff' => 'nullable|url',
            'tbf_cf_cookie' => 'nullable|string',
            'tbf_user_agent' => 'nullable|string',
        ]);

        foreach ($data as $key => $val) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $val ?? '']);
        }

        return redirect()->route('admin.bot.index')->with('success', 'Bot ayarları güncellendi.');
    }

    public function clearLogs()
    {
        BotLog::truncate();
        return redirect()->route('admin.bot.index')->with('success', 'Bot log geçmişi temizlendi.');
    }
}
