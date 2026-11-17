import React, { useEffect, useState } from 'react';
import { Image, StyleSheet, Text, View } from 'react-native';
import type { Article, GiveawaySummary, Match, Player, Standing } from '../api/types';
import { fullDate, longDate, shortDate, time, trParts } from '../lib/format';
import { fonts, radius, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';
import { Gradient } from './Gradient';
import { Icon } from './Icon';
import { CountUp, PressableScale, Pulse } from './motion';
import { TopImage } from './TopImage';
import { Pill, TeamBadge } from './ui';

/** Belirli aralıkla güncellenen "şimdi" (geri sayımlar) */
export function useNow(intervalMs = 1000) {
  const [now, setNow] = useState(Date.now());
  useEffect(() => {
    const t = setInterval(() => setNow(Date.now()), intervalMs);
    return () => clearInterval(t);
  }, [intervalMs]);
  return now;
}

function splitRemaining(iso: string | null, now: number) {
  const s = iso ? Math.max(0, Math.floor((Date.parse(iso) - now) / 1000)) : 0;
  return { d: Math.floor(s / 86400), h: Math.floor((s % 86400) / 3600), m: Math.floor((s % 3600) / 60), s: s % 60, done: s === 0 };
}
const pad = (n: number) => String(n).padStart(2, '0');

// ---------------------------------------------------------------- Maç merkezi

/**
 * Ana ekranın kalbi: sıradaki maç. Kutusuz, sade: içerik doğrudan sayfa zemininde;
 * üç bölüm (takımlar + saat, geri sayım, salon) ince çizgilerle ayrılır. Tarih bir kez yazılır.
 */
export function MatchCenter({ match, logo, onPress }: { match: Match; logo?: string | number | null; onPress?: () => void }) {
  const { c } = useTheme();
  const styles = useStyles();
  const now = useNow(1000);
  const r = splitRemaining(match.date, now);
  const t = time(match.date, match.has_time);
  const units = [{ v: r.d, l: 'GÜN' }, { v: r.h, l: 'SAAT' }, { v: r.m, l: 'DAKİKA' }, { v: r.s, l: 'SANİYE' }];
  return (
    <PressableScale onPress={onPress} disabled={!onPress} style={styles.center} accessibilityLabel={`Sıradaki maç: ${match.home_team} - ${match.away_team}, ${longDate(match.date)} ${t ?? ''}`}>
      <View style={styles.centerTop}>
        <Text style={styles.centerKicker}>SIRADAKİ MAÇ{match.round ? ` · ${match.round}. HAFTA` : ''}</Text>
        <Text style={[styles.tag, match.is_home && styles.tagHome]}>{match.is_home ? 'İÇ SAHA' : 'DEPLASMAN'}</Text>
      </View>

      <View style={styles.teams}>
        <View style={styles.team}>
          <TeamBadge name={match.home_team} ours={match.is_home} logo={logo} opponentLogo={match.home_logo} size={58} />
          <Text style={[styles.teamName, match.is_home && styles.centerOurs]} numberOfLines={2}>{match.home_team}</Text>
        </View>
        <View style={styles.mid}>
          <Text style={styles.midTime}>{t ?? '--:--'}</Text>
          <Text style={styles.midDate}>{longDate(match.date).toLocaleUpperCase('tr-TR')}</Text>
        </View>
        <View style={styles.team}>
          <TeamBadge name={match.away_team} ours={!match.is_home} logo={logo} opponentLogo={match.away_logo} size={58} />
          <Text style={[styles.teamName, !match.is_home && styles.centerOurs]} numberOfLines={2}>{match.away_team}</Text>
        </View>
      </View>

      {match.date && !r.done ? (
        <View style={styles.countdown} accessible accessibilityLabel={`Maça ${r.d} gün ${r.h} saat ${r.m} dakika`}>
          {units.map((u, i) => (
            <View key={u.l} style={[styles.unit, i > 0 && styles.unitDivider]}>
              <Text style={styles.unitValue}>{pad(u.v)}</Text>
              <Text style={styles.unitLabel}>{u.l}</Text>
            </View>
          ))}
        </View>
      ) : null}

      {match.venue ? (
        <View style={styles.centerMeta}>
          <Icon name="pin" size={14} color={c.muted} />
          <Text style={styles.metaText} numberOfLines={1}>{match.venue}</Text>
        </View>
      ) : null}
    </PressableScale>
  );
}

// ---------------------------------------------------------------- Skorlar

/** Yatay kaydırılan sonuç (ana ekran): kutusuz, sonuçlar arasında ince dikey çizgi */
export function ResultCard({ match }: { match: Match }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const win = match.result === 'W';
  return (
    <View style={styles.result} accessible accessibilityLabel={`${match.home_team} ${match.home_score} - ${match.away_score} ${match.away_team}, ${win ? 'galibiyet' : 'mağlubiyet'}`}>
      <View style={styles.resultHead}>
        <Text style={T.label}>{shortDate(match.date)}</Text>
        <Text style={[styles.resultBadge, { color: win ? c.win : c.loss }]}>{win ? 'GALİBİYET' : 'MAĞLUBİYET'}</Text>
      </View>
      {[
        { name: match.home_team, score: match.home_score, ours: match.is_home, logo: match.home_logo },
        { name: match.away_team, score: match.away_score, ours: !match.is_home, logo: match.away_logo },
      ].map((s, i) => {
        const won = i === 0 ? (match.home_score ?? 0) > (match.away_score ?? 0) : (match.away_score ?? 0) > (match.home_score ?? 0);
        return (
          <View key={i} style={styles.resultLine}>
            {!s.ours && s.logo ? <TeamBadge name={s.name} opponentLogo={s.logo} size={20} /> : null}
            <Text style={[styles.resultTeam, s.ours && styles.teamOurs]} numberOfLines={1}>{s.name}</Text>
            <Text style={[styles.resultScore, !won && { color: c.faint }]}>{s.score}</Text>
          </View>
        );
      })}
    </View>
  );
}

/** Fikstür / sonuç listesi satırı */
export function MatchRow({ match }: { match: Match }) {
  const { c } = useTheme();
  const styles = useStyles();
  const played = match.home_score !== null && match.away_score !== null;
  const t = time(match.date, match.has_time);
  const p = trParts(match.date);
  const label = played
    ? `${match.home_team} ${match.home_score} - ${match.away_score} ${match.away_team}, ${match.result === 'W' ? 'galibiyet' : 'mağlubiyet'}`
    : `${match.home_team} - ${match.away_team}, ${longDate(match.date)} ${t ?? ''}`;
  return (
    <View style={styles.mrow} accessible accessibilityLabel={label}>
      <View style={styles.mrowDate}>
        <Text style={styles.mrowDay}>{p ? p.day : '–'}</Text>
        <Text style={styles.mrowMonth}>{shortDate(match.date).split(' ')[1]?.toLocaleUpperCase('tr-TR') ?? ''}</Text>
      </View>
      <View style={styles.mrowTeams}>
        {[
          { name: match.home_team, score: match.home_score, ours: match.is_home, won: played && match.home_score! > match.away_score!, logo: match.home_logo },
          { name: match.away_team, score: match.away_score, ours: !match.is_home, won: played && match.away_score! > match.home_score!, logo: match.away_logo },
        ].map((s, i) => (
          <View key={i} style={styles.mrowLine}>
            {!s.ours && s.logo ? <TeamBadge name={s.name} opponentLogo={s.logo} size={20} /> : null}
            <Text style={[styles.mrowTeam, s.ours && styles.teamOurs]} numberOfLines={1}>{s.name}</Text>
            {played ? <Text style={[styles.mrowScore, !s.won && { color: c.faint }]}>{s.score}</Text> : null}
          </View>
        ))}
        {!played ? (
          <Text style={styles.mrowMeta} numberOfLines={1}>
            {t ?? 'Saat açıklanacak'}{match.venue ? ` · ${match.venue}` : ''}
          </Text>
        ) : null}
      </View>
      {played ? (
        <Text style={[styles.wlText, { color: match.result === 'W' ? c.win : c.loss }]}>{match.result === 'W' ? 'G' : 'M'}</Text>
      ) : (
        <Text style={[styles.mrowHa, match.is_home && { color: c.text }]}>{match.is_home ? 'İÇ' : 'DEP'}</Text>
      )}
    </View>
  );
}

// ---------------------------------------------------------------- Sezon

export function SeasonStrip({ standing }: { standing: Standing }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  return (
    <View style={styles.season} accessible accessibilityLabel={`${standing.season}: ${standing.rank}. sıra, ${standing.won} galibiyet, ${standing.lost} mağlubiyet, ${standing.points} puan`}>
      <View style={styles.rankBox}>
        <CountUp value={standing.rank} suffix="." style={styles.rank} />
        <Text style={styles.rankLabel}>{standing.rank === 1 ? 'LİDER' : 'SIRA'}</Text>
      </View>
      <View style={styles.seasonNums}>
        {[
          { v: standing.won, l: 'G', c: c.win },
          { v: standing.lost, l: 'M', c: c.loss },
          { v: standing.points, l: 'PUAN', c: c.text },
          { v: standing.diff, l: 'AVERAJ', c: c.gold, p: standing.diff > 0 ? '+' : '' },
        ].map(n => (
          <View key={n.l} style={styles.seasonItem}>
            <CountUp value={n.v} prefix={n.p} style={[styles.seasonNum, { color: n.c }]} />
            <Text style={T.label}>{n.l}</Text>
          </View>
        ))}
      </View>
    </View>
  );
}

// ---------------------------------------------------------------- Kadro

export function PlayerCard({ player, onPress, width }: { player: Player; onPress: () => void; width?: number }) {
  const styles = useStyles();
  return (
    <PressableScale onPress={onPress} style={[styles.player, width ? { width } : { flex: 1 }]} accessibilityLabel={`${player.number ? player.number + ' numara ' : ''}${player.name}, ${player.position ?? ''}`}>
      <TopImage uri={player.image} />
      <Gradient />
      {player.number ? <Text style={styles.bigNumber}>{player.number}</Text> : null}
      <View style={styles.playerInfo}>
        <Text style={styles.playerFirst} numberOfLines={1}>{player.first_name}</Text>
        <Text style={styles.playerLast} numberOfLines={1}>{player.last_name}</Text>
        {player.position ? <Text style={styles.playerPos} numberOfLines={1}>{player.position.replace(/\s*\(\d\)/, '')}</Text> : null}
      </View>
    </PressableScale>
  );
}

// ---------------------------------------------------------------- Haberler

export function NewsFeature({ article, onPress }: { article: Article; onPress: () => void }) {
  const { c } = useTheme();
  const styles = useStyles();
  return (
    <PressableScale onPress={onPress} style={styles.feature} accessibilityLabel={article.title}>
      <Image source={{ uri: article.image }} style={StyleSheet.absoluteFill} resizeMode="cover" />
      <Gradient stops={[{ offset: 0, color: '#05100A', opacity: 0.1 }, { offset: 0.45, color: '#05100A', opacity: 0.35 }, { offset: 1, color: '#05100A', opacity: 0.98 }]} />
      <View style={styles.featureBody}>
        <Text style={[styles.newsKicker, { color: c.gold }]}>{article.type === 'press' ? 'BASINDA' : article.category}{article.date ? `  ·  ${fullDate(article.date)}` : ''}</Text>
        <Text style={styles.featureTitle} numberOfLines={3}>{article.title}</Text>
      </View>
    </PressableScale>
  );
}

export function NewsRow({ article, onPress }: { article: Article; onPress: () => void }) {
  const { T } = useTheme();
  const styles = useStyles();
  return (
    <PressableScale onPress={onPress} style={styles.newsRow} accessibilityLabel={article.title}>
      <Image source={{ uri: article.image }} style={styles.newsThumb} resizeMode="cover" />
      <View style={{ flex: 1, gap: 4 }}>
        <Text style={styles.newsKicker}>{article.type === 'press' ? 'BASINDA' : article.category}</Text>
        <Text style={styles.newsTitle} numberOfLines={3}>{article.title}</Text>
        {article.date ? <Text style={T.small}>{fullDate(article.date)}</Text> : null}
      </View>
    </PressableScale>
  );
}

// ---------------------------------------------------------------- Çekiliş

/** Ana ekrandaki kırmızı çekiliş afişi */
export function GiveawayBanner({ giveaway, onPress }: { giveaway: GiveawaySummary; onPress: () => void }) {
  const { c } = useTheme();
  const styles = useStyles();
  return (
    <PressableScale onPress={onPress} style={styles.banner} accessibilityLabel={`Çekiliş: ${giveaway.title}, ${giveaway.prize}`}>
      <Gradient vertical={false} stops={[{ offset: 0, color: '#C40C0C' }, { offset: 1, color: '#6E0000' }]} />
      {giveaway.image ? (
        <>
          <Image source={{ uri: giveaway.image }} style={styles.bannerImg} resizeMode="cover" />
          <Gradient vertical={false} stops={[{ offset: 0, color: '#8E0303', opacity: 1 }, { offset: 0.5, color: '#8E0303', opacity: 0.85 }, { offset: 1, color: '#8E0303', opacity: 0.1 }]} />
        </>
      ) : null}
      <View style={{ flex: 1, gap: 8, maxWidth: '72%' }}>
        <View style={styles.row}><Pulse color={c.white} size={7} /><Text style={styles.bannerKicker}>ÇEKİLİŞ DEVAM EDİYOR</Text></View>
        <Text style={styles.bannerTitle} numberOfLines={2}>{giveaway.prize}</Text>
        <Text style={styles.bannerSub} numberOfLines={1}>{giveaway.title}</Text>
        <View style={styles.bannerCta}><Text style={styles.bannerCtaText}>HEMEN KATIL</Text><Icon name="chevron" size={14} color={c.red} strokeWidth={3} /></View>
      </View>
      <View style={styles.bannerIcon}><Icon name="gift" size={34} color={c.white} /></View>
    </PressableScale>
  );
}

export function GiveawayCard({ giveaway, onPress }: { giveaway: GiveawaySummary; onPress: () => void }) {
  const { c } = useTheme();
  const styles = useStyles();
  const tone = giveaway.status === 'completed' ? 'gold' : giveaway.is_open ? 'green' : 'gray';
  return (
    <PressableScale onPress={onPress} style={styles.gw} accessibilityLabel={`${giveaway.title}, ${giveaway.prize}, ${giveaway.status_text}`}>
      {giveaway.image ? <Image source={{ uri: giveaway.image }} style={styles.gwImg} /> : <View style={[styles.gwImg, styles.center1]}><Icon name="gift" size={28} color={c.gold} /></View>}
      <View style={{ flex: 1, gap: 8 }}>
        <Pill text={giveaway.status_text} tone={tone} live={giveaway.is_open} />
        <Text style={styles.gwTitle} numberOfLines={2}>{giveaway.title}</Text>
        <Text style={styles.gwPrize} numberOfLines={1}>{giveaway.prize}</Text>
      </View>
      <Icon name="chevron" size={20} color={c.muted} />
    </PressableScale>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  row: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  rowBetween: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  center1: { alignItems: 'center', justifyContent: 'center' },
  teamOurs: { color: c.text, fontFamily: fonts.condBlack },
  centerOurs: { color: c.text, fontFamily: fonts.condBlack },

  center: { paddingTop: space.sm },
  centerTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  centerKicker: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 2, color: c.muted },
  tag: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.gold },
  tagHome: { color: c.text },
  teams: { flexDirection: 'row', alignItems: 'flex-start', marginTop: space.xxl },
  team: { flex: 1, alignItems: 'center', gap: space.sm },
  teamName: { fontFamily: fonts.condBold, fontSize: 15, lineHeight: 22, color: c.textSoft, textAlign: 'center', textTransform: 'uppercase' },
  mid: { width: 112, alignItems: 'center', paddingTop: 8 },
  midTime: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.text, fontVariant: ['tabular-nums'] },
  midDate: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1, color: c.gold, textAlign: 'center', marginTop: 4 },
  countdown: { flexDirection: 'row', marginTop: space.xxl, paddingTop: space.xl, borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: c.line },
  unit: { flex: 1, alignItems: 'center' },
  unitDivider: { borderLeftWidth: StyleSheet.hairlineWidth, borderLeftColor: c.line },
  unitValue: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.text, fontVariant: ['tabular-nums'] },
  unitLabel: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.muted, marginTop: 4 },
  centerMeta: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, marginTop: space.xl },
  metaText: { fontFamily: fonts.regular, fontSize: 12, color: c.muted, flexShrink: 1 },

  result: { width: 216, gap: 12, paddingRight: space.xl, borderRightWidth: StyleSheet.hairlineWidth, borderRightColor: c.line },
  resultHead: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 4 },
  resultBadge: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1 },
  resultLine: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  resultTeam: { flex: 1, fontFamily: fonts.condBold, fontSize: 15, color: c.muted, textTransform: 'uppercase' },
  resultScore: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, fontVariant: ['tabular-nums'] },

  mrow: { flexDirection: 'row', alignItems: 'center', paddingVertical: space.md, gap: space.md, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: c.line },
  mrowDate: { width: 44, alignItems: 'center' },
  mrowDay: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.text },
  mrowMonth: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1, color: c.gold },
  mrowTeams: { flex: 1, gap: 4 },
  mrowLine: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  mrowTeam: { flex: 1, fontFamily: fonts.condBold, fontSize: 15, color: c.muted, textTransform: 'uppercase' },
  mrowScore: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, minWidth: 34, textAlign: 'right', fontVariant: ['tabular-nums'] },
  mrowMeta: { fontFamily: fonts.regular, fontSize: 12, color: c.muted, marginTop: 4 },
  wlText: { fontFamily: fonts.condBlack, fontSize: 22, width: 24, textAlign: 'center' },
  mrowHa: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1, color: c.muted, width: 30, textAlign: 'center' },

  season: { flexDirection: 'row', alignItems: 'center', gap: space.xl },
  rankBox: { alignItems: 'center', minWidth: 64 },
  rank: { fontFamily: fonts.condBlackItalic, fontSize: 40, lineHeight: 42, color: c.text },
  rankLabel: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 2, color: c.muted },
  seasonNums: { flex: 1, flexDirection: 'row' },
  seasonItem: { flex: 1, alignItems: 'center', gap: 4 },
  seasonNum: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, fontVariant: ['tabular-nums'] },

  player: { height: 250, borderRadius: radius.lg, overflow: 'hidden', backgroundColor: c.surface2 },
  bigNumber: { position: 'absolute', top: 6, right: 10, fontFamily: fonts.condBlackItalic, fontSize: 40, lineHeight: 42, color: 'rgba(255,255,255,0.9)', textShadowColor: 'rgba(0,0,0,0.45)', textShadowRadius: 12, textShadowOffset: { width: 0, height: 2 } },
  playerInfo: { position: 'absolute', left: space.md, right: space.md, bottom: space.md },
  playerFirst: { fontFamily: fonts.condBold, fontSize: 15, color: 'rgba(255,255,255,0.8)', textTransform: 'uppercase' },
  playerLast: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.white, textTransform: 'uppercase' },
  playerPos: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.2, color: c.gold, textTransform: 'uppercase', marginTop: 4 },

  feature: { height: 300, borderRadius: radius.xl, overflow: 'hidden', backgroundColor: c.surface2, justifyContent: 'flex-end' },
  featureBody: { padding: space.xl, gap: 8 },
  featureTitle: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.white, textTransform: 'uppercase' },
  newsKicker: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.muted, textTransform: 'uppercase' },
  newsRow: { flexDirection: 'row', gap: space.md, alignItems: 'center' },
  newsThumb: { width: 104, height: 84, borderRadius: radius.md, backgroundColor: c.surface2 },
  newsTitle: { fontFamily: fonts.condBold, fontSize: 22, lineHeight: 26, color: c.text },

  banner: { flexDirection: 'row', alignItems: 'center', borderRadius: radius.xl, padding: space.xl, overflow: 'hidden', minHeight: 140 },
  bannerImg: { position: 'absolute', right: 0, top: 0, bottom: 0, width: '55%' },
  bannerKicker: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 2, color: 'rgba(255,255,255,0.85)' },
  bannerTitle: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.white, textTransform: 'uppercase' },
  bannerSub: { fontFamily: fonts.semibold, fontSize: 15, color: 'rgba(255,255,255,0.85)', marginTop: 0 },
  bannerCta: { flexDirection: 'row', alignItems: 'center', gap: 4, alignSelf: 'flex-start', backgroundColor: c.white, borderRadius: radius.pill, paddingHorizontal: 12, paddingVertical: 8, marginTop: 4 },
  bannerCtaText: { fontFamily: fonts.condBlack, fontSize: 12, letterSpacing: 1, color: c.red },
  bannerIcon: { position: 'absolute', right: space.lg, top: space.lg, width: 56, height: 56, borderRadius: 28, backgroundColor: 'rgba(0,0,0,0.25)', alignItems: 'center', justifyContent: 'center' },

  gw: { flexDirection: 'row', alignItems: 'center', padding: space.md, gap: space.md, backgroundColor: c.surface, borderRadius: radius.lg, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  gwImg: { width: 76, height: 76, borderRadius: radius.md, backgroundColor: c.surface2 },
  gwTitle: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.text, textTransform: 'uppercase' },
  gwPrize: { fontFamily: fonts.semibold, fontSize: 15, color: c.gold },
}));
