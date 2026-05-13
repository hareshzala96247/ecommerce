<template>
  <!-- Page header -->
  <section class="shop-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-4" style="color:#A8A8A8;">
        <RouterLink to="/" class="hover:text-gray-900 transition-colors">Home</RouterLink>
        <span>/</span>
        <span style="color:#1A1A1A;">Shop</span>
      </nav>
      <h1 class="text-3xl sm:text-4xl font-bold" style="color:#1A1A1A;">All Products</h1>
      <p class="text-sm mt-2" style="color:#6B6B6B;">
        Discover {{ totalCount > 0 ? totalCount + ' curated' : 'our' }} products across every category
      </p>
    </div>
  </section>

  <!-- Filters + Grid -->
  <section class="py-10 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Filter bar -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
          </svg>
          <input
            v-model="searchInput"
            type="text"
            placeholder="Search products..."
            class="search-input"
            @input="onSearchInput"
          />
          <button v-if="searchInput" @click="clearSearch"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Sort -->
        <select v-model="sort" @change="applyFilters(true)" class="sort-select">
          <option value="latest">Latest</option>
          <option value="price_asc">Price: Low → High</option>
          <option value="price_desc">Price: High → Low</option>
          <option value="name">Name A–Z</option>
        </select>
      </div>

      <!-- Category pills -->
      <div class="flex flex-wrap gap-2 mb-8">
        <RouterLink to="/shop" class="cat-pill cat-pill-active">All</RouterLink>
        <RouterLink
          v-for="cat in categories"
          :key="cat.id"
          :to="`/category/${cat.slug}`"
          class="cat-pill">
          <span>{{ cat.icon || '🛍️' }}</span>
          {{ cat.name }}
          <span class="cat-count">{{ cat.products_count }}</span>
        </RouterLink>
      </div>

      <!-- Results info row -->
      <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">
          <span v-if="!loading">Showing <strong class="text-gray-800">{{ products.length }}</strong> of <strong class="text-gray-800">{{ totalCount }}</strong> products</span>
          <span v-else class="text-gray-400">Loading products…</span>
        </p>
        <button v-if="hasActiveFilter" @click="clearAll" class="text-sm text-blue-600 hover:underline font-medium">
          Clear filters
        </button>
      </div>

      <!-- Skeleton -->
      <div v-if="loading && products.length === 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <div v-for="n in 8" :key="n" class="skeleton-card"></div>
      </div>

      <!-- Product grid -->
      <div v-else-if="products.length > 0"
        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <ProductCard
          v-for="(product, i) in products"
          :key="product.id"
          :product="product"
          :style="`--i:${i % 12}`"
          class="shop-card-enter"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-24">
        <div class="text-6xl mb-5">🔍</div>
        <h3 class="text-xl font-bold mb-2" style="color:#1A1A1A;">No products found</h3>
        <p class="text-gray-400 mb-6">Try a different search or clear your filters.</p>
        <button @click="clearAll" class="btn-primary-sm">Browse all products</button>
      </div>

      <!-- Load more -->
      <div v-if="currentPage < lastPage && !loading" class="text-center mt-12">
        <button @click="loadMore" :disabled="loadingMore" class="load-more-btn">
          <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          {{ loadingMore ? 'Loading…' : `Load More · ${totalCount - products.length} remaining` }}
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import { normalizeProduct } from '../composables/useProducts.js';

const route  = useRoute();
const router = useRouter();

const products      = ref([]);
const categories    = ref([]);
const loading       = ref(true);
const loadingMore   = ref(false);
const currentPage   = ref(1);
const lastPage      = ref(1);
const totalCount    = ref(0);

const searchInput   = ref('');
const searchQuery   = ref('');
const sort          = ref('latest');

let searchTimer = null;

const hasActiveFilter = computed(() => !!searchQuery.value);

onMounted(async () => {
  const q = route.query;
  if (q.search) { searchInput.value = q.search; searchQuery.value = q.search; }
  if (q.sort)   sort.value = q.sort;

  await Promise.all([fetchCategories(), fetchProducts(true)]);
});

watch(() => route.query, (q) => {
  const newSearch = q.search ?? '';
  const newSort   = q.sort   ?? 'latest';
  if (newSearch !== searchQuery.value || newSort !== sort.value) {
    searchInput.value = newSearch;
    searchQuery.value = newSearch;
    sort.value        = newSort;
    fetchProducts(true);
  }
});

