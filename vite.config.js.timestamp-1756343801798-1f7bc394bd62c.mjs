// vite.config.js
import { defineConfig } from "file:///C:/Users/akusu/OneDrive/Documents/6.%20Magang%20MKI%20DINKES/BACKUP/newsimpuswangi-main/newsimpuswangi-main/newsimpuswangi-main/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/akusu/OneDrive/Documents/6.%20Magang%20MKI%20DINKES/BACKUP/newsimpuswangi-main/newsimpuswangi-main/newsimpuswangi-main/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/Users/akusu/OneDrive/Documents/6.%20Magang%20MKI%20DINKES/BACKUP/newsimpuswangi-main/newsimpuswangi-main/newsimpuswangi-main/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///C:/Users/akusu/OneDrive/Documents/6.%20Magang%20MKI%20DINKES/BACKUP/newsimpuswangi-main/newsimpuswangi-main/newsimpuswangi-main/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import path from "path";
import ziggy from "file:///C:/Users/akusu/OneDrive/Documents/6.%20Magang%20MKI%20DINKES/BACKUP/newsimpuswangi-main/newsimpuswangi-main/newsimpuswangi-main/node_modules/vite-plugin-ziggy/dist/index.js";
var __vite_injected_original_dirname = "C:\\Users\\akusu\\OneDrive\\Documents\\6. Magang MKI DINKES\\BACKUP\\newsimpuswangi-main\\newsimpuswangi-main\\newsimpuswangi-main";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true
    }),
    vue(),
    vueJsx(),
    // ← pastikan versi cocok & posisi setelah vue()
    ziggy()
  ],
  resolve: {
    alias: {
      "@": path.resolve(__vite_injected_original_dirname, "./resources/js")
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxha3VzdVxcXFxPbmVEcml2ZVxcXFxEb2N1bWVudHNcXFxcNi4gTWFnYW5nIE1LSSBESU5LRVNcXFxcQkFDS1VQXFxcXG5ld3NpbXB1c3dhbmdpLW1haW5cXFxcbmV3c2ltcHVzd2FuZ2ktbWFpblxcXFxuZXdzaW1wdXN3YW5naS1tYWluXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxha3VzdVxcXFxPbmVEcml2ZVxcXFxEb2N1bWVudHNcXFxcNi4gTWFnYW5nIE1LSSBESU5LRVNcXFxcQkFDS1VQXFxcXG5ld3NpbXB1c3dhbmdpLW1haW5cXFxcbmV3c2ltcHVzd2FuZ2ktbWFpblxcXFxuZXdzaW1wdXN3YW5naS1tYWluXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9Vc2Vycy9ha3VzdS9PbmVEcml2ZS9Eb2N1bWVudHMvNi4lMjBNYWdhbmclMjBNS0klMjBESU5LRVMvQkFDS1VQL25ld3NpbXB1c3dhbmdpLW1haW4vbmV3c2ltcHVzd2FuZ2ktbWFpbi9uZXdzaW1wdXN3YW5naS1tYWluL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCB2dWVKc3ggZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlLWpzeCdcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnO1xuaW1wb3J0IHppZ2d5IGZyb20gJ3ZpdGUtcGx1Z2luLXppZ2d5JztcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgcGx1Z2luczogW1xuICAgIGxhcmF2ZWwoe1xuICAgICAgaW5wdXQ6IFsncmVzb3VyY2VzL2Nzcy9hcHAuY3NzJywgJ3Jlc291cmNlcy9qcy9hcHAuanMnXSxcbiAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgfSksXG4gICAgdnVlKCksXG4gICAgdnVlSnN4KCksICAgICAgIC8vIFx1MjE5MCBwYXN0aWthbiB2ZXJzaSBjb2NvayAmIHBvc2lzaSBzZXRlbGFoIHZ1ZSgpXG4gICAgemlnZ3koKSxcblxuICBdLFxuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgICdAJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJy4vcmVzb3VyY2VzL2pzJyksXG4gICAgfSxcbiAgfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUF1akIsU0FBUyxvQkFBb0I7QUFDcGxCLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFDaEIsT0FBTyxZQUFZO0FBQ25CLE9BQU8sVUFBVTtBQUNqQixPQUFPLFdBQVc7QUFMbEIsSUFBTSxtQ0FBbUM7QUFPekMsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDMUIsU0FBUztBQUFBLElBQ1AsUUFBUTtBQUFBLE1BQ04sT0FBTyxDQUFDLHlCQUF5QixxQkFBcUI7QUFBQSxNQUN0RCxTQUFTO0FBQUEsSUFDWCxDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsSUFDSixPQUFPO0FBQUE7QUFBQSxJQUNQLE1BQU07QUFBQSxFQUVSO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDUCxPQUFPO0FBQUEsTUFDTCxLQUFLLEtBQUssUUFBUSxrQ0FBVyxnQkFBZ0I7QUFBQSxJQUMvQztBQUFBLEVBQ0Y7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
