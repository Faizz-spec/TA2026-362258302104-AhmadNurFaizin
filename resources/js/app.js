import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route, ZiggyVue } from 'ziggy-js';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// =================== AXIOS ===================
axios.defaults.baseURL = '/';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF dari <meta>
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
window.axios = axios;

// =================== GLOBAL LOADER (overlay spinner tengah) ===================
const makeLoader = () => {
  const loader = document.createElement('div');
  loader.id = 'global-loader';
  loader.style.cssText = `
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
  `;
  loader.innerHTML = `
    <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  `;
  document.body.appendChild(loader);
  return loader;
};

let loaderEl = null;
const showLoader = () => {
  if (!loaderEl) loaderEl = makeLoader();
  loaderEl.style.opacity = '1';
  loaderEl.style.pointerEvents = 'auto';
};
const hideLoader = () => {
  if (!loaderEl) return;
  loaderEl.style.opacity = '0';
  setTimeout(() => (loaderEl.style.pointerEvents = 'none'), 250);
};

// Pastikan body siap lalu pasang event
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    router.on('start', (event) => {
      // skip loader jika request local-table
      if (event.detail.visit?.showGlobalLoader === false) {
        return;
      }

      showLoader();
    });
    router.on('finish', (event) => {
      if (event.detail.visit?.showGlobalLoader === false) {
        return;
      }

      hideLoader();
    });
  });
} else {
  router.on('start', (event) => {
    // skip loader jika request local-table
    if (event.detail.visit?.showGlobalLoader === false) {
      return;
    }

    showLoader();
  });
  router.on('finish', (event) => {
    if (event.detail.visit?.showGlobalLoader === false) {
      return;
    }

    hideLoader();
  });
}

// =================== AUTO LOGOUT + TIMER DISPLAY ===================
document.addEventListener('DOMContentLoaded', () => {
  const maxIdle = 600 * 10000; // 60 detik
  const warningBefore = 10 * 1000; // popup 10 detik sebelum logout
  const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
  let timeLeft = maxIdle / 1000;
  let timerInterval;
  let warningShown = false;

  const timerDiv = document.createElement('div');
  timerDiv.style.position = 'fixed';
  timerDiv.style.bottom = '15px';
  timerDiv.style.right = '15px';
  timerDiv.style.background = 'rgba(0,0,0,0.7)';
  timerDiv.style.color = 'white';
  timerDiv.style.padding = '8px 14px';
  timerDiv.style.borderRadius = '8px';
  timerDiv.style.fontFamily = 'monospace';
  timerDiv.style.zIndex = '9999';
  // timerDiv.textContent = `Auto logout: ${timeLeft}s`
  document.body.appendChild(timerDiv);

  const resetTimer = () => {
    timeLeft = maxIdle / 1000;
    warningShown = false;
  };

  const autoLogout = async () => {
    clearInterval(timerInterval);
    try {
      await axios.post('/logout');
      window.location.href = '/login';
    } catch (error) {
      console.error('Auto logout gagal:', error);
    }
  };

  const showWarning = () => {
    warningShown = true;
    Swal.fire({
      title: 'Kamu akan logout otomatis!',
      text: 'Tidak ada aktivitas. Logout dalam 10 detik...',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan sesi',
      cancelButtonText: 'Logout sekarang',
      timer: warningBefore,
      timerProgressBar: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
    }).then((result) => {
      if (result.isConfirmed) resetTimer();
      else autoLogout();
    });

    setTimeout(() => {
      if (warningShown) autoLogout();
    }, warningBefore);
  };

  timerInterval = setInterval(() => {
    timeLeft--;
    // timerDiv.textContent = `Auto logout: ${timeLeft}s`

    if (timeLeft <= warningBefore / 1000 && !warningShown) showWarning();
    if (timeLeft <= 0) autoLogout();
  }, 1000);

  events.forEach((e) => {
    window.addEventListener(e, () => resetTimer());
  });
});

// =================== INERTIA APP ===================
createInertiaApp({
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .mount(el);
  },
  // Matikan progress bar default (kita pakai overlay spinner)
  progress: false,
});
