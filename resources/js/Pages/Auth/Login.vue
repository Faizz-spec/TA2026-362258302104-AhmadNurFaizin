<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

axios.defaults.baseURL = '/';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
delete axios.defaults.headers.common['X-CSRF-TOKEN']; // pastikan ini tidak ada

const showPassword = ref(false)
// ====== State popup ganti password ======
const showForceModal = ref(false)
const newPass = ref('')
const newPassConfirm = ref('')
const savingNew = ref(false)
const newPassErr = ref('')
const pendingRedirect = ref('/') // redirect tujuan setelah ganti password

// ====== State login ======
const username = ref('')
const password = ref('')
const loading  = ref(false)
const siteKey  = import.meta.env.VITE_RECAPTCHA_SITE_KEY
const errors   = ref({ username: '', password: '', captcha: '' })
let widgetId   = null

// (aman) set CSRF header kalau meta tag ada
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Loader reCAPTCHA idempotent
const loadRecaptcha = () => {
  if (window.grecaptcha) return Promise.resolve(window.grecaptcha)

  const existed = document.querySelector('#recaptcha-script')
  if (existed) {
    return new Promise((resolve, reject) => {
      existed.addEventListener('load', () => resolve(window.grecaptcha))
      existed.addEventListener('error', reject)
    })
  }

  return new Promise((resolve, reject) => {
    const s = document.createElement('script')
    s.id = 'recaptcha-script'
    s.src = 'https://www.google.com/recaptcha/api.js?render=explicit'
    s.async = true
    s.defer = true
    s.onload = () => resolve(window.grecaptcha)
    s.onerror = () => reject(new Error('Failed to load reCAPTCHA'))
    document.head.appendChild(s)
  })
}

/* ================================
   PING / INDICATOR LOGIC
================================ */
const latencyMs = ref(null)          // number | null
const quality  = ref('checking')     // 'checking' | 'good' | 'ok' | 'bad' | 'down'
const lastAt   = ref(null)           // Date | null
const intervalMs = 3000
let pingTimer = null

const PING_URL = '/favicon.ico'
const TIMEOUT_MS = 5000

