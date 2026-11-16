<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mobil uygulamadan katılım: "Uygulamayı indir" şartı, sadece uygulamadaki çekiliş ekranından
 * gönderilen kullanıcı adlarıyla sağlanır. Cihaz kimliği ham hâliyle değil, özetlenerek saklanır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giveaway_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('giveaway_entries', 'app_verified_at')) {
                $table->dateTime('app_verified_at')->nullable()->after('registered_at');
            }
            if (!Schema::hasColumn('giveaway_entries', 'app_device_hash')) {
                $table->string('app_device_hash', 64)->nullable()->after('app_verified_at');
                $table->index(['giveaway_id', 'app_device_hash']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('giveaway_entries', function (Blueprint $table) {
            $table->dropIndex(['giveaway_id', 'app_device_hash']);
            $table->dropColumn(['app_verified_at', 'app_device_hash']);
        });
    }
};
