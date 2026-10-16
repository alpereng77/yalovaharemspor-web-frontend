@extends('admin.layout')

@section('title', 'Bülten Aboneleri')
@section('topbar-title', 'Bülten Aboneleri (Newsletter)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

  <!-- İstatistik Kartları -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid var(--primary-green);">
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Aktif Aboneler</div>
      <div style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.3rem; font-weight: 900; color: #68d391; line-height: 1.1; margin-top: 0.25rem;">
        {{ number_format($activeCount) }}
      </div>
      <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Bülteni aktif alan e-postalar</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid var(--primary-red);">
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Toplam Kayıt</div>
      <div style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.3rem; font-weight: 900; color: #fff; line-height: 1.1; margin-top: 0.25rem;">
        {{ number_format($totalCount) }}
      </div>
      <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Tüm zamanlar kayıt sayısı</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid var(--accent-gold);">
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Bugün Kaydolan</div>
      <div style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.3rem; font-weight: 900; color: var(--accent-gold); line-height: 1.1; margin-top: 0.25rem;">
        {{ number_format($todayCount) }}
      </div>
      <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Bugün katılan yeni aboneler</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #718096;">
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Abonelikten Çıkanlar</div>
      <div style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.3rem; font-weight: 900; color: #a0aec0; line-height: 1.1; margin-top: 0.25rem;">
        {{ number_format($unsubscribedCount) }}
      </div>
      <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Pasif / İptal edilen kayıtlar</div>
    </div>
  </div>

  <!-- Ana Kart: Liste & Filtreler -->
  <div class="card" style="margin-bottom: 0;">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
      <div class="card-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <span>BÜLTEN ABONE LİSTESİ</span>
      </div>

      <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
        <!-- Toplu Kopyala Butonu -->
        <button type="button" class="btn btn-secondary" onclick="copyAllEmails()" title="Tüm aktif e-postaları panoya kopyalar">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          <span id="copyBtnText">E-Postaları Kopyala</span>
        </button>

        <!-- CSV İndir -->
        <a href="{{ route('admin.newsletters.export', ['status' => request('status', 'active')]) }}" class="btn btn-green">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          <span>Excel / CSV İndir</span>
        </a>

        <!-- Yeni Abone Ekle Butonu -->
        <button type="button" class="btn btn-primary" onclick="toggleAddModal()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>Manuel Abone Ekle</span>
        </button>
      </div>
    </div>

    <!-- Filtre & Arama Barı -->
    <form method="GET" action="{{ route('admin.newsletters.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
      <div style="flex: 1; min-width: 240px; position: relative;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="E-posta adresine göre ara..." 
               style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: #fff; padding: 0.6rem 0.85rem; border-radius: 6px; font-size: 0.9rem;">
      </div>

      <div style="min-width: 160px;">
        <select name="status" onchange="this.form.submit()" 
                style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: #fff; padding: 0.6rem 0.85rem; border-radius: 6px; font-size: 0.9rem;">
          <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tüm Durumlar</option>
          <option value="active" {{ request('status') === 'active' || !request()->has('status') ? 'selected' : '' }}>Yalnızca Aktif</option>
          <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Abonelikten Çıkanlar</option>
        </select>
      </div>

      <button type="submit" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span>Filtrele</span>
      </button>

      @if(request()->has('search') || (request()->has('status') && request('status') !== 'all'))
        <a href="{{ route('admin.newsletters.index') }}" class="btn btn-secondary" title="Filtreleri Temizle">Temizle</a>
      @endif
    </form>

    <!-- Tablo -->
    <div style="overflow-x: auto;">
      <table class="table">
        <thead>
          <tr>
            <th style="width: 60px;">#</th>
            <th>E-Posta Adresi</th>
            <th>Durum</th>
            <th>Kayıt Tarihi</th>
            <th>IP Adresi</th>
            <th>Not</th>
            <th style="width: 140px; text-align: right;">İşlemler</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subscribers as $sub)
          <tr>
            <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $sub->id }}</td>
            <td>
              <a href="mailto:{{ $sub->email }}" style="color: #fff; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-muted);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span>{{ $sub->email }}</span>
              </a>
            </td>
            <td>
              @if($sub->status === 'active')
                <span class="badge badge-success">Aktif</span>
              @else
                <span class="badge badge-danger">İptal Edildi</span>
              @endif
            </td>
            <td style="color: var(--text-muted); font-size: 0.85rem;">
              {{ $sub->created_at ? $sub->created_at->format('d.m.Y H:i') : '—' }}
            </td>
            <td style="color: var(--text-muted); font-size: 0.82rem; font-family: monospace;">
              {{ $sub->ip_address ?? '—' }}
            </td>
            <td style="color: var(--text-muted); font-size: 0.85rem;">
              {{ $sub->notes ?? '—' }}
            </td>
            <td style="text-align: right;">
              <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                <!-- Durum Değiştir -->
                <form action="{{ route('admin.newsletters.toggle', $sub->id) }}" method="POST" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;" 
                          title="{{ $sub->status === 'active' ? 'Abonelikten Çıkar' : 'Tekrar Aktif Et' }}">
                    @if($sub->status === 'active')
                      <span>Pasif Yap</span>
                    @else
                      <span style="color: #68d391;">Aktif Et</span>
                    @endif
                  </button>
                </form>

                <!-- Sil -->
                <form action="{{ route('admin.newsletters.destroy', $sub->id) }}" method="POST" style="display: inline;" 
                      onsubmit="return confirm('Bu e-posta adresini silmek istediğinize emin misiniz?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.55rem; font-size: 0.75rem;" title="Sil">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.5rem; opacity: 0.5;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <div>Henüz kayıtlı bülten abonesi bulunmuyor.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Sayfalama -->
    @if($subscribers->hasPages())
    <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
      {{ $subscribers->links() }}
    </div>
    @endif
  </div>

