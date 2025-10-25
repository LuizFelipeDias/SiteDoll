import { createRouter, createWebHistory } from 'vue-router';

import Home       from '@/pages/Home.vue';
import Login      from '@/auth/Login.vue';
import CreateUser from '@/auth/CreateUser.vue';
import Prompts    from '@/pages/Prompts.vue';

const routes = [
    // 1) Primeira página = login
    { path: '/login',    name: 'login',    component: Login },

    // 2) Depois do login = home
    { path: '/home',     name: 'home',     component: Home },

    // cadastro deve abrir o CreateUser.vue
    { path: '/cadastro', name: 'cadastro', component: CreateUser },

    // páginas protegidas que você já tem
    { path: '/prompts',  name: 'prompts',  component: Prompts },

    // raiz → /login
    { path: '/', redirect: { name: 'login' } },

    // fallback
    { path: '/:pathMatch(.*)*', redirect: { name: 'login' } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
