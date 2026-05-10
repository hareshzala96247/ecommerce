<template>
  <div class="max-w-3xl space-y-6">

    <!-- Site Info -->
    <div class="card">
      <h2 class="section-title">
        <span class="section-icon">🏪</span> Site Information
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="field">
          <label>Site Name</label>
          <input v-model="form.site_name" type="text" placeholder="ShopVue" />
        </div>
        <div class="field">
          <label>Tagline</label>
          <input v-model="form.site_tagline" type="text" placeholder="Your one-stop shop" />
        </div>
        <!-- Logo -->
        <div class="field">
          <label>Logo</label>
          <div class="flex items-center gap-4">
            <div v-if="logoPreview || existingLogo" class="w-20 h-14 rounded-xl border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50">
              <img :src="logoPreview || existingLogo" class="max-w-full max-h-full object-contain" />
            </div>
            <label class="upload-btn cursor-pointer">
              {{ logoPreview || existingLogo ? 'Change' : 'Upload' }} Logo
              <input type="file" accept="image/*" class="sr-only" @change="onLogoChange" />
            </label>
            <button v-if="logoPreview || existingLogo" @click="removeLogo" class="text-red-500 text-sm hover:underline">Remove</button>
          </div>
        </div>
        <!-- Favicon -->
        <div class="field">
          <label>Favicon</label>
          <div class="flex items-center gap-4">
            <div v-if="faviconPreview || existingFavicon" class="w-10 h-10 rounded-lg border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50">
              <img :src="faviconPreview || existingFavicon" class="max-w-full max-h-full object-contain" />
            </div>
            <label class="upload-btn cursor-pointer">
              {{ faviconPreview || existingFavicon ? 'Change' : 'Upload' }} Favicon
              <input type="file" accept=".ico,image/png,image/svg+xml" class="sr-only" @change="onFaviconChange" />
            </label>
            <button v-if="faviconPreview || existingFavicon" @click="removeFavicon" class="text-red-500 text-sm hover:underline">Remove</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Info -->
    <div class="card">
      <h2 class="section-title">
        <span class="section-icon">📞</span> Contact Information
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="field">
          <label>Email</label>
          <input v-model="form.contact_email" type="email" placeholder="hello@example.com" />
        </div>
        <div class="field">
          <label>Phone</label>
          <input v-model="form.contact_phone" type="text" placeholder="+1 (555) 000-0000" />
        </div>
        <div class="field sm:col-span-2">
          <label>Address</label>
          <textarea v-model="form.contact_address" rows="2" placeholder="123 Main St, City, Country"></textarea>
        </div>
      </div>
    </div>

    <!-- Social Links -->
    <div class="card">
      <h2 class="section-title">
        <span class="section-icon">🔗</span> Social Links
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="field">
          <label>Facebook</label>
          <div class="input-icon-wrap">
            <span class="input-icon">f</span>
            <input v-model="form.social_facebook" type="url" placeholder="https://facebook.com/yourpage" />
          </div>
        </div>
        <div class="field">
          <label>Instagram</label>
          <div class="input-icon-wrap">
            <span class="input-icon">📷</span>
            <input v-model="form.social_instagram" type="url" placeholder="https://instagram.com/yourhandle" />
          </div>
        </div>
        <div class="field">
          <label>Twitter / X</label>
          <div class="input-icon-wrap">
            <span class="input-icon">𝕏</span>
            <input v-model="form.social_twitter" type="url" placeholder="https://twitter.com/yourhandle" />
          </div>
        </div>
        <div class="field">
          <label>YouTube</label>
          <div class="input-icon-wrap">
            <span class="input-icon">▶</span>
            <input v-model="form.social_youtube" type="url" placeholder="https://youtube.com/yourchannel" />
          </div>
        </div>
      </div>
    </div>

    <!-- SEO -->
    <div class="card">
      <h2 class="section-title">
        <span class="section-icon">🔍</span> SEO
      </h2>
      <div class="space-y-5">
        <div class="field">
          <label>Meta Title</label>
          <input v-model="form.meta_title" type="text" placeholder="ShopVue – Best Online Store" maxlength="150" />
          <span class="hint">{{ (form.meta_title || '').length }}/150</span>
        </div>
        <div class="field">
          <label>Meta Description</label>
          <textarea v-model="form.meta_description" rows="3" placeholder="Describe your store in 1-2 sentences…" maxlength="300"></textarea>
          <span class="hint">{{ (form.meta_description || '').length }}/300</span>
        </div>
      </div>
    </div>

    <!-- Save -->
    <div class="flex justify-end">
      <button @click="save" :disabled="saving" class="save-btn">
        <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        {{ saving ? 'Saving…' : 'Save Settings' }}
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { showToast } from '../../store';

