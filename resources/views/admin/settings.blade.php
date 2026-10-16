@extends('admin.layout')

@section('title', 'Site ve Anasayfa Ayarları')
@section('topbar-title', 'SİTE VE ANASAYFA AYARLARI')

@section('content')
<div class="card" style="max-width: 960px;">
  <div class="card-header">
    <h2 class="card-title">ANASAYFA TÜM BÖLÜMLERİNİ YÖNET</h2>
    <span class="badge badge-gold">Canlı Düzenleyici</span>
  </div>

  <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 1. Genel & Marka -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      1. GENEL BAŞLIK VE KULÜP ADI
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="city_name">Şehir Başlığı</label>
        <input class="form-control" type="text" name="city_name" id="city_name" value="{{ $settings['city_name'] ?? 'YALOVA' }}" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="club_name">Kulüp Adı</label>
        <input class="form-control" type="text" name="club_name" id="club_name" value="{{ $settings['club_name'] ?? 'HAREMSPOR' }}" required>
      </div>
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="site_title">Tarayıcı Başlığı (SEO Title)</label>
        <input class="form-control" type="text" name="site_title" id="site_title" value="{{ $settings['site_title'] ?? '' }}" required>
      </div>
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="meta_description">Meta Açıklaması (SEO Description)</label>
        <input class="form-control" type="text" name="meta_description" id="meta_description" value="{{ $settings['meta_description'] ?? '' }}">
      </div>
    </div>

    <!-- 2. Hero Arena -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      2. HERO AÇILIŞ ALANI (ARENA)
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="hero_title_1">Hero Slogan 1. Satır</label>
        <input class="form-control" type="text" name="hero_title_1" id="hero_title_1" value="{{ $settings['hero_title_1'] ?? 'AYNI TUTKU.' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="hero_title_2">Hero Slogan 2. Satır (Vurgulu)</label>
        <input class="form-control" type="text" name="hero_title_2" id="hero_title_2" value="{{ $settings['hero_title_2'] ?? 'YENİ HEYECAN.' }}">
      </div>
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="hero_description">Hero Açıklama Metni</label>
        <textarea class="form-control" name="hero_description" id="hero_description" rows="2">{{ $settings['hero_description'] ?? '' }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="hero_cta_text">Buton Metni</label>
        <input class="form-control" type="text" name="hero_cta_text" id="hero_cta_text" value="{{ $settings['hero_cta_text'] ?? 'MAÇ MERKEZİ' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="hero_cta_url">Buton Hedef Linki</label>
        <input class="form-control" type="text" name="hero_cta_url" id="hero_cta_url" value="{{ $settings['hero_cta_url'] ?? '#maclar' }}">
      </div>
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="hero_image_file">Hero Arka Plan Fotoğrafını Değiştir (Opsiyonel)</label>
        <input class="form-control" type="file" name="hero_image_file" id="hero_image_file" accept="image/*">
      </div>
    </div>

    <!-- 3. Sosyal Medya -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      3. SOSYAL MEDYA HESAPLARI
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="social_instagram">Instagram URL</label>
        <input class="form-control" type="url" name="social_instagram" id="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="social_x">X / Twitter URL</label>
        <input class="form-control" type="url" name="social_x" id="social_x" value="{{ $settings['social_x'] ?? '' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="social_youtube">YouTube URL</label>
        <input class="form-control" type="url" name="social_youtube" id="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}">
      </div>
    </div>

    <!-- 4. Kulüp Hikayemiz -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      4. KULÜP / BİZİM HİKÂYEMİZ BÖLÜMÜ
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="about_title">Başlık (Satır sonu için Enter kullanın)</label>
        <textarea class="form-control" name="about_title" id="about_title" rows="2">{{ $settings['about_title'] ?? '' }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="about_cta_text">Buton Metni</label>
        <input class="form-control" type="text" name="about_cta_text" id="about_cta_text" value="{{ $settings['about_cta_text'] ?? 'MAÇ TAKVİMİ' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="about_cta_url">Buton Linki</label>
        <input class="form-control" type="text" name="about_cta_url" id="about_cta_url" value="{{ $settings['about_cta_url'] ?? '#maclar' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="about_image_1_file">1. Fotoğrafı Güncelle</label>
        <input class="form-control" type="file" name="about_image_1_file" id="about_image_1_file" accept="image/*">
      </div>
      <div class="form-group">
        <label class="form-label" for="about_image_2_file">2. Fotoğrafı Güncelle</label>
        <input class="form-control" type="file" name="about_image_2_file" id="about_image_2_file" accept="image/*">
      </div>
    </div>

    <!-- 5. Kimliğimiz ve Renkler -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      5. KİMLİĞİMİZ VE RENKLERİMİZ
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
      <!-- Koyu Yeşil -->
      <div style="background: rgba(0, 63, 32, 0.2); padding: 1rem; border-radius: 6px; border: 1px solid rgba(0, 63, 32, 0.4);">
        <h4 style="font-size: 0.9rem; color: #68d391; margin-bottom: 0.75rem;">01 / KOYU YEŞİL</h4>
        <div class="form-group">
          <label class="form-label" for="identity_green_title">Başlık</label>
          <input class="form-control" type="text" name="identity_green_title" id="identity_green_title" value="{{ $settings['identity_green_title'] ?? '' }}">
        </div>
        <div class="form-group">
          <label class="form-label" for="identity_green_text">Açıklama</label>
          <textarea class="form-control" name="identity_green_text" id="identity_green_text" rows="3">{{ $settings['identity_green_text'] ?? '' }}</textarea>
        </div>
      </div>

      <!-- Agresif Kırmızı -->
      <div style="background: rgba(167, 1, 1, 0.2); padding: 1rem; border-radius: 6px; border: 1px solid rgba(167, 1, 1, 0.4);">
        <h4 style="font-size: 0.9rem; color: #fc8181; margin-bottom: 0.75rem;">02 / AGRESİF KIRMIZI</h4>
        <div class="form-group">
          <label class="form-label" for="identity_red_title">Başlık</label>
          <input class="form-control" type="text" name="identity_red_title" id="identity_red_title" value="{{ $settings['identity_red_title'] ?? '' }}">
        </div>
        <div class="form-group">
          <label class="form-label" for="identity_red_text">Açıklama</label>
          <textarea class="form-control" name="identity_red_text" id="identity_red_text" rows="3">{{ $settings['identity_red_text'] ?? '' }}</textarea>
        </div>
      </div>

      <!-- Maskot Tostos -->
      <div style="background: rgba(229, 169, 60, 0.15); padding: 1rem; border-radius: 6px; border: 1px solid rgba(229, 169, 60, 0.3);">
        <h4 style="font-size: 0.9rem; color: var(--accent-gold); margin-bottom: 0.75rem;">03 / MASKOT</h4>
        <div class="form-group">
          <label class="form-label" for="identity_mascot_title">Başlık</label>
          <input class="form-control" type="text" name="identity_mascot_title" id="identity_mascot_title" value="{{ $settings['identity_mascot_title'] ?? '' }}">
        </div>
        <div class="form-group">
          <label class="form-label" for="identity_mascot_text">Açıklama</label>
          <textarea class="form-control" name="identity_mascot_text" id="identity_mascot_text" rows="3">{{ $settings['identity_mascot_text'] ?? '' }}</textarea>
        </div>
      </div>
    </div>

    <!-- 6. Footer -->
    <h3 style="font-family: 'Barlow Condensed'; font-size: 1.25rem; color: var(--accent-gold); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.35rem;">
      6. FOOTER VE TELİF
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label class="form-label" for="footer_hashtag">Hashtag / Slogan</label>
        <input class="form-control" type="text" name="footer_hashtag" id="footer_hashtag" value="{{ $settings['footer_hashtag'] ?? '#aynıtutkuyeniheyecan' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="footer_copyright">Telif Hakkı Metni</label>
        <input class="form-control" type="text" name="footer_copyright" id="footer_copyright" value="{{ $settings['footer_copyright'] ?? '' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="footer_credit_name">Geliştirici İmzası</label>
        <input class="form-control" type="text" name="footer_credit_name" id="footer_credit_name" value="{{ $settings['footer_credit_name'] ?? 'alperengundogdu' }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="footer_credit_url">Geliştirici URL</label>
        <input class="form-control" type="url" name="footer_credit_url" id="footer_credit_url" value="{{ $settings['footer_credit_url'] ?? 'https://alperengundogdu.com.tr' }}">
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2.5rem; font-size: 1rem;">Tüm Ayarları Kaydet</button>
  </form>
</div>
@endsection
