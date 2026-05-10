<template>
  <div>
  <!-- Loading state -->
  <div v-if="loading" class="min-h-screen flex items-center justify-center">
    <div class="flex flex-col items-center gap-4">
      <div class="w-14 h-14 rounded-2xl animate-pulse" style="background: linear-gradient(135deg,#0D6EFD,#7C3AED);"></div>
      <p class="text-gray-400 text-sm font-medium">Loading product…</p>
    </div>
  </div>

  <!-- Not found -->
  <div v-else-if="!product" class="min-h-screen flex items-center justify-center text-center px-4">
    <div>
      <div class="text-7xl mb-6">😕</div>
      <h2 class="text-2xl font-extrabold mb-2" style="color:#0F0F1A;">Product Not Found</h2>
      <p class="text-gray-400 mb-8">This product may have been removed or doesn't exist.</p>
      <RouterLink to="/shop" class="btn-primary">Browse All Products</RouterLink>
    </div>
  </div>

  <!-- Product detail -->
  <main v-else>
    <!-- Breadcrumb bar -->
    <div class="bg-white border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center gap-2 text-sm text-gray-400 flex-wrap">
          <RouterLink to="/" class="hover:text-blue-600 transition-colors">Home</RouterLink>
          <span>/</span>
          <RouterLink to="/shop" class="hover:text-blue-600 transition-colors">Shop</RouterLink>
          <span v-if="product.category">/</span>
          <RouterLink v-if="product.category"
            :to="`/category/${product.category.slug}`"
            class="hover:text-blue-600 transition-colors">
            {{ product.category.name }}
          </RouterLink>
          <span>/</span>
          <span class="text-gray-700 font-medium line-clamp-1">{{ product.name }}</span>
        </nav>
      </div>
    </div>

    <!-- Main section -->
    <section class="bg-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-20">

          <!-- LEFT: Product image/gallery -->
          <div class="lg:sticky lg:top-24 self-start">
            <!-- Main image -->
            <div class="relative rounded-3xl overflow-hidden aspect-square"
              :class="displayImageUrl ? '' : bg" style="min-height:320px;">
              <div class="absolute inset-0 img-shine pointer-events-none z-10"></div>

              <!-- Real image -->
              <img v-if="displayImageUrl" :src="displayImageUrl" :alt="product.name"
                class="w-full h-full object-cover transition-opacity duration-300" />
              <!-- Emoji fallback -->
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="product-emoji select-none" style="filter: drop-shadow(0 8px 40px rgba(0,0,0,0.18));">
                  {{ product.emoji || '🛍️' }}
                </span>
              </div>

              <!-- Badge -->
              <div v-if="product.badge" class="absolute top-5 left-5 z-20">
                <span :class="['detail-badge', badgeClass]">{{ product.badge }}</span>
              </div>
              <!-- Discount chip -->
              <div v-if="discount" class="absolute top-5 right-5 z-20 discount-chip">
                {{ discount }}% OFF
              </div>
            </div>

            <!-- Thumbnail strip (real gallery) -->
            <div v-if="productGallery.length > 1" class="flex gap-3 mt-4 overflow-x-auto pb-1">
              <button v-for="(url, i) in productGallery" :key="i"
                type="button"
                @click="activeThumb = url"
                class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden transition-all duration-200 hover:scale-105"
                :style="effectiveThumb === url
                  ? 'border:2px solid #0D6EFD; box-shadow:0 0 0 2px rgba(13,110,253,0.2);'
                  : 'border:2px solid #e5e7eb;'">
                <img :src="url" :alt="`Image ${i + 1}`" class="w-full h-full object-cover" />
              </button>
            </div>
            <!-- Decorative strip fallback (no gallery) -->
            <div v-else class="flex gap-3 mt-4">
              <div v-for="c in thumbColors" :key="c"
                class="flex-1 rounded-xl h-16 cursor-pointer transition-all duration-200 hover:scale-105 ring-2 ring-transparent hover:ring-blue-400"
                :class="c">
              </div>
            </div>
          </div>

          <!-- RIGHT: Product info -->
          <div class="flex flex-col gap-6">

            <!-- Category + badges -->
            <div class="flex items-center gap-2 flex-wrap">
              <RouterLink v-if="product.category"
                :to="`/category/${product.category.slug}`"
                class="text-xs font-extrabold uppercase tracking-widest transition-colors hover:text-blue-800"
                style="color:#0D6EFD;">
                {{ product.category.name }}
              </RouterLink>
              <span v-if="product.badge" :class="['text-badge', badgeClass]">{{ product.badge }}</span>
            </div>

            <!-- Name -->
            <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight" style="color:#0F0F1A;">
              {{ product.name }}
            </h1>

            <!-- Rating -->
            <div v-if="product.rating > 0" class="flex items-center gap-3">
              <div class="flex gap-0.5">
                <svg v-for="i in 5" :key="i" class="w-4 h-4"
                  :style="i <= Math.round(product.rating) ? 'color:#FF6B00' : 'color:#E5E7EB'"
                  fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
              </div>
              <span class="text-sm font-semibold" style="color:#374151;">{{ product.rating }}</span>
              <span class="text-sm text-gray-400">({{ product.reviews }} reviews)</span>
            </div>

            <!-- Price: simple -->
            <div v-if="product.type === 'simple'" class="flex items-baseline gap-3">
              <span class="text-4xl font-extrabold" style="color:#0F0F1A;">${{ product.price }}</span>
              <span v-if="product.original_price" class="text-xl text-gray-400 line-through">${{ product.original_price }}</span>
              <span v-if="discount" class="text-sm font-bold px-2.5 py-1 rounded-full text-white" style="background:#FF6B00;">Save {{ discount }}%</span>
            </div>
            <!-- Price: variable -->
            <div v-else-if="product.type === 'variable'" class="flex items-baseline gap-3">
              <template v-if="selectedVariation">
                <span class="text-4xl font-extrabold" style="color:#0F0F1A;">${{ selectedVariation.price }}</span>
                <span v-if="selectedVariation.original_price" class="text-xl text-gray-400 line-through">${{ selectedVariation.original_price }}</span>
              </template>
              <template v-else>
                <span class="text-4xl font-extrabold" style="color:#0F0F1A;">{{ variablePriceRange ?? '—' }}</span>
                <span class="text-sm text-gray-400 font-medium">select options</span>
              </template>
            </div>
            <!-- Price: grouped -->
            <div v-else-if="groupPriceLabel" class="flex items-baseline gap-3">
              <span class="text-4xl font-extrabold" style="color:#0F0F1A;">{{ groupPriceLabel }}</span>
              <span class="text-sm text-gray-400 font-medium">starting price</span>
            </div>

            <div class="divider"></div>

            <!-- Description -->
            <div>
              <h3 class="text-sm font-extrabold uppercase tracking-wider mb-2 text-gray-400">Description</h3>
              <p class="text-gray-600 text-sm leading-relaxed">
                {{ product.description || 'A high-quality product curated just for you. Crafted with premium materials for lasting comfort and style.' }}
              </p>
            </div>

            <div class="divider"></div>

            <!-- Variable: attribute / variant selector -->
            <template v-if="product.type === 'variable'">
              <div v-for="attr in productAttributes" :key="attr.id" class="flex flex-col gap-2">
                <span class="text-sm font-extrabold uppercase tracking-wider text-gray-400">{{ attr.name }}</span>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="val in attr.values"
                    :key="val.id"
                    @click="isValueAvailable(attr.id, val.id) && selectAttribute(attr.id, val.id)"
                    :class="['variation-pill',
                      selectedAttrs[attr.id] === val.id ? 'variation-pill-active' : '',
                      !isValueAvailable(attr.id, val.id) ? 'variation-pill-disabled' : '']"
                  >{{ val.value }}</button>
                </div>
              </div>
              <p v-if="productAttributes.length > 0 && !selectedVariation" class="text-xs text-gray-400 italic">
                Select all options above to add to cart.
              </p>
            </template>

            <!-- Quantity + Cart: simple always, variable only when variation selected -->
            <template v-if="product.type === 'simple' || (product.type === 'variable' && selectedVariation)">
              <div class="flex items-center gap-4">
                <div class="qty-control">
                  <button @click="qty = Math.max(1, qty - 1)" class="qty-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                  </button>
                  <span class="qty-num">{{ qty }}</span>
                  <button @click="qty++" class="qty-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                  </button>
                </div>
                <button @click="addToCart" :class="['cart-btn flex-1', added ? 'cart-btn-done' : '']">
                  <Transition name="btn-swap" mode="out-in">
                    <span v-if="!added" key="add" class="flex items-center justify-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                      </svg>
                      Add to Cart
                    </span>
                    <span v-else key="done" class="flex items-center justify-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                      Added to Cart!
                    </span>
                  </Transition>
                </button>
                <button @click="toggleFav" :class="['wish-btn', wishlisted ? 'wish-active' : '']">
                  <svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
                  </svg>
                </button>
              </div>
            </template>

            <!-- Grouped: items list -->
            <template v-if="product.type === 'grouped'">
              <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider mb-4 text-gray-400">Products in this Bundle</h3>
                <div class="flex flex-col gap-3">
                  <div v-for="item in product.grouped_products" :key="item.id"
                    class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-gray-100 hover:border-blue-200 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                      <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0 bg-gray-50">
                        {{ item.emoji || '🛍️' }}
                      </div>
                      <div class="min-w-0">
                        <RouterLink :to="`/product/${item.slug}`"
                          class="text-sm font-semibold hover:text-blue-600 transition-colors line-clamp-1"
                          style="color:#0F0F1A;">
                          {{ item.name }}
                        </RouterLink>
                        <p class="text-xs text-gray-400">
                          {{ item.price ? '$' + item.price : 'Price varies' }}
                        </p>
                      </div>
                    </div>
                    <RouterLink :to="`/product/${item.slug}`"
                      class="flex-shrink-0 text-xs font-bold px-4 py-2 rounded-full text-white transition-all duration-200 hover:opacity-90"
                      style="background: linear-gradient(135deg,#0D6EFD,#7C3AED);">
                      View
                    </RouterLink>
                  </div>
                </div>
              </div>
            </template>

            <!-- Trust badges -->
            <div class="grid grid-cols-3 gap-3 mt-2">
              <div v-for="t in trust" :key="t.label" class="trust-item">
                <span class="text-xl mb-1.5">{{ t.icon }}</span>
                <span class="text-[11px] font-semibold text-center leading-tight" style="color:#374151;">{{ t.label }}</span>
              </div>
            </div>

            <!-- Meta row -->
            <div class="flex flex-wrap gap-4 text-xs text-gray-400 pt-2">
              <span v-if="product.type === 'simple' && product.stock !== null && product.stock !== undefined">
                <strong class="text-gray-600">Stock:</strong> {{ product.stock }} units
              </span>
              <span v-if="product.type === 'variable' && selectedVariation">
                <strong class="text-gray-600">Stock:</strong> {{ selectedVariation.stock }} units
              </span>
              <span v-if="product.category">
                <strong class="text-gray-600">Category:</strong> {{ product.category.name }}
              </span>
              <span v-if="product.badge">
                <strong class="text-gray-600">Tag:</strong> {{ product.badge }}
              </span>
              <span>
                <strong class="text-gray-600">Type:</strong>
                {{ product.type === 'simple' ? 'Single Product' : product.type === 'variable' ? 'Variable Product' : 'Bundle' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Related products -->
    <section v-if="related.length > 0" class="py-16" style="background:#FAFAFA;">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
          <span class="text-xs font-extrabold uppercase tracking-[0.2em]" style="color:#0D6EFD;">More Like This</span>
          <h2 class="text-3xl font-extrabold mt-1" style="color:#0F0F1A;">You May Also Like</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
          <ProductCard
            v-for="(p, i) in related"
            :key="p.id"
            :product="p"
            :style="`--i:${i}`"
          />
        </div>
      </div>
    </section>
  </main>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import { settings } from '../store/settings';
import { normalizeProduct, bgColors, discountPercent } from '../composables/useProducts.js';
import { addToCart as addToCartStore } from '../store/cart.js';
import { isFavorite, toggleFavorite } from '../store/favorites.js';

const route = useRoute();

const product      = ref(null);
const related      = ref([]);
const loading      = ref(true);
const qty          = ref(1);
const added        = ref(false);
const wishlisted   = computed(() => product.value ? isFavorite(product.value.id) : false);
const selectedAttrs = reactive({});
const activeThumb   = ref(null);

// All images in display order: main image + gallery
const productGallery = computed(() => {
  if (!product.value) return [];
  const urls = [];
  if (product.value.image) urls.push(`/storage/${product.value.image}`);
  for (const img of (product.value.images ?? [])) {
    urls.push(`/storage/${img.path}`);
  }
  return urls;
});

// The gallery thumbnail that is "selected" (defaults to first)
const effectiveThumb = computed(() => activeThumb.value ?? productGallery.value[0] ?? null);

// What to show in the main viewer — variation image takes priority over gallery
const displayImageUrl = computed(() => {
  if (selectedVariation.value?.image) return `/storage/${selectedVariation.value.image}`;
  return effectiveThumb.value;
});

const bg = computed(() => {
  if (!product.value) return bgColors[0];
  const idx = (product.value.id ?? 0) % bgColors.length;
  return bgColors[idx];
});

const thumbColors = ['bg-blue-50', 'bg-purple-50', 'bg-pink-50', 'bg-orange-50'];

const badgeClass = computed(() => ({
  'New':      'badge-blue',
  'Sale':     'badge-orange',
  'Hot':      'badge-orange',
  'Trending': 'badge-dark',
}[product.value?.badge] ?? 'badge-dark'));

const discount = computed(() =>
  product.value ? discountPercent(product.value.price, product.value.original_price) : null
);

const groupPriceLabel = computed(() => {
  if (product.value?.type !== 'grouped') return null;
  const prices = (product.value.grouped_products ?? [])
    .map(i => parseFloat(i.price))
    .filter(p => !isNaN(p) && p > 0);
  if (!prices.length) return null;
  const min = Math.min(...prices);
  const max = Math.max(...prices);
  return min === max ? `$${min.toFixed(2)}` : `From $${min.toFixed(2)}`;
});

const productAttributes = computed(() => {
  if (product.value?.type !== 'variable') return [];
  const attrs = {};
  for (const v of (product.value.variations ?? [])) {
    if (!v.is_active) continue;
    for (const av of (v.attribute_values ?? [])) {
      if (!attrs[av.attribute.id]) {
        attrs[av.attribute.id] = { id: av.attribute.id, name: av.attribute.name, values: [] };
      }
      if (!attrs[av.attribute.id].values.find(x => x.id === av.id)) {
        attrs[av.attribute.id].values.push({ id: av.id, value: av.value });
      }
    }
  }
  return Object.values(attrs);
});

const selectedVariation = computed(() => {
  if (product.value?.type !== 'variable') return null;
  const attrs = productAttributes.value;
  if (!attrs.length || Object.keys(selectedAttrs).length < attrs.length) return null;
  return (product.value.variations ?? []).find(v => {
    if (!v.is_active) return false;
    return attrs.every(attr => {
      const selId = selectedAttrs[attr.id];
      return (v.attribute_values ?? []).some(av => av.id === selId);
    });
  }) ?? null;
});

const variablePriceRange = computed(() => {
  if (product.value?.type !== 'variable') return null;
  const prices = (product.value.variations ?? [])
    .filter(v => v.is_active)
    .map(v => parseFloat(v.price))
    .filter(p => !isNaN(p));
  if (!prices.length) return null;
  const min = Math.min(...prices);
  const max = Math.max(...prices);
  return min === max ? `$${min.toFixed(2)}` : `From $${min.toFixed(2)}`;
});

function selectAttribute(attrId, valueId) {
  selectedAttrs[attrId] = valueId;
}

function isValueAvailable(attrId, valueId) {
  const testAttrs = { ...selectedAttrs, [attrId]: valueId };
  return (product.value?.variations ?? []).some(v => {
    if (!v.is_active) return false;
    return Object.entries(testAttrs).every(([aId, vId]) =>
      (v.attribute_values ?? []).some(av => av.attribute.id === parseInt(aId) && av.id === vId)
    );
  });
}

const trust = [
  { icon: '🚚', label: 'Free Shipping'    },
  { icon: '↩️', label: 'Free Returns'     },
  { icon: '🔒', label: 'Secure Checkout'  },
];

async function fetchProduct(slug) {
  loading.value = true;
  product.value = null;
  related.value = [];
  try {
    const res  = await fetch(`/api/products/${encodeURIComponent(slug)}`);
    if (!res.ok) { loading.value = false; return; }
    const json = await res.json();
    product.value = json.data;
    document.title = `${json.data.name} — ${settings.site_name}`;
    related.value = (json.related ?? []).map(normalizeProduct);
  } catch {
    product.value = null;
  } finally {
    loading.value = false;
    qty.value     = 1;
    added.value   = false;
  }
}

onMounted(() => fetchProduct(route.params.slug));
watch(() => route.params.slug, (slug) => {
  if (slug) {
    Object.keys(selectedAttrs).forEach(k => delete selectedAttrs[k]);
    activeThumb.value = null;
    fetchProduct(slug);
  }
});

function addToCart() {
  if (added.value) return;

  const price = product.value.type === 'variable'
    ? selectedVariation.value?.price
    : product.value.price;

  const variationLabel = product.value.type === 'variable' && selectedVariation.value
    ? (selectedVariation.value.attribute_values ?? [])
        .map(av => `${av.attribute.name}: ${av.value}`)
        .join(', ')
    : '';

  addToCartStore({
    productId:      product.value.id,
    name:           product.value.name,
    slug:           product.value.slug,
    emoji:          product.value.emoji ?? '🛍️',
    price,
    qty:            qty.value,
    variationId:    selectedVariation.value?.id ?? null,
    variationLabel,
  });

  added.value = true;
  setTimeout(() => { added.value = false; }, 2500);
}

function toggleFav() {
  if (!product.value) return;
  const idx = (product.value.id ?? 0) % bgColors.length;
  toggleFavorite({ ...product.value, bg: bgColors[idx], category: product.value.category?.name ?? 'General' });
}
</script>

<style scoped>
@reference "../../css/app.css";

/* ── Buttons ── */
.btn-primary {
  @apply inline-flex items-center justify-center gap-2 font-bold px-7 py-3.5 rounded-full text-white transition-all duration-300;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 8px 24px rgba(13,110,253,0.4);
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(13,110,253,0.5); }

/* ── Image area ── */
.product-emoji {
  font-size: 160px;
  animation: float-emoji 4s ease-in-out infinite;
}
@keyframes float-emoji {
  0%, 100% { transform: translateY(0) rotate(-2deg); }
  50%       { transform: translateY(-14px) rotate(2deg); }
}
.img-shine {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 60%);
  pointer-events: none;
}

/* ── Badges ── */
.detail-badge {
  @apply text-[11px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wide;
  animation: badge-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes badge-pop {
  from { transform: scale(0); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}
.text-badge {
  @apply text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wide;
}
.badge-blue   { background: #0D6EFD; color: #fff; }
.badge-orange { background: #FF6B00; color: #fff; }
.badge-dark   { background: #0F0F1A; color: #fff; }

.discount-chip {
  @apply text-[11px] font-extrabold px-3 py-1.5 rounded-full text-white;
  background: #FF6B00;
  box-shadow: 0 4px 12px rgba(255,107,0,0.4);
  animation: badge-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.1s both;
}

/* ── Divider ── */
.divider {
  @apply w-full;
  height: 1px;
  background: #F3F4F6;
}

/* ── Quantity control ── */
.qty-control {
  @apply flex items-center rounded-2xl overflow-hidden;
  border: 1.5px solid #E5E7EB;
}
.qty-btn {
  @apply w-11 h-12 flex items-center justify-center transition-colors duration-150 text-gray-500;
}
.qty-btn:hover { background: #F5F5F7; color: #0F0F1A; }
.qty-num {
  @apply w-12 h-12 flex items-center justify-center text-sm font-extrabold;
  color: #0F0F1A;
  border-left: 1px solid #E5E7EB;
  border-right: 1px solid #E5E7EB;
}

/* ── Cart button ── */
.cart-btn {
  @apply py-3.5 px-6 rounded-2xl font-bold text-sm text-white transition-all duration-300;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 8px 24px rgba(13,110,253,0.4);
}
.cart-btn:hover { transform: translateY(-1px); box-shadow: 0 14px 32px rgba(13,110,253,0.5); }
.cart-btn-done {
  background: #16a34a !important;
  box-shadow: 0 8px 24px rgba(22,163,74,0.4) !important;
}

/* ── Wishlist button ── */
.wish-btn {
  @apply w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200;
  border: 1.5px solid #E5E7EB;
  color: #D1D5DB;
}
.wish-btn:hover { border-color: #FCA5A5; color: #EF4444; }
.wish-active { color: #EF4444; border-color: #FCA5A5; background: #FEF2F2; }

/* ── Trust badges ── */
.trust-item {
  @apply flex flex-col items-center gap-1.5 py-4 px-2 rounded-2xl;
  background: #F9FAFB;
  border: 1.5px solid #F3F4F6;
}

/* ── Button label swap ── */
.btn-swap-enter-active, .btn-swap-leave-active { transition: all 0.18s ease; }
.btn-swap-enter-from { opacity: 0; transform: translateY(6px); }
.btn-swap-leave-to   { opacity: 0; transform: translateY(-6px); }

/* ── Variation pills ── */
.variation-pill {
  @apply px-4 py-2 rounded-xl text-sm font-semibold border transition-all duration-150;
  border-color: #E5E7EB;
  color: #374151;
  background: #fff;
}
.variation-pill:hover:not(.variation-pill-disabled) {
  border-color: #0D6EFD;
  color: #0D6EFD;
}
.variation-pill-active {
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  border-color: transparent;
  color: #fff !important;
}
.variation-pill-disabled {
  opacity: 0.35;
  cursor: not-allowed;
  text-decoration: line-through;
}
</style>
