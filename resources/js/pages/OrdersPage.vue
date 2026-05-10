<template>
  <div class="min-h-[calc(100vh-120px)] px-4 py-10" style="background:#FAFAFA;">
    <div class="max-w-3xl mx-auto">

      <!-- Header -->
      <div class="flex items-center gap-3 mb-8">
        <RouterLink to="/account"
          class="w-9 h-9 rounded-xl flex items-center justify-center"
          style="background:#fff;border:1px solid #E5E7EB;">
          <svg class="w-4 h-4" style="color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </RouterLink>
        <div>
          <h1 class="text-2xl font-black" style="color:#0F0F1A;">My Orders</h1>
          <p class="text-sm" style="color:#9ca3af;">Track and manage your orders</p>
        </div>
      </div>

      <!-- Not logged in -->
      <div v-if="!authStore.user" class="text-center py-24">
        <div class="text-5xl mb-4">🔒</div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">Sign in to view your orders</h2>
        <p class="text-sm mb-6" style="color:#6b7280;">You need to be logged in to see your order history.</p>
        <RouterLink to="/login"
          class="px-6 py-2.5 rounded-xl text-sm font-bold text-white"
          style="background:#0D6EFD;box-shadow:0 4px 12px rgba(13,110,253,0.3);">
          Sign In
        </RouterLink>
      </div>

      <!-- Loading -->
      <div v-else-if="loading" class="space-y-4">
        <div v-for="n in 3" :key="n"
          class="bg-white rounded-2xl h-28 animate-pulse"
          style="border:1px solid rgba(0,0,0,0.06);"></div>
      </div>

      <!-- Empty -->
      <div v-else-if="orders.length === 0" class="text-center py-24">
        <div class="inline-flex w-20 h-20 rounded-3xl items-center justify-center mb-6"
          style="background:#F5F5F7;">
          <svg class="w-10 h-10" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">No orders yet</h2>
        <p class="text-sm mb-6" style="color:#6b7280;">When you place an order it will appear here.</p>
        <RouterLink to="/shop"
          class="px-6 py-2.5 rounded-xl text-sm font-bold text-white"
          style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);box-shadow:0 4px 12px rgba(13,110,253,0.3);">
          Start Shopping
        </RouterLink>
      </div>

      <!-- Orders list -->
      <div v-else class="space-y-4">
        <div v-for="order in orders" :key="order.id"
          class="bg-white rounded-2xl p-5 transition-all duration-200"
          style="border:1px solid rgba(0,0,0,0.06);">

          <!-- Order header row -->
          <div class="flex items-start justify-between gap-3 mb-4">
            <div>
              <p class="text-base font-black" style="color:#0F0F1A;">
                #{{ String(order.id).padStart(5, '0') }}
              </p>
              <p class="text-xs mt-0.5" style="color:#9ca3af;">{{ fmtDate(order.created_at) }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize flex-shrink-0"
              :style="statusStyle(order.status)">
              <span class="w-1.5 h-1.5 rounded-full" :style="{ background: statusDot(order.status) }"></span>
              {{ order.status }}
            </span>
          </div>

          <!-- Items preview -->
          <div class="flex flex-wrap gap-2 mb-4">
            <div v-for="item in order.items" :key="item.id"
              class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium"
              style="background:#F5F5F7;color:#374151;">
              <span>{{ item.product_name }}</span>
              <span style="color:#9ca3af;">× {{ item.quantity }}</span>
            </div>
          </div>

          <!-- Footer row -->
          <div class="flex items-center justify-between pt-3" style="border-top:1px solid #F3F4F6;">
            <div>
              <span class="text-xs" style="color:#9ca3af;">Total</span>
              <p class="text-base font-black" style="color:#0D6EFD;">${{ parseFloat(order.total).toFixed(2) }}</p>
            </div>
            <RouterLink :to="`/orders/${order.id}`"
              class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-150"
              style="border:1.5px solid #E5E7EB;color:#374151;"
              @mouseenter="(e) => { e.currentTarget.style.borderColor='#0D6EFD'; e.currentTarget.style.color='#0D6EFD'; }"
              @mouseleave="(e) => { e.currentTarget.style.borderColor='#E5E7EB'; e.currentTarget.style.color='#374151'; }">
              View Details
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </RouterLink>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import axios from 'axios';
import { authStore, initAuth } from '../store/auth';

const orders  = ref([]);
const loading = ref(true);

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
function fmtDate(d) {
  return new Date(d).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
  });
}

onMounted(async () => {
  await initAuth();
  if (!authStore.user) { loading.value = false; return; }
  try {
    const { data } = await axios.get('/api/orders');
    orders.value = data.data ?? [];
  } catch {
    orders.value = [];
  } finally {
    loading.value = false;
  }
});
</script>
