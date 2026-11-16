/**
 * Uçtan uca ekran testleri: fetch, yerel Laravel API'sinden alınmış gerçek yanıtlarla (fixtures) taklit edilir.
 */
import React from 'react';
import { fireEvent, render, screen, waitFor } from '@testing-library/react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import App, { queryClient } from '../App';
import home from './fixtures/home.json';
import upcoming from './fixtures/matches-upcoming.json';
import played from './fixtures/matches-played.json';
import standing from './fixtures/standing.json';
import players from './fixtures/players.json';
import player from './fixtures/player.json';
import staff from './fixtures/staff.json';
import news from './fixtures/news.json';
import article from './fixtures/article.json';
import giveaways from './fixtures/giveaways.json';
import giveawayApp from './fixtures/giveaway-app.json';

type Call = { url: string; method: string; headers: Record<string, string>; body?: any };
let calls: Call[] = [];
let joinResponse: { status: number; body: any } = { status: 201, body: {} };
let offline = false;

const ROUTES: [RegExp, unknown][] = [
  [/\/home$/, home],
  [/\/matches\?status=upcoming$/, upcoming],
  [/\/matches\?status=played$/, played],
  [/\/standing$/, standing],
  [/\/players$/, players],
  [/\/players\/\d+$/, player],
  [/\/staff$/, staff],
  [/\/news(\?type=\w+)?$/, news],
  [/\/news\/\d+$/, article],
  [/\/giveaways$/, giveaways],
  [/\/giveaways\/uygulama-testi$/, giveawayApp],
];

function json(status: number, body: unknown) {
  return Promise.resolve({ ok: status < 400, status, json: () => Promise.resolve(body) } as Response);
}

let joined: any = null;

beforeEach(() => {
  calls = [];
  offline = false;
  joined = null;
  queryClient.clear();
  (globalThis as any).fetch = jest.fn((url: string, init: RequestInit = {}) => {
    const call: Call = { url, method: init.method ?? 'GET', headers: init.headers as Record<string, string>, body: init.body ? JSON.parse(String(init.body)) : undefined };
    calls.push(call);
    if (offline) {
      return Promise.reject(new TypeError('Network request failed'));
    }
    if (call.method === 'POST' && /\/join$/.test(url)) {
      // Gerçek sunucu gibi: başarılı katılımdan sonra çekiliş detayı bu cihazın kaydını döndürür
      if (joinResponse.status < 400) {
        joined = joinResponse.body.data;
      }
      return json(joinResponse.status, joinResponse.body);
    }
    if (/\/giveaways\/uygulama-testi$/.test(url) && joined) {
      return json(200, { data: { ...(giveawayApp as any).data, my_entry: joined } });
    }
    const hit = ROUTES.find(([re]) => re.test(url));
    return hit ? json(200, hit[1]) : json(404, { message: 'Bulunamadı' });
  });
});

const nextMatch = (home as any).data.next_match;
const firstPlayer = (players as any).data[0];

