import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        server: {
            host: '0.0.0.0',
            port: 5173,
            cors: true,
            hmr: {
                host: env.APP_URL ? new URL(env.APP_URL).hostname : 'localhost',
            },
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/styles.css',
                    'resources/js/app.js',
                    'resources/js/scripts.js'
                ],
                refresh: true,
            })
        ],
    };
});