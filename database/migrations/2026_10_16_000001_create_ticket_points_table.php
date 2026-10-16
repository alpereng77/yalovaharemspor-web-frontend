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
        Schema::create('ticket_points', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // İşletme / Satış Noktası Adı
            $table->string('logo_url')->nullable(); // İşletme Logosu
            $table->text('address')->nullable(); // Açık Adres / Konum Bilgisi
            $table->text('maps_url')->nullable(); // Google Haritalar / Konum Linki
            $table->string('phone')->nullable(); // İletişim Telefonu
            $table->string('working_hours')->nullable(); // Çalışma Saatleri (örn: 09:00 - 20:00)
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_points');
    }
};
