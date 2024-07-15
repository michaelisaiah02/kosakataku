import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ["jquery", "sweetalert2"],
    },
    server: {
        host: "localhost", // atau gunakan 'localhost'
        port: 3000, // atau port yang Anda gunakan
    },
});
