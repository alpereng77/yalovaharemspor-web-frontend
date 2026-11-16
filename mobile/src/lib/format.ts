/**
 * Türkçe tarih biçimleri. Sunucu tarihleri +03:00 ile verir; telefonun saat dilimi ne olursa olsun
 * maç saati Türkiye saatiyle gösterilir (Intl desteğine bağımlı değil).
 */
const MONTHS = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
const MONTHS_SHORT = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
const DAYS = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
const TR_OFFSET_MIN = 180;

type Parts = { year: number; month: number; day: number; weekday: number; hour: number; minute: number };

/** ISO tarihini Türkiye saatine göre parçalara ayırır. "2026-09-29" gibi yalnız tarihleri de kabul eder. */
export function trParts(iso: string | null | undefined): Parts | null {
  if (!iso) {
    return null;
  }
  const dateOnly = /^\d{4}-\d{2}-\d{2}$/.test(iso);
  const ms = Date.parse(dateOnly ? `${iso}T12:00:00+03:00` : iso);
  if (Number.isNaN(ms)) {
    return null;
  }
  const d = new Date(ms + TR_OFFSET_MIN * 60000);
  return {
    year: d.getUTCFullYear(),
    month: d.getUTCMonth(),
    day: d.getUTCDate(),
    weekday: d.getUTCDay(),
    hour: d.getUTCHours(),
    minute: d.getUTCMinutes(),
  };
}

const pad = (n: number) => String(n).padStart(2, '0');

/** "21 Kasım Cumartesi" */
export function longDate(iso: string | null | undefined): string {
  const p = trParts(iso);
  return p ? `${p.day} ${MONTHS[p.month]} ${DAYS[p.weekday]}` : 'Tarih açıklanacak';
}

/** "21 Kasım 2026" */
export function fullDate(iso: string | null | undefined): string {
  const p = trParts(iso);
  return p ? `${p.day} ${MONTHS[p.month]} ${p.year}` : '';
}

/** "21 Kas" */
export function shortDate(iso: string | null | undefined): string {
  const p = trParts(iso);
  return p ? `${p.day} ${MONTHS_SHORT[p.month]}` : '—';
}

/** "14:00" ya da saat belli değilse null */
export function time(iso: string | null | undefined, hasTime = true): string | null {
  const p = trParts(iso);
  return p && hasTime ? `${pad(p.hour)}:${pad(p.minute)}` : null;
}

/** "2 gün 3 sa 12 dk" — geri sayım */
export function remaining(iso: string | null | undefined, now = Date.now()): string | null {
  if (!iso) {
    return null;
  }
  const s = Math.floor((Date.parse(iso) - now) / 1000);
  if (Number.isNaN(s)) {
    return null;
  }
  if (s <= 0) {
    return 'Süre doldu';
  }
  const d = Math.floor(s / 86400);
  const h = Math.floor((s % 86400) / 3600);
  const m = Math.floor((s % 3600) / 60);
  return (d ? `${d} gün ` : '') + `${h} sa ${m} dk`;
}

/** Ondalıklı istatistik: 12.5 → "12,5" */
export function stat(n: number | null | undefined, digits = 1): string {
  if (n === null || n === undefined || Number.isNaN(n)) {
    return '—';
  }
  return n.toFixed(digits).replace('.', ',');
}

/** TBF yüzdesi "63.2%" → Türkçe "%63,2" */
export function pct(value: string | null | undefined): string {
  const m = String(value ?? '').match(/-?\d+(?:[.,]\d+)?/);
  return m ? `%${m[0].replace('.', ',')}` : '—';
}

/** Instagram kullanıcı adını sunucunun kurallarıyla aynı şekilde temizler; geçersizse null */
export function normalizeInstagram(value: string): string | null {
  let u = value.trim().toLowerCase();
  u = u.replace(/^https?:\/\/(www\.)?instagram\.com\//, '').replace(/^@+/, '').replace(/[/?#].*$/, '');
  return /^[a-z0-9._]{1,30}$/.test(u) ? u : null;
}
