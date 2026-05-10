<template>
  <div class="space-y-5">
    <div class="flex items-center justify-between">
      <h2 class="font-bold text-lg" style="color:#1e293b;">All Categories</h2>
      <router-link to="/admin/categories/create" class="btn-primary">+ Add Category</router-link>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <table class="w-full">
        <thead>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <th class="th">#</th>
            <th class="th">Category</th>
            <th class="th">Description</th>
            <th class="th">Products</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">Loading…</td>
          </tr>
          <tr v-else-if="!categories.length">
            <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">No categories yet.</td>
          </tr>
          <tr v-else v-for="c in categories" :key="c.id"
            class="hover:bg-slate-50 transition-colors"
            style="border-bottom:1px solid #f8fafc;">
            <td class="td text-xs font-mono" style="color:#94a3b8;">{{ c.id }}</td>
            <td class="td">
              <div class="flex items-center gap-3">
                <span class="text-2xl leading-none">{{ c.icon ?? '📁' }}</span>
                <span class="font-semibold text-sm" style="color:#1e293b;">{{ c.name }}</span>
              </div>
            </td>
            <td class="td text-sm" style="color:#64748b;">{{ c.description ?? '—' }}</td>
            <td class="td">
              <span class="font-semibold text-sm" style="color:#1e293b;">{{ c.products_count }}</span>
            </td>
            <td class="td">
              <div class="flex gap-3">
                <router-link :to="`/admin/categories/${c.id}/edit`" class="action-link" style="color:#0D6EFD;">Edit</router-link>
                <button @click="remove(c)" class="action-link" style="color:#ef4444;">Delete</button>
              </div>
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

const categories = ref([]);
const loading    = ref(true);

const load = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/admin/categories');
        categories.value = data.data;
    } finally {
        loading.value = false;
    }
};

const remove = async (c) => {
    if (!confirm(`Delete "${c.name}"? Products in this category won't be deleted.`)) return;
    await axios.delete(`/api/admin/categories/${c.id}`);
    showToast('Category deleted.');
    load();
};

onMounted(load);
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-6 py-3; color:#94a3b8; }
.td { @apply px-6 py-3.5; }
.action-link { @apply text-xs font-semibold hover:underline; }
.btn-primary {
    @apply inline-flex items-center px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all duration-200;
    background:#0D6EFD;
}
.btn-primary:hover { background:#0a58ca; }
</style>
