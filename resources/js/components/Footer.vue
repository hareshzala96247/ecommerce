<template>
  <footer class="footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">

        <!-- Brand column (wider) -->
        <div class="lg:col-span-4">
          <a href="/" class="group inline-flex items-center gap-2.5 mb-5">
            <img v-if="settings.logo" :src="'/storage/' + settings.logo"
              class="h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300" />
            <div v-else class="relative w-9 h-9">
              <div class="absolute inset-0 rounded-xl opacity-40 blur-sm group-hover:opacity-60 transition-opacity duration-300"
                style="background: linear-gradient(135deg, #1D3FB8, #1D3FB8);"></div>
              <div class="relative w-9 h-9 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300"
                style="background: linear-gradient(135deg, #1D3FB8, #1D3FB8);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 7h13L17 13M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
              </div>
            </div>
          </a>
          <p class="text-sm leading-relaxed mb-6 max-w-xs" style="color:#6B6B6B;">
            {{ settings.site_tagline || 'Your one-stop destination for fashion, electronics, and lifestyle — curated with care, delivered with love.' }}
          </p>

          <!-- Socials -->
          <div class="flex gap-2">
            <a v-for="social in activeSocials" :key="social.name" :href="social.url" target="_blank" rel="noopener noreferrer" :title="social.name" class="social-btn">
              <span class="text-sm">{{ social.icon }}</span>
            </a>
          </div>

          <!-- Trust badges -->
          <div class="flex flex-wrap gap-2 mt-6">
            <div v-for="badge in trustBadges" :key="badge" class="trust-badge">{{ badge }}</div>
          </div>
        </div>

        <!-- Shop links -->
        <div class="lg:col-span-2">
          <h4 class="footer-col-title">Shop</h4>
          <ul class="space-y-2">
            <li v-for="link in shopLinks" :key="link">
              <a href="#" class="footer-link">{{ link }}</a>
            </li>
          </ul>
        </div>

        <!-- Support links -->
        <div class="lg:col-span-2">
          <h4 class="footer-col-title">Support</h4>
          <ul class="space-y-2">
            <li v-for="link in supportLinks" :key="link">
              <a href="#" class="footer-link">{{ link }}</a>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="lg:col-span-4">
          <h4 v-if="contactRows.length" class="footer-col-title">Get in Touch</h4>
          <ul class="space-y-3 mb-6">
            <li v-for="c in contactRows" :key="c.text" class="contact-row">
              <span class="contact-icon">{{ c.icon }}</span>
              <a v-if="c.href" :href="c.href" class="hover:text-gray-300 transition-colors duration-200">{{ c.text }}</a>
              <span v-else>{{ c.text }}</span>
            </li>
          </ul>

          <!-- App badges -->
          <div class="flex flex-col sm:flex-row gap-2">
            <a v-for="app in apps" :key="app.name" href="#" class="app-badge">
              <span class="text-2xl">{{ app.icon }}</span>
              <div>
                <p class="text-[10px] mb-0.5" style="color:#6B6B6B;">{{ app.sub }}</p>
                <p class="text-xs font-bold text-white leading-tight">{{ app.name }}</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Divider + Bottom bar -->
    <div style="border-top: 1px solid #3F3F3F;">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs" style="color:#525252;">
          © {{ new Date().getFullYear() }} {{ settings.site_name }}, Inc. All rights reserved.
        </p>
        <div class="flex items-center gap-1 text-xs" style="color:#525252;">
          <span v-for="(link, i) in legalLinks" :key="link" class="flex items-center gap-1">
            <a href="#" class="hover:text-white transition-colors duration-200">{{ link }}</a>
            <span v-if="i < legalLinks.length - 1" style="color:#3F3F3F;">·</span>
          </span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';
import { settings } from '../store/settings';

const socialDefs = [
  { key: 'social_facebook',  name: 'Facebook',  icon: 'f'  },
  { key: 'social_instagram', name: 'Instagram', icon: '📸' },
  { key: 'social_twitter',   name: 'Twitter/X', icon: '𝕏'  },
  { key: 'social_youtube',   name: 'YouTube',   icon: '▶'  },
];

const activeSocials = computed(() =>
  socialDefs.filter(s => settings[s.key]).map(s => ({ ...s, url: settings[s.key] }))
);

const contactRows = computed(() => {
  const rows = [];
  if (settings.contact_email)   rows.push({ icon: '📧', text: settings.contact_email,   href: `mailto:${settings.contact_email}` });
  if (settings.contact_phone)   rows.push({ icon: '📞', text: settings.contact_phone,   href: `tel:${settings.contact_phone}` });
  if (settings.contact_address) rows.push({ icon: '📍', text: settings.contact_address, href: null });
  return rows;
});

const trustBadges = ['SSL Secure', 'PCI Compliant', '4.9 Rated'];
const shopLinks    = ['New Arrivals', 'Best Sellers', 'Sale Items', 'Gift Cards', 'All Products'];
const supportLinks = ['My Account', 'Track Order', 'Returns & Exchanges', 'FAQs', 'Contact Us'];
const apps = [
  { icon: '🍎', sub: 'Download on the', name: 'App Store'   },
  { icon: '🤖', sub: 'Get it on',        name: 'Google Play' },
];
const legalLinks = ['Privacy Policy', 'Terms of Service', 'Cookie Policy'];
</script>

<style scoped>
@reference "../../css/app.css";

.footer { background: #1A1A1A; }

.footer-col-title {
  @apply text-xs font-extrabold uppercase tracking-widest mb-5;
  color: #A8A8A8;
}

.footer-link {
  @apply flex items-center text-sm transition-all duration-200;
  color: #6B6B6B;
}
.footer-link:hover { color: #E4E0D8; padding-left: 4px; }

.social-btn {
  @apply w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 cursor-pointer;
  background: #3F3F3F;
  border: 1px solid #3F3F3F;
  color: #A8A8A8;
}
.social-btn:hover {
  background: #3F3F3F;
  border-color: #525252;
  transform: translateY(-3px) scale(1.08);
  box-shadow: 0 8px 20px rgba(0,0,0,0.4);
  color: #fff;
}

.trust-badge {
  @apply text-[10px] font-semibold px-2.5 py-1 rounded-lg;
  background: #3F3F3F;
  color: #6B6B6B;
  border: 1px solid #3F3F3F;
}

.contact-row {
  @apply flex items-center gap-3 text-sm;
  color: #6B6B6B;
}
.contact-row:hover { color: #A8A8A8; }
.contact-icon {
  @apply w-8 h-8 rounded-lg flex items-center justify-center text-base flex-shrink-0;
  background: #3F3F3F;
}

.app-badge {
  @apply flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 flex-1;
  background: #3F3F3F;
  border: 1px solid #3F3F3F;
}
.app-badge:hover {
  border-color: #1D3FB8;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(29,63,184,0.2);
}
</style>
