<template>
  <div class="space-y-6">
    <router-link to="/admin/products" class="inline-flex items-center gap-1 text-sm font-medium"
      style="color:#64748b;">
      ← Back to Products
    </router-link>

    <!-- Main form card -->
    <div class="max-w-2xl bg-white rounded-2xl p-8" style="border:1px solid #e2e8f0;">
      <h2 class="text-lg font-bold mb-6" style="color:#1e293b;">
        {{ isEdit ? 'Edit Product' : 'New Product' }}
      </h2>

      <form @submit.prevent="submit" class="space-y-5">
        <div v-if="error" class="px-4 py-3 rounded-xl text-sm font-medium"
          style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
          {{ error }}
        </div>

        <!-- Product type selector -->
        <div>
          <label class="form-label">Product Type</label>
          <div class="flex gap-3">
            <label v-for="t in types" :key="t.value"
              class="flex-1 flex items-start gap-3 p-4 rounded-xl cursor-pointer transition-all"
              :style="form.type === t.value
                ? 'border:2px solid #0D6EFD;background:rgba(13,110,253,0.04);'
                : 'border:2px solid #e5e7eb;background:white;'">
              <input type="radio" v-model="form.type" :value="t.value" class="mt-0.5 flex-shrink-0"
                style="accent-color:#0D6EFD;" />
              <div>
                <p class="font-semibold text-sm" style="color:#1e293b;">{{ t.label }}</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ t.desc }}</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Common fields -->
        <div>
          <label class="form-label">Product Name *</label>
          <input v-model="form.name" required class="form-input" placeholder="e.g. Premium Wireless Headphones" />
        </div>

        <div v-if="form.type !== 'grouped'">
          <label class="form-label">Category</label>
          <select v-model="form.category_id" class="form-input">
            <option value="">— None —</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.icon }} {{ c.name }}</option>
          </select>
        </div>

        <div>
          <label class="form-label">Description</label>
          <textarea v-model="form.description" class="form-input" rows="3" placeholder="Product description…"></textarea>
        </div>

        <!-- Main Product Image -->
        <div>
          <label class="form-label">Main Image</label>
          <div v-if="imagePreview || form.image_url" class="mb-3 flex items-start gap-4">
            <img :src="imagePreview || form.image_url" alt="Product image"
              class="w-28 h-28 object-cover rounded-xl" style="border:1.5px solid #e5e7eb;" />
            <button type="button" @click="removeImage"
              class="text-xs font-semibold mt-1 px-3 py-1.5 rounded-lg transition-colors"
              style="background:#fee2e2;color:#991b1b;">
              Remove
            </button>
          </div>
          <label class="img-upload-label">
            <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
              style="position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;pointer-events:none;"
              @change="onImageChange" />
            <span>{{ imagePreview || form.image_url ? 'Replace main image' : 'Choose main image' }}</span>
            <span class="text-xs" style="color:#94a3b8;">JPEG, PNG, WebP · Max 2 MB</span>
          </label>
        </div>

        <!-- Gallery Images -->
        <div>
          <label class="form-label">Gallery Images</label>

          <!-- Existing gallery -->
          <div v-if="galleryImages.length" class="grid grid-cols-4 gap-3 mb-3">
            <div v-for="img in galleryImages" :key="img.id" class="relative group">
              <img :src="`/storage/${img.path}`" alt="Gallery"
                class="w-full aspect-square object-cover rounded-xl" style="border:1.5px solid #e5e7eb;" />
              <button type="button" @click="deleteGalleryImage(img)"
                class="absolute top-1 right-1 w-6 h-6 rounded-full flex items-center justify-center
                       text-white text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity"
                style="background:#ef4444;">
                ×
              </button>
            </div>
          </div>

          <!-- Upload button (edit mode only) -->
          <label v-if="isEdit" class="img-upload-label" :class="{ 'opacity-60 cursor-not-allowed': galleryUploading }">
            <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" multiple
              style="position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;pointer-events:none;"
              :disabled="galleryUploading" @change="uploadGalleryImage" />
            <span>{{ galleryUploading ? `Uploading…` : '+ Add gallery images' }}</span>
            <span class="text-xs" style="color:#94a3b8;">JPEG, PNG, WebP · Max 4 MB each · Multiple allowed</span>
          </label>
          <p v-else class="text-xs px-4 py-3 rounded-xl" style="background:#f8fafc;color:#94a3b8;border:1.5px dashed #cbd5e1;">
            Save the product first to add gallery images.
          </p>
        </div>

        <!-- Simple-only fields -->
        <template v-if="form.type === 'simple'">
          <div class="grid grid-cols-2 gap-5">
            <div>
              <label class="form-label">Price *</label>
              <input v-model="form.price" type="number" step="0.01" min="0" required class="form-input" placeholder="0.00" />
            </div>
            <div>
              <label class="form-label">Original Price</label>
              <input v-model="form.original_price" type="number" step="0.01" min="0" class="form-input" placeholder="0.00" />
            </div>
            <div>
              <label class="form-label">Stock *</label>
              <input v-model="form.stock" type="number" min="0" required class="form-input" placeholder="0" />
            </div>
            <div>
              <label class="form-label">Badge</label>
              <select v-model="form.badge" class="form-input">
                <option value="">— None —</option>
                <option>Hot</option><option>New</option><option>Sale</option><option>Trending</option>
              </select>
            </div>
            <div>
              <label class="form-label">Emoji</label>
              <input v-model="form.emoji" class="form-input" placeholder="e.g. 📱" maxlength="10" />
            </div>
          </div>
        </template>

        <!-- Variable-only fields (no price/stock — those are per variation) -->
        <template v-else-if="form.type === 'variable'">
          <div class="grid grid-cols-2 gap-5">
            <div>
              <label class="form-label">Badge</label>
              <select v-model="form.badge" class="form-input">
                <option value="">— None —</option>
                <option>Hot</option><option>New</option><option>Sale</option><option>Trending</option>
              </select>
            </div>
            <div>
              <label class="form-label">Emoji</label>
              <input v-model="form.emoji" class="form-input" placeholder="e.g. 👕" maxlength="10" />
            </div>
          </div>
          <p class="text-xs p-3 rounded-xl" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;">
            💡 Price and stock are managed per variation below, not at the product level.
          </p>
        </template>

        <!-- Grouped has no price/stock/badge/emoji -->

        <!-- Active toggle (all types) -->
        <div class="flex items-center gap-3 pt-1">
          <input v-model="form.is_active" type="checkbox" id="is_active"
            class="w-4 h-4 rounded" style="accent-color:#0D6EFD;" />
          <label for="is_active" class="text-sm font-medium" style="color:#374151;">Active (visible in store)</label>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="saving" class="btn-primary">
            {{ saving ? 'Saving…' : (isEdit ? 'Update Product' : 'Create Product') }}
          </button>
          <router-link to="/admin/products" class="btn-secondary">Cancel</router-link>
        </div>
      </form>
    </div>

    <!-- Variations panel (variable products, edit mode only) -->
    <template v-if="isEdit && form.type === 'variable'">
      <div class="flex items-center gap-3 mt-2">
        <div class="h-px flex-1" style="background:#e2e8f0;"></div>
        <span class="text-xs font-bold uppercase tracking-widest" style="color:#94a3b8;">Variations</span>
        <div class="h-px flex-1" style="background:#e2e8f0;"></div>
      </div>
      <VariationsPanel :product-id="Number(route.params.id)" />
    </template>

    <!-- Group panel (grouped products, edit mode only) -->
    <template v-if="isEdit && form.type === 'grouped'">
      <div class="flex items-center gap-3 mt-2">
        <div class="h-px flex-1" style="background:#e2e8f0;"></div>
        <span class="text-xs font-bold uppercase tracking-widest" style="color:#94a3b8;">Grouped Products</span>
        <div class="h-px flex-1" style="background:#e2e8f0;"></div>
      </div>
      <GroupPanel :product-id="Number(route.params.id)" />
    </template>

    <!-- Hint for new variable/grouped products -->
    <div v-if="!isEdit && form.type !== 'simple'"
      class="max-w-2xl text-sm p-4 rounded-xl text-center" style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd;">
      Save the product first, then you'll be able to
      {{ form.type === 'variable' ? 'add variations' : 'add grouped products' }}.
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { showToast } from '../../store';
import VariationsPanel from './VariationsPanel.vue';
import GroupPanel from './GroupPanel.vue';

