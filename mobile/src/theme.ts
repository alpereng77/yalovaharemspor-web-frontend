import { TextStyle } from 'react-native';

/**
 * Yalova Haremspor görsel kimliği: koyu zümrüt, agresif kırmızı, altın vurgular.
 * İki palet aynı marka renklerini paylaşır; sadece zemin, yüzey ve yazı tonları değişir.
 */
export type Palette = {
  mode: 'dark' | 'light';
  bg: string;
  surface: string;
  surface2: string;
  surface3: string;
  line: string;
  lineStrong: string;
  tabBar: string;
  green: string;
  greenBright: string;
  greenGlow: string;
  red: string;
  redBright: string;
  gold: string;
  goldSoft: string;
  text: string;
  textSoft: string;
  muted: string;
  faint: string;
  win: string;
  loss: string;
  white: string;
  black: string;
  /** Fotoğraf üzerindeki koyu perde: iki modda da koyu kalır, beyaz yazı okunur */
  scrim: string;
};

const brand = {
  green: '#003F20',
  greenBright: '#00A859',
  red: '#A70101',
  white: '#FFFFFF',
  black: '#000000',
  scrim: '#05100A',
};

export const dark: Palette = {
  ...brand,
  mode: 'dark',
  bg: '#05100A',
  surface: '#0B1A12',
  surface2: '#11251A',
  surface3: '#18321F',
  line: 'rgba(255,255,255,0.08)',
  lineStrong: 'rgba(255,255,255,0.16)',
  tabBar: '#030B06',
  greenGlow: '#16C26A',
  redBright: '#E01B1B',
  gold: '#E5A93C',
  goldSoft: 'rgba(229,169,60,0.14)',
  text: '#F3F7F4',
  textSoft: '#C3D1C8',
  muted: '#7E9488',
  faint: '#4C6155',
  win: '#16C26A',
  loss: '#F04646',
};

export const light: Palette = {
  ...brand,
  mode: 'light',
  bg: '#F2F5F3',
  surface: '#FFFFFF',
  surface2: '#EDF2EE',
  surface3: '#DFE7E2',
  line: 'rgba(5,16,10,0.08)',
  lineStrong: 'rgba(5,16,10,0.14)',
  tabBar: '#FFFFFF',
  greenGlow: '#0E9F55',
  redBright: '#C80E0E',
  gold: '#B07612',
  goldSoft: 'rgba(176,118,18,0.12)',
  text: '#0A1A11',
  textSoft: '#33463B',
  muted: '#64776C',
  faint: '#A4B3AA',
  win: '#0F9A52',
  loss: '#D42F2F',
};

/** Uygulamaya gömülü yazı tipleri. Android'de özel yazı tipiyle fontWeight kullanılmaz; ağırlık aile adındadır. */
export const fonts = {
  regular: 'Barlow-Regular',
  medium: 'Barlow-Medium',
  semibold: 'Barlow-SemiBold',
  bold: 'Barlow-Bold',
  condSemi: 'BarlowCondensed-SemiBold',
  condBold: 'BarlowCondensed-Bold',
  condXBold: 'BarlowCondensed-ExtraBold',
  condBlack: 'BarlowCondensed-Black',
  condBlackItalic: 'BarlowCondensed-BlackItalic',
};

export const space = { xs: 4, sm: 8, md: 12, lg: 16, xl: 20, xxl: 28, xxxl: 40 };
export const radius = { sm: 6, md: 10, lg: 14, xl: 20, pill: 999 };

export function makeType(c: Palette) {
  return {
    display: { fontFamily: fonts.condBlackItalic, fontSize: 40, lineHeight: 42, color: c.text, textTransform: 'uppercase', letterSpacing: -0.5 } as TextStyle,
    h1: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.text, textTransform: 'uppercase' } as TextStyle,
    h2: { fontFamily: fonts.condBlack, fontSize: 22, lineHeight: 26, color: c.text, textTransform: 'uppercase' } as TextStyle,
    h3: { fontFamily: fonts.condBold, fontSize: 22, lineHeight: 26, color: c.text } as TextStyle,
    kicker: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 2, color: c.muted, textTransform: 'uppercase' } as TextStyle,
    body: { fontFamily: fonts.regular, fontSize: 15, lineHeight: 22, color: c.textSoft } as TextStyle,
    bodyStrong: { fontFamily: fonts.semibold, fontSize: 15, lineHeight: 22, color: c.text } as TextStyle,
    small: { fontFamily: fonts.regular, fontSize: 12, lineHeight: 16, color: c.muted } as TextStyle,
    label: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.2, color: c.muted, textTransform: 'uppercase' } as TextStyle,
    number: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.text, fontVariant: ['tabular-nums'] } as TextStyle,
  };
}
export type Typography = ReturnType<typeof makeType>;
