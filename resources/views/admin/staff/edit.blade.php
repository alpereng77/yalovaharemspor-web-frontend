@extends('admin.layout')

@section('title', 'Personeli Düzenle: ' . $staff->name)
@section('topbar-title', 'PERSONELİ DÜZENLE')

@section('content')
<div class="card" style="max-width: 700px;">
  <div class="card-header">
    <h2 class="card-title">{{ $staff->name }} ({{ $staff->role }})</h2>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label class="form-label" for="name">Adı ve Soyadı</label>
      <input class="form-control" type="text" name="name" id="name" required value="{{ old('name', $staff->name) }}">
    </div>

    <div class="form-group">
      <label class="form-label" for="role">Görevi</label>
      <input class="form-control" type="text" name="role" id="role" required value="{{ old('role', $staff->role) }}">
    </div>

    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
      <div style="width: 60px; height: 60px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color); background: #000; flex-shrink: 0;">
        <img src="{{ $staff->image }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div class="form-group" style="flex: 1; margin-bottom: 0;">
        <label class="form-label" for="image">Yeni Fotoğraf Yükle</label>
        <input class="form-control" type="file" name="image" id="image" accept="image/*">
      </div>
      <div class="form-group" style="width: 120px; margin-bottom: 0;">
        <label class="form-label" for="order">Sıra</label>
        <input class="form-control" type="number" name="order" id="order" value="{{ old('order', $staff->order) }}">
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }}> Aktif Kadroda Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Değişiklikleri Kaydet</button>
  </form>
</div>
@endsection
