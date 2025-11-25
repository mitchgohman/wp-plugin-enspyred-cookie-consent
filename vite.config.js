import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { dirname, resolve } from "path";
import { fileURLToPath } from "url";
import { readFileSync } from "fs";

const __dirname = dirname(fileURLToPath(import.meta.url));
const port = 5139;
const pkg = JSON.parse(
    readFileSync(resolve(__dirname, "package.json"), "utf-8")
);

export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            "@Core": resolve(__dirname, "src/core"),
        },
    },
    define: {
        "process.env": {}, // Avoids undefined env for styled-components
        __PLUGIN_VERSION__: JSON.stringify(pkg.version),
    },
    server: {
        host: "0.0.0.0",
        port,
        strictPort: true,
        origin: `http://localhost:${port}`,
        cors: true,
        hmr: {
            clientPort: port, // helpful behind proxies/containers
        },
    },
    build: {
        manifest: true,
        outDir: resolve(__dirname, "build"),
        emptyOutDir: false,
        sourcemap: true,
        rollupOptions: {
            input: {
                main: resolve(__dirname, "src/index.jsx"),
                optout: resolve(__dirname, "src/optOutLink.js"),
            },
        },
    },
});
