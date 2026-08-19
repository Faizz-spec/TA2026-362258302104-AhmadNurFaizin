import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import ziggy from 'vite-plugin-ziggy'
import path from 'path'

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    ziggy(),
  ],
  resolve: {
    alias: { '@': path.resolve(__dirname, './resources/js') },
    dedupe: ['vue', '@vue/runtime-core', '@vue/runtime-dom'],
  },
  server: {
    host: true,
    port: 5179,
    strictPort: true,

    // URL publik HMR
    origin: 'https://dev-hmr.safiradmin.com',

    // Izinkan front-end di dev.safiradmin.com
    cors: { origin: ['https://dev.safiradmin.com'], credentials: true },
    headers: {
      'Access-Control-Allow-Origin': 'https://dev.safiradmin.com',
      'Access-Control-Allow-Credentials': 'true',
    },

    hmr: {
      host: 'dev-hmr.safiradmin.com',
      protocol: 'wss',
      clientPort: 443,
    },
  },
})
