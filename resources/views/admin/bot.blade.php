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

<!-- Tek Tuşla Ana Senkronizasyon Alanı -->
<div class="sync-hero-card">
  <div style="margin-bottom: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 68px; height: 68px; border-radius: 50%; background: rgba(0, 168, 89, 0.15); border: 2px solid rgba(0, 168, 89, 0.5); color: #00A859;">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/></svg>
  </div>
  <h1 style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.4rem; font-weight: 900; color: #fff; margin: 0 0 0.5rem; letter-spacing: 0.5px;">
    EUROBASKET & TBF OTOMATİK VERİ BOTU
  </h1>
  <p style="color: #bbf7d0; font-size: 1.05rem; max-width: 680px; margin: 0 auto 2rem; line-height: 1.6;">
    Aşağıdaki butona basarak botu başlatabilirsiniz. Bot web servislerine bağlanıp <strong>tüm oyuncuları, detaylı istatistikleri, maç sonuçlarını, güncel fikstürü ve lig puan durumunu</strong> otomatik olarak çeker ve veritabanına işler.
  </p>

  <form method="POST" action="{{ route('admin.bot.sync_all') }}" onsubmit="return confirm('Eurobasket & TBF Web Botu çalıştırılacaktır. Tüm maçlar, sonuçlar, oyuncular ve istatistikler çekilsin mi?')">
    @csrf
    <button type="submit" class="sync-main-btn">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      <span>BOTU BAŞLAT VE VERİLERİ ÇEK</span>
    </button>
  </form>

  <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap;">
    <a href="https://basketball.eurobasket.com/team/Haremspor-Istanbul/62577/Games" target="_blank" style="background: rgba(255,255,255,0.08); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span>Eurobasket Maçlar (/Games) ↗</span>
    </a>
    <a href="https://basketball.eurobasket.com/team/Haremspor-Istanbul/62577/Roster" target="_blank" style="background: rgba(255,255,255,0.08); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span>Eurobasket Oyuncular (/Roster) ↗</span>
    </a>
    <a href="https://basketball.eurobasket.com/team/Haremspor-Istanbul/62577/Stats" target="_blank" style="background: rgba(255,255,255,0.08); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      <span>Eurobasket İstatistikler (/Stats) ↗</span>
    </a>
  </div>

  <!-- Veri Durumu Kartları -->
  <div class="sync-status-grid">
    <div class="sync-stat-item">
      <div class="sync-stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </div>
      <div>
        <div class="sync-stat-title">Oyuncu Kadrosu</div>
        <div class="sync-stat-val">13 Aktif Basketbolcu</div>
      </div>
    </div>
    <div class="sync-stat-item">
      <div class="sync-stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <div>
        <div class="sync-stat-title">Detaylı İstatistikler</div>
        <div class="sync-stat-val">PPG, RPG, APG, Yüzdeler</div>
      </div>
    </div>
    <div class="sync-stat-item">
      <div class="sync-stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="sync-stat-title">Maçlar & Fikstür</div>
        <div class="sync-stat-val">16 Hafta (6 Oynandı)</div>
      </div>
    </div>
    <div class="sync-stat-item">
      <div class="sync-stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div>
        <div class="sync-stat-title">TBL Puan Durumu</div>
        <div class="sync-stat-val">2. Sıra (11 Puan, 5G 1M)</div>
      </div>
    </div>
  </div>
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
