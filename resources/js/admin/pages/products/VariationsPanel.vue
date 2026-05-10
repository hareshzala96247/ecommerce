<template>
  <div class="space-y-5">

    <!-- Generate section -->
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #e2e8f0;">
      <h3 class="font-bold mb-4" style="color:#1e293b;">Generate Variations</h3>

      <div v-if="!allAttributes.length" class="text-sm" style="color:#94a3b8;">
        No attributes defined yet.
        <router-link to="/admin/attributes" class="font-semibold hover:underline" style="color:#0D6EFD;">
          Create attributes first →
        </router-link>
      </div>

      <template v-else>
        <!-- Attribute + value selection -->
        <div class="space-y-3 mb-5">
          <div v-for="attr in allAttributes" :key="attr.id"
            class="p-4 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <div class="flex items-center gap-2 mb-3">
              <input type="checkbox" :id="`attr-${attr.id}`"
                v-model="selectedAttrs" :value="attr.id"
                class="w-4 h-4 rounded" style="accent-color:#0D6EFD;" />
              <label :for="`attr-${attr.id}`" class="font-semibold text-sm cursor-pointer" style="color:#1e293b;">
                {{ attr.name }}
              </label>
            </div>
            <div v-if="selectedAttrs.includes(attr.id)" class="flex flex-wrap gap-2 ml-6">
              <label v-for="val in attr.values" :key="val.id"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm cursor-pointer transition-all"
                :style="isValueSelected(attr.id, val.id)
                  ? 'background:#0D6EFD;color:white;'
                  : 'background:#e2e8f0;color:#64748b;'">
                <input type="checkbox" class="hidden"
                  :checked="isValueSelected(attr.id, val.id)"
                  @change="toggleValue(attr.id, val.id)" />
                {{ val.value }}
              </label>
              <span v-if="!attr.values.length" class="text-xs italic" style="color:#94a3b8;">No values</span>
            </div>
          </div>
        </div>

        <!-- Default price/stock + generate button -->
        <div class="flex flex-wrap items-end gap-4">
          <div>
            <label class="block text-xs font-semibold mb-1" style="color:#64748b;">Default Price</label>
            <input v-model="defaultPrice" type="number" step="0.01" min="0"
              class="form-input w-32" placeholder="0.00" />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1" style="color:#64748b;">Default Stock</label>
            <input v-model="defaultStock" type="number" min="0"
              class="form-input w-24" placeholder="0" />
          </div>
          <button @click="generate" :disabled="generating || !canGenerate" class="btn-primary">
            {{ generating ? 'Generating…' : 'Generate Variations' }}
          </button>
        </div>
      </template>
    </div>

    <!-- Variations table -->
    <div v-if="variations.length" class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #e2e8f0;">
      <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid #f1f5f9;">
        <h3 class="font-bold" style="color:#1e293b;">Variations ({{ variations.length }})</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <th class="th">Attributes</th>
              <th class="th">Image</th>
              <th class="th">Price *</th>
              <th class="th">Original Price</th>
              <th class="th">Stock *</th>
              <th class="th">SKU</th>
              <th class="th">Active</th>
              <th class="th"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="v in variations" :key="v.id"
              class="hover:bg-slate-50 transition-colors"
              style="border-bottom:1px solid #f8fafc;">
              <!-- Attribute labels -->
              <td class="td">
                <div class="flex flex-wrap gap-1">
                  <span v-for="av in v.attribute_values" :key="av.id"
                    class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                    style="background:#dbeafe;color:#1e40af;">
                    {{ av.attribute?.name }}: {{ av.value }}
                  </span>
                </div>
              </td>
              <!-- Variation Image -->
              <td class="td">
                <div class="flex flex-col items-start gap-1">
                  <img v-if="v.image" :src="`/storage/${v.image}`" alt="Variation image"
                    class="w-12 h-12 object-cover rounded-lg mb-1" style="border:1px solid #e5e7eb;" />
                  <div class="flex items-center gap-2">
                    <label class="cursor-pointer">
                      <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                        style="position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;pointer-events:none;"
                        :disabled="v._imgUploading" @change="uploadVariationImage(v, $event)" />
                      <span class="text-xs font-semibold" :style="v._imgUploading ? 'color:#94a3b8;' : 'color:#0D6EFD;'">
                        {{ v._imgUploading ? '…' : (v.image ? 'Change' : 'Upload') }}
                      </span>
                    </label>
                    <button v-if="v.image" type="button" @click="removeVariationImage(v)"
                      class="text-xs font-semibold" style="color:#ef4444;">
                      Remove
                    </button>
                  </div>
                </div>
              </td>

              <td class="td">
                <input v-model="v._price" type="number" step="0.01" min="0"
                  class="form-input w-24" placeholder="0.00" />
              </td>
              <td class="td">
                <input v-model="v._original_price" type="number" step="0.01" min="0"
                  class="form-input w-24" placeholder="—" />
              </td>
              <td class="td">
                <input v-model="v._stock" type="number" min="0"
                  class="form-input w-20" placeholder="0" />
              </td>
              <td class="td">
                <input v-model="v._sku" class="form-input w-28" placeholder="optional" />
              </td>
              <td class="td">
                <input type="checkbox" v-model="v._is_active"
                  class="w-4 h-4 rounded" style="accent-color:#0D6EFD;" />
              </td>
              <td class="td">
                <div class="flex gap-2">
                  <button @click="saveVariation(v)" :disabled="v._saving"
                    class="text-xs font-semibold hover:underline" style="color:#0D6EFD;">
                    {{ v._saving ? '…' : 'Save' }}
                  </button>
                  <button @click="deleteVariation(v)"
                    class="text-xs font-semibold hover:underline" style="color:#ef4444;">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else-if="loaded" class="text-sm text-center py-4" style="color:#94a3b8;">
      No variations yet. Select attributes above and click Generate.
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { showToast } from '../../store';