async function fetchCategories() {
  try {
    const res  = await fetch('/api/categories');
    const json = await res.json();
    categories.value = json.data ?? [];
  } catch { categories.value = []; }
}

async function fetchProducts(reset = false) {
  if (reset) {
    loading.value = true;
    currentPage.value = 1;
    products.value    = [];
  } else {
    loadingMore.value = true;
  }

  try {
    const params = new URLSearchParams({ page: currentPage.value, per_page: 12, sort: sort.value });
    if (searchQuery.value) params.set('search', searchQuery.value);

    const res  = await fetch('/api/products?' + params);
    const json = await res.json();

    const normalized = (json.data ?? []).map((p, i) =>
      normalizeProduct(p, (reset ? 0 : products.value.length) + i)
    );

    products.value  = reset ? normalized : [...products.value, ...normalized];
    currentPage.value = json.current_page ?? 1;
    lastPage.value    = json.last_page    ?? 1;
    totalCount.value  = json.total        ?? normalized.length;
  } catch {
    if (reset) products.value = [];
  } finally {
    loading.value    = false;
    loadingMore.value = false;
  }
}

function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    searchQuery.value = searchInput.value;
    applyFilters(true);
  }, 400);
}

function clearSearch() {
  searchInput.value = '';
  searchQuery.value = '';
  applyFilters(true);
}

function clearAll() {
  searchInput.value = '';
  searchQuery.value = '';
  sort.value        = 'latest';
  applyFilters(true);
}

function applyFilters(reset = false) {
  const query = {};
  if (searchQuery.value)       query.search = searchQuery.value;
  if (sort.value !== 'latest') query.sort   = sort.value;
  router.replace({ path: '/shop', query });
  fetchProducts(reset);
}

function loadMore() {
  currentPage.value++;
  fetchProducts(false);
}
</script>

<style scoped>
@reference "../../css/app.css";

/* ── Hero ── */
.shop-hero {
  @apply relative;
  background: #fff;
  border-bottom: 1px solid #F0EDE6;
}

/* ── Search input ── */
.search-input {
  @apply w-full pl-11 pr-10 py-3 text-sm rounded-2xl outline-none transition-all duration-300;
  background: #F0EDE6;
  border: 1.5px solid transparent;
  color: #1A1A1A;
}
.search-input::placeholder { color: #A8A8A8; }
.search-input:focus {
  background: #fff;
  border-color: #1D3FB8;
  box-shadow: 0 0 0 3px rgba(29,63,184,0.1);
}

/* ── Sort select ── */
.sort-select {
  @apply px-4 py-3 text-sm rounded-2xl outline-none transition-all duration-200 cursor-pointer font-medium;
  background: #F0EDE6;
  border: 1.5px solid transparent;
  color: #3F3F3F;
}
.sort-select:focus { border-color: #1D3FB8; background: #fff; }

/* ── Category pills ── */
.cat-pill {
  @apply inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200;
  background: #F0EDE6;
  color: #6B6B6B;
  border: 1.5px solid transparent;
}
.cat-pill:hover { background: #E4E0D8; color: #3F3F3F; }
.cat-pill-active {
  background: #1A1A1A;
  color: #fff;
  border-color: transparent;
}
.cat-count {
  @apply text-[10px] font-bold px-1.5 py-0.5 rounded-full;
  background: rgba(0,0,0,0.1);
}
.cat-pill-active .cat-count { background: rgba(255,255,255,0.25); }

/* ── Skeleton ── */
.skeleton-card {
  @apply rounded-[20px] bg-gray-100 animate-pulse;
  height: 340px;
}

/* ── Buttons ── */
.btn-primary-sm {
  @apply inline-flex items-center gap-2 font-semibold px-6 py-3 rounded-xl text-white text-sm transition-all duration-200;
  background: #1D3FB8;
}
.btn-primary-sm:hover { background: #16358F; }

.load-more-btn {
  @apply inline-flex items-center gap-2.5 font-semibold px-8 py-3.5 rounded-xl text-sm transition-all duration-200 disabled:opacity-60;
  border: 1.5px solid #E4E0D8;
  color: #3F3F3F;
  background: #fff;
}
.load-more-btn:not(:disabled):hover {
  border-color: #1D3FB8;
  color: #1D3FB8;
}
</style>
