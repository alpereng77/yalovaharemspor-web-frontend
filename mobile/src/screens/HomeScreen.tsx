import React, { useRef, useState } from 'react';
import { Animated, Image, Pressable, RefreshControl, ScrollView, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useHome, usePlayers } from '../api/hooks';
import logo from '../assets/logo.jpg';
import { GiveawayBanner, MatchCenter, NewsFeature, NewsRow, PlayerCard, ResultCard, SeasonStrip } from '../components/cards';
import { Gradient } from '../components/Gradient';
import { Icon } from '../components/Icon';
import { FadeIn, NATIVE, Skeleton } from '../components/motion';
import { ErrorState, MadeBy, openUrl, SectionHeader } from '../components/ui';
import type { TabScreenProps } from '../navigation/types';
import { fonts, radius, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';

const HERO = 430;

export function HomeScreen({ navigation }: TabScreenProps<'Home'>) {
  const { c, toggle } = useTheme();
  const styles = useStyles();
  const insets = useSafeAreaInsets();
  const { data, isPending, error, refetch } = useHome();
  const players = usePlayers();
  const y = useRef(new Animated.Value(0)).current;
  const [refreshing, setRefreshing] = useState(false);
  const club = data?.club;
  const openGiveaways = data?.giveaways.filter(g => g.is_open) ?? [];

  // Kapak: paralaks + aşağı çekince büyüme; kaydırınca üstte kompakt başlık belirir
  const heroY = y.interpolate({ inputRange: [-HERO, 0, HERO], outputRange: [-HERO / 2, 0, HERO * 0.5], extrapolateRight: 'clamp' });
  const heroScale = y.interpolate({ inputRange: [-HERO, 0], outputRange: [2, 1], extrapolateRight: 'clamp' });
  const heroText = y.interpolate({ inputRange: [0, HERO * 0.5], outputRange: [1, 0], extrapolate: 'clamp' });
  const barOpacity = y.interpolate({ inputRange: [HERO * 0.55, HERO * 0.8], outputRange: [0, 1], extrapolate: 'clamp' });

  if (error && !data) {
    return <View style={styles.root}><View style={{ height: insets.top }} /><ErrorState error={error} onRetry={refetch} /></View>;
  }

  return (
    <View style={styles.root} testID="home-screen">
      <Animated.ScrollView
        scrollEventThrottle={16}
        showsVerticalScrollIndicator={false}
        onScroll={Animated.event([{ nativeEvent: { contentOffset: { y } } }], { useNativeDriver: NATIVE })}
        contentContainerStyle={{ paddingBottom: space.xxxl }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            tintColor={c.gold}
            colors={[c.red]}
            progressBackgroundColor={c.surface2}
            progressViewOffset={insets.top}
            onRefresh={async () => {
              setRefreshing(true);
              try { await Promise.all([refetch(), players.refetch()]); } finally { setRefreshing(false); }
            }}
          />
        }>
        {/* KAPAK */}
        <View style={{ height: HERO, overflow: 'hidden' }}>
          <Animated.View style={[StyleSheet.absoluteFill, { transform: [{ translateY: heroY }, { scale: heroScale }] }]}>
            {club?.hero_image ? <Image source={{ uri: club.hero_image }} style={StyleSheet.absoluteFill} resizeMode="cover" /> : <View style={[StyleSheet.absoluteFill, { backgroundColor: c.green }]} />}
          </Animated.View>
          <Gradient stops={[{ offset: 0, color: '#000', opacity: 0.6 }, { offset: 0.3, color: c.scrim, opacity: 0.15 }, { offset: 0.7, color: c.scrim, opacity: 0.75 }, { offset: 1, color: c.scrim, opacity: 1 }]} />

          <View style={[styles.topBar, { paddingTop: insets.top + space.sm }]}>
            <View style={styles.brand}>
              <Image source={club?.logo ? { uri: club.logo } : logo} style={styles.logo} />
              <View>
                <Text style={styles.city}>{club?.city_name ?? 'YALOVA'}</Text>
                <Text style={styles.clubName}>{club?.club_name ?? 'HAREMSPOR'}</Text>
              </View>
            </View>
            <View style={styles.topActions}>
            <Pressable onPress={toggle} hitSlop={10} accessibilityRole="button" accessibilityLabel={c.mode === 'dark' ? 'Açık temaya geç' : 'Koyu temaya geç'} style={[styles.glassBtn, styles.iconBtn]} testID="theme-toggle">
              <Icon name={c.mode === 'dark' ? 'sun' : 'moon'} size={17} color="#FFFFFF" />
            </Pressable>
            <Pressable onPress={() => navigation.navigate('Club')} hitSlop={10} accessibilityRole="button" accessibilityLabel="Kulüp bilgileri" style={styles.glassBtn}>
              <Icon name="shield" size={16} color={c.white} />
              <Text style={styles.glassText}>KULÜP</Text>
            </Pressable>
            </View>
          </View>

          <Animated.View style={[styles.heroCopy, { opacity: heroText, transform: [{ translateY: heroText.interpolate({ inputRange: [0, 1], outputRange: [-20, 0] }) }] }]}>
            {club?.footer_hashtag ? <Text style={styles.hashtag}>{club.footer_hashtag}</Text> : null}
            <Text style={styles.heroTitle}>{club?.hero_title_1 ?? 'AYNI TUTKU.'}</Text>
            <Text style={[styles.heroTitle, { color: c.redBright }]}>{club?.hero_title_2 ?? 'YENİ HEYECAN.'}</Text>
          </Animated.View>
        </View>

        <View style={styles.content}>
          {isPending ? (
            <>
              <Skeleton height={330} r={radius.xl} />
              <Skeleton height={128} r={radius.xl} />
              <Skeleton height={120} r={radius.lg} />
            </>
          ) : data ? (
            <>
              {data.next_match ? (
                <FadeIn index={0}>
                  <MatchCenter match={data.next_match} logo={club?.logo} onPress={() => navigation.navigate('Matches')} />
                </FadeIn>
              ) : null}

              {openGiveaways.map((g, i) => (
                <FadeIn key={g.slug} index={1 + i}>
                  <GiveawayBanner giveaway={g} onPress={() => navigation.navigate('Giveaway', { slug: g.slug, title: g.title })} />
                </FadeIn>
              ))}

              {data.standing ? (
                <FadeIn index={2}>
                  <SectionHeader title="Sezon" note={data.standing.season} />
                  <SeasonStrip standing={data.standing} />
                </FadeIn>
              ) : null}

              {data.recent_results.length ? (
                <FadeIn index={3}>
                  <SectionHeader title="Son sonuçlar" action="Tümü" onAction={() => navigation.navigate('Matches')} />
                  <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.hList} style={styles.hScroll}>
                    {data.recent_results.map(m => <ResultCard key={m.id} match={m} />)}
                  </ScrollView>
                </FadeIn>
              ) : null}

              {players.data?.length ? (
                <FadeIn index={4}>
                  <SectionHeader title="Kadro" action="Tümü" onAction={() => navigation.navigate('Squad')} />
                  <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.hList} style={styles.hScroll} decelerationRate="fast" snapToInterval={162}>
                    {players.data.map(p => (
                      <PlayerCard key={p.id} player={p} width={150} onPress={() => navigation.navigate('Player', { id: p.id, name: p.name })} />
                    ))}
                  </ScrollView>
                </FadeIn>
              ) : null}

              {data.news.length ? (
                <FadeIn index={5} style={{ gap: space.lg }}>
                  <SectionHeader title="Haberler" action="Tümü" onAction={() => navigation.navigate('News')} />
                  <NewsFeature article={data.news[0]} onPress={() => navigation.navigate('Article', { id: data.news[0].id })} />
                  {data.news.slice(1, 4).map(a => (
                    <NewsRow key={a.id} article={a} onPress={() => navigation.navigate('Article', { id: a.id })} />
                  ))}
                </FadeIn>
              ) : null}

              <FadeIn index={6} style={styles.socials}>
                {([['instagram', club?.social_instagram], ['x', club?.social_x], ['youtube', club?.social_youtube]] as const)
                  .filter(([, url]) => !!url)
                  .map(([icon, url]) => (
                    <Pressable key={icon} onPress={() => openUrl(url)} accessibilityRole="link" accessibilityLabel={icon} style={styles.social}>
                      <Icon name={icon} size={20} color={c.textSoft} />
                    </Pressable>
                  ))}
              </FadeIn>
              <MadeBy name={club?.footer_credit_name} url={club?.footer_credit_url} />
            </>
          ) : null}
        </View>
      </Animated.ScrollView>

      {/* Kaydırınca beliren kompakt üst çubuk */}
      <Animated.View pointerEvents="none" style={[styles.stickyBar, { paddingTop: insets.top + 6, opacity: barOpacity }]}>
        <Image source={club?.logo ? { uri: club.logo } : logo} style={styles.stickyLogo} />
        <Text style={styles.stickyTitle}>{club?.city_name ?? 'YALOVA'} {club?.club_name ?? 'HAREMSPOR'}</Text>
      </Animated.View>
    </View>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  root: { flex: 1, backgroundColor: c.bg },
  topBar: { position: 'absolute', left: 0, right: 0, top: 0, paddingHorizontal: space.lg, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  brand: { flexDirection: 'row', alignItems: 'center', gap: space.md },
  logo: { width: 46, height: 46, borderRadius: 23, borderWidth: 2, borderColor: 'rgba(255,255,255,0.85)' },
  city: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 3, color: 'rgba(255,255,255,0.8)' },
  clubName: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.white, letterSpacing: 0.5 },
  topActions: { flexDirection: 'row', alignItems: 'center', gap: space.sm },
  iconBtn: { paddingHorizontal: 8 },
  glassBtn: { flexDirection: 'row', alignItems: 'center', gap: 8, backgroundColor: 'rgba(0,0,0,0.35)', borderWidth: 1, borderColor: 'rgba(255,255,255,0.25)', paddingHorizontal: 12, paddingVertical: 8, borderRadius: radius.pill },
  glassText: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.white },
  heroCopy: { position: 'absolute', left: space.lg, right: space.lg, bottom: 28 },
  hashtag: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 1.5, color: c.gold, marginBottom: 8 },
  heroTitle: { fontFamily: fonts.condBlackItalic, fontSize: 40, lineHeight: 42, color: c.white, textTransform: 'uppercase', letterSpacing: -0.5 },
  content: { paddingHorizontal: space.lg, gap: 48, marginTop: space.xl },
  hScroll: { marginHorizontal: -space.lg },
  hList: { paddingHorizontal: space.lg, gap: space.xl },
  socials: { flexDirection: 'row', justifyContent: 'center', gap: space.md, marginTop: space.sm },
  social: { width: 46, height: 46, borderRadius: 23, backgroundColor: c.surface, borderWidth: StyleSheet.hairlineWidth, borderColor: c.lineStrong, alignItems: 'center', justifyContent: 'center' },
  stickyBar: { position: 'absolute', left: 0, right: 0, top: 0, flexDirection: 'row', alignItems: 'center', gap: space.sm, paddingHorizontal: space.lg, paddingBottom: space.sm, backgroundColor: c.mode === 'dark' ? 'rgba(5,16,10,0.96)' : 'rgba(255,255,255,0.97)', borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: c.line },
  stickyLogo: { width: 30, height: 30, borderRadius: 15 },
  stickyTitle: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, letterSpacing: 0.5 },
}));
