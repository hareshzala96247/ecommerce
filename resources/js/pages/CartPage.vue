<template>
  <div class="min-h-[calc(100vh-120px)] py-10 px-4" style="background:#FAFAFA;">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="mb-8 flex items-center gap-3">
        <RouterLink to="/shop"
          class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors duration-150"
          style="background:#fff;border:1px solid #E5E7EB;">
          <svg class="w-4 h-4" style="color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </RouterLink>
        <div>
          <h1 class="text-2xl font-black" style="color:#0F0F1A;">Shopping Cart</h1>
          <p class="text-sm" style="color:#9ca3af;">
            {{ cartCount }} {{ cartCount === 1 ? 'item' : 'items' }}
          </p>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-24 h-24 rounded-3xl flex items-center justify-center mb-6"
          style="background:#F5F5F7;">
          <svg class="w-12 h-12" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:#0F0F1A;">Your cart is empty</h2>
        <p class="text-sm mb-8 max-w-xs" style="color:#6b7280;">
          Looks like you haven't added anything yet. Browse our products and find something you love!
        </p>
        <RouterLink to="/shop"
          class="px-8 py-3 rounded-xl font-bold text-sm text-white transition-all duration-200"
          style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);box-shadow:0 4px 16px rgba(13,110,253,0.35);">
          Browse Products
        </RouterLink>
      </div>

      <!-- Cart content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Items list -->
        <div class="lg:col-span-2 flex flex-col gap-3">

          <TransitionGroup name="cart-item" tag="div" class="flex flex-col gap-3">
            <div v-for="item in cartStore.items" :key="item.key"
              class="bg-white rounded-2xl p-4 flex items-start gap-4"
              style="border:1px solid rgba(0,0,0,0.06);">

              <!-- Product thumbnail -->
              <RouterLink :to="`/product/${item.slug}`"
                class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 transition-transform duration-200 hover:scale-105">
                <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                  class="w-full h-full object-cover" />
                <div v-else :class="['w-full h-full flex items-center justify-center text-3xl', itemBg(item.productId)]">
                  {{ item.emoji }}
                </div>
              </RouterLink>

              <!-- Info -->
              <div class="flex-1 min-w-0">
                <RouterLink :to="`/product/${item.slug}`"
                  class="text-sm font-bold leading-tight hover:text-blue-600 transition-colors line-clamp-2"
                  style="color:#0F0F1A;">
                  {{ item.name }}
                </RouterLink>
                <p v-if="item.variationLabel" class="text-[12px] mt-0.5" style="color:#9ca3af;">
                  {{ item.variationLabel }}
                </p>
                <p class="text-base font-black mt-1" style="color:#0D6EFD;">
                  ${{ (item.price * item.qty).toFixed(2) }}
                </p>
                <p class="text-[11px]" style="color:#9ca3af;">
                  ${{ parseFloat(item.price).toFixed(2) }} each
                </p>
              </div>

              <!-- Qty + Remove -->
              <div class="flex flex-col items-end gap-3 flex-shrink-0">
                <button @click="removeFromCart(item.key)"
                  class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors duration-150"
                  style="color:#9ca3af;"
                  @mouseenter="(e) => { e.currentTarget.style.background='#fee2e2'; e.currentTarget.style.color='#ef4444'; }"
                  @mouseleave="(e) => { e.currentTarget.style.background=''; e.currentTarget.style.color='#9ca3af'; }">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>

                <div class="flex items-center rounded-xl overflow-hidden" style="border:1.5px solid #E5E7EB;">
                  <button @click="updateQty(item.key, item.qty - 1)"
                    class="w-8 h-8 flex items-center justify-center transition-colors duration-150 text-gray-500 hover:bg-gray-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                  </button>
                  <span class="w-9 text-center text-sm font-bold" style="color:#0F0F1A;border-left:1px solid #E5E7EB;border-right:1px solid #E5E7EB;">
                    {{ item.qty }}
                  </span>
                  <button @click="updateQty(item.key, item.qty + 1)"
                    class="w-8 h-8 flex items-center justify-center transition-colors duration-150 text-gray-500 hover:bg-gray-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </TransitionGroup>

          <!-- Clear cart -->
          <div class="flex justify-end mt-1">
            <button @click="confirmClear = true"
              class="text-sm font-medium transition-colors duration-150"
              style="color:#9ca3af;"
              @mouseenter="(e) => e.currentTarget.style.color='#ef4444'"
              @mouseleave="(e) => e.currentTarget.style.color='#9ca3af'">
              Clear cart
            </button>
          </div>
        </div>

        <!-- Order summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl p-6 sticky top-24" style="border:1px solid rgba(0,0,0,0.06);">
            <h2 class="text-base font-bold mb-5" style="color:#0F0F1A;">Order Summary</h2>

            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span style="color:#6b7280;">Subtotal ({{ cartCount }} items)</span>
                <span class="font-semibold" style="color:#0F0F1A;">${{ cartSubtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span style="color:#6b7280;">Shipping</span>
                <span class="font-semibold" :style="shipping === 0 ? 'color:#22c55e;' : 'color:#0F0F1A;'">
                  {{ shipping === 0 ? 'Free' : '$' + shipping.toFixed(2) }}
                </span>
              </div>
              <div v-if="shipping > 0" class="text-[12px] px-3 py-2 rounded-xl" style="background:#eff6ff;color:#1d4ed8;">
                Add ${{ (freeShippingThreshold - cartSubtotal).toFixed(2) }} more for free shipping
              </div>
              <!-- Divider -->
              <div style="height:1px;background:#F3F4F6;"></div>
              <div class="flex justify-between text-base font-black">
                <span style="color:#0F0F1A;">Total</span>
                <span style="color:#0D6EFD;">${{ orderTotal.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Coupon -->
            <div class="mt-5">
              <div class="flex gap-2">
                <input v-model="coupon" type="text" placeholder="Coupon code"
                  class="flex-1 px-3 py-2.5 rounded-xl text-sm outline-none transition-all"
                  :style="couponFocused
                    ? 'border:1.5px solid #0D6EFD;box-shadow:0 0 0 3px rgba(13,110,253,0.1);'
                    : 'border:1.5px solid #E5E7EB;'"
                  @focus="couponFocused=true" @blur="couponFocused=false"
                />
                <button @click="applyCoupon"
                  class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-150"
                  style="background:#F5F5F7;color:#374151;"
                  @mouseenter="(e) => { e.currentTarget.style.background='#0D6EFD'; e.currentTarget.style.color='#fff'; }"
                  @mouseleave="(e) => { e.currentTarget.style.background='#F5F5F7'; e.currentTarget.style.color='#374151'; }">
                  Apply
                </button>
              </div>
              <p v-if="couponMsg" class="text-[12px] mt-1.5 font-medium" :style="{ color: couponError ? '#ef4444' : '#22c55e' }">
                {{ couponMsg }}
              </p>
            </div>

            <!-- Checkout -->
            <RouterLink to="/checkout"
              class="block w-full mt-5 py-3.5 rounded-xl font-bold text-sm text-white text-center transition-all duration-200"
              style="background:linear-gradient(135deg,#0D6EFD,#7C3AED);box-shadow:0 4px 16px rgba(13,110,253,0.35);">
              Proceed to Checkout
            </RouterLink>

            <!-- Trust row -->
            <div class="flex items-center justify-center gap-4 mt-5">
              <div v-for="t in trust" :key="t.label" class="flex items-center gap-1.5">
                <span class="text-base">{{ t.icon }}</span>
                <span class="text-[11px] font-medium" style="color:#9ca3af;">{{ t.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Clear confirm dialog -->
    <Transition name="fade">
      <div v-if="confirmClear"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="background:rgba(0,0,0,0.45);"
        @click.self="confirmClear=false">
        <div class="bg-white rounded-2xl p-6 w-full max-w-xs shadow-2xl">
          <h3 class="text-base font-bold mb-1" style="color:#0F0F1A;">Clear cart?</h3>
          <p class="text-sm mb-5" style="color:#6b7280;">This will remove all items from your cart.</p>
          <div class="flex gap-3">
            <button @click="confirmClear=false"
              class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-colors"
              style="border:1.5px solid #E5E7EB;color:#374151;">
              Cancel
            </button>
            <button @click="doClear"
              class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
              style="background:#ef4444;">
              Clear All
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { cartStore, cartCount, cartSubtotal, removeFromCart, updateQty, clearCart } from '../store/cart';
import { bgColors } from '../composables/useProducts';

const confirmClear  = ref(false);
const coupon        = ref('');
const couponFocused = ref(false);
const couponMsg     = ref('');
const couponError   = ref(false);

const freeShippingThreshold = 50;

const shipping = computed(() =>
    cartSubtotal.value >= freeShippingThreshold ? 0 : 5.99
);

const orderTotal = computed(() => cartSubtotal.value + shipping.value);

function itemBg(productId) {
    return bgColors[(productId ?? 0) % bgColors.length];
}

function doClear() {
    clearCart();
    confirmClear.value = false;
}

function applyCoupon() {
    if (!coupon.value.trim()) return;
    couponMsg.value  = 'Invalid or expired coupon code.';
    couponError.value = true;
}

const trust = [
    { icon: '🔒', label: 'Secure' },
    { icon: '↩️', label: 'Free Returns' },
    { icon: '🚚', label: 'Fast Delivery' },
];
</script>

<style scoped>
.cart-item-enter-active { transition: all 0.3s ease; }
.cart-item-leave-active { transition: all 0.25s ease; }
.cart-item-enter-from   { opacity: 0; transform: translateY(-12px); }
.cart-item-leave-to     { opacity: 0; transform: translateX(20px); }
.cart-item-move         { transition: transform 0.3s ease; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
