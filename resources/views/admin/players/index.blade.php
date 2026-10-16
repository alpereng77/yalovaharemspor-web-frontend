@extends('admin.layout')

@section('title', 'Oyuncular & İstatistikler')
@section('topbar-title', 'OYUNCULAR VE İSTATİSTİKLER')

@section('content')
<div class="card">
  <div class="card-header">
    <h2 class="card-title">TAKIM OYUNCU KADROSU ({{ $players->total() }})</h2>
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <form method="POST" action="{{ route('admin.bot.sync_all') }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Eurobasket & TBF teyitli TÜM VERİLER (Oyuncular, İstatistikler, Fikstür, Puan Durumu) güncellenecektir. Onaylıyor musunuz?')">
        @csrf
        <button type="submit" class="btn btn-sm btn-success" style="background: #00A859; border: none; font-weight: 700; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          <span>Tüm Verileri Eşitle</span>
        </button>
      </form>
      <a href="{{ route('admin.bot.index') }}" class="btn btn-sm btn-gold" style="display: inline-flex; align-items: center; gap: 5px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>
        <span>Veri Botu</span>
      </a>
      <a href="{{ route('admin.players.create') }}" class="btn btn-sm btn-primary">+ Yeni Oyuncu Ekle</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Fotoğraf</th>
          <th>No</th>
          <th>Ad Soyad</th>
          <th>Mevki</th>
          <th>SAY (PPG)</th>
          <th>RIB (RPG)</th>
          <th>AST (APG)</th>
          <th>Maç (GP)</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($players as $player)
        @php
          $stats = $player->stats;
        @endphp
        <tr>
          <td style="width: 50px;">
            <img src="{{ $player->image }}" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-color); background: #000;">
          </td>
          <td>
            <span style="font-family: 'Barlow Condensed'; font-size: 1.2rem; font-weight: 800; color: var(--accent-gold);">
              #{{ $player->jersey_number ?? '—' }}
            </span>
          </td>
          <td>
            <strong>{{ $player->full_name }}</strong>
            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $player->height ?? '' }} • {{ $player->nationality ?? 'Türkiye' }}</div>
          </td>
          <td>{{ $player->position ?? '—' }}</td>
          <td><strong style="color: var(--accent-gold);">{{ $stats->points_avg ?? '0.0' }}</strong></td>
          <td>{{ $stats->rebounds_avg ?? '0.0' }}</td>
          <td>{{ $stats->assists_avg ?? '0.0' }}</td>
          <td>{{ $stats->games_played ?? '0' }}</td>
          <td>
            @if($player->is_active)
              <span class="badge badge-success">Aktif</span>
            @else
              <span class="badge badge-danger">Pasif</span>
            @endif
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-sm btn-secondary">Düzenle</a>
              <form action="{{ route('admin.players.destroy', $player) }}" method="POST" onsubmit="return confirm('Bu oyuncu silinsin mi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 2rem;">Kayıtlı oyuncu bulunamadı.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 1.25rem;">
    {{ $players->links() }}
  </div>
</div>
@endsection
