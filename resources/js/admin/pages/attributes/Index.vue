<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="font-bold text-lg" style="color:#1e293b;">Attributes</h2>
      <button @click="showNewAttr = true" class="btn-primary">+ New Attribute</button>
    </div>

    <!-- New attribute inline form -->
    <div v-if="showNewAttr" class="bg-white rounded-2xl p-5" style="border:1px solid #e2e8f0;">
      <p class="text-sm font-bold mb-3" style="color:#1e293b;">New Attribute</p>
      <div class="flex gap-3">
        <input v-model="newAttrName" class="form-input flex-1" placeholder="e.g. Size, Color, Material"
          @keyup.enter="createAttr" />
        <button @click="createAttr" :disabled="!newAttrName.trim()" class="btn-primary">Add</button>
        <button @click="showNewAttr = false; newAttrName = ''" class="btn-secondary">Cancel</button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-sm py-6 text-center" style="color:#94a3b8;">Loading…</div>

    <!-- Attribute cards -->
    <div v-else-if="!attributes.length" class="bg-white rounded-2xl p-8 text-center" style="border:1px solid #e2e8f0;">
      <p class="text-sm" style="color:#94a3b8;">No attributes yet. Add one to define product options like Size or Color.</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="attr in attributes" :key="attr.id"
        class="bg-white rounded-2xl p-5" style="border:1px solid #e2e8f0;">

        <!-- Attribute header -->
        <div class="flex items-center justify-between mb-4">
          <div v-if="editingAttr === attr.id" class="flex items-center gap-2 flex-1 mr-4">
            <input v-model="editAttrName" class="form-input" @keyup.enter="saveAttr(attr)" />
            <button @click="saveAttr(attr)" class="btn-primary text-xs px-3 py-1.5">Save</button>
            <button @click="editingAttr = null" class="btn-secondary text-xs px-3 py-1.5">Cancel</button>
          </div>
          <h3 v-else class="font-bold" style="color:#1e293b;">{{ attr.name }}</h3>

          <div class="flex gap-2 flex-shrink-0">
            <button v-if="editingAttr !== attr.id" @click="startEditAttr(attr)"
              class="text-xs font-semibold hover:underline" style="color:#0D6EFD;">Rename</button>
            <button @click="deleteAttr(attr)"
              class="text-xs font-semibold hover:underline" style="color:#ef4444;">Delete</button>
          </div>
        </div>

        <!-- Values -->
        <div class="flex flex-wrap gap-2 mb-3">
          <span v-for="val in attr.values" :key="val.id"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium"
            style="background:#f1f5f9;color:#1e293b;">
            {{ val.value }}
            <button @click="deleteValue(attr, val)"
              class="text-gray-400 hover:text-red-500 transition-colors leading-none text-base">×</button>
          </span>
          <span v-if="!attr.values.length" class="text-xs italic" style="color:#94a3b8;">No values yet</span>
        </div>

        <!-- Add value -->
        <div class="flex gap-2 mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
          <input v-model="newValues[attr.id]" class="form-input text-sm py-1.5"
            :placeholder="`Add value (e.g. ${attr.name === 'Size' ? 'M' : 'Red'})`"
            @keyup.enter="addValue(attr)" />
          <button @click="addValue(attr)" :disabled="!newValues[attr.id]?.trim()"
            class="btn-primary text-xs px-3 py-1.5">Add</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { showToast } from '../../store';

const attributes  = ref([]);
const loading     = ref(true);
const showNewAttr = ref(false);
const newAttrName = ref('');
const newValues   = reactive({});
const editingAttr = ref(null);
const editAttrName = ref('');

const load = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/admin/attributes');
        attributes.value = data.data;
    } finally {
        loading.value = false;
    }
};

const createAttr = async () => {
    if (!newAttrName.value.trim()) return;
    try {
        const { data } = await axios.post('/api/admin/attributes', { name: newAttrName.value.trim() });
        attributes.value.push(data.data);
        newAttrName.value = '';
        showNewAttr.value = false;
        showToast('Attribute created.');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Error', 'error');
    }
};

const startEditAttr = (attr) => {
    editingAttr.value = attr.id;
    editAttrName.value = attr.name;
};

const saveAttr = async (attr) => {
    try {
        const { data } = await axios.put(`/api/admin/attributes/${attr.id}`, { name: editAttrName.value });
        attr.name = data.data.name;
        editingAttr.value = null;
        showToast('Attribute renamed.');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Error', 'error');
    }
};

const deleteAttr = async (attr) => {
    if (!confirm(`Delete attribute "${attr.name}" and all its values?`)) return;
    await axios.delete(`/api/admin/attributes/${attr.id}`);
    attributes.value = attributes.value.filter(a => a.id !== attr.id);
    showToast('Attribute deleted.');
};

const addValue = async (attr) => {
    const val = newValues[attr.id]?.trim();
    if (!val) return;
    try {
        const { data } = await axios.post(`/api/admin/attributes/${attr.id}/values`, { value: val });
        attr.values.push(data.data);
        newValues[attr.id] = '';
        showToast('Value added.');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Error', 'error');
    }
};

const deleteValue = async (attr, val) => {
    await axios.delete(`/api/admin/attributes/${attr.id}/values/${val.id}`);
    attr.values = attr.values.filter(v => v.id !== val.id);
    showToast('Value removed.');
};

onMounted(load);
</script>

<style scoped>
@reference "../../../../css/app.css";
.form-input {
    @apply w-full px-3 py-2 rounded-xl text-sm outline-none transition-all;
    border: 1.5px solid #e5e7eb; color: #1e293b; background: white;
}
.form-input:focus { border-color:#0D6EFD; box-shadow:0 0 0 3px rgba(13,110,253,0.1); }
.btn-primary {
    @apply inline-flex items-center px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all duration-200 flex-shrink-0;
    background:#0D6EFD;
}
.btn-primary:hover:not(:disabled) { background:#0a58ca; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-secondary {
    @apply inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex-shrink-0;
    background:#f1f5f9; color:#1e293b;
}
.btn-secondary:hover { background:#e2e8f0; }
</style>
