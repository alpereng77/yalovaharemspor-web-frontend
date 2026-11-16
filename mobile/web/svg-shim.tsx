/**
 * Web önizlemesi için react-native-svg yerine geçen küçük köprü.
 * Uygulama sadece Svg/Path/Circle/Rect/Defs/LinearGradient/Stop kullanır; bunlar tarayıcının kendi SVG öğelerine çevrilir.
 */
import React from 'react';

type Props = Record<string, any> & { children?: React.ReactNode };

function clean({ accessibilityElementsHidden, importantForAccessibility, ...rest }: Props) {
  return accessibilityElementsHidden || importantForAccessibility === 'no' ? { ...rest, 'aria-hidden': true } : rest;
}

export default function Svg(props: Props) {
  return React.createElement('svg', { xmlns: 'http://www.w3.org/2000/svg', style: { display: 'block' }, ...clean(props) });
}
export { Svg };
export const Path = (props: Props) => React.createElement('path', props);
export const Circle = (props: Props) => React.createElement('circle', props);
export const Rect = (props: Props) => React.createElement('rect', props);
export const Defs = (props: Props) => React.createElement('defs', props);
export const LinearGradient = (props: Props) => React.createElement('linearGradient', props);
export const Stop = (props: Props) => React.createElement('stop', props);