describe('Uygulama', () => {
  it('ana ekran: sıradaki maç, sezon ve haberler API\'den gelir', async () => {
    await render(<App />);
    expect(await screen.findByText(nextMatch.opponent)).toBeTruthy();
    expect(screen.getAllByText(nextMatch.is_home ? nextMatch.home_team : nextMatch.away_team).length).toBeGreaterThan(0);
    expect(screen.getByText('SIRADAKİ MAÇ' + (nextMatch.round ? ` · ${nextMatch.round}. HAFTA` : ''))).toBeTruthy();
    expect(screen.getByText('Son sonuçlar')).toBeTruthy();
    expect(screen.getByText((home as any).data.news[0].title)).toBeTruthy();
    // her istek JSON ister ve cihaz kimliği taşır
    expect(calls[0].url).toMatch(/\/api\/v1\/home$/);
    expect(calls[0].headers.Accept).toBe('application/json');
    expect(calls[0].headers['X-Device-Id']).toMatch(/^[A-Za-z0-9_-]{16,128}$/);
  });

  it('sekmeler: maçlar, kadro ve oyuncu detayı', async () => {
    await render(<App />);
    await screen.findByText(nextMatch.opponent);

    await fireEvent.press(screen.getByText('Maçlar'));
    expect(await screen.findByText('Fikstür')).toBeTruthy();
    await waitFor(() => expect(calls.some(c => c.url.endsWith('/matches?status=upcoming'))).toBe(true));

    await fireEvent.press(screen.getByText('Sonuçlar'));
    await waitFor(() => expect(calls.some(c => c.url.endsWith('/matches?status=played'))).toBe(true));

    await fireEvent.press(screen.getByText('Kadro'));
    expect(await screen.findByText(firstPlayer.last_name)).toBeTruthy();
    await fireEvent.press(screen.getByText(firstPlayer.last_name));
    await waitFor(() => expect(calls.some(c => /\/players\/\d+$/.test(c.url))).toBe(true));
  });

  it('çekiliş: uygulamadan katılım isteği doğru gider ve "Katıldın" görünür', async () => {
    joinResponse = {
      status: 201,
      body: { message: 'Katılımın alındı @ali_test!', data: { username: 'ali_test', joined_at: '2026-11-17T10:00:00+03:00', app_verified: true, is_eligible: false, hint: 'Yorumun henüz görünmüyor.' } },
    };
    await render(<App />);
    await screen.findByText(nextMatch.opponent);
    await fireEvent.press(screen.getByText('Çekiliş'));
    await fireEvent.press(await screen.findByText('Uygulama Testi'));

    expect(await screen.findByText('Nasıl katılırım?')).toBeTruthy();
    // önce hatalı girişler istemci tarafında yakalanır
    await fireEvent.press(screen.getByTestId('join-button'));
    expect(await screen.findByText(/Geçerli bir Instagram kullanıcı adı/)).toBeTruthy();

    await fireEvent.changeText(screen.getByTestId('ig-input'), '@Ali_Test');
    await fireEvent.press(screen.getByTestId('join-button'));
    expect(await screen.findByText(/onay kutusunu/)).toBeTruthy();
    expect(calls.some(c => c.method === 'POST')).toBe(false);

    await fireEvent.press(screen.getByTestId('consent'));
    await fireEvent.press(screen.getByTestId('join-button'));

    expect(await screen.findByText('Katıldın!')).toBeTruthy();
    const post = calls.find(c => c.method === 'POST')!;
    expect(post.url).toMatch(/\/api\/v1\/giveaways\/uygulama-testi\/join$/);
    expect(post.body).toEqual({ instagram_username: 'ali_test', consent: true });
    expect(post.headers['X-Device-Id']).toMatch(/^[A-Za-z0-9_-]{16,128}$/);
    expect(screen.getByText('Yorumun henüz görünmüyor.')).toBeTruthy();
  });

  it('çekiliş: sunucunun "tek cihaz tek hesap" reddi kullanıcıya gösterilir', async () => {
    joinResponse = { status: 422, body: { message: 'Bu cihazdan bu çekilişe zaten @ali_test hesabıyla katıldın.' } };
    await render(<App />);
    await screen.findByText(nextMatch.opponent);
    await fireEvent.press(screen.getByText('Çekiliş'));
    await fireEvent.press(await screen.findByText('Uygulama Testi'));
    await fireEvent.changeText(await screen.findByTestId('ig-input'), 'veli_test');
    await fireEvent.press(screen.getByTestId('consent'));
    await fireEvent.press(screen.getByTestId('join-button'));
    expect(await screen.findByText(/zaten @ali_test hesabıyla katıldın/)).toBeTruthy();
    expect(screen.queryByText('Katıldın!')).toBeNull();
  });

  it('tema: ana sayfadaki düğmeyle açık/koyu arasında geçer ve seçim saklanır', async () => {
    await render(<App />);
    await screen.findByText(nextMatch.opponent);
    const toggle = screen.getByTestId('theme-toggle');
    const before = toggle.props.accessibilityLabel as string;
    await fireEvent.press(toggle);
    const after = screen.getByTestId('theme-toggle').props.accessibilityLabel as string;
    expect(after).not.toBe(before);
    expect([before, after].sort()).toEqual(['Açık temaya geç', 'Koyu temaya geç']);
    await waitFor(async () => expect(['light', 'dark']).toContain(await AsyncStorage.getItem('yh.theme')));
    // tekrar basınca ilk temaya döner
    await fireEvent.press(screen.getByTestId('theme-toggle'));
    expect(screen.getByTestId('theme-toggle').props.accessibilityLabel).toBe(before);
  });

  it('bağlantı yoksa anlaşılır hata ve tekrar dene', async () => {
    offline = true;
    await render(<App />);
    // React Query iki kez daha dener (yaklaşık 3 sn), sonra hata ekranı gelir
    await waitFor(() => expect(screen.getByText('Bağlantı yok')).toBeTruthy(), { timeout: 8000 });
    expect(screen.getByText(/Sunucuya ulaşılamadı/)).toBeTruthy();
    offline = false;
    await fireEvent.press(screen.getByText('Tekrar dene'));
    expect(await screen.findByText(nextMatch.opponent)).toBeTruthy();
  }, 15000);
});