</div>

<!-- Manuel / Toplu E-Posta Ekleme Modalı -->
<div id="addSubscriberModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
  <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; width: 100%; max-width: 520px; box-shadow: 0 20px 40px rgba(0,0,0,0.8); overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-family: 'Barlow Condensed', sans-serif; font-size: 1.3rem; margin: 0; color: #fff; font-weight: 800;">
        MANUEL BÜLTEN ABONESİ EKLE
      </h3>
      <button type="button" onclick="toggleAddModal()" style="color: var(--text-muted); font-size: 1.4rem; line-height: 1; cursor: pointer;">&times;</button>
    </div>

    <form action="{{ route('admin.newsletters.store') }}" method="POST" style="padding: 1.5rem;">
      @csrf
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #fff; margin-bottom: 0.4rem;">
          E-Posta Adresi (veya Birden Fazla Adres) <span style="color: var(--primary-red);">*</span>
        </label>
        <textarea name="email" rows="4" required placeholder="ornek@alanadi.com&#10;ornek2@alanadi.com (Birden fazla adresi virgül veya satırla ayırabilirsiniz)"
                  style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem; border-radius: 6px; font-size: 0.88rem; resize: vertical;"></textarea>
        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Toplu eklemelerde virgül veya alt alta satır kullanabilirsiniz.</small>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #fff; margin-bottom: 0.4rem;">
          İç Not / Kaynak (İsteğe bağlı)
        </label>
        <input type="text" name="notes" placeholder="Örn: Maç günü salonda form dolduran taraftar"
               style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: #fff; padding: 0.65rem 0.75rem; border-radius: 6px; font-size: 0.88rem;">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" class="btn btn-secondary" onclick="toggleAddModal()">Vazgeç</button>
        <button type="submit" class="btn btn-primary">Kaydet ve Ekle</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Toplu e-postaları panoya kopyalama
  const allActiveEmails = @json($activeEmailsString);

  function copyAllEmails() {
    if (!allActiveEmails || allActiveEmails.trim() === '') {
      alert('Kopyalanacak aktif e-posta adresi bulunmuyor.');
      return;
    }
    navigator.clipboard.writeText(allActiveEmails).then(() => {
      const btnText = document.getElementById('copyBtnText');
      btnText.textContent = '✓ Kopyalandı!';
      setTimeout(() => {
        btnText.textContent = 'E-Postaları Kopyala';
      }, 2000);
    }).catch(err => {
      alert('Panoya kopyalama başarısız oldu, lütfen manuel kopyalayın.');
    });
  }

  function toggleAddModal() {
    const modal = document.getElementById('addSubscriberModal');
    if (modal.style.display === 'none' || modal.style.display === '') {
      modal.style.display = 'flex';
    } else {
      modal.style.display = 'none';
    }
  }

  // Escape tuşu ile modalı kapatma
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('addSubscriberModal');
      if (modal && modal.style.display === 'flex') modal.style.display = 'none';
    }
  });
</script>
@endsection
