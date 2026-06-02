import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo is now initialized lazily from notifications.js via ensureEcho().
 * Keep bootstrap lean to avoid auto WebSocket connections on page load.
 */
