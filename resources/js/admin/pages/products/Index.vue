<template>
  <div class="space-y-5">
    <div class="flex items-center justify-between">
      <h2 class="font-bold text-lg" style="color:#1e293b;">All Products</h2>
      <router-link to="/admin/products/create" class="btn-primary">+ Add Product</router-link>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">#</th>
              <th class="th">Product</th>
              <th class="th">Type</th>
              <th class="th">Category</th>
              <th class="th">Price</th>
              <th class="th">Stock</th>
              <th class="th">Badge</th>
              <th class="th">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-10 text-sm" style="color:#94a3b8;">Loading…</td>
            </tr>
            <tr v-else-if="!products.length">
              <td colspan="7" class="text-center py-10 text-sm" style="color:#94a3b8;">No products yet.</td>
            </tr>
            <tr v-else v-for="p in products" :key="p.id"
              class="hover:bg-slate-50 transition-colors"
              style="border-bottom:1px solid #f8fafc;">
              <td class="td text-xs font-mono" style="color:#94a3b8;">{{ p.id }}</td>
              <td class="td">
                <div class="flex items-center gap-2.5">
                  <img v-if="p.image" :src="`/storage/${p.image}`" :alt="p.name"
                    class="w-9 h-9 rounded-lg object-cover flex-shrink-0" style="border:1px solid #e5e7eb;" />
                  <span v-else class="text-xl leading-none flex-shrink-0">{{ p.emoji ?? '📦' }}</span>
                  <div>
                    <p class="font-semibold text-sm" style="color:#1e293b;">{{ p.name }}</p>
                    <p class="text-xs" :style="p.is_active ? 'color:#22c55e;' : 'color:#ef4444;'">
                      {{ p.is_active ? 'Active' : 'Inactive' }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="td"><span :class="typeBadge(p.type)">{{ p.type }}</span></td>
              <td class="td text-sm" style="color:#64748b;">{{ p.category?.name ?? '—' }}</td>
              <td class="td">
                <span class="font-bold text-sm" style="color:#1e293b;">${{ p.price }}</span>
                <span v-if="p.original_price" class="text-xs line-through ml-1.5" style="color:#94a3b8;">${{ p.original_price }}</span>
              </td>
              <td class="td text-sm font-medium" style="color:#1e293b;">{{ p.stock }}</td>
              <td class="td">
                <span v-if="p.badge" class="badge">{{ p.badge }}</span>
                <span v-else style="color:#cbd5e1;">—</span>
              </td>
              <td class="td">
                <div class="flex gap-3">
                  <router-link :to="`/admin/products/${p.id}/edit`" class="action-link" style="color:#0D6EFD;">Edit</router-link>
                  <button @click="remove(p)" class="action-link" style="color:#ef4444;">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
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
import { showToast } from '../../store';

const products    = ref([]);
const loading     = ref(true);
const currentPage = ref(1);
const lastPage    = ref(1);

const fetchPage = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/admin/products?page=${page}`);
        products.value    = data.data;
        currentPage.value = data.current_page;
        lastPage.value    = data.last_page;
    } finally {
        loading.value = false;
    }
};

const typeBadge = (t) => {
    const map = { simple:'type-simple', variable:'type-variable', grouped:'type-grouped' };
    return ['type-badge', map[t] ?? ''];
};

const remove = async (p) => {
    if (!confirm(`Delete "${p.name}"? This cannot be undone.`)) return;
    await axios.delete(`/api/admin/products/${p.id}`);
    showToast('Product deleted.');
    fetchPage(currentPage.value);
};

onMounted(() => fetchPage());
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.badge { @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold; background:#dbeafe; color:#1e40af; }
.type-badge { @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize; }
.type-simple   { background:#f1f5f9; color:#64748b; }
.type-variable { background:#ede9fe; color:#5b21b6; }
.type-grouped  { background:#dcfce7; color:#166534; }
.action-link { @apply text-xs font-semibold hover:underline; }
.btn-primary {
    @apply inline-flex items-center px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all duration-200;
    background:#0D6EFD;
}
.btn-primary:hover { background:#0a58ca; }
.page-btn {
    @apply px-4 py-1.5 rounded-lg text-sm font-medium transition-colors;
    background:#f1f5f9; color:#1e293b;
}
.page-btn:hover:not(:disabled) { background:#e2e8f0; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>
