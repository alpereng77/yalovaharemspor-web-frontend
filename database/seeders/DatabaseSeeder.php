<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\TechnicalStaff;
use App\Models\BasketballMatch;
use App\Models\Standing;
use App\Models\Sponsor;
use App\Models\MediaArticle;

use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // 1. Admin Kullanıcısı
        User::updateOrCreate(
            ['email' => 'admin@yalovaharemspor.com'],
            [
                'name' => 'Yalova Haremspor Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Site Ayarları
        $settings = [
            // Genel & Hero
            'site_title' => 'Yalova Haremspor • #aynıtutkuyeniheyecan',
            'meta_description' => 'Yalova Haremspor Basketbol Kulübü. Bir şehir, bir arma, aynı tutku yeni heyecan.',
            'city_name' => 'YALOVA',
            'club_name' => 'HAREMSPOR',
            'hero_title_1' => 'AYNI TUTKU.',
            'hero_title_2' => 'YENİ HEYECAN.',
            'hero_description' => "Sesimiz tribünde. Gücümüz sahada.\nSon düdüğe kadar, hep birlikte.",
            'hero_cta_text' => 'MAÇ MERKEZİ',
            'hero_cta_url' => '#maclar',
            'hero_image' => 'assets/images/saha.jpg',

            // Sosyal Medya
            'social_instagram' => 'https://instagram.com/yalovaharemspor',
            'social_x' => 'https://x.com/yalovaharemspor',
            'social_youtube' => 'https://youtube.com/@yalovaharemspor',

            // Kulüp / Hakkımızda
            'about_title' => "YALOVA İÇİN.\nSON DÜDÜĞE KADAR.",
            'about_image_1' => 'assets/images/birlik.jpg',
            'about_image_2' => 'assets/images/tribun.jpg',
            'about_cta_text' => 'MAÇ TAKVİMİ',
            'about_cta_url' => '#maclar',

            // Kimliğimiz
            'identity_green_badge' => '#003F20',
            'identity_green_num' => '01 / KOYU YEŞİL',
            'identity_green_title' => "KÖKLERİMİZ\nSAĞLAM.",
            'identity_green_text' => 'Bağlılık, kararlılık ve kulübün sarsılmaz temeli. Parkede asla pes etmeyen takım disiplini.',

            'identity_red_badge' => '#A70101',
            'identity_red_num' => '02 / AGRESİF KIRMIZI',
            'identity_red_title' => "ATEŞİMİZ\nDURMAZ.",
            'identity_red_text' => 'Mücadelenin ateşi, hücumun hızı ve tribünün bitmeyen tutkusu. Kazanma hırsımızın simgesi.',

            'identity_mascot_num' => '03 / MASKOTUMUZ',
            'identity_mascot_title' => 'TOSTOS’UN GÜCÜ.',
            'identity_mascot_text' => 'Cesur, çevik ve her saniye oyunda. Son saniyeye kadar sahadaki liderimiz.',

            // TBF Bot Ayarları
            'tbf_team_id' => '266056',
            'tbf_season' => 'tbl-2026-2027',
            'tbf_url_overview' => 'https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/genel-bakis',
            'tbf_url_stats' => 'https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/istatistikler',
            'tbf_url_matches' => 'https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/maclar',
            'tbf_url_players' => 'https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/basketbolcular',
            'tbf_url_staff' => 'https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/teknik-kadro',
            'tbf_cf_cookie' => '',
            'tbf_user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',

            // Footer
            'footer_hashtag' => '#aynıtutkuyeniheyecan',
            'footer_copyright' => 'Yalova Haremspor Basketbol Kulübü. Tüm Hakları Saklıdır.',
            'footer_credit_name' => 'alperengundogdu',
            'footer_credit_url' => 'https://alperengundogdu.com.tr',
        ];

        foreach ($settings as $key => $val) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 3. Teknik Kadro
        TechnicalStaff::truncate();
        TechnicalStaff::create([
            'name' => 'Faruk Beşok',
            'role' => 'Başantrenör',
            'image_url' => 'assets/images/yalova-logo.jpg',
            'order' => 1,
            'is_active' => true,
        ]);
        TechnicalStaff::create([
            'name' => 'Ahmet Murat Yolcu',
            'role' => 'Yardımcı Antrenör',
            'image_url' => 'assets/images/yalova-logo.jpg',
            'order' => 2,
            'is_active' => true,
        ]);
        TechnicalStaff::create([
            'name' => 'Serdar Ergök',
            'role' => 'Kondisyoner / Atletik Performans',
            'image_url' => 'assets/images/yalova-logo.jpg',
            'order' => 3,
            'is_active' => true,
        ]);

        // 4. Oyuncular ve İstatistikleri
        Player::truncate();
        PlayerStat::truncate();

        $roster = [
            [
                'first_name' => 'Abdoulaye', 'last_name' => 'Gueye', 'jersey_number' => '10', 'position' => 'Pivot (5)',
                'height' => '2.08 m', 'weight' => '106 kg', 'birth_date' => '1994', 'nationality' => 'Senegal',
                'image_url' => 'assets/players/2q7aa9begz0kkbmumz8avj0mh.png', 'tbf_id' => 'gueye-10',
                'stats' => ['gp' => 4, 'min' => 26.5, 'pts' => 16.0, 'reb' => 9.5, 'ast' => 1.8, 'stl' => 1.0, 'blk' => 1.5, 'to' => 1.2, 'eff' => 21.0, 'fg' => '62%', 'tp' => '33%', 'ft' => '75%']
            ],
            [
                'first_name' => 'Atakan', 'last_name' => 'Şahinkaya', 'jersey_number' => '9', 'position' => 'Kısa Forvet (3)',
                'height' => '2.01 m', 'weight' => '95 kg', 'birth_date' => '1998', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/8rtnhxsnektd3ygi26qz24650.png', 'tbf_id' => 'sahinkaya-9',
                'stats' => ['gp' => 4, 'min' => 18.2, 'pts' => 7.5, 'reb' => 4.2, 'ast' => 1.2, 'stl' => 0.8, 'blk' => 0.2, 'to' => 0.9, 'eff' => 8.5, 'fg' => '48%', 'tp' => '36%', 'ft' => '80%']
            ],
            [
                'first_name' => 'Berke', 'last_name' => 'Aygündüz', 'jersey_number' => '8', 'position' => 'Skorer Gard (2)',
                'height' => '1.96 m', 'weight' => '88 kg', 'birth_date' => '1998', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/einjgudjtgszzjv4wfyfhpu96.png', 'tbf_id' => 'aygunduz-8',
                'stats' => ['gp' => 4, 'min' => 22.0, 'pts' => 11.2, 'reb' => 3.1, 'ast' => 2.5, 'stl' => 1.1, 'blk' => 0.3, 'to' => 1.0, 'eff' => 12.3, 'fg' => '51%', 'tp' => '41%', 'ft' => '85%']
            ],
            [
                'first_name' => 'Cavit Ege', 'last_name' => 'Havsa', 'jersey_number' => '5', 'position' => 'Oyun Kurucu (1)',
                'height' => '1.94 m', 'weight' => '84 kg', 'birth_date' => '1997', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/2zrbsb3n6x6wmfgsej0myiwvd.png', 'tbf_id' => 'havsa-5',
                'stats' => ['gp' => 4, 'min' => 24.5, 'pts' => 9.8, 'reb' => 2.8, 'ast' => 5.4, 'stl' => 1.5, 'blk' => 0.1, 'to' => 1.6, 'eff' => 13.8, 'fg' => '47%', 'tp' => '38%', 'ft' => '88%']
            ],
            [
                'first_name' => 'Duşan', 'last_name' => 'Cantekin', 'jersey_number' => '17', 'position' => 'Pivot (5)',
                'height' => '2.21 m', 'weight' => '118 kg', 'birth_date' => '1990', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/cvll5qrvz9l3123l080zhj2ll.png', 'tbf_id' => 'cantekin-17',
                'stats' => ['gp' => 4, 'min' => 16.0, 'pts' => 8.4, 'reb' => 6.2, 'ast' => 0.6, 'stl' => 0.2, 'blk' => 1.8, 'to' => 1.1, 'eff' => 12.0, 'fg' => '65%', 'tp' => '0%', 'ft' => '70%']
            ],
            [
                'first_name' => 'Ege', 'last_name' => 'Özçelik', 'jersey_number' => '77', 'position' => 'Gard (1)',
                'height' => '1.90 m', 'weight' => '80 kg', 'birth_date' => '2003', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/754jogx0iu07lo4wty3388m50.png', 'tbf_id' => 'ozcelik-77',
                'stats' => ['gp' => 3, 'min' => 11.0, 'pts' => 4.3, 'reb' => 1.2, 'ast' => 2.0, 'stl' => 0.6, 'blk' => 0.0, 'to' => 0.8, 'eff' => 5.2, 'fg' => '44%', 'tp' => '35%', 'ft' => '78%']
            ],
            [
                'first_name' => 'Erkin', 'last_name' => 'Şenel', 'jersey_number' => '18', 'position' => 'Skorer Gard (2)',
                'height' => '1.92 m', 'weight' => '85 kg', 'birth_date' => '1999', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/5nkf75m7o173zlnbwrz36lex.png', 'tbf_id' => 'senel-18',
                'stats' => ['gp' => 4, 'min' => 19.5, 'pts' => 8.9, 'reb' => 2.5, 'ast' => 2.1, 'stl' => 0.9, 'blk' => 0.1, 'to' => 1.0, 'eff' => 9.4, 'fg' => '49%', 'tp' => '39%', 'ft' => '82%']
            ],
            [
                'first_name' => 'Hakan', 'last_name' => 'Yapar', 'jersey_number' => '15', 'position' => 'Uzun Forvet (4)',
                'height' => '2.03 m', 'weight' => '102 kg', 'birth_date' => '1984', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/6ujjy1tqzyd9veh8cq8k2hk6i.png', 'tbf_id' => 'yapar-15',
                'stats' => ['gp' => 4, 'min' => 21.0, 'pts' => 9.5, 'reb' => 5.8, 'ast' => 3.2, 'stl' => 0.7, 'blk' => 0.4, 'to' => 1.2, 'eff' => 14.5, 'fg' => '54%', 'tp' => '37%', 'ft' => '84%']
            ],
            [
                'first_name' => 'Metin', 'last_name' => 'Türen', 'jersey_number' => '7', 'position' => 'Uzun Forvet (4)',
                'height' => '2.06 m', 'weight' => '104 kg', 'birth_date' => '1994', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/38wy73n9s8wd17sqgjxgeqfvt.png', 'tbf_id' => 'turen-7',
                'stats' => ['gp' => 4, 'min' => 25.0, 'pts' => 12.4, 'reb' => 7.8, 'ast' => 2.4, 'stl' => 1.0, 'blk' => 0.8, 'to' => 1.4, 'eff' => 17.2, 'fg' => '56%', 'tp' => '38%', 'ft' => '81%']
            ],
            [
                'first_name' => 'Salih Ege', 'last_name' => 'Yaman', 'jersey_number' => '12', 'position' => 'Kısa Forvet (3)',
                'height' => '1.98 m', 'weight' => '90 kg', 'birth_date' => '2004', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/3fxtr3mxgb0kmt5yqdcxb9jo.png', 'tbf_id' => 'yaman-12',
                'stats' => ['gp' => 2, 'min' => 7.5, 'pts' => 2.5, 'reb' => 1.0, 'ast' => 0.5, 'stl' => 0.3, 'blk' => 0.1, 'to' => 0.4, 'eff' => 3.0, 'fg' => '42%', 'tp' => '30%', 'ft' => '70%']
            ],
            [
                'first_name' => 'Samuel Lee', 'last_name' => 'Sessoms', 'jersey_number' => '3', 'position' => 'Oyun Kurucu (1)',
                'height' => '1.83 m', 'weight' => '86 kg', 'birth_date' => '1999', 'nationality' => 'ABD',
                'image_url' => 'assets/players/4h12izxlaes4qs00o1numjpjo.png', 'tbf_id' => 'sessoms-3',
                'stats' => ['gp' => 4, 'min' => 32.5, 'pts' => 24.8, 'reb' => 4.5, 'ast' => 7.2, 'stl' => 2.1, 'blk' => 0.3, 'to' => 2.8, 'eff' => 28.5, 'fg' => '53%', 'tp' => '42%', 'ft' => '89%']
            ],
            [
                'first_name' => 'Yavuz Selim', 'last_name' => 'Kara', 'jersey_number' => '13', 'position' => 'Pivot (5)',
                'height' => '2.07 m', 'weight' => '105 kg', 'birth_date' => '2003', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/9wkajcie0u7m8olllyak8gkyc.png', 'tbf_id' => 'kara-13',
                'stats' => ['gp' => 2, 'min' => 8.0, 'pts' => 3.0, 'reb' => 2.5, 'ast' => 0.3, 'stl' => 0.1, 'blk' => 0.5, 'to' => 0.5, 'eff' => 4.5, 'fg' => '50%', 'tp' => '0%', 'ft' => '65%']
            ],
            [
                'first_name' => 'Yiğit', 'last_name' => 'Özkan', 'jersey_number' => '2', 'position' => 'Gard (1)',
                'height' => '1.88 m', 'weight' => '82 kg', 'birth_date' => '2001', 'nationality' => 'Türkiye',
                'image_url' => 'assets/players/9huyqahwhduixl67svqdr5gmi.png', 'tbf_id' => 'ozkan-2',
                'stats' => ['gp' => 3, 'min' => 12.0, 'pts' => 5.1, 'reb' => 1.8, 'ast' => 2.3, 'stl' => 0.7, 'blk' => 0.1, 'to' => 1.0, 'eff' => 6.2, 'fg' => '46%', 'tp' => '36%', 'ft' => '83%']
            ]
        ];

        foreach ($roster as $index => $item) {
            $statsData = $item['stats'];
            unset($item['stats']);
            $item['order'] = $index + 1;
            $item['is_active'] = true;
            $player = Player::create($item);

            PlayerStat::create([
                'player_id' => $player->id,
                'season' => '2026-2027',
                'games_played' => $statsData['gp'],
                'minutes_avg' => $statsData['min'],
                'points_avg' => $statsData['pts'],
                'rebounds_avg' => $statsData['reb'],
                'assists_avg' => $statsData['ast'],
                'steals_avg' => $statsData['stl'],
                'blocks_avg' => $statsData['blk'],
                'turnovers_avg' => $statsData['to'],
                'efficiency_avg' => $statsData['eff'],
                'fg_percentage' => $statsData['fg'],
                'three_point_percentage' => $statsData['tp'],
                'ft_percentage' => $statsData['ft'],
                'total_points' => (int) round($statsData['pts'] * $statsData['gp']),
                'total_rebounds' => (int) round($statsData['reb'] * $statsData['gp']),
                'total_assists' => (int) round($statsData['ast'] * $statsData['gp']),
            ]);
        }

        // 5. Puan Durumu
        Standing::truncate();
        Standing::create([
            'season' => 'TBL 2026-2027',
            'rank' => 2,
            'played' => 4,
            'won' => 3,
            'lost' => 1,
            'points' => 7,
            'points_for' => 353,
            'points_against' => 294,
        ]);

        // 6. Maçlar
        BasketballMatch::truncate();
        $matches = [
            [
                'round' => 1, 'match_date' => '2026-09-12 18:00:00',
                'home_team' => 'Yalova Harem Spor', 'away_team' => 'Ankaragücü Mamak Bel.',
                'home_score' => 113, 'away_score' => 62, 'is_home' => true,
                'venue' => 'Yalova 90. Yıl Spor Salonu', 'status' => 'played', 'is_next_match' => false
            ],
            [
                'round' => 2, 'match_date' => '2026-09-20 15:00:00',
                'home_team' => 'Konyaspor', 'away_team' => 'Yalova Harem Spor',
                'home_score' => 81, 'away_score' => 91, 'is_home' => false,
                'venue' => 'Selçuklu Bld. Spor Salonu', 'status' => 'played', 'is_next_match' => false
            ],
            [
                'round' => 3, 'match_date' => '2026-09-26 16:30:00',
                'home_team' => 'Yalova Harem Spor', 'away_team' => 'iLab Basketbol',
                'home_score' => 82, 'away_score' => 81, 'is_home' => true,
                'venue' => 'Yalova 90. Yıl Spor Salonu', 'status' => 'played', 'is_next_match' => false
            ],
            [
                'round' => 4, 'match_date' => '2026-10-03 17:00:00',
                'home_team' => 'Final Spor', 'away_team' => 'Yalova Harem Spor',
                'home_score' => 70, 'away_score' => 67, 'is_home' => false,
                'venue' => 'Naim Süleymanoğlu Spor Kompleksi', 'status' => 'played', 'is_next_match' => false
            ],
            [
                'round' => 5, 'match_date' => '2026-10-18 17:00:00',
                'home_team' => 'Yalova Harem Spor', 'away_team' => 'Fenerbahçe Koleji Novotel',
                'home_score' => null, 'away_score' => null, 'is_home' => true,
                'venue' => 'Yalova 90. Yıl Spor Salonu', 'status' => 'upcoming', 'is_next_match' => true
            ],
            [
                'round' => 6, 'match_date' => '2026-10-25 15:30:00',
                'home_team' => 'Ormanspor', 'away_team' => 'Yalova Harem Spor',
                'home_score' => null, 'away_score' => null, 'is_home' => false,
                'venue' => 'M. Sait Zarifoğlu Spor Salonu', 'status' => 'upcoming', 'is_next_match' => false
            ],
            [
                'round' => 7, 'match_date' => '2026-11-01 16:00:00',
                'home_team' => 'Yalova Harem Spor', 'away_team' => 'Gaziantep Basketbol',
                'home_score' => null, 'away_score' => null, 'is_home' => true,
                'venue' => 'Yalova 90. Yıl Spor Salonu', 'status' => 'upcoming', 'is_next_match' => false
            ],
        ];

        foreach ($matches as $match) {
            BasketballMatch::create($match);
        }

        // 7. Sponsorlar
        Sponsor::truncate();
        $sponsors = [
            ['name' => 'AKTİF INTERNATIONAL', 'category' => 'SAĞLIK SPONSORU', 'logo_url' => 'assets/sponsors/aktif-hospital.png', 'website_url' => 'https://aktifhastaneleri.com', 'order' => 1],
            ['name' => 'ALKAŞ YAPI', 'category' => 'RESMİ SPONSOR', 'logo_url' => 'assets/sponsors/alkas-yapi.png', 'website_url' => null, 'order' => 2],
            ['name' => 'CEMELE GOLD', 'category' => 'RESMİ SPONSOR', 'logo_url' => 'assets/sponsors/cemele-gold.png', 'website_url' => null, 'order' => 3],
            ['name' => 'KAYA JUWELIER', 'category' => 'RESMİ SPONSOR', 'logo_url' => 'assets/sponsors/kaya-juwelier.png', 'website_url' => null, 'order' => 4],
            ['name' => 'TAÇ ZİNCİR', 'category' => 'RESMİ SPONSOR', 'logo_url' => 'assets/sponsors/tac-zincir.png', 'website_url' => null, 'order' => 5],
        ];
        foreach ($sponsors as $sp) {
            Sponsor::create($sp);
        }

        // 8. Medya / Blog ve Basında Biz
        MediaArticle::truncate();
        $blogs = [
            [
                'type' => 'blog', 'category' => 'YENİ DÖNEM', 'title' => 'Yalova’da yeni bir basketbol hikâyesi.',
                'publish_date' => '2026-07-06',
                'summary' => 'Yürüyen Köşk’ten parkeye uzanan yeni dönemin ilk adımı.',
                'content' => "Yalova Belediyesi ile Haremspor arasındaki iş birliği, şehrin basketbol hikâyesinde yeni bir sayfa açtı. 6 Temmuz 2026 tarihli lansman duyurusu, Yürüyen Köşk’te buluşmayı ve Yalova Haremspor adıyla başlayacak yeni dönemi kamuoyuna taşıdı.\n\n2026–2027 Türkiye Basketbol Ligi sezonu için kurulan yapı, Faruk Beşok yönetimindeki kadroyla yola çıktı. Bu başlangıç, Yalova’nın basketbol kültürünü sahadaki yeni hedeflerle buluşturan önemli bir adım oldu.",
                'image_url' => 'assets/images/identity-yuruyen-kosk.jpg', 'source_title' => 'Kaynak: Haberci', 'source_url' => 'https://haberci.com.tr/potada-yeni-donem-resmen-basliyor/', 'order' => 1
            ],
            [
                'type' => 'blog', 'category' => 'SEZON HAZIRLIĞI', 'title' => 'Ankara’dan kupayla, sezona moralle.',
                'publish_date' => '2026-08-26',
                'summary' => 'TED Meşale Turnuvası’nda iki galibiyet ve bir şampiyonluk.',
                'content' => "Yalova Haremspor, TED Meşale Turnuvası’nda Gaziantep Basketbol’u 76–70, finalde TED Ankara Kolejliler’i 80–73 mağlup ederek kupaya ulaştı. Hazırlık dönemindeki bu iki galibiyet, yeni sezon öncesinde takıma moral verdi.\n\nTurnuva sonrasında hazırlık programı Bandırma Bordo ve Finalspor karşılaşmalarıyla devam edecek şekilde planlandı.",
                'image_url' => 'assets/images/birlik.jpg', 'source_title' => 'Kaynak: Haberci', 'source_url' => 'https://haberci.com.tr/yalova-haremsporda-mesai-suruyor/', 'order' => 2
            ],
            [
                'type' => 'blog', 'category' => 'PARKEDEN NOTLAR', 'title' => 'Konya’da galibiyetin adı: takım oyunu.',
                'publish_date' => '2026-09-21',
                'summary' => '91–81’lik deplasman galibiyeti ve Sessoms’un 35 sayısı.',
                'content' => "Konya Büyükşehir Belediyespor deplasmanında Yalova Haremspor, mücadeleyi 91–81 kazanarak ligde önemli bir galibiyet aldı. Sam Sessoms, 35 sayı, 4 ribaund ve 4 asistle karşılaşmanın öne çıkan oyuncusu oldu.\n\nİlk çeyrekte üretilen 10 asist, hücumdaki paylaşımın dikkat çeken göstergesiydi. Abdoulaye Gueye de 16 sayıyla skora katkı verdi.",
                'image_url' => 'assets/images/saha.jpg', 'source_title' => 'Kaynak: Yalovamız', 'source_url' => 'https://www.yalovamiz.com/spor/sessoms-35-atti-yalova-konya-da-kazandi-66638', 'order' => 3
            ],
            [
                'type' => 'blog', 'category' => 'MAÇ HİKÂYESİ', 'title' => 'Son topa kadar. Son düdüğe kadar.',
                'publish_date' => '2026-09-26',
                'summary' => 'iLab karşısında tek sayılık fark, büyük bir heyecan.',
                'content' => "Yalova Haremspor, iLab Basketbol karşılaşmasından 82–81 galip ayrıldı. Son topa kadar süren mücadele, sezonun ilk haftalarındaki en heyecanlı maç hikâyelerinden birine dönüştü.\n\nBir sayılık farkla gelen galibiyet, skor tabelasındaki her pozisyonun önemini hatırlattı.",
                'image_url' => 'assets/images/tribun.jpg', 'source_title' => 'Kaynak: Fark Yalova’da', 'source_url' => 'https://www.farkyalovada.com/haber/yalova-harem-82-ilam-81-14861.html', 'order' => 4
            ],
        ];

        foreach ($blogs as $b) {
            MediaArticle::create($b);
        }

        $press = [
            [
                'type' => 'press', 'category' => 'HABERCİ', 'title' => 'Yalova Harem zirveye abone.',
                'publish_date' => '2026-09-29',
                'summary' => 'Üçüncü haftayı üç galibiyetle tamamlayan Yalova Haremspor, namağlup liderliğiyle basında.',
                'image_url' => 'assets/images/saha.jpg', 'source_title' => 'Haberci', 'source_url' => 'https://haberci.com.tr/yalova-harem-zirveye-abone/', 'order' => 1
            ],
            [
                'type' => 'press', 'category' => 'YALOVAMIZ', 'title' => 'Sessoms 35 attı, Yalova Konya’da kazandı.',
                'publish_date' => '2026-09-21',
                'summary' => 'Konya deplasmanındaki 91–81’lik galibiyetin öne çıkanları.',
                'image_url' => 'assets/images/birlik.jpg', 'source_title' => 'Yalovamız', 'source_url' => 'https://www.yalovamiz.com/spor/sessoms-35-atti-yalova-konya-da-kazandi-66638', 'order' => 2
            ],
            [
                'type' => 'press', 'category' => 'YALOVA GAZETESİ', 'title' => 'Yalova Belediyesi ve Haremspor güçlerini birleştirdi.',
                'publish_date' => '2026-07-07',
                'summary' => 'Yalova basketbolunda yeni dönemi başlatan iş birliği basına yansıdı.',
                'image_url' => 'assets/images/identity-yuruyen-kosk.jpg', 'source_title' => 'Yalova Gazetesi', 'source_url' => 'https://www.yalovagazetesi.com/yalova-belediyesi-ve-haremspor-guclerini-birlestirdi', 'order' => 3
            ],
            [
                'type' => 'press', 'category' => 'FARK YALOVA’DA', 'title' => 'Yalova Harem 82, iLab 81.',
                'publish_date' => '2026-09-26',
                'summary' => 'Son saniyeye taşınan mücadelede kazanan Yalova Haremspor oldu.',
                'image_url' => 'assets/images/tribun.jpg', 'source_title' => 'Fark Yalova’da', 'source_url' => 'https://www.farkyalovada.com/haber/yalova-harem-82-ilam-81-14861.html', 'order' => 4
            ],
        ];

        foreach ($press as $p) {
            MediaArticle::create($p);
        }

        Schema::enableForeignKeyConstraints();
    }
}
