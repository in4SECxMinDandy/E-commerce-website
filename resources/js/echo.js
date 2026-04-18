/**
 * File frontend: resources/js/echo.js
 * Chuc nang: Khoi tao Laravel Echo + Pusher/Reverb cho cac tinh nang realtime.
 * Vai tro: Doc bien moi truong VITE_REVERB_* va tao window.Echo de cac man hinh chat co the lang nghe su kien broadcast.
 * Tac dung giao dien: Neu websocket kha dung, frontend cap nhat hoi thoai gan nhu tuc thi thay vi chi dua vao polling.
 */
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

