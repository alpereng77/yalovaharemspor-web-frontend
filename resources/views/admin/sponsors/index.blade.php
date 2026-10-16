@extends('admin.layout')

@section('title', 'Sponsorlar')
@section('topbar-title', 'SPONSORLAR')

@section('content')
<div class="card">
  <div class="card-header">
    <h2 class="card-title">DESTEKÇİ VE SPONSORLAR ({{ $sponsors->count() }})</h2>
    <a href="{{ route('admin.sponsors.create') }}" class="btn btn-sm btn-primary">+ Yeni Sponsor Ekle</a>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Logo</th>
          <th>Sponsor Adı</th>
          <th>Kategori / Rozet</th>
          <th>Web Sitesi</th>
          <th>Sıra</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sponsors as $sponsor)
        <tr>
          <td style="width: 140px; background: rgba(0,0,0,0.3); text-align: center; border-radius: 4px;">
            <img src="{{ $sponsor->logo }}" alt="" style="max-width: 120px; max-height: 40px; object-fit: contain;">
          </td>
          <td><strong>{{ $sponsor->name }}</strong></td>
          <td><span class="badge badge-gold">{{ $sponsor->category }}</span></td>
          <td>
            @if($sponsor->website_url)
              <a href="{{ $sponsor->website_url }}" target="_blank" style="color: var(--text-muted); text-decoration: underline; font-size: 0.82rem;">Link ↗</a>
            @else
              <span style="color: var(--text-muted);">—</span>
            @endif
          </td>
          <td>{{ $sponsor->order }}</td>
          <td>
            @if($sponsor->is_active)
              <span class="badge badge-success">Aktif</span>
            @else
              <span class="badge badge-danger">Pasif</span>
            @endif
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-secondary">Düzenle</a>
              <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('Bu sponsor silinsin mi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Kayıtlı sponsor bulunamadı.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
