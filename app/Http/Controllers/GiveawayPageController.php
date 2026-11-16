<?php

namespace App\Http\Controllers;

use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use App\Models\SiteSetting;
use App\Services\GiveawayService;
use Illuminate\Http\Request;

class GiveawayPageController extends Controller
{
    public function index()
    {
        $giveaways = Giveaway::public()->with('picks.entry')
            ->orderByRaw("FIELD(status, 'active', 'drawn', 'completed')")
            ->orderByDesc('published_at')->orderBy('ends_at')
            ->get();

        return view('giveaways', [
            'settings' => SiteSetting::pluck('value', 'key')->toArray(),
            'giveaways' => $giveaways,
            'single' => false,
        ]);
    }

    public function show(Giveaway $giveaway)
    {
        abort_unless(in_array($giveaway->status, ['active', 'drawn', 'completed'], true), 404);
        $giveaway->load('picks.entry');

        return view('giveaways', [
            'settings' => SiteSetting::pluck('value', 'key')->toArray(),
            'giveaways' => collect([$giveaway]),
            'single' => true,
        ]);
    }

    /** Sitedeki katılım formu: Instagram kullanıcı adı (+ gerekirse YouTube kanalı). */
    public function register(Request $request, Giveaway $giveaway, GiveawayService $service)
    {
        abort_unless($giveaway->status === 'active' && $giveaway->requires('site_registration'), 404);
        $back = redirect()->to(route('giveaways.show', $giveaway) . '#katil');

        // Bot tuzağı: gerçek kullanıcılar bu gizli alanı boş bırakır
        if (filled($request->input('website'))) {
            return $back->with('gw_success', 'Katılımın alındı.');
        }

        $needsYoutube = $giveaway->requires('youtube_subscribe');
        $request->validate([
            'instagram_username' => 'required|string|max:60',
            'youtube_channel' => [$needsYoutube ? 'required' : 'nullable', 'string', 'max:150'],
            'consent' => 'accepted',
        ], [
            'instagram_username.required' => 'Instagram kullanıcı adını yaz.',
            'youtube_channel.required' => 'YouTube kanal adını veya bağlantısını yaz.',
            'consent.accepted' => 'Devam etmek için onay kutusunu işaretle.',
        ]);

        $username = GiveawayEntry::normalizeUsername($request->input('instagram_username'));
        if (!$username) {
            return $back->withInput()->withErrors(['instagram_username' => 'Geçerli bir Instagram kullanıcı adı yaz (harf, rakam, nokta, alt çizgi).']);
        }
        $youtube = $request->filled('youtube_channel') ? GiveawayEntry::normalizeYoutube($request->input('youtube_channel')) : null;
        if ($request->filled('youtube_channel') && !$youtube) {
            return $back->withInput()->withErrors(['youtube_channel' => 'YouTube kanal adı tanınmadı. Örnek: @kanaladi veya kanal bağlantısı.']);
        }

        try {
            $result = $service->register($giveaway, $username, $youtube, (string) $request->ip());
        } catch (\RuntimeException $e) {
            return $back->withInput()->withErrors(['instagram_username' => $e->getMessage()]);
        }

        return $back->with('gw_success', $result['created']
            ? "Katılımın alındı @{$username}! Diğer şartları da tamamladığından emin ol; kazananlar bu sayfada duyurulacak."
            : "@{$username} zaten kayıtlı. Bilgilerin güncellendi.");
    }
}
