<template>
  <section class="hero relative overflow-hidden flex items-center min-h-[640px]">

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24 z-10">
      <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

        <!-- LEFT: Copy -->
        <div class="flex-1 text-center lg:text-left max-w-xl lg:max-w-none">

          <!-- Badge pill -->
          <div class="badge-pill">
            <span class="pulse-dot"></span>
            Summer Collection 2025 &nbsp;·&nbsp; Now Live
          </div>

          <!-- Headline -->
          <h1 class="hero-heading fade-up" style="animation-delay:0.05s;">
            Discover Your<br>
            <span class="gradient-word">Perfect&nbsp;Style</span>
          </h1>

          <p class="text-[#93A3BF] text-lg leading-relaxed max-w-md mx-auto lg:mx-0 mb-10 fade-up" style="animation-delay:0.15s;">
            Shop thousands of curated products across fashion, electronics, and home — delivered fast, returned easy.
          </p>

          <!-- CTAs -->
          <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-14 fade-up" style="animation-delay:0.22s;">
            <RouterLink to="/shop" class="btn-primary">
              Shop the Collection
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </RouterLink>
            <a href="#categories" class="btn-ghost">
              Browse Categories
            </a>
          </div>

          <!-- Stats -->
          <div class="flex items-center gap-8 justify-center lg:justify-start fade-up" style="animation-delay:0.3s;">
            <div v-for="(stat, i) in stats" :key="stat.label" class="flex items-center gap-3">
              <div class="stat-icon">{{ stat.icon }}</div>
              <div>
                <div class="text-2xl font-extrabold text-white leading-none">{{ stat.value }}</div>
                <div class="text-[11px] uppercase tracking-widest mt-0.5" style="color:#4A5568;">{{ stat.label }}</div>
              </div>
              <div v-if="i < stats.length - 1" class="stat-divider"></div>
            </div>
          </div>
        </div>

        <!-- RIGHT: Product showcase -->
        <div class="flex-1 flex items-center justify-center">
          <div class="relative">

            <!-- Glow behind card -->
            <div class="absolute -inset-10 rounded-full blur-3xl opacity-20 animate-glow"
              style="background: radial-gradient(circle, #0D6EFD 30%, #7C3AED 70%);"></div>

            <!-- Main floating card -->
            <div class="hero-card float-anim">
              <!-- Image area -->
              <div class="relative rounded-2xl overflow-hidden mb-5" style="aspect-ratio:1; background: linear-gradient(135deg, #0D1B3E 0%, #0D6EFD 50%, #7C3AED 100%);">
                <div class="absolute inset-0 shine-sweep"></div>
                <div class="w-full h-full flex items-center justify-center">
                  <span class="text-[90px] drop-shadow-2xl select-none" style="filter: drop-shadow(0 0 30px rgba(255,255,255,0.3));">👟</span>
                </div>
                <!-- Sale badge -->
                <div class="absolute top-3 left-3 sale-badge">50% OFF</div>
              </div>

              <!-- Product info -->
              <div class="px-1">
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color:#6B8CFF;">New Arrival</p>
                <h3 class="text-white font-extrabold text-lg leading-snug">Air Max Runner X</h3>
                <div class="flex items-center gap-2 mt-1 mb-4">
                  <div class="flex gap-0.5">
                    <svg v-for="i in 5" :key="i" class="w-3 h-3" :style="i<=5?'color:#FF6B00':'color:#374151'" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                  </div>
                  <span class="text-xs" style="color:#4A5568;">4.9 (342 reviews)</span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-white">$79.99</span>
                    <span class="text-sm line-through" style="color:#374151;">$159.00</span>
                  </div>
                  <button class="add-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Floating mini-badge: Rating -->
            <div class="mini-chip mini-chip-left float-anim" style="animation-delay:0.4s;">
              <span class="text-lg">⭐</span>
              <div>
                <p class="text-white text-xs font-bold leading-none">Top Rated</p>
                <p class="text-[11px] mt-0.5" style="color:#4A5568;">4.9 / 5 stars</p>
              </div>
            </div>

            <!-- Floating mini-badge: Delivery -->
            <div class="mini-chip mini-chip-right float-anim" style="animation-delay:0.8s;">
              <span class="text-lg">🚚</span>
              <div>
                <p class="text-white text-xs font-bold leading-none">Free Delivery</p>
                <p class="text-[11px] mt-0.5" style="color:#4A5568;">2–4 business days</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom wave -->
    <div class="absolute bottom-0 left-0 right-0 leading-none">
      <svg viewBox="0 0 1440 70" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;display:block;">
        <path d="M0 35C360 70 1080 0 1440 35V70H0V35Z" fill="#ffffff"/>
      </svg>
    </div>
  </section>
