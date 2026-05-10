<template>
  <div class="min-h-screen flex items-center justify-center px-4"
    style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);">
    <div class="w-full max-w-sm">

      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex w-14 h-14 rounded-2xl items-center justify-center mb-4 shadow-xl"
          style="background:#0D6EFD;">
          <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 7h13L17 13M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-black text-white">{{ appName }} Admin</h1>
        <p class="text-sm mt-1.5" style="color:#94a3b8;">Sign in to your admin account</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl p-8 shadow-2xl">
        <form @submit.prevent="login" class="space-y-5">
          <div v-if="error"
            class="px-4 py-3 rounded-xl text-sm font-medium"
            style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.email
                ? 'border:1.5px solid #0D6EFD;box-shadow:0 0 0 3px rgba(13,110,253,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.email=true" @blur="focused.email=false"
              placeholder="admin@example.com"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">Password</label>
            <input v-model="form.password" type="password" required
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.password
                ? 'border:1.5px solid #0D6EFD;box-shadow:0 0 0 3px rgba(13,110,253,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.password=true" @blur="focused.password=false"
              placeholder="••••••••"
            />
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3 rounded-xl text-white text-sm font-bold transition-all duration-200 mt-2"
            :style="loading ? 'background:#93c5fd;cursor:not-allowed;' : 'background:#0D6EFD;box-shadow:0 4px 15px rgba(13,110,253,0.35);'">
            {{ loading ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>


      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { setUser } from '../store';

const appName = window.APP_NAME ?? 'Admin';

const router  = useRouter();
const form    = reactive({ email: '', password: '' });
const loading = ref(false);
const error   = ref('');
const focused = reactive({ email: false, password: false });

const login = async () => {
    loading.value = true;
    error.value   = '';
    try {
        const { data } = await axios.post('/api/admin/login', form);
        setUser(data.user);
        router.push('/admin/dashboard');
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>
