import { defineConfig, loadEnv } from "vite";

const destination = "./theme/assets/dist";
const entries = ["./theme/assets/main.js"];

export default defineConfig(({ mode }) => {
  return {
    base: "./",
    resolve: {
      alias: {
        "@": __dirname,
      },
    },
    server: {
      cors: true,
      strictPort: true,
      port: 3000,
      https: false,
      hmr: {
        host: "localhost",
      },
    },
    build: {
      outDir: destination,
      emptyOutDir: true,
      manifest: true,
      target: "es2018",
      rollupOptions: {
        input: entries,
      },
      minify: true,
      write: true,
    },
  };
});
