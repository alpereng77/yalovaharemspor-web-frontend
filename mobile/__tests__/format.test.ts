import { fullDate, longDate, normalizeInstagram, pct, remaining, shortDate, stat, time } from '../src/lib/format';

describe('Türkçe tarih biçimleri', () => {
  // Sunucu Türkiye saatiyle verir; telefonun saat dilimi farklı olsa da aynı görünmeli
  const iso = '2026-11-21T14:00:00+03:00';

  it('uzun, kısa ve tam tarih', () => {
    expect(longDate(iso)).toBe('21 Kasım Cumartesi');
    expect(shortDate(iso)).toBe('21 Kas');
    expect(fullDate(iso)).toBe('21 Kasım 2026');
  });

  it('saat, belli değilse null', () => {
    expect(time(iso)).toBe('14:00');
    expect(time(iso, false)).toBeNull();
  });

  it('gece yarısına yakın saatler gün kaymaz', () => {
    expect(longDate('2026-11-21T23:30:00+03:00')).toBe('21 Kasım Cumartesi');
    expect(time('2026-11-21T00:15:00+03:00')).toBe('00:15');
  });

  it('yalnız tarih (haberler)', () => {
    expect(fullDate('2026-09-29')).toBe('29 Eylül 2026');
  });

  it('boş tarih', () => {
    expect(longDate(null)).toBe('Tarih açıklanacak');
    expect(fullDate(undefined)).toBe('');
  });

  it('geri sayım', () => {
    const now = Date.parse('2026-11-19T10:00:00+03:00');
    expect(remaining('2026-11-21T13:12:00+03:00', now)).toBe('2 gün 3 sa 12 dk');
    expect(remaining('2026-11-19T10:45:00+03:00', now)).toBe('0 sa 45 dk');
    expect(remaining('2026-11-18T10:00:00+03:00', now)).toBe('Süre doldu');
  });

  it('yüzde Türkçe biçimde', () => {
    expect(pct('63.2%')).toBe('%63,2');
    expect(pct('0%')).toBe('%0');
    expect(pct(null)).toBe('—');
  });

  it('istatistik ondalık virgülle', () => {
    expect(stat(12.5)).toBe('12,5');
    expect(stat(null)).toBe('—');
  });
});

describe('Instagram kullanıcı adı (sunucuyla aynı kurallar)', () => {
  it.each([
    ['@Ali_Test', 'ali_test'],
    ['  veli.test  ', 'veli.test'],
    ['https://www.instagram.com/yalovaharemspor/', 'yalovaharemspor'],
    ['https://instagram.com/abc?igsh=xyz', 'abc'],
  ])('%s → %s', (input, out) => {
    expect(normalizeInstagram(input)).toBe(out);
  });

  it.each(['', 'ali veli', 'çağrı', 'a'.repeat(31), '@'])('geçersiz: %s', input => {
    expect(normalizeInstagram(input)).toBeNull();
  });
});
