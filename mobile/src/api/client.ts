import { API_URL, APP_KEY, REQUEST_TIMEOUT } from '../config';
import { getDeviceId } from '../lib/device';

export class ApiError extends Error {
  status: number;
  fieldErrors: Record<string, string[]>;

  constructor(message: string, status: number, fieldErrors: Record<string, string[]> = {}) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.fieldErrors = fieldErrors;
  }

  /** İnternet yok / sunucuya ulaşılamadı */
  get isNetwork(): boolean {
    return this.status === 0;
  }
}

type Options = { method?: 'GET' | 'POST'; body?: unknown; signal?: AbortSignal };

/** Tüm API istekleri buradan geçer: zaman aşımı, cihaz kimliği, uygulama anahtarı ve anlaşılır hata mesajları. */
export async function request<T>(path: string, { method = 'GET', body, signal }: Options = {}): Promise<T> {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT);
  const onAbort = () => controller.abort();
  signal?.addEventListener('abort', onAbort);

  const headers: Record<string, string> = {
    Accept: 'application/json',
    'X-Device-Id': await getDeviceId(),
  };
  if (body !== undefined) {
    headers['Content-Type'] = 'application/json';
  }
  if (APP_KEY) {
    headers['X-App-Key'] = APP_KEY;
  }

  let res: Response;
  try {
    res = await fetch(`${API_URL}/api/v1${path}`, {
      method,
      headers,
      body: body === undefined ? undefined : JSON.stringify(body),
      signal: controller.signal,
    });
  } catch {
    throw new ApiError(
      signal?.aborted ? 'İstek iptal edildi.' : 'Sunucuya ulaşılamadı. İnternet bağlantını kontrol edip tekrar dene.',
      0,
    );
  } finally {
    clearTimeout(timer);
    signal?.removeEventListener('abort', onAbort);
  }

  let json: any = null;
  try {
    json = await res.json();
  } catch {
    // gövde JSON değilse aşağıda genel hata verilir
  }

  if (!res.ok) {
    const fallback =
      res.status === 404 ? 'Aradığın içerik bulunamadı.'
      : res.status === 429 ? 'Çok fazla deneme yapıldı. Bir dakika bekleyip tekrar dene.'
      : res.status >= 500 ? 'Sunucuda bir sorun oluştu. Biraz sonra tekrar dene.'
      : 'İşlem tamamlanamadı.';
    throw new ApiError(json?.message || fallback, res.status, json?.errors ?? {});
  }
  return json as T;
}

/** { data: T } zarfını açar */
export async function getData<T>(path: string, signal?: AbortSignal): Promise<T> {
  const json = await request<{ data: T }>(path, { signal });
  return json.data;
}
