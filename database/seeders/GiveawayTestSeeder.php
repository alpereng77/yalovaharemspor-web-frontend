<?php

namespace Database\Seeders;

use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use App\Services\GiveawayService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Çekiliş ekranını denemek için örnek veri.
 * Çalıştırma: php artisan db:seed --class=GiveawayTestSeeder
 * Her çalıştırmada "test-cekilisi" baştan oluşturulur; diğer çekilişlere dokunulmaz.
 * Katılımcılar gerçek aktarımla aynı servisten geçer, yani şart kontrolü gerçek akışla aynıdır.
 */
class GiveawayTestSeeder extends Seeder
{
    public const SLUG = 'test-cekilisi';

    public function run(GiveawayService $service): void
    {
        Giveaway::where('slug', self::SLUG)->get()->each(function (Giveaway $g) {
            $g->picks()->delete();
            $g->entries()->delete();
            $g->delete();
        });

        $now = Carbon::parse(now(GiveawayService::TZ)->format('Y-m-d H:i:s'));
        $endsAt = $now->copy()->subHour()->startOfMinute();

        $giveaway = Giveaway::create([
            'title' => 'TEST · Maç Bileti Çekilişi',
            'slug' => self::SLUG,
            'prize' => '2 kişilik maç bileti',
            'description' => 'Deneme amaçlı oluşturulmuş test çekilişidir.',
            'status' => 'active',
            'requirements' => [
                'site_registration' => true,
                'instagram_comment' => true,
                'instagram_follow' => true,
                'instagram_like' => true,
                'app_download' => false,
            ],
            'required_mentions' => 2,
            'winner_count' => 1,
            'reserve_count' => 2,
            'rules' => [],
            'ends_at' => $endsAt,
        ]);

        // [kullanıcı adı, yorum, bitişten kaç dakika önce] — negatif dakika = süre bittikten sonra
        $comments = [];
        $valid = ['test_ahmet', 'test_ayse', 'test_mehmet', 'test_zeynep', 'test_can', 'test_elif', 'test_burak',
            'test_selin', 'test_emre', 'test_deniz', 'test_kerem', 'test_ece', 'test_mert', 'test_irem', 'test_onur',
            'test_gizem', 'test_umut', 'test_melis', 'test_baris', 'test_pinar'];
        foreach ($valid as $i => $u) {
            $comments[] = [$u, "Haydi Haremspor! @arkadas_{$i}a @arkadas_{$i}b", 600 - $i * 20];
        }
        // Aynı kişi iki yorum: tek katılımcı sayılır, etiketler birleşir
        $comments[] = ['test_ahmet', 'Bir daha deniyorum @arkadas_x', 100];
        // Yetersiz etiket
        $comments[] = ['test_tek_etiket', 'Bilet bizim olsun @sadece_bir', 300];
        $comments[] = ['test_etiketsiz', 'Tebrikler takım 👏', 280];
        // Kendini etiketleyen (sayılmaz)
        $comments[] = ['test_kendini', '@test_kendini @arkadas_k', 260];
        // Süre bittikten sonra yorum
        $comments[] = ['test_gec_kalan', 'Geç kaldım mı? @arkadas_g1 @arkadas_g2', -15];
        // Siteye kayıt olmayan ama yorumu geçerli olan
        $comments[] = ['test_kayitsiz', 'Ben de varım @arkadas_s1 @arkadas_s2', 400];

        $service->importComments($giveaway, array_map(fn($c) => [
            'username' => $c[0],
            'text' => $c[1],
            'time' => $endsAt->copy()->subMinutes($c[2])->format('Y-m-d H:i:s'),
        ], $comments), 'instagram', true);

        // Sitedeki forma kayıt: test_kayitsiz hariç yorum yapan herkes + yorumu olmayan bir site kaydı
        $registered = array_merge($valid, ['test_tek_etiket', 'test_etiketsiz', 'test_kendini', 'test_gec_kalan', 'test_sadece_site']);
        foreach ($registered as $i => $u) {
            $entry = $giveaway->entries()->firstOrNew(['username' => $u]);
            if (!$entry->exists) {
                $entry->source = 'site';
            }
            $entry->registered_at = $endsAt->copy()->subMinutes(500 - $i * 10);
            $entry->registration_ip_hash = hash('sha256', 'test-ip-' . $i);
            $service->evaluate($giveaway, $entry);
            $entry->save();
        }

        $giveaway->update(['comments_synced_at' => now()]);

        $this->command?->info(sprintf(
            'Test çekilişi hazır: %d katılımcı, %d geçerli → /admin/giveaways/%s',
            $giveaway->entries()->count(),
            $giveaway->entries()->where('is_eligible', true)->count(),
            self::SLUG
        ));
    }
}
