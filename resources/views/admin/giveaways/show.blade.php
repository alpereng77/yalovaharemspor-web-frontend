@extends('admin.layout')

@section('title', $giveaway->title . ' · Çekiliş')
@section('topbar-title', 'ÇEKİLİŞ YÖNETİMİ')

@section('content')
<style>
  .gw-head { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
  .gw-head img { width: 72px; height: 72px; border-radius: 10px; object-fit: cover; flex: none; }
  .gw-head .info { flex: 1; min-width: 220px; }
  .gw-head h2 { margin: 0; }
  .gw-head p { color: var(--text-muted); font-size: 0.88rem; margin: 0.3rem 0 0; }
  .gw-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
  .gw-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 1.25rem; }
  .gw-stat { background: var(--surface-sunken); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.9rem 1rem; }
  .gw-stat b { display: block; font-family: 'Barlow Condensed', sans-serif; font-size: 2rem; line-height: 1; color: var(--text-strong); }
  .gw-stat span { color: var(--text-muted); font-size: 0.82rem; }
  .gw-stat.ok b { color: var(--success-text); }
  .gw-stat.bad b { color: var(--danger-text); }
  .gw-next { display: flex; gap: 0.75rem; align-items: center; margin-top: 1rem; padding: 0.85rem 1rem; border-radius: 10px; background: rgba(229,169,60,0.1); border: 1px solid rgba(229,169,60,0.35); color: var(--warn-text); font-size: 0.92rem; }
  .gw-next b { color: var(--accent-gold); white-space: nowrap; }
  .gw-reasons { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
  .gw-reasons span { font-size: 0.78rem; color: var(--danger-text); background: rgba(252,129,129,0.1); border-radius: 999px; padding: 0.25rem 0.65rem; }

  .gw-draw { text-align: center; padding: 1.5rem 0 0.5rem; }
  .gw-draw .big { font-family: 'Barlow Condensed', sans-serif; font-size: 3.2rem; line-height: 1; color: var(--text-strong); }
  .gw-draw p { color: var(--text-muted); margin: 0.4rem 0 1.25rem; }

  .gw-pick { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 10px; margin-bottom: 0.6rem; background: var(--surface-sunken); flex-wrap: wrap; }
  .gw-pick.is-winner { border-color: rgba(229,169,60,0.55); }
  .gw-pick.is-rejected { opacity: 0.5; }
  .gw-pick .pos { font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 1.3rem; width: 2rem; text-align: center; color: var(--accent-gold); }
  .gw-pick .who { flex: 1; min-width: 160px; }
  .gw-pick .who a { color: var(--text-strong); font-weight: 700; text-decoration: none; }
  .gw-pick .who small { display: block; color: var(--text-muted); font-size: 0.8rem; margin-top: 0.2rem; }
  .gw-checks { flex-basis: 100%; margin: 0.4rem 0 0 2.75rem; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; }
  .gw-check { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.4rem 0; flex-wrap: wrap; }
  .gw-check-label strong { display: block; color: var(--text-soft); font-size: 0.88rem; }
  .gw-check-label span { color: var(--text-muted); font-size: 0.8rem; }
  .gw-check-label a { color: var(--accent-gold); }
  .gw-check.is-yes .gw-check-label strong { color: var(--success-text); }
  .gw-check.is-no .gw-check-label strong { color: var(--danger-text); }
  .gw-check-actions { display: flex; gap: 0.35rem; }

  .gw-filter { display: flex; gap: 0.25rem; flex-wrap: wrap; }
  .gw-filter a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; padding: 0.35rem 0.75rem; border-radius: 999px; }
  .gw-filter a.active { color: var(--text-strong); background: var(--hover); font-weight: 700; }
  .gw-table td { vertical-align: top; }
  .gw-comment { color: var(--text-soft); font-size: 0.85rem; max-width: 420px; }
  @media (max-width: 640px) {
    .gw-stats { gap: 0.5rem; }
    .gw-stat { padding: 0.7rem; }
    .gw-stat b { font-size: 1.6rem; }
    .gw-checks { margin-left: 0; }
    .gw-hide-sm { display: none; }
  }
</style>

