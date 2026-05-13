<template>
  <nav :class="['sticky top-0 z-50 transition-all duration-300',
    scrolled ? 'bg-white/95 backdrop-blur-2xl shadow-lg shadow-black/[0.06]' : 'bg-white']"
    style="border-bottom: 1px solid rgba(0,0,0,0.06);">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="relative flex items-center h-[60px]">

        <!-- Logo -->
        <RouterLink to="/" class="group flex items-center gap-2.5 flex-shrink-0">
          <!-- Custom logo image -->
          <img v-if="settings.logo" :src="'/storage/' + settings.logo"
            class="h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300" />
          <!-- Fallback icon -->
          <div v-else class="relative w-9 h-9">
            <div class="absolute inset-0 rounded-xl opacity-30 blur-sm group-hover:opacity-50 transition-opacity duration-300"
              style="background: linear-gradient(135deg, #1D3FB8, #1D3FB8);"></div>
            <div class="relative w-9 h-9 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300"
              style="background: linear-gradient(135deg, #1D3FB8, #1D3FB8);">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 7h13L17 13M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
              </svg>
            </div>
          </div>
        </RouterLink>

        <!-- Search Bar — absolutely centered -->
        <div class="desktop-search" style="position:absolute;left:50%;transform:translateX(-50%);width:100%;max-width:480px;padding:0 8px;">
          <div class="relative w-full group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 transition-colors duration-200"
              :style="searchFocused ? 'color:#1D3FB8' : 'color:#A8A8A8'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products, brands, categories..."
              @focus="searchFocused = true"
              @blur="searchFocused = false"
              @keyup.enter="goSearch"
              class="w-full pl-11 pr-4 py-2.5 text-sm rounded-2xl outline-none transition-all duration-300"
              :style="searchFocused
                ? 'background:#fff; border: 1.5px solid #1D3FB8; box-shadow: 0 0 0 3px rgba(29,63,184,0.1);'
                : 'background:#F0EDE6; border: 1.5px solid transparent;'"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-0.5 ml-auto flex-shrink-0">

          <!-- Wishlist -->
          <RouterLink to="/favorites" class="nav-btn relative hidden sm:flex" title="Favorites">
            <svg class="w-[18px] h-[18px]" :fill="favoritesCount > 0 ? '#A4351A' : 'none'"
              :stroke="favoritesCount > 0 ? '#A4351A' : 'currentColor'" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
            </svg>
            <span v-if="favoritesCount > 0" class="fav-badge">{{ favoritesCount > 99 ? '99+' : favoritesCount }}</span>
          </RouterLink>

          <!-- Cart -->
          <RouterLink to="/cart" class="nav-btn relative" title="Cart">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span v-if="cartCount > 0" class="cart-badge">{{ cartCount > 99 ? '99+' : cartCount }}</span>
          </RouterLink>

          <!-- Account -->
          <div class="relative">
            <button @click="accountOpen = !accountOpen" class="nav-btn"
              :class="accountOpen ? 'nav-btn-active' : ''">
              <span v-if="authStore.user"
                class="w-[22px] h-[22px] rounded-lg flex items-center justify-center text-white text-[11px] font-black"
                style="background:linear-gradient(135deg,#1D3FB8,#1D3FB8);">
                {{ authStore.user.name.charAt(0).toUpperCase() }}
              </span>
              <svg v-else class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </button>
            <Transition name="dropdown">
              <div v-if="accountOpen"
                class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl shadow-black/10 py-2 z-50 overflow-hidden"
                style="border: 1px solid rgba(0,0,0,0.06);">

                <!-- Logged in state -->
                <template v-if="authStore.user">
                  <div class="px-4 py-3 mb-1" style="border-bottom: 1px solid #F0EDE6;">
                    <p class="text-[11px] text-gray-400 font-medium">Signed in as</p>
                    <p class="text-sm font-bold truncate" style="color:#1A1A1A;">{{ authStore.user.name }}</p>
                    <p class="text-[11px] truncate" style="color:#A8A8A8;">{{ authStore.user.email }}</p>
                  </div>
                  <RouterLink to="/account" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    My Account
                  </RouterLink>
                  <RouterLink to="/orders" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    My Orders
                  </RouterLink>
                  <RouterLink to="/favorites" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon" style="background:rgba(164,53,26,0.08);color:#A4351A;">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.682l1.318-1.364a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
                    </div>
                    My Favorites
                    <span v-if="favoritesCount > 0"
                      class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full text-white"
                      style="background:#A4351A;">{{ favoritesCount }}</span>
                  </RouterLink>
                  <div class="mx-4 my-1.5" style="height:1px; background:#F0EDE6;"></div>
                  <button @click="handleLogout" class="dropdown-item w-full text-left" style="color:#A4351A;">
                    <div class="dropdown-icon" style="background:rgba(164,53,26,0.08);color:#A4351A;">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                    </div>
                    Sign Out
                  </button>
                </template>

                <!-- Guest state -->
                <template v-else>
                  <div class="px-4 py-3 mb-1" style="border-bottom: 1px solid #F0EDE6;">
                    <p class="text-[11px] text-gray-400 font-medium">Welcome</p>
                    <p class="text-sm font-bold" style="color:#1A1A1A;">Sign in to your account</p>
                  </div>
                  <RouterLink to="/login" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg></div>
                    Sign In
                  </RouterLink>
                  <RouterLink to="/register" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
                    Create Account
                  </RouterLink>
                  <div class="mx-4 my-1.5" style="height:1px; background:#F0EDE6;"></div>
                  <RouterLink to="/orders" class="dropdown-item" @click="accountOpen=false">
                    <div class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    My Orders
                  </RouterLink>
                </template>
              </div>
            </Transition>
          </div>

          <!-- Mobile menu toggle -->
          <button @click="mobileOpen = !mobileOpen" class="nav-btn hamburger-btn ml-1">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h10M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Nav strip -->
      <div class="cat-strip items-center gap-0.5 pb-1.5" style="border-top: 1px solid #F0EDE6;">
        <!-- Static tabs -->
        <RouterLink to="/shop" class="cat-link">All Products</RouterLink>
        <RouterLink to="/shop?sort=latest" class="cat-link">New Arrivals</RouterLink>
        <RouterLink to="/shop?sort=price_asc" class="cat-link cat-link-hot">
          <span class="w-1.5 h-1.5 rounded-full mr-1 flex-shrink-0 animate-pulse" style="background:#B5532C;"></span>
          Sale
        </RouterLink>
        <!-- Real categories from API -->
        <RouterLink
          v-for="cat in navCategories"
          :key="cat.id"
          :to="`/category/${cat.slug}`"
          class="cat-link">
          <span v-if="cat.icon" class="mr-1">{{ cat.icon }}</span>
          {{ cat.name }}
        </RouterLink>
      </div>

      <!-- Mobile Panel -->
      <Transition name="slide-down">
        <div v-if="mobileOpen" class="py-4 space-y-3" style="border-top: 1px solid #F0EDE6;">
          <!-- Mobile search -->
          <div class="relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input v-model="searchQuery" type="text" placeholder="Search products..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm outline-none"
              style="background:#F0EDE6; border: 1.5px solid transparent;"
              @focus="(e) => e.target.style.borderColor='#1D3FB8'"
              @blur="(e) => e.target.style.borderColor='transparent'"
              @keyup.enter="goSearch(); mobileOpen = false"
            />
          </div>
          <!-- Mobile nav links -->
          <div class="grid grid-cols-2 gap-1.5">
            <RouterLink to="/shop" class="mobile-cat-link" @click="mobileOpen = false">All Products</RouterLink>
            <RouterLink to="/shop?sort=latest" class="mobile-cat-link" @click="mobileOpen = false">New Arrivals</RouterLink>
            <RouterLink to="/shop?sort=price_asc" class="mobile-cat-link mobile-cat-hot" @click="mobileOpen = false">🔥 Sale</RouterLink>
            <RouterLink
              v-for="cat in navCategories"
              :key="cat.id"
              :to="`/category/${cat.slug}`"
              class="mobile-cat-link"
              @click="mobileOpen = false">
              <span v-if="cat.icon" class="mr-1">{{ cat.icon }}</span>{{ cat.name }}
            </RouterLink>
          </div>
        </div>
      </Transition>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { authStore, logoutUser } from '../store/auth';
