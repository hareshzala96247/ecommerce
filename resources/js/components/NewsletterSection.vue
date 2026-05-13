<template>
  <section class="nl-section relative">

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="nl-card">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

          <!-- LEFT: Text -->
          <div class="flex-1 text-center lg:text-left">
            <div class="nl-icon-wrap">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mt-6 mb-4 leading-tight">
              Join our community
            </h2>
            <p class="text-blue-200/70 text-base leading-relaxed mb-8 max-w-sm mx-auto lg:mx-0">
              Get exclusive deals, early access to new arrivals, and weekly style inspiration delivered straight to your inbox.
            </p>

            <!-- Benefits list -->
            <ul class="space-y-2.5 text-sm inline-block text-left">
              <li v-for="b in benefits" :key="b" class="flex items-center gap-2.5 text-blue-100/80">
                <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center" style="background:rgba(47,125,90,0.2);">
                  <svg class="w-3 h-3" style="color:#2F7D5A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                  </svg>
                </span>
                {{ b }}
              </li>
            </ul>
          </div>

          <!-- RIGHT: Form -->
          <div class="flex-1 w-full max-w-md lg:max-w-none">
            <div class="nl-form-card">

              <!-- Social proof -->
              <div class="flex items-center gap-3 mb-6">
                <div class="flex -space-x-2">
                  <div v-for="(c, i) in avatarColors" :key="i"
                    class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold text-white"
                    :style="`background:${c}; z-index:${5 - i};`">
                    {{ avatarLetters[i] }}
                  </div>
                </div>
                <p class="text-sm" style="color:#3F3F3F;">
                  <span class="font-bold" style="color:#1A1A1A;">25,000+</span> subscribers already
                </p>
              </div>

              <h3 class="text-xl font-bold mb-5" style="color:#1A1A1A;">Get 10% off your first order</h3>

              <form @submit.prevent="subscribe" class="space-y-3">
                <div class="relative">
                  <input
                    v-model="email"
                    type="email"
                    placeholder="Enter your email address"
                    required
                    class="nl-input"
                    :class="focused ? 'nl-input-focus' : ''"
                    @focus="focused = true"
                    @blur="focused = false"
                  />
                </div>
                <button type="submit" :disabled="subscribed" class="nl-submit" :class="subscribed ? 'nl-submit-done' : ''">
                  <Transition name="btn-swap" mode="out-in">
                    <span v-if="!subscribed" key="a" class="flex items-center justify-center gap-2">
                      Get My Discount
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                      </svg>
                    </span>
                    <span v-else key="b" class="flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                      </svg>
                      You're in! Check your inbox
                    </span>
                  </Transition>
                </button>
              </form>

              <p class="flex items-center gap-1.5 mt-4 text-xs" style="color:#A8A8A8;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                No spam ever. Unsubscribe with one click.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const email      = ref('');
const subscribed = ref(false);
const focused    = ref(false);

const benefits = [
  'Exclusive members-only deals',
  'Early access to new arrivals',
  'Weekly style tips & lookbooks',
];

const avatarColors  = ['#1D3FB8', '#1D3FB8', '#B5532C', '#2F7D5A'];
const avatarLetters = ['A', 'S', 'J', 'M'];

const subscribe = () => {
  if (!email.value) return;
  subscribed.value = true;
  setTimeout(() => { subscribed.value = false; email.value = ''; }, 4000);
};

</script>

<style scoped>
@reference "../../css/app.css";

/* ── Section ── */
.nl-section { background: #1A1A1A; }

/* ── Glass card wrapper ── */
.nl-card {
  @apply rounded-3xl p-8 sm:p-12;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
  box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5);
}

/* ── Left side ── */
.nl-icon-wrap {
  @apply w-12 h-12 rounded-xl flex items-center justify-center;
  background: #1D3FB8;
}

/* ── Right form card ── */
.nl-form-card {
  @apply rounded-2xl p-8;
  background: #ffffff;
  box-shadow: 0 24px 60px rgba(0,0,0,0.25);
}

.nl-input {
  @apply w-full px-4 py-3.5 rounded-xl text-sm text-gray-800 outline-none transition-all duration-300;
  border: 1.5px solid #E4E0D8;
  background: #FAFAF7;
}
.nl-input::placeholder { color: #A8A8A8; }
.nl-input-focus {
  border-color: #1D3FB8 !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(29,63,184,0.1);
}

.nl-submit {
  @apply w-full py-3.5 rounded-xl font-semibold text-sm text-white transition-all duration-200;
  background: #1D3FB8;
}
.nl-submit:hover:not(:disabled) { background: #16358F; }
.nl-submit-done { background: #2F7D5A !important; }

.btn-swap-enter-active, .btn-swap-leave-active { transition: all 0.2s ease; }
.btn-swap-enter-from { opacity: 0; transform: translateY(8px); }
.btn-swap-leave-to   { opacity: 0; transform: translateY(-8px); }
</style>