@php
  $winners = $giveaway->currentWinners();
  $reserves = $giveaway->currentReserves();
  $hasPicks = $giveaway->picks->isNotEmpty();
  $needsComment = $giveaway->requires('instagram_comment');
  $needsSite = $giveaway->requires('site_registration');
  $needsApp = $giveaway->requires('app_download');
  $rejectedCount = $totalCount - $eligibleCount;

  // Sıradaki adım: yöneticiye ne yapacağını tek cümleyle söyler
  $next = match (true) {
    $giveaway->status === 'draft' => 'Çekiliş taslakta, sitede görünmüyor. "Düzenle"den durumu "Yayında" yapın.',
    $giveaway->status === 'completed' => 'Kazananlar sitede duyuruldu. Çekiliş tamamlandı.',
    $giveaway->status === 'drawn' => 'Kazananın şartlarını aşağıda kontrol edip onaylayın, sonra "Kazananları Duyur"a basın.',
    $totalCount === 0 => $needsComment ? '"Yorumları Aktar" ile Instagram gönderisinin yorumlarını aktarın.' : ($needsApp ? 'Katılımcıların mobil uygulamadan katılmasını bekleyin.' : 'Katılımcıların çekiliş sayfasındaki formu doldurmasını bekleyin.'),
    $eligibleCount === 0 => 'Şartları sağlayan katılımcı yok. Aşağıdaki nedenlere bakın.',
    default => 'Hazır! Aşağıdan çekilişi başlatabilirsiniz.',
  };
@endphp

{{-- Özet --}}
<div class="card">
  <div class="gw-head">
    @if($giveaway->image_url)<img src="{{ $giveaway->image_url }}" alt="" width="72" height="72">@endif
    <div class="info">
      <h2 class="card-title">{{ $giveaway->title }}</h2>
      <p>
        🎁 <strong style="color: var(--text-strong);">{{ $giveaway->prize }}</strong>
        · {{ $giveaway->winner_count }} kazanan + {{ $giveaway->reserve_count }} yedek
        @if($giveaway->ends_at) · Bitiş {{ $giveaway->ends_at->format('d.m.Y H:i') }} @endif
      </p>
    </div>
    <div class="gw-actions">
      <span class="badge {{ $giveaway->status === 'draft' ? 'badge-danger' : ($giveaway->status === 'drawn' ? 'badge-gold' : 'badge-success') }}" style="align-self: center;">{{ $giveaway->status_label }}</span>
      @if($needsComment && !$hasPicks)
        <a href="{{ route('admin.instagram.import') }}" class="btn btn-sm btn-primary">Yorumları Aktar</a>
      @endif
      @if($giveaway->status !== 'draft')
        <a href="{{ route('giveaways.show', $giveaway) }}" target="_blank" class="btn btn-sm btn-secondary">Sayfayı Gör ↗</a>
      @endif
      <a href="{{ route('admin.giveaways.edit', $giveaway) }}" class="btn btn-sm btn-secondary">Düzenle</a>
    </div>
  </div>

  <div class="gw-stats">
    <div class="gw-stat"><b>{{ $totalCount }}</b><span>Katılımcı</span></div>
    <div class="gw-stat ok"><b>{{ $eligibleCount }}</b><span>Geçerli</span></div>
    <div class="gw-stat {{ $rejectedCount ? 'bad' : '' }}"><b>{{ $rejectedCount }}</b><span>Elenen</span></div>
  </div>

  <div class="gw-next"><b>Sıradaki adım</b><span>{{ $next }}</span></div>

  @if($rejectedCount && $ineligibleReasons->isNotEmpty())
    <div class="gw-reasons" aria-label="Elenme nedenleri">
      @foreach($ineligibleReasons as $reason => $count)<span>{{ $reason }}: {{ $count }}</span>@endforeach
    </div>
    @if(!$hasPicks && $eligibleCount === 0 && isset($ineligibleReasons['Sitede kayıt yok']))
      <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.6rem 0 0;">
        "Sitedeki forma kayıt" şartı açık ama yorum yapanlar formu doldurmamış. Sadece Instagram yorumlarıyla çekiliş yapacaksanız
        <a href="{{ route('admin.giveaways.edit', $giveaway) }}" style="color: var(--accent-gold);">bu şartı kapatın</a>.
      </p>
    @endif
  @endif
</div>

