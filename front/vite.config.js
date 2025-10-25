// vite.config.js
import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'node:path'
import {fileURLToPath} from 'node:url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default defineConfig({
    plugins: [vue(), tailwindcss()],
    resolve: {
        alias: {'@': path.resolve(__dirname, 'src')},
    },
    server: {
        port: 5173,
        proxy: {
            '/sanctum': {target: 'http://127.0.0.1:8000', changeOrigin: true},
            '/logout': {target: 'http://127.0.0.1:8000', changeOrigin: true},
            '/me': {target: 'http://127.0.0.1:8000', changeOrigin: true},
            '/api': {target: 'http://127.0.0.1:8000', changeOrigin: true},

            // /login: proxy só para POST; GET retorna a SPA
            '/login': {
                target: 'http://127.0.0.1:8000',
                changeOrigin: true,
                /**
                 * Se NÃO for POST, devolve /index.html do Vite (SPA).
                 * @param {import('http').IncomingMessage} req
                 * @returns {string|void}
                 */
                bypass(req) {
                    const method = /** @type {string} */ (req.method ?? '').toUpperCase()
                    if (method !== 'POST') {
                        return '/index.html'
                    }
                    // POST segue para o proxy
                },
            },

        },
    },
})
