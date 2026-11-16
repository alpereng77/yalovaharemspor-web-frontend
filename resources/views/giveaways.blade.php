<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @php $first = $giveaways->first(); @endphp
  <title>{{ $single && $first ? $first->title . ' · ' : '' }}Çekilişler · {{ $settings['club_name'] ?? 'Yalova Haremspor' }}</title>
  <meta name="description" content="{{ $single && $first ? $first->prize . ' çekilişi. ' . \Illuminate\Support\Str::limit($first->description, 120) : 'Yalova Haremspor Instagram çekilişleri: katılım şartları ve kazananlar.' }}">
  <link rel="icon" href="{{ asset('assets/images/yalova-logo.jpg') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css?v=20261015-laravel') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,700;0,800;0,900;1,800;1,900&family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { background: var(--darker-bg, #020b06); color: #e8efe9; }
    .gw-page { padding: 140px 0 80px; min-height: 70vh; }
    .gw-page .container { max-width: 1100px; }
    .gw-hero h1 { font-family: 'Barlow Condensed', sans-serif; font-weight: 900; font-size: clamp(40px, 7vw, 76px); line-height: .92; letter-spacing: -.5px; color: #fff; margin: 0 0 14px; text-transform: uppercase; }
    .gw-hero h1 em { color: var(--red, #a70101); font-style: italic; }
    .gw-hero p { color: #9fb3a6; font-size: 17px; max-width: 620px; margin: 0 0 48px; }
    .gw-card { position: relative; background: linear-gradient(160deg, rgba(0, 63, 32, .55), rgba(2, 26, 13, .9)); border: 1px solid rgba(255, 255, 255, .08); border-radius: 14px; padding: 32px; margin-bottom: 28px; overflow: hidden; }
    .gw-card::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--red, #a70101); }
    .gw-card.is-completed::before { background: #e5a93c; }
    .gw-top { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; align-items: flex-start; margin-bottom: 18px; }
    .gw-status { display: inline-flex; align-items: center; gap: 8px; font: 800 13px/1 'Barlow', sans-serif; letter-spacing: 1.5px; text-transform: uppercase; padding: 8px 12px; border-radius: 999px; background: rgba(167, 1, 1, .18); color: #ff8a8a; }
    .gw-status.is-drawn { background: rgba(229, 169, 60, .15); color: #e5a93c; }
    .gw-status.is-completed { background: rgba(104, 211, 145, .14); color: #68d391; }
    .gw-status .dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: gwPulse 1.6s infinite; }
    @keyframes gwPulse { 50% { opacity: .3; } }
    @media (prefers-reduced-motion: reduce) { .gw-status .dot { animation: none; } }
    .gw-card h2 { font-family: 'Barlow Condensed', sans-serif; font-weight: 900; font-size: clamp(30px, 4.5vw, 44px); line-height: 1; color: #fff; margin: 14px 0 6px; text-transform: uppercase; }
    .gw-card h2 a { color: inherit; text-decoration: none; }
    .gw-prize { font-size: 18px; color: #e5a93c; font-weight: 700; margin: 0 0 14px; }
    .gw-desc { color: #b9c9be; line-height: 1.65; margin: 0 0 22px; white-space: pre-line; }
    .gw-body { display: grid; grid-template-columns: 1.2fr 1fr; gap: 28px; }
    .gw-box { background: rgba(0, 0, 0, .25); border: 1px solid rgba(255, 255, 255, .06); border-radius: 10px; padding: 22px; }
    .gw-box h3 { font: 800 13px/1 'Barlow', sans-serif; letter-spacing: 2px; color: #9fb3a6; margin: 0 0 16px; text-transform: uppercase; }
    .gw-rules { list-style: none; counter-reset: r; margin: 0; padding: 0; }
    .gw-rules li { counter-increment: r; display: flex; gap: 12px; align-items: baseline; padding: 9px 0; border-bottom: 1px dashed rgba(255, 255, 255, .08); color: #e8efe9; }
    .gw-rules li:last-child { border-bottom: 0; }
    .gw-rules li::before { content: counter(r, decimal-leading-zero); font: 900 16px/1 'Barlow Condensed', sans-serif; color: var(--red, #a70101); min-width: 22px; }
    .gw-count { display: flex; gap: 10px; margin-bottom: 18px; }
    .gw-count div { flex: 1; text-align: center; background: rgba(0, 0, 0, .3); border-radius: 8px; padding: 12px 4px; }
    .gw-count b { display: block; font: 900 30px/1 'Barlow Condensed', sans-serif; color: #fff; font-variant-numeric: tabular-nums; }
    .gw-count span { font-size: 11px; letter-spacing: 1.5px; color: #9fb3a6; text-transform: uppercase; }
    .gw-meta { font-size: 14px; color: #9fb3a6; line-height: 1.7; margin: 0 0 18px; }
    .gw-btn { display: inline-flex; align-items: center; gap: 8px; background: var(--red, #a70101); color: #fff; font: 800 14px/1 'Barlow', sans-serif; letter-spacing: 1px; padding: 14px 20px; border-radius: 6px; text-decoration: none; text-transform: uppercase; }
    .gw-btn:hover { background: var(--red-hover, #c90202); }
    .gw-btn:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
    .gw-winners { list-style: none; margin: 0; padding: 0; }
    .gw-winners li { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid rgba(255, 255, 255, .07); }
    .gw-winners li:last-child { border-bottom: 0; }
    .gw-winners .rank { width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; background: #e5a93c; color: #1a1205; font: 900 18px/1 'Barlow Condensed', sans-serif; flex: none; }
    .gw-winners a { color: #fff; font: 800 20px/1.2 'Barlow Condensed', sans-serif; text-decoration: none; letter-spacing: .3px; word-break: break-all; }
    .gw-winners a:hover { color: #e5a93c; }
    .gw-note { font-size: 13px; color: #7f948a; margin: 14px 0 0; line-height: 1.6; }
    .gw-rules li { flex-wrap: wrap; }
    .gw-rules li > span:first-of-type { flex: 1; min-width: 160px; }
    .gw-rule-links { display: inline-flex; gap: 8px; flex-wrap: wrap; }
    .gw-rule-links a { font: 700 12px/1 'Barlow', sans-serif; letter-spacing: .5px; color: #fff; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); padding: 7px 10px; border-radius: 999px; text-decoration: none; white-space: nowrap; }
    .gw-rule-links a:hover { background: rgba(255,255,255,.16); }
    .gw-form { margin-top: 24px; background: rgba(0, 0, 0, .3); border: 1px solid rgba(255, 255, 255, .1); border-radius: 10px; padding: 22px; scroll-margin-top: 110px; }
    .gw-form h3 { font: 900 24px/1 'Barlow Condensed', sans-serif; color: #fff; margin: 0 0 16px; text-transform: uppercase; scroll-margin-top: 110px; }
    .gw-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-bottom: 14px; }
    .gw-field { display: flex; flex-direction: column; gap: 6px; font-size: 13px; font-weight: 700; color: #9fb3a6; letter-spacing: .5px; }
    .gw-field input { width: 100%; background: #04140b; border: 1px solid rgba(255, 255, 255, .18); border-radius: 6px; color: #fff; font: 600 16px 'Barlow', sans-serif; padding: 12px 14px; }
    .gw-input-wrap { position: relative; display: block; }
    .gw-input-wrap input { padding-left: 32px; }
    .gw-at { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #9fb3a6; font-weight: 700; }
    .gw-field input:focus { outline: 2px solid #e5a93c; outline-offset: 1px; border-color: transparent; }
    .gw-field input[aria-invalid="true"] { border-color: #ff8a8a; }
    .gw-consent { display: flex; gap: 10px; align-items: flex-start; font-size: 13px; color: #9fb3a6; line-height: 1.5; margin-bottom: 18px; cursor: pointer; }
    .gw-consent input { margin-top: 3px; width: 18px; height: 18px; accent-color: #a70101; flex: none; }
    .gw-form .gw-btn { border: 0; cursor: pointer; }
    .gw-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
    .gw-alert { margin: 0 0 14px; padding: 12px 14px; border-radius: 6px; font-weight: 600; font-size: 14px; }
    .gw-alert.is-ok { background: rgba(104, 211, 145, .14); color: #9ae6b4; }
    .gw-alert.is-err { background: rgba(255, 90, 90, .14); color: #feb2b2; }
    .gw-title-row { display: flex; gap: 18px; align-items: center; }
    .gw-img { width: 96px; height: 96px; border-radius: 12px; object-fit: cover; flex: none; border: 2px solid rgba(255,255,255,.15); transform: rotate(-3deg); }
    @media (max-width: 520px) { .gw-img { width: 72px; height: 72px; } }
    .gw-empty { text-align: center; padding: 70px 20px; border: 1px dashed rgba(255, 255, 255, .12); border-radius: 14px; color: #9fb3a6; }
    .gw-empty b { display: block; font: 900 30px/1.1 'Barlow Condensed', sans-serif; color: #fff; margin-bottom: 8px; text-transform: uppercase; }
    .gw-back { display: inline-block; margin-bottom: 26px; color: #9fb3a6; text-decoration: none; font-weight: 700; font-size: 14px; }
    .gw-back:hover { color: #fff; }
    @media (max-width: 820px) {
      .gw-page { padding: 110px 0 60px; }
      .gw-body { grid-template-columns: 1fr; }
      .gw-card { padding: 22px 18px; }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="{{ route('home') }}" aria-label="Ana sayfa">
        <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor Kulübü logosu">
        <div class="brand-text">
          <span class="city-name">{{ $settings['city_name'] ?? 'YALOVA' }}</span>
          <b class="club-name">{{ $settings['club_name'] ?? 'HAREMSPOR' }}</b>
        </div>
      </a>
      <nav id="nav" aria-label="Ana menü">
        <a href="{{ route('home') }}#kulup">KULÜP</a>
        <a href="{{ route('home') }}#maclar">MAÇ MERKEZİ</a>
        <a href="{{ route('home') }}#oyuncular">OYUNCULAR</a>
        <a href="{{ route('giveaways.index') }}" aria-current="page">ÇEKİLİŞ</a>
      </nav>
      <a class="navcta" href="{{ route('home') }}#maclar">MAÇ MERKEZİ <span aria-hidden="true">↗</span></a>
      <button class="menu" aria-label="Menüyü aç" aria-expanded="false" aria-controls="nav">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </header>

  <main class="gw-page">
    <div class="container">
      @if($single)
        <a class="gw-back" href="{{ route('giveaways.index') }}">← Tüm çekilişler</a>
      @else
        <section class="gw-hero" aria-labelledby="gw-title">
          <h1 id="gw-title">ÇEKİLİŞLER<em>.</em></h1>
          <p>Instagram çekilişlerimizin katılım şartları ve kazananları burada duyurulur. Kazananlar şartları sağlayan katılımcılar arasından rastgele seçilir.</p>
        </section>
      @endif

      @forelse($giveaways as $g)
        @php
          $winners = $g->status === 'completed' ? $g->currentWinners() : collect();
          $statusClass = ['active' => '', 'drawn' => 'is-drawn', 'completed' => 'is-completed'][$g->status] ?? '';
          $statusText = $g->status === 'completed' ? 'Kazananlar Açıklandı' : ($g->status === 'drawn' ? 'Çekiliş Yapıldı' : ($g->is_open ? 'Katılım Devam Ediyor' : 'Katılım Sona Erdi'));
        @endphp
        <article class="gw-card {{ $g->status === 'completed' ? 'is-completed' : '' }}" id="{{ $g->slug }}">
          <div class="gw-top">
            <span class="gw-status {{ $statusClass }}">@if($g->is_open)<span class="dot" aria-hidden="true"></span>@endif {{ $statusText }}</span>
            @if($g->instagram_post_url)
              <a class="gw-btn" href="{{ $g->instagram_post_url }}" target="_blank" rel="noopener noreferrer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                {{ $g->is_open ? 'Gönderiye Git ve Katıl' : 'Gönderiyi Gör' }}
              </a>
            @endif
          </div>

          <div class="gw-title-row">
            @if($g->image_url)<img class="gw-img" src="{{ $g->image_url }}" alt="Çekiliş gönderisi" width="96" height="96" loading="lazy">@endif
            <div>
              <h2>@if(!$single)<a href="{{ route('giveaways.show', $g) }}">{{ $g->title }}</a>@else{{ $g->title }}@endif</h2>
              <p class="gw-prize">🎁 {{ $g->prize }}</p>
            </div>
          </div>
          @if($g->description)<p class="gw-desc">{{ $g->description }}</p>@endif

          <div class="gw-body">
            <div class="gw-box">
              <h3>Katılım Şartları</h3>
              @php
                $ig = $settings['social_instagram'] ?? null;
                $igHandle = $ig ? \App\Models\GiveawayEntry::normalizeUsername(preg_replace('#[?].*$#', '', $ig)) : null;
                $steps = [];
                if ($g->requires('app_download')) {
                  $links = array_filter(['App Store' => $settings['app_store_url'] ?? null, 'Google Play' => $settings['play_store_url'] ?? null]);
                  $steps[] = ['Mobil uygulamamızı indir', $links];
                }
                if ($g->requires('instagram_follow')) $steps[] = ['Instagram\'da ' . ($igHandle ? '@' . $igHandle : 'hesabımızı') . ' takip et', $ig ? ['Takip Et' => $ig] : []];
                if ($g->requires('instagram_like')) $steps[] = ['Çekiliş gönderisini beğen', $g->instagram_post_url ? ['Gönderi' => $g->instagram_post_url] : []];
                if ($g->requires('instagram_comment')) $steps[] = [$g->required_mentions > 0 ? "Gönderiye yorum yap ve {$g->required_mentions} arkadaşını etiketle" : 'Gönderiye yorum yap', $g->instagram_post_url ? ['Yorum Yap' => $g->instagram_post_url] : []];
                if ($g->requires('youtube_subscribe')) $steps[] = ['YouTube kanalımıza abone ol', !empty($settings['social_youtube']) ? ['Abone Ol' => $settings['social_youtube']] : []];
                if ($g->requires('site_registration')) $steps[] = ['Instagram kullanıcı adını aşağıdaki forma yaz', $g->is_open ? ['Forma Git' => '#katil-' . $g->slug] : []];
                foreach ($g->rules ?? [] as $extra) $steps[] = [$extra, []];
              @endphp
              @if($steps)
                <ol class="gw-rules">
                  @foreach($steps as [$text, $links])
                    <li>
                      <span>{{ $text }}</span>
                      @if($links)
                        <span class="gw-rule-links">
                          @foreach($links as $linkText => $href)
                            <a href="{{ $href }}" @if(!str_starts_with($href, '#')) target="_blank" rel="noopener noreferrer" @endif>{{ $linkText }} ↗</a>
                          @endforeach
                        </span>
                      @endif
                    </li>
                  @endforeach
                </ol>
              @else
                <p class="gw-meta" style="margin:0;">Şartlar Instagram gönderisinde yer alıyor.</p>
              @endif
            </div>

            <div class="gw-box">
              @if($g->status === 'completed')
                <h3>{{ $winners->count() > 1 ? 'Kazananlar' : 'Kazanan' }}</h3>
                <ul class="gw-winners">
                  @foreach($winners as $i => $pick)
                    <li>
                      <span class="rank">{{ $i + 1 }}</span>
                      <a href="{{ $pick->entry->profile_url }}" target="_blank" rel="noopener noreferrer nofollow">&#64;{{ $pick->entry->username }}</a>
                    </li>
                  @endforeach
                </ul>
                <p class="gw-note">
                  {{ $g->eligible_count_at_draw }} geçerli katılımcı arasından {{ $g->drawn_at?->timezone('Europe/Istanbul')->format('d.m.Y') }} tarihinde çekildi.
                  Kazananlarla Instagram üzerinden iletişime geçilecektir. Kulübümüz sizden asla ödeme veya şifre istemez.
                </p>
              @elseif($g->status === 'drawn')
                <h3>Sonuç</h3>
                <p class="gw-meta" style="margin:0;">Çekiliş yapıldı. Kazananların şartları sağladığı kontrol ediliyor; sonuç kısa süre içinde bu sayfada duyurulacak.</p>
              @else
                <h3>{{ $g->is_open ? 'Son Katılıma Kalan' : 'Durum' }}</h3>
                @if($g->is_open && $g->ends_at)
                  <div class="gw-count" data-ends="{{ $g->ends_at_iso }}" aria-live="off">
                    <div><b data-u="d">–</b><span>Gün</span></div>
                    <div><b data-u="h">–</b><span>Saat</span></div>
                    <div><b data-u="m">–</b><span>Dakika</span></div>
                  </div>
                @endif
                <p class="gw-meta">
                  @if($g->ends_at) Son katılım: <strong style="color:#fff;">{{ $g->ends_at->locale('tr')->translatedFormat('d F Y, H:i') }}</strong><br>@endif
                  {{ $g->winner_count }} kişi kazanacak. Kazananlar bu sayfada duyurulacak.
                </p>
              @endif
            </div>
          </div>

          @if($g->is_open && $g->requires('site_registration'))
            <form class="gw-form" id="katil-{{ $g->slug }}" action="{{ route('giveaways.register', $g) }}" method="POST" novalidate>
              @csrf
              <h3 id="katil">Çekilişe Katıl</h3>
              @if($single && session('gw_success'))
                <p class="gw-alert is-ok" role="status">{{ session('gw_success') }}</p>
              @endif
              @if($single && $errors->any())
                <p class="gw-alert is-err" role="alert">{{ $errors->first() }}</p>
              @endif
              <div class="gw-fields">
                <label class="gw-field">
                  <span>Instagram kullanıcı adın</span>
                  <span class="gw-input-wrap"><span class="gw-at" aria-hidden="true">@</span>
                    <input type="text" name="instagram_username" required maxlength="60" autocomplete="off" autocapitalize="none" spellcheck="false" inputmode="text" placeholder="kullaniciadi" value="{{ $single ? old('instagram_username') : '' }}" @if($single && $errors->has('instagram_username')) aria-invalid="true" @endif>
                  </span>
                </label>
                @if($g->requires('youtube_subscribe'))
                  <label class="gw-field">
                    <span>YouTube kanal adın</span>
                    <input type="text" name="youtube_channel" required maxlength="150" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder="@kanaladi veya kanal bağlantısı" value="{{ $single ? old('youtube_channel') : '' }}" @if($single && $errors->has('youtube_channel')) aria-invalid="true" @endif>
                  </label>
                @endif
              </div>
              <label class="gw-hp" aria-hidden="true">Web sitesi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
              <label class="gw-consent">
                <input type="checkbox" name="consent" value="1" required @checked($single && old('consent'))>
                <span>Kullanıcı adımın bu çekiliş için kaydedilmesini ve kazanırsam bu sayfada yayınlanmasını kabul ediyorum. Şartları sağlamayan katılımlar geçersiz sayılır.</span>
              </label>
              <button type="submit" class="gw-btn">Çekilişe Katıl ↗</button>
            </form>
          @endif
        </article>
      @empty
        <div class="gw-empty">
          <b>Şu an aktif çekiliş yok</b>
          Yeni çekilişler için Instagram hesabımızı takipte kalın.
          @if(!empty($settings['social_instagram']))
            <div style="margin-top: 22px;"><a class="gw-btn" href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer">Instagram'da Takip Et ↗</a></div>
          @endif
        </div>
      @endforelse
    </div>
  </main>

  <footer class="site-footer" id="footer">
    <div class="container">
      <div class="footer-bottom">
        <span>© {{ date('Y') }} {{ $settings['footer_copyright'] ?? 'Yalova Haremspor Basketbol Kulübü. Tüm Hakları Saklıdır.' }}</span>
        <a href="{{ route('home') }}" style="color: inherit;">Ana Sayfa ↗</a>
      </div>
    </div>
  </footer>

  <script>
    // Mobil menü
    (function () {
      var btn = document.querySelector('.menu'), nav = document.getElementById('nav');
      if (!btn || !nav) return;
      btn.addEventListener('click', function () {
        var open = nav.classList.toggle('open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    })();
    // Geri sayım
    document.querySelectorAll('.gw-count[data-ends]').forEach(function (el) {
      var end = new Date(el.dataset.ends).getTime();
      function tick() {
        var s = Math.max(0, Math.floor((end - Date.now()) / 1000));
        el.querySelector('[data-u="d"]').textContent = Math.floor(s / 86400);
        el.querySelector('[data-u="h"]').textContent = String(Math.floor(s % 86400 / 3600)).padStart(2, '0');
        el.querySelector('[data-u="m"]').textContent = String(Math.floor(s % 3600 / 60)).padStart(2, '0');
        if (s === 0) clearInterval(timer);
      }
      var timer = setInterval(tick, 30000); tick();
    });
  </script>
</body>
</html>
