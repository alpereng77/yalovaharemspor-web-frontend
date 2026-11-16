<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BasketballMatch;
use App\Models\Giveaway;
use App\Models\MediaArticle;
use App\Models\Newsletter;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\Standing;
use App\Models\TechnicalStaff;
use App\Models\TicketPoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Mobil uygulama için herkese açık, sadece okunur içerik uç noktaları. */
class ContentController extends Controller
{
    /** Ana ekran: tek istekle kulüp bilgisi, puan durumu, sıradaki maç, son sonuçlar, haberler, çekilişler. */
    public function home(): JsonResponse
    {
        $next = BasketballMatch::where('is_next_match', true)->first()
            ?? BasketballMatch::where('status', 'upcoming')->orderBy('match_date')->first();

        return $this->json([
            'club' => Presenter::club(),
            'standing' => Presenter::standing(Standing::latest('id')->first()),
            'next_match' => $next ? Presenter::match($next) : null,
            'recent_results' => BasketballMatch::where('status', 'played')->orderByDesc('match_date')->limit(5)->get()->map([Presenter::class, 'match']),
            'upcoming' => BasketballMatch::where('status', 'upcoming')->orderBy('match_date')->limit(5)->get()->map([Presenter::class, 'match']),
            'news' => $this->articles()->limit(6)->get()->map(fn($a) => Presenter::article($a)),
            'giveaways' => Giveaway::public()->latest()->limit(5)->get()
                ->filter(fn($g) => $g->status !== 'completed' || $g->published_at?->gt(now()->subDays(14)))
                ->values()->map(fn($g) => Presenter::giveaway($g)),
        ]);
    }

    public function club(): JsonResponse
    {
        return $this->json(Presenter::club());
    }

    /** ?status=upcoming|played (varsayılan: hepsi). Gelecek maçlar yakından uzağa, oynananlar yeniden eskiye. */
    public function matches(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $q = BasketballMatch::query();
        if (in_array($status, ['upcoming', 'played', 'postponed'], true)) {
            $q->where('status', $status);
        }
        $q = $status === 'played' ? $q->orderByDesc('match_date') : $q->orderByRaw('match_date IS NULL')->orderBy('match_date');

        $played = BasketballMatch::where('status', 'played')->get();
        return $this->json([
            'matches' => $q->get()->map([Presenter::class, 'match']),
            'record' => [
                'won' => $played->filter(fn($m) => $m->is_winner === true)->count(),
                'lost' => $played->filter(fn($m) => $m->is_winner === false)->count(),
            ],
        ]);
    }

    public function match(BasketballMatch $match): JsonResponse
    {
        return $this->json(Presenter::match($match));
    }

    public function standing(): JsonResponse
    {
        return $this->json(Presenter::standing(Standing::latest('id')->first()));
    }

    public function players(): JsonResponse
    {
        return $this->json(Player::with('stats')->where('is_active', true)->orderBy('order')->get()->map(fn($p) => Presenter::player($p)));
    }

    public function player(Player $player): JsonResponse
    {
        abort_unless($player->is_active, 404);
        return $this->json(Presenter::player($player->load('stats')));
    }

    public function staff(): JsonResponse
    {
        return $this->json(TechnicalStaff::where('is_active', true)->orderBy('order')->get()->map([Presenter::class, 'staff']));
    }

    /** ?type=blog|press */
    public function news(Request $request): JsonResponse
    {
        $q = $this->articles();
        if (in_array($request->query('type'), ['blog', 'press'], true)) {
            $q->where('type', $request->query('type'));
        }
        return $this->json($q->get()->map(fn($a) => Presenter::article($a)));
    }

    public function article(MediaArticle $article): JsonResponse
    {
        abort_unless($article->is_active, 404);
        return $this->json(Presenter::article($article, true));
    }

    public function sponsors(): JsonResponse
    {
        return $this->json(Sponsor::where('is_active', true)->orderBy('order')->get()->map([Presenter::class, 'sponsor']));
    }

    public function ticketPoints(): JsonResponse
    {
        return $this->json(TicketPoint::where('is_active', true)->orderBy('order')->get()->map([Presenter::class, 'ticketPoint']));
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => 'required|email:rfc|max:190'], [
            'email.required' => 'E-posta adresini yaz.',
            'email.email' => 'Geçerli bir e-posta adresi yaz.',
        ]);
        $sub = Newsletter::firstOrNew(['email' => strtolower($data['email'])]);
        $already = $sub->exists && $sub->status === 'active';
        $sub->fill(['status' => 'active', 'ip_address' => $request->ip(), 'user_agent' => 'mobile-app'])->save();

        return response()->json(['message' => $already ? 'Bu adres zaten bültene kayıtlı.' : 'Bültene kaydoldun, teşekkürler!']);
    }

    private function articles()
    {
        return MediaArticle::where('is_active', true)->orderByRaw('publish_date IS NULL')->orderByDesc('publish_date')->orderBy('order');
    }

    /** Kısa önbellek: uygulama sık yeniler, sunucu yorulmaz. */
    private function json($data): JsonResponse
    {
        return response()->json(['data' => $data])->header('Cache-Control', 'public, max-age=60');
    }
}
