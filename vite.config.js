import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Dentro de Docker el server debe escuchar en 0.0.0.0 (VITE_HOST lo setea el
// docker-compose). Fuera de Docker sigue siendo localhost, como siempre.
const inDocker = !!process.env.VITE_HOST;

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || 'localhost',
        port: 5173,
        // El navegador corre en el host, así que el hot reload siempre
        // apunta a localhost aunque el server escuche en 0.0.0.0.
        hmr: { host: 'localhost' },
        // Los bind mounts de Docker (sobre todo en Windows/WSL) no propagan
        // eventos de filesystem: sin polling el hot reload no detecta cambios.
        watch: inDocker ? { usePolling: true, interval: 300 } : undefined,
        cors: {
            origin: /^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?$/,
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
