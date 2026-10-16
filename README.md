# Yalova Haremspor - Laravel Backend & Yönetim Paneli

Yalova Haremspor resmi web sitesi, tamamen **Saf (Pure) Laravel 10** mimarisine geçirilmiştir. React veya harici ağır paketler kullanılmadan; standart Laravel Blade, Eloquent ORM ve MySQL veritabanı ile çalışmaktadır.

---

## 🚀 Öne Çıkan Özellikler

1. **Tamamen Dinamik Anasayfa (Blade)**:
   - Hero alanı, sloganlar, duyurular, arka plan görselleri ve sosyal medya bağlantıları.
   - **Sıradaki Karşılaşma** afişi ve fikstür/sonuçlar tablosu.
   - Puan durumu ve sezon istatistikleri özeti.
   - Sponsor kayan şeridi (sonsuz carousel döngüsü).
   - **Oyuncularımız Bölümü**: Veritabanından ve TBF botundan çekilen yüksek çözünürlüklü fotoğraflar, forma numaraları, mevkiler ve **tıklandığında açılan interaktif istatistik penceresi (Modal)**.
   - Kulüp kimliği ve renk hikayeleri (#003F20 ve #A70101).
   - Basında Biz ve Kulüp Blogları (modal açılır pencereli tam metinler).

2. **Yönetim Paneli (Admin Dashboard)**:
   - Giriş URL'si: `http://localhost:8000/login`
   - Varsayılan Giriş Bilgileri:
     - **E-Posta**: `admin@yalovaharemspor.com`
     - **Şifre**: `admin123`
   - **Gösterge Paneli**: Kadro sayıları, sıradaki maç durumu ve son bot logları.
   - **TBF Veri Botu**: TBF sayfalarından tek tıkla otomatik veri çekme ve Cloudflare koruması için hızlı manuel HTML ayrıştırıcı.
   - **Oyuncular & İstatistikler**: Oyuncu ekleme/düzenleme/silme ve TBF resmi istatistiklerini (Sayı, Ribaund, Asist, Top Çalma, Blok, Şut Yüzdeleri vb.) yönetme.
   - **Fikstür & Sonuçlar**: Karşılaşma ekleme, skor girişi, sıradaki maç belirleme ve lig puan tablosunu doğrudan güncelleme.
   - **Teknik Kadro**: Başantrenör ve teknik ekip yönetimi.
   - **Sponsorlar**: Kategori, logo ve web sitesi linki yönetimi.
   - **Medya & Blog**: Basın haberleri ve blog hikayeleri yönetimi.
   - **Site Ayarları**: Anasayfadaki tüm metin ve görsellerin canlı yönetimi.

3. **TBF Veri Botu Modülü**:
   Aşağıdaki resmi TBF bağlantılarından verileri çeker ve eşitler:
   - `https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/genel-bakis`
   - `https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/istatistikler`
   - `https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/maclar`
   - `https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/basketbolcular`
   - `https://www.tbf.org.tr/ligler/tbl-2026-2027/takim-detay/266056/teknik-kadro`

   *Not:* TBF sitesi Cloudflare challenge sunarsa, paneldeki **"Manuel HTML Ayrıştır"** sekmesine sayfa kaynağı yapıştırılarak sıfır engelle veriler saniyeler içinde içeri aktarılabilir.

---

## 🛠️ Kurulum ve Çalıştırma

### 1. Veritabanı (MySQL)
Proje yerel MySQL servisine bağlanır:
- **Veritabanı Adı**: `yalova_haremspor`
- **Kullanıcı**: `root`
- **Şifre**: *(boş)*

Tabloları ve başlangıç verilerini sıfırdan oluşturmak isterseniz:
```bash
php artisan migrate:fresh --seed
```

### 2. Sunucuyu Başlatma
```bash
php artisan serve
```
Tarayıcınızda açın:
- **Ana Sayfa**: `http://localhost:8000`
- **Yönetim Paneli**: `http://localhost:8000/login`
