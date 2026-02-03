import { defineConfig } from "vite";
import path from "path";
import mkcert from "vite-plugin-mkcert";
import devManifest from "vite-plugin-dev-manifest";
import tailwindcss from "@tailwindcss/vite";

const ROOT = path.resolve("../../../");
const BASE = __dirname.replace(ROOT, "");
const destination = "./dist";
const entries = ["./assets/main.js"];

export default defineConfig(({ mode }) => {
  return {
    base: process.env.NODE_ENV === "production" ? `${BASE}/dist/` : BASE,
    plugins: [mkcert(), devManifest(), tailwindcss()],
    resolve: {
      alias: {
        "@": BASE,
      },
    },
    server: {
      port: 3000,
      cors: true,
      https: true,
      hmr: {
        host: "localhost",
      },
      watch: {
        include: ["views/**/*"],
      },
    },
    esbuild: {
      // Remove console logs from production builds
      drop: ["console", "debugger"],
    },
    build: {
      assetsDir: "./assets",
      outDir: destination,
      emptyOutDir: true,
      manifest: true,
      rollupOptions: {
        input: entries,
      },
      minify: true,
      write: true,
    },
  };
});
