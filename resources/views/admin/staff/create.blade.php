@extends('admin.layout')

@section('title', 'Yeni Teknik Personel Ekle')
@section('topbar-title', 'YENİ TEKNİK PERSONEL EKLE')

@section('content')
<div class="card" style="max-width: 700px;">
  <div class="card-header">
    <h2 class="card-title">PERSONEL BİLGİLERİ</h2>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label class="form-label" for="name">Adı ve Soyadı</label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Örn: Faruk Beşok">
    </div>

    <div class="form-group">
      <label class="form-label" for="role">Görevi</label>
      <input class="form-control" type="text" name="role" id="role" required value="{{ old('role') }}" placeholder="Örn: Başantrenör, Yrd. Antrenör, Genel Menajer, Kondisyoner">
    </div>

    <div class="form-group">
      <label class="form-label" for="image">Fotoğraf</label>
      <input class="form-control" type="file" name="image" id="image" accept="image/*">
    </div>

    <div class="form-group">
      <label class="form-label" for="order">Sıralama</label>
      <input class="form-control" type="number" name="order" id="order" value="{{ old('order', 1) }}">
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" checked> Aktif Kadroda Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Personeli Kaydet</button>
  </form>
</div>
@endsection
