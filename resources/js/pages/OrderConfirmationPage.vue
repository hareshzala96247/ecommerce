<template>
  <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-4 py-16"
    style="background:#FAFAFA;">
    <div class="w-full max-w-lg">

      <!-- Loading -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-10 h-10 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-sm" style="color:#9ca3af;">Loading your order…</p>
      </div>

      <template v-else-if="order">
        <!-- Success animation -->
        <div class="text-center mb-8">
          <div class="relative inline-flex mb-6">
            <div class="w-24 h-24 rounded-full flex items-center justify-center"
              style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);box-shadow:0 16px 48px rgba(13,110,253,0.4);"
              :class="animate ? 'scale-in' : ''">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div class="absolute -top-1 -right-1 w-8 h-8 rounded-full flex items-center justify-center text-lg"
              style="background:#fff;box-shadow:0 2px 12px rgba(0,0,0,0.12);">
              🎉
            </div>
          </div>
          <h1 class="text-3xl font-black mb-2" style="color:#0F0F1A;">Order Placed!</h1>
          <p class="text-sm" style="color:#6b7280;">
            Thank you, <strong>{{ order.customer_name }}</strong>! Your order has been received.
          </p>
        </div>

        <!-- Order card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-4" style="border:1px solid rgba(0,0,0,0.06);">

          <!-- Order meta -->
          <div class="grid grid-cols-2 gap-4 mb-5 pb-5" style="border-bottom:1px solid #F3F4F6;">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#9ca3af;">Order Number</p>
              <p class="text-base font-black" style="color:#0F0F1A;">#{{ String(order.id).padStart(5, '0') }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#9ca3af;">Status</p>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize"
                :style="statusStyle(order.status)">
                <span class="w-1.5 h-1.5 rounded-full" :style="{ background: statusDot(order.status) }"></span>
                {{ order.status }}
              </span>
            </div>
            <div>
              <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#9ca3af;">Order Date</p>
              <p class="text-sm font-semibold" style="color:#0F0F1A;">{{ orderDate }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#9ca3af;">Total</p>
              <p class="text-base font-black" style="color:#0D6EFD;">${{ parseFloat(order.total).toFixed(2) }}</p>
            </div>
          </div>

          <!-- Items -->
          <h3 class="text-sm font-bold mb-3" style="color:#0F0F1A;">Items Ordered</h3>
          <div class="space-y-2.5 mb-5">
            <div v-for="item in order.items" :key="item.id"
              class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0" style="background:#F5F5F7;">
                  <img v-if="item.variation?.image || item.product?.image"
                    :src="`/storage/${item.variation?.image ?? item.product.image}`"
                    :alt="item.product_name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-base">
                    {{ item.product?.emoji || '🛍️' }}
                  </div>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium truncate" style="color:#0F0F1A;">{{ item.product_name }}</p>
                  <p class="text-[11px]" style="color:#9ca3af;">Qty: {{ item.quantity }}</p>
                </div>
              </div>
              <span class="text-sm font-bold flex-shrink-0" style="color:#0F0F1A;">
                ${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
              </span>
            </div>
          </div>

          <!-- Shipping address -->
          <div class="pt-4" style="border-top:1px solid #F3F4F6;">
            <p class="text-[11px] font-bold uppercase tracking-wider mb-2" style="color:#9ca3af;">Shipping To</p>
            <div class="flex items-start gap-2.5">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <p class="text-sm" style="color:#374151;">
                {{ order.address }}, {{ order.city }}<span v-if="order.state">, {{ order.state }}</span>
                {{ order.zip }}, {{ order.country }}
              </p>
            </div>
          </div>
        </div>

        <!-- Confirmation email note -->
        <div class="flex items-center gap-3 px-4 py-3.5 rounded-xl mb-6"
          style="background:rgba(13,110,253,0.06);border:1px solid rgba(13,110,253,0.12);">
          <svg class="w-4 h-4 flex-shrink-0" style="color:#0D6EFD;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <p class="text-sm" style="color:#1d4ed8;">
            A confirmation has been sent to <strong>{{ order.customer_email }}</strong>
          </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3">
          <RouterLink to="/shop"
            class="flex-1 py-3 rounded-xl font-bold text-sm text-center transition-all duration-200 text-white"
            style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);box-shadow:0 4px 16px rgba(13,110,253,0.35);">
            Continue Shopping
          </RouterLink>
          <RouterLink to="/account"
            class="flex-1 py-3 rounded-xl font-bold text-sm text-center transition-all duration-200"
            style="border:1.5px solid #E5E7EB;color:#374151;">
            My Account
          </RouterLink>
        </div>
      </template>

      <!-- Error / not found -->
      <div v-else class="text-center py-16">
        <div class="text-5xl mb-4">😕</div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">Order not found</h2>
        <RouterLink to="/shop" class="text-sm font-semibold" style="color:#0D6EFD;">Back to Shop</RouterLink>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import axios from 'axios';

const route   = useRoute();
const order   = ref(null);
const loading = ref(true);
const animate = ref(false);

async function fetchOrder(id) {
  if (!id) { loading.value = false; return; }
  order.value   = null;
  animate.value = false;
  loading.value = true;
  try {
    const tokens = JSON.parse(sessionStorage.getItem('guestOrderTokens') || '{}');
    const params = tokens[id] ? { token: tokens[id] } : {};
    const { data } = await axios.get(`/api/orders/${id}`, { params });
    order.value = data.data;
    setTimeout(() => { animate.value = true; }, 100);
  } catch {
    order.value = null;
  } finally {
    loading.value = false;
  }
}

watch(() => route.query.id, (id) => fetchOrder(id));

const statusMap = {
  pending:    { bg: 'rgba(234,179,8,0.12)',  color: '#a16207' },
  processing: { bg: 'rgba(13,110,253,0.10)', color: '#1d4ed8' },
  shipped:    { bg: 'rgba(124,58,237,0.10)', color: '#6b21a8' },
  delivered:  { bg: 'rgba(34,197,94,0.10)',  color: '#166534' },
  cancelled:  { bg: 'rgba(239,68,68,0.10)',  color: '#991b1b' },
};

function statusStyle(s) {
  const m = statusMap[s] ?? statusMap.pending;
  return `background:${m.bg};color:${m.color};`;
}
function statusDot(s) {
  return (statusMap[s] ?? statusMap.pending).color;
}

const orderDate = computed(() => {
  if (!order.value?.created_at) return '—';
  return new Date(order.value.created_at).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric',
  });
});

onMounted(() => fetchOrder(route.query.id));

onUnmounted(() => {
    const id = route.query.id;
    if (!id) return;
    const tokens = JSON.parse(sessionStorage.getItem('guestOrderTokens') || '{}');
    delete tokens[id];
    sessionStorage.setItem('guestOrderTokens', JSON.stringify(tokens));
});
</script>

<style scoped>
.scale-in {
  animation: scale-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
@keyframes scale-in {
  from { transform: scale(0); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}
</style>
