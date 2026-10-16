<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Site Ayarları & İçerik Yönetimi
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // 2. Oyuncular
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('tbf_id')->nullable()->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('jersey_number', 10)->nullable();
            $table->string('position')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('nationality')->default('Türkiye');
            $table->string('image_url')->nullable();
            $table->string('tbf_profile_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Oyuncu İstatistikleri (Detaylı TBF İstatistikleri)
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('season')->default('2026-2027');
            $table->integer('games_played')->default(0); // Maç sayısı (M)
            $table->decimal('minutes_avg', 5, 2)->default(0.00); // Süre ortalaması (DK)
            $table->decimal('points_avg', 5, 2)->default(0.00); // Sayı ortalaması (SAY)
            $table->decimal('rebounds_avg', 5, 2)->default(0.00); // Ribaund ortalaması (RIB)
            $table->decimal('assists_avg', 5, 2)->default(0.00); // Asist ortalaması (AST)
            $table->decimal('steals_avg', 5, 2)->default(0.00); // Top Çalma (TÇ)
            $table->decimal('blocks_avg', 5, 2)->default(0.00); // Blok (BL)
            $table->decimal('turnovers_avg', 5, 2)->default(0.00); // Top Kaybı (TK)
            $table->decimal('fouls_avg', 5, 2)->default(0.00); // Faul (F)
            $table->string('fg_percentage')->default('0%'); // 2 Sayı %
            $table->string('three_point_percentage')->default('0%'); // 3 Sayı %
            $table->string('ft_percentage')->default('0%'); // Serbest Atış %
            $table->integer('total_points')->default(0);
            $table->integer('total_rebounds')->default(0);
            $table->integer('total_assists')->default(0);
            $table->decimal('efficiency_avg', 5, 2)->default(0.00); // Verimlilik puanı (VP)
            $table->timestamps();
        });

        // 4. Teknik Kadro
        Schema::create('technical_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role'); // Başantrenör, Yrd. Antrenör vb.
            $table->string('image_url')->nullable();
            $table->string('nationality')->default('Türkiye');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 5. Maçlar / Fikstür & Sonuçlar
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('tbf_match_id')->nullable()->index();
            $table->integer('round')->nullable(); // Hafta no
            $table->dateTime('match_date')->nullable();
            $table->string('home_team');
            $table->string('away_team');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->boolean('is_home')->default(true);
            $table->string('venue')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, played, postponed
            $table->boolean('is_next_match')->default(false);
            $table->timestamps();
        });

        // 6. Puan Durumu / Sezon İstatistiği
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->string('season')->default('2026-2027');
            $table->integer('rank')->default(1);
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('points')->default(0);
            $table->integer('points_for')->default(0);
            $table->integer('points_against')->default(0);
            $table->timestamps();
        });

        // 7. Sponsorlar
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('RESMİ SPONSOR');
            $table->string('logo_url');
            $table->string('website_url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Medya / Blog ve Basında Biz
        Schema::create('media_articles', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('blog'); // blog, press
            $table->string('category');
            $table->string('title');
            $table->date('publish_date')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url')->nullable();
            $table->string('source_title')->nullable();
            $table->string('source_url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. TBF Bot Çalışma Geçmişi (Logs)
        Schema::create('bot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // scrape_all, scrape_players, scrape_staff, scrape_matches, scrape_stats
            $table->string('status'); // success, warning, error
            $table->text('message')->nullable();
            $table->integer('items_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_logs');
        Schema::dropIfExists('media_articles');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('standings');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('technical_staff');
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('players');
        Schema::dropIfExists('site_settings');
    }
};
