@extends('admin.layout')

@section('title', 'Gösterge Paneli')
@section('topbar-title', 'GÖSTERGE PANELİ')

@section('content')
<style>
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }
  .stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }
  .stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(167, 1, 1, 0.15);
    color: var(--primary-red);
  }
  .stat-card-info h3 {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 600;
  }
  .stat-card-info .stat-number {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.1;
    color: #fff;
  }

  .dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }
  @media (max-width: 992px) {
    .dash-grid {
      grid-template-columns: 1fr;
    }
  }

  .quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: 1rem;
  }
</style>

<!-- Tek Tıkla Senkronizasyon Banner -->
<div style="background: linear-gradient(135deg, rgba(0, 168, 89, 0.15) 0%, rgba(0, 63, 32, 0.4) 100%); border: 1px solid rgba(0, 168, 89, 0.5); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.6rem;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      <strong style="color: #fff; font-size: 1.15rem; font-family: 'Barlow Condensed'; letter-spacing: 0.5px;">TEK TIKLA TÜM VERİLERİ EŞİTLE (EUROBASKET & TBF)</strong>
    </div>
    <div style="color: #c6e6cf; font-size: 0.85rem; margin-top: 0.25rem;">
      Tüm oyuncular, maç skorları, fikstürler, puan durumu ve teknik kadroyu tek tıkla resmi verilere senkronize edin.
    </div>
  </div>
  <form method="POST" action="{{ route('admin.bot.sync_all') }}" onsubmit="return confirm('Tüm veritabanı (Oyuncular, İstatistikler, Maçlar, Fikstür, Puan Durumu, Teknik Kadro) güncellenecektir. Onaylıyor musunuz?')">
    @csrf
    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #00A859 0%, #006030 100%); font-weight: 800; font-family: 'Barlow Condensed'; font-size: 1.1rem; padding: 10px 22px; border: none; box-shadow: 0 4px 15px rgba(0, 168, 89, 0.4); cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      <span>TÜMÜNÜ ŞİMDİ EŞİTLE</span>
    </button>
  </form>
</div>

