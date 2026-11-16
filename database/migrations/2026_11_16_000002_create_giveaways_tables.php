<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Çekilişler
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('prize');
            $table->text('description')->nullable();
            $table->json('rules')->nullable();                 // Sayfada gösterilen şart listesi
            $table->string('instagram_post_url')->nullable();
            $table->string('instagram_media_id')->nullable();  // API ile çekerken bulunur
            $table->unsignedTinyInteger('required_mentions')->default(2);
            $table->text('excluded_usernames')->nullable();    // Kulüp hesabı, personel vb.
            $table->dateTime('ends_at')->nullable();
            $table->unsignedSmallInteger('winner_count')->default(1);
            $table->unsignedSmallInteger('reserve_count')->default(2);
            // draft: taslak, active: yayında/katılım açık, drawn: çekildi/onay bekliyor, completed: kazananlar duyuruldu
            $table->string('status')->default('draft');
            $table->unsignedInteger('eligible_count_at_draw')->nullable();
            $table->dateTime('drawn_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('comments_synced_at')->nullable();
            $table->timestamps();
        });

        // Katılımcılar (kullanıcı başına tek kayıt)
        Schema::create('giveaway_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giveaway_id')->constrained()->cascadeOnDelete();
            $table->string('username');
            $table->text('comment_text')->nullable();
            $table->unsignedSmallInteger('comment_count')->default(1);
            $table->unsignedSmallInteger('mention_count')->default(0);
            $table->dateTime('first_commented_at')->nullable();
            $table->boolean('is_eligible')->default(false);
            $table->string('ineligible_reason')->nullable();
            $table->string('source')->default('paste'); // paste | csv | api
            $table->timestamps();
            $table->unique(['giveaway_id', 'username']);
        });

        // Çekiliş sırası: önce asıl kazananlar, sonra yedekler
        Schema::create('giveaway_picks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giveaway_id')->constrained()->cascadeOnDelete();
            $table->foreignId('giveaway_entry_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            // pending: takip kontrolü bekliyor, confirmed: onaylandı, rejected: şartı sağlamadı
            $table->string('status')->default('pending');
            $table->string('note')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
            $table->unique(['giveaway_id', 'position']);
            $table->unique(['giveaway_id', 'giveaway_entry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giveaway_picks');
        Schema::dropIfExists('giveaway_entries');
        Schema::dropIfExists('giveaways');
    }
};
