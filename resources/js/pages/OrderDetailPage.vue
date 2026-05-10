<template>
  <div class="min-h-[calc(100vh-120px)] px-4 py-10" style="background:#FAFAFA;">
    <div class="max-w-3xl mx-auto">

      <!-- Back -->
      <div class="mb-6">
        <RouterLink to="/orders"
          class="inline-flex items-center gap-2 text-sm font-medium transition-colors duration-150"
          style="color:#6b7280;"
          @mouseenter="(e) => e.currentTarget.style.color='#0D6EFD'"
          @mouseleave="(e) => e.currentTarget.style.color='#6b7280'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to My Orders
        </RouterLink>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-4">
        <div class="bg-white rounded-2xl h-32 animate-pulse" style="border:1px solid rgba(0,0,0,0.06);"></div>
        <div class="bg-white rounded-2xl h-48 animate-pulse" style="border:1px solid rgba(0,0,0,0.06);"></div>
        <div class="bg-white rounded-2xl h-40 animate-pulse" style="border:1px solid rgba(0,0,0,0.06);"></div>
      </div>

      <!-- Not found -->
      <div v-else-if="!order" class="text-center py-24">
        <div class="text-5xl mb-4">😕</div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">Order not found</h2>
        <p class="text-sm mb-6" style="color:#6b7280;">This order doesn't exist or you don't have access to it.</p>
        <RouterLink to="/orders"
          class="px-6 py-2.5 rounded-xl text-sm font-bold text-white"
          style="background:#0D6EFD;">
          My Orders
        </RouterLink>
      </div>

      <template v-else>

        <!-- Header card -->
        <div class="bg-white rounded-2xl p-6 mb-4" style="border:1px solid rgba(0,0,0,0.06);">
          <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#9ca3af;">Order Number</p>
              <h1 class="text-2xl font-black" style="color:#0F0F1A;">
                #{{ String(order.id).padStart(5, '0') }}
              </h1>
              <p class="text-sm mt-1" style="color:#9ca3af;">Placed on {{ fmtDate(order.created_at) }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold capitalize"
              :style="statusStyle(order.status)">
              <span class="w-2 h-2 rounded-full" :style="{ background: statusDot(order.status) }"></span>
              {{ order.status }}
            </span>
          </div>

          <!-- Progress tracker -->
          <div class="mt-6">
            <div class="flex items-center gap-0">
              <template v-for="(step, i) in statusSteps" :key="step.key">
                <div class="flex flex-col items-center" style="min-width:0;flex:1;">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300"
                    :style="stepDone(step.key)
                      ? 'background:linear-gradient(135deg,#0D6EFD,#7C3AED);'
                      : stepActive(step.key)
                        ? 'background:#0D6EFD;'
                        : 'background:#F3F4F6;'">
                    <svg v-if="stepDone(step.key)" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span v-else class="text-xs font-bold"
                      :style="stepActive(step.key) ? 'color:#fff;' : 'color:#9ca3af;'">
                      {{ i + 1 }}
                    </span>
                  </div>
                  <p class="text-[10px] font-semibold mt-1.5 text-center leading-tight"
                    :style="stepDone(step.key) || stepActive(step.key) ? 'color:#0F0F1A;' : 'color:#9ca3af;'">
                    {{ step.label }}
                  </p>
                </div>
                <div v-if="i < statusSteps.length - 1"
                  class="h-0.5 flex-1 mb-5 transition-all duration-300"
                  :style="stepDone(step.key) ? 'background:#0D6EFD;' : 'background:#F3F4F6;'">
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-2xl mb-4 overflow-hidden" style="border:1px solid rgba(0,0,0,0.06);">
          <div class="px-6 py-4" style="border-bottom:1px solid #F3F4F6;">
            <h2 class="text-base font-bold" style="color:#0F0F1A;">
              Items Ordered
              <span class="text-sm font-normal ml-1" style="color:#9ca3af;">({{ order.items.length }})</span>
            </h2>
          </div>
          <div class="divide-y divide-gray-50">
            <div v-for="item in order.items" :key="item.id"
              class="flex items-center gap-4 px-6 py-4">
              <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0" style="background:#F5F5F7;">
                <img v-if="item.variation?.image || item.product?.image"
                  :src="`/storage/${item.variation?.image ?? item.product.image}`"
                  :alt="item.product_name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-xl">
                  {{ item.product?.emoji || '🛍️' }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate" style="color:#0F0F1A;">{{ item.product_name }}</p>
                <p class="text-xs mt-0.5" style="color:#9ca3af;">
                  ${{ parseFloat(item.price).toFixed(2) }} × {{ item.quantity }}
                </p>
              </div>
              <span class="text-sm font-bold flex-shrink-0" style="color:#0F0F1A;">
                ${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
              </span>
            </div>
          </div>
          <!-- Totals -->
          <div class="px-6 py-4 space-y-2" style="border-top:1px solid #F3F4F6;background:#FAFAFA;">
            <div class="flex justify-between text-sm">
              <span style="color:#6b7280;">Subtotal</span>
              <span class="font-medium" style="color:#0F0F1A;">${{ subtotal }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span style="color:#6b7280;">Shipping</span>
              <span class="font-medium" :style="shipping === '0.00' ? 'color:#22c55e;' : 'color:#0F0F1A;'">
                {{ shipping === '0.00' ? 'Free' : '$' + shipping }}
              </span>
            </div>
            <div class="flex justify-between text-base font-black pt-1" style="border-top:1px solid #E5E7EB;">
              <span style="color:#0F0F1A;">Total</span>
              <span style="color:#0D6EFD;">${{ parseFloat(order.total).toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <!-- Two-col: shipping + customer -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

          <!-- Shipping address -->
          <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
            <h2 class="text-sm font-bold mb-3" style="color:#0F0F1A;">Shipping Address</h2>
            <div class="flex items-start gap-2.5">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <div class="text-sm space-y-0.5" style="color:#374151;">
                <p class="font-semibold" style="color:#0F0F1A;">{{ order.customer_name }}</p>
                <p>{{ order.address }}</p>
                <p>
                  {{ order.city }}<span v-if="order.state">, {{ order.state }}</span> {{ order.zip }}
                </p>
                <p>{{ order.country }}</p>
                <p v-if="order.phone" style="color:#9ca3af;">{{ order.phone }}</p>
              </div>
            </div>
          </div>

          <!-- Customer info -->
          <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
            <h2 class="text-sm font-bold mb-3" style="color:#0F0F1A;">Contact</h2>
            <div class="space-y-2.5 text-sm">
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span style="color:#374151;">{{ order.customer_email }}</span>
              </div>
              <div v-if="order.phone" class="flex items-center gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span style="color:#374151;">{{ order.phone }}</span>
              </div>
              <div v-if="order.notes" class="pt-2" style="border-top:1px solid #F3F4F6;">
                <p class="text-xs font-semibold mb-1" style="color:#9ca3af;">Notes</p>
                <p style="color:#374151;">{{ order.notes }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment -->
        <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
          <h2 class="text-sm font-bold mb-3" style="color:#0F0F1A;">Payment Method</h2>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
              style="background:#F5F5F7;">💵</div>
            <div>
              <p class="text-sm font-semibold" style="color:#0F0F1A;">Cash on Delivery</p>
              <p class="text-xs" style="color:#9ca3af;">Pay when your order arrives</p>
            </div>
          </div>
        </div>

      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import axios from 'axios';

const route   = useRoute();
const order   = ref(null);
const loading = ref(true);

const statusMap = {
  pending:    { bg: 'rgba(234,179,8,0.12)',  color: '#a16207' },
  processing: { bg: 'rgba(13,110,253,0.10)', color: '#1d4ed8' },
  shipped:    { bg: 'rgba(124,58,237,0.10)', color: '#6b21a8' },
  delivered:  { bg: 'rgba(34,197,94,0.10)',  color: '#166534' },
  cancelled:  { bg: 'rgba(239,68,68,0.10)',  color: '#991b1b' },
};

const statusSteps = [
  { key: 'pending',    label: 'Pending'    },
  { key: 'processing', label: 'Processing' },
  { key: 'shipped',    label: 'Shipped'    },
  { key: 'delivered',  label: 'Delivered'  },
];

const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];

function statusStyle(s) {
  const m = statusMap[s] ?? statusMap.pending;
  return `background:${m.bg};color:${m.color};`;
}
function statusDot(s) {
  return (statusMap[s] ?? statusMap.pending).color;
}
function stepDone(key) {
  if (!order.value || order.value.status === 'cancelled') return false;
  return statusOrder.indexOf(order.value.status) >= statusOrder.indexOf(key);
}
function stepActive(key) {
  return order.value?.status === key;
}
function fmtDate(d) {
  return new Date(d).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric',
  });
}

const subtotal = computed(() => {
  if (!order.value) return '0.00';
  return order.value.items
    .reduce((s, i) => s + parseFloat(i.price) * i.quantity, 0)
    .toFixed(2);
});

const shipping = computed(() => {
  if (!order.value) return '0.00';
  return (parseFloat(order.value.total) - parseFloat(subtotal.value)).toFixed(2);
});

onMounted(async () => {
  try {
    const tokens = JSON.parse(sessionStorage.getItem('guestOrderTokens') || '{}');
    const params = tokens[route.params.id] ? { token: tokens[route.params.id] } : {};
    const { data } = await axios.get(`/api/orders/${route.params.id}`, { params });
    order.value = data.data;
  } catch {
    order.value = null;
  } finally {
    loading.value = false;
  }
});
</script>
