import React, { ReactNode, useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator, Animated, Easing, Image, LayoutChangeEvent, Linking, Pressable, RefreshControl, ScrollView,
  StyleProp, StyleSheet, Text, View, ViewStyle,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { ApiError } from '../api/client';
import { fonts, radius, space, Palette } from '../theme';
import { createStyles, useTheme } from '../themeContext';
import { Icon, IconName } from './Icon';
import { NATIVE, PressableScale, Skeleton } from './motion';

/** Aşağı çekip yenilemeli kaydırılabilir ekran gövdesi */
export function Screen({
  children, onRefresh, contentStyle, testID, padded = true,
}: { children: ReactNode; onRefresh?: () => Promise<unknown>; contentStyle?: StyleProp<ViewStyle>; testID?: string; padded?: boolean }) {
  const { c } = useTheme();
  const styles = useStyles();
  const insets = useSafeAreaInsets();
  const [refreshing, setRefreshing] = useState(false);
  const refresh = onRefresh
    ? async () => {
        setRefreshing(true);
        try {
          await onRefresh();
        } finally {
          setRefreshing(false);
        }
      }
    : undefined;
  return (
    <ScrollView
      testID={testID}
      style={styles.screen}
      contentContainerStyle={[padded && styles.content, { paddingBottom: space.xxxl + insets.bottom }, contentStyle]}
      refreshControl={refresh ? <RefreshControl refreshing={refreshing} onRefresh={refresh} tintColor={c.gold} colors={[c.red]} progressBackgroundColor={c.surface2} /> : undefined}
      keyboardShouldPersistTaps="handled"
      showsVerticalScrollIndicator={false}>
      {children}
    </ScrollView>
  );
}

export function Card({ children, style, onPress, accessibilityLabel, testID }: {
  children: ReactNode; style?: StyleProp<ViewStyle>; onPress?: () => void; accessibilityLabel?: string; testID?: string;
}) {
  const styles = useStyles();
  if (onPress) {
    return <PressableScale onPress={onPress} accessibilityLabel={accessibilityLabel} testID={testID} style={[styles.card, style]}>{children}</PressableScale>;
  }
  return <View style={[styles.card, style]} testID={testID}>{children}</View>;
}

/** Bölüm başlığı: kırmızı dikey çizgi + büyük harf dar başlık + sağda "Tümü" */
export function SectionHeader({ title, action, onAction, note, style }: { title: string; action?: string; onAction?: () => void; note?: string; style?: StyleProp<ViewStyle> }) {
  const { c } = useTheme();
  const styles = useStyles();
  return (
    <View style={[styles.sectionHeader, style]}>
      <View style={styles.sectionLeft}>
        <Text style={styles.sectionTitle} accessibilityRole="header">{title}</Text>
      </View>
      {note ? <Text style={styles.sectionNote}>{note}</Text> : null}
      {action && onAction ? (
        <Pressable onPress={onAction} hitSlop={12} accessibilityRole="button" style={styles.sectionAction}>
          <Text style={styles.sectionActionText}>{action}</Text>
          <Icon name="chevron" size={14} color={c.gold} strokeWidth={2.6} />
        </Pressable>
      ) : null}
    </View>
  );
}

type PillTone = 'green' | 'red' | 'gold' | 'gray';
const pillColors = (c: Palette): Record<PillTone, { bg: string; fg: string }> => ({
  green: { bg: c.mode === 'dark' ? 'rgba(22,194,106,0.14)' : 'rgba(14,159,85,0.12)', fg: c.greenGlow },
  red: { bg: c.mode === 'dark' ? 'rgba(224,27,27,0.16)' : 'rgba(200,14,14,0.1)', fg: c.mode === 'dark' ? '#FF6B6B' : c.redBright },
  gold: { bg: c.goldSoft, fg: c.gold },
  gray: { bg: c.mode === 'dark' ? 'rgba(255,255,255,0.07)' : 'rgba(5,16,10,0.06)', fg: c.muted },
});
export function Pill({ text, tone = 'green', live }: { text: string; tone?: PillTone; live?: boolean }) {
  const styles = useStyles();
  const { c } = useTheme();
  const pc = pillColors(c)[tone];
  return (
    <View style={[styles.pill, { backgroundColor: pc.bg }]}>
      {live ? <LiveDot color={pc.fg} /> : null}
      <Text style={[styles.pillText, { color: pc.fg }]}>{text}</Text>
    </View>
  );
}

function LiveDot({ color }: { color: string }) {
  const styles = useStyles();
  const v = useRef(new Animated.Value(1)).current;
  useEffect(() => {
    const loop = Animated.loop(Animated.sequence([
      Animated.timing(v, { toValue: 0.25, duration: 650, useNativeDriver: NATIVE }),
      Animated.timing(v, { toValue: 1, duration: 650, useNativeDriver: NATIVE }),
    ]));
    loop.start();
    return () => loop.stop();
  }, [v]);
  return <Animated.View style={[styles.dot, { backgroundColor: color, opacity: v }]} />;
}

export function Button({
  title, onPress, variant = 'primary', icon, loading, disabled, style, testID,
}: {
  title: string; onPress: () => void; variant?: 'primary' | 'secondary' | 'ghost' | 'gold'; icon?: IconName;
  loading?: boolean; disabled?: boolean; style?: StyleProp<ViewStyle>; testID?: string;
}) {
  const { c } = useTheme();
  const styles = useStyles();
  const fg = variant === 'gold' ? '#1A1205' : variant === 'ghost' || variant === 'secondary' ? c.text : c.white;
  return (
    <PressableScale
      testID={testID}
      onPress={onPress}
      disabled={disabled || loading}
      accessibilityLabel={title}
      style={[
        styles.button,
        variant === 'primary' && styles.buttonPrimary,
        variant === 'secondary' && styles.buttonSecondary,
        variant === 'ghost' && styles.buttonGhost,
        variant === 'gold' && styles.buttonGold,
        (disabled || loading) && styles.buttonDisabled,
        style,
      ]}>
      {loading ? <ActivityIndicator color={fg} /> : (
        <>
          {icon ? <Icon name={icon} size={18} color={fg} strokeWidth={2.4} /> : null}
          <Text style={[styles.buttonText, { color: fg }]}>{title}</Text>
        </>
      )}
    </PressableScale>
  );
}

/** Takım arması: kendi takımımız için logo, rakip için baş harfli rozet */
/**
 * Takım arması — sadece GERÇEK logo gösterilir:
 *  • kendi takımımız: kulüp logosu,
 *  • rakip: TBF'den alınıp sitede saklanan resmi logo (beyaz disk içinde, her zeminde okunur),
 *  • logo yoksa hiçbir şey uydurulmaz: sade düz daire içinde silik basketbol topu.
 * onDark: koyu yeşil kartın (maç merkezi) içinde her iki temada da koyu stil.
 */
export function TeamBadge({ name, ours, logo, opponentLogo, size = 44, onDark }: {
  name: string; ours?: boolean; logo?: string | number | null; opponentLogo?: string | null; size?: number; onDark?: boolean;
}) {
  const { c } = useTheme();
  const styles = useStyles();
  if (ours && logo) {
    return <Image source={typeof logo === 'string' ? { uri: logo } : logo} style={{ width: size, height: size, borderRadius: size / 2 }} accessibilityLabel={`${name} logosu`} />;
  }
  if (!ours && opponentLogo) {
    return (
      <View style={[styles.logoDisc, { width: size, height: size, borderRadius: size / 2, padding: size * 0.12 }]}>
        <Image source={{ uri: opponentLogo }} style={styles.logoImg} resizeMode="contain" accessibilityLabel={`${name} logosu`} />
      </View>
    );
  }
  const fill = onDark ? 'rgba(255,255,255,0.06)' : c.surface2;
  return (
    <View
      style={[styles.noLogo, { width: size, height: size, borderRadius: size / 2, backgroundColor: fill }]}
      accessibilityLabel={`${name}: logo yok`}>
      <Icon name="ball" size={size * 0.4} color={onDark ? 'rgba(255,255,255,0.3)' : c.faint} strokeWidth={1.5} />
    </View>
  );
}

/** Dış bağlantı açma (Instagram, harita, telefon). Açılamazsa sessizce geçer. */
/**
 * "Uygulamayı hazırlayan" satırı. Ad ve bağlantı site ayarlarından gelir (admin panelinden değiştirilebilir);
 * ayar boşsa varsayılan değerler kullanılır. Dokununca hazırlayanın sitesi açılır.
 */
export function MadeBy({ name, url }: { name?: string | null; url?: string | null }) {
  const { c } = useTheme();
  const who = name?.trim() || 'alperengundogdu';
  const link = url?.trim() || 'https://alperengundogdu.com.tr';
  return (
    <Pressable onPress={() => openUrl(link)} hitSlop={12} accessibilityRole="link" accessibilityLabel={`Uygulamayı hazırlayan: ${who}`} testID="made-by"
      style={{ alignSelf: 'center', paddingVertical: space.sm }}>
      <Text style={{ fontFamily: fonts.regular, fontSize: 12, lineHeight: 16, color: c.muted, textAlign: 'center' }}>
        Uygulamayı hazırlayan <Text style={{ fontFamily: fonts.semibold, color: c.textSoft }}>{who}</Text>
      </Text>
    </Pressable>
  );
}

export function openUrl(url: string | null | undefined) {
  if (url) {
    Linking.openURL(url).catch(() => undefined);
  }
}

/** Liste ekranları için iskelet yükleyici */
export function Loading({ variant = 'list' }: { variant?: 'list' | 'detail' | 'grid' }) {
  const styles = useStyles();
  if (variant === 'detail') {
    return (
      <View style={[styles.screen, { padding: space.lg, gap: space.lg }]} accessibilityLabel="Yükleniyor">
        <Skeleton height={260} r={radius.xl} />
        <Skeleton width="40%" height={14} />
        <Skeleton width="80%" height={34} />
        <Skeleton height={90} r={radius.lg} />
        <Skeleton height={140} r={radius.lg} />
      </View>
    );
  }
  if (variant === 'grid') {
    return (
      <View style={styles.skelGrid} accessibilityLabel="Yükleniyor">
        {[0, 1, 2, 3].map(i => <Skeleton key={i} width="48%" height={230} r={radius.lg} />)}
      </View>
    );
  }
  return (
    <View style={{ gap: space.md }} accessibilityLabel="Yükleniyor">
      {[0, 1, 2, 3, 4].map(i => <Skeleton key={i} height={82} r={radius.lg} />)}
    </View>
  );
}

export function ErrorState({ error, onRetry }: { error: unknown; onRetry?: () => void }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const message = error instanceof ApiError ? error.message : 'Bir şeyler ters gitti. Tekrar dene.';
  const offline = error instanceof ApiError && error.isNetwork;
  return (
    <View style={styles.state} accessibilityRole="alert">
      <View style={styles.stateIcon}><Icon name={offline ? 'globe' : 'alert'} size={30} color={c.gold} /></View>
      <Text style={[T.h2, { marginTop: space.lg, textAlign: 'center' }]}>{offline ? 'Bağlantı yok' : 'Yüklenemedi'}</Text>
      <Text style={[T.small, styles.stateText]}>{message}</Text>
      {onRetry ? <Button title="Tekrar dene" icon="refresh" variant="secondary" onPress={onRetry} style={{ marginTop: space.xl, alignSelf: 'center' }} /> : null}
    </View>
  );
}

export function Empty({ title, text, icon = 'info' }: { title: string; text?: string; icon?: IconName }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  return (
    <View style={styles.state}>
      <View style={styles.stateIcon}><Icon name={icon} size={30} color={c.muted} /></View>
      <Text style={[T.h3, { marginTop: space.lg, textAlign: 'center' }]}>{title}</Text>
      {text ? <Text style={[T.small, styles.stateText]}>{text}</Text> : null}
    </View>
  );
}

/** Göstergesi seçilen sekmeye kayan seçim çubuğu */
export function Segmented<T extends string>({ value, options, onChange }: {
  value: T; options: { value: T; label: string }[]; onChange: (v: T) => void;
}) {
  const styles = useStyles();
  const [width, setWidth] = useState(0);
  const index = Math.max(0, options.findIndex(o => o.value === value));
  const x = useRef(new Animated.Value(index)).current;
  useEffect(() => {
    Animated.spring(x, { toValue: index, useNativeDriver: NATIVE, speed: 18, bounciness: 6 }).start();
  }, [x, index]);
  const segW = width ? (width - 8) / options.length : 0;
  return (
    <View style={styles.segmented} accessibilityRole="tablist" onLayout={(e: LayoutChangeEvent) => setWidth(e.nativeEvent.layout.width)}>
      {segW ? (
        <Animated.View
          style={[styles.segIndicator, { width: segW, transform: [{ translateX: x.interpolate({ inputRange: [0, 1], outputRange: [0, segW] }) }] }]}
        />
      ) : null}
      {options.map(o => {
        const active = o.value === value;
        return (
          <Pressable
            key={o.value}
            onPress={() => onChange(o.value)}
            accessibilityRole="tab"
            accessibilityState={{ selected: active }}
            style={styles.segment}>
            <Text style={[styles.segmentText, active && styles.segmentTextActive]}>{o.label}</Text>
          </Pressable>
        );
      })}
    </View>
  );
}

/** Ekranlar arası yumuşak geçişlerde kullanılan kısa animasyon süresi */
export const ease = Easing.out(Easing.cubic);

const useStyles = createStyles((c) => StyleSheet.create({
  screen: { flex: 1, backgroundColor: c.bg },
  content: { padding: space.lg, paddingTop: space.xl, gap: space.xxl },
  card: { backgroundColor: c.surface, borderRadius: radius.lg, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line, overflow: 'hidden' },
  sectionHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: space.lg },
  sectionNote: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.muted },
  sectionLeft: { flexDirection: 'row', alignItems: 'center', gap: space.sm },
  sectionTitle: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, textTransform: 'uppercase', letterSpacing: 0.3 },
  sectionAction: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  sectionActionText: { fontFamily: fonts.condBold, fontSize: 15, color: c.gold, letterSpacing: 1, textTransform: 'uppercase' },
  pill: { flexDirection: 'row', alignItems: 'center', alignSelf: 'flex-start', gap: 8, paddingHorizontal: 12, paddingVertical: 4, borderRadius: radius.pill },
  dot: { width: 7, height: 7, borderRadius: 4 },
  pillText: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1, textTransform: 'uppercase' },
  button: { minHeight: 52, borderRadius: radius.md, paddingHorizontal: space.xl, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: space.sm },
  buttonPrimary: { backgroundColor: c.red },
  buttonSecondary: { backgroundColor: c.surface2, borderWidth: 1, borderColor: c.lineStrong },
  buttonGhost: { backgroundColor: 'transparent', borderWidth: 1, borderColor: c.lineStrong },
  buttonGold: { backgroundColor: c.gold },
  buttonDisabled: { opacity: 0.45 },
  buttonText: { fontFamily: fonts.condBlack, fontSize: 22, letterSpacing: 1, textTransform: 'uppercase' },
  logoDisc: { backgroundColor: '#FFFFFF', alignItems: 'center', justifyContent: 'center', borderWidth: StyleSheet.hairlineWidth, borderColor: c.lineStrong },
  logoImg: { width: '100%', height: '100%' },
  noLogo: { alignItems: 'center', justifyContent: 'center' },
  state: { alignItems: 'center', justifyContent: 'center', paddingVertical: 64, paddingHorizontal: space.xl },
  stateIcon: { width: 68, height: 68, borderRadius: 34, backgroundColor: c.surface2, alignItems: 'center', justifyContent: 'center' },
  stateText: { textAlign: 'center', marginTop: space.xs, maxWidth: 300 },
  skelGrid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', rowGap: space.md },
  segmented: { flexDirection: 'row', backgroundColor: c.surface, borderRadius: radius.md, padding: 4, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  segIndicator: { position: 'absolute', top: 4, bottom: 4, left: 4, borderRadius: radius.sm, backgroundColor: c.surface3 },
  segment: { flex: 1, paddingVertical: 12, alignItems: 'center' },
  segmentText: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 1, textTransform: 'uppercase', color: c.muted },
  segmentTextActive: { color: c.text },
}));
