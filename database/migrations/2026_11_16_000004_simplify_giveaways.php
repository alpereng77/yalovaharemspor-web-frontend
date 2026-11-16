<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            // Yer imiyle aktarılan Instagram gönderisinin görseli (yerel kopya)
            $table->string('image_path')->nullable()->after('instagram_media_id');
        });
        // Mobil uygulama API'si kaldırıldı
        Schema::table('giveaway_entries', function (Blueprint $table) {
            $table->dropIndex(['giveaway_id', 'app_device_hash']);
            $table->dropColumn(['app_verified_at', 'app_device_hash']);
        });
    }

    public function down(): void
    {
        Schema::table('giveaway_entries', function (Blueprint $table) {
            $table->dateTime('app_verified_at')->nullable();
            $table->string('app_device_hash', 64)->nullable();
            $table->index(['giveaway_id', 'app_device_hash']);
        });
        Schema::table('giveaways', fn(Blueprint $t) => $t->dropColumn('image_path'));
    }
};
