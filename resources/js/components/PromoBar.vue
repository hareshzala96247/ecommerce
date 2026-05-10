<template>
  <div v-if="!closed" class="promo-bar relative overflow-hidden select-none" style="height:38px;">
    <div class="h-full flex items-center">
      <!-- Scrolling content -->
      <div class="flex-1 overflow-hidden h-full flex items-center">
        <div class="track flex items-center">
          <template v-for="n in 3" :key="n">
            <span v-for="item in items" :key="item.text + n"
              class="inline-flex items-center gap-2 px-8 shrink-0 whitespace-nowrap">
              <span class="text-sm">{{ item.icon }}</span>
              <span class="text-[11px] font-semibold tracking-wide text-white/90">{{ item.text }}</span>
              <span class="text-white/20 ml-6">✦</span>
            </span>
          </template>
        </div>
      </div>
      <!-- Close -->
      <!-- <button @click="closed = true"
        class="flex-shrink-0 w-9 h-full flex items-center justify-center text-white/40 hover:text-white transition-colors duration-200">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button> -->
    </div>
    <div class="absolute inset-0 pointer-events-none shimmer-overlay"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const closed = ref(false);
const items = [
  { icon: '🚚', text: 'Free shipping on orders over $50' },
  { icon: '↩️', text: 'Easy 30-day returns' },
  { icon: '🔒', text: '100% Secure payments' },
  { icon: '🎁', text: 'Free gift wrapping' },
  { icon: '⚡', text: 'Same-day dispatch before 2PM' },
];
</script>

<style scoped>
.promo-bar {
  background: linear-gradient(90deg, #06061A 0%, #0D6EFD 35%, #7C3AED 65%, #06061A 100%);
  background-size: 300% 100%;
  animation: bg-shift 12s ease-in-out infinite alternate;
}
@keyframes bg-shift {
  from { background-position: 0% 50%; }
  to   { background-position: 100% 50%; }
}
.track {
  animation: marquee 32s linear infinite;
}
@keyframes marquee {
  from { transform: translateX(0); }
  to   { transform: translateX(-33.333%); }
}
.shimmer-overlay {
  background: linear-gradient(90deg, transparent 20%, rgba(255,255,255,0.08) 50%, transparent 80%);
  animation: shimmer 4s ease-in-out infinite;
}
@keyframes shimmer {
  from { transform: translateX(-100%); }
  to   { transform: translateX(200%); }
}
</style>
