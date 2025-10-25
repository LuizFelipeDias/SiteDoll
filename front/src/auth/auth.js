// src/auth.js
import { reactive, computed } from 'vue';
import { api, ensureCsrfCookie, install401Handler } from '@/lib/api';

const state = reactive({
    user: null,     // objeto do back (App\Models\Usuario) ou null
    ready: false,   // já tentou carregar /me pelo menos uma vez?
});

export const isAuthenticated = computed(() => !!state.user);
export const currentUser     = computed(() => state.user);

export async function fetchUser() {
    try {
        const { data } = await api.get('/me'); // rota web protegida por 'auth'
        state.user  = data ?? null;
    } catch {
        state.user = null;
    } finally {
        state.ready = true;
    }
}

export async function login({ email, password, remember = false }) {
    await ensureCsrfCookie();
    await api.post('/login', { email, password, remember }); // cria sessão
    await fetchUser();
}

export async function logout() {
    try {
        await api.post('/logout');
    } finally {
        state.user = null;
    }
}

// instala handler 401: se sessão expirar, zera user
install401Handler(() => { state.user = null; });

export default {
    state, isAuthenticated, currentUser, fetchUser, login, logout,
};
