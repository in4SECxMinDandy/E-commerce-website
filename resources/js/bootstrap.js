/**
 * File frontend: resources/js/bootstrap.js
 * Chuc nang: Cau hinh lop giao tiep Ajax co ban cho frontend.
 * Vai tro: Khoi tao axios tren window va gan header X-Requested-With de backend Laravel nhan dien request Ajax.
 * Tac dung giao dien: Cac script frontend co the goi API/Ajax bang axios theo cau hinh thong nhat.
 */
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

