# Yalova Haremspor Mobil Uygulaması

React Native CLI (Expo kullanılmaz) ile yazılmış Android/iOS uygulaması. Tüm veriler Laravel sitesinin
`/api/v1` uç noktalarından gelir; uygulamada sabit içerik yoktur.

## Ekranlar

| Sekme | İçerik | API |
|---|---|---|
| Ana Sayfa | Sıradaki maç, çekilişler, sezon özeti, son sonuçlar, haberler | `GET /home` |
| Maçlar | Fikstür / Sonuçlar, puan durumu, G-M dengesi | `GET /matches?status=upcoming\|played`, `GET /standing` |
| Kadro | Oyuncular (detayda tüm istatistikler), teknik kadro | `GET /players`, `GET /players/{id}`, `GET /staff` |
| Haberler | Kulüpten / Basında, haber detayı | `GET /news?type=blog\|press`, `GET /news/{id}` |
| Çekiliş | Çekilişler, katılım adımları, **uygulamadan katılım**, kazananlar | `GET /giveaways`, `GET /giveaways/{slug}`, `POST /giveaways/{slug}/join` |
| Kulüp (ana sayfadan) | Hakkında, sosyal medya, bilet noktaları, sponsorlar, e-bülten | `GET /club`, `GET /ticket-points`, `GET /sponsors`, `POST /newsletter` |

Her ekran aşağı çekilince yenilenir; internet yoksa "Bağlantı yok / Tekrar dene" gösterilir; uygulama ön plana
gelince veriler tazelenir.

## Çekilişte "Uygulamayı indir" şartı

Admin panelinde bir çekilişte **"Mobil uygulamamızı indir ve çekilişe uygulamadan katıl"** şartı açıksa:

- Katılımcı Instagram kullanıcı adını **sadece uygulamadaki çekiliş ekranından** gönderebilir. Sitedeki form bu
  çekilişler için kayıt almaz, kullanıcıyı uygulamaya yönlendirir.
- Uygulama her istekte kalıcı bir cihaz kimliği gönderir (`X-Device-Id`: Android ID / iOS identifierForVendor).
  Sunucu bu kimliği ham hâliyle değil, özetini saklar.
- **Bir cihazdan bir çekilişe tek hesapla** katılınır; bir hesap başka cihazdan doğrulanmışsa devralınamaz.
- Uygulamadan gelmeyen kayıtlar "Uygulamadan katılım yok" nedeniyle otomatik elenir. Admin katılımcı tablosunda
  "Uygulama 📱 ✓" sütununu görür.
- Uygulamadan katılım, "Sitedeki forma kullanıcı adını yaz" şartını da karşılar.

**Sınırlar (dürüstçe):** Cihaz kimliği fabrika ayarlarına dönüş ya da emülatör gibi yollarla değiştirilebilir;
uygulamaya gömülü anahtar (aşağıda) kararlı biri tarafından çıkarılabilir. Bu kurallar sıradan hileyi engeller.
Daha güçlüsü için Google Play Integrity / Apple App Attest ile cihaz doğrulaması eklenebilir.

## Geliştirme ortamı

Gereksinimler: Node 22+, Android için Android Studio (SDK + JDK 17), iOS için macOS + Xcode + CocoaPods.
Ayrıntı: https://reactnative.dev/docs/set-up-your-environment

```bash
cd mobile
npm install
npm start            # Metro paketleyici
npm run android      # ayrı terminalde, emülatör ya da USB ile bağlı telefon
npm run ios          # sadece macOS (önce: cd ios && bundle install && bundle exec pod install)
```

Laravel sunucusu çalışıyor olmalı. Adres `src/config.ts` dosyasında:

- Android emülatörü bilgisayara `http://10.0.2.2:8000` ile erişir (varsayılan).
- iOS simülatörü `http://localhost:8000` kullanır (varsayılan).
- **Gerçek telefonda** bilgisayarın yerel IP'sini yazın (ör. `http://192.168.1.20:8000`) ve Laravel'i
  `php artisan serve --host=0.0.0.0 --port=8000` ile başlatın.

## Yayına alma

1. `src/config.ts` içinde `PRODUCTION_API_URL` değerini sitenin gerçek **https** adresi yapın.
2. Sunucunun `.env` dosyasına uzun, rastgele bir `MOBILE_APP_KEY` yazın ve aynı değeri `src/config.ts` içindeki
   `APP_KEY`'e girin. (Boş bırakılırsa anahtar kontrolü yapılmaz.)
3. Uygulama simgesi: `android/app/src/main/res/mipmap-*` ve `ios/YalovaHaremspor/Images.xcassets/AppIcon.appiconset`
   klasörlerine kulüp logosundan üretilmiş simgeleri koyun (Android Studio > Image Asset ile kolayca üretilir).
4. Android: imzalama anahtarı oluşturup `npx react-native build-android --mode=release` ile AAB üretin ve Google Play
   Console'a yükleyin. iOS: Xcode'da Archive → App Store Connect.
5. Mağaza bağlantılarını admin panelinde **Çekilişler → Mobil uygulama bağlantıları** bölümüne girin; site ve
   uygulama bu bağlantıları kullanır.

## Kontroller

```bash
npx tsc --noEmit     # tip kontrolü
npm run lint         # ESLint
npm test             # Jest: biçimlendirme + gerçek API yanıtlarıyla ekran testleri (__tests__/fixtures)
```

Test verilerini güncellemek için yerel API'den yeniden alın (ör. `curl -H "Accept: application/json"
http://localhost:8000/api/v1/home > __tests__/fixtures/home.json`).

## Klasörler

```
src/
  config.ts          API adresi ve uygulama anahtarı
  theme.ts           renkler, boşluklar, yazı stilleri
  api/               istemci (zaman aşımı, hata mesajları, cihaz kimliği), tipler, React Query kancaları
  lib/               cihaz kimliği, Türkçe tarih/sayı biçimleri
  components/        ikonlar, ortak arayüz parçaları, kartlar
  navigation/        alt sekmeler + detay ekranları
  screens/           ekranlar
```
