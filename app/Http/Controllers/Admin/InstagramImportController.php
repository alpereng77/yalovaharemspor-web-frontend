<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Services\GiveawayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * "Instagram → Site" yer imi.
 *
 * Yer imi, yöneticinin kendi tarayıcısında açık olan instagram.com sayfasında çalışır;
 * Instagram'ın kendi web sayfasının kullandığı adreslerden gönderileri ve yorumları okur
 * ve bu sayfaya (açılan pencereye) tarayıcı içinde iletir. Sunucu Instagram'a hiç istek atmaz.
 * Instagram sayfası başka sitelere doğrudan veri gönderilmesini engellediği için (CSP) veri
 * açılan pencereye postMessage ile aktarılır; kaydetme işlemi admin oturumuyla yapılır.
 */
class InstagramImportController extends Controller
{
    public function __construct(private GiveawayService $service) {}

    public function page()
    {
        return view('admin.giveaways.instagram-import', [
            'giveaways' => Giveaway::whereIn('status', ['draft', 'active'])->whereDoesntHave('picks')->latest()->get(['id', 'title', 'slug', 'instagram_media_id', 'status']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'giveaway' => 'required|string|max:190',
            'post.media_id' => 'required|regex:/^[0-9]{5,40}$/',
            'post.shortcode' => 'nullable|regex:/^[A-Za-z0-9_-]{5,60}$/',
            'post.caption' => 'nullable|string|max:5000',
            'post.image' => 'nullable|string|max:3000',
            'post.owner' => 'nullable|string|max:60',
            'comments' => 'present|array|max:60000',
            'comments.*.username' => 'nullable|string|max:60',
            'comments.*.text' => 'nullable|string|max:3000',
            'comments.*.time' => 'nullable',
        ]);
        $post = $data['post'];

        if ($data['giveaway'] === 'new') {
            $giveaway = Giveaway::where('instagram_media_id', $post['media_id'])->whereDoesntHave('picks')->first();
            if (!$giveaway) {
                $firstLine = trim((string) strtok((string) ($post['caption'] ?? ''), "\n"));
                $title = $firstLine !== '' ? Str::limit($firstLine, 70, '') . ' Çekilişi' : 'Instagram Çekilişi ' . now()->format('d.m.Y');
                $giveaway = Giveaway::create([
                    'title' => $title,
                    'slug' => $this->uniqueSlug($title),
                    'prize' => 'Ödül belirlenecek',
                    'status' => 'draft',
                    'required_mentions' => 2,
                    'winner_count' => 1,
                    'reserve_count' => 2,
                    'rules' => [],
                ]);
            }
        } else {
            $giveaway = Giveaway::where('slug', $data['giveaway'])->firstOrFail();
        }

        $comments = array_map(fn($c) => [
            'username' => $c['username'] ?? null,
            'text' => (string) ($c['text'] ?? ''),
            'time' => is_numeric($c['time'] ?? null) ? '@' . (int) $c['time'] : ($c['time'] ?? null),
        ], $data['comments']);

        try {
            $stats = $this->service->importComments($giveaway, $comments, 'instagram', true);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $giveaway->update(array_filter([
            'instagram_media_id' => $post['media_id'],
            'instagram_post_url' => !empty($post['shortcode']) ? 'https://www.instagram.com/p/' . $post['shortcode'] . '/' : null,
            'comments_synced_at' => now(),
        ]));
        $this->service->storeImage($giveaway, $post['image'] ?? null);

        $owner = strtolower((string) ($post['owner'] ?? ''));
        $club = $this->service->clubInstagramUsername();
        $warning = ($owner && $club && $owner !== $club) ? " Not: gönderi @{$owner} hesabına ait, kulüp hesabı @{$club}." : '';

        $message = "{$stats['comments']} yorum aktarıldı: {$stats['added']} yeni, {$stats['updated']} güncellenen katılımcı.{$warning}";
        session()->flash($warning ? 'warning' : 'success', $message);

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => $giveaway->wasRecentlyCreated
                ? route('admin.giveaways.edit', $giveaway)
                : route('admin.giveaways.show', $giveaway),
        ]);
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
