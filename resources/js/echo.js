import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

let echoInstance = null;

export function ensureEcho() {
    if (echoInstance) {
        return echoInstance;
    }

    if (!import.meta.env.VITE_REVERB_APP_KEY) {
        return null;
    }

    let wsHost = import.meta.env.VITE_REVERB_HOST;
    if (wsHost && (wsHost.startsWith('http://') || wsHost.startsWith('https://'))) {
        wsHost = new URL(wsHost).hostname;
    }

    const useTls = (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https';
    const transports = useTls ? ['wss'] : ['ws'];

    echoInstance = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: wsHost || window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: useTls,
        enabledTransports: transports,
    });

    window.Echo = echoInstance;
    return echoInstance;
}
