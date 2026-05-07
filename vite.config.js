import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { visualizer } from 'rollup-plugin-visualizer';
import legacy from '@vitejs/plugin-legacy';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        legacy({
            targets: ['defaults', 'not IE 11']
        }),
        visualizer({
            filename: 'dist/stats.html',
            open: true,
            gzipSize: true,
            brotliSize: true,
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', '@inertiajs/vue3'],
                    charts: ['chart.js', 'vue-chartjs'],
                    tables: ['datatables.net', 'datatables.net-vue3'],
                    utils: ['axios', 'pinia', 'vue-router'],
                    ui: ['@fortawesome/fontawesome-free', 'aos', 'bootstrap'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
        assetsInlineLimit: 4096,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
            mangle: {
                safari10: true,
            },
        },
    },
    optimizeDeps: {
        include: [
            'vue',
            '@inertiajs/vue3',
            'pinia',
            'vue-router',
            'axios',
        ],
    },
    server: {
        hmr: {
            overlay: false,
        },
    },
});
