import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite'
import {PrimeVueResolver} from '@primevue/auto-import-resolver';
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
        Components({
            resolvers: [
                PrimeVueResolver(),
                ElementPlusResolver()
            ]
        }),
        AutoImport({
            resolvers: [ElementPlusResolver()]
        }),
    ],
    build: { chunkSizeWarningLimit: 2600, },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        alias: {
            '@' : '/resources/js'
        }
    },
});
