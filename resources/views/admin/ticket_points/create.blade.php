@extends('admin.layout')

@section('title', 'Yeni Bilet Satış Noktası Ekle')
@section('topbar-title', 'YENİ BİLET SATIŞ NOKTASI')

@section('content')
<div class="card" style="max-width: 750px;">
  <div class="card-header">
    <div>
      <h2 class="card-title">SATIŞ NOKTASI BİLGİLERİ</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">
        Konum linki (Google Maps) girerek taraftarların tek tıkla yol tarifi almasını sağlayabilirsiniz.
      </p>
    </div>
    <a href="{{ route('admin.ticket-points.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.ticket-points.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label class="form-label" for="name">İşletme / Nokta Adı <span style="color: var(--primary-red);">*</span></label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Örn: 90. Yıl Spor Salonu Gişesi, Sahil Kafe & Bistro">
    </div>

    <div class="form-group">
      <label class="form-label" for="logo">İşletme Logosu</label>
      <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
      <small style="color: var(--text-muted); display: block; margin-top: 4px;">PNG, JPG veya WebP formatında işletme veya gişe görseli (Seçilmezse varsayılan kulüp logosu kullanılır).</small>
    </div>

    <div class="form-group">
      <label class="form-label" for="maps_url">Konum / Harita Linki (Google Maps / Yandex)</label>
      <input class="form-control" type="text" name="maps_url" id="maps_url" value="{{ old('maps_url') }}" placeholder="https://maps.app.goo.gl/... veya https://www.google.com/maps?q=...">
      <small style="color: var(--text-muted); display: block; margin-top: 4px;">Google Haritalar'dan "Paylaş -> Bağlantıyı Kopyala" ile alınan linki buraya yapıştırın.</small>
    </div>

    <div class="form-group">
      <label class="form-label" for="address">Açık Adres / Konum Detayı</label>
      <textarea class="form-control" name="address" id="address" rows="3" placeholder="Örn: Süleyman Bey Mah. Cengiz Koçal Cad. No: 14/A, Merkez / Yalova">{{ old('address') }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="phone">İletişim / Telefon (Opsiyonel)</label>
        <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Örn: 0226 814 00 00">
      </div>

      <div class="form-group">
        <label class="form-label" for="working_hours">Çalışma Saatleri (Opsiyonel)</label>
        <input class="form-control" type="text" name="working_hours" id="working_hours" value="{{ old('working_hours') }}" placeholder="Örn: Hafta içi 09:00 - 19:00">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="order">Sıralama</label>
      <input class="form-control" type="number" name="order" id="order" value="{{ old('order', 1) }}">
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff;">
        <input type="checkbox" name="is_active" value="1" checked> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2.2rem; font-size: 0.95rem;">
      Satış Noktasını Kaydet
    </button>
  </form>
</div>
@endsection
