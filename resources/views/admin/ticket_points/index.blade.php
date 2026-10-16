@extends('admin.layout')

@section('title', 'Bilet Satış Noktaları')
@section('topbar-title', 'BİLET SATIŞ NOKTALARI')

@section('content')
<div class="card">
  <div class="card-header">
    <div>
      <h2 class="card-title">BİLET SATIŞ NOKTALARI ({{ $ticketPoints->count() }})</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">
        Web sitesinde maç biletlerinin temin edilebileceği fiziksel işletmeler ve gişe noktaları.
      </p>
    </div>
    <a href="{{ route('admin.ticket-points.create') }}" class="btn btn-sm btn-primary">+ Yeni Satış Noktası Ekle</a>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Logo</th>
          <th>İşletme Adı</th>
          <th>Konum / Harita</th>
          <th>Adres & İletişim</th>
          <th>Sıra</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ticketPoints as $point)
        <tr>
          <td style="width: 100px; background: rgba(0,0,0,0.3); text-align: center; border-radius: 4px; padding: 0.5rem;">
            <img src="{{ $point->logo }}" alt="{{ $point->name }}" style="max-width: 80px; max-height: 48px; object-fit: contain;">
          </td>
          <td>
            <strong style="font-size: 0.95rem; color: #fff;">{{ $point->name }}</strong>
            @if($point->working_hours)
              <div style="font-size: 0.75rem; color: var(--accent-gold); margin-top: 2px;">
                Saatler: {{ $point->working_hours }}
              </div>
            @endif
          </td>
          <td>
            @if($point->maps_url)
              <a href="{{ $point->maps_url }}" target="_blank" class="badge badge-gold" style="text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Haritada Gör ↗
              </a>
            @else
              <span style="color: var(--text-muted); font-size: 0.82rem;">Link Yok</span>
            @endif
          </td>
          <td style="max-width: 250px;">
            <div style="font-size: 0.85rem; color: #e2e8f0; line-height: 1.3;">
              {{ $point->address ?? '—' }}
            </div>
            @if($point->phone)
              <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 3px;">
                Tel: {{ $point->phone }}
              </div>
            @endif
          </td>
          <td>{{ $point->order }}</td>
          <td>
            @if($point->is_active)
              <span class="badge badge-success">Yayında</span>
            @else
              <span class="badge badge-danger">Pasif</span>
            @endif
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.ticket-points.edit', $point) }}" class="btn btn-sm btn-secondary">Düzenle</a>
              <form action="{{ route('admin.ticket-points.destroy', $point) }}" method="POST" onsubmit="return confirm('Bu satış noktası silinsin mi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
            <div style="font-size: 1.1rem; margin-bottom: 0.5rem;">Henüz bilet satış noktası eklenmemiş.</div>
            <a href="{{ route('admin.ticket-points.create') }}" class="btn btn-sm btn-primary">+ İlk Satış Noktasını Ekle</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
