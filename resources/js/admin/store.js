import { reactive } from 'vue';

export const store = reactive({
    user: JSON.parse(localStorage.getItem('adminUser') || 'null'),
    toast: null,
});

export function setUser(user) {
    store.user = user;
    if (user) localStorage.setItem('adminUser', JSON.stringify(user));
    else localStorage.removeItem('adminUser');
}

export function showToast(message, type = 'success') {
    store.toast = { message, type };
    setTimeout(() => (store.toast = null), 3000);
}
