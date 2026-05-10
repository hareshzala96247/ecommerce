<template>
  <div class="space-y-5">
    <h2 class="font-bold text-lg" style="color:#1e293b;">All Orders</h2>

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">Order</th>
              <th class="th">Customer</th>
              <th class="th">Total</th>
              <th class="th">Status</th>
              <th class="th">Date</th>
              <th class="th">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-10 text-sm" style="color:#94a3b8;">Loading…</td>
            </tr>
            <tr v-else-if="!orders.length">
              <td colspan="6" class="text-center py-10 text-sm" style="color:#94a3b8;">No orders yet.</td>
            </tr>
            <tr v-else v-for="o in orders" :key="o.id"
              class="hover:bg-slate-50 transition-colors"
              style="border-bottom:1px solid #f8fafc;">
              <td class="td font-mono text-xs font-semibold" style="color:#64748b;">#{{ String(o.id).padStart(4,'0') }}</td>
              <td class="td">
                <p class="font-semibold text-sm" style="color:#1e293b;">{{ o.customer_name }}</p>
                <p class="text-xs" style="color:#94a3b8;">{{ o.customer_email }}</p>
              </td>
              <td class="td font-bold text-sm" style="color:#1e293b;">${{ Number(o.total).toFixed(2) }}</td>
              <td class="td"><span :class="statusBadge(o.status)">{{ o.status }}</span></td>
              <td class="td text-sm" style="color:#64748b;">{{ fmtDate(o.created_at) }}</td>
              <td class="td">
                <router-link :to="`/admin/orders/${o.id}`" class="text-xs font-semibold hover:underline" style="color:#0D6EFD;">
                  View →
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="lastPage > 1" class="flex items-center justify-between px-6 py-4" style="border-top:1px solid #f1f5f9;">
        <p class="text-sm" style="color:#64748b;">Page {{ currentPage }} of {{ lastPage }}</p>
        <div class="flex gap-2">
          <button @click="fetchPage(currentPage - 1)" :disabled="currentPage === 1" class="page-btn">← Prev</button>
          <button @click="fetchPage(currentPage + 1)" :disabled="currentPage === lastPage" class="page-btn">Next →</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders      = ref([]);
const loading     = ref(true);
const currentPage = ref(1);
const lastPage    = ref(1);

const statusBadge = (s) => {
    const map = { pending:'badge-yellow', processing:'badge-blue', shipped:'badge-purple', delivered:'badge-green', cancelled:'badge-red' };
    return ['badge', map[s] ?? ''];
};

const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

const fetchPage = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/admin/orders?page=${page}`);
        orders.value      = data.data;
        currentPage.value = data.current_page;
        lastPage.value    = data.last_page;
    } finally {
        loading.value = false;
    }
};

onMounted(() => fetchPage());
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.badge { @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize; }
.badge-yellow { background:#fef9c3; color:#854d0e; }
.badge-blue   { background:#dbeafe; color:#1e40af; }
.badge-purple { background:#f3e8ff; color:#6b21a8; }
.badge-green  { background:#dcfce7; color:#166534; }
.badge-red    { background:#fee2e2; color:#991b1b; }
.page-btn {
    @apply px-4 py-1.5 rounded-lg text-sm font-medium transition-colors;
    background:#f1f5f9; color:#1e293b;
}
.page-btn:hover:not(:disabled) { background:#e2e8f0; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>