{{-- Kazananlar --}}
<div class="card">
  <div class="card-header">
    <h2 class="card-title">KAZANANLAR</h2>
    @if($hasPicks && $giveaway->status === 'drawn')
      <form action="{{ route('admin.giveaways.reset', $giveaway) }}" method="POST" onsubmit="return confirm('Çekiliş sonuçları silinecek, katılımcılar korunacak. Emin misiniz?')">
        @csrf <button class="btn btn-sm btn-secondary">Çekilişi Sıfırla</button>
      </form>
    @endif
  </div>

  @if(!$hasPicks)
    <div class="gw-draw">
      <div class="big">{{ $eligibleCount }}</div>
      <p>geçerli katılımcı arasından <strong style="color: var(--text-strong);">{{ $giveaway->winner_count }} kazanan</strong>@if($giveaway->reserve_count) ve {{ $giveaway->reserve_count }} yedek @endif rastgele seçilecek.
        @if($giveaway->is_open)<br><span style="color: var(--accent-gold);">Not: Son katılım tarihi henüz dolmadı.</span>@endif
      </p>
      <form action="{{ route('admin.giveaways.draw', $giveaway) }}" method="POST" id="gw-draw-form">
        @csrf
        <button class="btn btn-primary" style="padding: 0.95rem 2.6rem; font-size: 1.1rem;" @disabled($eligibleCount === 0)>🎲 ÇEKİLİŞİ BAŞLAT</button>
      </form>
    </div>
  @else
    @php
      $manual = $giveaway->manualChecks();
      $clubIg = rtrim((string) $giveaway->instagram_profile_url, '/');
      $likedBy = $giveaway->instagram_post_url ? rtrim(preg_replace('#[?].*$#', '', $giveaway->instagram_post_url), '/') . '/liked_by/' : null;
    @endphp
    <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1rem;">
      {{ $giveaway->eligible_count_at_draw }} geçerli katılımcı arasından {{ $giveaway->drawn_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }} tarihinde çekildi.
      @if($manual && $giveaway->status === 'drawn')
        Her kazanan için şartları işaretleyin: hepsi <strong style="color: var(--success-text);">Evet</strong> olunca onaylanır, biri <strong style="color: var(--danger-text);">Hayır</strong> olursa elenir ve sıradaki yedek kazanan olur.
      @endif
    </p>

    @foreach($giveaway->picks as $pick)
      @php
        $isWinner = $winners->contains('id', $pick->id);
        $label = $pick->status === 'rejected' ? 'ELENDİ' : ($isWinner ? 'KAZANAN' : 'YEDEK');
        $e = $pick->entry;
      @endphp
      <div class="gw-pick {{ $isWinner ? 'is-winner' : '' }} {{ $pick->status === 'rejected' ? 'is-rejected' : '' }}">
        <div class="pos">{{ $pick->position }}</div>
        <div class="who">
          <a href="{{ $e->profile_url }}" target="_blank" rel="noopener">&#64;{{ $e->username }} ↗</a>
          <span class="badge {{ $pick->status === 'rejected' ? 'badge-danger' : ($isWinner ? 'badge-gold' : '') }}" style="margin-left: 0.4rem;">{{ $label }}</span>
          @if($pick->status === 'confirmed') <span class="badge badge-success">✓ Onaylı</span> @endif
          @if($pick->note || $e->comment_text)
            <small>{{ $pick->note ? $pick->note . ' · ' : '' }}{{ \Illuminate\Support\Str::limit(str_replace("\n", ' · ', (string) $e->comment_text), 110) }}</small>
          @endif
        </div>
        @if($giveaway->status === 'drawn')
          <div style="display: flex; gap: 0.35rem;">
            @if(!$manual && $pick->status !== 'confirmed')
              <form action="{{ route('admin.giveaways.picks.decide', [$giveaway, $pick]) }}" method="POST">@csrf<input type="hidden" name="status" value="confirmed"><button class="btn btn-sm btn-green">Onayla</button></form>
            @endif
            @if($pick->status !== 'rejected')
              <form action="{{ route('admin.giveaways.picks.decide', [$giveaway, $pick]) }}" method="POST" onsubmit="return confirm('&#64;{{ $e->username }} elensin mi?')">@csrf<input type="hidden" name="status" value="rejected"><button class="btn btn-sm btn-danger" title="Başka bir nedenle ele">Ele</button></form>
            @endif
            @if($pick->status !== 'pending' || $pick->checks)
              <form action="{{ route('admin.giveaways.picks.decide', [$giveaway, $pick]) }}" method="POST">@csrf<input type="hidden" name="status" value="pending"><button class="btn btn-sm btn-secondary" title="Kontrolleri sıfırla" aria-label="Kontrolleri sıfırla">↺</button></form>
            @endif
          </div>
        @endif

        @if($manual && $pick->status !== 'rejected')
          <div class="gw-checks">
            @foreach($manual as $key => $checkLabel)
              @php $v = $pick->check($key); @endphp
              <div class="gw-check {{ $v ? 'is-' . $v : '' }}">
                <div class="gw-check-label">
                  <strong>{{ $checkLabel }}</strong>
                  <span>
                    @switch($key)
                      @case('instagram_follow')
                        @if($clubIg)<a href="{{ $clubIg }}/followers/" target="_blank" rel="noopener">Takipçilerde ara ↗</a> · @endif
                        <a href="{{ $e->profile_url }}" target="_blank" rel="noopener">Profili aç ↗</a>
                        @break
                      @case('instagram_like')
                        @if($likedBy)<a href="{{ $likedBy }}" target="_blank" rel="noopener">Beğenenlerde ara ↗</a>@else Gönderi bağlantısı eklenmemiş. @endif
                        @break
                      @case('app_download')
                        Kazanandan uygulamadaki profilinin ekran görüntüsünü isteyin.
                        @break
                    @endswitch
                  </span>
                </div>
                @if($giveaway->status === 'drawn')
                  <div class="gw-check-actions">
                    <form action="{{ route('admin.giveaways.picks.check', [$giveaway, $pick]) }}" method="POST">@csrf<input type="hidden" name="key" value="{{ $key }}"><input type="hidden" name="value" value="{{ $v === 'yes' ? '' : 'yes' }}"><button class="btn btn-sm {{ $v === 'yes' ? 'btn-green' : 'btn-secondary' }}" aria-pressed="{{ $v === 'yes' ? 'true' : 'false' }}">✓ Evet</button></form>
                    <form action="{{ route('admin.giveaways.picks.check', [$giveaway, $pick]) }}" method="POST">@csrf<input type="hidden" name="key" value="{{ $key }}"><input type="hidden" name="value" value="{{ $v === 'no' ? '' : 'no' }}"><button class="btn btn-sm {{ $v === 'no' ? 'btn-danger' : 'btn-secondary' }}" aria-pressed="{{ $v === 'no' ? 'true' : 'false' }}">✗ Hayır</button></form>
                  </div>
                @else
                  <span class="badge {{ $v === 'yes' ? 'badge-success' : 'badge-danger' }}">{{ $v === 'yes' ? 'Evet' : ($v === 'no' ? 'Hayır' : '—') }}</span>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach

    @if($giveaway->status === 'drawn')
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; align-items: center;">
        <form action="{{ route('admin.giveaways.publish', $giveaway) }}" method="POST" onsubmit="return confirm('Kazananlar çekiliş sayfasında herkese açık duyurulsun mu?')">
          @csrf
          <button class="btn btn-primary" @disabled(!$giveaway->canPublish())>📣 Kazananları Duyur</button>
        </form>
        @if($winners->count() < $giveaway->winner_count || $reserves->isEmpty())
          <form action="{{ route('admin.giveaways.extra_reserve', $giveaway) }}" method="POST">
            @csrf <button class="btn btn-secondary">+ Ek Yedek Çek</button>
          </form>
        @endif
        @if(!$giveaway->canPublish())
          <span style="color: var(--text-muted); font-size: 0.82rem;">Duyurmak için {{ $giveaway->winner_count }} kazananın tamamı onaylanmalı.</span>
        @endif
      </div>
    @elseif($giveaway->status === 'completed')
      <div class="alert alert-success" style="margin: 1rem 0 0;"><div>Kazananlar {{ $giveaway->published_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }} tarihinde duyuruldu.</div></div>
      <form action="{{ route('admin.giveaways.unpublish', $giveaway) }}" method="POST" style="margin-top: 0.75rem;" onsubmit="return confirm('Duyuru yayından kaldırılsın mı?')">
        @csrf <button class="btn btn-sm btn-secondary">Duyuruyu Geri Al</button>
      </form>
    @endif
  @endif
