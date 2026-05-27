import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

if (import.meta.env.VITE_REVERB_APP_KEY) {
    let wsHost = import.meta.env.VITE_REVERB_HOST;
    if (wsHost && (wsHost.startsWith('http://') || wsHost.startsWith('https://'))) {
        wsHost = new URL(wsHost).hostname;
    }
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: wsHost || window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    console.warn("Laravel Echo/Reverb is not configured properly in .env");
}
