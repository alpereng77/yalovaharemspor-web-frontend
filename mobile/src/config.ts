import { Platform } from 'react-native';

/**
 * Uygulama ayarları.
 *
 * API_URL: Laravel sitesinin adresi (sonunda / olmadan). Tüm veriler `${API_URL}/api/v1/...` adresinden gelir.
 *  - Canlıda: sitenin gerçek adresi (https://...).
 *  - Geliştirmede Android emülatörü bilgisayarı 10.0.2.2 adresinden görür; iOS simülatörü localhost'u kullanır.
 *  - Gerçek telefonda deneme: bilgisayarın yerel ağ IP'sini yazın (ör. http://192.168.1.20:8000) ve
 *    Laravel'i `php artisan serve --host=0.0.0.0 --port=8000` ile başlatın.
 *
 * APP_KEY: Sunucudaki MOBILE_APP_KEY ile aynı olmalı. Çekilişe katılım gibi yazma isteklerinde gönderilir.
 *  Sunucuda MOBILE_APP_KEY boşsa bu değer kullanılmaz.
 */
const PRODUCTION_API_URL = 'https://yalovaharemspor.alperengundogdu.com.tr';

const DEV_API_URL = Platform.select({
  android: 'http://10.0.2.2:8000',
  default: 'http://localhost:8000',
});

export const API_URL: string = __DEV__ ? DEV_API_URL : PRODUCTION_API_URL;

export const APP_KEY = '';

/** İstek zaman aşımı (ms) */
export const REQUEST_TIMEOUT = 15000;
