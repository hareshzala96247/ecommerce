<template>
  <section id="categories" class="py-24" style="background:#FAFAFA;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Header -->
      <div class="text-center mb-14">
        <span class="section-tag">Browse</span>
        <h2 class="section-heading">Shop by Category</h2>
        <p class="section-sub">Explore our handpicked collections across every lifestyle</p>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div v-for="n in 6" :key="n" class="rounded-2xl animate-pulse bg-gray-200" style="height:160px;"></div>
      </div>

      <!-- Category grid -->
      <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <RouterLink
          v-for="(cat, i) in categories"
          :key="cat.id ?? cat.name"
          :to="`/category/${cat.slug}`"
          class="cat-card group"
          :style="`--gradient: ${gradients[i % gradients.length]}; animation-delay:${i * 0.07}s;`"
        >
          <!-- Gradient background that reveals on hover -->
          <div class="cat-bg"></div>

          <!-- Content -->
          <div class="relative z-10 flex flex-col items-center gap-3 p-6">
            <div class="cat-icon-wrap group-hover:cat-icon-active">
              <span class="text-3xl">{{ cat.icon || '🛍️' }}</span>
            </div>
            <span class="text-sm font-bold text-center leading-tight transition-colors duration-300" style="color:#0F0F1A;">
              {{ cat.name }}
            </span>
            <span class="text-[11px] font-medium transition-colors duration-300 text-gray-400">
              {{ formatCount(cat.products_count) }} items
            </span>
            <!-- Arrow appears on hover -->
            <div class="h-4 flex items-center">
              <span class="text-[11px] font-bold flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300 -translate-y-1 group-hover:translate-y-0"
                style="color:#0D6EFD;">
                Explore
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </span>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';

const categories = ref([]);
const loading    = ref(true);

const gradients = [
  'linear-gradient(135deg, #667EEA 0%, #764BA2 100%)',
  'linear-gradient(135deg, #F093FB 0%, #F5576C 100%)',
  'linear-gradient(135deg, #4FACFE 0%, #00F2FE 100%)',
  'linear-gradient(135deg, #43E97B 0%, #38F9D7 100%)',
  'linear-gradient(135deg, #FA709A 0%, #FEE140 100%)',
  'linear-gradient(135deg, #A18CD1 0%, #FBC2EB 100%)',
];

const formatCount = (n) => {
  if (!n) return '0';
  return n >= 1000 ? (n / 1000).toFixed(1).replace('.0', '') + 'k' : String(n);
};

onMounted(async () => {
  try {
    const res  = await fetch('/api/categories');
    const json = await res.json();
    categories.value = json.data ?? [];
  } catch {
    categories.value = [];
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
@reference "../../css/app.css";

/* ── Section header ── */
.section-tag {
  @apply inline-block text-xs font-bold uppercase tracking-[0.2em] mb-3;
  color: #0D6EFD;
}
.section-heading {
  @apply text-4xl font-extrabold mb-3;
  color: #0F0F1A;
}
.section-sub {
  @apply text-gray-400 text-base;
}

/* ── Category cards ── */
@keyframes fade-up {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

.cat-card {
  @apply relative block rounded-2xl overflow-hidden cursor-pointer;
  background: #ffffff;
  border: 1.5px solid #EBEBEB;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  transition: transform 0.35s cubic-bezier(0.22,1,0.36,1), box-shadow 0.35s ease, border-color 0.3s ease;
  animation: fade-up 0.55s ease both;
}
.cat-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 24px 50px -12px rgba(0,0,0,0.12);
  border-color: transparent;
}

.cat-bg {
  position: absolute;
  inset: 0;
  background: var(--gradient);
  opacity: 0;
  transition: opacity 0.35s ease;
}
.cat-card:hover .cat-bg { opacity: 1; }

/* Invert text color on hover */
.cat-card:hover span.text-sm { color: #ffffff !important; }
.cat-card:hover span.text-gray-400 { color: rgba(255,255,255,0.7) !important; }

.cat-icon-wrap {
  @apply w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-400;
  background: rgba(0,0,0,0.04);
}
.cat-card:hover .cat-icon-wrap {
  background: rgba(255,255,255,0.2);
  transform: scale(1.1) rotate(-6deg);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.cat-card:hover .cat-icon-wrap span {
  filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
  animation: emoji-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes emoji-pop {
  from { transform: scale(0.7); }
  to   { transform: scale(1); }
}
</style>
