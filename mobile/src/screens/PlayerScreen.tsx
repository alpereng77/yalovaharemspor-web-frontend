import React, { useMemo } from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { usePlayer, usePlayers } from '../api/hooks';
import type { PlayerStats } from '../api/types';
import { Bar, CountUp, FadeIn } from '../components/motion';
import { ParallaxScreen } from '../components/Parallax';
import { ErrorState, Loading } from '../components/ui';
import { pct, stat } from '../lib/format';
import type { RootScreenProps } from '../navigation/types';
import { fonts, radius, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';

type NumKey = 'points' | 'rebounds' | 'assists' | 'steals' | 'blocks' | 'efficiency' | 'minutes';

export function PlayerScreen({ route }: RootScreenProps<'Player'>) {
  const { T } = useTheme();
  const styles = useStyles();
  const { data: p, isPending, error, refetch } = usePlayer(route.params.id);
  const team = usePlayers();

  // Takımın her istatistikteki en iyisi: çubuklar oyuncuyu takıma göre gösterir
  const best = useMemo(() => {
    const out: Partial<Record<NumKey, number>> = {};
    for (const pl of team.data ?? []) {
      for (const k of ['points', 'rebounds', 'assists', 'steals', 'blocks', 'efficiency', 'minutes'] as NumKey[]) {
        out[k] = Math.max(out[k] ?? 0, pl.stats?.[k] ?? 0);
      }
    }
    return out;
  }, [team.data]);

  if (isPending && !p) {
    return <Loading variant="detail" />;
  }
  if (!p) {
    return <ErrorState error={error} onRetry={refetch} />;
  }
  const s = p.stats;
  const facts = [['Mevki', p.position?.replace(/\s*\(\d\)/, '')], ['Boy', p.height], ['Kilo', p.weight], ['Doğum', p.birth_date], ['Uyruk', p.nationality]]
    .filter(([, v]) => !!v) as [string, string][];

  return (
    <ParallaxScreen
      image={{ uri: p.image }}
      alignTop
      height={460}
      onRefresh={refetch}
      testID="player-screen"
      overlay={
        <View style={styles.hero}>
          {p.number ? <Text style={styles.number}>{p.number}</Text> : null}
          <View style={{ flex: 1 }}>
            <Text style={styles.first}>{p.first_name}</Text>
            <Text style={styles.last} numberOfLines={2}>{p.last_name}</Text>
          </View>
        </View>
      }>
      <FadeIn style={styles.facts}>
        {facts.map(([k, v]) => (
          <View key={k} style={styles.fact}>
            <Text style={T.label}>{k}</Text>
            <Text style={styles.factValue}>{v}</Text>
          </View>
        ))}
      </FadeIn>

      {s ? (
        <>
          <FadeIn index={1} style={styles.tiles}>
            {[
              { k: 'points' as const, l: 'SAYI' },
              { k: 'rebounds' as const, l: 'RİBAUND' },
              { k: 'assists' as const, l: 'ASİST' },
            ].map((tile, i) => (
              <View key={tile.k} style={[styles.tile, i === 0 && styles.tileHot]}>
                <CountUp value={s[tile.k]} decimals={1} style={styles.tileValue} />
                <Text style={styles.tileLabel}>{tile.l}</Text>
                <Text style={styles.tileSub}>MAÇ BAŞI</Text>
              </View>
            ))}
          </FadeIn>

          <FadeIn index={2} style={styles.card}>
            <Text style={styles.cardTitle}>Takım içindeki yeri</Text>
            {([
              ['Sayı', 'points'], ['Ribaund', 'rebounds'], ['Asist', 'assists'], ['Top çalma', 'steals'], ['Blok', 'blocks'], ['Verimlilik', 'efficiency'], ['Süre (dk)', 'minutes'],
            ] as [string, NumKey][]).map(([label, k], i) => (
              <StatBar key={k} label={label} value={s[k]} max={best[k] || s[k] || 1} delay={150 + i * 80} />
            ))}
          </FadeIn>

          <FadeIn index={3} style={styles.card}>
            <Text style={styles.cardTitle}>{s.season} sezonu</Text>
            <Grid s={s} />
          </FadeIn>
        </>
      ) : (
        <View style={styles.card}><Text style={T.body}>Bu sezon için henüz istatistik yok.</Text></View>
      )}
    </ParallaxScreen>
  );
}

function StatBar({ label, value, max, delay }: { label: string; value: number; max: number; delay: number }) {
  const { c } = useTheme();
  const styles = useStyles();
  const leader = value > 0 && value >= max;
  return (
    <View style={{ gap: 8 }}>
      <View style={styles.statHead}>
        <Text style={styles.statLabel}>{label}{leader ? '  ·  TAKIM LİDERİ' : ''}</Text>
        <Text style={styles.statValue}>{stat(value)}</Text>
      </View>
      <Bar ratio={value / max} color={leader ? c.gold : c.textSoft} delay={delay} />
    </View>
  );
}

function Grid({ s }: { s: PlayerStats }) {
  const { T } = useTheme();
  const styles = useStyles();
  const cells = [
    ['MAÇ', String(s.games)], ['2 SAYI', pct(s.fg_pct)], ['3 SAYI', pct(s.three_pct)], ['SERBEST', pct(s.ft_pct)],
    ['TOP KAYBI', stat(s.turnovers)], ['FAUL', stat(s.fouls)], ['T. SAYI', String(s.total_points)], ['T. RİBAUND', String(s.total_rebounds)],
  ];
  return (
    <View style={styles.grid}>
      {cells.map(([k, v]) => (
        <View key={k} style={styles.cell}>
          <Text style={styles.cellValue}>{v}</Text>
          <Text style={T.label}>{k}</Text>
        </View>
      ))}
    </View>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  hero: { flexDirection: 'row', alignItems: 'flex-end', gap: space.md },
  number: { fontFamily: fonts.condBlackItalic, fontSize: 40, lineHeight: 42, color: c.redBright },
  first: { fontFamily: fonts.condBold, fontSize: 22, letterSpacing: 1, color: 'rgba(255,255,255,0.8)', textTransform: 'uppercase' },
  last: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.white, textTransform: 'uppercase' },
  facts: { flexDirection: 'row', flexWrap: 'wrap', gap: space.sm, marginTop: space.sm },
  fact: { flexGrow: 1, minWidth: '30%', backgroundColor: c.surface, borderRadius: radius.md, paddingHorizontal: space.md, paddingVertical: 12, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  factValue: { fontFamily: fonts.condBold, fontSize: 22, color: c.text, marginTop: 4 },
  tiles: { flexDirection: 'row', gap: space.sm },
  tile: { flex: 1, alignItems: 'center', backgroundColor: c.surface, borderRadius: radius.lg, paddingVertical: space.lg, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  tileHot: {},
  tileValue: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.text, fontVariant: ['tabular-nums'] },
  tileLabel: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 1.5, color: c.textSoft },
  tileSub: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1, color: c.muted },
  card: { backgroundColor: c.surface, borderRadius: radius.xl, padding: space.lg, gap: space.md, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  cardTitle: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, textTransform: 'uppercase' },
  statHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'baseline' },
  statLabel: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 0.8, color: c.textSoft, textTransform: 'uppercase' },
  statValue: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, fontVariant: ['tabular-nums'] },
  grid: { flexDirection: 'row', flexWrap: 'wrap', rowGap: space.lg },
  cell: { width: '25%', alignItems: 'center' },
  cellValue: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, fontVariant: ['tabular-nums'] },
}));
