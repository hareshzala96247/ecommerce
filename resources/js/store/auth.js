import { reactive } from 'vue';
import axios from 'axios';

export const authStore = reactive({
    user: null,
    initialized: false,
});

export async function initAuth() {
    if (authStore.initialized) return;
    try {
        const { data } = await axios.get('/api/auth/me');
        authStore.user = data.user ?? null;
    } catch {
        authStore.user = null;
    } finally {
        authStore.initialized = true;
    }
}

export async function loginUser(email, password, remember = false) {
    const { data } = await axios.post('/api/auth/login', { email, password, remember });
    authStore.user = data.user;
    return data.user;
}

export async function registerUser(name, email, password, password_confirmation) {
    const { data } = await axios.post('/api/auth/register', { name, email, password, password_confirmation });
    authStore.user = data.user;
    return data.user;
}

export async function logoutUser() {
    await axios.post('/api/auth/logout');
    authStore.user = null;
}
