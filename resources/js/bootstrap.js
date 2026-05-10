import axios from 'axios';
window.axios = axios;

window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// CSRF is handled via the XSRF-TOKEN cookie (set by Laravel on every response).
// Axios reads it automatically and sends it as X-XSRF-TOKEN on every request,
// so it stays in sync even after login/logout regenerate the session token.
