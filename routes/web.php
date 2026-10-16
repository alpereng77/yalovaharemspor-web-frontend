<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\TicketPointController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ana Sayfa (Public)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/players/{id}', [HomeController::class, 'playerDetail'])->name('player.detail');
Route::post('/api/tbf-sync', [BotController::class, 'syncFromBrowser'])->name('api.tbf.sync');

Route::post('/newsletter', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:254',
    ], [
        'email.required' => 'Lütfen geçerli bir e-posta adresi girin.',
        'email.email' => 'Lütfen geçerli bir e-posta formatı girin.',
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    $email = strtolower(trim($request->input('email')));
    $subscriber = Newsletter::where('email', $email)->first();

    if ($subscriber) {
        if ($subscriber->status === 'active') {
            $msg = 'Bu e-posta adresi zaten bültenimize kayıtlı.';
        } else {
            $subscriber->update([
                'status' => 'active',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $msg = 'Bülten aboneliğiniz tekrar aktif edildi!';
        }
    } else {
        Newsletter::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'active',
        ]);
        $msg = 'Bültenimize başarıyla kaydoldunuz! Teşekkür ederiz.';
    }

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['success' => true, 'message' => $msg]);
    }

    return back()->with('newsletter_success', $msg);
})->name('newsletter.subscribe');

// Kimlik Doğrulama (Auth)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Yönetim Paneli (Admin Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // TBF & Eurobasket Veri Botu
    Route::get('/bot', [BotController::class, 'index'])->name('bot.index');
    Route::post('/bot/sync-all', [BotController::class, 'syncAll'])->name('bot.sync_all');
    Route::post('/bot/sync-official', [BotController::class, 'syncOfficial'])->name('bot.sync_official');
    Route::post('/bot/scrape', [BotController::class, 'scrape'])->name('bot.scrape');
    Route::post('/bot/parse-manual', [BotController::class, 'parseManualHtml'])->name('bot.parse_manual');
    Route::post('/bot/settings', [BotController::class, 'updateSettings'])->name('bot.settings');
    Route::post('/bot/clear-logs', [BotController::class, 'clearLogs'])->name('bot.clear_logs');

    // Oyuncular & İstatistikler
    Route::resource('players', PlayerController::class);

    // Maç Merkezi & Fikstür
    Route::resource('matches', MatchController::class);
    Route::post('/matches/standing', [MatchController::class, 'updateStanding'])->name('matches.standing');

    // Teknik Kadro
    Route::resource('staff', StaffController::class);

    // Sponsorlar
    Route::resource('sponsors', SponsorController::class);

    // Bilet Satış Noktaları
    Route::resource('ticket-points', TicketPointController::class);

    // Medya / Basında Biz & Bloglar
    Route::resource('media', MediaController::class);

    // Bülten Aboneleri (Newsletter)
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::post('/newsletters', [NewsletterController::class, 'store'])->name('newsletters.store');
    Route::post('/newsletters/{id}/toggle', [NewsletterController::class, 'toggleStatus'])->name('newsletters.toggle');
    Route::delete('/newsletters/{id}', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');
    Route::get('/newsletters/export', [NewsletterController::class, 'export'])->name('newsletters.export');

    // Site Ayarları & Anasayfa Yönetimi
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
