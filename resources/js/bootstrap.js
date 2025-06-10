import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    cluster: import.meta.env.VITE_REVERB_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_REVERB_HOST ? import.meta.env.VITE_REVERB_HOST : `ws-${import.meta.env.VITE_REVERB_APP_CLUSTER}.reverb.com`,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});