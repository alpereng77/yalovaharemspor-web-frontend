import React, { useId } from 'react';
import { StyleProp, StyleSheet, View, ViewStyle } from 'react-native';
import Svg, { Defs, LinearGradient, Rect, Stop } from 'react-native-svg';

type GradientStop = { offset: number; color: string; opacity?: number };

/**
 * Fotoğrafların üzerine koyulan degrade katman (ek yerel kütüphane gerektirmez, react-native-svg ile).
 * Varsayılan: alttan koyu zemine kararan perde — beyaz yazılar fotoğraf üzerinde okunur.
 */
export function Gradient({
  stops = [{ offset: 0, color: '#05100A', opacity: 0 }, { offset: 0.55, color: '#05100A', opacity: 0.55 }, { offset: 1, color: '#05100A', opacity: 1 }],
  vertical = true,
  style,
}: { stops?: GradientStop[]; vertical?: boolean; style?: StyleProp<ViewStyle> }) {
  const id = 'g' + useId().replace(/[^a-zA-Z0-9]/g, '');
  return (
    <View pointerEvents="none" style={[StyleSheet.absoluteFill, style]}>
      <Svg width="100%" height="100%" preserveAspectRatio="none">
        <Defs>
          <LinearGradient id={id} x1="0" y1="0" x2={vertical ? '0' : '1'} y2={vertical ? '1' : '0'}>
            {stops.map((s, i) => <Stop key={i} offset={s.offset} stopColor={s.color} stopOpacity={s.opacity ?? 1} />)}
          </LinearGradient>
        </Defs>
        <Rect x="0" y="0" width="100%" height="100%" fill={`url(#${id})`} />
      </Svg>
    </View>
  );
}
