import React, { useEffect, useRef, useState } from 'react';
import { Animated, LayoutChangeEvent, Pressable, StyleSheet, Text, View } from 'react-native';
import type { BottomTabBarProps } from '@react-navigation/bottom-tabs';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { Icon, IconName } from '../components/Icon';
import { NATIVE } from '../components/motion';
import { fonts } from '../theme';
import { createStyles, useTheme } from '../themeContext';

const ICONS: Record<string, IconName> = { Home: 'home', Matches: 'calendar', Squad: 'users', News: 'news', Giveaways: 'gift' };

/** Koyu alt sekme çubuğu: aktif sekmeye yaylanarak kayan kırmızı gösterge, seçilince büyüyen ikon */
export function TabBar({ state, descriptors, navigation }: BottomTabBarProps) {
  const styles = useStyles();
  const insets = useSafeAreaInsets();
  const [width, setWidth] = useState(0);
  const x = useRef(new Animated.Value(state.index)).current;
  useEffect(() => {
    Animated.spring(x, { toValue: state.index, useNativeDriver: NATIVE, speed: 16, bounciness: 7 }).start();
  }, [x, state.index]);
  const tabW = width / state.routes.length;

  return (
    <View style={[styles.bar, { paddingBottom: Math.max(insets.bottom, 8) }]} onLayout={(e: LayoutChangeEvent) => setWidth(e.nativeEvent.layout.width)} accessibilityRole="tablist">
      {tabW ? (
        <Animated.View style={[styles.indicator, { width: tabW * 0.42, left: tabW * 0.29, transform: [{ translateX: x.interpolate({ inputRange: [0, 1], outputRange: [0, tabW] }) }] }]} />
      ) : null}
      {state.routes.map((route, i) => {
        const focused = state.index === i;
        const label = descriptors[route.key].options.title ?? route.name;
        return (
          <TabItem
            key={route.key}
            label={label}
            icon={ICONS[route.name] ?? 'home'}
            focused={focused}
            onPress={() => {
              const event = navigation.emit({ type: 'tabPress', target: route.key, canPreventDefault: true });
              if (!focused && !event.defaultPrevented) {
                navigation.navigate(route.name, route.params);
              }
            }}
          />
        );
      })}
    </View>
  );
}

function TabItem({ label, icon, focused, onPress }: { label: string; icon: IconName; focused: boolean; onPress: () => void }) {
  const { c } = useTheme();
  const styles = useStyles();
  const s = useRef(new Animated.Value(focused ? 1 : 0)).current;
  useEffect(() => {
    Animated.spring(s, { toValue: focused ? 1 : 0, useNativeDriver: NATIVE, speed: 20, bounciness: 10 }).start();
  }, [s, focused]);
  return (
    <Pressable onPress={onPress} style={styles.item} accessibilityRole="tab" accessibilityState={{ selected: focused }} accessibilityLabel={label}>
      <Animated.View style={{ transform: [{ scale: s.interpolate({ inputRange: [0, 1], outputRange: [1, 1.12] }) }, { translateY: s.interpolate({ inputRange: [0, 1], outputRange: [0, -1] }) }] }}>
        <Icon name={icon} size={23} color={focused ? c.text : c.muted} strokeWidth={focused ? 2.4 : 1.9} />
      </Animated.View>
      <Text style={[styles.label, focused && styles.labelOn]} numberOfLines={1}>{label}</Text>
    </Pressable>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  bar: { flexDirection: 'row', backgroundColor: c.tabBar, borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: c.lineStrong, paddingTop: 12 },
  indicator: { position: 'absolute', top: 0, height: 3, borderBottomLeftRadius: 3, borderBottomRightRadius: 3, backgroundColor: c.red },
  item: { flex: 1, alignItems: 'center', gap: 4 },
  label: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 0.8, color: c.muted, textTransform: 'uppercase' },
  labelOn: { color: c.text },
}));
