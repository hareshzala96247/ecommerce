<template>
  <div class="min-h-[calc(100vh-120px)] px-4 py-12">
    <div class="max-w-2xl mx-auto">

      <!-- Loading -->
      <div v-if="!authStore.initialized" class="flex justify-center py-24">
        <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <!-- Not logged in -->
      <div v-else-if="!authStore.user" class="text-center py-24">
        <div class="inline-flex w-16 h-16 rounded-2xl items-center justify-center mb-6"
          style="background:#F5F5F7;">
          <svg class="w-8 h-8" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">Sign in to your account</h2>
        <p class="text-sm mb-6" style="color:#6b7280;">Access your orders, wishlist, and profile settings.</p>
        <div class="flex gap-3 justify-center">
          <RouterLink to="/login"
            class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-200"
            style="background:#0D6EFD; box-shadow:0 4px 12px rgba(13,110,253,0.3);">
            Sign In
          </RouterLink>
          <RouterLink to="/register"
            class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200"
            style="border:1.5px solid #e5e7eb; color:#374151;">
            Create Account
          </RouterLink>
        </div>
      </div>

      <!-- Logged in -->
      <template v-else>
        <!-- Profile header -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-4" style="border:1px solid rgba(0,0,0,0.06);">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black text-white flex-shrink-0"
              style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);">
              {{ authStore.user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <h1 class="text-xl font-black truncate" style="color:#0F0F1A;">{{ authStore.user.name }}</h1>
              <p class="text-sm truncate" style="color:#6b7280;">{{ authStore.user.email }}</p>
              <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                style="background:rgba(13,110,253,0.1);color:#0D6EFD;">
                Customer
              </span>
            </div>
            <button @click="handleLogout" :disabled="loggingOut"
              class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex-shrink-0"
              style="border:1.5px solid #fee2e2;color:#ef4444;"
              @mouseenter="(e) => e.currentTarget.style.background='#fee2e2'"
              @mouseleave="(e) => e.currentTarget.style.background='transparent'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
              </svg>
              {{ loggingOut ? 'Signing out…' : 'Sign Out' }}
            </button>
          </div>
        </div>

        <!-- Quick links -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <RouterLink v-for="item in quickLinks" :key="item.label" :to="item.to"
            class="bg-white rounded-2xl p-5 flex flex-col gap-3 transition-all duration-200 group"
            style="border:1px solid rgba(0,0,0,0.06);"
            @mouseenter="(e) => e.currentTarget.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"
            @mouseleave="(e) => e.currentTarget.style.boxShadow='none'">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="{ background: item.bg }">
              <component :is="item.icon" class="w-5 h-5" :style="{ color: item.color }" />
            </div>
            <div>
              <p class="text-sm font-bold" style="color:#0F0F1A;">{{ item.label }}</p>
              <p class="text-[12px]" style="color:#9ca3af;">{{ item.desc }}</p>
            </div>
          </RouterLink>
        </div>

        <!-- Account info -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mt-4" style="border:1px solid rgba(0,0,0,0.06);">
          <h2 class="text-base font-bold mb-4" style="color:#0F0F1A;">Account Details</h2>
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2.5"
              style="border-bottom:1px solid #F3F4F6;">
              <span class="text-sm" style="color:#6b7280;">Full Name</span>
              <span class="text-sm font-semibold" style="color:#0F0F1A;">{{ authStore.user.name }}</span>
            </div>
            <div class="flex items-center justify-between py-2.5"
              style="border-bottom:1px solid #F3F4F6;">
              <span class="text-sm" style="color:#6b7280;">Email</span>
              <span class="text-sm font-semibold" style="color:#0F0F1A;">{{ authStore.user.email }}</span>
            </div>
            <div class="flex items-center justify-between py-2.5">
              <span class="text-sm" style="color:#6b7280;">Member Since</span>
              <span class="text-sm font-semibold" style="color:#0F0F1A;">{{ joinDate }}</span>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { authStore, logoutUser } from '../store/auth';

const router     = useRouter();
const loggingOut = ref(false);

const joinDate = computed(() => {
    if (!authStore.user?.created_at) return '—';
    return new Date(authStore.user.created_at).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
    });
});

const handleLogout = async () => {
    loggingOut.value = true;
    try {
        await logoutUser();
        router.push('/');
    } finally {
        loggingOut.value = false;
    }
};

const ShoppingBagIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2',
                d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' }),
        ]);
    },
};
const HeartIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2',
                d: 'M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z' }),
        ]);
    },
};
const TagIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2',
                d: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z' }),
        ]);
    },
};

const quickLinks = [
    { label: 'My Orders', desc: 'Track your orders', icon: ShoppingBagIcon, bg: 'rgba(13,110,253,0.08)', color: '#0D6EFD', to: '/orders' },
    { label: 'Wishlist',  desc: 'Saved items',        icon: HeartIcon,       bg: 'rgba(239,68,68,0.08)',  color: '#ef4444', to: '/shop'   },
    { label: 'Deals',     desc: 'Exclusive offers',   icon: TagIcon,         bg: 'rgba(34,197,94,0.08)',  color: '#22c55e', to: '/shop'   },
];
</script>
