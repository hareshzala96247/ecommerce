<template>
  <div class="space-y-6">
    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="card in statCards" :key="card.label"
        class="bg-white rounded-2xl p-5"
        style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold uppercase tracking-widest" style="color:#94a3b8;">{{ card.label }}</span>
          <span class="text-2xl">{{ card.icon }}</span>
        </div>
        <p class="text-2xl font-black" style="color:#1e293b;">
          {{ card.value }}
        </p>
      </div>
    </div>

    <!-- Recent orders -->
    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="px-6 py-4" style="border-bottom:1px solid #f1f5f9;">
        <h2 class="font-bold" style="color:#1e293b;">Recent Orders</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">Order</th>
              <th class="th">Customer</th>
              <th class="th">Total</th>
              <th class="th">Status</th>
              <th class="th">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">Loading…</td>
            </tr>
            <tr v-else-if="!recentOrders.length">
              <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">No orders yet.</td>
            </tr>
            <tr v-else v-for="o in recentOrders" :key="o.id"
              class="hover:bg-slate-50 transition-colors"
              style="border-bottom:1px solid #f8fafc;">
              <td class="td font-mono text-xs" style="color:#64748b;">#{{ String(o.id).padStart(4,'0') }}</td>
              <td class="td font-medium text-sm" style="color:#1e293b;">{{ o.customer_name }}</td>
              <td class="td font-bold text-sm" style="color:#1e293b;">${{ Number(o.total).toFixed(2) }}</td>
              <td class="td"><span :class="statusBadge(o.status)">{{ o.status }}</span></td>
              <td class="td text-sm" style="color:#64748b;">{{ fmtDate(o.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const loading      = ref(true);
const stats        = ref({});
const recentOrders = ref([]);

const statCards = computed(() => [
    { label: 'Products',       icon: '📦', value: stats.value.products      ?? '—' },
    { label: 'Categories',     icon: '🏷️', value: stats.value.categories    ?? '—' },
    { label: 'Orders',         icon: '🛒', value: stats.value.orders        ?? '—' },
    { label: 'Revenue',        icon: '💰', value: stats.value.revenue != null ? `$${Number(stats.value.revenue).toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '—' },
    { label: 'Users',          icon: '👥', value: stats.value.users         ?? '—' },
    { label: 'Pending Orders', icon: '⏳', value: stats.value.pending_orders ?? '—' },
]);

const statusBadge = (s) => {
    const map = { pending:'badge-yellow', processing:'badge-blue', shipped:'badge-purple', delivered:'badge-green', cancelled:'badge-red' };
    return ['badge', map[s] ?? ''];
};

const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/admin/dashboard');
        stats.value        = data.stats;
        recentOrders.value = data.recent_orders;
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
@reference "../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.badge { @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize; }
.badge-yellow { background:#fef9c3; color:#854d0e; }
.badge-blue   { background:#dbeafe; color:#1e40af; }
.badge-purple { background:#f3e8ff; color:#6b21a8; }
.badge-green  { background:#dcfce7; color:#166534; }
.badge-red    { background:#fee2e2; color:#991b1b; }
</style>