const saving = ref(false);

const form = reactive({
  site_name:        '',
  site_tagline:     '',
  contact_email:    '',
  contact_phone:    '',
  contact_address:  '',
  social_facebook:  '',
  social_instagram: '',
  social_twitter:   '',
  social_youtube:   '',
  meta_title:       '',
  meta_description: '',
});

const existingLogo    = ref(null);
const existingFavicon = ref(null);
const logoPreview     = ref(null);
const faviconPreview  = ref(null);
const logoFile        = ref(null);
const faviconFile     = ref(null);
const removeLogoFlag    = ref(false);
const removeFaviconFlag = ref(false);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/admin/settings');
    Object.keys(form).forEach(k => { if (data[k] !== undefined) form[k] = data[k] ?? ''; });
    if (data.logo)    existingLogo.value    = '/storage/' + data.logo;
    if (data.favicon) existingFavicon.value = '/storage/' + data.favicon;
  } catch {
    showToast('Failed to load settings.', 'error');
  }
});

function onLogoChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  logoFile.value    = file;
  logoPreview.value = URL.createObjectURL(file);
  removeLogoFlag.value = false;
}

function onFaviconChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  faviconFile.value    = file;
  faviconPreview.value = URL.createObjectURL(file);
  removeFaviconFlag.value = false;
}

function removeLogo() {
  logoFile.value    = null;
  logoPreview.value = null;
  existingLogo.value = null;
  removeLogoFlag.value = true;
}

function removeFavicon() {
  faviconFile.value    = null;
  faviconPreview.value = null;
  existingFavicon.value = null;
  removeFaviconFlag.value = true;
}

async function save() {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.entries(form).forEach(([k, v]) => fd.append(k, v ?? ''));
    if (logoFile.value)    fd.append('logo',    logoFile.value);
    if (faviconFile.value) fd.append('favicon', faviconFile.value);
    if (removeLogoFlag.value)    fd.append('remove_logo',    '1');
    if (removeFaviconFlag.value) fd.append('remove_favicon', '1');

    const { data } = await axios.post('/api/admin/settings', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (data.settings?.logo)    existingLogo.value    = '/storage/' + data.settings.logo;
    if (data.settings?.favicon) existingFavicon.value = '/storage/' + data.settings.favicon;
    logoFile.value = null; faviconFile.value = null;

    showToast('Settings saved successfully!');
  } catch (err) {
    const msg = err.response?.data?.message ?? 'Failed to save settings.';
    showToast(msg, 'error');
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
@reference "../../../../css/app.css";

.card {
  @apply bg-white rounded-2xl p-6;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

.section-title {
  @apply flex items-center gap-2 text-base font-bold mb-5;
  color: #1e293b;
}
.section-icon {
  @apply text-lg;
}

.field {
  @apply flex flex-col gap-1.5;
}
.field label {
  @apply text-xs font-semibold uppercase tracking-wide;
  color: #64748b;
}
.field input,
.field textarea {
  @apply w-full px-3.5 py-2.5 rounded-xl text-sm outline-none transition-all duration-200;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  color: #1e293b;
  resize: none;
}
.field input:focus,
.field textarea:focus {
  border-color: #0D6EFD;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.08);
}
.hint {
  @apply text-xs;
  color: #94a3b8;
}

.input-icon-wrap {
  @apply relative;
}
.input-icon {
  @apply absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold;
  color: #94a3b8;
}
.input-icon-wrap input {
  @apply pl-8;
}

.upload-btn {
  @apply inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200;
  background: #f1f5f9;
  border: 1.5px solid #e2e8f0;
  color: #475569;
}
.upload-btn:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.save-btn {
  @apply inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-sm text-white transition-all duration-200 disabled:opacity-60;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 4px 14px rgba(13,110,253,0.35);
}
.save-btn:not(:disabled):hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(13,110,253,0.4);
}
</style>
