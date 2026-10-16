@extends('admin.layout')

@section('title', 'Sponsoru Düzenle: ' . $sponsor->name)
@section('topbar-title', 'SPONSORU DÜZENLE')

@section('content')
<div class="card" style="max-width: 700px;">
  <div class="card-header">
    <h2 class="card-title">{{ $sponsor->name }}</h2>
    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.sponsors.update', $sponsor) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label class="form-label" for="name">Sponsor Adı</label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name', $sponsor->name) }}">
    </div>

    <div class="form-group">
      <label class="form-label" for="category">Kategori / Rozet</label>
      <input class="form-control" type="text" name="category" id="category" required value="{{ old('category', $sponsor->category) }}">
    </div>

    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
      <div style="width: 140px; height: 50px; background: rgba(0,0,0,0.4); border-radius: 6px; padding: 4px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
        <img src="{{ $sponsor->logo }}" alt="" style="max-width: 100%; max-height: 100%; object-fit: contain;">
      </div>
      <div class="form-group" style="flex: 1; margin-bottom: 0;">
        <label class="form-label" for="logo">Yeni Logo Yükle</label>
        <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="website_url">Web Sitesi URL (Opsiyonel)</label>
      <input class="form-control" type="url" name="website_url" id="website_url" value="{{ old('website_url', $sponsor->website_url) }}">
    </div>

    <div class="form-group">
      <label class="form-label" for="order">Sıralama</label>
      <input class="form-control" type="number" name="order" id="order" value="{{ old('order', $sponsor->order) }}">
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" {{ $sponsor->is_active ? 'checked' : '' }}> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Değişiklikleri Kaydet</button>
  </form>
</div>
@endsection
