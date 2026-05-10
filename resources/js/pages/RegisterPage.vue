<template>
  <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-sm">

      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex w-14 h-14 rounded-2xl items-center justify-center mb-4"
          style="background:linear-gradient(135deg,#7C3AED,#0D6EFD); box-shadow:0 8px 24px rgba(124,58,237,0.3);">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-black" style="color:#0F0F1A;">Create account</h1>
        <p class="text-sm mt-1.5" style="color:#6b7280;">Join {{ settings.site_name }} and start shopping</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl p-8 shadow-xl" style="border:1px solid rgba(0,0,0,0.06);">
        <form @submit.prevent="handleRegister" class="space-y-4">

          <div v-if="error"
            class="px-4 py-3 rounded-xl text-sm font-medium"
            style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Full name</label>
            <input v-model="form.name" type="text" required autocomplete="name"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.name
                ? 'border:1.5px solid #7C3AED;box-shadow:0 0 0 3px rgba(124,58,237,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.name=true" @blur="focused.name=false"
              placeholder="John Smith"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Email address</label>
            <input v-model="form.email" type="email" required autocomplete="email"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.email
                ? 'border:1.5px solid #7C3AED;box-shadow:0 0 0 3px rgba(124,58,237,0.1);'
                : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.email=true" @blur="focused.email=false"
              placeholder="you@example.com"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Password</label>
            <input v-model="form.password" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="focused.password
                ? 'border:1.5px solid #7C3AED;box-shadow:0 0 0 3px rgba(124,58,237,0.1);'
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
            <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Confirm password</label>
            <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all"
              :style="pwMismatch
                ? 'border:1.5px solid #ef4444;'
                : focused.confirm
                  ? 'border:1.5px solid #7C3AED;box-shadow:0 0 0 3px rgba(124,58,237,0.1);'
                  : 'border:1.5px solid #e5e7eb;'"
              @focus="focused.confirm=true" @blur="focused.confirm=false"
              placeholder="••••••••"
            />
            <p v-if="pwMismatch" class="text-[11px] mt-1 text-red-500 font-medium">Passwords do not match</p>
          </div>

          <button type="submit" :disabled="loading || pwMismatch"
            class="w-full py-3 rounded-xl text-white text-sm font-bold transition-all duration-200 mt-2"
            :style="(loading || pwMismatch)
              ? 'background:#c4b5fd;cursor:not-allowed;'
              : 'background:linear-gradient(135deg,#7C3AED,#0D6EFD);box-shadow:0 4px 15px rgba(124,58,237,0.35);'">
            {{ loading ? 'Creating account…' : 'Create Account' }}
          </button>
        </form>

        <p class="text-center text-sm mt-6" style="color:#6b7280;">
          Already have an account?
          <RouterLink to="/login" class="font-semibold" style="color:#0D6EFD;">Sign in</RouterLink>
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
    const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#22c55e'];
    return { score, label: labels[score] || 'Weak', color: colors[score] || '#ef4444' };
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
