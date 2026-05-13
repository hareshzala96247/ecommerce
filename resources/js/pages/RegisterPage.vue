<template>
  <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-sm">

      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex w-12 h-12 rounded-xl items-center justify-center mb-4"
          style="background:#1A1A1A;">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold" style="color:#1A1A1A;">Create account</h1>
        <p class="text-sm mt-1.5" style="color:#6B6B6B;">Join {{ settings.site_name }} and start shopping</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl p-8 shadow-xl" style="border:1px solid rgba(0,0,0,0.06);">
        <form @submit.prevent="handleRegister" class="space-y-4">

          <div v-if="error"
            class="px-4 py-3 rounded-xl text-sm font-medium"
            style="background:#F4ECE6;color:#7A2412;border:1px solid #fecaca;">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#3F3F3F;">Full name</label>
            <input v-model="form.name" type="text" required autocomplete="name"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.name
                ? 'border:1.5px solid #1D3FB8;box-shadow:0 0 0 3px rgba(29,63,184,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.name=true" @blur="focused.name=false"
              placeholder="John Smith"
            />
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
            <label class="block text-sm font-semibold mb-1.5" style="color:#3F3F3F;">Password</label>
            <input v-model="form.password" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.password
                ? 'border:1.5px solid #1D3FB8;box-shadow:0 0 0 3px rgba(29,63,184,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.password=true" @blur="focused.password=false"
              placeholder="Min. 8 characters"
            />
            <!-- Strength indicator -->
            <div v-if="form.password" class="flex gap-1 mt-2">
              <div v-for="n in 4" :key="n" class="h-1 flex-1 rounded-full transition-all duration-300"
                :style="{ background: n <= strength.score ? strength.color : '#e5e7eb' }"></div>
            </div>
            <p v-if="form.password" class="text-[11px] mt-1 font-medium" :style="{ color: strength.color }">
              {{ strength.label }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#3F3F3F;">Confirm password</label>
            <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="pwMismatch
                ? 'border:1.5px solid #A4351A;'
                : focused.confirm
                  ? 'border:1.5px solid #1D3FB8;box-shadow:0 0 0 3px rgba(29,63,184,0.1);'
                  : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.confirm=true" @blur="focused.confirm=false"
              placeholder="••••••••"
            />
            <p v-if="pwMismatch" class="text-[11px] mt-1 text-red-500 font-medium">Passwords do not match</p>
          </div>

          <button type="submit" :disabled="loading || pwMismatch"
            class="w-full py-3 rounded-xl text-white text-sm font-semibold transition-all duration-200 mt-2"
            :style="(loading || pwMismatch)
              ? 'background:#93c5fd;cursor:not-allowed;'
              : 'background:#1D3FB8;'">
            {{ loading ? 'Creating account…' : 'Create Account' }}
          </button>
        </form>

        <p class="text-center text-sm mt-6" style="color:#6B6B6B;">
          Already have an account?
          <RouterLink to="/login" class="font-semibold" style="color:#1D3FB8;">Sign in</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { registerUser } from '../store/auth';
import { settings } from '../store/settings';

const router  = useRouter();
const loading = ref(false);
const error   = ref('');
const focused = reactive({ name: false, email: false, password: false, confirm: false });
const form    = reactive({ name: '', email: '', password: '', password_confirmation: '' });

const pwMismatch = computed(() =>
    form.password_confirmation.length > 0 && form.password !== form.password_confirmation
);

const strength = computed(() => {
    const p = form.password;
    let score = 0;
    if (p.length >= 8)  score++;
    if (/[A-Z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['', '#A4351A', '#f59e0b', '#3b82f6', '#22c55e'];
    return { score, label: labels[score] || 'Weak', color: colors[score] || '#A4351A' };
});

const handleRegister = async () => {
    if (pwMismatch.value) return;
    loading.value = true;
    error.value   = '';
    try {
        await registerUser(form.name, form.email, form.password, form.password_confirmation);
        router.push('/account');
    } catch (e) {
        const errors = e.response?.data?.errors;
        if (errors) {
            error.value = Object.values(errors).flat().join(' ');
        } else {
            error.value = e.response?.data?.message ?? 'Registration failed. Please try again.';
        }
    } finally {
        loading.value = false;
    }
};
</script>
