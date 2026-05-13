<template>
  <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-sm">

      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex w-12 h-12 rounded-xl items-center justify-center mb-4"
          style="background:#1A1A1A;">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold" style="color:#1A1A1A;">Welcome back</h1>
        <p class="text-sm mt-1.5" style="color:#6B6B6B;">Sign in to your {{ settings.site_name }} account</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl p-8 shadow-xl" style="border:1px solid rgba(0,0,0,0.06);">
        <form @submit.prevent="handleLogin" class="space-y-5">

          <div v-if="error"
            class="px-4 py-3 rounded-xl text-sm font-medium"
            style="background:#F4ECE6;color:#7A2412;border:1px solid #fecaca;">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#3F3F3F;">Email address</label>
            <input v-model="form.email" type="email" required autocomplete="email"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.email
                ? 'border:1.5px solid #1D3FB8;box-shadow:0 0 0 3px rgba(29,63,184,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.email=true" @blur="focused.email=false"
              placeholder="you@example.com"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-sm font-semibold" style="color:#3F3F3F;">Password</label>
            </div>
            <input v-model="form.password" type="password" required autocomplete="current-password"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.password
                ? 'border:1.5px solid #1D3FB8;box-shadow:0 0 0 3px rgba(29,63,184,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.password=true" @blur="focused.password=false"
              placeholder="••••••••"
            />
          </div>

          <label class="flex items-center gap-2.5 cursor-pointer">
            <input v-model="form.remember" type="checkbox"
              class="w-4 h-4 rounded accent-blue-600" />
            <span class="text-sm" style="color:#6B6B6B;">Remember me</span>
          </label>

          <button type="submit" :disabled="loading"
            class="w-full py-3 rounded-xl text-white text-sm font-semibold transition-all duration-200"
            :style="loading
              ? 'background:#93c5fd;cursor:not-allowed;'
              : 'background:#1D3FB8;'">
            {{ loading ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>

        <p class="text-center text-sm mt-6" style="color:#6B6B6B;">
          Don't have an account?
          <RouterLink to="/register" class="font-semibold" style="color:#1D3FB8;">Create one</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { RouterLink } from 'vue-router';
import { loginUser } from '../store/auth';
import { settings } from '../store/settings';

const router  = useRouter();
const loading = ref(false);
const error   = ref('');
const focused = reactive({ email: false, password: false });
const form    = reactive({ email: '', password: '', remember: false });

const handleLogin = async () => {
    loading.value = true;
    error.value   = '';
    try {
        await loginUser(form.email, form.password, form.remember);
        router.push('/account');
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>
