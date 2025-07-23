import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    // Cargar variables de entorno
    const env = loadEnv(mode, process.cwd(), '');
    const isProduction = mode === 'production';
    
    // Configuración base
    const config = {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: {
            host: '0.0.0.0',
            watch: {
                usePolling: true,
            },
        },
        base: isProduction ? '/build/' : '/',
    };

    // Configuración específica para desarrollo
    if (!isProduction) {
        config.server.hmr = {
            protocol: env.VITE_HMR_PROTOCOL || 'ws',
            host: env.VITE_HMR_HOST ? env.VITE_HMR_HOST.replace(/^https?:\/\//, '') : 'localhost',
            port: parseInt(env.VITE_HMR_PORT || 5173, 10)
        };
        
        config.define = {
            'process.env': {}
        };
    }

    return config;
});
