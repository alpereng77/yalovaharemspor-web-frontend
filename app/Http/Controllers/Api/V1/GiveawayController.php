<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use App\Services\GiveawayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Çekilişler. Uygulamadan katılım ("Uygulamayı indir" şartının tek doğrulama yolu) burada.
 * Cihaz kimliği her istekte X-Device-Id başlığıyla gelir; sunucuda sadece özeti saklanır.
 */
class GiveawayController extends Controller
{
    public function __construct(private GiveawayService $service) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => Giveaway::public()->latest()->get()->map(fn($g) => Presenter::giveaway($g))]);
    }

    public function show(Request $request, Giveaway $giveaway): JsonResponse
    {
        abort_unless(in_array($giveaway->status, ['active', 'drawn', 'completed'], true), 404);
        $giveaway->load('picks.entry');
        $data = Presenter::giveaway($giveaway, true);

        $device = $this->deviceId($request);
        $entry = $device ? $this->service->entryForDevice($giveaway, $device) : null;
        $data['my_entry'] = $entry ? Presenter::myEntry($giveaway, $entry) : null;

        return response()->json(['data' => $data]);
    }

    public function join(Request $request, Giveaway $giveaway): JsonResponse
    {
        abort_unless($giveaway->status === 'active', 404);
        if (!$giveaway->requires('app_download') && !$giveaway->requires('site_registration')) {
            return response()->json(['message' => 'Bu çekilişe katılım sadece Instagram gönderisi üzerinden.'], 422);
        }

        $device = $this->deviceId($request);
        if (!$device) {
            return response()->json(['message' => 'Uygulama cihaz bilgisi alınamadı. Uygulamayı kapatıp açarak tekrar dene.'], 422);
        }

        $data = $request->validate([
            'instagram_username' => 'required|string|max:60',
            'consent' => 'accepted',
        ], [
            'instagram_username.required' => 'Instagram kullanıcı adını yaz.',
            'consent.accepted' => 'Devam etmek için onay kutusunu işaretle.',
        ]);
        $username = GiveawayEntry::normalizeUsername($data['instagram_username']);
        if (!$username) {
            return response()->json(['message' => 'Geçerli bir Instagram kullanıcı adı yaz (harf, rakam, nokta, alt çizgi).', 'errors' => ['instagram_username' => ['Geçersiz kullanıcı adı']]], 422);
        }

        try {
            $result = $this->service->registerFromApp($giveaway, $username, $device, (string) $request->ip());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => $result['created']
                ? "Katılımın alındı @{$username}! Diğer şartları da tamamladığından emin ol."
                : "@{$username} zaten kayıtlı, bilgilerin güncellendi.",
            'data' => Presenter::myEntry($giveaway, $result['entry']),
        ], $result['created'] ? 201 : 200);
    }

    /** Uygulamanın ürettiği kalıcı cihaz kimliği: 16-128 karakter, harf/rakam/-/_ */
    private function deviceId(Request $request): ?string
    {
        $id = (string) $request->header('X-Device-Id', '');
        return preg_match('/^[A-Za-z0-9_-]{16,128}$/', $id) ? $id : null;
    }
}
