<template>
  <div class="space-y-5">
    <h2 class="font-bold text-lg" style="color:#1e293b;">All Users</h2>

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">#</th>
              <th class="th">User</th>
              <th class="th">Email</th>
              <th class="th">Role</th>
              <th class="th">Joined</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">Loading…</td>
            </tr>
            <tr v-else-if="!users.length">
              <td colspan="5" class="text-center py-10 text-sm" style="color:#94a3b8;">No users yet.</td>
            </tr>
            <tr v-else v-for="u in users" :key="u.id"
              class="hover:bg-slate-50 transition-colors"
              style="border-bottom:1px solid #f8fafc;">
              <td class="td text-xs font-mono" style="color:#94a3b8;">{{ u.id }}</td>
              <td class="td">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                    :style="`background:${u.role === 'admin' ? '#0D6EFD' : '#64748b'};`">
                    {{ u.name[0].toUpperCase() }}
                  </div>
                  <span class="font-semibold text-sm" style="color:#1e293b;">{{ u.name }}</span>
                </div>
              </td>
              <td class="td text-sm" style="color:#64748b;">{{ u.email }}</td>
              <td class="td">
                <span class="badge" :style="u.role === 'admin'
                  ? 'background:#dbeafe;color:#1e40af;'
                  : 'background:#f1f5f9;color:#64748b;'">
                  {{ u.role }}
                </span>
              </td>
              <td class="td text-sm" style="color:#64748b;">{{ fmtDate(u.created_at) }}</td>
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

const users       = ref([]);
const loading     = ref(true);
const currentPage = ref(1);
const lastPage    = ref(1);

const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

const fetchPage = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/admin/users?page=${page}`);
        users.value       = data.data;
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
.page-btn {
    @apply px-4 py-1.5 rounded-lg text-sm font-medium transition-colors;
    background:#f1f5f9; color:#1e293b;
}
.page-btn:hover:not(:disabled) { background:#e2e8f0; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>
