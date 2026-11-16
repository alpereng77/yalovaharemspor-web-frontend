<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\TbfImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * tbf.org.tr sayfasında çalışan yer iminin (bookmarklet) veri gönderdiği uç nokta.
 * Yetki: admin panelindeki gizli içe aktarma anahtarı.
 */
class TbfImportController extends Controller
{
    private const ALLOWED_ORIGINS = ['https://www.tbf.org.tr', 'https://tbf.org.tr'];

    public function preflight(Request $request): Response
    {
        return $this->cors($request, response('', 204));
    }

    public function store(Request $request): JsonResponse
    {
        // Gövde text/plain JSON olarak gelir (CORS ön kontrolüne gerek kalmasın diye).
        $payload = json_decode($request->getContent(), true);

        $expected = (string) SiteSetting::get('tbf_import_token', '');
        $given = is_array($payload) ? (string) ($payload['token'] ?? '') : '';

        if ($expected === '' || !hash_equals($expected, $given)) {
            return $this->cors($request, response()->json(['success' => false, 'message' => 'Geçersiz içe aktarma anahtarı.'], 403));
        }

        $data = $payload['data'] ?? null;
        if (!is_array($data) || empty($data['matches']) || empty($data['stats'])) {
            return $this->cors($request, response()->json(['success' => false, 'message' => 'TBF verisi eksik geldi.'], 422));
        }

        try {
            $teamId = (int) SiteSetting::get('tbf_team_id', '266056');
            $result = (new TbfImportService($teamId))->import($data);

            return $this->cors($request, response()->json(['success' => true] + $result));
        } catch (\Throwable $e) {
            report($e);
            return $this->cors($request, response()->json(['success' => false, 'message' => 'İçe aktarma hatası: ' . $e->getMessage()], 500));
        }
    }

    /** Bookmarklet her oyuncu fotoğrafını ayrı bir istekle gönderir (base64). */
    public function photo(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $expected = (string) SiteSetting::get('tbf_import_token', '');
        $given = is_array($payload) ? (string) ($payload['token'] ?? '') : '';
        if ($expected === '' || !hash_equals($expected, $given)) {
            return $this->cors($request, response()->json(['success' => false, 'message' => 'Geçersiz içe aktarma anahtarı.'], 403));
        }

        $binary = base64_decode(preg_replace('#^data:[^,]*,#', '', (string) ($payload['data'] ?? '')), true);
        if (!$binary || empty($payload['tbf_id']) || empty($payload['url'])) {
            return $this->cors($request, response()->json(['success' => false, 'message' => 'Fotoğraf verisi eksik.'], 422));
        }

        try {
            $teamId = (int) SiteSetting::get('tbf_team_id', '266056');
            $path = (new TbfImportService($teamId))->savePhoto((string) $payload['tbf_id'], (string) $payload['url'], $binary);
            return $this->cors($request, response()->json(['success' => true, 'path' => $path]));
        } catch (\Throwable $e) {
            return $this->cors($request, response()->json(['success' => false, 'message' => $e->getMessage()], 422));
        }
    }

    private function cors(Request $request, $response)
    {
        $origin = $request->headers->get('Origin');
        if (in_array($origin, self::ALLOWED_ORIGINS, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
            $response->headers->set('Access-Control-Allow-Private-Network', 'true');
            $response->headers->set('Vary', 'Origin');
        }
        return $response;
    }
}
