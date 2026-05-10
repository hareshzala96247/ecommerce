<template>
  <div class="flex h-screen overflow-hidden" style="background:#f1f5f9;">

    <!-- Sidebar -->
    <aside class="w-64 flex flex-col flex-shrink-0" style="background:#0f172a;">
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 py-5" style="border-bottom:1px solid rgba(255,255,255,0.06);">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#0D6EFD;">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 7h13L17 13M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
          </svg>
        </div>
        <div>
          <p class="text-white font-bold text-sm leading-tight">{{ appName }}</p>
          <p class="text-xs leading-tight" style="color:#475569;">Admin Panel</p>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <router-link to="/admin/dashboard"
          :class="['nav-link', isActive('/admin/dashboard') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          Dashboard
        </router-link>

        <router-link to="/admin/products"
          :class="['nav-link', isActive('/admin/products') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          Products
        </router-link>

        <router-link to="/admin/categories"
          :class="['nav-link', isActive('/admin/categories') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
          </svg>
          Categories
        </router-link>

        <router-link to="/admin/attributes"
          :class="['nav-link', isActive('/admin/attributes') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
          </svg>
          Attributes
        </router-link>

        <router-link to="/admin/orders"
          :class="['nav-link', isActive('/admin/orders') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          Orders
        </router-link>

        <router-link to="/admin/users"
          :class="['nav-link', isActive('/admin/users') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Users
        </router-link>

        <div class="pt-3 pb-1 px-3" style="font-size:10px;font-weight:700;letter-spacing:.1em;color:#334155;text-transform:uppercase;">
          Settings
        </div>

        <router-link to="/admin/settings/general"
          :class="['nav-link', isActive('/admin/settings') ? 'nav-link-active' : '']">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          General
        </router-link>
      </nav>

      <!-- User & Logout -->
      <div class="px-3 pb-4" style="border-top:1px solid rgba(255,255,255,0.06); padding-top:12px;">
        <div class="flex items-center gap-3 px-3 py-2 mb-1">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
            style="background:#0D6EFD;">
            {{ store.user?.name?.[0]?.toUpperCase() ?? 'A' }}
          </div>
          <div class="min-w-0">
            <p class="text-white text-sm font-medium truncate leading-tight">{{ store.user?.name }}</p>
            <p class="text-xs truncate leading-tight" style="color:#475569;">Administrator</p>
          </div>
        </div>
        <button @click="logout"
          class="nav-link w-full text-left"
          style="color:#f87171;">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Logout
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top bar -->
      <header class="bg-white px-6 py-4 flex items-center justify-between flex-shrink-0"
        style="border-bottom:1px solid #e2e8f0;">
        <h1 class="text-lg font-bold" style="color:#1e293b;">{{ pageTitle }}</h1>
        <span class="text-sm" style="color:#94a3b8;">{{ today }}</span>
      </header>

      <!-- Page -->
      <main class="flex-1 overflow-y-auto p-6">
        <router-view />
      </main>
    </div>

    <!-- Toast notification -->
    <Transition name="toast">
      <div v-if="store.toast"
        class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-xl text-white text-sm font-semibold"
        :style="store.toast.type === 'success' ? 'background:#22c55e;' : 'background:#ef4444;'">
        {{ store.toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { store, setUser, showToast } from '../store';

const appName = window.APP_NAME ?? 'Admin';

const route  = useRoute();
const router = useRouter();

const pageTitles = {
    '/admin/dashboard':         'Dashboard',
    '/admin/products':          'Products',
    '/admin/attributes':        'Attributes',
    '/admin/categories':        'Categories',
    '/admin/orders':            'Orders',
    '/admin/users':             'Users',
    '/admin/settings/general':  'General Settings',
};

const pageTitle = computed(() => {
    const p = route.path;
    if (p.endsWith('/products/create'))  return 'Create Product';
    if (p.includes('/products/') && p.endsWith('/edit')) return 'Edit Product';
    if (p.endsWith('/categories/create')) return 'Create Category';
    if (p.includes('/categories/') && p.endsWith('/edit')) return 'Edit Category';
    if (/\/orders\/\d+$/.test(p)) return 'Order Details';
    return pageTitles[p] ?? 'Admin';
});

const today = computed(() =>
    new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
);

const isActive = (path) => route.path.startsWith(path);

const logout = async () => {
    try { await axios.post('/api/admin/logout'); } catch {}
    setUser(null);
    showToast('Logged out successfully.');
    router.push('/admin/login');
};
</script>

<style scoped>
@reference "../../../css/app.css";

.nav-link {
    @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer;
    color: #94a3b8;
}
.nav-link:hover {
    background: rgba(255,255,255,0.06);
    color: #e2e8f0;
}
.nav-link-active {
    background: rgba(13,110,253,0.15) !important;
    color: #60a5fa !important;
}

.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
