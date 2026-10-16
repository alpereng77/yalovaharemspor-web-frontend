@extends('admin.layout')

@section('title', 'Bilet Satış Noktasını Düzenle: ' . $ticketPoint->name)
@section('topbar-title', 'SATIŞ NOKTASINI DÜZENLE')

@section('content')
<div class="card" style="max-width: 750px;">
  <div class="card-header">
    <div>
      <h2 class="card-title">{{ $ticketPoint->name }}</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">
        Satış noktası bilgilerini, konum linkini ve logoyu güncelleyin.
      </p>
    </div>
    <a href="{{ route('admin.ticket-points.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.ticket-points.update', $ticketPoint) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label class="form-label" for="name">İşletme / Nokta Adı <span style="color: var(--primary-red);">*</span></label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name', $ticketPoint->name) }}">
    </div>

    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem; background: rgba(0,0,0,0.25); padding: 1rem; border-radius: 6px; border: 1px solid var(--border-color);">
      <div style="width: 100px; height: 60px; background: rgba(0,0,0,0.5); border-radius: 6px; padding: 4px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); flex-shrink: 0;">
        <img src="{{ $ticketPoint->logo }}" alt="{{ $ticketPoint->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
      </div>
      <div class="form-group" style="flex: 1; margin-bottom: 0;">
        <label class="form-label" for="logo">Yeni Logo Yükle</label>
        <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Logoyu değiştirmek istemiyorsanız boş bırakın.</small>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="maps_url">Konum / Harita Linki (Google Maps / Yandex)</label>
      <input class="form-control" type="text" name="maps_url" id="maps_url" value="{{ old('maps_url', $ticketPoint->maps_url) }}" placeholder="https://maps.app.goo.gl/...">
      @if($ticketPoint->maps_url)
        <small style="margin-top: 4px; display: block;">
          <a href="{{ $ticketPoint->maps_url }}" target="_blank" style="color: var(--accent-gold); text-decoration: underline;">Mevcut konumu haritada aç ↗</a>
        </small>
      @endif
    </div>

    <div class="form-group">
      <label class="form-label" for="address">Açık Adres / Konum Detayı</label>
      <textarea class="form-control" name="address" id="address" rows="3">{{ old('address', $ticketPoint->address) }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="phone">İletişim / Telefon (Opsiyonel)</label>
        <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $ticketPoint->phone) }}">
      </div>

      <div class="form-group">
        <label class="form-label" for="working_hours">Çalışma Saatleri (Opsiyonel)</label>
        <input class="form-control" type="text" name="working_hours" id="working_hours" value="{{ old('working_hours', $ticketPoint->working_hours) }}">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="order">Sıralama</label>
      <input class="form-control" type="number" name="order" id="order" value="{{ old('order', $ticketPoint->order) }}">
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff;">
        <input type="checkbox" name="is_active" value="1" {{ $ticketPoint->is_active ? 'checked' : '' }}> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2.2rem; font-size: 0.95rem;">
      Değişiklikleri Kaydet
    </button>
  </form>
</div>
@endsection
