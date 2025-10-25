import axios from 'axios';

export const api = axios.create({
    baseURL: '/', // usa o proxy do Vite
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

let csrfReady = false;
export async function ensureCsrfCookie() {
    if (csrfReady) return;
    await api.get('/sanctum/csrf-cookie'); // gera cookie XSRF-TOKEN
    csrfReady = true;
}

api.interceptors.request.use(async (config) => {
    const unsafe = /^(post|put|patch|delete)$/i.test(config.method || '');
    if (unsafe) await ensureCsrfCookie();
    return config;
});
