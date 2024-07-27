import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/css/app.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: "public/build",
        rollupOptions: {
            input: {
                main1: "resources/js/app.js",
                main2: "resources/js/latihan.js",
                main3: "resources/js/riwayat.js",
                style1: "resources/sass/app.scss",
                style2: "resources/css/app.css",
            },
        },
    },
});
