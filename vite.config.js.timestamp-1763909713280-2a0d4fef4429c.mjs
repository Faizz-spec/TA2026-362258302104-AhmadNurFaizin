// vite.config.js
import { defineConfig } from "file:///D:/magang%20dinkes/newsimpuswangi-wildan/node_modules/vite/dist/node/index.js";
import laravel from "file:///D:/magang%20dinkes/newsimpuswangi-wildan/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///D:/magang%20dinkes/newsimpuswangi-wildan/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///D:/magang%20dinkes/newsimpuswangi-wildan/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import path from "path";
import ziggy from "file:///D:/magang%20dinkes/newsimpuswangi-wildan/node_modules/vite-plugin-ziggy/dist/index.js";
var __vite_injected_original_dirname = "D:\\magang dinkes\\newsimpuswangi-wildan";
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxtYWdhbmcgZGlua2VzXFxcXG5ld3NpbXB1c3dhbmdpLXdpbGRhblwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiRDpcXFxcbWFnYW5nIGRpbmtlc1xcXFxuZXdzaW1wdXN3YW5naS13aWxkYW5cXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0Q6L21hZ2FuZyUyMGRpbmtlcy9uZXdzaW1wdXN3YW5naS13aWxkYW4vdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnO1xuaW1wb3J0IHZ1ZUpzeCBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUtanN4JztcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnO1xuaW1wb3J0IHppZ2d5IGZyb20gJ3ZpdGUtcGx1Z2luLXppZ2d5JztcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgcGx1Z2luczogW1xuICAgIGxhcmF2ZWwoe1xuICAgICAgaW5wdXQ6IFsncmVzb3VyY2VzL2Nzcy9hcHAuY3NzJywgJ3Jlc291cmNlcy9qcy9hcHAuanMnXSxcbiAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgfSksXG4gICAgdnVlKCksXG4gICAgdnVlSnN4KCksIC8vIFx1MjE5MCBwYXN0aWthbiB2ZXJzaSBjb2NvayAmIHBvc2lzaSBzZXRlbGFoIHZ1ZSgpXG4gICAgemlnZ3koKSxcbiAgXSxcbiAgcmVzb2x2ZToge1xuICAgIGFsaWFzOiB7XG4gICAgICAnQCc6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsICcuL3Jlc291cmNlcy9qcycpLFxuICAgIH0sXG4gIH0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBNFMsU0FBUyxvQkFBb0I7QUFDelUsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUNoQixPQUFPLFlBQVk7QUFDbkIsT0FBTyxVQUFVO0FBQ2pCLE9BQU8sV0FBVztBQUxsQixJQUFNLG1DQUFtQztBQU96QyxJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUMxQixTQUFTO0FBQUEsSUFDUCxRQUFRO0FBQUEsTUFDTixPQUFPLENBQUMseUJBQXlCLHFCQUFxQjtBQUFBLE1BQ3RELFNBQVM7QUFBQSxJQUNYLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxJQUNKLE9BQU87QUFBQTtBQUFBLElBQ1AsTUFBTTtBQUFBLEVBQ1I7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNMLEtBQUssS0FBSyxRQUFRLGtDQUFXLGdCQUFnQjtBQUFBLElBQy9DO0FBQUEsRUFDRjtBQUNGLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