import { cartCount } from '../store/cart';
import { favoritesCount } from '../store/favorites';
import { settings } from '../store/settings';

const searchQuery   = ref('');
const searchFocused = ref(false);
const accountOpen   = ref(false);
const mobileOpen    = ref(false);
const scrolled      = ref(false);
const navCategories = ref([]);

const router = useRouter();

const goSearch = () => {
  const q = searchQuery.value.trim();
  if (!q) return;
  router.push({ path: '/shop', query: { search: q } });
};

const handleLogout = async () => {
  accountOpen.value = false;
  await logoutUser();
  router.push('/');
};

const onScroll = () => { scrolled.value = window.scrollY > 8; };

onMounted(async () => {
  window.addEventListener('scroll', onScroll);
  try {
    const res  = await fetch('/api/categories');
    const json = await res.json();
    navCategories.value = json.data ?? [];
  } catch {
    navCategories.value = [];
  }
});

onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<style scoped>
@reference "../../css/app.css";

.nav-btn {
  @apply p-2.5 rounded-xl flex items-center justify-center transition-all duration-200 active:scale-90;
  color: #6B6B6B;
}
.nav-btn:hover { background: #F0EDE6; color: #1A1A1A; }
.nav-btn-active { background: rgba(29,63,184,0.08); color: #1D3FB8; }

.cart-badge {
  @apply absolute -top-0.5 -right-0.5 w-[17px] h-[17px] rounded-full text-white flex items-center justify-center;
  font-size: 9px;
  font-weight: 800;
  background: #B5532C;
  animation: badge-pulse 2.5s ease-in-out infinite;
}

.fav-badge {
  @apply absolute -top-0.5 -right-0.5 w-[17px] h-[17px] rounded-full text-white flex items-center justify-center;
  font-size: 9px;
  font-weight: 800;
  background: #A4351A;
}
@keyframes badge-pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(181,83,44,0.45); }
  50%       { box-shadow: 0 0 0 5px rgba(181,83,44,0); }
}

