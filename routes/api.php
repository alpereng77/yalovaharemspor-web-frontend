<?php

use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\GiveawayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobil uygulama API'si (v1)
|--------------------------------------------------------------------------
| Herkese açık, sadece okunur içerik + mobil uygulamadan çekiliş katılımı.
| Tüm yanıtlar JSON: { "data": ... } ya da hata için { "message": ..., "errors"?: ... }
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/home', [ContentController::class, 'home'])->name('home');
    Route::get('/club', [ContentController::class, 'club'])->name('club');
    Route::get('/matches', [ContentController::class, 'matches'])->name('matches');
    Route::get('/matches/{match}', [ContentController::class, 'match'])->whereNumber('match')->name('matches.show');
    Route::get('/standing', [ContentController::class, 'standing'])->name('standing');
    Route::get('/players', [ContentController::class, 'players'])->name('players');
    Route::get('/players/{player}', [ContentController::class, 'player'])->whereNumber('player')->name('players.show');
    Route::get('/staff', [ContentController::class, 'staff'])->name('staff');
    Route::get('/news', [ContentController::class, 'news'])->name('news');
    Route::get('/news/{article}', [ContentController::class, 'article'])->whereNumber('article')->name('news.show');
    Route::get('/sponsors', [ContentController::class, 'sponsors'])->name('sponsors');
    Route::get('/ticket-points', [ContentController::class, 'ticketPoints'])->name('ticket_points');

    Route::get('/giveaways', [GiveawayController::class, 'index'])->name('giveaways');
    Route::get('/giveaways/{giveaway}', [GiveawayController::class, 'show'])->name('giveaways.show');

    // Yazma işlemleri: sadece uygulamadan, sıkı istek sınırıyla
    Route::middleware(['mobile.app', 'throttle:10,1'])->group(function () {
        Route::post('/giveaways/{giveaway}/join', [GiveawayController::class, 'join'])->name('giveaways.join');
        Route::post('/newsletter', [ContentController::class, 'subscribe'])->name('newsletter');
    });
});
