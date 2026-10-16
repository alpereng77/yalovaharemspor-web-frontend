@extends('admin.layout')

@section('title', 'İçeriği Düzenle: ' . $article->title)
@section('topbar-title', 'İÇERİĞİ DÜZENLE')

@section('content')
<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <h2 class="card-title">{{ Str::limit($article->title, 40) }}</h2>
    <a href="{{ route('admin.media.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.media.update', $article) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="type">İçerik Tipi</label>
        <select class="form-control" name="type" id="type" required>
          <option value="blog" {{ $article->type === 'blog' ? 'selected' : '' }}>Blog Yazısı (Kulüp Blogu)</option>
          <option value="press" {{ $article->type === 'press' ? 'selected' : '' }}>Basında Biz (Basın Haberi)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="category">Kategori</label>
        <input class="form-control" type="text" name="category" id="category" value="{{ old('category', $article->category) }}" required>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="title">Başlık</label>
      <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="publish_date">Yayın Tarihi</label>
        <input class="form-control" type="date" name="publish_date" id="publish_date" value="{{ old('publish_date', $article->publish_date ? $article->publish_date->format('Y-m-d') : '') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="order">Sıra</label>
        <input class="form-control" type="number" name="order" id="order" value="{{ old('order', $article->order) }}">
      </div>
    </div>

    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
      <div style="width: 80px; height: 60px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
        <img src="{{ $article->image }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div class="form-group" style="flex: 1; margin-bottom: 0;">
        <label class="form-label" for="image">Yeni Görsel Yükle</label>
        <input class="form-control" type="file" name="image" id="image" accept="image/*">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="summary">Kısa Özet (Kartta Gözüken Metin)</label>
      <textarea class="form-control" name="summary" id="summary" rows="3">{{ old('summary', $article->summary) }}</textarea>
    </div>

    <div class="form-group">
      <label class="form-label" for="content">Detaylı İçerik (Blog Popup / Tam Metin)</label>
      <textarea class="form-control" name="content" id="content" rows="6">{{ old('content', $article->content) }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="source_title">Kaynak Başlığı (Varsa)</label>
        <input class="form-control" type="text" name="source_title" id="source_title" value="{{ old('source_title', $article->source_title) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="source_url">Kaynak Bağlantısı (URL)</label>
        <input class="form-control" type="url" name="source_url" id="source_url" value="{{ old('source_url', $article->source_url) }}">
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" {{ $article->is_active ? 'checked' : '' }}> Sitede Yayında Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Değişiklikleri Güncelle</button>
  </form>
</div>
@endsection
