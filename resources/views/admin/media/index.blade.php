@extends('admin.layout')

@section('title', 'Medya Odası (Blog & Basında Biz)')
@section('topbar-title', 'MEDYA ODASI')

@section('content')
<div class="card">
  <div class="card-header">
    <h2 class="card-title">BLOG YAZILARI VE BASIN HABERLERİ ({{ $articles->total() }})</h2>
    <a href="{{ route('admin.media.create') }}" class="btn btn-sm btn-primary">+ Yeni İçerik Ekle</a>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Görsel</th>
          <th>Tip</th>
          <th>Kategori</th>
          <th>Başlık</th>
          <th>Tarih</th>
          <th>Sıra</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $item)
        <tr>
          <td style="width: 50px;">
            <img src="{{ $item->image }}" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-color);">
          </td>
          <td>
            @if($item->type === 'blog')
              <span class="badge badge-gold">BLOG</span>
            @else
              <span class="badge badge-success">BASINDA BİZ</span>
            @endif
          </td>
          <td><strong>{{ $item->category }}</strong></td>
          <td>{{ Str::limit($item->title, 40) }}</td>
          <td style="color: var(--text-muted); font-size: 0.8rem; white-space: nowrap;">
            {{ $item->publish_date ? $item->publish_date->format('d.m.Y') : '—' }}
          </td>
          <td>{{ $item->order }}</td>
          <td>
            @if($item->is_active)
              <span class="badge badge-success">Yayında</span>
            @else
              <span class="badge badge-danger">Taslak</span>
            @endif
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.media.edit', $item) }}" class="btn btn-sm btn-secondary">Düzenle</a>
              <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Bu içerik silinsin mi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">Kayıtlı medya içeriği bulunamadı.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 1.25rem;">
    {{ $articles->links() }}
  </div>
</div>
@endsection
