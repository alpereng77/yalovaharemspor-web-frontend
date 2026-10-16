@extends('admin.layout')

@section('title', 'Yeni Karşılaşma Ekle')
@section('topbar-title', 'YENİ KARŞILAŞMA EKLE')

@section('content')
<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <h2 class="card-title">MAÇ DETAYLARI</h2>
    <a href="{{ route('admin.matches.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.matches.store') }}" method="POST">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="home_team">Ev Sahibi Takım</label>
        <input class="form-control" type="text" name="home_team" id="home_team" value="{{ old('home_team', 'Yalova Harem Spor') }}" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="away_team">Misafir Takım</label>
        <input class="form-control" type="text" name="away_team" id="away_team" value="{{ old('away_team') }}" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="round">Hafta No</label>
        <input class="form-control" type="number" name="round" id="round" value="{{ old('round') }}" placeholder="Örn: 5">
      </div>
      <div class="form-group">
        <label class="form-label" for="match_date">Maç Tarihi ve Saati</label>
        <input class="form-control" type="datetime-local" name="match_date" id="match_date" value="{{ old('match_date') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="venue">Salon</label>
        <input class="form-control" type="text" name="venue" id="venue" value="{{ old('venue', 'Yalova 90. Yıl Spor Salonu') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="status">Maç Durumu</label>
        <select class="form-control" name="status" id="status">
          <option value="upcoming">Yaklaşan (Gelecek Maç)</option>
          <option value="played">Oynandı (Sonuçlandı)</option>
          <option value="postponed">Ertelendi</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="home_score">Ev Sahibi Skor (Varsa)</label>
        <input class="form-control" type="number" name="home_score" id="home_score" value="{{ old('home_score') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="away_score">Misafir Skor (Varsa)</label>
        <input class="form-control" type="number" name="away_score" id="away_score" value="{{ old('away_score') }}">
      </div>
    </div>

    <div style="display: flex; gap: 2rem; margin: 1rem 0 1.5rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_home" value="1" checked> Yalova Harem Spor Ev Sahibi
      </label>
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_next_match" value="1"> Anasayfada "Sıradaki Karşılaşma" Olarak Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Maçı Kaydet</button>
  </form>
</div>
@endsection