</div>

{{-- Katılımcılar --}}
<div class="card">
  <div class="card-header" style="flex-wrap: wrap; gap: 0.5rem;">
    <h2 class="card-title">KATILIMCILAR</h2>
    <nav class="gw-filter" aria-label="Katılımcı filtresi">
      <a href="?filtre=hepsi" class="{{ $filter === 'hepsi' ? 'active' : '' }}">Hepsi ({{ $totalCount }})</a>
      <a href="?filtre=gecerli" class="{{ $filter === 'gecerli' ? 'active' : '' }}">Geçerli ({{ $eligibleCount }})</a>
      <a href="?filtre=gecersiz" class="{{ $filter === 'gecersiz' ? 'active' : '' }}">Elenen ({{ $rejectedCount }})</a>
    </nav>
    @if($totalCount && !$hasPicks)
      <form action="{{ route('admin.giveaways.entries.clear', $giveaway) }}" method="POST" onsubmit="return confirm('Tüm katılımcılar silinsin mi?')">
        @csrf @method('DELETE') <button class="btn btn-sm btn-secondary">Listeyi Temizle</button>
      </form>
    @endif
  </div>
  <div class="table-responsive">
    <table class="table gw-table">
      <thead><tr><th>Hesap</th><th>Durum</th>@if($needsComment)<th class="gw-hide-sm">Yorum</th><th>Etiket</th>@endif @if($needsSite)<th>Formda</th>@endif @if($needsApp)<th>Uygulama</th>@endif</tr></thead>
      <tbody>
        @forelse($entries as $e)
          <tr>
            <td style="white-space: nowrap;"><a href="{{ $e->profile_url }}" target="_blank" rel="noopener" style="color: var(--text-strong); text-decoration: none; font-weight: 600;">&#64;{{ $e->username }}</a></td>
            <td>
              @if($e->is_eligible)
                <span class="badge badge-success">Geçerli</span>
              @else
                <span class="badge badge-danger">{{ $e->ineligible_reason }}</span>
              @endif
            </td>
            @if($needsComment)
              <td class="gw-comment gw-hide-sm">{{ \Illuminate\Support\Str::limit(str_replace("\n", ' · ', (string) $e->comment_text), 140) ?: '—' }}</td>
              <td>{{ $e->mention_count }}</td>
            @endif
            @if($needsSite)
              <td>@if($e->registered_at)<span style="color:var(--success-text);" title="{{ $e->registered_at->format('d.m H:i') }}">✓</span>@else<span style="color: var(--text-muted);">—</span>@endif</td>
            @endif
            @if($needsApp)
              <td>@if($e->app_verified_at)<span style="color: var(--success-text);" title="{{ $e->app_verified_at->format('d.m H:i') }}">📱 ✓</span>@else<span style="color: var(--text-muted);">—</span>@endif</td>
            @endif
          </tr>
        @empty
          <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">Henüz katılımcı yok.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top: 1rem;">{{ $entries->links('pagination::simple-default') }}</div>
