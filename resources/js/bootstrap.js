import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.baseURL = '/';            // <= penting: same-origin
axios.defaults.withCredentials = true;   // <= kirim cookie session
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
