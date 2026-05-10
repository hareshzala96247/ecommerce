<template>
  <div class="min-h-[calc(100vh-120px)]" style="background:#FAFAFA;">

    <!-- Hero -->
    <div class="fav-hero">
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="fh-orb fh-orb-1"></div>
        <div class="fh-orb fh-orb-2"></div>
      </div>
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <nav class="flex items-center gap-2 text-sm mb-5 text-white/50">
          <RouterLink to="/" class="hover:text-white transition-colors">Home</RouterLink>
          <span>/</span>
          <span class="text-white/80">Favorites</span>
        </nav>
        <div class="flex items-center gap-3">
          <span class="text-4xl">❤️</span>
          <div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white">My Favorites</h1>
            <p class="text-white/60 text-base mt-1">
              {{ favoritesStore.items.length }}
              saved item{{ favoritesStore.items.length !== 1 ? 's' : '' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Empty state -->
      <div v-if="favoritesStore.items.length === 0" class="text-center py-24">
        <div class="inline-flex w-24 h-24 rounded-3xl items-center justify-center mb-6" style="background:#FEF2F2;">
          <svg class="w-12 h-12" style="color:#FCA5A5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-extrabold mb-2" style="color:#0F0F1A;">No favorites yet</h2>
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
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl">
          <div class="text-4xl mb-4 text-center">🗑️</div>
          <h3 class="text-lg font-extrabold mb-2 text-center" style="color:#0F0F1A;">Clear all favorites?</h3>
          <p class="text-sm text-center mb-6" style="color:#6b7280;">This will remove all your saved items.</p>
          <div class="flex gap-3">
            <button @click="confirmClear = false"
              class="flex-1 py-3 rounded-2xl text-sm font-bold border transition-colors"
              style="border-color:#E5E7EB;color:#374151;">
              Cancel
            </button>
            <button @click="doClear"
              class="flex-1 py-3 rounded-2xl text-sm font-bold text-white transition-colors"
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
  @apply relative overflow-hidden;
  background: #06061A;
  min-height: 200px;
}
.fh-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: 0.25;
}
.fh-orb-1 {
  width: 500px; height: 500px;
  background: radial-gradient(circle, #EF4444, transparent 70%);
  top: -200px; left: -100px;
}
.fh-orb-2 {
  width: 350px; height: 350px;
  background: radial-gradient(circle, #7C3AED, transparent 70%);
  top: -100px; right: 0;
}

.btn-primary {
  @apply inline-flex items-center gap-2 font-bold px-7 py-3.5 rounded-full text-white transition-all duration-300;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 8px 24px rgba(13,110,253,0.4);
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(13,110,253,0.5); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
