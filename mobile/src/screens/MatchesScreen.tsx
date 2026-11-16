import React, { useMemo, useState } from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { useClub, useMatches, useStanding } from '../api/hooks';
import type { Match } from '../api/types';
import { MatchCenter, MatchRow, SeasonStrip } from '../components/cards';
import { FadeIn } from '../components/motion';
import { Empty, ErrorState, Loading, Screen, Segmented } from '../components/ui';
import { trParts } from '../lib/format';
import { fonts, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';

type Tab = 'upcoming' | 'played';
const MONTHS = ['OCAK', 'ŞUBAT', 'MART', 'NİSAN', 'MAYIS', 'HAZİRAN', 'TEMMUZ', 'AĞUSTOS', 'EYLÜL', 'EKİM', 'KASIM', 'ARALIK'];

/** Maçları aya göre gruplar: [{ title: 'KASIM 2026', items }] */
function byMonth(matches: Match[]) {
  const groups: { title: string; items: Match[] }[] = [];
  for (const m of matches) {
    const p = trParts(m.date);
    const title = p ? `${MONTHS[p.month]} ${p.year}` : 'TARİH AÇIKLANACAK';
    const last = groups[groups.length - 1];
    if (last && last.title === title) {
      last.items.push(m);
    } else {
      groups.push({ title, items: [m] });
    }
  }
  return groups;
}

export function MatchesScreen() {
  const { c } = useTheme();
  const styles = useStyles();
  const [tab, setTab] = useState<Tab>('upcoming');
  const matches = useMatches(tab);
  const standing = useStanding();
  const club = useClub();
  const list = useMemo(() => matches.data?.matches ?? [], [matches.data]);
  const next = tab === 'upcoming' ? list[0] : undefined;
  const groups = useMemo(() => byMonth(tab === 'upcoming' ? list.slice(1) : list), [list, tab]);
  const record = matches.data?.record;

  return (
    <Screen onRefresh={() => Promise.all([matches.refetch(), standing.refetch()])} testID="matches-screen">
      <Segmented<Tab>
        value={tab}
        onChange={setTab}
        options={[{ value: 'upcoming', label: 'Fikstür' }, { value: 'played', label: 'Sonuçlar' }]}
      />

      {matches.isPending ? <Loading /> : matches.error && !matches.data ? (
        <ErrorState error={matches.error} onRetry={matches.refetch} />
      ) : list.length === 0 ? (
        <Empty icon="calendar" title={tab === 'upcoming' ? 'Planlanmış maç yok' : 'Henüz oynanmış maç yok'} text="Fikstür açıklandığında burada görünecek." />
      ) : (
        <>
          {tab === 'played' && record ? (
            <FadeIn style={styles.record}>
              <View style={styles.recordItem}><Text style={[styles.recordNum, { color: c.win }]}>{record.won}</Text><Text style={styles.recordLabel}>GALİBİYET</Text></View>
              <View style={styles.recordDivider} />
              <View style={styles.recordItem}><Text style={[styles.recordNum, { color: c.loss }]}>{record.lost}</Text><Text style={styles.recordLabel}>MAĞLUBİYET</Text></View>
            </FadeIn>
          ) : null}
          {tab === 'played' && standing.data ? <FadeIn index={1}><SeasonStrip standing={standing.data} /></FadeIn> : null}
          {next ? <FadeIn><MatchCenter match={next} logo={club.data?.logo} /></FadeIn> : null}

          {groups.map((g, gi) => (
            <View key={g.title}>
              <Text style={styles.month}>{g.title}</Text>
              {g.items.map((m, i) => (
                <FadeIn key={m.id} index={gi * 2 + i}>
                  <MatchRow match={m} />
                </FadeIn>
              ))}
            </View>
          ))}
        </>
      )}
    </Screen>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  record: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: space.xl },
  recordItem: { alignItems: 'center' },
  recordNum: { fontFamily: fonts.condBlackItalic, fontSize: 64, lineHeight: 64 },
  recordLabel: { fontFamily: fonts.condBold, fontSize: 13, letterSpacing: 2, color: c.muted },
  recordDivider: { width: 2, height: 54, backgroundColor: c.lineStrong, transform: [{ rotate: '18deg' }] },
  month: { fontFamily: fonts.condBold, fontSize: 14, letterSpacing: 2.5, color: c.gold, marginTop: space.sm },
}));
