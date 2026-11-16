import AsyncStorage from '@react-native-async-storage/async-storage';
import React, { createContext, ReactNode, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { useColorScheme } from 'react-native';
import { dark, light, makeType, Palette, Typography } from './theme';

/** Kullanıcının tercihi: telefonun ayarını izle ya da elle açık/koyu */
export type ThemePref = 'system' | 'light' | 'dark';

const STORAGE_KEY = 'yh.theme';

type Ctx = { c: Palette; T: Typography; pref: ThemePref; setPref: (p: ThemePref) => void; toggle: () => void };

const typeCache = new WeakMap<Palette, Typography>();
const typeFor = (p: Palette) => {
  let t = typeCache.get(p);
  if (!t) {
    t = makeType(p);
    typeCache.set(p, t);
  }
  return t;
};

const ThemeContext = createContext<Ctx>({ c: dark, T: typeFor(dark), pref: 'system', setPref: () => undefined, toggle: () => undefined });

export function ThemeProvider({ children }: { children: ReactNode }) {
  const system = useColorScheme();
  const [pref, setPrefState] = useState<ThemePref>('system');

  useEffect(() => {
    AsyncStorage.getItem(STORAGE_KEY)
      .then(v => (v === 'light' || v === 'dark' || v === 'system') && setPrefState(v))
      .catch(() => undefined);
  }, []);

  const setPref = useCallback((p: ThemePref) => {
    setPrefState(p);
    AsyncStorage.setItem(STORAGE_KEY, p).catch(() => undefined);
  }, []);

  // Telefon ayarı bilinmiyorsa kulübün koyu teması varsayılan
  const resolved: 'light' | 'dark' = pref === 'system' ? (system === 'light' ? 'light' : 'dark') : pref;
  const c = resolved === 'light' ? light : dark;
  const toggle = useCallback(() => setPref(resolved === 'light' ? 'dark' : 'light'), [resolved, setPref]);

  const value = useMemo(() => ({ c, T: typeFor(c), pref, setPref, toggle }), [c, pref, setPref, toggle]);
  return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}

export const useTheme = () => useContext(ThemeContext);

/**
 * Temaya bağlı stil tanımı: `const useStyles = createStyles((c) => StyleSheet.create({...}))`
 * Bileşende `const styles = useStyles()`. Her palet için bir kez üretilir, önbellekten döner.
 */
export function createStyles<S>(factory: (c: Palette, T: Typography) => S): () => S {
  const cache = new WeakMap<Palette, S>();
  return function useStyles() {
    const { c, T } = useTheme();
    let s = cache.get(c);
    if (!s) {
      s = factory(c, T);
      cache.set(c, s);
    }
    return s;
  };
}
