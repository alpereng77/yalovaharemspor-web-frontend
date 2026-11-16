<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            // Açılıp kapatılabilen sabit şartlar (bkz. Giveaway::REQUIREMENTS)
            $table->json('requirements')->nullable()->after('rules');
        });

        Schema::table('giveaway_entries', function (Blueprint $table) {
            // Sitedeki katılım formu
            $table->dateTime('registered_at')->nullable()->after('first_commented_at');
            $table->string('youtube_handle')->nullable()->after('registered_at');
            $table->string('registration_ip_hash', 64)->nullable()->after('youtube_handle');
            // Mobil uygulama üzerinden katılım (uygulama indirme şartı)
            $table->dateTime('app_verified_at')->nullable()->after('first_commented_at');
            $table->string('app_device_hash', 64)->nullable()->after('app_verified_at');
            $table->index(['giveaway_id', 'app_device_hash']);
        });

        Schema::table('giveaway_picks', function (Blueprint $table) {
            // Kazanan onayında elle yapılan kontroller: {"instagram_follow": "yes"|"no", ...}
            $table->json('checks')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('giveaway_picks', fn(Blueprint $t) => $t->dropColumn('checks'));
        Schema::table('giveaway_entries', function (Blueprint $t) {
            $t->dropIndex(['giveaway_id', 'app_device_hash']);
            $t->dropColumn(['registered_at', 'youtube_handle', 'registration_ip_hash', 'app_verified_at', 'app_device_hash']);
        });
        Schema::table('giveaways', fn(Blueprint $t) => $t->dropColumn('requirements'));
    }
};
