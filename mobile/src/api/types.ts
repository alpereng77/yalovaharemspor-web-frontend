/** Laravel API (v1) yanıt tipleri. Sunucudaki App\Http\Controllers\Api\V1\Presenter ile birebir. */

export type Club = {
  club_name: string | null;
  city_name: string | null;
  site_title: string | null;
  meta_description: string | null;
  hero_title_1: string | null;
  hero_title_2: string | null;
  hero_description: string | null;
  hero_image: string | null;
  about_title: string | null;
  about_image_1: string | null;
  about_image_2: string | null;
  identity_green_title: string | null;
  identity_green_text: string | null;
  identity_red_title: string | null;
  identity_red_text: string | null;
  identity_mascot_title: string | null;
  identity_mascot_text: string | null;
  social_instagram: string | null;
  social_x: string | null;
  social_youtube: string | null;
  footer_hashtag: string | null;
  footer_copyright: string | null;
  app_store_url: string | null;
  play_store_url: string | null;
  logo: string;
  website: string;
};

export type Standing = {
  season: string;
  rank: number;
  played: number;
  won: number;
  lost: number;
  points: number;
  points_for: number;
  points_against: number;
  diff: number;
};

export type Match = {
  id: number;
  round: number | null;
  date: string | null;
  has_time: boolean;
  home_team: string;
  away_team: string;
  /** Gerçek TBF logosu; yoksa null */
  home_logo: string | null;
  away_logo: string | null;
  home_score: number | null;
  away_score: number | null;
  is_home: boolean;
  opponent: string;
  venue: string | null;
  status: 'upcoming' | 'played' | 'postponed' | string;
  is_next: boolean;
  result: 'W' | 'L' | null;
};

export type PlayerStats = {
  season: string;
  games: number;
  minutes: number;
  points: number;
  rebounds: number;
  assists: number;
  steals: number;
  blocks: number;
  turnovers: number;
  fouls: number;
  efficiency: number;
  fg_pct: string;
  three_pct: string;
  ft_pct: string;
  total_points: number;
  total_rebounds: number;
  total_assists: number;
};

export type Player = {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
  number: string | null;
  position: string | null;
  height: string | null;
  weight: string | null;
  birth_date: string | null;
  nationality: string | null;
  image: string;
  stats: PlayerStats | null;
};

export type Staff = { id: number; name: string; role: string; nationality: string | null; image: string };

export type Article = {
  id: number;
  type: 'blog' | 'press';
  category: string;
  title: string;
  date: string | null;
  summary: string | null;
  image: string;
  source_title: string | null;
  source_url: string | null;
  content?: string | null;
};

export type Sponsor = { id: number; name: string; category: string; logo: string; website: string | null };

export type TicketPoint = {
  id: number;
  name: string;
  logo: string;
  address: string | null;
  maps_url: string | null;
  phone: string | null;
  working_hours: string | null;
};

export type GiveawaySummary = {
  slug: string;
  title: string;
  prize: string;
  image: string | null;
  status: 'active' | 'drawn' | 'completed' | string;
  status_text: string;
  is_open: boolean;
  ends_at: string | null;
  winner_count: number;
  web_url: string;
  app_join: boolean;
  requires_app: boolean;
};

export type GiveawayStep = { key: string; text: string; action: { label: string; url: string } | null };

export type MyEntry = {
  username: string;
  joined_at: string | null;
  app_verified: boolean;
  is_eligible: boolean;
  hint: string | null;
};

export type Giveaway = GiveawaySummary & {
  description: string | null;
  instagram_post_url: string | null;
  required_mentions: number;
  steps: GiveawayStep[];
  drawn_at: string | null;
  eligible_count_at_draw: number | null;
  winners: { username: string; profile_url: string }[];
  my_entry: MyEntry | null;
};

export type Home = {
  club: Club;
  standing: Standing | null;
  next_match: Match | null;
  recent_results: Match[];
  upcoming: Match[];
  news: Article[];
  giveaways: GiveawaySummary[];
};

export type MatchesResponse = { matches: Match[]; record: { won: number; lost: number } };
