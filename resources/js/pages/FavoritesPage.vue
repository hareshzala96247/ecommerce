<template>
  <div class="min-h-[calc(100vh-120px)]" style="background:#FAFAFA;">

    <!-- Hero -->
    <div class="fav-hero">
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <nav class="flex items-center gap-2 text-sm mb-4" style="color:#9ca3af;">
          <RouterLink to="/" class="hover:text-gray-900 transition-colors">Home</RouterLink>
          <span>/</span>
          <span style="color:#0F0F1A;">Favorites</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold" style="color:#0F0F1A;">My Favorites</h1>
        <p class="text-sm mt-2" style="color:#6b7280;">
          {{ favoritesStore.items.length }}
          saved item{{ favoritesStore.items.length !== 1 ? 's' : '' }}
        </p>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Empty state -->
      <div v-if="favoritesStore.items.length === 0" class="text-center py-24">
        <div class="inline-flex w-20 h-20 rounded-2xl items-center justify-center mb-6" style="background:#F5F5F7;">
          <svg class="w-10 h-10" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">No favorites yet</h2>
        <p class="text-gray-400 mb-8">Tap the heart on any product to save it here.</p>
        <RouterLink to="/shop" class="btn-primary">Start Shopping</RouterLink>
      </div>

      <!-- Grid -->
      <template v-else>
        <!-- Clear all -->
        <div class="flex items-center justify-between mb-6">
          <p class="text-sm text-gray-500">
            Showing <strong class="text-gray-800">{{ favoritesStore.items.length }}</strong> saved items
          </p>
          <button @click="confirmClear = true"
            class="text-sm text-red-400 hover:text-red-600 font-medium transition-colors">
            Clear all
          </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
          <ProductCard
            v-for="(product, i) in favoritesStore.items"
            :key="product.id"
            :product="product"
            :style="`--i:${i % 12}`"
          />
        </div>
      </template>
    </div>

    <!-- Clear all dialog -->
    <Transition name="fade">
      <div v-if="confirmClear"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="background:rgba(0,0,0,0.45);"
        @click.self="confirmClear = false">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
          <h3 class="text-base font-bold mb-1" style="color:#0F0F1A;">Clear all favorites?</h3>
          <p class="text-sm mb-5" style="color:#6b7280;">This will remove all your saved items.</p>
          <div class="flex gap-3">
            <button @click="confirmClear = false"
              class="flex-1 py-2.5 rounded-xl text-sm font-semibold border transition-colors"
              style="border-color:#E5E7EB;color:#374151;">
              Cancel
            </button>
            <button @click="doClear"
              class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-colors"
              style="background:#EF4444;">
              Clear All
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import { favoritesStore, clearFavorites } from '../store/favorites.js';

const confirmClear = ref(false);

function doClear() {
  clearFavorites();
  confirmClear.value = false;
}
</script>

<style scoped>
@reference "../../css/app.css";

.fav-hero {
  @apply relative;
  background: #fff;
  border-bottom: 1px solid #F3F4F6;
}

.btn-primary {
  @apply inline-flex items-center gap-2 font-semibold px-7 py-3 rounded-xl text-white transition-all duration-200;
  background: #0D6EFD;
}
.btn-primary:hover { background: #0B5ED7; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