</div>
@endsection

@section('scripts')
<style>
  .gw-overlay { position: fixed; inset: 0; z-index: 9999; background: rgba(3, 10, 6, 0.92); display: grid; place-items: center; padding: 1rem; }
  .gw-overlay[hidden] { display: none; }
  .gw-stage { width: min(520px, 100%); text-align: center; }
  .gw-stage img { width: 180px; height: 180px; object-fit: cover; border-radius: 16px; transform: rotate(-4deg); box-shadow: 0 20px 50px rgba(0,0,0,.6); border: 4px solid #fff; }
  .gw-roll { margin: 1.5rem 0 1rem; font-family: 'Barlow Condensed', sans-serif; font-size: clamp(1.8rem, 6vw, 2.6rem); font-weight: 800; color: #fff; min-height: 1.3em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .gw-bar { height: 12px; background: rgba(255,255,255,.12); border-radius: 999px; overflow: hidden; }
  .gw-bar span { display: block; height: 100%; width: 0; background: linear-gradient(90deg, var(--primary-red), var(--accent-gold)); border-radius: 999px; transition: width linear; }
  .gw-hint { color: var(--text-muted); font-size: 0.85rem; margin-top: 0.75rem; }
  .gw-winlist { list-style: none; padding: 0; margin: 1.25rem 0; text-align: left; }
  .gw-winlist li { display: flex; align-items: center; gap: 0.9rem; background: #fff; color: #1a202c; border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 0.6rem; font-weight: 700; font-size: 1.1rem; box-shadow: 0 8px 24px rgba(0,0,0,.35); animation: gwPop .45s both; }
  .gw-winlist li .n { color: var(--primary-red); font-family: 'Barlow Condensed', sans-serif; font-size: 1.4rem; width: 1.4rem; }
  .gw-winlist li small { margin-left: auto; color: #718096; font-weight: 600; font-size: 0.75rem; }
  .gw-confetti { position: fixed; top: -12px; width: 10px; height: 14px; z-index: 10000; pointer-events: none; animation: gwFall linear forwards; }
  @keyframes gwFall { to { transform: translate3d(var(--dx), 105vh, 0) rotate(720deg); } }
  @keyframes gwPop { from { opacity: 0; transform: translateY(12px) scale(.96); } }
  @media (prefers-reduced-motion: reduce) { .gw-winlist li { animation: none; } .gw-confetti { display: none; } }
</style>

<div class="gw-overlay" id="gw-rolling" hidden role="dialog" aria-modal="true" aria-label="Çekiliş yapılıyor">
  <div class="gw-stage">
    @if($giveaway->image_url)<img src="{{ $giveaway->image_url }}" alt="">@endif
    <div class="gw-roll" id="gw-roll-name" aria-live="off">…</div>
    <div class="gw-bar"><span id="gw-roll-bar"></span></div>
    <p class="gw-hint">{{ $eligibleCount }} geçerli katılımcı arasından seçiliyor. Sayfayı kapatmayın.</p>
  </div>
</div>

@if(session('just_drawn') && $giveaway->picks->isNotEmpty())
  <div class="gw-overlay" id="gw-reveal" role="dialog" aria-modal="true" aria-labelledby="gw-reveal-title">
    <div class="gw-stage">
      <h2 id="gw-reveal-title" style="font-family: 'Barlow Condensed', sans-serif; color: #fff; font-size: 2.2rem; margin: 0;">🎉 KAZANANLAR</h2>
      <ol class="gw-winlist">
        @foreach($giveaway->picks as $p)
          <li style="animation-delay: {{ $loop->index * 0.25 }}s; {{ $loop->index >= $giveaway->winner_count ? 'opacity:.75;' : '' }}">
            <span class="n">{{ $p->position }}</span> &#64;{{ $p->entry->username }}
            <small>{{ $loop->index < $giveaway->winner_count ? 'Kazanan' : 'Yedek' }}</small>
          </li>
        @endforeach
      </ol>
      <button type="button" class="btn btn-primary" id="gw-reveal-close" style="padding: 0.8rem 2rem;">Kontrollere Geç</button>
    </div>
  </div>
@endif

<script>
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Çekiliş animasyonu: isimler döner, ardından form gönderilir. Seçim sunucuda güvenli rastgelelikle yapılır.
  var form = document.getElementById('gw-draw-form');
  if (form) {
    var names = @json($eligibleNames);
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!confirm('Çekiliş şimdi yapılsın mı? Bundan sonra katılımcı listesi kilitlenir.')) return;
      var ov = document.getElementById('gw-rolling'), label = document.getElementById('gw-roll-name'), bar = document.getElementById('gw-roll-bar');
      var duration = reduce ? 600 : 4000, start = performance.now();
      ov.hidden = false;
      bar.style.transitionDuration = duration + 'ms';
      requestAnimationFrame(function () { bar.style.width = '100%'; });
      (function spin(now) {
        var t = (now - start) / duration;
        if (names.length) label.textContent = '@' + names[Math.floor(Math.random() * names.length)];
        if (t < 1) setTimeout(function () { spin(performance.now()); }, reduce ? 200 : 60 + t * 140);
        else { label.textContent = 'Kazananlar belirleniyor…'; form.submit(); }
      })(start);
    });
  }

  // Sonuç ekranı ve konfeti
  var reveal = document.getElementById('gw-reveal');
  if (reveal) {
    var closeBtn = document.getElementById('gw-reveal-close');
    closeBtn.focus();
    closeBtn.addEventListener('click', function () { reveal.remove(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && reveal.isConnected) reveal.remove(); });
    if (!reduce) {
      var colors = ['#a70101', '#00A859', '#e5a93c', '#ffffff', '#6b46c1'];
      for (var i = 0; i < 90; i++) {
        var c = document.createElement('i');
        c.className = 'gw-confetti';
        c.style.left = Math.random() * 100 + 'vw';
        c.style.background = colors[i % colors.length];
        c.style.setProperty('--dx', (Math.random() * 200 - 100) + 'px');
        c.style.animationDuration = (2.5 + Math.random() * 2.5) + 's';
        c.style.animationDelay = (Math.random() * 0.8) + 's';
        document.body.appendChild(c);
        c.addEventListener('animationend', function () { this.remove(); });
      }
    }
  }
})();
</script>
@endsection

