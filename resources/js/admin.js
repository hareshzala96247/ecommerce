import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './admin/App.vue';
import axios from 'axios';
import { store, initAdminAuth } from './admin/store';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// CSRF handled via XSRF-TOKEN cookie — axios sends it as X-XSRF-TOKEN automatically.

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/admin/login',
            component: () => import('./admin/pages/Login.vue'),
            meta: { guest: true },
        },
        {
            path: '/admin',
            component: () => import('./admin/layouts/AdminLayout.vue'),
            meta: { requiresAuth: true },
            children: [
                { path: '', redirect: '/admin/dashboard' },
                { path: 'dashboard',          component: () => import('./admin/pages/Dashboard.vue') },
                { path: 'products',           component: () => import('./admin/pages/products/Index.vue') },
                { path: 'products/create',    component: () => import('./admin/pages/products/Form.vue') },
                { path: 'products/:id/edit',  component: () => import('./admin/pages/products/Form.vue') },
                { path: 'attributes',          component: () => import('./admin/pages/attributes/Index.vue') },
                { path: 'categories',         component: () => import('./admin/pages/categories/Index.vue') },
                { path: 'categories/create',  component: () => import('./admin/pages/categories/Form.vue') },
                { path: 'categories/:id/edit',component: () => import('./admin/pages/categories/Form.vue') },
                { path: 'orders',             component: () => import('./admin/pages/orders/Index.vue') },
                { path: 'orders/:id',         component: () => import('./admin/pages/orders/Show.vue') },
                { path: 'users',              component: () => import('./admin/pages/users/Index.vue') },
                { path: 'settings/general',   component: () => import('./admin/pages/settings/General.vue') },
            ],
        },
        { path: '/:pathMatch(.*)*', redirect: '/admin/dashboard' },
    ],
});

router.beforeEach(async (to) => {
    await initAdminAuth();
    const loggedIn = !!store.user;
    if (to.meta.requiresAuth && !loggedIn) return '/admin/login';
    if (to.meta.guest && loggedIn) return '/admin/dashboard';
});

createApp(App).use(router).mount('#admin-app');
