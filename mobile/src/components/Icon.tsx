import React from 'react';
import Svg, { Circle, Path, Rect } from 'react-native-svg';
import { useTheme } from '../themeContext';

export type IconName =
  | 'home' | 'calendar' | 'users' | 'news' | 'gift' | 'chevron' | 'back' | 'pin' | 'clock' | 'phone'
  | 'instagram' | 'x' | 'youtube' | 'globe' | 'ticket' | 'star' | 'mail' | 'check' | 'alert' | 'refresh' | 'info' | 'external'
  | 'ball' | 'trophy' | 'shield' | 'sun' | 'moon';

type Props = { name: IconName; size?: number; color?: string; strokeWidth?: number };

/** Çizgi ikonlar (24x24). Ek font/bağlantı gerektirmez. */
export function Icon({ name, size = 22, color: colorProp, strokeWidth = 2 }: Props) {
  const { c } = useTheme();
  const color = colorProp ?? c.text;
  const p = { stroke: color, strokeWidth, fill: 'none', strokeLinecap: 'round' as const, strokeLinejoin: 'round' as const };
  return (
    <Svg width={size} height={size} viewBox="0 0 24 24" accessibilityElementsHidden importantForAccessibility="no">
      {name === 'home' && <><Path d="M3 10.5 12 3l9 7.5" {...p} /><Path d="M5 9.5V20h14V9.5" {...p} /><Path d="M10 20v-5h4v5" {...p} /></>}
      {name === 'calendar' && <><Rect x={3} y={4.5} width={18} height={16} rx={2.5} {...p} /><Path d="M3 9.5h18M8 3v3M16 3v3" {...p} /></>}
      {name === 'users' && <><Circle cx={9} cy={8} r={3.5} {...p} /><Path d="M2.5 20c.6-3.4 3.2-5.5 6.5-5.5s5.9 2.1 6.5 5.5" {...p} /><Path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.8c1.9.7 3.1 2.5 3.5 5.2" {...p} /></>}
      {name === 'news' && <><Rect x={3} y={4} width={18} height={16} rx={2.5} {...p} /><Path d="M7 8.5h10M7 12h10M7 15.5h6" {...p} /></>}
      {name === 'gift' && <><Rect x={3} y={8} width={18} height={4} rx={1} {...p} /><Path d="M5 12v8h14v-8M12 8v12" {...p} /><Path d="M12 8C10.5 4 6.5 4 7 6.5 7.3 8 12 8 12 8ZM12 8c1.5-4 5.5-4 5-1.5C16.7 8 12 8 12 8Z" {...p} /></>}
      {name === 'chevron' && <Path d="m9 5 7 7-7 7" {...p} />}
      {name === 'back' && <Path d="M15 5 8 12l7 7" {...p} />}
      {name === 'pin' && <><Path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z" {...p} /><Circle cx={12} cy={9.5} r={2.5} {...p} /></>}
      {name === 'clock' && <><Circle cx={12} cy={12} r={9} {...p} /><Path d="M12 7v5l3 2" {...p} /></>}
      {name === 'phone' && <Path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" {...p} />}
      {name === 'instagram' && <><Rect x={3} y={3} width={18} height={18} rx={5} {...p} /><Circle cx={12} cy={12} r={4} {...p} /><Circle cx={17.3} cy={6.7} r={0.6} fill={color} stroke={color} /></>}
      {name === 'x' && <Path d="M4 4l16 16M20 4 4 20" {...p} />}
      {name === 'youtube' && <><Rect x={2.5} y={5.5} width={19} height={13} rx={4} {...p} /><Path d="m10 9.5 5 2.5-5 2.5Z" {...p} /></>}
      {name === 'globe' && <><Circle cx={12} cy={12} r={9} {...p} /><Path d="M3 12h18M12 3c2.5 2.7 3.5 5.7 3.5 9s-1 6.3-3.5 9c-2.5-2.7-3.5-5.7-3.5-9S9.5 5.7 12 3Z" {...p} /></>}
      {name === 'ticket' && <Path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z M10 6v12" {...p} />}
      {name === 'star' && <Path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9Z" {...p} />}
      {name === 'mail' && <><Rect x={3} y={5} width={18} height={14} rx={2.5} {...p} /><Path d="m3.5 6.5 8.5 6.5 8.5-6.5" {...p} /></>}
      {name === 'check' && <Path d="m5 12.5 4.5 4.5L19 7.5" {...p} />}
      {name === 'alert' && <><Circle cx={12} cy={12} r={9} {...p} /><Path d="M12 7.5v5.5M12 16.5v.01" {...p} /></>}
      {name === 'info' && <><Circle cx={12} cy={12} r={9} {...p} /><Path d="M12 11v5.5M12 7.5v.01" {...p} /></>}
      {name === 'refresh' && <><Path d="M20 11a8 8 0 1 0-2.3 5.7" {...p} /><Path d="M20 4v7h-7" {...p} /></>}
      {name === 'ball' && <><Circle cx={12} cy={12} r={9} {...p} /><Path d="M3 12h18M12 3v18" {...p} /><Path d="M5.6 5.6c3.5 3.5 3.5 9.3 0 12.8M18.4 5.6c-3.5 3.5-3.5 9.3 0 12.8" {...p} /></>}
      {name === 'trophy' && <><Path d="M7 4h10v5a5 5 0 0 1-10 0Z" {...p} /><Path d="M7 6H4a3 3 0 0 0 3 4M17 6h3a3 3 0 0 1-3 4M12 14v4M8 21h8M9 18h6" {...p} /></>}
      {name === 'sun' && <><Circle cx={12} cy={12} r={4} {...p} /><Path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" {...p} /></>}
      {name === 'moon' && <Path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5Z" {...p} />}
      {name === 'shield' &&<Path d="M12 3 4.5 6v6c0 4.5 3.2 7.6 7.5 9 4.3-1.4 7.5-4.5 7.5-9V6Z" {...p} />}
      {name === 'external' &&<><Path d="M14 4h6v6M20 4l-9 9" {...p} /><Path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" {...p} /></>}
    </Svg>
  );
}
