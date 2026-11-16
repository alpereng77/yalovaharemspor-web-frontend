import React from 'react';
import { View } from 'react-native';
import { useGiveaways } from '../api/hooks';
import { GiveawayBanner, GiveawayCard } from '../components/cards';
import { FadeIn } from '../components/motion';
import { Empty, ErrorState, Loading, Screen, SectionHeader } from '../components/ui';
import type { TabScreenProps } from '../navigation/types';
import { space } from '../theme';

export function GiveawaysScreen({ navigation }: TabScreenProps<'Giveaways'>) {
  const { data, isPending, error, refetch } = useGiveaways();
  const open = data?.filter(g => g.is_open) ?? [];
  const rest = data?.filter(g => !g.is_open) ?? [];
  const go = (slug: string, title: string) => navigation.navigate('Giveaway', { slug, title });

  return (
    <Screen onRefresh={refetch} testID="giveaways-screen">
      {isPending ? <Loading /> : error && !data ? <ErrorState error={error} onRetry={refetch} />
        : !data?.length ? <Empty icon="gift" title="Şu an aktif çekiliş yok" text="Yeni çekilişler burada duyurulur. Instagram hesabımızı takipte kal!" />
        : (
          <>
            {open.length ? (
              <View style={{ gap: space.md }}>
                {open.map((g, i) => (
                  <FadeIn key={g.slug} index={i}>
                    <GiveawayBanner giveaway={g} onPress={() => go(g.slug, g.title)} />
                  </FadeIn>
                ))}
              </View>
            ) : <Empty icon="gift" title="Şu an aktif çekiliş yok" text="Yeni çekilişler burada duyurulur." />}
            {rest.length ? (
              <View style={{ gap: space.md }}>
                <SectionHeader title="Geçmiş çekilişler" />
                <View style={{ height: space.xs }} />
                {rest.map((g, i) => (
                  <FadeIn key={g.slug} index={open.length + i}>
                    <GiveawayCard giveaway={g} onPress={() => go(g.slug, g.title)} />
                  </FadeIn>
                ))}
              </View>
            ) : null}
          </>
        )}
    </Screen>
  );
}
