<template>
  <!-- Hero -->
  <section class="cat-hero">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div class="ch-orb ch-orb-1"></div>
      <div class="ch-orb ch-orb-2"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <nav class="flex items-center gap-2 text-sm mb-5 text-white/50">
        <RouterLink to="/" class="hover:text-white transition-colors">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/shop" class="hover:text-white transition-colors">Shop</RouterLink>
        <span>/</span>
        <span class="text-white/80">{{ category?.name ?? 'Category' }}</span>
      </nav>

      <!-- Category meta skeleton -->
      <div v-if="catLoading" class="space-y-2">
        <div class="h-10 w-56 rounded-xl bg-white/10 animate-pulse"></div>
        <div class="h-5 w-72 rounded-lg bg-white/10 animate-pulse"></div>
      </div>

      <template v-else-if="category">
        <div class="flex items-center gap-4 mb-2">
          <span class="text-4xl">{{ category.icon || '🛍️' }}</span>
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white">{{ category.name }}</h1>
        </div>
        <p class="text-white/60 text-base">
          {{ category.products_count }} product{{ category.products_count !== 1 ? 's' : '' }} in this category
        </p>
      </template>

      <!-- Not found -->
      <div v-else class="text-white/80 text-xl font-bold">Category not found</div>
    </div>
  </section>

  <!-- Products section -->
  <section class="py-10 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Filter bar -->
      <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <div class="relative flex-1 max-w-md">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
          </svg>
          <input
            v-model="searchInput"
            type="text"
            placeholder="Search in this category..."
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

        <select v-model="sort" @change="fetchProducts(true)" class="sort-select">
          <option value="latest">Latest</option>
          <option value="price_asc">Price: Low → High</option>
          <option value="price_desc">Price: High → Low</option>
          <option value="name">Name A–Z</option>
        </select>
      </div>

      <!-- Results info -->
      <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">
          <span v-if="!loading">
            Showing <strong class="text-gray-800">{{ products.length }}</strong>
            of <strong class="text-gray-800">{{ totalCount }}</strong> products
          </span>
          <span v-else class="text-gray-400">Loading products…</span>
        </p>
        <button v-if="searchInput" @click="clearSearch" class="text-sm text-blue-600 hover:underline font-medium">
          Clear search
        </button>
      </div>

      <!-- Skeleton -->
      <div v-if="loading && products.length === 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <div v-for="n in 8" :key="n" class="skeleton-card"></div>
      </div>

      <!-- Grid -->
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

      <!-- Empty -->
      <div v-else class="text-center py-24">
        <div class="text-6xl mb-5">🔍</div>
        <h3 class="text-xl font-bold mb-2" style="color:#0F0F1A;">No products found</h3>
        <p class="text-gray-400 mb-6">
          {{ searchInput ? 'Try a different search term.' : 'This category has no active products yet.' }}
        </p>
        <RouterLink v-if="searchInput" @click.prevent="clearSearch"
          to="#" class="btn-primary-sm">Clear search</RouterLink>
        <RouterLink v-else to="/shop" class="btn-primary-sm">Browse all products</RouterLink>
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
import { ref, watch, onMounted } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import { settings } from '../store/settings';
import { normalizeProduct } from '../composables/useProducts.js';

const route  = useRoute();
const router = useRouter();

const category   = ref(null);
const catLoading = ref(true);

const products    = ref([]);
const loading     = ref(true);
const loadingMore = ref(false);
const currentPage = ref(1);
const lastPage    = ref(1);
const totalCount  = ref(0);

const searchInput = ref('');
const sort        = ref('latest');
let   searchTimer = null;

onMounted(() => {
  loadAll();
});

watch(() => route.params.slug, (newSlug, oldSlug) => {
  if (newSlug && newSlug !== oldSlug) loadAll();
});

async function loadAll() {
  catLoading.value = true;
  category.value   = null;
  searchInput.value = '';
  sort.value = 'latest';

  try {
    const res  = await fetch(`/api/categories/${route.params.slug}`);
    if (!res.ok) { catLoading.value = false; loading.value = false; return; }
    const json = await res.json();
    category.value = json.data;
    document.title = `${category.value.name} — ${settings.site_name}`;
  } catch {
    catLoading.value = false;
    loading.value = false;
    return;
  }

  catLoading.value = false;
  await fetchProducts(true);
}

async function fetchProducts(reset = false) {
  if (reset) {
    loading.value     = true;
    currentPage.value = 1;
    products.value    = [];
  } else {
    loadingMore.value = true;
  }

  try {
    const params = new URLSearchParams({
      page:          currentPage.value,
      per_page:      12,
      sort:          sort.value,
      category_slug: route.params.slug,
    });
    if (searchInput.value) params.set('search', searchInput.value);

    const res  = await fetch('/api/products?' + params);
    const json = await res.json();

    const normalized = (json.data ?? []).map((p, i) =>
      normalizeProduct(p, (reset ? 0 : products.value.length) + i)
    );

    products.value    = reset ? normalized : [...products.value, ...normalized];
    currentPage.value = json.current_page ?? 1;
    lastPage.value    = json.last_page    ?? 1;
    totalCount.value  = json.total        ?? normalized.length;
  } catch {
    if (reset) products.value = [];
  } finally {
    loading.value     = false;
    loadingMore.value = false;
  }
}

function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchProducts(true), 400);
}

function clearSearch() {
  searchInput.value = '';
  fetchProducts(true);
}

function loadMore() {
  currentPage.value++;
  fetchProducts(false);
}
</script>

<style scoped>
@reference "../../css/app.css";

.cat-hero {
  @apply relative overflow-hidden;
  background: #06061A;
  min-height: 200px;
}
.ch-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: 0.25;
}
.ch-orb-1 {
  width: 500px; height: 500px;
  background: radial-gradient(circle, #7C3AED, transparent 70%);
  top: -200px; left: -100px;
}
.ch-orb-2 {
  width: 350px; height: 350px;
  background: radial-gradient(circle, #0D6EFD, transparent 70%);
  top: -100px; right: 0;
}

.search-input {
  @apply w-full pl-11 pr-10 py-3 text-sm rounded-2xl outline-none transition-all duration-300;
  background: #F5F5F7;
  border: 1.5px solid transparent;
  color: #0F0F1A;
}
.search-input::placeholder { color: #9CA3AF; }
.search-input:focus {
  background: #fff;
  border-color: #0D6EFD;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
}

.sort-select {
  @apply px-4 py-3 text-sm rounded-2xl outline-none transition-all duration-200 cursor-pointer font-medium;
  background: #F5F5F7;
  border: 1.5px solid transparent;
  color: #374151;
}
.sort-select:focus { border-color: #0D6EFD; background: #fff; }

.skeleton-card {
  @apply rounded-[20px] bg-gray-100 animate-pulse;
  height: 340px;
}

.btn-primary-sm {
  @apply inline-flex items-center gap-2 font-bold px-6 py-3 rounded-full text-white text-sm transition-all duration-200;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 6px 20px rgba(13,110,253,0.35);
}
.btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(13,110,253,0.45); }

.load-more-btn {
  @apply inline-flex items-center gap-2.5 font-bold px-8 py-4 rounded-full text-sm transition-all duration-300 disabled:opacity-60;
  border: 2px solid #0D6EFD;
  color: #0D6EFD;
  background: transparent;
}
.load-more-btn:not(:disabled):hover {
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  border-color: transparent;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 10px 28px rgba(13,110,253,0.35);
}
</style>