const props = defineProps({ productId: { type: Number, required: true } });

const allAttributes  = ref([]);
const variations     = ref([]);
const selectedAttrs  = ref([]);
const selectedValues = ref({}); // { attrId: Set of valueIds }
const defaultPrice   = ref('');
const defaultStock   = ref(0);
const generating     = ref(false);
const loaded         = ref(false);

const canGenerate = computed(() =>
    selectedAttrs.value.some(attrId =>
        selectedValues.value[attrId]?.size > 0
    )
);

const isValueSelected = (attrId, valId) =>
    selectedValues.value[attrId]?.has(valId) ?? false;

const toggleValue = (attrId, valId) => {
    if (!selectedValues.value[attrId]) {
        selectedValues.value[attrId] = new Set();
    }
    const set = selectedValues.value[attrId];
    if (set.has(valId)) set.delete(valId);
    else set.add(valId);
};

const loadData = async () => {
    const [attrsRes, varsRes] = await Promise.all([
        axios.get('/api/admin/attributes'),
        axios.get(`/api/admin/products/${props.productId}/variations`),
    ]);
    allAttributes.value = attrsRes.data.data;
    setVariations(varsRes.data.data);
    loaded.value = true;
};

const setVariations = (data) => {
    variations.value = data.map(v => ({
        ...v,
        _price:          v.price,
        _original_price: v.original_price ?? '',
        _stock:          v.stock,
        _sku:            v.sku ?? '',
        _is_active:      v.is_active,
        _saving:         false,
        _imgUploading:   false,
    }));
};

const generate = async () => {
    generating.value = true;
    try {
        const groups = selectedAttrs.value
            .filter(attrId => selectedValues.value[attrId]?.size > 0)
            .map(attrId => [...selectedValues.value[attrId]]);

        const { data } = await axios.post(
            `/api/admin/products/${props.productId}/variations/generate`,
            { groups, default_price: defaultPrice.value || 0, default_stock: defaultStock.value || 0 }
        );
        setVariations(data.data);
        showToast(data.message);
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Generation failed.', 'error');
    } finally {
        generating.value = false;
    }
};

const saveVariation = async (v) => {
    v._saving = true;
    try {
        await axios.put(
            `/api/admin/products/${props.productId}/variations/${v.id}`,
            {
                price:          v._price,
                original_price: v._original_price || null,
                stock:          v._stock,
                sku:            v._sku || null,
                is_active:      v._is_active,
            }
        );
        showToast('Variation saved.');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Save failed.', 'error');
    } finally {
        v._saving = false;
    }
};

const uploadVariationImage = async (v, e) => {
    const file = e.target.files[0];
    if (!file) return;
    e.target.value = '';
    v._imgUploading = true;
    try {
        const fd = new FormData();
        fd.append('image', file);
        const { data } = await axios.post(
            `/api/admin/products/${props.productId}/variations/${v.id}/image`,
            fd
        );
        v.image = data.data.image;
        showToast('Image uploaded.');
    } catch {
        showToast('Upload failed.', 'error');
    } finally {
        v._imgUploading = false;
    }
};

const removeVariationImage = async (v) => {
    if (!confirm('Remove this variation image?')) return;
    await axios.delete(`/api/admin/products/${props.productId}/variations/${v.id}/image`);
    v.image = null;
    showToast('Image removed.');
};

const deleteVariation = async (v) => {
    if (!confirm('Delete this variation?')) return;
    await axios.delete(`/api/admin/products/${props.productId}/variations/${v.id}`);
    variations.value = variations.value.filter(x => x.id !== v.id);
    showToast('Variation deleted.');
};

onMounted(loadData);
</script>

<style scoped>
@reference "../../../../css/app.css";
.th { @apply text-left text-xs font-bold uppercase tracking-wide px-4 py-3; color:#94a3b8; }
.td { @apply px-4 py-3; }
.form-input {
    @apply px-3 py-1.5 rounded-lg text-sm outline-none transition-all;
    border: 1.5px solid #e5e7eb; color: #1e293b; background: white;
}
.form-input:focus { border-color:#0D6EFD; }
.btn-primary {
    @apply inline-flex items-center px-5 py-2 rounded-xl text-white text-sm font-semibold transition-all duration-200;
    background:#0D6EFD;
}
.btn-primary:hover:not(:disabled) { background:#0a58ca; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
</style>
