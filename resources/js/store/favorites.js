import { reactive, computed } from 'vue';
import axios from 'axios';
import { normalizeProduct } from '../composables/useProducts.js';

const stored = JSON.parse(localStorage.getItem('favorites') ?? '[]');

export const favoritesStore = reactive({
  items: stored,
});

export const favoritesCount = computed(() => favoritesStore.items.length);

export const isFavorite = (id) => favoritesStore.items.some(p => p.id === id);

export const toggleFavorite = async (product) => {
  const idx = favoritesStore.items.findIndex(p => p.id === product.id);
  if (idx >= 0) {
    favoritesStore.items.splice(idx, 1);
  } else {
    favoritesStore.items.push(product);
  }
  persist();

  try {
    await axios.post(`/api/favorites/${product.id}`);
  } catch {}
};

export const loadUserFavorites = async () => {
  try {
    const { data } = await axios.get('/api/favorites');
    const serverItems = (data.data ?? []).map((p, i) => normalizeProduct(p, i));

    const localIds = new Set(favoritesStore.items.map(p => p.id));
    for (const item of serverItems) {
      if (!localIds.has(item.id)) {
        favoritesStore.items.push(item);
      }
    }
    persist();
  } catch {}
};

export const clearFavorites = () => {
  favoritesStore.items = [];
  localStorage.removeItem('favorites');
};

function persist() {
  localStorage.setItem('favorites', JSON.stringify(favoritesStore.items));
}
