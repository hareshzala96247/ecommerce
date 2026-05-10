<template>
  <div class="space-y-5">
    <!-- Search + Add -->
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #e2e8f0;">
      <h3 class="font-bold mb-4" style="color:#1e293b;">Add Products to Group</h3>

      <div class="relative">
        <input v-model="query" @input="search" @focus="showResults = true"
          class="form-input w-full" placeholder="Search products by name…" />

        <!-- Results dropdown -->
        <div v-if="showResults && results.length"
          class="absolute z-20 mt-1 w-full bg-white rounded-xl shadow-xl overflow-hidden"
          style="border:1px solid #e2e8f0; max-height:220px; overflow-y:auto;">
          <button v-for="p in results" :key="p.id"
            @click="addProduct(p)"
            class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 transition-colors"
            style="border-bottom:1px solid #f8fafc;">
            <span class="text-xl leading-none">{{ p.emoji ?? '📦' }}</span>
            <div class="min-w-0">
              <p class="font-medium text-sm truncate" style="color:#1e293b;">{{ p.name }}</p>
              <p class="text-xs" style="color:#94a3b8;">{{ p.category?.name ?? '—' }} · ${{ p.price ?? '—' }}</p>
            </div>
          </button>
        </div>
        <div v-else-if="showResults && query.length >= 2 && !searching"
          class="absolute z-20 mt-1 w-full bg-white rounded-xl shadow-xl px-4 py-3 text-sm"
          style="border:1px solid #e2e8f0;color:#94a3b8;">
          No products found.
        </div>
      </div>
    </div>

    <!-- Current group items -->
    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="px-6 py-4" style="border-bottom:1px solid #f1f5f9;">
        <h3 class="font-bold" style="color:#1e293b;">Grouped Products ({{ groupItems.length }})</h3>
      </div>

      <div v-if="loadingItems" class="text-sm text-center py-8" style="color:#94a3b8;">Loading…</div>

      <div v-else-if="!groupItems.length" class="text-sm text-center py-8" style="color:#94a3b8;">
        No products in this group yet. Search and add products above.
      </div>

      <table v-else class="w-full">
        <thead>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <th class="th">Product</th>
            <th class="th">Category</th>
            <th class="th">Price</th>
            <th class="th">Type</th>
            <th class="th"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in groupItems" :key="p.id"
            class="hover:bg-slate-50 transition-colors"
            style="border-bottom:1px solid #f8fafc;">
            <td class="td">
              <div class="flex items-center gap-2.5">
                <span class="text-xl leading-none">{{ p.emoji ?? '📦' }}</span>
                <span class="font-semibold text-sm" style="color:#1e293b;">{{ p.name }}</span>
              </div>
            </td>
            <td class="td text-sm" style="color:#64748b;">{{ p.category?.name ?? '—' }}</td>
            <td class="td font-semibold text-sm" style="color:#1e293b;">
              {{ p.price ? `$${p.price}` : '—' }}
            </td>
            <td class="td">
              <span class="type-badge">{{ p.type }}</span>
            </td>
            <td class="td">
              <button @click="removeProduct(p)"
                class="text-xs font-semibold hover:underline" style="color:#ef4444;">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { showToast } from '../../store';

const props = defineProps({ productId: { type: Number, required: true } });

const query       = ref('');
const results     = ref([]);
const showResults = ref(false);
const searching   = ref(false);
const groupItems  = ref([]);
const loadingItems = ref(true);

let searchTimeout = null;

const search = () => {
    clearTimeout(searchTimeout);
    if (query.value.length < 2) { results.value = []; return; }
    searching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get(`/api/admin/products?search=${encodeURIComponent(query.value)}&per_page=10`);
            // Filter out already-grouped items and this product
            const grouped = new Set(groupItems.value.map(p => p.id));
            results.value = data.data.filter(p => p.id !== props.productId && !grouped.has(p.id));
        } finally {
            searching.value = false;
        }
    }, 300);
};

const addProduct = async (p) => {
    showResults.value = false;
    query.value = '';
    results.value = [];
    try {
        const { data } = await axios.post(`/api/admin/products/${props.productId}/group-items`, { child_id: p.id });
        groupItems.value = data.data;
        showToast('Product added to group.');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Error', 'error');
    }
};

const removeProduct = async (p) => {
    await axios.delete(`/api/admin/products/${props.productId}/group-items/${p.id}`);
    groupItems.value = groupItems.value.filter(x => x.id !== p.id);
    showToast('Product removed from group.');
};

const loadItems = async () => {
    loadingItems.value = true;
    try {
        const { data } = await axios.get(`/api/admin/products/${props.productId}/group-items`);
        groupItems.value = data.data;
    } finally {
        loadingItems.value = false;
    }
};

onMounted(loadItems);
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.form-input {
    @apply w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all;
    border: 1.5px solid #e5e7eb; color: #1e293b; background: white;
}
.form-input:focus { border-color:#0D6EFD; box-shadow:0 0 0 3px rgba(13,110,253,0.1); }
.type-badge {
    @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize;
    background:#f1f5f9; color:#64748b;
}
</style>
