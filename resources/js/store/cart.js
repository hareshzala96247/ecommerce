import { reactive, computed } from 'vue';

const saved = JSON.parse(localStorage.getItem('shopCart') || '[]');

export const cartStore = reactive({
    items: saved,
});

function persist() {
    localStorage.setItem('shopCart', JSON.stringify(cartStore.items));
}

export const cartCount = computed(() =>
    cartStore.items.reduce((sum, item) => sum + item.qty, 0)
);

export const cartSubtotal = computed(() =>
    cartStore.items.reduce((sum, item) => sum + item.price * item.qty, 0)
);

export function addToCart({ productId, name, slug, emoji, price, qty = 1, variationId = null, variationLabel = '' }) {
    const key = variationId ? `${productId}_${variationId}` : `${productId}`;
    const existing = cartStore.items.find(i => i.key === key);
    if (existing) {
        existing.qty += qty;
    } else {
        cartStore.items.push({ key, productId, variationId, name, slug, emoji, price: parseFloat(price), qty, variationLabel });
    }
    persist();
}

export function removeFromCart(key) {
    const idx = cartStore.items.findIndex(i => i.key === key);
    if (idx !== -1) cartStore.items.splice(idx, 1);
    persist();
}

export function updateQty(key, qty) {
    const item = cartStore.items.find(i => i.key === key);
    if (!item) return;
    if (qty < 1) { removeFromCart(key); return; }
    item.qty = qty;
    persist();
}

export function clearCart() {
    cartStore.items.splice(0);
    persist();
}
