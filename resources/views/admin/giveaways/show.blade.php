@extends('admin.layout')

@section('title', $giveaway->title . ' · Çekiliş')
@section('topbar-title', 'ÇEKİLİŞ YÖNETİMİ')

@section('content')
<style>
  .gw-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
  .gw-step { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.9rem 1rem; }
  .gw-step b { display: block; font-family: 'Barlow Condensed', sans-serif; font-size: 1.6rem; color: #fff; line-height: 1.1; }
  .gw-step span { color: var(--text-muted); font-size: 0.8rem; }
  .gw-step.is-done { border-color: rgba(104, 211, 145, 0.45); }
  .gw-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; }
  .gw-pick { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.6rem; background: rgba(0,0,0,0.2); flex-wrap: wrap; }
  .gw-pick.is-winner { border-color: rgba(229, 169, 60, 0.55); }
  .gw-pick.is-rejected { opacity: 0.5; }
  .gw-pick .pos { font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 1.3rem; width: 2rem; text-align: center; color: var(--accent-gold); }
  .gw-pick .who { flex: 1; min-width: 160px; }
  .gw-pick .who a { color: #fff; font-weight: 700; text-decoration: none; }
  .gw-pick .who small { display: block; color: var(--text-muted); font-size: 0.78rem; white-space: pre-line; max-height: 3.2em; overflow: hidden; }
  .gw-checks { flex-basis: 100%; margin: 0.4rem 0 0 2.75rem; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; }
  .gw-check { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.4rem 0; flex-wrap: wrap; }
  .gw-check-label strong { display: block; color: #e2e8f0; font-size: 0.85rem; }
  .gw-check-label span { color: var(--text-muted); font-size: 0.78rem; }
  .gw-check-label a { color: var(--accent-gold); }
  .gw-check.is-yes .gw-check-label strong { color: #68d391; }
  .gw-check.is-no .gw-check-label strong { color: #fc8181; }
  .gw-check-actions { display: flex; gap: 0.35rem; }
  .gw-filter a { color: var(--text-muted); text-decoration: none; margin-right: 0.9rem; font-size: 0.85rem; }
  .gw-filter a.active { color: #fff; font-weight: 700; border-bottom: 2px solid var(--primary-red); }
  @media (max-width: 1100px) { .gw-grid { grid-template-columns: 1fr; } .gw-steps { grid-template-columns: 1fr 1fr; } }
  @media (max-width: 600px) { .gw-steps { grid-template-columns: 1fr; } }
</style>

@php
  $winners = $giveaway->currentWinners();
  $reserves = $giveaway->currentReserves();
  $confirmed = $winners->where('status', 'confirmed')->count();
  $hasPicks = $giveaway->picks->isNotEmpty();
@endphp

<div class="card">
  <div class="card-header" style="flex-wrap: wrap; gap: 0.75rem;">
    <div style="display: flex; gap: 1rem; align-items: center;">
      @if($giveaway->image_url)<img src="{{ $giveaway->image_url }}" alt="" width="64" height="64" style="border-radius: 8px; object-fit: cover; flex: none;">@endif
      <div>
      <h2 class="card-title">{{ $giveaway->title }}</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">
        Ödül: <strong style="color: #fff;">{{ $giveaway->prize }}</strong>
        · {{ $giveaway->winner_count }} kazanan, {{ $giveaway->reserve_count }} yedek
        · En az {{ $giveaway->required_mentions }} etiket
        @if($giveaway->ends_at) · Bitiş: {{ $giveaway->ends_at->format('d.m.Y H:i') }} @endif
      </p>
      </div>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <span class="badge {{ $giveaway->status === 'draft' ? 'badge-danger' : ($giveaway->status === 'drawn' ? 'badge-gold' : 'badge-success') }}">{{ $giveaway->status_label }}</span>
      @if($giveaway->status !== 'draft')
        <a href="{{ route('giveaways.show', $giveaway) }}" target="_blank" class="btn btn-sm btn-secondary">Sayfayı Gör ↗</a>
      @endif
      @if($giveaway->instagram_post_url)
        <a href="{{ $giveaway->instagram_post_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">Gönderi ↗</a>
      @endif
      <a href="{{ route('admin.giveaways.edit', $giveaway) }}" class="btn btn-sm btn-secondary">Düzenle</a>
    </div>
  </div>

  <div class="gw-steps">
    <div class="gw-step {{ $totalCount ? 'is-done' : '' }}"><b>{{ $totalCount }}</b><span>1 · Katılımcı hesap</span></div>
    <div class="gw-step {{ $eligibleCount ? 'is-done' : '' }}"><b>{{ $eligibleCount }}</b><span>2 · Şartları sağlayan</span></div>
    <div class="gw-step {{ $hasPicks ? 'is-done' : '' }}"><b>{{ $hasPicks ? $giveaway->drawn_at->timezone('Europe/Istanbul')->format('d.m H:i') : '—' }}</b><span>3 · Çekiliş</span></div>
    <div class="gw-step {{ $giveaway->status === 'completed' ? 'is-done' : '' }}"><b>{{ $confirmed }}/{{ $giveaway->winner_count }}</b><span>4 · Onaylı kazanan · duyuru</span></div>
  </div>

  @if($giveaway->status === 'draft')
    <div class="alert alert-info" style="margin-bottom: 0;"><div>Bu çekiliş taslakta; sitede görünmüyor. Yayınlamak için "Düzenle" bölümünden durumu "Yayında" yapın.</div></div>
  @endif
</div>

<div class="gw-grid">
  {{-- SOL: Çekiliş & kazananlar --}}
  <div>
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
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
          Şartları sağlayan <strong style="color: #fff;">{{ $eligibleCount }}</strong> hesap arasından {{ $giveaway->winner_count }} kazanan ve {{ $giveaway->reserve_count }} yedek rastgele seçilir.
          Seçim, tahmin edilemeyen güvenli rastgele sayı üreteciyle yapılır.
          @if($giveaway->is_open) <br><span style="color: var(--accent-gold);">Not: Son katılım tarihi henüz dolmadı.</span> @endif
        </p>
        <form action="{{ route('admin.giveaways.draw', $giveaway) }}" method="POST" id="gw-draw-form">
          @csrf
          <button class="btn btn-primary" style="padding: 0.9rem 2.4rem; font-size: 1.05rem;" @disabled($eligibleCount === 0)>🎲 BAŞLA!</button>
        </form>
      @else
        @php
          $manual = $giveaway->manualChecks();
          $clubIg = rtrim((string) $giveaway->instagram_profile_url, '/');
          $likedBy = $giveaway->instagram_post_url ? rtrim(preg_replace('#[?].*$#', '', $giveaway->instagram_post_url), '/') . '/liked_by/' : null;
        @endphp
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
          {{ $giveaway->eligible_count_at_draw }} geçerli katılımcı arasından {{ $giveaway->drawn_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }} tarihinde çekildi.
          @if($manual)
            Otomatik kontrol edilemeyen şartları her kazanan için işaretleyin. Tüm şartlar <strong style="color:#68d391;">Evet</strong> olunca kazanan onaylanır; biri <strong style="color:#fc8181;">Hayır</strong> olursa elenir ve sıradaki yedek kazanan olur.
          @else
            Elle kontrol edilecek şart yok; kazananları onaylayıp duyurabilirsiniz.
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
              <small>
                {{ $pick->note ? $pick->note . ' · ' : '' }}
                @if($e->youtube_handle) YouTube: <a href="{{ $e->youtube_url }}" target="_blank" rel="noopener" style="font-weight: 400; color: #cbd5e0;">{{ $e->youtube_handle }} ↗</a> · @endif
                {{ \Illuminate\Support\Str::limit($e->comment_text, 120) }}
              </small>
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
                  <form action="{{ route('admin.giveaways.picks.decide', [$giveaway, $pick]) }}" method="POST">@csrf<input type="hidden" name="status" value="pending"><button class="btn btn-sm btn-secondary" title="Kontrolleri sıfırla">↺</button></form>
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
                            @if($clubIg)<a href="{{ $clubIg }}/followers/" target="_blank" rel="noopener">Takipçilerimizde ara ↗</a>@endif
                            veya <a href="{{ $e->profile_url }}" target="_blank" rel="noopener">profilinden bak ↗</a>
                            @break
                          @case('instagram_like')
                            @if($likedBy)<a href="{{ $likedBy }}" target="_blank" rel="noopener">Gönderiyi beğenenlerde ara ↗</a>@else Gönderi bağlantısı eklenmemiş. @endif
                            @break
                          @case('youtube_subscribe')
                            @if($e->youtube_handle)
                              <a href="{{ $e->youtube_url }}" target="_blank" rel="noopener">Kanalını aç ↗</a> ·
                              <a href="https://studio.youtube.com/" target="_blank" rel="noopener">YouTube Studio'da aboneler ↗</a> · gizliyse ekran görüntüsü isteyin
                            @else
                              Kanal adı verilmemiş; kazanandan abonelik ekran görüntüsü isteyin.
                            @endif
                            @break
                          @case('app_download')
                            Kazanandan, uygulamada açık profilinin ekran görüntüsünü DM ile isteyin.
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
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
            <form action="{{ route('admin.giveaways.publish', $giveaway) }}" method="POST" onsubmit="return confirm('Kazananlar çekiliş sayfasında herkese açık duyurulsun mu?')">
              @csrf
              <button class="btn btn-primary" @disabled(!$giveaway->canPublish())>📣 Kazananları Duyur</button>
            </form>
            @if($winners->count() < $giveaway->winner_count || $reserves->isEmpty())
              <form action="{{ route('admin.giveaways.extra_reserve', $giveaway) }}" method="POST">
                @csrf <button class="btn btn-secondary">+ Ek Yedek Çek</button>
              </form>
            @endif
          </div>
          @if(!$giveaway->canPublish())
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;">Duyurmak için {{ $giveaway->winner_count }} kazananın tamamı onaylanmalı.</p>
          @endif
        @elseif($giveaway->status === 'completed')
          <div class="alert alert-success" style="margin: 1rem 0 0;"><div>Kazananlar {{ $giveaway->published_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }} tarihinde duyuruldu.</div></div>
          <form action="{{ route('admin.giveaways.unpublish', $giveaway) }}" method="POST" style="margin-top: 0.75rem;" onsubmit="return confirm('Duyuru yayından kaldırılsın mı?')">
            @csrf <button class="btn btn-sm btn-secondary">Duyuruyu Geri Al</button>
          </form>
        @endif
      @endif
    </div>
  </div>

  {{-- SAĞ: Katılımcı ekleme --}}
  <div>
    <div class="card">
      <div class="card-header"><h2 class="card-title">KATILIMCILAR NEREDEN GELİR?</h2></div>
      @if($giveaway->requires('site_registration'))
        <p style="color: #e2e8f0; font-size: 0.9rem; margin-bottom: 0.4rem;"><strong>Sitedeki form:</strong> Kişiler çekiliş sayfasında Instagram kullanıcı adını yazar.</p>
        @if($giveaway->status !== 'draft')
          <input class="form-control" type="text" readonly value="{{ route('giveaways.show', $giveaway) }}" onclick="this.select()" style="font-family: monospace; margin-bottom: 1rem;" aria-label="Çekiliş sayfası bağlantısı">
        @else
          <p style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 1rem;">Çekiliş taslakta; form, yayına alınınca açılır.</p>
        @endif
      @endif
      @if($giveaway->requires('instagram_comment'))
        <p style="color: #e2e8f0; font-size: 0.9rem; margin-bottom: 0.4rem;"><strong>Instagram yorumları:</strong>
          @if($giveaway->comments_synced_at) Son aktarım {{ $giveaway->comments_synced_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}. @endif
        </p>
        @if($hasPicks)
          <p style="color: var(--text-muted); font-size: 0.85rem;">Çekiliş yapıldığı için liste kilitli.</p>
        @else
          <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">instagram.com'da çekiliş gönderisini açıp yer imine tıklayın; yorumlar bu çekilişe aktarılır. Yeni yorumlar için istediğiniz kadar tekrarlayabilirsiniz.</p>
          @include('admin.giveaways._ig-bookmarklet')
          <a href="{{ route('admin.instagram.import') }}" style="color: var(--accent-gold); font-size: 0.85rem; margin-left: 0.5rem;">Nasıl kullanılır?</a>
        @endif
      @endif
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap: wrap; gap: 0.5rem;">
    <h2 class="card-title">KATILIMCILAR</h2>
    <div class="gw-filter">
      <a href="?filtre=hepsi" class="{{ $filter === 'hepsi' ? 'active' : '' }}">Hepsi ({{ $totalCount }})</a>
      <a href="?filtre=gecerli" class="{{ $filter === 'gecerli' ? 'active' : '' }}">Geçerli ({{ $eligibleCount }})</a>
      <a href="?filtre=gecersiz" class="{{ $filter === 'gecersiz' ? 'active' : '' }}">Geçersiz ({{ $totalCount - $eligibleCount }})</a>
    </div>
    @if($totalCount && !$hasPicks)
      <form action="{{ route('admin.giveaways.entries.clear', $giveaway) }}" method="POST" onsubmit="return confirm('Tüm katılımcılar silinsin mi?')">
        @csrf @method('DELETE') <button class="btn btn-sm btn-danger">Listeyi Temizle</button>
      </form>
    @endif
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead><tr><th>Hesap</th><th>Sitede Kayıt</th><th>Yorum</th><th>Etiket</th>@if($giveaway->requires('youtube_subscribe'))<th>YouTube</th>@endif <th>Durum</th></tr></thead>
      <tbody>
        @forelse($entries as $e)
          <tr>
            <td style="white-space: nowrap;"><a href="{{ $e->profile_url }}" target="_blank" rel="noopener" style="color: #fff; text-decoration: none;">&#64;{{ $e->username }}</a></td>
            <td style="white-space: nowrap; font-size: 0.8rem;">@if($e->registered_at)<span style="color:#68d391;">✓</span> <span style="color: var(--text-muted);">{{ $e->registered_at->format('d.m H:i') }}</span>@else<span style="color: var(--text-muted);">—</span>@endif</td>
            <td style="max-width: 380px; color: #cbd5e0; font-size: 0.85rem;">{{ \Illuminate\Support\Str::limit(str_replace("\n", ' · ', $e->comment_text), 160) }} @if($e->comment_count > 1)<span style="color: var(--text-muted);">({{ $e->comment_count }} yorum)</span>@endif</td>
            <td>{{ $e->mention_count }}</td>
            @if($giveaway->requires('youtube_subscribe'))<td style="font-size: 0.8rem;">@if($e->youtube_handle)<a href="{{ $e->youtube_url }}" target="_blank" rel="noopener" style="color:#cbd5e0;">{{ \Illuminate\Support\Str::limit($e->youtube_handle, 22) }}</a>@else — @endif</td>@endif

            <td>
              @if($e->is_eligible)
                <span class="badge badge-success">Geçerli</span>
              @else
                <span class="badge badge-danger">{{ $e->ineligible_reason }}</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Henüz katılımcı yok.@if($giveaway->requires('site_registration')) Katılımcılar çekiliş sayfasındaki formdan kullanıcı adlarını yazdıkça burada görünür.@endif</td></tr>
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

