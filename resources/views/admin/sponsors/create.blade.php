@extends('admin.layout')

@section('title', 'Yeni Sponsor Ekle')
@section('topbar-title', 'YENİ SPONSOR EKLE')

@section('content')
<div class="card" style="max-width: 700px;">
  <div class="card-header">
    <h2 class="card-title">SPONSOR DETAYLARI</h2>
    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.sponsors.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label class="form-label" for="name">Sponsor Adı</label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Örn: AKTİF INTERNATIONAL">
    </div>

    <div class="form-group">
      <label class="form-label" for="category">Kategori / Rozet</label>
      <input class="form-control" type="text" name="category" id="category" required value="{{ old('category', 'RESMİ SPONSOR') }}" placeholder="Örn: SAĞLIK SPONSORU, RESMİ SPONSOR, ANA SPONSOR">
    </div>

    <div class="form-group">
      <label class="form-label" for="logo">Sponsor Logosu</label>
      <input class="form-control" type="file" name="logo" id="logo" accept="image/*" required>
    </div>

    <div class="form-group">
      <label class="form-label" for="website_url">Web Sitesi URL (Opsiyonel)</label>
      <input class="form-control" type="url" name="website_url" id="website_url" value="{{ old('website_url') }}" placeholder="https://...">
    </div>

    <div class="form-group">
      <label class="form-label" for="order">Sıralama</label>
      <input class="form-control" type="number" name="order" id="order" value="{{ old('order', 1) }}">
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" checked> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Sponsoru Kaydet</button>
  </form>
</div>
@endsection