const route  = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);

const types = [
    { value: 'simple',   label: 'Simple',   desc: 'Single price & stock'              },
    { value: 'variable', label: 'Variable', desc: 'Multiple variants (size, color…)'  },
    { value: 'grouped',  label: 'Grouped',  desc: 'Bundle of related products'        },
];

const form = reactive({
    type: 'simple', name: '', category_id: '', description: '',
    price: '', original_price: '', stock: '',
    emoji: '', badge: '', is_active: true,
    image_url: '',
});

const categories      = ref([]);
const saving          = ref(false);
const error           = ref('');
const imageInput      = ref(null);
const imageFile       = ref(null);
const imagePreview    = ref('');
const removeImageFlag = ref(false);
const galleryImages   = ref([]);
const galleryUploading = ref(false);

onMounted(async () => {
    const { data: catData } = await axios.get('/api/admin/categories');
    categories.value = catData.data;

    if (isEdit.value) {
        const { data } = await axios.get(`/api/admin/products/${route.params.id}`);
        const p = data.data;
        Object.assign(form, {
            type:           p.type ?? 'simple',
            name:           p.name,
            category_id:    p.category_id ?? '',
            description:    p.description ?? '',
            price:          p.price ?? '',
            original_price: p.original_price ?? '',
            stock:          p.stock ?? '',
            emoji:          p.emoji ?? '',
            badge:          p.badge ?? '',
            is_active:      p.is_active,
            image_url:      p.image ? `/storage/${p.image}` : '',
        });
        galleryImages.value = p.images ?? [];
    }
});

const onImageChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    imageFile.value = file;
    removeImageFlag.value = false;
    imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
    imageFile.value = null;
    imagePreview.value = '';
    removeImageFlag.value = true;
    form.image_url = '';
    if (imageInput.value) imageInput.value.value = '';
};

const uploadGalleryImage = async (e) => {
    const files = [...e.target.files];
    if (!files.length) return;
    e.target.value = '';
    galleryUploading.value = true;
    try {
        const results = await Promise.all(
            files.map(file => {
                const fd = new FormData();
                fd.append('image', file);
                return axios.post(`/api/admin/products/${route.params.id}/images`, fd);
            })
        );
        results.forEach(({ data }) => galleryImages.value.push(data.data));
        showToast(`${files.length} image${files.length > 1 ? 's' : ''} uploaded.`);
    } catch {
        showToast('One or more uploads failed.', 'error');
    } finally {
        galleryUploading.value = false;
    }
};

const deleteGalleryImage = async (img) => {
    if (!confirm('Remove this gallery image?')) return;
    await axios.delete(`/api/admin/products/${route.params.id}/images/${img.id}`);
    galleryImages.value = galleryImages.value.filter(i => i.id !== img.id);
    showToast('Gallery image removed.');
};

const buildFormData = () => {
    const fd = new FormData();
    fd.append('type', form.type);
    fd.append('name', form.name);
    fd.append('description', form.description || '');
    fd.append('is_active', form.is_active ? '1' : '0');
    if (form.category_id)       fd.append('category_id', form.category_id);
    if (form.price !== '')      fd.append('price', form.price);
    if (form.original_price !== '') fd.append('original_price', form.original_price);
    if (form.stock !== '')      fd.append('stock', form.stock);
    if (form.emoji)             fd.append('emoji', form.emoji);
    if (form.badge)             fd.append('badge', form.badge);
    if (imageFile.value) {
        fd.append('image', imageFile.value);
    } else if (removeImageFlag.value) {
        fd.append('remove_image', '1');
    }
    return fd;
};

const submit = async () => {
    saving.value = true;
    error.value  = '';
    try {
        const fd = buildFormData();
        if (isEdit.value) {
            fd.append('_method', 'PUT');
            await axios.post(`/api/admin/products/${route.params.id}`, fd);
            showToast('Product updated.');
        } else {
            const { data } = await axios.post('/api/admin/products', fd);
            showToast('Product created.');
            router.push(`/admin/products/${data.data.id}/edit`);
            return;
        }
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Something went wrong.';
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
@reference "../../../../css/app.css";
.form-label { @apply block text-sm font-semibold mb-1.5; color:#374151; }
.form-input {
    @apply w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all;
    border: 1.5px solid #e5e7eb; color: #1e293b; background: white;
}
.form-input:focus { border-color:#0D6EFD; box-shadow:0 0 0 3px rgba(13,110,253,0.1); }
.img-upload-label {
    @apply flex flex-col items-center justify-center gap-1 w-full py-5 rounded-xl cursor-pointer text-sm font-semibold transition-all;
    border: 1.5px dashed #cbd5e1; color: #64748b; background: #f8fafc;
}
.img-upload-label:hover { border-color: #0D6EFD; color: #0D6EFD; background: rgba(13,110,253,0.03); }
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
