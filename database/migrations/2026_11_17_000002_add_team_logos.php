<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rakip takımların gerçek logoları (TBF'den).
 * Logolar yöneticinin tarayıcısıyla TBF'den indirilip sitede saklanır (TBF, sunucudan gelen isteği engeller).
 * Maçlar takımlara TBF takım kimliğiyle bağlanır; logosu olmayan takım için logo uydurulmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_logos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tbf_team_id')->unique();
            $table->string('name')->nullable();
            $table->string('source_url', 500)->nullable(); // TBF'deki logo adresi (değişirse yeniden indirilir)
            $table->string('image_path')->nullable();      // public/ altındaki yerel dosya
            $table->timestamps();
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('home_tbf_team_id')->nullable()->after('away_team');
            $table->unsignedBigInteger('away_tbf_team_id')->nullable()->after('home_tbf_team_id');
        });
    }

    public function down(): void
    {
        Schema::table('matches', fn(Blueprint $t) => $t->dropColumn(['home_tbf_team_id', 'away_tbf_team_id']));
        Schema::dropIfExists('team_logos');
    }
};
