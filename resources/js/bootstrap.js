import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configuración de Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Interceptor para manejar errores de autenticación
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // Si la sesión ha expirado, recargar la página
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

// Configuración de Echo para WebSockets
const reverbConfig = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'local-key',
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT || 8088, 10),
    wssPort: parseInt(import.meta.env.VITE_REVERB_PORT || 8088, 10),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    // Asegurar que se usen los puertos correctos según el esquema
    ...(window.location.protocol === 'https:' ? {
        wsPort: 443,
        wssPort: 443
    } : {})
};

// Solo inicializar Echo si estamos en el cliente
if (typeof window !== 'undefined') {
    window.Echo = new Echo(reverbConfig);
    
    // Manejar eventos de conexión/desconexión
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Conexión WebSocket establecida correctamente');
    });

    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.warn('Desconectado del servidor WebSocket');
    });

    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('Error en la conexión WebSocket:', error);
    });
}