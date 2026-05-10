import { createRouter, createWebHistory } from 'vue-router';
import { watch } from 'vue';
import { settings } from '../store/settings';
// HomePage is eagerly bundled — it's the landing page, always needed immediately.
import HomePage from '../pages/HomePage.vue';

const routes = [
  { path: '/',                  name: 'home',               component: HomePage,                                                   meta: { pageTitle: 'Your Online Store', home: true } },
  { path: '/shop',              name: 'shop',               component: () => import('../pages/ShopPage.vue'),                      meta: { pageTitle: 'All Products' } },
  { path: '/product/:slug',     name: 'product',            component: () => import('../pages/ProductDetailPage.vue'),             meta: { pageTitle: 'Product' } },
  { path: '/cart',              name: 'cart',               component: () => import('../pages/CartPage.vue'),                      meta: { pageTitle: 'Cart' } },
  { path: '/checkout',          name: 'checkout',           component: () => import('../pages/CheckoutPage.vue'),                  meta: { pageTitle: 'Checkout' } },
  { path: '/order/confirmation',name: 'order-confirmation', component: () => import('../pages/OrderConfirmationPage.vue'),         meta: { pageTitle: 'Order Confirmed' } },
  { path: '/login',             name: 'login',              component: () => import('../pages/LoginPage.vue'),                     meta: { pageTitle: 'Sign In' } },
  { path: '/register',          name: 'register',           component: () => import('../pages/RegisterPage.vue'),                  meta: { pageTitle: 'Create Account' } },
  { path: '/account',           name: 'account',            component: () => import('../pages/AccountPage.vue'),                   meta: { pageTitle: 'My Account' } },
  { path: '/orders',            name: 'orders',             component: () => import('../pages/OrdersPage.vue'),                    meta: { pageTitle: 'My Orders' } },
  { path: '/orders/:id',        name: 'order-detail',       component: () => import('../pages/OrderDetailPage.vue'),               meta: { pageTitle: 'Order Detail' } },
  { path: '/category/:slug',    name: 'category',           component: () => import('../pages/CategoryPage.vue'),                  meta: { pageTitle: 'Category' } },
  { path: '/favorites',         name: 'favorites',          component: () => import('../pages/FavoritesPage.vue'),                  meta: { pageTitle: 'My Favorites' } },
];

function buildTitle(meta) {
  const brand = settings.site_name;
  if (!brand) return meta.pageTitle ?? '';
  return meta.home ? brand : `${meta.pageTitle} — ${brand}`;
}

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, _from, savedPosition) {
    if (savedPosition) return savedPosition;
    if (to.hash) return { el: to.hash, behavior: 'smooth', top: 80 };
    return { top: 0, behavior: 'smooth' };
  },
});

router.afterEach((to) => {
  document.title = buildTitle(to.meta);
});

// Re-apply title once settings load (site_name arrives after initial navigation)
watch(() => settings.site_name, () => {
  document.title = buildTitle(router.currentRoute.value.meta);
});

export default router;
