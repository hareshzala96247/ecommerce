<template>
  <div
    ref="cardRef"
    class="pcard group"
    @click="goToProduct"
    @mousemove="onMouseMove"
    @mouseleave="onMouseLeave"
    :style="cardStyle"
  >
    <!-- Glare overlay -->
    <div class="pcard-glare" :style="glareStyle"></div>

    <!-- Image area -->
    <div class="pcard-img-wrap">
      <img v-if="product.image" :src="`/storage/${product.image}`" :alt="product.name"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
      <div v-else :class="['w-full h-full flex items-center justify-center transition-transform duration-500 group-hover:scale-110', product.bg || 'bg-blue-50']">
        <span class="text-7xl select-none drop-shadow-lg transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
          {{ product.emoji || '🛍️' }}
        </span>
      </div>

      <!-- Badge -->
      <div v-if="product.badge" class="absolute top-3 left-3">
        <span :class="['pcard-badge', badgeClass]">{{ product.badge }}</span>
      </div>

      <!-- Wishlist -->
      <button @click.stop="toggleWishlist" :class="['wishlist-btn', wishlisted ? 'wished' : '']">
        <svg class="w-4 h-4" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
        </svg>
      </button>

      <!-- Quick add overlay -->
      <div class="quick-overlay">
        <button @click.stop="handleQuickAction" :class="['quick-btn', added ? 'quick-done' : '']">
          <template v-if="product.type === 'simple'">
            <template v-if="!added">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
              Quick Add
            </template>
            <template v-else>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Added!
            </template>
          </template>
          <template v-else-if="product.type === 'variable'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            View Options
          </template>
          <template v-else>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            View Bundle
          </template>
        </button>
      </div>
    </div>

    <!-- Info -->
    <div class="p-4">
      <!-- Category -->
      <p class="text-[10px] font-extrabold uppercase tracking-widest mb-1.5" style="color:#0D6EFD;">
        {{ product.category }}
      </p>

      <!-- Name -->
      <h3 class="text-[13px] font-semibold leading-snug mb-2.5 line-clamp-2" style="color:#0F0F1A;">
        {{ product.name }}
      </h3>

      <!-- Rating -->
      <div v-if="product.rating > 0" class="flex items-center gap-1.5 mb-3">
        <div class="flex gap-0.5">
          <svg v-for="i in 5" :key="i" class="w-3 h-3"
            :style="i <= Math.round(product.rating) ? 'color:#FF6B00' : 'color:#E5E7EB'"
            fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
          </svg>
        </div>
        <span class="text-[11px] text-gray-400 font-medium">{{ product.rating }} ({{ product.reviews }})</span>
      </div>

      <!-- Price + Action button -->
      <div class="flex items-center justify-between">
        <div class="flex items-baseline gap-1.5">
          <template v-if="product.type === 'grouped'">
            <span class="text-sm font-bold text-gray-400">Bundle</span>
          </template>
          <template v-else>
            <span v-if="product.type === 'variable' && product.price" class="text-[10px] font-bold text-gray-400 self-center">From</span>
            <span class="text-base font-extrabold" style="color:#0F0F1A;">
              {{ product.price ? '$' + product.price : '—' }}
            </span>
            <span v-if="product.originalPrice" class="text-[11px] text-gray-400 line-through">${{ product.originalPrice }}</span>
          </template>
        </div>
        <button @click.stop="handleQuickAction" :class="['add-btn', added ? 'add-done' : '']">
          <template v-if="product.type === 'simple'">
            <svg v-if="!added" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </template>
          <template v-else>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </template>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { addToCart as addToCartStore } from '../store/cart.js';
import { isFavorite, toggleFavorite } from '../store/favorites.js';

const props  = defineProps({ product: { type: Object, required: true } });
const router = useRouter();

const goToProduct = (e) => {
  if (e.target.closest('button')) return;
  if (!props.product.slug) return;
  router.push(`/product/${props.product.slug}`);
};

const cardRef    = ref(null);
const wishlisted = computed(() => isFavorite(props.product.id));
const added      = ref(false);
const rotX       = ref(0);
const rotY       = ref(0);
const glareX     = ref(50);
const glareY     = ref(50);

const cardStyle = computed(() => ({
  transform: `perspective(900px) rotateX(${rotX.value}deg) rotateY(${rotY.value}deg) translateZ(0)`,
  transition: rotX.value === 0 ? 'transform 0.5s cubic-bezier(0.23,1,0.32,1)' : 'transform 0.1s ease',
}));

const glareStyle = computed(() => ({
  background: `radial-gradient(circle at ${glareX.value}% ${glareY.value}%, rgba(255,255,255,0.18) 0%, transparent 60%)`,
  opacity: rotX.value !== 0 || rotY.value !== 0 ? 1 : 0,
  transition: 'opacity 0.3s ease',
}));

