import AsyncStorage from '@react-native-async-storage/async-storage';
import DeviceInfo from 'react-native-device-info';

const STORAGE_KEY = 'yh.device-id';
let cached: string | null = null;

/** Sunucunun kabul ettiği biçim: 16-128 karakter, harf/rakam/-/_ */
function sanitize(raw: string): string {
  return raw.replace(/[^A-Za-z0-9_-]/g, '').slice(0, 128);
}

function randomId(): string {
  let out = '';
  while (out.length < 32) {
    out += Math.random().toString(36).slice(2);
  }
  return 'r_' + out.slice(0, 32);
}

/**
 * Kalıcı cihaz kimliği. Çekilişte "her cihazdan tek hesap" kuralı buna dayanır.
 * Önce işletim sisteminin kimliği (Android ID / iOS identifierForVendor) kullanılır; uygulama silinip
 * yüklense de çoğu durumda aynı kalır. Alınamazsa bir kez üretilip saklanan rastgele kimlik kullanılır.
 * Sunucu bu değeri ham hâliyle değil, özetini saklar.
 */
export async function getDeviceId(): Promise<string> {
  if (cached) {
    return cached;
  }
  try {
    const id = sanitize(await DeviceInfo.getUniqueId());
    if (id.length >= 16) {
      cached = 'd_' + id;
      return cached;
    }
  } catch {
    // aşağıdaki yedek kimliğe geç
  }
  try {
    const stored = await AsyncStorage.getItem(STORAGE_KEY);
    if (stored && stored.length >= 16) {
      cached = stored;
      return stored;
    }
  } catch {
    // depolama okunamadıysa yeni kimlik üret
  }
  const fresh = randomId();
  cached = fresh;
  try {
    await AsyncStorage.setItem(STORAGE_KEY, fresh);
  } catch {
    // saklanamazsa bu oturum için bellekteki kimlik kullanılır
  }
  return fresh;
}
