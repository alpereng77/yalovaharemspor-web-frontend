@extends('admin.layout')

@php $editing = $giveaway->exists; @endphp

@section('title', $editing ? 'Çekilişi Düzenle' : 'Yeni Çekiliş')
@section('topbar-title', $editing ? 'ÇEKİLİŞİ DÜZENLE' : 'YENİ ÇEKİLİŞ')

@section('content')
<style>
  .req-list { border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
  .req-row { display: grid; grid-template-columns: 1fr auto; gap: 0.25rem 1rem; align-items: center; padding: 0.85rem 1rem; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.15); }
  .req-row:last-child { border-bottom: 0; }
  .req-help { grid-column: 1 / -1; margin: 0; color: var(--text-muted); font-size: 0.8rem; line-height: 1.5; padding-left: 3.1rem; }
  .req-extra { grid-column: 1 / -1; padding-left: 3.1rem; margin-top: 0.4rem; color: #e2e8f0; font-size: 0.85rem; display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
  .req-extra.is-hidden { display: none; }
  .req-switch { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; color: #fff; font-weight: 600; }
  .req-switch input { position: absolute; opacity: 0; width: 1px; height: 1px; }
  .req-slider { width: 2.4rem; height: 1.35rem; border-radius: 999px; background: #2d3b34; position: relative; flex: none; transition: background .15s; }
  .req-slider::after { content: ""; position: absolute; top: 3px; left: 3px; width: calc(1.35rem - 6px); height: calc(1.35rem - 6px); border-radius: 50%; background: #fff; transition: transform .15s; }
  .req-switch input:checked + .req-slider { background: #00A859; }
  .req-switch input:checked + .req-slider::after { transform: translateX(1.05rem); }
  .req-switch input:focus-visible + .req-slider { outline: 2px solid var(--accent-gold); outline-offset: 2px; }
  .req-row.is-off .req-label, .req-row.is-off .req-help { opacity: 0.5; }
</style>
<div class="card" style="max-width: 850px;">
  <div class="card-header">
    <h2 class="card-title">ÇEKİLİŞ BİLGİLERİ</h2>
    <a href="{{ $editing ? route('admin.giveaways.show', $giveaway) : route('admin.giveaways.index') }}" class="btn btn-sm btn-secondary">← Geri</a>
  </div>

  @if($errors->any())
    <div class="alert alert-error"><div>{!! implode('<br>', array_map('e', $errors->all())) !!}</div></div>
  @endif

  <form action="{{ $editing ? route('admin.giveaways.update', $giveaway) : route('admin.giveaways.store') }}" method="POST">
    @csrf
    @if($editing) @method('PUT') @endif


    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="title">Çekiliş Başlığı <span style="color: var(--primary-red);">*</span></label>
        <input class="form-control" type="text" name="title" id="title" required maxlength="190" value="{{ old('title', $giveaway->title) }}" placeholder="Örn: Göztepe Maçı Forma Çekilişi">
      </div>
      <div class="form-group">
        <label class="form-label" for="prize">Ödül <span style="color: var(--primary-red);">*</span></label>
        <input class="form-control" type="text" name="prize" id="prize" required maxlength="190" value="{{ old('prize', $giveaway->prize) }}" placeholder="Örn: İmzalı maç forması">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Açıklama</label>
      <textarea class="form-control" name="description" id="description" rows="3" placeholder="Çekiliş sayfasında görünecek kısa açıklama">{{ old('description', $giveaway->description) }}</textarea>
    </div>

    <div class="form-group">
      <label class="form-label" for="instagram_post_url">Instagram Gönderi Bağlantısı</label>
      <input class="form-control" type="url" name="instagram_post_url" id="instagram_post_url" value="{{ old('instagram_post_url', $giveaway->instagram_post_url) }}" placeholder="https://www.instagram.com/p/ABC123xyz/">
    </div>

    {{-- Açılıp kapatılabilen sabit şartlar --}}
    <div class="form-group">
      <label class="form-label">Katılım Şartları</label>
      @php
        $checkLabels = ['auto' => ['Otomatik kontrol', 'badge-success'], 'manual' => ['Kazananda kontrol', 'badge-gold']];
        $isOn = fn($k) => (bool) old("req.$k", $giveaway->requires($k));
      @endphp
      <div class="req-list">
        @foreach(\App\Models\Giveaway::REQUIREMENTS as $key => $r)
          <div class="req-row">
            <label class="req-switch">
              <input type="checkbox" name="req[{{ $key }}]" value="1" @checked($isOn($key)) data-req="{{ $key }}">
              <span class="req-slider" aria-hidden="true"></span>
              <span class="req-label">{{ $r['label'] }}</span>
            </label>
            <span class="badge {{ $checkLabels[$r['check']][1] }}">{{ $checkLabels[$r['check']][0] }}</span>
            <p class="req-help">{{ $r['help'] }}</p>

            @if($key === 'instagram_comment')
              <div class="req-extra" data-for="instagram_comment">
                <label for="required_mentions">Yorumda etiketlenmesi gereken kişi sayısı</label>
                <input class="form-control" type="number" min="0" max="10" name="required_mentions" id="required_mentions" value="{{ old('required_mentions', $giveaway->required_mentions) }}" style="max-width: 90px;">
              </div>
            @endif
          </div>
        @endforeach
      </div>
      <small style="color: var(--text-muted); display: block; margin-top: 6px;">Açık şartlar çekiliş sayfasında bağlantılarıyla birlikte otomatik listelenir.</small>
    </div>

    <div class="form-group">
      <label class="form-label" for="rules_text">Ek Şartlar / Notlar (opsiyonel, her satıra bir tane)</label>
      <textarea class="form-control" name="rules_text" id="rules_text" rows="2" placeholder="Örn: 18 yaşından büyük olmak gerekir">{{ old('rules_text', implode("\n", $giveaway->rules ?? [])) }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="winner_count">Kazanan Sayısı</label>
        <input class="form-control" type="number" min="1" max="50" name="winner_count" id="winner_count" value="{{ old('winner_count', $giveaway->winner_count) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="reserve_count">Yedek Sayısı</label>
        <input class="form-control" type="number" min="0" max="50" name="reserve_count" id="reserve_count" value="{{ old('reserve_count', $giveaway->reserve_count) }}">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="ends_at">Son Katılım Tarihi</label>
        <input class="form-control" type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', optional($giveaway->ends_at)->format('Y-m-d\TH:i')) }}">
        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Bu tarihten sonra yapılan yorumlar geçersiz sayılır (yorum tarihi biliniyorsa).</small>
      </div>
      <div class="form-group">
        <label class="form-label" for="status">Durum</label>
        @if(in_array($giveaway->status, ['drawn', 'completed']))
          <input class="form-control" type="text" value="{{ $giveaway->status_label }}" disabled>
          <input type="hidden" name="status" value="active">
        @else
          <select class="form-control" name="status" id="status">
            <option value="draft" @selected(old('status', $giveaway->status) === 'draft')>Taslak (sitede görünmez)</option>
            <option value="active" @selected(old('status', $giveaway->status) === 'active')>Yayında (katılım açık)</option>
          </select>
        @endif
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="excluded_usernames">Hariç Tutulan Hesaplar</label>
      <textarea class="form-control" name="excluded_usernames" id="excluded_usernames" rows="2" placeholder="@personel1 @sponsorhesabi">{{ old('excluded_usernames', $giveaway->excluded_usernames) }}</textarea>
      <small style="color: var(--text-muted); display: block; margin-top: 4px;">Bu hesaplar çekilişe katılamaz ve etiket olarak sayılmaz. Kulübün kendi Instagram hesabı otomatik hariç tutulur.</small>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2.2rem;">{{ $editing ? 'Değişiklikleri Kaydet' : 'Çekilişi Oluştur' }}</button>
  </form>

  @if($editing)
    <form action="{{ route('admin.giveaways.destroy', $giveaway) }}" method="POST" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;" onsubmit="return confirm('Çekiliş, tüm katılımcıları ve sonuçlarıyla birlikte kalıcı olarak silinsin mi?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger">Çekilişi Sil</button>
    </form>
  @endif
</div>
@endsection

@section('scripts')
<script>
  document.querySelectorAll('[data-req]').forEach(function (cb) {
    function sync() {
      var row = cb.closest('.req-row');
      row.classList.toggle('is-off', !cb.checked);
      row.querySelectorAll('.req-extra').forEach(function (x) { x.classList.toggle('is-hidden', !cb.checked); });
    }
    cb.addEventListener('change', sync); sync();
  });
</script>
@endsection
