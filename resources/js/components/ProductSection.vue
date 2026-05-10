<template>
  <section id="products" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-12 gap-6">
        <div>
          <span class="section-tag">This Week</span>
          <h2 class="section-heading">{{ tabTitles[activeTab] }}</h2>
          <p class="text-gray-400 mt-1.5 text-sm">Handpicked products you'll love</p>
        </div>

        <!-- Tab switcher -->
        <div class="relative flex p-1 rounded-2xl gap-1" style="background:#F3F4F6;">
          <div class="tab-pill" :style="pillStyle"></div>
          <button
            v-for="(tab, i) in tabs"
            :key="tab.key"
            :ref="el => tabRefs[i] = el"
            @click="switchTab(tab.key, i)"
            :class="['relative z-10 px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200',
              activeTab === tab.key ? 'text-white' : 'text-gray-400 hover:text-gray-600']"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Loading skeletons -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <div v-for="n in 8" :key="n" class="rounded-[20px] animate-pulse bg-gray-100" style="height:340px;"></div>
      </div>

      <!-- Products grid -->
      <TransitionGroup v-else name="pgrid" tag="div"
        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <ProductCard
          v-for="(product, i) in filteredProducts"
          :key="product.id"
          :product="product"
          :style="`--i:${i}`"
        />
      </TransitionGroup>

      <!-- Empty state -->
      <div v-if="!loading && filteredProducts.length === 0"
        class="text-center py-20 text-gray-400">
        <div class="text-5xl mb-4">🛍️</div>
        <p class="font-medium">No products in this category yet.</p>
      </div>

      <!-- View All -->
      <div class="text-center mt-14">
        <RouterLink to="/shop"
          ref="viewAllRef"
          @mousemove="onMagnet"
          @mouseleave="resetMagnet"
          :style="magnetStyle"
          class="view-all-btn group"
        >
          <span class="relative z-10 flex items-center gap-2.5">
            View All Products
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </span>
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import ProductCard from './ProductCard.vue';
import { normalizeProduct } from '../composables/useProducts.js';

const activeTab  = ref('trending');
const tabRefs    = ref([]);
const pillLeft   = ref(4);
const pillWidth  = ref(100);
const pillColor  = ref('#0D6EFD');
const viewAllRef = ref(null);
const magnetX    = ref(0);
const magnetY    = ref(0);
const products   = ref([]);
const loading    = ref(true);

const tabs = [
  { key: 'trending',    label: 'Trending'     },
  { key: 'new',         label: 'New Arrivals' },
  { key: 'bestsellers', label: 'Best Sellers' },
];
const tabTitles = {
  trending:    'Trending Now',
  new:         'New Arrivals',
  bestsellers: 'Best Sellers',
};

const pillStyle = computed(() => ({
  left:  `${pillLeft.value}px`,
  width: `${pillWidth.value}px`,
}));

const magnetStyle = computed(() => ({
  transform: `translate(${magnetX.value}px, ${magnetY.value}px)`,
  transition: magnetX.value === 0 ? 'transform 0.4s cubic-bezier(0.23,1,0.32,1)' : 'transform 0.1s ease',
}));

const updatePill = (index) => {
  const el = tabRefs.value[index];
  if (!el) return;
  pillLeft.value  = el.offsetLeft;
  pillWidth.value = el.offsetWidth;
};

const switchTab = (key, i) => { activeTab.value = key; updatePill(i); };


const fetchProducts = async () => {
  loading.value = true;
  try {
    const res  = await fetch('/api/products');
    const json = await res.json();
    products.value = (json.data ?? []).map(normalizeProduct);
  } catch {
    products.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await fetchProducts();
  updatePill(0);
});

const filteredProducts = computed(() => products.value.filter(p => p.tab === activeTab.value));

const onMagnet = (e) => {
  const btn = viewAllRef.value;
  if (!btn) return;
  const r = btn.getBoundingClientRect();
  magnetX.value = (e.clientX - r.left - r.width  / 2) * 0.28;
  magnetY.value = (e.clientY - r.top  - r.height / 2) * 0.28;
};
const resetMagnet = () => { magnetX.value = 0; magnetY.value = 0; };
</script>

<style scoped>
@reference "../../css/app.css";

/* ── Section header ── */
.section-tag {
  @apply block text-xs font-extrabold uppercase tracking-[0.2em] mb-2;
  color: #0D6EFD;
}
.section-heading {
  @apply text-4xl font-extrabold;
  color: #0F0F1A;
}

/* ── Tab pill ── */
.tab-pill {
  @apply absolute top-1 bottom-1 rounded-xl transition-all duration-300;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 4px 14px rgba(13,110,253,0.4);
}

/* ── TransitionGroup ── */
.pgrid-enter-active {
  transition: all 0.45s cubic-bezier(0.22,1,0.36,1);
  transition-delay: calc(var(--i) * 0.06s);
}
.pgrid-leave-active {
  transition: all 0.2s ease-in;
  position: absolute;
}
.pgrid-enter-from { opacity: 0; transform: translateY(28px) scale(0.95); }
.pgrid-leave-to   { opacity: 0; transform: scale(0.94); }

/* ── View All button ── */
.view-all-btn {
  @apply relative inline-flex items-center justify-center font-bold px-10 py-4 rounded-full overflow-hidden transition-colors duration-300;
  border: 2px solid #0D6EFD;
  color: #0D6EFD;
}
.view-all-btn::before {
  content: '';
  @apply absolute inset-0 rounded-full;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
}
.view-all-btn:hover::before { transform: scaleX(1); transform-origin: left; }
.view-all-btn:hover {
  color: #fff;
  border-color: transparent;
}
</style>
