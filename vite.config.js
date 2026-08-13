import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL;

    let hmrHost = env.VITE_HMR_HOST;

    if (!hmrHost) {
        try {
            hmrHost = new URL(appUrl).hostname;
        } catch {
            hmrHost = 'localhost';
        }
    }

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.jsx',
                ],
                refresh: true,
            }),
            tailwindcss(),
            react(),
        ],
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            hmr: {
                host: hmrHost,
                port: Number(env.VITE_HMR_PORT || 5173),
                protocol: env.VITE_HMR_PROTOCOL || 'ws',
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
