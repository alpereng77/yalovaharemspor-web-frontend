import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

/**
 * Tarayıcı önizlemesi: aynı React Native kodu react-native-web ile çalışır.
 * Sadece geliştirme/önizleme içindir; mağazaya giden uygulama Android/iOS derlemesidir.
 * Çalıştırma: npm run web  →  http://localhost:8081
 */
const here = path.dirname(fileURLToPath(import.meta.url));

// Kütüphanelerin web sürümleri (.web.js) her zaman yerel (native) dosyalardan önce seçilir
const extensions = ['.web.tsx', '.web.ts', '.web.jsx', '.web.js', '.web.mjs', '.tsx', '.ts', '.jsx', '.js', '.mjs', '.json'];

export default defineConfig({
  root: here,
  plugins: [react()],
  resolve: {
    // Sadece "react-native" paket adının kendisi yönlendirilir; alt yollar değil
    alias: [
      { find: /^react-native$/, replacement: 'react-native-web' },
      { find: /^react-native-svg$/, replacement: path.resolve(here, 'svg-shim.tsx') },
    ],
    extensions,
  },
  define: {
    __DEV__: 'true',
    'process.env.NODE_ENV': JSON.stringify('development'),
    global: 'globalThis',
  },
  optimizeDeps: {
    rolldownOptions: {
      resolve: { extensions },
    },
  },
  server: {
    port: 8081,
    strictPort: true,
    fs: { allow: [path.resolve(here, '..')] },
  },
});
