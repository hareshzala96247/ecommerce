<template>
  <div class="max-w-2xl space-y-6">
    <router-link to="/admin/orders" class="inline-flex items-center gap-1 text-sm font-medium"
      style="color:#64748b;">
      ← Back to Orders
    </router-link>

    <div v-if="loading" class="text-sm py-10 text-center" style="color:#94a3b8;">Loading…</div>

    <template v-else-if="order">
      <!-- Header card -->
      <div class="bg-white rounded-2xl p-6" style="border:1px solid #e2e8f0;">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-black" style="color:#1e293b;">
              Order #{{ String(order.id).padStart(4,'0') }}
            </h2>
            <p class="text-sm mt-1" style="color:#64748b;">{{ fmtDate(order.created_at) }}</p>
          </div>
          <span :class="statusBadge(order.status)">{{ order.status }}</span>
        </div>

        <!-- Customer -->
        <div class="mt-5 pt-5" style="border-top:1px solid #f1f5f9;">
          <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Customer</p>
          <p class="font-semibold" style="color:#1e293b;">{{ order.customer_name }}</p>
          <p class="text-sm" style="color:#64748b;">{{ order.customer_email }}</p>
          <p v-if="order.phone" class="text-sm mt-0.5" style="color:#64748b;">{{ order.phone }}</p>
        </div>

        <!-- Shipping address -->
        <div v-if="order.address" class="mt-5 pt-5" style="border-top:1px solid #f1f5f9;">
          <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Shipping Address</p>
          <p class="text-sm" style="color:#1e293b;">{{ order.address }}</p>
          <p class="text-sm" style="color:#64748b;">
            {{ order.city }}<span v-if="order.state">, {{ order.state }}</span> {{ order.zip }}
          </p>
          <p class="text-sm" style="color:#64748b;">{{ order.country }}</p>
        </div>

        <!-- Notes -->
        <div v-if="order.notes" class="mt-5 pt-5" style="border-top:1px solid #f1f5f9;">
          <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Order Notes</p>
          <p class="text-sm" style="color:#64748b;">{{ order.notes }}</p>
        </div>

        <!-- Status update -->
        <div class="mt-5 pt-5" style="border-top:1px solid #f1f5f9;">
          <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:#94a3b8;">Update Status</p>
          <div class="flex gap-3">
            <select v-model="newStatus" class="px-4 py-2 rounded-xl text-sm outline-none transition-all"
              style="border:1.5px solid #e5e7eb;color:#1e293b;background:white;">
              <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
            </select>
            <button @click="updateStatus"
              :disabled="updating || newStatus === order.status"
              class="px-5 py-2 rounded-xl text-white text-sm font-bold transition-all duration-200"
              :style="(updating || newStatus === order.status)
                ? 'background:#93c5fd;cursor:not-allowed;'
                : 'background:#0D6EFD;'">
              {{ updating ? 'Saving…' : 'Update' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Items table -->
      <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4" style="border-bottom:1px solid #f1f5f9;">
          <h3 class="font-bold" style="color:#1e293b;">Order Items</h3>
        </div>
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">Product</th>
              <th class="th">Unit Price</th>
              <th class="th">Qty</th>
              <th class="th">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.items" :key="item.id"
              style="border-bottom:1px solid #f8fafc;">
              <td class="td font-medium text-sm" style="color:#1e293b;">{{ item.product_name }}</td>
              <td class="td text-sm" style="color:#64748b;">${{ item.price }}</td>
              <td class="td text-sm" style="color:#64748b;">{{ item.quantity }}</td>
              <td class="td font-semibold text-sm" style="color:#1e293b;">
                ${{ (item.price * item.quantity).toFixed(2) }}
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr style="border-top:2px solid #e2e8f0;">
              <td colspan="3" class="td text-right font-bold text-sm" style="color:#64748b;">Total</td>
              <td class="td font-black text-base" style="color:#1e293b;">${{ Number(order.total).toFixed(2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { showToast } from '../../store';

const route     = useRoute();
const order     = ref(null);
const loading   = ref(true);
const updating  = ref(false);
const newStatus = ref('');
const statuses  = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

const statusBadge = (s) => {
    const map = { pending:'badge-yellow', processing:'badge-blue', shipped:'badge-purple', delivered:'badge-green', cancelled:'badge-red' };
    return ['badge', map[s] ?? ''];
};

const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
});

const updateStatus = async () => {
    updating.value = true;
    try {
        const { data } = await axios.patch(`/api/admin/orders/${route.params.id}/status`, { status: newStatus.value });
        order.value.status = data.data.status;
        showToast('Order status updated.');
    } catch {
        showToast('Failed to update status.', 'error');
    } finally {
        updating.value = false;
    }
};

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/admin/orders/${route.params.id}`);
        order.value     = data.data;
        newStatus.value = data.data.status;
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.badge { @apply inline-block px-3 py-1 rounded-full text-xs font-bold capitalize; }
.badge-yellow { background:#fef9c3; color:#854d0e; }
.badge-blue   { background:#dbeafe; color:#1e40af; }
.badge-purple { background:#f3e8ff; color:#6b21a8; }
.badge-green  { background:#dcfce7; color:#166534; }
.badge-red    { background:#fee2e2; color:#991b1b; }
</style>