<!-- İstatistik Sayaçları -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Kadro Oyuncuları</h3>
      <div class="stat-number">{{ $playerCount }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background: rgba(229, 169, 60, 0.15); color: var(--accent-gold);">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Toplam Maç</h3>
      <div class="stat-number">{{ $matchCount }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background: rgba(72, 187, 120, 0.15); color: #68d391;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Teknik Kadro</h3>
      <div class="stat-number">{{ $staffCount }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background: rgba(66, 153, 225, 0.15); color: #90cdf4;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Sponsorlar</h3>
      <div class="stat-number">{{ $sponsorCount }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background: rgba(237, 137, 54, 0.15); color: #fbd38d;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Bilet Noktaları</h3>
      <div class="stat-number">{{ $ticketPointCount }}</div>
    </div>
  </div>

  <a href="{{ route('admin.newsletters.index') }}" class="stat-card" style="text-decoration: none;">
    <div class="stat-card-icon" style="background: rgba(16, 185, 129, 0.18); color: #34d399;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
    <div class="stat-card-info">
      <h3>Bülten Aboneleri</h3>
      <div class="stat-number">{{ $newsletterCount }}</div>
    </div>
  </a>
</div>

<div class="dash-grid">
  <!-- Sol Bölüm: TBF Bot & Hızlı İşlemler -->
  <div>
    <!-- TBF Bot Kartı -->
    <div class="card" style="border-left: 4px solid var(--accent-gold);">
      <div class="card-header">
        <h2 class="card-title">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>
          TBF VERİ BOTU
        </h2>
        <a href="{{ route('admin.bot.index') }}" class="btn btn-sm btn-gold">Bot Merkezine Git ↗</a>
      </div>
      <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem;">
        TBF resmi web sitesinden (Basketbolcular, Teknik Kadro, İstatistikler ve Maçlar) otomatik veri çekebilir veya Cloudflare koruması devredeyken <strong>"Manuel HTML Ayrıştır"</strong> sekmesiyle saniyeler içinde tüm kadroyu ve istatistikleri güncelleyebilirsiniz.
      </p>

      <form action="{{ route('admin.bot.scrape') }}" method="POST" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        @csrf
        <button type="submit" name="type" value="all" class="btn btn-primary">
          <span>Tüm Verileri TBF'den Çek</span>
        </button>
        <button type="submit" name="type" value="players" class="btn btn-secondary">
          <span>Oyuncuları Güncelle</span>
        </button>
        <button type="submit" name="type" value="stats" class="btn btn-secondary">
          <span>İstatistikleri Çek</span>
        </button>
      </form>
    </div>

    <!-- Son Bot Hareketleri -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">SON BOT İŞLEMLERİ</h2>
        <a href="{{ route('admin.bot.index') }}" class="btn btn-sm btn-secondary">Tümünü Gör</a>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>İşlem</th>
              <th>Durum</th>
              <th>Mesaj</th>
              <th>Zaman</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentLogs as $log)
            <tr>
              <td><code>{{ $log->action }}</code></td>
              <td>
                <span class="badge {{ $log->status === 'success' ? 'badge-success' : ($log->status === 'warning' ? 'badge-gold' : 'badge-danger') }}">
                  {{ strtoupper($log->status) }}
                </span>
              </td>
              <td>{{ Str::limit($log->message, 50) }}</td>
              <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $log->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" style="text-align: center; color: var(--text-muted);">Henüz bir bot işlem kaydı bulunmuyor.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sağ Bölüm: Sıradaki Maç & Hızlı Ekleme -->
  <div>
    <!-- Sıradaki Karşılaşma Kartı -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">SIRADAKİ MAÇ</h2>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-sm btn-secondary">Düzenle</a>
      </div>
      @if($upcomingMatch)
        <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 6px; text-align: center; border: 1px solid var(--border-color);">
          <div style="font-size: 0.8rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase;">
            {{ $upcomingMatch->is_home ? 'İÇ SAHA KARŞILAŞMASI' : 'DEPLASMAN' }}
          </div>
          <div style="font-family: 'Barlow Condensed', sans-serif; font-size: 1.4rem; font-weight: 800; margin: 0.5rem 0;">
            {{ $upcomingMatch->home_team }} <br><span style="color: var(--primary-red); font-size: 1rem;">VS</span><br> {{ $upcomingMatch->away_team }}
          </div>
          <div style="font-size: 0.85rem; color: var(--text-muted);">
            {{ $upcomingMatch->formatted_date }}
          </div>
          <div style="font-size: 0.8rem; color: #fff; margin-top: 0.3rem; display: flex; align-items: center; justify-content: center; gap: 4px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>{{ $upcomingMatch->venue ?? 'Yalova 90. Yıl Spor Salonu' }}</span>
          </div>
        </div>
      @else
        <p style="color: var(--text-muted); font-size: 0.9rem;">Aktif olarak belirlenmiş sıradaki maç bulunmuyor.</p>
      @endif
    </div>

    <!-- Hızlı Eylemler -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">HIZLI YÖNETİM</h2>
      </div>
      <div class="quick-actions">
        <a href="{{ route('admin.players.create') }}" class="btn btn-secondary" style="justify-content: center;">+ Yeni Oyuncu</a>
        <a href="{{ route('admin.matches.create') }}" class="btn btn-secondary" style="justify-content: center;">+ Yeni Maç</a>
        <a href="{{ route('admin.ticket-points.create') }}" class="btn btn-secondary" style="justify-content: center;">+ Yeni Bilet Noktası</a>
        <a href="{{ route('admin.sponsors.create') }}" class="btn btn-secondary" style="justify-content: center;">+ Yeni Sponsor</a>
        <a href="{{ route('admin.media.create') }}" class="btn btn-secondary" style="justify-content: center;">+ Yeni Blog/Haber</a>
      </div>
      <div style="margin-top: 1rem;">
        <a href="{{ route('admin.settings.index') }}" class="btn btn-green" style="width: 100%; justify-content: center; gap: 6px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          <span>Site ve Anasayfa Ayarları</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
