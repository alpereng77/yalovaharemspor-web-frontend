import React, { useMemo, useState } from 'react';
import { Image, StyleSheet, Text, View } from 'react-native';
import { usePlayers, useStaff } from '../api/hooks';
import type { Player } from '../api/types';
import { PlayerCard } from '../components/cards';
import { Gradient } from '../components/Gradient';
import { FadeIn } from '../components/motion';
import { Empty, ErrorState, Loading, Screen, Segmented } from '../components/ui';
import type { TabScreenProps } from '../navigation/types';
import { fonts, radius, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';

type Tab = 'players' | 'staff';

function pairs<T>(items: T[]): [T, T | undefined][] {
  const out: [T, T | undefined][] = [];
  for (let i = 0; i < items.length; i += 2) {
    out.push([items[i], items[i + 1]]);
  }
  return out;
}

export function SquadScreen({ navigation }: TabScreenProps<'Squad'>) {
  const { c } = useTheme();
  const styles = useStyles();
  const [tab, setTab] = useState<Tab>('players');
  const players = usePlayers();
  const staff = useStaff();
  const open = (p: Player) => navigation.navigate('Player', { id: p.id, name: p.name });
  // Forma numarasına göre sıralı kadro
  const sorted = useMemo(
    () => [...(players.data ?? [])].sort((a, b) => (parseInt(a.number ?? '999', 10) || 999) - (parseInt(b.number ?? '999', 10) || 999)),
    [players.data],
  );

  return (
    <Screen onRefresh={() => Promise.all([players.refetch(), staff.refetch()])} testID="squad-screen">
      <Segmented<Tab> value={tab} onChange={setTab} options={[{ value: 'players', label: 'Oyuncular' }, { value: 'staff', label: 'Teknik Kadro' }]} />

      {tab === 'players' ? (
        players.isPending ? <Loading variant="grid" /> : players.error && !players.data ? <ErrorState error={players.error} onRetry={players.refetch} />
        : !sorted.length ? <Empty icon="users" title="Kadro henüz açıklanmadı" />
        : (
          <View style={{ gap: space.md }}>
            {pairs(sorted).map(([a, b], i) => (
              <FadeIn key={a.id} index={i} style={styles.pair}>
                <PlayerCard player={a} onPress={() => open(a)} />
                {b ? <PlayerCard player={b} onPress={() => open(b)} /> : <View style={styles.flex} />}
              </FadeIn>
            ))}
          </View>
        )
      ) : staff.isPending ? <Loading /> : staff.error && !staff.data ? <ErrorState error={staff.error} onRetry={staff.refetch} />
        : !staff.data?.length ? <Empty icon="users" title="Teknik kadro bilgisi yok" />
        : (
          <View style={{ gap: space.md }}>
            {staff.data.map((s, i) => (
              <FadeIn key={s.id} index={i} style={styles.staff}>
                <View style={styles.staffImgWrap}>
                  <Image source={{ uri: s.image }} style={StyleSheet.absoluteFill} resizeMode="cover" />
                  <Gradient vertical={false} stops={[{ offset: 0.5, color: c.surface, opacity: 0 }, { offset: 1, color: c.surface, opacity: 1 }]} />
                </View>
                <View style={styles.staffInfo}>
                  <Text style={styles.staffRole}>{s.role}</Text>
                  <Text style={styles.staffName}>{s.name}</Text>
                  {s.nationality ? <Text style={styles.staffNat}>{s.nationality}</Text> : null}
                </View>
              </FadeIn>
            ))}
          </View>
        )}
    </Screen>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  flex: { flex: 1 },
  pair: { flexDirection: 'row', gap: space.md },
  staff: { flexDirection: 'row', alignItems: 'center', backgroundColor: c.surface, borderRadius: radius.lg, overflow: 'hidden', borderWidth: StyleSheet.hairlineWidth, borderColor: c.line, height: 112 },
  staffImgWrap: { width: 110, height: '100%', backgroundColor: c.surface2 },
  staffInfo: { flex: 1, paddingHorizontal: space.md, gap: 2 },
  staffRole: { fontFamily: fonts.condBold, fontSize: 13, letterSpacing: 1.5, color: c.redBright, textTransform: 'uppercase' },
  staffName: { fontFamily: fonts.condBlack, fontSize: 24, lineHeight: 26, color: c.text, textTransform: 'uppercase' },
  staffNat: { fontFamily: fonts.medium, fontSize: 13, color: c.muted },
}));
