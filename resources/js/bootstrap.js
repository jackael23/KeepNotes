import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Show API errors to the user instead of failing silently.
 * - 401: session expired / not signed in -> back to the login page
 * - 422: validation error -> show the first message
 * - other errors -> generic message
 */
import Swal from 'sweetalert2';

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status;
        const url = error.config?.url || '';
        const isAuthCall = url.includes('login') || url.includes('register');

        if (status === 401 && !isAuthCall) {
            if (window.location.pathname !== '/') window.location.href = '/';
        } else if (status === 422 && !isAuthCall) {
            const errors = error.response.data?.errors || {};
            const first = Object.values(errors)[0];
            Swal.fire({ icon: 'warning', title: 'Please check your input', text: Array.isArray(first) ? first[0] : 'Some fields are invalid.' });
        } else if (!isAuthCall) {
            Swal.fire({ icon: 'error', title: 'Something went wrong', text: 'Your change was not saved. Please try again.' });
        }
        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
