# Yalova Haremspor

Statik kulüp web sitesi. Giriş sayfası `index.html` dosyasıdır.

## Klasör düzeni

- `assets/css/`: sayfa stilleri.
- `assets/js/`: sayfa etkileşimleri ve yerel maç verisi.
- `assets/images/`: kullanılan kulüp ve içerik görselleri.
- `assets/players/`: oyuncu görselleri.
- `assets/sponsors/`: sponsor logoları.
- `data/`: kadro kaynak verisi.
- `scripts/`: veri bakım betikleri.

## Maç verilerini güncelleme

Proje kökünde çalıştırın:

```sh
python3 scripts/update_match_data.py
```

Betik Fanatik API’sinden fikstür ve puan durumunu alır, tutarlılığını kontrol eder ve `assets/js/match-data.js` dosyasını yeniler. Kaynak hatasında mevcut veriyi korur. Sayfa yerel kopyayı kullanır; otomatik veri güncelleme servisi bağlı değildir.

## Yerel önizleme

```sh
python3 -m http.server 8000
```

Tarayıcıda `http://localhost:8000` adresini açın. Yayına alırken `index.html` ve `assets/` klasörünü birlikte aktarın.
