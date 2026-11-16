import React, { ReactNode, useRef, useState } from 'react';
import { Animated, Image, ImageSourcePropType, RefreshControl, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { space } from '../theme';
import { createStyles, useTheme } from '../themeContext';
import { Gradient } from './Gradient';
import { Icon } from './Icon';
import { NATIVE } from './motion';

/**
 * Fotoğraflı detay ekranı: kaydırınca fotoğraf yavaş kayar (paralaks), aşağı çekince büyür;
 * üstte geri butonu için koyu perde, altta içeriğe kararan geçiş.
 */
export function ParallaxScreen({
  image, height = 380, overlay, children, onRefresh, testID,
}: {
  image: ImageSourcePropType | null; height?: number; overlay?: ReactNode; children: ReactNode;
  onRefresh?: () => Promise<unknown>; testID?: string;
}) {
  const { c } = useTheme();
  const styles = useStyles();
  const insets = useSafeAreaInsets();
  const y = useRef(new Animated.Value(0)).current;
  const [refreshing, setRefreshing] = useState(false);

  const translateY = y.interpolate({ inputRange: [-height, 0, height], outputRange: [-height / 2, 0, height * 0.45], extrapolateRight: 'clamp' });
  const scale = y.interpolate({ inputRange: [-height, 0], outputRange: [2, 1], extrapolateRight: 'clamp' });

  return (
    <Animated.ScrollView
      testID={testID}
      style={styles.root}
      contentContainerStyle={{ paddingBottom: space.xxxl + insets.bottom }}
      scrollEventThrottle={16}
      showsVerticalScrollIndicator={false}
      onScroll={Animated.event([{ nativeEvent: { contentOffset: { y } } }], { useNativeDriver: NATIVE })}
      refreshControl={onRefresh ? (
        <RefreshControl
          refreshing={refreshing}
          tintColor={c.gold}
          colors={[c.red]}
          progressBackgroundColor={c.surface2}
          onRefresh={async () => {
            setRefreshing(true);
            try { await onRefresh(); } finally { setRefreshing(false); }
          }}
        />
      ) : undefined}>
      <View style={{ height, overflow: 'hidden' }}>
        <Animated.View style={[StyleSheet.absoluteFill, { transform: [{ translateY }, { scale }] }]}>
          {image ? <Image source={image} style={StyleSheet.absoluteFill} resizeMode="cover" /> : (
            // Fotoğraf yoksa kulüp renklerinde degrade + silik basketbol topu
            <View style={StyleSheet.absoluteFill}>
              <Gradient vertical={false} stops={[{ offset: 0, color: '#0B5A31' }, { offset: 0.6, color: '#062414' }, { offset: 1, color: '#3A0000' }]} />
              <View style={styles.ball}><Icon name="ball" size={260} color="rgba(255,255,255,0.06)" strokeWidth={1.2} /></View>
            </View>
          )}
        </Animated.View>
        <Gradient stops={[{ offset: 0, color: '#000', opacity: 0.55 }, { offset: 0.22, color: '#000', opacity: 0 }, { offset: 0.5, color: c.scrim, opacity: 0.35 }, { offset: 1, color: c.scrim, opacity: 0.96 }]} />
        {overlay ? <View style={styles.overlay}>{overlay}</View> : null}
      </View>
      <View style={styles.body}>{children}</View>
    </Animated.ScrollView>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  root: { flex: 1, backgroundColor: c.bg },
  overlay: { position: 'absolute', left: space.lg, right: space.lg, bottom: space.lg },
  body: { paddingHorizontal: space.lg, gap: space.xl },
  ball: { position: 'absolute', right: -60, top: 20 },
}));
