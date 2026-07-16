import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

//配置路径
import path from "path";
// https://vitejs.dev/config/
export default defineConfig({
  root: __dirname,
  publicDir: false,
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, "dist"),
    emptyOutDir: true,
    cssCodeSplit: false,
    modulePreload: false,
    rollupOptions: {
      output: {
        // Count is enqueued as a classic WordPress admin script. Keep every
        // bundled symbol inside an IIFE so minified dependency names cannot
        // overwrite globals such as WordPress' window.wp.
        format: "iife",
        // 指定 chunk 文件名（含导出的代码）
        //chunkFileNames: 'js/[name].js',
        // 指定静态资源文件名（不含导出的代码）
        //assetFileNames: 'assets/[name].[ext]',
        entryFileNames: "index.js",
        assetFileNames: (assetInfo) =>
          assetInfo.name?.endsWith(".css")
            ? "index.css"
            : "[name][extname]",
        chunkFileNames: "[name].js",
        inlineDynamicImports: true,
      },
    },
    chunkSizeWarningLimit: 650,
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "src"),
    },
  },
  base: "./",
});
