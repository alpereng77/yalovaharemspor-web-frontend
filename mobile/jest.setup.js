/* global jest */
// Testlerde yerel (native) modüllerin yerine geçen taklitler.

jest.mock('@react-native-async-storage/async-storage', () => {
  const store = new Map();
  const api = {
    getItem: jest.fn(async k => (store.has(k) ? store.get(k) : null)),
    setItem: jest.fn(async (k, v) => void store.set(k, v)),
    removeItem: jest.fn(async k => void store.delete(k)),
    clear: jest.fn(async () => store.clear()),
  };
  return { __esModule: true, default: api };
});

jest.mock('react-native-device-info', () => ({
  __esModule: true,
  default: { getUniqueId: jest.fn(async () => 'a1b2c3d4e5f6a7b8') },
}));

jest.mock('react-native-safe-area-context', () => require('react-native-safe-area-context/jest/mock').default);

// SVG ikonlar testte çizilmez; yalnızca yapı kontrol edilir
jest.mock('react-native-svg', () => {
  const React = require('react');
  const make = name => props => React.createElement(name, props, props.children);
  return {
    __esModule: true, default: make('Svg'), Svg: make('Svg'), Path: make('Path'), Circle: make('Circle'), Rect: make('Rect'),
    Defs: make('Defs'), LinearGradient: make('LinearGradient'), Stop: make('Stop'),
  };
});
