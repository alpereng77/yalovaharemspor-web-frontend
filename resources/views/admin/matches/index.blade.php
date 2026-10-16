@extends('admin.layout')

@section('title', 'Fikstür ve Maç Merkezi')
@section('topbar-title', 'FİKSTÜR VE MAÇ MERKEZİ')

@section('content')
<style>
  .matches-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }
  @media (max-width: 992px) {
    .matches-layout {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="matches-layout">
  <!-- Sol Bölüm: Maç Listesi -->
  <div>
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">KARŞILAŞMALAR ({{ $matches->total() }})</h2>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <form method="POST" action="{{ route('admin.bot.sync_all') }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Eurobasket & TBF teyitli tüm maçlar, skorlar, fikstür ve puan durumu güncellenecektir. Onaylıyor musunuz?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" style="background: #00A859; border: none; font-weight: 700; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              <span>Fikstür ve Maçları Eşitle</span>
            </button>
          </form>
          <a href="{{ route('admin.matches.create') }}" class="btn btn-sm btn-primary">+ Yeni Maç Ekle</a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Hafta</th>
              <th>Tarih</th>
              <th>Ev Sahibi</th>
              <th>Skor</th>
              <th>Misafir</th>
              <th>Durum</th>
              <th>Sıradaki</th>
              <th>İşlemler</th>
            </tr>
          </thead>
          <tbody>
            @forelse($matches as $m)
            <tr>
              <td><strong>{{ $m->round ? $m->round . '. Hf' : '—' }}</strong></td>
              <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                {{ $m->match_date ? $m->match_date->format('d.m.Y H:i') : 'Belirsiz' }}
              </td>
              <td><span style="{{ $m->is_home ? 'color:var(--accent-gold);font-weight:700;' : '' }}">{{ $m->home_team }}</span></td>
              <td>
                @if($m->home_score !== null && $m->away_score !== null)
                  <strong style="font-family:'Barlow Condensed';font-size:1.2rem;">{{ $m->home_score }} - {{ $m->away_score }}</strong>
                @else
                  <span style="color:var(--text-muted);">vs</span>
                @endif
              </td>
              <td><span style="{{ !$m->is_home ? 'color:var(--accent-gold);font-weight:700;' : '' }}">{{ $m->away_team }}</span></td>
              <td>
                @if($m->status === 'played')
                  <span class="badge badge-success">Oynandı</span>
                @elseif($m->status === 'upcoming')
                  <span class="badge badge-gold">Yaklaşan</span>
                @else
                  <span class="badge badge-danger">Ertelendi</span>
                @endif
              </td>
              <td>
                @if($m->is_next_match)
                  <span class="badge badge-gold">⭐ SIRADAKİ</span>
                @endif
              </td>
              <td>
                <div style="display: flex; gap: 0.4rem;">
                  <a href="{{ route('admin.matches.edit', $m) }}" class="btn btn-sm btn-secondary">Düzenle</a>
                  <form action="{{ route('admin.matches.destroy', $m) }}" method="POST" onsubmit="return confirm('Bu maç silinsin mi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">Kayıtlı maç bulunamadı.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="margin-top: 1.25rem;">
        {{ $matches->links() }}
      </div>
    </div>
  </div>

  <!-- Sağ Bölüm: Sezon / Puan Durumu Özeti Düzenleme -->
  <div>
    <div class="card" style="border-top: 4px solid var(--accent-gold);">
      <div class="card-header">
        <h2 class="card-title">LİG PUAN DURUMU & SEZON İSTATİSTİĞİ</h2>
      </div>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
        Anasayfadaki "Maç Merkezi" başlığı altındaki lig sırası, galibiyet ve puan sayısını buradan güncelleyebilirsiniz.
      </p>

      <form action="{{ route('admin.matches.standing') }}" method="POST">
        @csrf
        <div class="form-group">
          <label class="form-label" for="rank">Lig Sırası</label>
          <input class="form-control" type="number" name="rank" id="rank" value="{{ old('rank', $standing->rank ?? 2) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div class="form-group">
            <label class="form-label" for="played">Oynanan (O)</label>
            <input class="form-control" type="number" name="played" id="played" value="{{ old('played', $standing->played ?? 4) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="points">Puan (P)</label>
            <input class="form-control" type="number" name="points" id="points" value="{{ old('points', $standing->points ?? 7) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="won">Galibiyet (G)</label>
            <input class="form-control" type="number" name="won" id="won" value="{{ old('won', $standing->won ?? 3) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="lost">Mağlubiyet (M)</label>
            <input class="form-control" type="number" name="lost" id="lost" value="{{ old('lost', $standing->lost ?? 1) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="points_for">Atılan (A)</label>
            <input class="form-control" type="number" name="points_for" id="points_for" value="{{ old('points_for', $standing->points_for ?? 353) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="points_against">Yenilen (Y)</label>
            <input class="form-control" type="number" name="points_against" id="points_against" value="{{ old('points_against', $standing->points_against ?? 294) }}" required>
          </div>
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
          Puan Durumunu Kaydet
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
