@extends('admin.layout')

@section('title', 'Çekilişler')
@section('topbar-title', 'ÇEKİLİŞLER')

@section('content')
<div class="card">
  <div class="card-header">
    <div>
      <h2 class="card-title">ÇEKİLİŞLER ({{ $giveaways->count() }})</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">
        Instagram çekilişlerini yönetin, katılımcıları şartlara göre süzün ve kazananları
        <a href="{{ route('giveaways.index') }}" target="_blank" style="color: var(--accent-gold);">çekiliş sayfasında ↗</a> duyurun.
      </p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="{{ route('admin.instagram.import') }}" class="btn btn-sm btn-primary">📷 Instagram'dan Aktar</a>
      <a href="{{ route('admin.giveaways.create') }}" class="btn btn-sm btn-secondary">+ Yeni Çekiliş</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Çekiliş</th>
          <th>Ödül</th>
          <th>Bitiş</th>
          <th>Katılımcı</th>
          <th>Durum</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @forelse($giveaways as $g)
        <tr>
          <td><a href="{{ route('admin.giveaways.show', $g) }}" style="color: var(--text-strong); font-weight: 700; text-decoration: none;">{{ $g->title }}</a></td>
          <td>{{ $g->prize }}</td>
          <td style="white-space: nowrap;">{{ $g->ends_at ? $g->ends_at->format('d.m.Y H:i') : '—' }}</td>
          <td>
            <strong style="color: #68d391;">{{ $g->eligible_count }}</strong>
            <span style="color: var(--text-muted);"> / {{ $g->entries_count }} geçerli</span>
          </td>
          <td>
            <span class="badge {{ ['draft' => 'badge-danger', 'active' => 'badge-success', 'drawn' => 'badge-gold', 'completed' => 'badge-success'][$g->status] ?? '' }}">{{ $g->status_label }}</span>
          </td>
          <td>
            <div style="display: flex; gap: 0.4rem;">
              <a href="{{ route('admin.giveaways.show', $g) }}" class="btn btn-sm btn-primary">Yönet</a>
              <a href="{{ route('admin.giveaways.edit', $g) }}" class="btn btn-sm btn-secondary">Düzenle</a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
            <div style="font-size: 1.1rem; margin-bottom: 0.5rem;">Henüz çekiliş yok.</div>
            <a href="{{ route('admin.giveaways.create') }}" class="btn btn-sm btn-primary">+ İlk Çekilişi Oluştur</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="max-width: 900px;">
  <div class="card-header">
    <div>
      <h2 class="card-title">INSTAGRAM → SİTE YER İMİ</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">
        Butonu yer imleri çubuğuna sürükleyin. instagram.com açıkken tıklayınca gönderileriniz listelenir; seçtiğiniz gönderinin yorumları çekilişe aktarılır.
        <a href="{{ route('admin.instagram.import') }}" style="color: var(--accent-gold);">Ayrıntılı kullanım</a>
      </p>
    </div>
  </div>
  @include('admin.giveaways._ig-bookmarklet')
</div>

<div class="card" style="max-width: 900px;">
  <div class="card-header">
    <div>
      <h2 class="card-title">MOBİL UYGULAMA BAĞLANTILARI</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">
        "Mobil uygulamamızı indir" şartında çekiliş sayfası bu bağlantıları gösterir.
        Instagram hesabı <a href="{{ route('admin.settings.index') }}" style="color: var(--accent-gold);">Site Ayarları</a>'ndaki sosyal medya bağlantılarından alınır.
      </p>
    </div>
  </div>
  <form action="{{ route('admin.giveaways.integrations') }}" method="POST">
    @csrf
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="app_store_url">App Store Bağlantısı (iOS)</label>
        <input class="form-control" type="url" name="app_store_url" id="app_store_url" value="{{ old('app_store_url', $settings['app_store_url'] ?? '') }}" placeholder="https://apps.apple.com/tr/app/...">
      </div>
      <div class="form-group">
        <label class="form-label" for="play_store_url">Google Play Bağlantısı (Android)</label>
        <input class="form-control" type="url" name="play_store_url" id="play_store_url" value="{{ old('play_store_url', $settings['play_store_url'] ?? '') }}" placeholder="https://play.google.com/store/apps/details?id=...">
      </div>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <button type="submit" class="btn btn-sm btn-primary">Kaydet</button>
    </div>
  </form>

</div>
@endsection
