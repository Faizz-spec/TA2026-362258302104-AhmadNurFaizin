<template>
  <div>
    <!-- Header -->
    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
      <div>
        <h5 class="fw-bold text-success mb-1">Integrasi SATU SEHAT (MTBS)</h5>
        <div class="text-muted small">
          Frontend hanya mengirim data dummy dan menampilkan log proses. Seluruh proses integrasi (token → FHIR) dilakukan di backend.
        </div>
      </div>

      <div class="text-end">
        <div class="small text-muted">Status</div>
        <span class="badge" :class="statusBadgeClass">
          {{ statusLabel }}
        </span>
      </div>
    </div>

    <!-- Config -->
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="fw-semibold">Konfigurasi</div>
            <div class="small text-muted">Org ID dari env frontend, request dikirim ke backend API.</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" @click="resetUI" :disabled="busy || polling">
              Reset
            </button>
            <button class="btn btn-primary btn-sm" @click="kirim" :disabled="busy">
              <span v-if="busy">Memulai...</span>
              <span v-else>Kirim (Dummy)</span>
            </button>
          </div>
        </div>

        <div class="row mt-3 g-2 small">
          <div class="col-12 col-md-6">
            <div class="text-muted">Organization ID</div>
            <div class="fw-semibold">{{ ORG_ID }}</div>
          </div>
          <div class="col-12 col-md-6">
            <div class="text-muted">API Base</div>
            <div class="fw-semibold">{{ API_BASE }}</div>
          </div>
        </div>

        <div v-if="jobId" class="mt-3">
          <span class="badge bg-dark">job_id: {{ jobId }}</span>
          <span v-if="polling" class="badge bg-info text-dark ms-2">polling...</span>
        </div>
      </div>
    </div>

    <!-- Steps / Progress -->
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Tahapan Proses (Backend)</div>
        <div class="small text-muted mb-3">
          Ini urutan standar integrasi MTBS ke SATU SEHAT: token → patient → practitioner → location → encounter → observation → questionnaireResponse → finish.
        </div>

        <div class="mb-3">
          <div class="small mb-1 text-muted">Progress</div>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar" role="progressbar" :style="{ width: progressPercent + '%' }"></div>
          </div>
          <div class="small text-muted mt-1">
            {{ progressText }}
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 32px;">#</th>
                <th>Step</th>
                <th>Status</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(s, idx) in steps" :key="s.key">
                <td class="text-muted">{{ idx + 1 }}</td>
                <td class="fw-semibold">{{ s.label }}</td>
                <td>
                  <span class="badge" :class="stepBadge(s.state)">
                    {{ s.state }}
                  </span>
                </td>
                <td class="small text-muted">{{ s.note }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="errorSummary" class="alert alert-danger mt-3 mb-0">
          <div class="fw-semibold">Gagal</div>
          <div class="small">{{ errorSummary }}</div>
        </div>

        <div v-if="successSummary" class="alert alert-success mt-3 mb-0">
          <div class="fw-semibold">Sukses ✅</div>
          <div class="small">{{ successSummary }}</div>
        </div>
      </div>
    </div>

    <!-- Payload -->
    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Payload Dummy (Frontend → Backend)</div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" @click="copyPayload" :disabled="busy">
                  Salin JSON
                </button>
                <button class="btn btn-outline-secondary btn-sm" @click="downloadPayload" :disabled="busy">
                  Unduh JSON
                </button>
              </div>
            </div>

            <div class="small text-muted mb-2">
              Data ini belum dari database. Ini hanya untuk ngetes alur integrasi backend.
            </div>

            <pre class="bg-light p-2 rounded small mb-0" style="max-height: 360px; overflow:auto;">{{ prettyPayload }}</pre>
          </div>
        </div>
      </div>

      <!-- Logs -->
      <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Log Proses (dari Backend)</div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" @click="fetchJobStatus" :disabled="!jobId || polling || busy">
                  Refresh
                </button>
                <button class="btn btn-outline-danger btn-sm" @click="clearLogs" :disabled="logs.length === 0 || busy || polling">
                  Bersihkan
                </button>
              </div>
            </div>

            <div class="small text-muted mb-2">
              Log ini ditarik dari backend berdasarkan <b>job_id</b>. Cocok untuk evidence uji integrasi.
            </div>

            <div v-if="logs.length === 0" class="text-muted small">
              Belum ada log.
            </div>

            <div v-else class="list-group">
              <div v-for="(l, idx) in logs" :key="idx" class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="fw-semibold">
                    <span class="badge me-2" :class="logBadge(l.level)">
                      {{ (l.level || 'info').toUpperCase() }}
                    </span>
                    {{ l.title }}
                  </div>
                  <div class="text-muted small">{{ l.time }}</div>
                </div>
                <div class="small text-muted mt-1">{{ l.message }}</div>

                <div v-if="l.meta" class="small mt-2">
                  <pre class="bg-light p-2 rounded mb-0" style="max-height: 160px; overflow:auto;">{{ l.meta }}</pre>
                </div>
              </div>
            </div>

            <div class="mt-3 small text-muted">
              <b>Catatan:</b> Jika job berhenti di step tertentu, cek error detail di log bagian meta.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-3 text-muted small">
      *Frontend tidak menyimpan secret. Semua call ke SATU SEHAT dilakukan backend.
      Data dummy hanya untuk menguji urutan proses dan logging.
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'

/* ======================
   ENV (frontend)
====================== */
const ORG_ID = import.meta.env.VITE_SATUSEHAT_ORG_ID || '665ed4bb-62dc-4fc8-bcb1-8f9c440102fb'
const API_BASE = import.meta.env.VITE_API_BASE || '/api'

/* ======================
   UI STATE
====================== */
const busy = ref(false)
const polling = ref(false)
const jobId = ref(null)

const logs = ref([])
const phase = ref('idle') // idle | running | success | failed

const errorSummary = ref('')
const successSummary = ref('')

/* ======================
   Steps state
====================== */
const steps = ref([
  { key: 'token', label: 'Ambil Token OAuth2', state: 'pending', note: 'client_credentials' },
  { key: 'patient', label: 'Patient (search/create)', state: 'pending', note: 'resolve patient_id' },
  { key: 'practitioner', label: 'Practitioner (search)', state: 'pending', note: 'resolve practitioner_id' },
  { key: 'location', label: 'Location (search)', state: 'pending', note: 'resolve location_id' },
  { key: 'encounter', label: 'Encounter (create)', state: 'pending', note: 'buat encounter_id' },
  { key: 'observation', label: 'Observation MTBS', state: 'pending', note: 'kirim hasil klasifikasi' },
  { key: 'questionnaire', label: 'QuestionnaireResponse Q0027', state: 'pending', note: 'kirim jawaban MTBS' },
  { key: 'finish', label: 'Update Encounter finished', state: 'pending', note: 'tutup kunjungan' },
])

const stepOrder = computed(() => steps.value.map(s => s.key))

/* ======================
   Dummy payload (frontend)
   — backend jangan query DB dulu
====================== */
const payload = computed(() => {
  const regId = `REG-${new Date().toISOString().slice(0, 10).replaceAll('-', '')}-0001`

return {
  meta: {
    org_id: ORG_ID, // 665ed4bb-62dc-4fc8-bcb1-8f9c440102fb
    module: 'MTBS',
    environment: 'sandbox',
    generated_at: new Date().toISOString(),
  },

  // =====================
  // PASIEN (WAJIB VALID)
  // =====================
  pasien: {
    // ⚠️ NIK DUMMY RESMI SANDBOX (BUKAN RANDOM)
    nik: '9271060312000001',
    nama: 'ANAK DUMMY MTBS',
    tanggal_lahir: '2021-06-01',
    jenis_kelamin: 'male', // male | female
    alamat: 'Banyuwangi',
  },

  // =====================
  // NAKES
  // =====================
nakes: {
  nik: '7209061211900001',
  ihs: '10009880728',
  nama: 'dr. Alexander',
  role: 'doctor',
},



  // =====================
  // KUNJUNGAN / ENCOUNTER
  // =====================
  kunjungan: {
    registration_id: regId || `REG-MTBS-${Date.now()}`,
    tanggal_kunjungan: new Date().toISOString(),

    // ⚠️ Lebih aman pakai nama generik yang biasanya ADA
    unit: 'Poli KIA',

    keluhan: 'Demam 2 hari, batuk pilek',
  },

  // =====================
  // MTBS (BELUM DIKIRIM KE FHIR, TAPI DISIMPAN UNTUK LOG)
  // =====================
  mtbs: {
    klasifikasi_status_kegawatan: 'Tidak gawat',

    diagnosa_medis: [
      {
        code: 'J06.9',
        display: 'ISPA akut non-spesifik',
      },
    ],

    tindakan: [
      {
        code: '99.99',
        display: 'Konseling umum',
      },
    ],

    alergi: {
      obat: [],
      makanan: [],
    },

    catatan: 'Dummy MTBS untuk uji integrasi SATU SEHAT sandbox.',
  },
}

})

const prettyPayload = computed(() => JSON.stringify(payload.value, null, 2))

/* ======================
   Helpers: Logs
====================== */
const pushLog = (level, title, message, metaObj = null) => {
  logs.value.unshift({
    level,
    title,
    message,
    time: new Date().toLocaleString(),
    meta: metaObj ? JSON.stringify(metaObj, null, 2) : null,
  })
}

const logBadge = (level) => {
  switch (level) {
    case 'success': return 'bg-success'
    case 'warn': return 'bg-warning text-dark'
    case 'error': return 'bg-danger'
    default: return 'bg-info text-dark'
  }
}

const stepBadge = (state) => {
  switch (state) {
    case 'done': return 'bg-success'
    case 'running': return 'bg-primary'
    case 'failed': return 'bg-danger'
    case 'skipped': return 'bg-secondary'
    default: return 'bg-warning text-dark' // pending
  }
}

/* ======================
   Status UI
====================== */
const statusLabel = computed(() => {
  switch (phase.value) {
    case 'idle': return 'Idle'
    case 'running': return 'Proses berjalan'
    case 'success': return 'Sukses'
    case 'failed': return 'Gagal'
    default: return 'Unknown'
  }
})

const statusBadgeClass = computed(() => {
  switch (phase.value) {
    case 'idle': return 'bg-secondary'
    case 'running': return 'bg-primary'
    case 'success': return 'bg-success'
    case 'failed': return 'bg-danger'
    default: return 'bg-secondary'
  }
})

const progressPercent = computed(() => {
  const total = steps.value.length
  const done = steps.value.filter(s => s.state === 'done').length
  const running = steps.value.some(s => s.state === 'running')
  const failed = steps.value.some(s => s.state === 'failed')

  if (failed) return Math.max(10, Math.round((done / total) * 100))
  if (running) return Math.max(10, Math.round(((done + 0.5) / total) * 100))
  if (phase.value === 'success') return 100
  return Math.max(10, Math.round((done / total) * 100))
})

const progressText = computed(() => {
  const failedStep = steps.value.find(s => s.state === 'failed')
  const runningStep = steps.value.find(s => s.state === 'running')
  if (failedStep) return `Gagal di step: ${failedStep.label}`
  if (runningStep) return `Sedang berjalan: ${runningStep.label}`
  if (phase.value === 'success') return 'Semua step selesai.'
  return 'Belum ada proses.'
})

/* ======================
   Backend API integration
====================== */
let pollTimer = null

const markStepsFromBackend = (stepKey, stepStateMap, noteMap) => {
  // stepStateMap: { token:"done", patient:"running", ... }
  steps.value = steps.value.map(s => ({
    ...s,
    state: stepStateMap?.[s.key] ?? s.state,
    note: noteMap?.[s.key] ?? s.note,
  }))

  // highlight "current step" if backend only gives current_step
  if (stepKey && !stepStateMap) {
    steps.value = steps.value.map(s => {
      if (s.state === 'done' || s.state === 'failed') return s
      return { ...s, state: s.key === stepKey ? 'running' : 'pending' }
    })
  }
}

const startPolling = () => {
  if (!jobId.value) return
  polling.value = true

  pollTimer = setInterval(async () => {
    await fetchJobStatus()
  }, 1200)
}

const stopPolling = () => {
  polling.value = false
  if (pollTimer) clearInterval(pollTimer)
  pollTimer = null
}

onBeforeUnmount(() => stopPolling())

/**
 * Expected backend response (contoh):
 * {
 *   "job_id": "abc",
 *   "status": "running|success|failed",
 *   "current_step": "patient",
 *   "steps": { "token":"done", "patient":"running", ... },   // optional
 *   "notes": { "patient":"found existing", ... },            // optional
 *   "logs": [ {level,title,message,time,meta}, ... ],
 *   "result": { encounter_id:"...", patient_id:"..." }       // optional
 * }
 */
const fetchJobStatus = async () => {
  if (!jobId.value) return

  try {
    const res = await fetch(`${API_BASE}/satusehat/jobs/${jobId.value}`, { method: 'GET' })
    const json = await res.json().catch(() => ({}))

    if (!res.ok) {
      throw { status: res.status, json }
    }

    // phase
    if (json.status === 'running') phase.value = 'running'
    if (json.status === 'success') phase.value = 'success'
    if (json.status === 'failed') phase.value = 'failed'

    // steps
    if (json.steps) markStepsFromBackend(null, json.steps, json.notes)
    else if (json.current_step) markStepsFromBackend(json.current_step)

    // logs (replace with latest from backend)
    if (Array.isArray(json.logs)) logs.value = json.logs

    // summary
    if (json.status === 'failed') {
      errorSummary.value = json.error_message || 'Proses gagal. Cek log untuk detail.'
      stopPolling()
    }

    if (json.status === 'success') {
      successSummary.value = 'Pengiriman selesai. Encounter/Observation/QR sudah dibuat (sandbox).'
      stopPolling()
    }
  } catch (e) {
    // Kalau polling error sekali, jangan langsung stop total—tapi logkan
    pushLog('error', 'Polling error', 'Gagal mengambil status job dari backend.', e)
  }
}

/* ======================
   ACTIONS
====================== */
const resetUI = () => {
  stopPolling()
  jobId.value = null
  busy.value = false
  phase.value = 'idle'
  errorSummary.value = ''
  successSummary.value = ''
  logs.value = []
  steps.value = steps.value.map(s => ({ ...s, state: 'pending' }))
}

const kirim = async () => {
  // reset output
  errorSummary.value = ''
  successSummary.value = ''
  logs.value = []
  steps.value = steps.value.map(s => ({ ...s, state: 'pending' }))

  try {
    busy.value = true
    phase.value = 'running'
    pushLog('info', 'Request dikirim', 'Mengirim payload dummy ke backend untuk diproses.')

    const res = await fetch(`${API_BASE}/satusehat/mtbs/send`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload.value),
    })

    const json = await res.json().catch(() => ({}))

    if (!res.ok) {
      throw { status: res.status, json }
    }

    jobId.value = json.job_id || json.id || null
    pushLog('success', 'Job dibuat', 'Backend menerima payload dan memulai proses.', json)

    // Mulai polling status
    if (jobId.value) {
      await fetchJobStatus()
      startPolling()
    } else {
      // kalau backend tidak pakai job, langsung anggap selesai
      phase.value = 'success'
      successSummary.value = 'Backend tidak mengembalikan job_id. Pastikan backend mengirim status/log.'
    }
  } catch (e) {
    phase.value = 'failed'
    errorSummary.value = 'Gagal memulai proses. Cek response error.'
    pushLog('error', 'Gagal memulai', 'Backend menolak request atau error.', e)
  } finally {
    busy.value = false
  }
}

const copyPayload = async () => {
  try {
    await navigator.clipboard.writeText(prettyPayload.value)
    pushLog('success', 'Payload disalin', 'JSON payload berhasil disalin ke clipboard.')
  } catch (e) {
    pushLog('error', 'Gagal menyalin', 'Browser menolak akses clipboard.', e)
  }
}

const downloadPayload = () => {
  try {
    const blob = new Blob([prettyPayload.value], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `mtbs-dummy-${Date.now()}.json`
    a.click()
    URL.revokeObjectURL(url)
    pushLog('success', 'Payload diunduh', 'File JSON payload berhasil diunduh.')
  } catch (e) {
    pushLog('error', 'Gagal mengunduh', 'Tidak bisa membuat file unduhan.', e)
  }
}

const clearLogs = () => {
  logs.value = []
}
</script>
