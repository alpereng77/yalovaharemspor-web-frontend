@extends('admin.layout')

@section('title', 'Yeni İçerik Ekle')
@section('topbar-title', 'YENİ İÇERİK EKLE')

@section('content')
<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <h2 class="card-title">MEDYA VE HABER İÇERİĞİ</h2>
    <a href="{{ route('admin.media.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="type">İçerik Tipi</label>
        <select class="form-control" name="type" id="type" required>
          <option value="blog">Blog Yazısı (Kulüp Blogu)</option>
          <option value="press">Basında Biz (Basın Haberi)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="category">Kategori</label>
        <input class="form-control" type="text" name="category" id="category" value="{{ old('category', 'YENİ DÖNEM') }}" placeholder="Örn: SEZON HAZIRLIĞI, HABERCİ, MAÇ HİKÂYESİ" required>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="title">Başlık</label>
      <input class="form-control" type="text" name="title" id="title" value="{{ old('title') }}" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="publish_date">Yayın Tarihi</label>
        <input class="form-control" type="date" name="publish_date" id="publish_date" value="{{ old('publish_date', date('Y-m-d')) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="image">Görsel Yükle</label>
        <input class="form-control" type="file" name="image" id="image" accept="image/*">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="summary">Kısa Özet (Kartta Gözüken Metin)</label>
      <textarea class="form-control" name="summary" id="summary" rows="3">{{ old('summary') }}</textarea>
    </div>

    <div class="form-group">
      <label class="form-label" for="content">Detaylı İçerik (Blog Popup / Tam Metin)</label>
      <textarea class="form-control" name="content" id="content" rows="6">{{ old('content') }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="source_title">Kaynak Başlığı (Varsa)</label>
        <input class="form-control" type="text" name="source_title" id="source_title" value="{{ old('source_title') }}" placeholder="Örn: Kaynak: Haberci">
      </div>
      <div class="form-group">
        <label class="form-label" for="source_url">Kaynak Bağlantısı (URL)</label>
        <input class="form-control" type="url" name="source_url" id="source_url" value="{{ old('source_url') }}" placeholder="https://...">
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" checked> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">İçeriği Kaydet</button>
  </form>
</div>
@endsection
