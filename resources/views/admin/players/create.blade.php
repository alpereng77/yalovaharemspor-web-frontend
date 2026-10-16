@extends('admin.layout')

@section('title', 'Yeni Oyuncu Ekle')
@section('topbar-title', 'YENİ OYUNCU EKLE')

@section('content')
<div class="card" style="max-width: 900px;">
  <div class="card-header">
    <h2 class="card-title">OYUNCU VE İSTATİSTİK BİLGİLERİ</h2>
    <a href="{{ route('admin.players.index') }}" class="btn btn-sm btn-secondary">← Listeye Dön</a>
  </div>

  <form action="{{ route('admin.players.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.15rem; color: var(--accent-gold); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      1. KİŞİSEL BİLGİLER
    </h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="first_name">Adı</label>
        <input class="form-control" type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="last_name">Soyadı</label>
        <input class="form-control" type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="jersey_number">Forma Numarası</label>
        <input class="form-control" type="text" name="jersey_number" id="jersey_number" value="{{ old('jersey_number') }}" placeholder="Örn: 10">
      </div>
      <div class="form-group">
        <label class="form-label" for="position">Mevki</label>
        <input class="form-control" type="text" name="position" id="position" value="{{ old('position') }}" placeholder="Örn: Gard (1), Pivot (5), Forvet (3)">
      </div>
      <div class="form-group">
        <label class="form-label" for="height">Boy</label>
        <input class="form-control" type="text" name="height" id="height" value="{{ old('height') }}" placeholder="Örn: 1.98 m">
      </div>
      <div class="form-group">
        <label class="form-label" for="weight">Kilo</label>
        <input class="form-control" type="text" name="weight" id="weight" value="{{ old('weight') }}" placeholder="Örn: 92 kg">
      </div>
      <div class="form-group">
        <label class="form-label" for="birth_date">Doğum Yılı / Tarihi</label>
        <input class="form-control" type="text" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" placeholder="Örn: 1998">
      </div>
      <div class="form-group">
        <label class="form-label" for="nationality">Uyruk</label>
        <input class="form-control" type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Türkiye') }}">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="image">Oyuncu Fotoğrafı</label>
        <input class="form-control" type="file" name="image" id="image" accept="image/*">
      </div>
      <div class="form-group">
        <label class="form-label" for="order">Sıralama</label>
        <input class="form-control" type="number" name="order" id="order" value="{{ old('order', 1) }}">
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" checked> Aktif Kadroda Göster
      </label>
    </div>

    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.15rem; color: var(--accent-gold); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      2. 2026–2027 SEZONU İSTATİSTİKLERİ (TBF)
    </h3>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="points_avg">Sayı Ort. (PPG)</label>
        <input class="form-control" type="number" step="0.1" name="points_avg" id="points_avg" value="{{ old('points_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="rebounds_avg">Ribaund Ort. (RPG)</label>
        <input class="form-control" type="number" step="0.1" name="rebounds_avg" id="rebounds_avg" value="{{ old('rebounds_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="assists_avg">Asist Ort. (APG)</label>
        <input class="form-control" type="number" step="0.1" name="assists_avg" id="assists_avg" value="{{ old('assists_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="games_played">Maç Sayısı (GP)</label>
        <input class="form-control" type="number" name="games_played" id="games_played" value="{{ old('games_played', '0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="minutes_avg">Süre Ort. (DK)</label>
        <input class="form-control" type="number" step="0.1" name="minutes_avg" id="minutes_avg" value="{{ old('minutes_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="steals_avg">Top Çalma (SPG)</label>
        <input class="form-control" type="number" step="0.1" name="steals_avg" id="steals_avg" value="{{ old('steals_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="blocks_avg">Blok Ort. (BPG)</label>
        <input class="form-control" type="number" step="0.1" name="blocks_avg" id="blocks_avg" value="{{ old('blocks_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="efficiency_avg">Verimlilik (VP)</label>
        <input class="form-control" type="number" step="0.1" name="efficiency_avg" id="efficiency_avg" value="{{ old('efficiency_avg', '0.0') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="fg_percentage">2 Sayı % (FG)</label>
        <input class="form-control" type="text" name="fg_percentage" id="fg_percentage" value="{{ old('fg_percentage', '50%') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="three_point_percentage">3 Sayı % (3PT)</label>
        <input class="form-control" type="text" name="three_point_percentage" id="three_point_percentage" value="{{ old('three_point_percentage', '35%') }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="ft_percentage">Serbest Atış % (FT)</label>
        <input class="form-control" type="text" name="ft_percentage" id="ft_percentage" value="{{ old('ft_percentage', '80%') }}">
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1rem;">Oyuncuyu Kaydet</button>
  </form>
</div>
@endsection
