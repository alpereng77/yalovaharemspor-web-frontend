@extends('admin.layout')

@section('title', 'Karşılaşmayı Düzenle')
@section('topbar-title', 'KARŞILAŞMAYI DÜZENLE')

@section('content')
<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <h2 class="card-title">{{ $match->home_team }} vs {{ $match->away_team }}</h2>
    <a href="{{ route('admin.matches.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.matches.update', $match) }}" method="POST">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="home_team">Ev Sahibi Takım</label>
        <input class="form-control" type="text" name="home_team" id="home_team" value="{{ old('home_team', $match->home_team) }}" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="away_team">Misafir Takım</label>
        <input class="form-control" type="text" name="away_team" id="away_team" value="{{ old('away_team', $match->away_team) }}" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="round">Hafta No</label>
        <input class="form-control" type="number" name="round" id="round" value="{{ old('round', $match->round) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="match_date">Maç Tarihi ve Saati</label>
        <input class="form-control" type="datetime-local" name="match_date" id="match_date" value="{{ old('match_date', $match->match_date ? $match->match_date->format('Y-m-d\TH:i') : '') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="venue">Salon</label>
        <input class="form-control" type="text" name="venue" id="venue" value="{{ old('venue', $match->venue) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="status">Maç Durumu</label>
        <select class="form-control" name="status" id="status">
          <option value="upcoming" {{ $match->status === 'upcoming' ? 'selected' : '' }}>Yaklaşan (Gelecek Maç)</option>
          <option value="played" {{ $match->status === 'played' ? 'selected' : '' }}>Oynandı (Sonuçlandı)</option>
          <option value="postponed" {{ $match->status === 'postponed' ? 'selected' : '' }}>Ertelendi</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="home_score">Ev Sahibi Skor</label>
        <input class="form-control" type="number" name="home_score" id="home_score" value="{{ old('home_score', $match->home_score) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="away_score">Misafir Skor</label>
        <input class="form-control" type="number" name="away_score" id="away_score" value="{{ old('away_score', $match->away_score) }}">
      </div>
    </div>

    <div style="display: flex; gap: 2rem; margin: 1rem 0 1.5rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_home" value="1" {{ $match->is_home ? 'checked' : '' }}> Yalova Harem Spor Ev Sahibi
      </label>
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_next_match" value="1" {{ $match->is_next_match ? 'checked' : '' }}> Anasayfada "Sıradaki Karşılaşma" Olarak Göster
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">Değişiklikleri Kaydet</button>
  </form>
</div>
@endsection
