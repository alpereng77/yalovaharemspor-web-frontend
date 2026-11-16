@extends('admin.layout')

@section('title', 'Veri Senkronizasyonu')
@section('topbar-title', 'VERİ SENKRONİZASYON MERKEZİ')

@section('content')
<style>
  .sync-hero-card {
    background: radial-gradient(circle at 50% 0%, rgba(0, 168, 89, 0.25) 0%, rgba(10, 26, 17, 0.95) 75%);
    border: 2px solid rgba(0, 168, 89, 0.5);
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
    margin-bottom: 2rem;
  }
  .sync-main-btn {
    background: linear-gradient(135deg, #00C853 0%, #007E33 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 18px 45px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(0, 200, 83, 0.45);
    transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
  }
  .sync-main-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 200, 83, 0.6);
    filter: brightness(1.08);
  }
  .sync-main-btn:active {
    transform: translateY(1px);
  }
  .sync-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.25rem;
    margin-top: 2rem;
    text-align: left;
  }
  .sync-stat-item {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .sync-stat-icon {
    font-size: 2rem;
    line-height: 1;
  }
  .sync-stat-title {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 700;
  }
  .sync-stat-val {
    font-size: 1.15rem;
    color: #fff;
    font-weight: 700;
    margin-top: 0.2rem;
  }
</style>

<!-- TBF Tarayıcı Aktarımı (Yer İmi) -->
@php
  $tbfTeam = (int) ($settings['tbf_team_id'] ?? 266056);
  $tbfLeague = (int) ($settings['tbf_league_id'] ?? 22215);
  $tbfSeason = (int) ($settings['tbf_season_id'] ?? 174);
  $tbfPrefix = $settings['tbf_prefix'] ?? 'tbl';
  $tbfJs = '(async()=>{'
    . 'const T=' . json_encode($settings['tbf_import_token'] ?? '') . ',U=' . json_encode(url('/api/tbf-import')) . ';'
    . 'if(!/(^|\.)tbf\.org\.tr$/.test(location.hostname)){alert("Önce tbf.org.tr sitesinde takım sayfasını açın, sonra bu yer imine tıklayın.");return;}'
    . 'const L=' . $tbfLeague . ',q="LeagueId="+L+"&SeasonId=' . $tbfSeason . '&TeamProcessId=' . $tbfTeam . '&prefix=' . $tbfPrefix . '";'
    . 'const g=async p=>{const r=await fetch(p,{credentials:"include"});if(!r.ok)throw new Error(p+" → HTTP "+r.status);return (await r.json()).data;};'
    . 'try{const[standings,matches,stats,players]=await Promise.all(['
    . 'g("/api/League/get-standings-table?leagueId="+L),'
    . 'g("/api/Team/get-team-detail-matches-by-season-and-league?"+q),'
    . 'g("/api/Team/get-team-detail-statistic?"+q),'
    . 'g("/api/Team/get-team-active-player-list-by-season-and-league?"+q)]);'
    . 'const r=await fetch(U,{method:"POST",headers:{"Content-Type":"text/plain"},body:JSON.stringify({token:T,data:{standings,matches,stats,players}})});'
    . 'const j=await r.json();if(!j.success){alert("✗ "+j.message);return;}'
    . 'let ok=0,fail=0;for(const ph of (j.photos_needed||[])){try{'
    . 'const b=await (await fetch(new URL(ph.url).pathname,{credentials:"include"})).blob();'
    . 'const d=await new Promise((res,rej)=>{const f=new FileReader();f.onload=()=>res(f.result);f.onerror=rej;f.readAsDataURL(b);});'
    . 'const pr=await (await fetch(U+"/photo",{method:"POST",headers:{"Content-Type":"text/plain"},body:JSON.stringify({token:T,tbf_id:ph.tbf_id,url:ph.url,data:d})})).json();'
    . 'pr.success?ok++:fail++;}catch(e){fail++;}}'
    . 'alert("✓ "+j.message+(ok||fail?"\\n"+ok+" oyuncu fotoğrafı eklendi"+(fail?", "+fail+" başarısız":"")+".":""));'
    . '}catch(e){alert("TBF aktarımı başarısız: "+e.message);}})();';
  $tbfBookmarklet = 'javascript:' . rawurlencode($tbfJs);
@endphp
<div class="card" style="border-left: 4px solid var(--primary-green);">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
    <h2 class="card-title">TBF'DEN TEK TIKLA AKTAR (YER İMİ)</h2>
    <form action="{{ route('admin.bot.import_token') }}" method="POST" onsubmit="return confirm('Yeni anahtar oluşturulursa eski yer imi çalışmaz. Devam edilsin mi?')">
      @csrf
      <button type="submit" class="btn btn-sm btn-secondary">Anahtarı Yenile</button>
    </form>
  </div>
  <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem;">
    Puan durumu, tüm maçlar ve oyuncu istatistikleri resmi TBF verisinden gelir. Sayfayı siz kendi tarayıcınızda açtığınız için Cloudflare doğrulaması sorun olmaz.
  </p>
  <ol style="color: #e2e8f0; font-size: 0.9rem; line-height: 1.8; margin: 0 0 1rem 1.2rem;">
    <li>Aşağıdaki yeşil butonu tarayıcınızın <strong>yer imleri çubuğuna sürükleyin</strong> (bir kez yapmanız yeterli).</li>
    <li><a href="https://www.tbf.org.tr/ligler/{{ $tbfPrefix }}-2026-2027/takim-detay/{{ $tbfTeam }}/genel-bakis" target="_blank" rel="noopener" style="color: var(--accent-gold);">TBF takım sayfasını açın ↗</a> ve sayfanın yüklenmesini bekleyin.</li>
    <li>Yer imleri çubuğundaki <strong>"TBF → Site"</strong> yer imine tıklayın. Sonuç bir uyarı penceresinde görünür.</li>
  </ol>
  <a href="{{ $tbfBookmarklet }}" onclick="alert('Bu butonu tıklamayın; yer imleri çubuğuna sürükleyin, sonra TBF sayfasındayken oradan tıklayın.'); return false;" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; cursor: grab;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
    <span>TBF → Site</span>
  </a>
  <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.75rem;">
    Yer imi gizli bir anahtar içerir; başkalarıyla paylaşmayın. Paylaşıldıysa "Anahtarı Yenile" ile geçersiz kılın.
  </p>
</div>

<!-- Son İşlem Geçmişi (Loglar) -->
<div class="card">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 class="card-title">SON SENKRONİZASYON LOGLARI</h2>
    @if($logs->count() > 0)
      <form action="{{ route('admin.bot.clear_logs') }}" method="POST" onsubmit="return confirm('Geçmiş kayıtları silmek istiyor musunuz?')">
        @csrf
        <button type="submit" class="btn btn-sm btn-secondary">Geçmişi Temizle</button>
      </form>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 140px;">Tarih & Saat</th>
          <th style="width: 120px;">İşlem</th>
          <th style="width: 90px;">Durum</th>
          <th>Açıklama</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
            {{ $log->created_at->format('d.m.Y H:i:s') }}
          </td>
          <td>
            <code style="color: var(--accent-gold); font-size: 0.8rem;">{{ $log->action }}</code>
          </td>
          <td>
            @if($log->status === 'success')
              <span class="badge badge-success">Başarılı</span>
            @elseif($log->status === 'warning')
              <span class="badge badge-gold">Uyarı</span>
            @else
              <span class="badge badge-danger">Hata</span>
            @endif
          </td>
          <td style="font-size: 0.88rem; color: #fff;">
            {{ $log->message }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
            Henüz işlem kaydı bulunmuyor. Yukarıdaki yeşil butona basarak ilk senkronizasyonu yapabilirsiniz.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