.cat-link {
  @apply flex items-center px-3 py-1.5 text-[13px] font-medium rounded-lg transition-all duration-200 whitespace-nowrap relative;
  color: #4b5563;
}
.cat-link:hover { background: #F0EDE6; color: #1A1A1A; }
.cat-link::after {
  content: '';
  @apply absolute bottom-0 left-3 right-3 h-0.5 rounded-full scale-x-0 origin-left transition-transform duration-300;
  background: #1D3FB8;
}
.cat-link:hover::after { transform: scaleX(1); }
.cat-link-hot { color: #B5532C; font-weight: 700; }
.cat-link-hot:hover { background: rgba(181,83,44,0.06); color: #8E3F1A; }
.cat-link-hot::after { background: #B5532C; }

.dropdown-item {
  @apply flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-150;
  color: #3F3F3F;
}
.dropdown-item:hover { background: #FAFAF7; color: #1D3FB8; }
.dropdown-item:hover .dropdown-icon { background: rgba(29,63,184,0.1); color: #1D3FB8; }

.dropdown-icon {
  @apply w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-150 flex-shrink-0;
  background: #F0EDE6;
  color: #A8A8A8;
}

/* ── Responsive: mobile/tablet (<1024px) → hamburger; desktop (≥1024px) → cat strip ── */
.desktop-search { display: none; }
.hamburger-btn  { display: flex; }
.cat-strip      { display: none; }

@media (min-width: 1024px) {
  .desktop-search { display: flex; }
  .hamburger-btn  { display: none; }
  .cat-strip      { display: flex; }
}

.mobile-cat-link {
  @apply text-sm py-2.5 px-4 rounded-xl transition-colors duration-150 font-medium;
  color: #3F3F3F;
  background: #F0EDE6;
}
.mobile-cat-link:hover { background: rgba(29,63,184,0.08); color: #1D3FB8; }
.mobile-cat-hot { color: #B5532C; }
.mobile-cat-hot:hover { background: rgba(181,83,44,0.06); color: #8E3F1A; }

.dropdown-enter-active { transition: all 0.2s cubic-bezier(0.16,1,0.3,1); }
.dropdown-leave-active { transition: all 0.15s ease-in; }
.dropdown-enter-from   { opacity: 0; transform: translateY(-10px) scale(0.96); }
.dropdown-leave-to     { opacity: 0; transform: translateY(-5px) scale(0.98); }

.slide-down-enter-active { transition: all 0.3s cubic-bezier(0.16,1,0.3,1); }
.slide-down-leave-active { transition: all 0.2s ease-in; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-16px); }
.slide-down-leave-to     { opacity: 0; transform: translateY(-8px); }
</style>
