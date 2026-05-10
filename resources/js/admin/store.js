import { reactive } from 'vue';
import axios from 'axios';

export const store = reactive({
    user: null,
    initialized: false,
    toast: null,
});

export function setUser(user) {
    store.user = user;
}

export async function initAdminAuth() {
    if (store.initialized) return;
    try {
        const { data } = await axios.get('/api/admin/me');
        store.user = data.user ?? null;
    } catch {
        store.user = null;
    } finally {
        store.initialized = true;
    }
}

export function showToast(message, type = 'success') {
    store.toast = { message, type };
    setTimeout(() => (store.toast = null), 3000);
}
