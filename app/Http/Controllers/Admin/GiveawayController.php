<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\GiveawayPick;
use App\Services\GiveawayService;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GiveawayController extends Controller
{
    public function __construct(private GiveawayService $service) {}

    public function index()
    {
        $giveaways = Giveaway::withCount(['entries', 'entries as eligible_count' => fn($q) => $q->where('is_eligible', true)])
            ->latest()->get();

        return view('admin.giveaways.index', [
            'giveaways' => $giveaways,
            'settings' => SiteSetting::pluck('value', 'key')->toArray(),
        ]);
    }

    public function create()
    {
        return view('admin.giveaways.form', ['giveaway' => new Giveaway([
            'required_mentions' => 2,
            'winner_count' => 1,
            'reserve_count' => 2,
            'status' => 'draft',
            'rules' => [],
        ])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $giveaway = Giveaway::create($data);

        return redirect()->route('admin.giveaways.show', $giveaway)->with('success', 'Çekiliş oluşturuldu.');
    }

    public function show(Giveaway $giveaway)
    {
        $giveaway->load('picks.entry');
        $filter = request('filtre', 'hepsi');
        $entries = $giveaway->entries()
            ->when($filter === 'gecerli', fn($q) => $q->where('is_eligible', true))
            ->when($filter === 'gecersiz', fn($q) => $q->where('is_eligible', false))
            ->orderByDesc('is_eligible')->orderBy('username')
            ->paginate(50)->withQueryString();

        return view('admin.giveaways.show', [
            'giveaway' => $giveaway,
            'entries' => $entries,
            'filter' => $filter,
            'totalCount' => $giveaway->entries()->count(),
            'eligibleCount' => $giveaway->entries()->where('is_eligible', true)->count(),
            // Elenme nedenleri (ör. "Yetersiz etiket (0/2)" → "Yetersiz etiket"), en sık olan önce
            'ineligibleReasons' => $giveaway->entries()->where('is_eligible', false)->pluck('ineligible_reason')
                ->map(fn($r) => trim(preg_replace('/\s*\(.*\)$/', '', (string) $r)) ?: 'Bilinmiyor')
                ->countBy()->sortDesc(),
            // Çekiliş animasyonunda dönen isimler
            'eligibleNames' => $giveaway->picks->isEmpty()
                ? $giveaway->entries()->where('is_eligible', true)->limit(400)->pluck('username')
                : collect(),
        ]);
    }

    public function edit(Giveaway $giveaway)
    {
        return view('admin.giveaways.form', compact('giveaway'));
    }

    public function update(Request $request, Giveaway $giveaway)
    {
        $data = $this->validated($request, $giveaway);
        // Çekiliş yapıldıysa durum bu formdan değil, çekiliş ekranından yönetilir.
        if (in_array($giveaway->status, ['drawn', 'completed'], true)) {
            unset($data['status']);
        }
        $giveaway->update($data);
        $this->service->reevaluateAll($giveaway);

        return redirect()->route('admin.giveaways.show', $giveaway)->with('success', 'Çekiliş güncellendi.');
    }

    public function destroy(Giveaway $giveaway)
    {
        $giveaway->delete();
        return redirect()->route('admin.giveaways.index')->with('success', 'Çekiliş silindi.');
    }

    // ------------------------------------------------------------ Katılımcılar

    public function clearEntries(Giveaway $giveaway)
    {
        if ($giveaway->picks()->exists()) {
            return back()->with('error', 'Çekiliş yapıldıktan sonra katılımcılar silinemez.');
        }
        $giveaway->entries()->delete();
        return back()->with('success', 'Katılımcı listesi temizlendi.');
    }

    // ------------------------------------------------------------------ Çekiliş

    public function draw(Giveaway $giveaway)
    {
        try {
            $this->service->draw($giveaway);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('just_drawn', true)->with('success', 'Çekiliş yapıldı. Kazananların kalan şartları sağladığını kontrol edip onaylayın.');
    }

    public function extraReserve(Giveaway $giveaway)
    {
        try {
            $pick = $this->service->drawExtraReserve($giveaway);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', '@' . $pick->entry->username . ' ek yedek olarak çekildi.');
    }

    public function decide(Request $request, Giveaway $giveaway, GiveawayPick $pick)
    {
        abort_unless($pick->giveaway_id === $giveaway->id, 404);
        $data = $request->validate(['status' => 'required|in:confirmed,rejected,pending', 'note' => 'nullable|string|max:190']);

        try {
            $this->service->decide($pick, $data['status'], $data['note'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        $msg = ['confirmed' => 'onaylandı', 'rejected' => 'elendi; sıradaki yedek kazanan listesine geçti', 'pending' => 'tekrar beklemeye alındı'][$data['status']];
        return back()->with('success', '@' . $pick->entry->username . ' ' . $msg . '.');
    }

    public function setCheck(Request $request, Giveaway $giveaway, GiveawayPick $pick)
    {
        abort_unless($pick->giveaway_id === $giveaway->id, 404);
        $data = $request->validate(['key' => 'required|string', 'value' => 'nullable|in:yes,no']);
        try {
            $this->service->setCheck($pick, $data['key'], $data['value'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        $pick->refresh();
        $msg = match ($pick->status) {
            'confirmed' => '@' . $pick->entry->username . ' tüm şartları sağladı ve onaylandı.',
            'rejected' => '@' . $pick->entry->username . ' elendi (' . $pick->note . '); sıradaki yedek kazanan listesine geçti.',
            default => 'Kontrol kaydedildi.',
        };
        return back()->with($pick->status === 'rejected' ? 'warning' : 'success', $msg);
    }

    public function publish(Giveaway $giveaway)
    {
        try {
            $this->service->publish($giveaway);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Kazananlar çekiliş sayfasında duyuruldu.');
    }

    public function unpublish(Giveaway $giveaway)
    {
        $this->service->unpublish($giveaway);
        return back()->with('success', 'Duyuru yayından kaldırıldı; kazananlar tekrar onay aşamasında.');
    }

    public function resetDraw(Giveaway $giveaway)
    {
        try {
            $this->service->resetDraw($giveaway);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Çekiliş sıfırlandı. Katılımcılar korundu.');
    }

    // ------------------------------------------------------------- Bağlantılar

    /** Çekiliş sayfasında "uygulamayı indir" şartında gösterilen mağaza bağlantıları */
    public function saveIntegrations(Request $request)
    {
        $data = $request->validate([
            'app_store_url' => ['nullable', 'url', 'max:255'],
            'play_store_url' => ['nullable', 'url', 'max:255'],
        ]);
        SiteSetting::set('app_store_url', $data['app_store_url'] ?? '', 'app', 'App Store bağlantısı');
        SiteSetting::set('play_store_url', $data['play_store_url'] ?? '', 'app', 'Google Play bağlantısı');

        return back()->with('success', 'Uygulama bağlantıları kaydedildi.');
    }

    // ------------------------------------------------------------------ Yardımcı

    private function validated(Request $request, ?Giveaway $giveaway = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:190',
            'prize' => 'required|string|max:190',
            'description' => 'nullable|string|max:5000',
            'rules_text' => 'nullable|string|max:3000',
            'instagram_post_url' => ['nullable', 'url', 'max:255', 'regex:#instagram\.com/#i'],
            'required_mentions' => 'required|integer|min:0|max:10',
            'excluded_usernames' => 'nullable|string|max:3000',
            'ends_at' => 'nullable|date',
            'winner_count' => 'required|integer|min:1|max:50',
            'reserve_count' => 'required|integer|min:0|max:50',
            'status' => ['required', Rule::in(['draft', 'active'])],
        ], [
            'instagram_post_url.regex' => 'Instagram gönderi bağlantısı girin.',
        ]);

        $data['rules'] = collect(preg_split('/\r\n|\r|\n/', (string) ($data['rules_text'] ?? '')))
            ->map(fn($r) => trim($r, " \t-•*"))->filter()->values()->all();
        unset($data['rules_text']);

        // Açılıp kapatılabilen şartlar (işaretlenmeyen kutu = kapalı)
        $req = [];
        foreach (array_keys(Giveaway::REQUIREMENTS) as $key) {
            $req[$key] = $request->boolean("req.{$key}");
        }
        $data['requirements'] = $req;
        if (!$req['site_registration'] && !$req['instagram_comment']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'req' => 'Katılımcıların listeye girebilmesi için "Sitedeki forma kullanıcı adını yaz" veya "Gönderiye yorum yap" şartlarından en az biri açık olmalı.',
            ]);
        }

        return $data;
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'cekilis';
        $slug = $base;
        for ($i = 2; Giveaway::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }
        return $slug;
    }
}
