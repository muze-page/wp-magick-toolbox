import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

//配置路径
import path from "path";

//媒体资源打包添加前缀
const site = "wp-content/plugins/wp-magick-toolbox/vite/public";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "src"),
    },
  },
  //媒体资源打包前缀

  base: site,
});
