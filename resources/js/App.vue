<template>
  <div class="min-h-screen font-sans antialiased bg-white">
    <PromoBar />
    <Navbar />
    <RouterView v-slot="{ Component }">
      <Transition name="page">
        <component v-if="Component" :is="Component" :key="$route.name" />
      </Transition>
    </RouterView>
    <Footer />
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import PromoBar from './components/PromoBar.vue';
import Navbar   from './components/Navbar.vue';
import Footer   from './components/Footer.vue';
import { initAuth, authStore } from './store/auth';
import { loadUserFavorites, clearFavorites } from './store/favorites';
import { loadSettings } from './store/settings';

onMounted(async () => {
  await Promise.all([initAuth(), loadSettings()]);
  if (authStore.user) loadUserFavorites();
});

watch(() => authStore.user, (user) => {
  if (user) loadUserFavorites();
  else clearFavorites();
});
</script>

<style>
.page-enter-active { transition: opacity 0.25s ease; }
.page-enter-from   { opacity: 0; }
</style>
