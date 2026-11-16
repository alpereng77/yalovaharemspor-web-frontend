<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#ffffff">
  @php $first = $giveaways->first(); @endphp
  <title>{{ $single && $first ? $first->title . ' · ' : '' }}Çekilişler · {{ $settings['club_name'] ?? 'Yalova Haremspor' }}</title>
  <meta name="description" content="{{ $single && $first ? $first->prize . ' çekilişi. ' . \Illuminate\Support\Str::limit($first->description, 120) : 'Yalova Haremspor Instagram çekilişleri: katılım şartları ve kazananlar.' }}">
  <link rel="icon" href="{{ asset('assets/images/yalova-logo.jpg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --green: #003F20; --green-soft: #e8f3ec; --red: #A70101; --red-soft: #fdecec;
      --text: #111827; --muted: #6b7280; --line: #e5e7eb; --bg: #f5f6f8; --card: #ffffff;
      --radius: 16px;
    }
    * { box-sizing: border-box; }
    html { -webkit-text-size-adjust: 100%; }
    body { margin: 0; background: var(--bg); color: var(--text); font: 16px/1.55 'Barlow', system-ui, sans-serif; }
    a { color: inherit; }
    img { max-width: 100%; display: block; }

    .top { background: #fff; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 10; }
    .top-in { max-width: 720px; margin: 0 auto; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .brand img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .brand b { font: 800 20px/1 'Barlow Condensed', sans-serif; letter-spacing: .3px; color: var(--green); text-transform: uppercase; }
    .top-link { font-size: 14px; font-weight: 600; color: var(--muted); text-decoration: none; padding: 8px 4px; }
    .top-link:hover { color: var(--text); }

    .wrap { max-width: 720px; margin: 0 auto; padding: 24px 16px 56px; }
    .page-title { font: 800 34px/1.05 'Barlow Condensed', sans-serif; text-transform: uppercase; margin: 4px 0 6px; color: var(--green); }
    .page-sub { color: var(--muted); margin: 0 0 22px; }
    .back { display: inline-block; color: var(--muted); text-decoration: none; font-weight: 600; font-size: 14px; margin-bottom: 14px; padding: 4px 0; }

    .card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; margin-bottom: 18px; box-shadow: 0 1px 2px rgba(17,24,39,.04); }
    .card-img { width: 100%; height: auto; aspect-ratio: 16 / 10; max-height: 360px; object-fit: cover; object-position: center 30%; background: var(--green-soft); }
    .card-body { padding: 20px; }
    .pill { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; padding: 5px 11px; border-radius: 999px; background: var(--green-soft); color: var(--green); }
    .pill.is-closed { background: #f3f4f6; color: var(--muted); }
    .pill.is-done { background: #fff7e6; color: #8a5a00; }
    .pill .dot { width: 7px; height: 7px; border-radius: 50%; background: #16a34a; }
    .title { font: 800 30px/1.1 'Barlow Condensed', sans-serif; text-transform: uppercase; margin: 12px 0 4px; }
    .prize { margin: 0; font-weight: 700; color: var(--red); font-size: 17px; }
    .desc { color: #374151; margin: 12px 0 0; white-space: pre-line; }

    .facts { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    .fact { flex: 1 1 140px; background: var(--bg); border-radius: 12px; padding: 10px 12px; }
    .fact small { display: block; color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
    .fact b { font-size: 16px; font-variant-numeric: tabular-nums; }

    .section { border-top: 1px solid var(--line); padding: 20px; }
    .section h3 { margin: 0 0 12px; font-size: 18px; }
    .steps { list-style: none; margin: 0; padding: 0; counter-reset: s; }
    .steps li { counter-increment: s; display: flex; gap: 12px; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid var(--line); }
    .steps li:last-child { border-bottom: 0; padding-bottom: 0; }
    .steps li::before { content: counter(s); flex: none; width: 28px; height: 28px; border-radius: 50%; background: var(--green); color: #fff; font-weight: 700; font-size: 14px; display: grid; place-items: center; margin-top: 1px; }
    .step-text { flex: 1; min-width: 0; }
    .step-links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    .chip { display: inline-flex; align-items: center; min-height: 36px; padding: 0 14px; border-radius: 999px; border: 1px solid var(--line); background: #fff; font-size: 14px; font-weight: 600; text-decoration: none; color: var(--green); }
    .chip:hover { border-color: var(--green); }

    .form label.lbl { display: block; font-weight: 700; margin-bottom: 6px; }
    .input { position: relative; }
    .input span { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-weight: 700; }
    .input input { width: 100%; height: 52px; border: 1.5px solid #d1d5db; border-radius: 12px; padding: 0 14px 0 34px; font: 600 17px 'Barlow', sans-serif; color: var(--text); background: #fff; }
    .input input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(0,63,32,.15); }
    .input input[aria-invalid="true"] { border-color: var(--red); }
    .consent { display: flex; gap: 10px; align-items: flex-start; margin: 14px 0 16px; font-size: 14px; color: #374151; cursor: pointer; }
    .consent input { width: 20px; height: 20px; margin: 1px 0 0; accent-color: var(--green); flex: none; }
    .btn { display: flex; width: 100%; align-items: center; justify-content: center; gap: 8px; min-height: 52px; border: 0; border-radius: 12px; background: var(--green); color: #fff; font: 700 17px 'Barlow', sans-serif; text-decoration: none; cursor: pointer; }
    .btn:hover { background: #00552b; }
    .btn:focus-visible, .chip:focus-visible, .input input:focus-visible { outline: 3px solid rgba(0,63,32,.35); outline-offset: 2px; }
    .btn.is-light { background: var(--green-soft); color: var(--green); }
    .hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
    .msg { margin: 0 0 14px; padding: 12px 14px; border-radius: 12px; font-weight: 600; font-size: 15px; }
    .msg.ok { background: var(--green-soft); color: var(--green); }
    .msg.err { background: var(--red-soft); color: var(--red); }
    .note { color: var(--muted); font-size: 13px; margin: 12px 0 0; }

    .winners { list-style: none; margin: 0; padding: 0; }
    .winners li { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #fff7e6; border-radius: 12px; margin-bottom: 8px; }
    .winners .n { width: 32px; height: 32px; border-radius: 50%; background: #e5a93c; color: #fff; font-weight: 800; display: grid; place-items: center; flex: none; }
    .winners a { font-weight: 700; font-size: 18px; text-decoration: none; word-break: break-all; }

    .row { display: flex; gap: 14px; align-items: center; padding: 14px; text-decoration: none; }
    .row img { width: 76px; height: 76px; border-radius: 12px; object-fit: cover; flex: none; background: var(--green-soft); }
    .row .t { flex: 1; min-width: 0; }
    .row .t b { display: block; font: 800 20px/1.15 'Barlow Condensed', sans-serif; text-transform: uppercase; }
    .row .t span { color: var(--red); font-weight: 600; font-size: 15px; }
    .row .go { color: var(--green); font-weight: 700; font-size: 22px; flex: none; }

    .empty { text-align: center; padding: 48px 20px; }
    .empty b { display: block; font-size: 20px; margin-bottom: 6px; }
    .empty p { color: var(--muted); margin: 0 0 18px; }
    footer { text-align: center; color: var(--muted); font-size: 13px; padding: 0 16px 32px; }

    @media (min-width: 640px) {
      .wrap { padding-top: 36px; }
      .page-title { font-size: 44px; }
      .card-body, .section { padding: 24px 28px; }
      .title { font-size: 36px; }
    }
  </style>
</head>
<body>
  <header class="top">
    <div class="top-in">
      <a class="brand" href="{{ route('home') }}" aria-label="Ana sayfa">
        <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="">
        <b>{{ $settings['club_name'] ?? 'Yalova Haremspor' }}</b>
      </a>
      <a class="top-link" href="{{ route('home') }}">Ana sayfa</a>
    </div>
  </header>

  <main class="wrap">
    @if($single)
      <a class="back" href="{{ route('giveaways.index') }}">← Tüm çekilişler</a>
    @else
      <h1 class="page-title">Çekilişler</h1>
      <p class="page-sub">Katılım şartları ve kazananlar burada duyurulur.</p>
    @endif

    @forelse($giveaways as $g)
      @php
        $statusText = $g->status === 'completed' ? 'Kazananlar açıklandı' : ($g->status === 'drawn' ? 'Çekiliş yapıldı' : ($g->is_open ? 'Katılım açık' : 'Katılım sona erdi'));
        $pillClass = $g->status === 'completed' ? 'is-done' : ($g->is_open ? '' : 'is-closed');
      @endphp

      @if(!$single)
        {{-- Liste: kısa kart --}}
        <a class="card row" href="{{ route('giveaways.show', $g) }}">
          @if($g->image_url)<img src="{{ $g->image_url }}" alt="" width="76" height="76" loading="lazy">@endif
          <div class="t">
            <span class="pill {{ $pillClass }}">@if($g->is_open)<span class="dot" aria-hidden="true"></span>@endif{{ $statusText }}</span>
            <b style="margin-top: 6px;">{{ $g->title }}</b>
            <span>{{ $g->prize }}</span>
          </div>
          <span class="go" aria-hidden="true">›</span>
        </a>
      @else
        {{-- Tek çekiliş: ayrıntı --}}
        @php
          $winners = $g->status === 'completed' ? $g->currentWinners() : collect();
          $ig = $settings['social_instagram'] ?? null;
          $igHandle = $ig ? \App\Models\GiveawayEntry::normalizeUsername(preg_replace('#[?].*$#', '', $ig)) : null;
          $steps = [];
          if ($g->requires('instagram_follow')) $steps[] = ['Instagram\'da ' . ($igHandle ? '@' . $igHandle : 'hesabımızı') . ' takip et', $ig ? ['Takip et' => $ig] : []];
          if ($g->requires('instagram_like')) $steps[] = ['Çekiliş gönderisini beğen', $g->instagram_post_url ? ['Gönderiyi aç' => $g->instagram_post_url] : []];
          if ($g->requires('instagram_comment')) $steps[] = [$g->required_mentions > 0 ? "Gönderiye yorum yap ve {$g->required_mentions} arkadaşını etiketle" : 'Gönderiye yorum yap', $g->instagram_post_url ? ['Yorum yap' => $g->instagram_post_url] : []];
          $storeLinks = array_filter(['App Store' => $settings['app_store_url'] ?? null, 'Google Play' => $settings['play_store_url'] ?? null]);
          $needsApp = $g->requires('app_download');
          if ($needsApp) $steps[] = ['Mobil uygulamamızı indir ve çekilişe uygulamadaki "Çekiliş" ekranından katıl', $storeLinks];
          elseif ($g->requires('site_registration')) $steps[] = ['Instagram kullanıcı adını aşağıya yaz', []];
          foreach ($g->rules ?? [] as $extra) $steps[] = [$extra, []];
        @endphp
        <article class="card" id="{{ $g->slug }}">
          @if($g->image_url)<img class="card-img" src="{{ $g->image_url }}" alt="Çekiliş görseli" width="720" height="720">@endif
          <div class="card-body">
            <span class="pill {{ $pillClass }}">@if($g->is_open)<span class="dot" aria-hidden="true"></span>@endif{{ $statusText }}</span>
            <h1 class="title">{{ $g->title }}</h1>
            <p class="prize">🎁 {{ $g->prize }}</p>
            @if($g->description)<p class="desc">{{ $g->description }}</p>@endif

            @if(in_array($g->status, ['active'], true))
              <div class="facts">
                @if($g->is_open && $g->ends_at)
                  <div class="fact"><small>Kalan süre</small><b class="countdown" data-ends="{{ $g->ends_at_iso }}">–</b></div>
                @endif
                @if($g->ends_at)
                  <div class="fact"><small>Son katılım</small><b>{{ $g->ends_at->locale('tr')->translatedFormat('d F, H:i') }}</b></div>
                @endif
                <div class="fact"><small>Kazanan</small><b>{{ $g->winner_count }} kişi</b></div>
              </div>
            @endif
          </div>

          @if($g->status === 'completed')
            <div class="section">
              <h3>{{ $winners->count() > 1 ? 'Kazananlar' : 'Kazanan' }} 🎉</h3>
              <ul class="winners">
                @foreach($winners as $i => $pick)
                  <li><span class="n">{{ $i + 1 }}</span><a href="{{ $pick->entry->profile_url }}" target="_blank" rel="noopener noreferrer nofollow">&#64;{{ $pick->entry->username }}</a></li>
                @endforeach
              </ul>
              <p class="note">{{ $g->eligible_count_at_draw }} geçerli katılımcı arasından {{ $g->drawn_at?->timezone('Europe/Istanbul')->format('d.m.Y') }} tarihinde çekildi. Kazananlarla Instagram'dan iletişime geçilecek. Kulübümüz sizden asla ödeme veya şifre istemez.</p>
            </div>
          @elseif($g->status === 'drawn')
            <div class="section">
              <h3>Çekiliş yapıldı</h3>
              <p style="margin: 0; color: #374151;">Kazananların şartları sağladığı kontrol ediliyor. Sonuç kısa süre içinde bu sayfada duyurulacak.</p>
            </div>
          @else
            @if($steps)
              <div class="section">
                <h3>Nasıl katılırım?</h3>
                <ol class="steps">
                  @foreach($steps as [$text, $links])
                    <li>
                      <div class="step-text">
                        {{ $text }}
                        @if($links)
                          <div class="step-links">
                            @foreach($links as $linkText => $href)
                              <a class="chip" href="{{ $href }}" target="_blank" rel="noopener noreferrer">{{ $linkText }} ↗</a>
                            @endforeach
                          </div>
                        @endif
                      </div>
                    </li>
                  @endforeach
                </ol>
              </div>
            @endif

            @if($g->is_open && $needsApp)
              <div class="section">
                <h3>Uygulamadan katıl</h3>
                <p style="margin: 0 0 14px; color: #374151;">Bu çekilişe sadece Yalova Haremspor mobil uygulamasından katılabilirsin. Uygulamayı indir, "Çekiliş" sekmesinden Instagram kullanıcı adını gönder.</p>
                @if($storeLinks)
                  <div class="step-links">
                    @foreach($storeLinks as $store => $href)
                      <a class="btn" href="{{ $href }}" target="_blank" rel="noopener noreferrer" style="flex: 1 1 160px;">{{ $store }}'dan indir</a>
                    @endforeach
                  </div>
                @else
                  <p class="note">Uygulama mağaza bağlantıları yakında burada olacak.</p>
                @endif
              </div>
            @elseif($g->is_open && $g->requires('site_registration'))
              <div class="section">
                <form class="form" id="katil" action="{{ route('giveaways.register', $g) }}" method="POST" novalidate style="scroll-margin-top: 80px;">
                  @csrf
                  @if(session('gw_success'))
                    <p class="msg ok" role="status">✓ {{ session('gw_success') }}</p>
                  @endif
                  @if($errors->any())
                    <p class="msg err" role="alert">{{ $errors->first() }}</p>
                  @endif
                  <label class="lbl" for="ig-user">Instagram kullanıcı adın</label>
                  <div class="input"><span aria-hidden="true">@</span>
                    <input id="ig-user" type="text" name="instagram_username" required maxlength="60" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder="kullaniciadi" value="{{ old('instagram_username') }}" @if($errors->has('instagram_username')) aria-invalid="true" @endif>
                  </div>
                  <label class="hp" aria-hidden="true">Web sitesi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                  <label class="consent">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                    <span>Kullanıcı adımın bu çekiliş için kaydedilmesini ve kazanırsam bu sayfada yayınlanmasını kabul ediyorum.</span>
                  </label>
                  <button type="submit" class="btn">Çekilişe katıl</button>
                  <p class="note">Şartları sağlamayan katılımlar geçersiz sayılır. Kazananlar bu sayfada duyurulur.</p>
                </form>
              </div>
            @elseif(!$g->is_open)
              <div class="section"><p style="margin: 0; color: #374151;">Katılım süresi doldu. Kazananlar yakında bu sayfada duyurulacak.</p></div>
            @elseif($g->instagram_post_url)
              <div class="section"><a class="btn" href="{{ $g->instagram_post_url }}" target="_blank" rel="noopener noreferrer">Instagram'da gönderiye git ↗</a></div>
            @endif
          @endif
        </article>
      @endif
    @empty
      <div class="card empty">
        <b>Şu an aktif çekiliş yok</b>
        <p>Yeni çekilişler için Instagram hesabımızı takipte kalın.</p>
        @if(!empty($settings['social_instagram']))
          <a class="btn" href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" style="max-width: 320px; margin: 0 auto;">Instagram'da takip et ↗</a>
        @endif
      </div>
    @endforelse
  </main>

  <footer>© {{ date('Y') }} {{ $settings['footer_copyright'] ?? 'Yalova Haremspor Basketbol Kulübü.' }}</footer>

  <script>
    // Form gönderildikten sonra sonuç/hata mesajı ekranda görünsün
    (function () {
      var msg = document.querySelector('#katil .msg');
      if (!msg) return;
      document.getElementById('katil').scrollIntoView({ block: 'start' });
      var bad = document.querySelector('#katil [aria-invalid="true"]');
      if (bad) bad.focus({ preventScroll: true });
    })();
    document.querySelectorAll('.countdown[data-ends]').forEach(function (el) {
      var end = new Date(el.dataset.ends).getTime();
      function tick() {
        var s = Math.max(0, Math.floor((end - Date.now()) / 1000));
        var d = Math.floor(s / 86400), h = Math.floor(s % 86400 / 3600), m = Math.floor(s % 3600 / 60);
        el.textContent = s === 0 ? 'Süre doldu' : (d ? d + ' gün ' : '') + h + ' sa ' + m + ' dk';
        if (s === 0) clearInterval(timer);
      }
      var timer = setInterval(tick, 30000); tick();
    });
  </script>
</body>
</html>