async function measurePing () {
  const start = performance.now()
  const ctrl = new AbortController()
  const timeout = setTimeout(() => ctrl.abort('timeout'), TIMEOUT_MS)

  try {
    const res = await fetch(PING_URL, {
      cache: 'no-store',
      signal: ctrl.signal,
      method: 'GET',
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    const end = performance.now()
    const ms = Math.max(1, Math.round(end - start))
    latencyMs.value = ms
    lastAt.value = new Date()

    if (ms < 100)       quality.value = 'good'
    else if (ms < 250)  quality.value = 'ok'
    else                quality.value = 'bad'
  } catch (e) {
    latencyMs.value = null
    quality.value = 'down'
    lastAt.value = new Date()
  } finally {
    clearTimeout(timeout)
  }
}

const pingLabel = computed(() => {
  switch (quality.value) {
    case 'good':
      return '🟢 BAGUS'
    case 'ok':
      return '🟡 SEDANG'
    case 'bad':
      return '🔴 JELEK'
    case 'down':
      return '⚫ OFFLINE'
    default:
      return '⏳ CEK...'
  }
})

const iconClass = computed(() => {
  switch (quality.value) {
    case 'good': return 'bi bi-reception-4'
    case 'ok':   return 'bi bi-reception-2'
    case 'bad':  return 'bi bi-reception-0'
    case 'down': return 'bi bi-wifi-off'
    default:     return 'bi bi-reception-1'
  }
})

const badgeClass = computed(() => {
  switch (quality.value) {
    case 'good': return 'bg-success-subtle text-success-emphasis border border-success-subtle'
    case 'ok':   return 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
    case 'bad':  return 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'
    case 'down': return 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'
    default:     return 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'
  }
})

const tooltipTitle = computed(() => {
  if (!lastAt.value) return 'Mengukur koneksi…'
  const ts = lastAt.value.toLocaleTimeString()
  if (quality.value === 'down') return `Terakhir cek ${ts} • koneksi bermasalah`
  if (quality.value === 'good') return `Terakhir cek ${ts} • koneksi bagus`
  if (quality.value === 'ok')   return `Terakhir cek ${ts} • koneksi sedang`
  if (quality.value === 'bad')  return `Terakhir cek ${ts} • koneksi jelek`
  return `Terakhir cek ${ts}`
})

onMounted(async () => {
  const grecaptcha = await loadRecaptcha()
  await nextTick()
  widgetId = grecaptcha.render('recaptcha-box', {
    sitekey: siteKey,
    theme: 'light',
  })

  // Tooltip untuk badge ping
  if (window.bootstrap) {
    const els = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    els.forEach(el => new window.bootstrap.Tooltip(el))
  }

  // Ping awal + interval
  measurePing()
  pingTimer = setInterval(measurePing, intervalMs)
})

onBeforeUnmount(() => {
  if (pingTimer) clearInterval(pingTimer)
})

const swalLoading = (title = 'Memverifikasi…') => {
  Swal.fire({
    title,
    allowEscapeKey: false,
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => Swal.showLoading(),
    background: 'rgba(255,255,255,0.95)',
  })
}
const swalError = (msg = 'Terjadi kesalahan') => {
  Swal.fire({
    icon: 'error',
    title: 'Login gagal',
    text: msg,
    showClass: { popup: 'animate__animated animate__fadeInDown' },
    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
  })
}
const swalSuccess = (msg = 'Berhasil!') => {
  Swal.fire({
    icon: 'success',
    title: msg,
    timer: 1200,
    showConfirmButton: false,
    showClass: { popup: 'animate__animated animate__fadeInDown' },
    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
  })
}
const swalToast = (title = 'Info', icon = 'info') => {
  Swal.fire({
    toast: true,
    position: 'top-end',
    timer: 2000,
    showConfirmButton: false,
    icon,
    title,
  })
}

const submit = async () => {
  errors.value = { username: '', password: '', captcha: '' }

  if (!username.value) errors.value.username = 'Username wajib diisi.'
  if (!password.value) errors.value.password = 'Password wajib diisi.'

  const recaptchaToken = window.grecaptcha?.getResponse(widgetId) || ''
  if (!recaptchaToken) errors.value.captcha = 'Tolong centang reCAPTCHA dulu.'

  if (errors.value.username || errors.value.password || errors.value.captcha) return

  loading.value = true
  try {
    swalLoading('Memverifikasi kredensial…')

    const debugRes = await axios.post('/_csrf-debug', {});
    console.log('CSRF-DEBUG', debugRes.data);
    const dbg = await axios.post('/_csrf-debug', {}); 
    console.log(dbg.data);
    await axios.get('/sanctum/csrf-cookie'); 
    await axios.post('/_csrf-debug', { ping: 'pong' }); 

    const res = await axios.post('/login', {
      username: username.value,
      password: password.value,
      'g-recaptcha-response': recaptchaToken,
    });

    const needChange = !!res?.data?.require_password_change
    const redirectUrl = res?.data?.redirect ?? '/dashboard'
    Swal.close()

    if (needChange) {
      pendingRedirect.value = redirectUrl
      showForceModal.value = true
      if (window.grecaptcha && widgetId !== null) window.grecaptcha.reset(widgetId)
    } else {
      window.location.href = redirectUrl
    }
  } catch (e) {
    if (window.grecaptcha && widgetId !== null) window.grecaptcha.reset(widgetId)
    Swal.close()

    if (e?.response?.status === 422) {
      const errs = e.response.data.errors || {}
      errors.value.username = errs.username?.[0] || ''
      errors.value.password = errs.password?.[0] || ''
      errors.value.captcha  = errs.captcha?.[0] || errs['g-recaptcha-response']?.[0] || ''

      swalToast('Cek kembali isian kamu', 'warning')

      const firstError =
        errors.value.username || errors.value.password || errors.value.captcha
      if (firstError) {
        Swal.fire({
          icon: 'error',
          title: 'Login gagal',
          text: firstError,
          confirmButtonColor: '#2d6a4f',
          background: 'rgba(255,255,255,0.95)',
          showClass: { popup: 'animate__animated animate__fadeInDown' },
          hideClass: { popup: 'animate__animated animate__fadeOutUp' },
        })
      }
      return
    }
    swalError(e?.response?.data?.message || 'Username/password salah atau server bermasalah.')
  } finally {
    loading.value = false
  }
  return
}

async function saveNewPassword() {
  newPassErr.value = ''

  if (!newPass.value || newPass.value.length < 8) {
    newPassErr.value = 'Minimal 8 karakter.'
    return
  }
  if (newPass.value !== newPassConfirm.value) {
    newPassErr.value = 'Konfirmasi password tidak cocok.'
    return
  }

  savingNew.value = true
  try {
    await axios.post('/auth/password/force-update', {
      new_password: newPass.value,
      new_password_confirmation: newPassConfirm.value,
    })

    showForceModal.value = false
    window.location.href = pendingRedirect.value || '/dashboard'
  } catch (e) {
    newPassErr.value = e?.response?.data?.message || 'Gagal menyimpan password baru.'
    Swal.fire({
      icon: 'error',
      title: 'Gagal menyimpan password',
      text: newPassErr.value,
      confirmButtonColor: '#2d6a4f',
      background: 'rgba(255,255,255,0.95)',
      showClass: { popup: 'animate__animated animate__fadeInDown' },
      hideClass: { popup: 'animate__animated animate__fadeOutUp' },
    })
  } finally {
    savingNew.value = false
  }
}
</script>

<template>
  <div class="login-page d-flex align-items-center justify-content-center px-3 py-5 position-relative">
    <div class="login-card row g-0 shadow-lg overflow-hidden position-relative z-1 w-100"
         style="max-width: 900px;">
      
      <!-- Panel Kiri -->
      <div class="left-panel col-12 col-md-6 d-flex flex-column justify-content-center align-items-center text-center order-2 order-md-1">
        <img
          src="../../../../public/images/Pukesmas.webp"
          alt="Puskesmas Illustration"
          class="img-fluid px-5 px-md-4"
          style="max-height: 260px"
        />
        <p class="small mt-3 text-muted mb-4 mb-md-0">&copy; 2025 SISTEM INFORMASI MANAJEMEN PUSKESMAS<br />Dinas Kesehatan Banyuwangi</p>
      </div>

      <!-- Panel Kanan -->
      <div class="right-panel col-12 col-md-6 text-white d-flex flex-column justify-content-center p-4 p-md-5 order-1 order-md-2">

        <!-- Badge ping di pojok kanan atas -->
        <div class="d-flex justify-content-end mb-2">
          <div
            class="d-flex align-items-center px-2 py-1 rounded-3 ping-badge"
            :class="badgeClass"
            :title="tooltipTitle"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
          >
            <i :class="iconClass" class="me-1"></i>
            <small class="fw-semibold">{{ pingLabel }}</small>
          </div>
        </div>

        <h4 class="fw-semibold mb-4 text-center text-md-start">Login</h4>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label class="form-label small text-white-50">Username</label>
            <input v-model="username" type="text" class="form-control input-custom" placeholder="Enter your username" :class="{'is-invalid': errors.username}" />
            <div v-if="errors.username" class="invalid-feedback d-block">{{ errors.username }}</div>
          </div>

          <div class="mb-3 position-relative">
            <label class="form-label small text-white-50">Password</label>
            <div class="input-group">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control input-custom"
                placeholder="Enter your password"
                :class="{ 'is-invalid': errors.password }"
              />
              <button
                type="button"
                class="btn btn-outline-secondary border-start-0"
                @click="showPassword = !showPassword"
                tabindex="-1"
              >
                <i :class="[showPassword ? 'bi bi-eye-slash' : 'bi bi-eye', 'text-white']"></i>
              </button>
            </div>
            <div v-if="errors.password" class="invalid-feedback d-block">{{ errors.password }}</div>
          </div>

          <div id="recaptcha-box" class="mb-2"></div>
          <div v-if="errors.captcha" class="invalid-feedback d-block mb-3">{{ errors.captcha }}</div>

          <div class="d-grid mb-3">
            <button :disabled="loading" type="submit" class="btn login-btn rounded-pill py-2 fw-semibold d-inline-flex align-items-center justify-content-center">
              <span v-if="!loading">Login Simpuswangi</span>
              <span v-else class="d-inline-flex align-items-center">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memproses…
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Paksa Ganti Password -->
    <div v-if="showForceModal"
         class="modal fade show"
         style="display:block; background: rgba(0,0,0,.35);"
         data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title">Password Kedaluwarsa</h5>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-3">Password Anda sudah lebih dari 15 hari. Silakan perbarui untuk melanjutkan.</p>

            <div class="mb-2">
              <label class="form-label">Password Baru</label>
              <input type="password" v-model="newPass" class="form-control" />
            </div>
            <div class="mb-2">
              <label class="form-label">Konfirmasi Password Baru</label>
              <input type="password" v-model="newPassConfirm" class="form-control" />
            </div>

            <div v-if="newPassErr" class="text-danger small mt-2">{{ newPassErr }}</div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary w-100" :disabled="savingNew" @click="saveNewPassword">
              {{ savingNew ? 'Menyimpan...' : 'Simpan Password' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
@import 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';

.login-page {
  background:
    linear-gradient(-45deg, rgba(142,202,230,.35), rgba(214,245,236,.35), rgba(167,214,193,.35), rgba(224,247,239,.35)),
    url('../../../../public/images/Background2.webp') center center / cover no-repeat;
  animation: gradientMove 15s ease infinite;
  font-family: 'Poppins', sans-serif;
  position: relative;
  overflow: hidden;
  z-index: 0;
  min-height: 100vh;
}

@keyframes gradientMove {
  0% { background-position: 0% 50%, center; }
  50% { background-position: 100% 50%, center; }
  100% { background-position: 0% 50%, center; }
}

.login-card {
  display: flex;
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(10px);
  background-color: rgba(255,255,255,0.1);
  box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
  border: 1px solid rgba(255,255,255,0.18);
  min-height: 520px;
}
@media (max-width: 767.98px) { .login-card { min-height: unset; } }

.left-panel {
  background-color: #ffffff;
  padding: 2rem;
  z-index: 1;
  clip-path: polygon(0 0, 85% 0, 70% 100%, 0% 100%);
}
@media (max-width: 767.98px) {
  .left-panel { clip-path: none; padding: 1.5rem 1rem 2rem; }
}
.right-panel {
  background: linear-gradient(160deg, #2d6a4f, #1b4332);
  color: white;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.input-custom {
  background-color: rgba(255,255,255,0.1);
  border: 1px solid #dee2e6;
  color: white;
  border-radius: 50px;
  padding: 0.6rem 1rem;
  transition: all 0.3s ease;
}
.input-custom::placeholder { color: rgba(255,255,255,0.5); }
.input-custom:focus {
  outline: none;
  background-color: rgba(255,255,255,0.2);
  border-color: #80ed99;
  box-shadow: 0 0 0 0.2rem rgba(129,199,132,0.25);
}

.login-btn {
  background: linear-gradient(to right, #38b000, #70e000);
  border: none;
  transition: background 0.3s ease;
}
.login-btn:hover {
  background: linear-gradient(to right, #70e000, #38b000);
}

/* Badge ping kecil rapi di halaman login */
.ping-badge {
  line-height: 1;
  font-size: 12px;
}

.bubbles { position: absolute; inset: 0; margin: 0; padding: 0; overflow: hidden; z-index: 0; list-style: none; }
.bubbles li {
  position: absolute;
  bottom: -120px;
  border-radius: 50%;
  animation: bubbleFloat 20s linear infinite;
  box-shadow: 0 0 20px rgba(129,199,132,0.2);
}
@keyframes bubbleFloat {
  0% { transform: translateY(0) scale(0.8); opacity: 0.3; }
  50% { transform: translateY(-500px) scale(1.1); opacity: 0.8; }
  100% { transform: translateY(-1000px) scale(1.3); opacity: 0; }
}

.rain { position: absolute; inset: 0; pointer-events: none; z-index: 0; }
.raindrop {
  position: absolute;
  top: -10%;
  width: 2px;
  height: 25px;
  background: rgba(0,0,0,0.15);
  animation: rainFall linear infinite;
  border-radius: 1px;
}
@keyframes rainFall { to { transform: translateY(120vh); } }

.particle { position: absolute; background: white; border-radius: 50%; z-index: 1; }

@media (max-width: 575.98px) { .right-panel { padding: 1.25rem !important; } }
</style>
