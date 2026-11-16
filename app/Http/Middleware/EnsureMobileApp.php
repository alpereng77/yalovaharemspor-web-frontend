<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yazma isteklerinin (çekilişe katılım vb.) mobil uygulamadan geldiğini doğrular.
 * MOBILE_APP_KEY .env'de tanımlıysa X-App-Key başlığı eşleşmelidir; tanımlı değilse (yerel geliştirme) kontrol atlanır.
 * Not: Uygulama paketine gömülü bir anahtar, kararlı biri tarafından çıkarılabilir; bu kontrol sıradan form/bot
 * isteklerini eler. Cihaz başına tek hesap kuralı ve istek sınırı asıl korumadır.
 */
class EnsureMobileApp
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.mobile_app.key');
        if ($expected !== '' && !hash_equals($expected, (string) $request->header('X-App-Key', ''))) {
            return response()->json(['message' => 'Bu işlem sadece Yalova Haremspor mobil uygulamasından yapılabilir.'], 403);
        }
        return $next($request);
    }
}
