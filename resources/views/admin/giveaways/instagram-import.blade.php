@extends('admin.layout')

@section('title', "Instagram'dan Aktar")
@section('topbar-title', "INSTAGRAM'DAN AKTAR")

@section('content')
<style>
  .ig-wrap { max-width: 1000px; }
  .ig-head { text-align: center; margin-bottom: 1.25rem; }
  .ig-head img { width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-red); background: #000; }
  .ig-head h2 { font-family: 'Barlow Condensed', sans-serif; font-size: 1.8rem; color: var(--text-strong); margin: 0.6rem 0 0.2rem; }
  .ig-head p { color: var(--text-muted); margin: 0; }
  .ig-tools { display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap; margin-bottom: 1rem; }
  .ig-tools select { max-width: 220px; }
  .ig-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
  .ig-post { position: relative; border-radius: 12px; overflow: hidden; background: #0b1712; aspect-ratio: 1; border: 2px solid transparent; cursor: pointer; padding: 0; transition: transform .15s, border-color .15s; }
  .ig-post:hover, .ig-post:focus-visible { transform: translateY(-3px); border-color: var(--primary-red); outline: none; }
  .ig-post img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .ig-post .cap { position: absolute; inset: 0; display: grid; place-items: center; padding: 0.75rem; color: var(--text-muted); font-size: 0.78rem; text-align: center; }
  .ig-post img + .cap { display: none; }
  .ig-type { position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,.6); color: #fff; border-radius: 6px; padding: 3px 6px; font-size: 0.7rem; font-weight: 700; }
  .ig-count { position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); background: #fff; color: #1a202c; border-radius: 999px; padding: 4px 12px; font-size: 0.82rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,.35); }
  .ig-has { position: absolute; top: 8px; left: 8px; background: var(--accent-gold); color: #1a1205; border-radius: 6px; padding: 3px 7px; font-size: 0.7rem; font-weight: 800; }
  .ig-state { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
  .ig-state strong { display: block; color: var(--text-strong); font-size: 1.15rem; margin-bottom: 0.4rem; }
  .ig-bar { height: 12px; max-width: 460px; margin: 1.25rem auto 0.5rem; background: rgba(255,255,255,.1); border-radius: 999px; overflow: hidden; }
  .ig-bar span { display: block; height: 100%; width: 0; background: linear-gradient(90deg, var(--primary-red), var(--accent-gold)); transition: width .3s; }
  .ig-spin { width: 34px; height: 34px; border: 3px solid rgba(255,255,255,.15); border-top-color: var(--accent-gold); border-radius: 50%; margin: 0 auto 1rem; animation: igspin 1s linear infinite; }
  @keyframes igspin { to { transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce) { .ig-spin { animation: none; } }
  .ig-preview { display: flex; gap: 1.25rem; align-items: flex-start; flex-wrap: wrap; }
  .ig-preview img { width: 160px; height: 160px; object-fit: cover; border-radius: 12px; }
  .ig-steps li { margin-bottom: 0.4rem; }
  [hidden] { display: none !important; }
</style>

<div class="card ig-wrap">
  {{-- 1) Pencere doğrudan açıldıysa: kurulum --}}
  <div id="ig-setup">
    <div class="card-header"><h2 class="card-title">INSTAGRAM → SİTE YER İMİ</h2></div>
    <p style="color: var(--text-muted); line-height: 1.6;">
      Instagram gönderilerinizi ve yorumlarını, Instagram'ı kendi tarayıcınızda açıp tek tıkla siteye aktarırsınız. Kurulum veya Instagram uygulaması gerekmez.
    </p>
    <ol class="ig-steps" style="color: var(--text-soft); line-height: 1.7; margin: 1rem 0 1.25rem 1.2rem;">
      <li>Aşağıdaki butonu tarayıcınızın <strong>yer imleri çubuğuna sürükleyin</strong> (bir kez yapmanız yeterli).</li>
      <li><a href="https://www.instagram.com/" target="_blank" rel="noopener" style="color: var(--accent-gold);">instagram.com</a>'u açın ve kulüp hesabıyla giriş yapmış olun.</li>
      <li>Kulüp <strong>profil sayfasını</strong> açıp yenileyin (F5), gönderiler görününce yer imine tıklayın. Bu pencerede gönderiler anında listelenir; çekiliş gönderisini seçin.
        <br><small style="color: var(--text-muted);">Gönderi Instagram sekmesinde kendiliğinden açılır ve yorumlar kaydırılarak okunur; Instagram'a ayrıca istek atılmaz.</small></li>
      <li>Yorumlar okunduktan sonra hangi çekilişe aktarılacağını seçip <strong>Aktar</strong>'a basın.</li>
    </ol>
    @include('admin.giveaways._ig-bookmarklet')
    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 1rem; line-height: 1.6;">
      Yer imi kendi isteğini atmaz; Instagram sayfasının yüklediği verileri okur ve yorum listesini sizin yerinize kaydırır. Instagram sayfa düzenini değiştirirse yer imi güncellenmelidir.
      Yanıt olarak yazılan yorumlar dahil edilmez.
      <br><strong style="color: var(--text-soft);">Yer imini güncelledikten sonra yer imleri çubuğundaki eski butonu silip bu butonu yeniden sürükleyin.</strong>
    </p>
  </div>

  {{-- 2) Bekleme --}}
  <div id="ig-wait" class="ig-state" hidden>
    <div class="ig-spin" aria-hidden="true"></div>
    <strong id="ig-wait-title">Instagram'dan veri bekleniyor…</strong>
    <span id="ig-wait-text">Instagram sekmesini kapatmayın.</span>
    <div class="ig-bar" id="ig-bar-wrap" hidden><span id="ig-bar"></span></div>
  </div>

  {{-- 3) Gönderi seçimi --}}
  <div id="ig-pick" hidden>
    <div class="ig-head">
      <img id="ig-user-pic" alt="" hidden>
      <h2 id="ig-user-name">Merhaba</h2>
      <p>Profildeki son gönderiler. Çekiliş yapmak istediğiniz gönderiyi seçin; Instagram sekmesinde açılıp yorumları okunacak.</p>
    </div>
    <div class="ig-tools">
      <select class="form-control" id="ig-filter" aria-label="Gönderi türü">
        <option value="all">Tüm gönderiler</option>
        <option value="image">Fotoğraflar</option>
        <option value="carousel">Albümler</option>
        <option value="reel">Reels</option>
        <option value="video">Videolar</option>
      </select>
      <select class="form-control" id="ig-sort" aria-label="Sıralama">
        <option value="new">En yeni</option>
        <option value="comments">En çok yorum</option>
      </select>
    </div>
    <div class="ig-grid" id="ig-grid"></div>
  </div>

  {{-- 4) Önizleme ve aktarma --}}
  <div id="ig-done" hidden>
    <div class="card-header"><h2 class="card-title">YORUMLAR HAZIR</h2></div>
    <div class="ig-preview">
      <img id="ig-prev-img" alt="" hidden>
      <div style="flex: 1; min-width: 240px;">
        <p id="ig-prev-cap" style="color: var(--text-soft); margin: 0 0 0.75rem; white-space: pre-line;"></p>
        <p style="margin: 0 0 1rem;"><span class="badge badge-gold" id="ig-prev-count"></span> <span class="badge badge-success" id="ig-prev-users"></span></p>
        <p id="ig-prev-note" hidden style="font-size: 0.85rem; margin: 0 0 1rem;"></p>
        <details class="ig-diag" hidden style="margin: 0 0 1rem;"><summary style="cursor: pointer; color: var(--text-muted); font-size: 0.82rem;">Teknik rapor (destek için kopyalayın)</summary><textarea class="form-control" readonly rows="8" style="font-family: monospace; font-size: 0.72rem; margin-top: 0.5rem;"></textarea></details>
        <div class="form-group">
          <label class="form-label" for="ig-target">Hangi çekilişe aktarılsın?</label>
          <select class="form-control" id="ig-target">
            <option value="new">+ Bu gönderi için yeni çekiliş oluştur</option>
            @foreach($giveaways as $g)
              <option value="{{ $g->slug }}" data-media="{{ $g->instagram_media_id }}">{{ $g->title }} ({{ $g->status === 'draft' ? 'taslak' : 'yayında' }})</option>
            @endforeach
          </select>
          <small style="color: var(--text-muted); display: block; margin-top: 4px;">Seçilen çekilişin yorum listesi bu yorumlarla yenilenir; sitedeki forma kayıt olanlar korunur.</small>
        </div>
        <button class="btn btn-primary" id="ig-send" style="padding: 0.8rem 2rem;">Aktar</button>
        <p id="ig-send-err" class="alert alert-error" hidden style="margin-top: 1rem;"></p>
      </div>
    </div>
  </div>

  {{-- Hata --}}
  <div id="ig-error" class="ig-state" hidden>
    <strong>Aktarım tamamlanamadı</strong>
    <span id="ig-error-text"></span>
    <p style="margin-top: 1rem;">Instagram sekmesine dönüp yer imine tekrar tıklayabilirsiniz.</p>
    <details class="ig-diag" hidden style="margin-top: 1rem; text-align: left;"><summary style="cursor: pointer; font-size: 0.82rem;">Teknik rapor (destek için kopyalayın)</summary><textarea class="form-control" readonly rows="8" style="font-family: monospace; font-size: 0.72rem; margin-top: 0.5rem;"></textarea></details>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
  var IG = /^https:\/\/([a-z0-9-]+\.)?instagram\.com$/;
  var $ = function (id) { return document.getElementById(id); };
  var screens = ['ig-setup', 'ig-wait', 'ig-pick', 'ig-done', 'ig-error'];
  function show(id) { screens.forEach(function (s) { $(s).hidden = s !== id; }); }
  var opener = window.opener, igOrigin = null, posts = [], data = null;
  var typeLabel = { reel: 'REELS', carousel: 'ALBÜM', video: 'VİDEO', image: '' };

  function showDiag(d, extra) {
    if (!d || !d.diag) return;
    var txt = JSON.stringify(Object.assign({}, extra || {}, d.diag), null, 1);
    document.querySelectorAll('.ig-diag').forEach(function (el) { el.hidden = false; el.querySelector('textarea').value = txt; });
  }

  function reply(msg) { if (opener && igOrigin) opener.postMessage(msg, igOrigin); }

  // Yer imi bu pencereyi açtıysa hazır olduğumuzu bildir (Instagram tarafı gönderimi buna göre başlatır)
  if (opener) {
    show('ig-wait');
    try { opener.postMessage({ type: 'ig-ready' }, 'https://www.instagram.com'); } catch (e) {}
    try { opener.postMessage({ type: 'ig-ready' }, 'https://instagram.com'); } catch (e) {}
  }

  function card(p) {
    var b = document.createElement('button');
    b.type = 'button'; b.className = 'ig-post';
    var date = p.time ? new Date(p.time * 1000).toLocaleDateString('tr-TR') : '';
    b.setAttribute('aria-label', (p.caption || 'Gönderi').slice(0, 80) + (typeof p.comments === 'number' ? ' — ' + p.comments + ' yorum' : '') + (date ? ', ' + date : ''));
    if (p.image) {
      var img = document.createElement('img'); img.src = p.image; img.alt = ''; img.loading = 'lazy'; img.referrerPolicy = 'no-referrer';
      img.onerror = function () { img.remove(); };
      b.appendChild(img);
    }
    var cap = document.createElement('span'); cap.className = 'cap'; cap.textContent = (date ? date + ' · ' : '') + (p.caption || 'Önizleme yok').slice(0, 90); b.appendChild(cap);
    if (typeLabel[p.type]) { var t = document.createElement('span'); t.className = 'ig-type'; t.textContent = typeLabel[p.type]; b.appendChild(t); }
    var has = document.querySelector('#ig-target option[data-media="' + p.id + '"]');
    if (has) { var h = document.createElement('span'); h.className = 'ig-has'; h.textContent = 'ÇEKİLİŞ VAR'; b.appendChild(h); }
    if (typeof p.comments === 'number') { var c = document.createElement('span'); c.className = 'ig-count'; c.textContent = '💬 ' + p.comments; b.appendChild(c); }
    else if (p.pinned) { var pn = document.createElement('span'); pn.className = 'ig-count'; pn.textContent = '📌 Sabitlenmiş'; b.appendChild(pn); }
    b.addEventListener('click', function () {
      reply({ type: 'ig-pick', id: p.id, code: p.code });
      $('ig-wait-title').textContent = 'Yorumlar okunuyor…';
      $('ig-wait-text').textContent = 'Instagram sekmesini kapatmayın.';
      show('ig-wait');
    });
    return b;
  }

  function renderGrid() {
    var f = $('ig-filter').value, list = posts.filter(function (p) { return f === 'all' || p.type === f; });
    if ($('ig-sort').value === 'comments') list = list.slice().sort(function (a, b) { return b.comments - a.comments; });
    $('ig-grid').replaceChildren.apply($('ig-grid'), list.map(card));
  }
  $('ig-filter').addEventListener('change', renderGrid);
  $('ig-sort').addEventListener('change', renderGrid);

  window.addEventListener('message', function (e) {
    if (!IG.test(e.origin) || !e.data || typeof e.data.type !== 'string') return;
    igOrigin = e.origin;
    var d = e.data;
    if (d.type === 'ig-posts') {
      posts = Array.isArray(d.posts) ? d.posts : [];
      if (d.user && d.user.username) $('ig-user-name').textContent = 'Merhaba, @' + d.user.username;
      if (d.user && d.user.pic) { $('ig-user-pic').src = d.user.pic; $('ig-user-pic').referrerPolicy = 'no-referrer'; $('ig-user-pic').hidden = false; }
      renderGrid(); show('ig-pick');
    } else if (d.type === 'ig-throttle') {
      if (d.seconds > 0) {
        var mm = Math.floor(d.seconds / 60), ss = d.seconds % 60;
        $('ig-wait-title').textContent = 'Instagram yavaşlamamızı istedi — ' + (mm ? mm + ' dk ' : '') + ss + ' sn sonra devam edilecek';
        $('ig-wait-text').textContent = 'Bekleme ' + d.attempt + ' / ' + d.max + '. Okunan yorumlar korunuyor; iki sekmeyi de kapatmayın.';
      } else {
        $('ig-wait-title').textContent = 'Devam ediliyor…';
        $('ig-wait-text').textContent = 'Instagram sekmesini kapatmayın.';
      }
      if ($('ig-pick').hidden) show('ig-wait');
    } else if (d.type === 'ig-progress') {
      $('ig-wait-text').textContent = 'Gönderi Instagram sekmesinde açıldı ve yorumlar kaydırılarak yükleniyor. Instagram sekmesini kapatmayın.';
      $('ig-wait-title').textContent = 'Yorumlar okunuyor… ' + d.count + (d.total ? ' / ' + d.total : '');
      $('ig-bar-wrap').hidden = !d.total;
      if (d.total) $('ig-bar').style.width = Math.min(100, Math.round(d.count / d.total * 100)) + '%';
      show('ig-wait');
    } else if (d.type === 'ig-data') {
      data = d;
      var users = {};
      (d.comments || []).forEach(function (c) { if (c.username) users[c.username] = 1; });
      $('ig-prev-cap').textContent = (d.post.caption || '').slice(0, 300);
      $('ig-prev-count').textContent = '💬 ' + (d.comments || []).length + ' yorum';
      $('ig-prev-users').textContent = Object.keys(users).length + ' farklı hesap';
      var nTop = (d.comments || []).length, nRep = d.post.replies || 0, igTotal = d.post.comment_count;
      $('ig-prev-count').textContent = '💬 ' + nTop + ' yorum' + (nRep ? ' + ' + nRep + ' yanıt' : '');
      var note = $('ig-prev-note');
      if (!nTop || typeof igTotal !== 'number') showDiag(d, { post: d.post.shortcode, top: nTop, replies: nRep, igTotal: igTotal });
      if (!nTop && igTotal !== 0) {
        note.hidden = false;
        note.className = 'alert alert-error';
        note.textContent = 'Hiç yorum okunamadı. Aşağıdaki teknik raporu kopyalayıp destek için iletin; Instagram profilini yenileyip (F5) tekrar deneyebilirsiniz.';
      } else if (typeof igTotal === 'number') {
        var missing = igTotal - nTop - nRep;
        note.hidden = false;
        if (missing > 0) {
          showDiag(d, { post: d.post.shortcode, top: nTop, replies: nRep, igTotal: igTotal });
          note.className = 'alert alert-error';
          note.textContent = 'Instagram ' + igTotal + ' yorum gösteriyor, ' + (nTop + nRep) + ' tanesi okundu; ' + missing + ' yorum eksik. Instagram profilini yenileyip (F5) yer imini tekrar çalıştırın.';
        } else {
          note.className = 'alert alert-success';
          note.textContent = 'Tamam: Instagram\'daki ' + igTotal + ' yorumun hepsi okundu' + (nRep ? ' (' + nTop + ' ana yorum + ' + nRep + ' yanıt). Yanıtlar çekilişe katılım sayılmaz.' : '.');
        }
      }
      if (d.post.image) { $('ig-prev-img').src = d.post.image; $('ig-prev-img').referrerPolicy = 'no-referrer'; $('ig-prev-img').hidden = false; $('ig-prev-img').onerror = function () { this.hidden = true; }; }
      var same = document.querySelector('#ig-target option[data-media="' + d.post.media_id + '"]');
      if (same) $('ig-target').value = same.value;
      show('ig-done');
    } else if (d.type === 'ig-error') {
      $('ig-error-text').textContent = d.message || 'Bilinmeyen hata';
      showDiag(d, { error: d.message });
      show('ig-error');
    }
  });

  $('ig-send').addEventListener('click', function () {
    if (!data) return;
    var btn = this; btn.disabled = true; btn.textContent = 'Aktarılıyor…'; $('ig-send-err').hidden = true;
    fetch(@json(route('admin.instagram.import.store')), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
      body: JSON.stringify({ giveaway: $('ig-target').value, post: data.post, comments: data.comments })
    })
      .then(function (r) { return r.json().then(function (j) { if (!r.ok || !j.success) throw new Error(j.message || 'Aktarım başarısız (HTTP ' + r.status + ')'); return j; }); })
      .then(function (j) { window.location.href = j.redirect; })
      .catch(function (err) { $('ig-send-err').textContent = err.message; $('ig-send-err').hidden = false; btn.disabled = false; btn.textContent = 'Aktar'; });
  });
})();
</script>
@endsection
