import React, { ReactNode, useEffect, useRef, useState } from 'react';
import {
  AccessibilityRole, Animated, Easing, GestureResponderEvent, Platform, Pressable, StyleProp, StyleSheet, View, ViewStyle,
} from 'react-native';
import { radius } from '../theme';
import { useTheme } from '../themeContext';

/** Web'de yerel sürücü yok; aynı kod iki ortamda da uyarısız çalışsın */
export const NATIVE = Platform.OS !== 'web';

/** Basınca hafifçe küçülen dokunulabilir yüzey (kartlar, butonlar) */
export function PressableScale({
  children, onPress, style, scaleTo = 0.965, disabled, accessibilityLabel, accessibilityRole = 'button', testID, hitSlop,
}: {
  children: ReactNode; onPress?: (e: GestureResponderEvent) => void; style?: StyleProp<ViewStyle>; scaleTo?: number;
  disabled?: boolean; accessibilityLabel?: string; accessibilityRole?: AccessibilityRole; testID?: string; hitSlop?: number;
}) {
  const scale = useRef(new Animated.Value(1)).current;
  const to = (v: number) => Animated.spring(scale, { toValue: v, useNativeDriver: NATIVE, speed: 40, bounciness: v === 1 ? 6 : 0 }).start();
  // Yerleşim (genişlik, esneme, kenar boşluğu) dış dokunma alanına; görünüm iç katmana.
  // Böylece "%31 genişlik" ya da "flex: 1" gibi değerler üst kaba göre doğru hesaplanır.
  const { outer, inner } = splitLayout(style);
  return (
    <Pressable
      testID={testID}
      onPress={onPress}
      onPressIn={() => to(scaleTo)}
      onPressOut={() => to(1)}
      disabled={disabled}
      hitSlop={hitSlop}
      style={outer}
      accessibilityRole={accessibilityRole}
      accessibilityLabel={accessibilityLabel}
      accessibilityState={{ disabled: !!disabled }}>
      <Animated.View style={[inner, { transform: [{ scale }] }]}>{children}</Animated.View>
    </Pressable>
  );
}

const LAYOUT_KEYS = [
  'flex', 'flexGrow', 'flexShrink', 'flexBasis', 'width', 'minWidth', 'maxWidth', 'alignSelf',
  'margin', 'marginTop', 'marginBottom', 'marginLeft', 'marginRight', 'marginHorizontal', 'marginVertical',
] as const;

function splitLayout(style: StyleProp<ViewStyle>) {
  const flat = (StyleSheet.flatten(style) ?? {}) as Record<string, unknown>;
  const outer: Record<string, unknown> = {};
  const inner: Record<string, unknown> = {};
  for (const [k, v] of Object.entries(flat)) {
    ((LAYOUT_KEYS as readonly string[]).includes(k) ? outer : inner)[k] = v;
  }
  // Dış kap genişlik/esneme aldıysa iç katman onu doldurur
  if ('flex' in outer || 'flexGrow' in outer || 'width' in outer || 'flexBasis' in outer) {
    inner.flexGrow = 1;
  }
  return { outer: outer as ViewStyle, inner: inner as ViewStyle };
}

/** Ekrana girerken aşağıdan yukarı süzülerek beliren içerik; index ile sırayla */
export function FadeIn({ children, index = 0, delay = 0, distance = 18, style }: {
  children: ReactNode; index?: number; delay?: number; distance?: number; style?: StyleProp<ViewStyle>;
}) {
  const v = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.timing(v, {
      toValue: 1, duration: 520, delay: delay + Math.min(index, 10) * 70,
      easing: Easing.out(Easing.cubic), useNativeDriver: NATIVE,
    }).start();
  }, [v, index, delay]);
  return (
    <Animated.View style={[style, { opacity: v, transform: [{ translateY: v.interpolate({ inputRange: [0, 1], outputRange: [distance, 0] }) }] }]}>
      {children}
    </Animated.View>
  );
}

/** Yüklenirken içerik yerine parıldayan gri bloklar */
export function Skeleton({ width = '100%', height = 16, r = radius.md, style }: {
  width?: number | `${number}%`; height?: number; r?: number; style?: StyleProp<ViewStyle>;
}) {
  const { c } = useTheme();
  const v = useRef(new Animated.Value(0.35)).current;
  useEffect(() => {
    const loop = Animated.loop(Animated.sequence([
      Animated.timing(v, { toValue: 0.75, duration: 750, easing: Easing.inOut(Easing.quad), useNativeDriver: NATIVE }),
      Animated.timing(v, { toValue: 0.35, duration: 750, easing: Easing.inOut(Easing.quad), useNativeDriver: NATIVE }),
    ]));
    loop.start();
    return () => loop.stop();
  }, [v]);
  return <Animated.View style={[{ width, height, borderRadius: r, backgroundColor: c.surface3, opacity: v }, style]} />;
}

/** Canlılık hissi veren atan nokta (katılım açık, sıradaki maç) */
export function Pulse({ color: colorProp, size = 8 }: { color?: string; size?: number }) {
  const { c } = useTheme();
  const color = colorProp ?? c.greenGlow;
  const v = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    const loop = Animated.loop(Animated.timing(v, { toValue: 1, duration: 1400, easing: Easing.out(Easing.quad), useNativeDriver: NATIVE }));
    loop.start();
    return () => loop.stop();
  }, [v]);
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <Animated.View
        style={{
          position: 'absolute', width: size, height: size, borderRadius: size, backgroundColor: color,
          opacity: v.interpolate({ inputRange: [0, 1], outputRange: [0.6, 0] }),
          transform: [{ scale: v.interpolate({ inputRange: [0, 1], outputRange: [1, 3] }) }],
        }}
      />
      <View style={{ width: size, height: size, borderRadius: size, backgroundColor: color }} />
    </View>
  );
}

/** 0'dan hedef sayıya akarak artan değer (sezon istatistikleri) */
export function useCountUp(target: number, duration = 900, decimals = 0): string {
  const [value, setValue] = useState(0);
  useEffect(() => {
    let frame = 0;
    const start = Date.now();
    const tick = () => {
      const t = Math.min(1, (Date.now() - start) / duration);
      const eased = 1 - Math.pow(1 - t, 3);
      setValue(target * eased);
      if (t < 1) {
        frame = requestAnimationFrame(tick);
      }
    };
    frame = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(frame);
  }, [target, duration]);
  return value.toFixed(decimals).replace('.', ',');
}

export function CountUp({ value, decimals = 0, prefix = '', suffix = '', style }: {
  value: number; decimals?: number; prefix?: string; suffix?: string; style?: any;
}) {
  const shown = useCountUp(value, 900, decimals);
  return <Animated.Text style={style}>{prefix}{shown}{suffix}</Animated.Text>;
}

/** Soldan sağa dolan istatistik çubuğu */
export function Bar({ ratio, color: colorProp, height = 6, delay = 0 }: { ratio: number; color?: string; height?: number; delay?: number }) {
  const { c } = useTheme();
  const color = colorProp ?? c.red;
  const v = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.timing(v, { toValue: Math.max(0, Math.min(1, ratio)), duration: 900, delay, easing: Easing.out(Easing.cubic), useNativeDriver: false }).start();
  }, [v, ratio, delay]);
  return (
    <View style={{ height, borderRadius: height, backgroundColor: c.surface3, overflow: 'hidden' }}>
      <Animated.View style={{ height, borderRadius: height, backgroundColor: color, width: v.interpolate({ inputRange: [0, 1], outputRange: ['0%', '100%'] }) }} />
    </View>
  );
}
