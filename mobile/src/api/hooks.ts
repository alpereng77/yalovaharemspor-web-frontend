import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { getData, request } from './client';
import type {
  Article, Club, Giveaway, GiveawaySummary, Home, MatchesResponse, MyEntry, Player, Sponsor, Staff, Standing, TicketPoint,
} from './types';

/** Tüm ekranların veri kancaları. Önbellek + aşağı çekip yenileme React Query ile. */
export const keys = {
  home: ['home'] as const,
  club: ['club'] as const,
  matches: (status: string) => ['matches', status] as const,
  standing: ['standing'] as const,
  players: ['players'] as const,
  player: (id: number) => ['player', id] as const,
  staff: ['staff'] as const,
  news: (type: string) => ['news', type] as const,
  article: (id: number) => ['article', id] as const,
  sponsors: ['sponsors'] as const,
  ticketPoints: ['ticket-points'] as const,
  giveaways: ['giveaways'] as const,
  giveaway: (slug: string) => ['giveaway', slug] as const,
};

export const useHome = () => useQuery({ queryKey: keys.home, queryFn: ({ signal }) => getData<Home>('/home', signal) });
export const useClub = () => useQuery({ queryKey: keys.club, queryFn: ({ signal }) => getData<Club>('/club', signal) });

export const useMatches = (status: 'upcoming' | 'played') =>
  useQuery({ queryKey: keys.matches(status), queryFn: ({ signal }) => getData<MatchesResponse>(`/matches?status=${status}`, signal) });

export const useStanding = () =>
  useQuery({ queryKey: keys.standing, queryFn: ({ signal }) => getData<Standing | null>('/standing', signal) });

export const usePlayers = () => useQuery({ queryKey: keys.players, queryFn: ({ signal }) => getData<Player[]>('/players', signal) });

export function usePlayer(id: number) {
  const client = useQueryClient();
  return useQuery<Player>({
    queryKey: keys.player(id),
    queryFn: ({ signal }) => getData<Player>(`/players/${id}`, signal),
    // Kadro listesinden gelindiyse oyuncu anında görünür, arka planda tazelenir
    placeholderData: () => client.getQueryData<Player[]>(keys.players)?.find(p => p.id === id),
  });
}

export const useStaff = () => useQuery({ queryKey: keys.staff, queryFn: ({ signal }) => getData<Staff[]>('/staff', signal) });

export const useNews = (type: 'all' | 'blog' | 'press') =>
  useQuery({
    queryKey: keys.news(type),
    queryFn: ({ signal }) => getData<Article[]>(type === 'all' ? '/news' : `/news?type=${type}`, signal),
  });

export const useArticle = (id: number) =>
  useQuery({ queryKey: keys.article(id), queryFn: ({ signal }) => getData<Article>(`/news/${id}`, signal) });

export const useSponsors = () => useQuery({ queryKey: keys.sponsors, queryFn: ({ signal }) => getData<Sponsor[]>('/sponsors', signal) });

export const useTicketPoints = () =>
  useQuery({ queryKey: keys.ticketPoints, queryFn: ({ signal }) => getData<TicketPoint[]>('/ticket-points', signal) });

export const useGiveaways = () =>
  useQuery({ queryKey: keys.giveaways, queryFn: ({ signal }) => getData<GiveawaySummary[]>('/giveaways', signal) });

export const useGiveaway = (slug: string) =>
  useQuery({ queryKey: keys.giveaway(slug), queryFn: ({ signal }) => getData<Giveaway>(`/giveaways/${encodeURIComponent(slug)}`, signal) });

export function useJoinGiveaway(slug: string) {
  const client = useQueryClient();
  return useMutation({
    mutationFn: (input: { instagram_username: string; consent: boolean }) =>
      request<{ message: string; data: MyEntry }>(`/giveaways/${encodeURIComponent(slug)}/join`, { method: 'POST', body: input }),
    onSuccess: res => {
      client.setQueryData<Giveaway>(keys.giveaway(slug), old => (old ? { ...old, my_entry: res.data } : old));
      client.invalidateQueries({ queryKey: keys.giveaway(slug) });
    },
  });
}

export const useSubscribe = () =>
  useMutation({ mutationFn: (email: string) => request<{ message: string }>('/newsletter', { method: 'POST', body: { email } }) });