</template>

<script setup>
const stats = [
  { icon: '🛍️', value: '50K+',  label: 'Products'  },
  { icon: '⭐', value: '4.9',   label: 'Avg Rating' },
  { icon: '🚚', value: 'Free',  label: 'Shipping'   },
];
</script>

<style scoped>
@reference "../../css/app.css";

/* ── Background ── */
.hero { background: #06061A; }

/* ── Fade-up animation ── */
.fade-up {
  opacity: 0;
  animation: fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
@keyframes fade-up {
  from { opacity: 0; transform: translateY(28px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Badge pill ── */
.badge-pill {
  @apply inline-flex items-center gap-2 text-white text-xs font-semibold px-4 py-2 rounded-full mb-7 backdrop-blur-sm;
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.12);
  animation: fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
.pulse-dot {
  @apply w-2 h-2 rounded-full flex-shrink-0;
  background: #22C55E;
  box-shadow: 0 0 0 0 rgba(34,197,94,0.5);
  animation: pulse-ring 2s ease infinite;
}
@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
  70%  { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
  100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}

/* ── Headline ── */
.hero-heading {
  @apply text-5xl sm:text-6xl lg:text-[72px] font-extrabold text-white leading-[1.04] mb-6;
}
.gradient-word {
  color: #60A5FA;
}

/* ── Buttons ── */
.btn-primary {
  @apply inline-flex items-center justify-center gap-2.5 font-semibold px-7 py-3.5 rounded-xl text-white transition-all duration-200;
  background: #0D6EFD;
  box-shadow: 0 4px 16px -4px rgba(13,110,253,0.4);
}
.btn-primary:hover {
  background: #0B5ED7;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px -4px rgba(13,110,253,0.5);
}
.btn-primary:active { transform: translateY(0); }

.btn-ghost {
  @apply inline-flex items-center justify-center gap-2.5 font-semibold px-7 py-3.5 rounded-xl text-white transition-all duration-200 backdrop-blur-sm;
  background: rgba(255,255,255,0.06);
  border: 1.5px solid rgba(255,255,255,0.15);
}
.btn-ghost:hover {
  background: rgba(255,255,255,0.11);
  border-color: rgba(255,255,255,0.3);
  transform: translateY(-1px);
}

/* ── Stats ── */
.stat-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center text-xl;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
}
.stat-divider {
  @apply w-px h-10 ml-4;
  background: rgba(255,255,255,0.08);
}

/* ── Hero card ── */
.hero-card {
  @apply relative rounded-3xl p-5 w-[300px] sm:w-[340px];
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow:
    0 40px 80px -20px rgba(0,0,0,0.7),
    0 0 0 1px rgba(255,255,255,0.04),
    inset 0 1px 0 rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
}
.float-anim {
  animation: float 5s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  33%       { transform: translateY(-14px) rotate(0.5deg); }
  66%       { transform: translateY(-7px) rotate(-0.5deg); }
}

.shine-sweep {
  background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.12) 50%, transparent 60%);
  animation: shine 4s ease-in-out infinite;
}
@keyframes shine {
  0%   { transform: translateX(-100%) skewX(-15deg); }
  100% { transform: translateX(300%) skewX(-15deg); }
}

.sale-badge {
  @apply text-[10px] font-extrabold px-2.5 py-1 rounded-full text-white tracking-wide;
  background: #FF6B00;
  box-shadow: 0 4px 12px rgba(255,107,0,0.5);
  animation: badge-bounce 2s ease-in-out infinite;
}
@keyframes badge-bounce {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-4px); }
}

.add-btn {
  @apply w-10 h-10 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 active:scale-90;
  background: linear-gradient(135deg, #0D6EFD, #7C3AED);
  box-shadow: 0 6px 20px -4px rgba(13,110,253,0.55);
}

/* ── Mini chips ── */
.mini-chip {
  @apply absolute flex items-center gap-2.5 rounded-2xl px-3.5 py-2.5 backdrop-blur-xl;
  background: rgba(6,6,26,0.85);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 16px 40px -8px rgba(0,0,0,0.5);
}
.mini-chip-left  { left: -60px; top: 60px; }
.mini-chip-right { right: -60px; bottom: 80px; }

@media (max-width: 640px) {
  .mini-chip-left  { left: -20px; }
  .mini-chip-right { right: -20px; }
}

@keyframes glow-pulse {
  0%, 100% { opacity: 0.15; transform: scale(0.96); }
  50%       { opacity: 0.28; transform: scale(1.04); }
}
.animate-glow { animation: glow-pulse 4s ease-in-out infinite; }
</style>
