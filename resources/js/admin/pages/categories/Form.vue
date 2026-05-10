<template>
  <div class="max-w-lg">
    <router-link to="/admin/categories" class="inline-flex items-center gap-1 text-sm font-medium mb-6"
      style="color:#64748b;">
      ← Back to Categories
    </router-link>

    <div class="bg-white rounded-2xl p-8" style="border:1px solid #e2e8f0;">
      <h2 class="text-lg font-bold mb-6" style="color:#1e293b;">
        {{ isEdit ? 'Edit Category' : 'New Category' }}
      </h2>

      <form @submit.prevent="submit" class="space-y-5">
        <div v-if="error" class="px-4 py-3 rounded-xl text-sm font-medium"
          style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
          {{ error }}
        </div>

        <div>
          <label class="form-label">Name *</label>
          <input v-model="form.name" required class="form-input" placeholder="e.g. Electronics" />
        </div>

        <div>
          <label class="form-label">Icon (emoji)</label>
          <input v-model="form.icon" class="form-input" placeholder="e.g. 📱" maxlength="10" />
        </div>

        <div>
          <label class="form-label">Description</label>
          <textarea v-model="form.description" class="form-input" rows="3"
            placeholder="Short description of this category…"></textarea>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="loading" class="btn-primary">
            {{ loading ? 'Saving…' : (isEdit ? 'Update Category' : 'Create Category') }}
          </button>
          <router-link to="/admin/categories" class="btn-secondary">Cancel</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { showToast } from '../../store';

const route  = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);

const form    = reactive({ name: '', icon: '', description: '' });
const loading = ref(false);
const error   = ref('');

onMounted(async () => {
    if (isEdit.value) {
        const { data } = await axios.get(`/api/admin/categories/${route.params.id}`);
        Object.assign(form, {
            name:        data.data.name,
            icon:        data.data.icon        ?? '',
            description: data.data.description ?? '',
        });
    }
});

const submit = async () => {
    loading.value = true;
    error.value   = '';
    try {
        if (isEdit.value) {
            await axios.put(`/api/admin/categories/${route.params.id}`, form);
            showToast('Category updated.');
        } else {
            await axios.post('/api/admin/categories', form);
            showToast('Category created.');
        }
        router.push('/admin/categories');
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Something went wrong.';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
@reference "../../../../css/app.css";
.form-label { @apply block text-sm font-semibold mb-1.5; color:#374151; }
.form-input {
    @apply w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all;
    border: 1.5px solid #e5e7eb;
    color: #1e293b;
    background: white;
}
.form-input:focus { border-color:#0D6EFD; box-shadow:0 0 0 3px rgba(13,110,253,0.1); }
.btn-primary {
    @apply inline-flex items-center px-6 py-2.5 rounded-xl text-white text-sm font-bold transition-all duration-200;
    background:#0D6EFD;
}
.btn-primary:hover:not(:disabled) { background:#0a58ca; }
.btn-primary:disabled { opacity:.6; cursor:not-allowed; }
.btn-secondary {
    @apply inline-flex items-center px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200;
    background:#f1f5f9; color:#1e293b;
}
.btn-secondary:hover { background:#e2e8f0; }
</style>