const onMouseMove = (e) => {
  const el = cardRef.value;
  if (!el) return;
  const r = el.getBoundingClientRect();
  rotY.value  =  ((e.clientX - r.left) / r.width  - 0.5) * 12;
  rotX.value  = -((e.clientY - r.top)  / r.height - 0.5) * 12;
  glareX.value = ((e.clientX - r.left) / r.width)  * 100;
  glareY.value = ((e.clientY - r.top)  / r.height) * 100;
};
const onMouseLeave = () => { rotX.value = 0; rotY.value = 0; };

const badgeClass = computed(() => ({
  'New':      'badge-blue',
  'Sale':     'badge-orange',
  'Hot':      'badge-orange',
  'Trending': 'badge-dark',
}[props.product.badge] ?? 'badge-dark'));

const toggleWishlist = () => toggleFavorite(props.product);

const handleQuickAction = () => {
  if (props.product.type !== 'simple') {
    router.push(`/product/${props.product.slug}`);
    return;
  }
  if (added.value) return;
  addToCartStore({
    productId: props.product.id,
    name:      props.product.name,
    slug:      props.product.slug,
    image:     props.product.image ?? null,
    emoji:     props.product.emoji ?? '🛍️',
    price:     props.product.price,
    qty:       1,
  });
  added.value = true;
  setTimeout(() => { added.value = false; }, 1800);
};
</script>

<style scoped>
@reference "../../css/app.css";

.pcard {
  @apply relative bg-white cursor-pointer select-none;
  border-radius: 20px;
  border: 1.5px solid #EBEBEB;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  overflow: hidden;
  transform-style: preserve-3d;
  will-change: transform;
  transition: box-shadow 0.35s ease, border-color 0.3s ease;
}
.pcard:hover {
  border-color: rgba(13,110,253,0.25);
  box-shadow: 0 24px 60px -12px rgba(13,110,253,0.18), 0 4px 16px rgba(0,0,0,0.06);
}

.pcard-glare {
  @apply absolute inset-0 pointer-events-none z-10;
  border-radius: 20px;
}

/* Image area */
.pcard-img-wrap {
  @apply relative overflow-hidden;
  aspect-ratio: 1;
  background: #F8F9FF;
}

/* Badge */
.pcard-badge {
  @apply text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wide;
  animation: badge-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes badge-pop {
  from { transform: scale(0) rotate(-15deg); opacity: 0; }
  to   { transform: scale(1) rotate(0deg);   opacity: 1; }
}
.badge-blue   { background: #0D6EFD; color: #fff; }
.badge-orange { background: #FF6B00; color: #fff; }
.badge-dark   { background: #0F0F1A; color: #fff; }

/* Wishlist */
.wishlist-btn {
  @apply absolute top-3 right-3 w-8 h-8 rounded-full bg-white flex items-center justify-center
         transition-all duration-200 hover:scale-110 active:scale-90;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border: 1px solid #EBEBEB;
  color: #D1D5DB;
}
.wishlist-btn:hover { color: #EF4444; border-color: #FECACA; }
.wished {
  color: #EF4444;
  border-color: #FECACA;
  animation: heart-pop 0.4s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes heart-pop {
  0%   { transform: scale(1); }
  40%  { transform: scale(1.45); }
  70%  { transform: scale(0.9); }
  100% { transform: scale(1); }
}

/* Quick add overlay */
.quick-overlay {
  @apply absolute inset-x-0 bottom-0 p-3;
  background: linear-gradient(to top, rgba(15,15,26,0.7) 0%, transparent 100%);
  transform: translateY(110%);
  transition: transform 0.3s cubic-bezier(0.22,1,0.36,1);
}
.pcard:hover .quick-overlay { transform: translateY(0); }

.quick-btn {
  @apply w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold transition-all duration-200;
  background: rgba(255,255,255,0.95);
  color: #0F0F1A;
  backdrop-filter: blur(8px);
}
.quick-btn:hover { background: #0D6EFD; color: #fff; }
.quick-done { background: #16a34a !important; color: #fff !important; }

/* Add button */
.add-btn {
  @apply w-9 h-9 rounded-xl flex items-center justify-center text-white transition-all duration-200 active:scale-90;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 4px 14px -3px rgba(13,110,253,0.5);
}
.add-btn:hover {
  transform: scale(1.1) rotate(-8deg);
  box-shadow: 0 8px 24px -4px rgba(13,110,253,0.65);
}
.add-done {
  background: #16a34a !important;
  box-shadow: 0 4px 14px -3px rgba(22,163,74,0.5) !important;
  animation: done-pop 0.4s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes done-pop {
  0%   { transform: scale(0.8); }
  60%  { transform: scale(1.2); }
  100% { transform: scale(1); }
}
</style>
