@extends('admin.layout')

@section('title', 'Teknik Kadro')
@section('topbar-title', 'TEKNİK KADRO')

@section('content')
<div class="card">
  <div class="card-header">
    <h2 class="card-title">TEKNİK VE İDARİ KADRO ({{ $staff->count() }})</h2>
    <div style="display: flex; gap: 0.5rem;">
      <a href="{{ route('admin.bot.index') }}" class="btn btn-sm btn-gold" style="display: inline-flex; align-items: center; gap: 5px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        <span>TBF'den Kadroyu Çek</span>
      </a>
      <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn-primary">+ Yeni Personel Ekle</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Fotoğraf</th>
          <th>Ad Soyad</th>
          <th>Görevi / Rolü</th>
          <th>Sıra</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($staff as $person)
        <tr>
          <td style="width: 50px;">
            <img src="{{ $person->image }}" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-color); background: #000;">
          </td>
          <td><strong>{{ $person->name }}</strong></td>
          <td><span class="badge badge-gold">{{ $person->role }}</span></td>
          <td>{{ $person->order }}</td>
          <td>
            @if($person->is_active)
              <span class="badge badge-success">Aktif</span>
            @else
              <span class="badge badge-danger">Pasif</span>
            @endif
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.staff.edit', $person) }}" class="btn btn-sm btn-secondary">Düzenle</a>
              <form action="{{ route('admin.staff.destroy', $person) }}" method="POST" onsubmit="return confirm('Bu personel silinsin mi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">Kayıtlı teknik kadro personeli bulunamadı.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
