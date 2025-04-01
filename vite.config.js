import {defineConfig} from "vite";
import dotenv from 'dotenv';
import dotenvExpand from 'dotenv-expand';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

dotenvExpand.expand(dotenv.config());

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        })
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: process.env.PROJECTHOST,
        },
        watch: {
            usePolling: true,
        },
        port: 5173
    }
});