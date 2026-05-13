<template>
  <div class="min-h-[calc(100vh-120px)] py-10 px-4" style="background:#FAFAFA;">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="mb-8 flex items-center gap-3">
        <RouterLink to="/cart"
          class="w-9 h-9 rounded-xl flex items-center justify-center"
          style="background:#fff;border:1px solid #E5E7EB;">
          <svg class="w-4 h-4" style="color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </RouterLink>
        <div>
          <h1 class="text-2xl font-bold" style="color:#0F0F1A;">Checkout</h1>
          <p class="text-sm" style="color:#9ca3af;">Complete your order</p>
        </div>
      </div>

      <!-- Step indicator -->
      <div class="flex items-center gap-2 mb-8">
        <div v-for="(step, i) in steps" :key="step" class="flex items-center gap-2">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300"
              :style="i <= currentStep
                ? 'background:#0D6EFD;color:#fff;'
                : 'background:#F5F5F7;color:#9ca3af;'">
              <svg v-if="i < currentStep" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <span v-else>{{ i + 1 }}</span>
            </div>
            <span class="text-sm font-medium hidden sm:block"
              :style="i <= currentStep ? 'color:#0F0F1A;' : 'color:#9ca3af;'">
              {{ step }}
            </span>
          </div>
          <div v-if="i < steps.length - 1" class="w-8 sm:w-16 h-px"
            :style="i < currentStep ? 'background:#0D6EFD;' : 'background:#E5E7EB;'">
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ── Form ── -->
        <div class="lg:col-span-2">
          <form @submit.prevent="placeOrder" class="flex flex-col gap-5">

            <!-- Contact -->
            <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
              <h2 class="text-base font-bold mb-5" style="color:#0F0F1A;">
                <span class="inline-flex w-6 h-6 rounded-md items-center justify-center text-xs font-semibold text-white mr-2"
                  style="background:#0D6EFD;">1</span>
                Contact Information
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="field-label">Full Name *</label>
                  <input v-model="form.name" type="text" required
                    class="field-input" :class="{ 'field-error': errors.name }"
                    placeholder="John Smith"
                    @focus="clearError('name')" />
                  <p v-if="errors.name" class="field-err-msg">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="field-label">Email Address *</label>
                  <input v-model="form.email" type="email" required
                    class="field-input" :class="{ 'field-error': errors.email }"
                    placeholder="you@example.com"
                    @focus="clearError('email')" />
                  <p v-if="errors.email" class="field-err-msg">{{ errors.email }}</p>
                </div>
                <div class="sm:col-span-2">
                  <label class="field-label">Phone Number</label>
                  <input v-model="form.phone" type="tel"
                    class="field-input"
                    placeholder="+1 (555) 000-0000" />
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
              <h2 class="text-base font-bold mb-5" style="color:#0F0F1A;">
                <span class="inline-flex w-6 h-6 rounded-md items-center justify-center text-xs font-semibold text-white mr-2"
                  style="background:#0D6EFD;">2</span>
                Shipping Address
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                  <label class="field-label">Street Address *</label>
                  <input v-model="form.address" type="text" required
                    class="field-input" :class="{ 'field-error': errors.address }"
                    placeholder="123 Main Street, Apt 4B"
                    @focus="clearError('address')" />
                  <p v-if="errors.address" class="field-err-msg">{{ errors.address }}</p>
                </div>
                <div>
                  <label class="field-label">City *</label>
                  <input v-model="form.city" type="text" required
                    class="field-input" :class="{ 'field-error': errors.city }"
                    placeholder="New York"
                    @focus="clearError('city')" />
                  <p v-if="errors.city" class="field-err-msg">{{ errors.city }}</p>
                </div>
                <div>
                  <label class="field-label">State / Province</label>
                  <input v-model="form.state" type="text"
                    class="field-input" placeholder="NY" />
                </div>
                <div>
                  <label class="field-label">ZIP / Postal Code *</label>
                  <input v-model="form.zip" type="text" required
                    class="field-input" :class="{ 'field-error': errors.zip }"
                    placeholder="10001"
                    @focus="clearError('zip')" />
                  <p v-if="errors.zip" class="field-err-msg">{{ errors.zip }}</p>
                </div>
                <div>
                  <label class="field-label">Country *</label>
                  <select v-model="form.country" class="field-input field-select">
                    <option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Payment (placeholder) -->
            <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
              <h2 class="text-base font-bold mb-5" style="color:#0F0F1A;">
                <span class="inline-flex w-6 h-6 rounded-md items-center justify-center text-xs font-semibold text-white mr-2"
                  style="background:#0D6EFD;">3</span>
                Payment Method
              </h2>
              <!-- Cash on delivery -->
              <label class="flex items-center gap-4 p-4 rounded-xl cursor-pointer transition-all duration-150"
                style="border:1.5px solid #0D6EFD;background:rgba(13,110,253,0.04);">
                <input type="radio" checked class="accent-blue-600 w-4 h-4 flex-shrink-0" />
                <div>
                  <p class="text-sm font-semibold" style="color:#0F0F1A;">Cash on Delivery</p>
                  <p class="text-xs" style="color:#9ca3af;">Pay when your order arrives</p>
                </div>
              </label>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-2xl p-6" style="border:1px solid rgba(0,0,0,0.06);">
              <label class="field-label mb-1.5 block">Order Notes <span style="color:#9ca3af;">(optional)</span></label>
              <textarea v-model="form.notes" rows="3"
                class="field-input resize-none"
                placeholder="Special delivery instructions, gift messages, etc.">
              </textarea>
            </div>

            <!-- Error banner -->
            <div v-if="serverError"
              class="px-4 py-3 rounded-xl text-sm font-medium"
              style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
              {{ serverError }}
            </div>

            <!-- Mobile: place order button -->
            <button type="submit" :disabled="placing || cartStore.items.length === 0"
              class="lg:hidden w-full py-4 rounded-xl font-semibold text-white text-sm transition-all duration-200"
              :style="placing || cartStore.items.length === 0
                ? 'background:#93c5fd;cursor:not-allowed;'
                : 'background:#0D6EFD;'">
              {{ placing ? 'Placing Order…' : `Place Order · $${orderTotal.toFixed(2)}` }}
            </button>
          </form>
        </div>

        <!-- ── Order Summary ── -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl p-6 sticky top-24" style="border:1px solid rgba(0,0,0,0.06);">
            <h2 class="text-base font-bold mb-4" style="color:#0F0F1A;">
              Order Summary
              <span class="text-sm font-normal ml-1" style="color:#9ca3af;">({{ cartCount }} items)</span>
            </h2>

            <!-- Item list -->
            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto pr-1">
              <div v-for="item in cartStore.items" :key="item.key"
                class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0">
                  <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                    class="w-full h-full object-cover" />
                  <div v-else :class="['w-full h-full flex items-center justify-center text-xl', itemBg(item.productId)]">
                    {{ item.emoji }}
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold truncate" style="color:#0F0F1A;">{{ item.name }}</p>
                  <p v-if="item.variationLabel" class="text-[11px]" style="color:#9ca3af;">{{ item.variationLabel }}</p>
                  <p class="text-[11px]" style="color:#9ca3af;">Qty: {{ item.qty }}</p>
                </div>
                <span class="text-sm font-bold flex-shrink-0" style="color:#0F0F1A;">
                  ${{ (item.price * item.qty).toFixed(2) }}
                </span>
              </div>
            </div>

            <!-- Totals -->
            <div style="border-top:1px solid #F3F4F6;" class="pt-4 space-y-2.5 text-sm">
              <div class="flex justify-between">
                <span style="color:#6b7280;">Subtotal</span>
                <span class="font-semibold" style="color:#0F0F1A;">${{ cartSubtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span style="color:#6b7280;">Shipping</span>
                <span class="font-semibold" :style="shipping === 0 ? 'color:#22c55e;' : 'color:#0F0F1A;'">
                  {{ shipping === 0 ? 'Free' : '$' + shipping.toFixed(2) }}
                </span>
              </div>
              <div style="border-top:1px solid #F3F4F6;" class="pt-2.5 flex justify-between font-bold text-base">
                <span style="color:#0F0F1A;">Total</span>
                <span style="color:#0F0F1A;">${{ orderTotal.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Desktop: place order button -->
            <button @click="placeOrder" :disabled="placing || cartStore.items.length === 0"
              class="hidden lg:block w-full mt-5 py-4 rounded-xl font-semibold text-white text-sm transition-all duration-200"
              :style="placing || cartStore.items.length === 0
                ? 'background:#93c5fd;cursor:not-allowed;'
                : 'background:#0D6EFD;'">
              <span class="flex items-center justify-center gap-2">
                <svg v-if="placing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ placing ? 'Placing Order…' : `Place Order · $${orderTotal.toFixed(2)}` }}
              </span>
            </button>

            <!-- Trust -->
            <div class="flex items-center justify-center gap-5 mt-4 pt-4" style="border-top:1px solid #F3F4F6;">
              <span class="text-[11px] font-medium" style="color:#9ca3af;">Secure checkout</span>
              <span style="color:#E5E7EB;">·</span>
              <span class="text-[11px] font-medium" style="color:#9ca3af;">Free returns</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import axios from 'axios';
import { cartStore, cartCount, cartSubtotal, clearCart } from '../store/cart';
import { authStore } from '../store/auth';
import { bgColors } from '../composables/useProducts';

const router      = useRouter();
const placing     = ref(false);
const serverError = ref('');
const currentStep = ref(0);
const errors      = reactive({});

const steps = ['Contact', 'Shipping', 'Payment'];

const form = reactive({
  name:    '',
  email:   '',
  phone:   '',
  address: '',
  city:    '',
  state:   '',
  zip:     '',
  country: 'US',
  notes:   '',
});

const freeShippingThreshold = 50;
const shipping  = computed(() => cartSubtotal.value >= freeShippingThreshold ? 0 : 5.99);
const orderTotal = computed(() => cartSubtotal.value + shipping.value);

function itemBg(productId) {
  return bgColors[(productId ?? 0) % bgColors.length];
}

function clearError(field) {
  delete errors[field];
}

// Pre-fill from logged-in user
onMounted(() => {
  if (cartStore.items.length === 0) {
    router.replace('/cart');
    return;
  }
  if (authStore.user) {
    form.name  = authStore.user.name  ?? '';
    form.email = authStore.user.email ?? '';
  }
});

async function placeOrder() {
  if (placing.value || cartStore.items.length === 0) return;

  // Client-side step progress
  currentStep.value = 1;
  serverError.value = '';
  Object.keys(errors).forEach(k => delete errors[k]);

  placing.value = true;
  try {
    currentStep.value = 2;

    const payload = {
      name:    form.name,
      email:   form.email,
      phone:   form.phone   || null,
      address: form.address,
      city:    form.city,
      state:   form.state   || null,
      zip:     form.zip,
      country: form.country,
      notes:   form.notes   || null,
      items: cartStore.items.map(item => ({
        product_id:   item.productId,
        variation_id: item.variationId ?? null,
        name:         item.variationLabel
                        ? `${item.name} (${item.variationLabel})`
                        : item.name,
        qty:          item.qty,
      })),
    };

    const { data } = await axios.post('/api/orders', payload);
    if (data.guest_token) {
      const tokens = JSON.parse(sessionStorage.getItem('guestOrderTokens') || '{}');
      tokens[data.order.id] = data.guest_token;
      sessionStorage.setItem('guestOrderTokens', JSON.stringify(tokens));
    }
    clearCart();
    router.push({ path: '/order/confirmation', query: { id: data.order.id } });
  } catch (e) {
    currentStep.value = 0;
    const errs = e.response?.data?.errors;
    if (errs) {
      Object.assign(errors, Object.fromEntries(
        Object.entries(errs).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      ));
      serverError.value = 'Please fix the highlighted fields and try again.';
    } else {
      serverError.value = e.response?.data?.message ?? 'Something went wrong. Please try again.';
    }
  } finally {
    placing.value = false;
  }
}

const countries = [
  { code: 'US', name: 'United States' },
  { code: 'GB', name: 'United Kingdom' },
  { code: 'CA', name: 'Canada' },
  { code: 'AU', name: 'Australia' },
  { code: 'IN', name: 'India' },
  { code: 'DE', name: 'Germany' },
  { code: 'FR', name: 'France' },
  { code: 'JP', name: 'Japan' },
  { code: 'SG', name: 'Singapore' },
  { code: 'AE', name: 'United Arab Emirates' },
  { code: 'OTHER', name: 'Other' },
];
</script>

<style scoped>
@reference "../../css/app.css";

.field-label {
  @apply block text-sm font-semibold mb-1.5;
  color: #374151;
}
.field-input {
  @apply w-full px-4 py-2.5 rounded-xl text-sm outline-none transition-all duration-200;
  border: 1.5px solid #E5E7EB;
  color: #0F0F1A;
  background: #fff;
}
.field-input::placeholder { color: #9ca3af; }
.field-input:focus {
  border-color: #0D6EFD;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
}
.field-error {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important;
}
.field-err-msg {
  @apply text-[11px] mt-1 font-medium;
  color: #ef4444;
}
.field-select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 2.5rem;
  cursor: pointer;
}
</style>
