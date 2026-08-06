import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfMeta = document.head.querySelector('meta[name="csrf-token"]');

function applyCsrfToken(token) {
    if (!token) return;
    if (csrfMeta) csrfMeta.content = token;
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

if (csrfMeta?.content) {
    applyCsrfToken(csrfMeta.content);
}

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 419) {
            window.location.reload();
            return new Promise(() => {});
        }

        return Promise.reject(error);
    }
);

function refreshCsrfToken() {
    return fetch('/csrf-token', {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
    })
        .then((response) => (response.ok ? response.json() : null))
        .then((data) => applyCsrfToken(data?.token))
        .catch(() => {});
}

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        refreshCsrfToken();
    }
});

// ponytail: ping tiap 15 menit supaya session nggak mati pas tab/APK dibuka lama
setInterval(() => {
    if (!document.hidden) {
        refreshCsrfToken();
    }
}, 15 * 60 * 1000);

/**
 * Echo is now initialized lazily from notifications.js via ensureEcho().
 * Keep bootstrap lean to avoid auto WebSocket connections on page load.
 */
