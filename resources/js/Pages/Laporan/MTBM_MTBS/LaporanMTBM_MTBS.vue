<template>
  <AppLayout title="Laporan MTBM + MTBS">
    <div class="container-fluid py-3 dashboard-wrap">
      <!-- HERO HEADER -->
      <div class="card hero-card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        <div class="hero-bg p-3 p-md-4">
          <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <div class="hero-icon d-flex align-items-center justify-content-center">
                <i class="bi bi-clipboard2-pulse fs-4"></i>
              </div>
              <div>
                <h4 class="mb-1 fw-bold text-white">Laporan MTBM + MTBS</h4>
                <div class="text-white-50 small">
                  Rekap gabungan subjektif, objektif, assessment/klasifikasi, planning, status pasien, gizi, imunisasi, diagnosa, dan alergi.
                </div>
              </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-light btn-sm fw-semibold" @click="fetchData" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-arrow-repeat me-2"></i>
                {{ loading ? 'Memuat...' : 'Refresh' }}
              </button>

              <button class="btn btn-success btn-sm fw-semibold" @click="exportAggregatXLSX" :disabled="loading">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                Export Aggregat
              </button>

              <button class="btn btn-primary btn-sm fw-semibold" @click="exportDetailXLSX" :disabled="loading">
                <i class="bi bi-download me-2"></i>
                Export Detail Lengkap
              </button>
            </div>
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-calendar3 me-1"></i>
              Periode: {{ f.date_from || '-' }} s/d {{ f.date_to || '-' }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-diagram-3 me-1"></i>
              Poli: {{ f.kdPoli || '-' }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-hospital me-1"></i>
              Puskesmas: {{ selectedPuskesmasName }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-database-check me-1"></i>
              Cakupan: {{ f.data_scope === 'module' ? 'Sudah ada MTBS/MTBM' : 'Semua kunjungan KIA' }}
            </span>
          </div>
        </div>
      </div>

      <!-- FILTERS -->
      <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
          <div class="fw-semibold d-flex align-items-center gap-2">
            <span class="filter-dot"></span>
            Filter
          </div>
          <div class="text-muted small">
            Data default hari ini. Tekan <kbd class="kbd">Enter</kbd> pada keyword untuk apply cepat.
          </div>
        </div>

        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Dari Tanggal</label>
            <input type="date" class="form-control form-control-sm" v-model="f.date_from" />
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Sampai Tanggal</label>
            <input type="date" class="form-control form-control-sm" v-model="f.date_to" />
          </div>

          <div class="col-12 col-md-1">
            <label class="form-label fw-semibold">Poli</label>
            <input type="text" class="form-control form-control-sm" v-model="f.kdPoli" placeholder="003" />
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Puskesmas</label>
            <select class="form-select form-select-sm" v-model="f.puskesmas_id" :disabled="puskesmasList.length === 0">
              <option value="">Semua Puskesmas</option>
              <option v-for="u in puskesmasList" :key="u.id" :value="String(u.id)">
                {{ u.nama }}
              </option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Cakupan Data</label>
            <select class="form-select form-select-sm" v-model="f.data_scope">
              <option value="all">Semua kunjungan KIA</option>
              <option value="module">Sudah ada data MTBS/MTBM</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Keyword</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light">
                <i class="bi bi-search"></i>
              </span>
              <input
                type="text"
                class="form-control"
                v-model="f.keyword"
                placeholder="Nama / MR / NIK"
                @keyup.enter="applyFilter"
              />
            </div>
          </div>

          <div class="col-12 col-md-1">
            <label class="form-label fw-semibold">Per Page</label>
            <select class="form-select form-select-sm" v-model.number="f.per_page">
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
          </div>

          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100 fw-semibold" @click="applyFilter" :disabled="loading">
              <i class="bi bi-funnel me-2"></i>Terapkan
            </button>
            <button class="btn btn-outline-danger btn-sm w-100 fw-semibold" @click="resetFilter" :disabled="loading">
              <i class="bi bi-x-circle me-2"></i>Reset
            </button>
          </div>
        </div>
      </div>

      <!-- SUMMARY -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-3">
          <div class="card stat-card stat-green border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small fw-semibold">Total Kunjungan</div>
            <div class="fs-3 fw-bold">{{ aggregat.total ?? 0 }}</div>
            <div class="small text-muted">
              Ada modul: {{ aggregat.module_total ?? 0 }} • Belum ada modul: {{ aggregat.without_module_total ?? 0 }}
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card stat-card stat-blue border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small fw-semibold">MTBS</div>
            <div class="fs-3 fw-bold">{{ aggregat.mtbs_total ?? 0 }}</div>
            <div class="small text-muted">Balita / assessment MTBS</div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card stat-card stat-teal border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small fw-semibold">MTBM</div>
            <div class="fs-3 fw-bold">{{ aggregat.mtbm_total ?? 0 }}</div>
            <div class="small text-muted">Bayi muda / assessment MTBM</div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card stat-card stat-pink border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small fw-semibold">L / P</div>
            <div class="fs-3 fw-bold">{{ aggregat.laki_laki ?? 0 }} / {{ aggregat.perempuan ?? 0 }}</div>
            <div class="small text-muted">Kolom gender: {{ aggregat.gender_col_used || '-' }}</div>
          </div>
        </div>
      </div>

      <!-- LIST -->
      <div class="card border-0 shadow-sm rounded-4 p-3 report-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <div class="fw-bold d-flex align-items-center gap-2">
              <i class="bi bi-table"></i>
              List Kunjungan
            </div>
            <div class="text-muted small">
              Semua kunjungan tetap terlihat. Badge MTBS/MTBM muncul jika tabel modulnya sudah memiliki data.
            </div>
          </div>
          <div class="text-muted small" v-if="rowsMeta">
            Page {{ rowsMeta.current_page }} • {{ rowsMeta.per_page }} / page
          </div>
        </div>

        <div class="table-responsive table-modern">
          <table class="table table-hover align-middle mb-0 report-table">
            <thead class="table-head-sticky">
              <tr>
                <th style="width: 105px;">Tanggal</th>
                <th style="min-width: 245px;">Pasien</th>
                <th style="width: 120px;">Layanan</th>
                <th style="min-width: 180px;">Unit</th>
                <th style="min-width: 210px;">Vital & Gizi</th>
                <th style="min-width: 330px;">Hasil Ringkas</th>
                <th style="min-width: 280px;">Status / Planning</th>
                <th style="width: 105px;">Served</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="r in rows" :key="r.kunjungan_id">
                <td class="text-nowrap">
                  <div class="fw-semibold">{{ formatDate(r.tglKunjungan || r.tglPelayanan) }}</div>
                  <div class="text-muted small">Pelayanan: {{ formatDate(r.tglPelayanan) }}</div>
                </td>

                <td>
                  <div class="fw-bold patient-name">{{ r.NAMA_LGKP || '-' }}</div>
                  <div class="patient-meta">
                    <span>MR: {{ r.NO_MR || '-' }}</span>
                    <span>NIK: {{ r.NIK || '-' }}</span>
                    <span>Umur: {{ umurText(r) }}</span>
                  </div>
                </td>

                <td>
                  <div class="d-flex flex-wrap gap-1">
                    <span v-if="isMTBS(r)" class="badge rounded-pill text-bg-primary">MTBS</span>
                    <span v-if="isMTBM(r)" class="badge rounded-pill text-bg-success">MTBM</span>
                    <span v-if="!isMTBS(r) && !isMTBM(r)" class="badge rounded-pill text-bg-secondary">-</span>
                  </div>
                </td>

                <td>
                  <div class="fw-semibold">{{ r.puskesmas_nama || '-' }}</div>
                  <div class="text-muted small">{{ r.nmPoli || r.kdPoli || '-' }}</div>
                </td>

                <td>
                  <div class="chip-wrap">
                    <span v-for="item in vitalItems(r)" :key="item.label" class="info-chip">
                      <b>{{ item.label }}</b> {{ item.value }}
                    </span>
                  </div>
                </td>

                <td>
                  <div class="summary-stack">
                    <div v-if="isMTBS(r)" class="summary-box mtbs-box">
                      <div class="summary-title text-primary">
                        <i class="bi bi-clipboard2-pulse me-1"></i> MTBS
                      </div>
                      <div class="summary-lines">
                        <div v-for="item in mtbsSummaryItems(r)" :key="item.label" class="summary-line">
                          <span class="summary-label">{{ item.label }}</span>
                          <span class="summary-value">{{ item.value }}</span>
                        </div>
                      </div>
                    </div>

                    <div v-if="isMTBM(r)" class="summary-box mtbm-box">
                      <div class="summary-title text-success">
                        <i class="bi bi-heart-pulse me-1"></i> MTBM
                      </div>
                      <div class="summary-lines">
                        <div v-for="item in mtbmSummaryItems(r)" :key="item.label" class="summary-line">
                          <span class="summary-label">{{ item.label }}</span>
                          <span class="summary-value">{{ item.value }}</span>
                        </div>
                      </div>
                    </div>

                    <div v-if="!isMTBS(r) && !isMTBM(r)" class="summary-box empty-box">
                      <div class="summary-title text-muted">
                        <i class="bi bi-exclamation-circle me-1"></i> Belum ada data modul
                      </div>
                      <div class="small text-muted">
                        Kunjungan ditemukan di loket/pelayanan, tetapi belum mempunyai baris pada tabel MTBS atau MTBM.
                      </div>
                    </div>
                  </div>
                </td>

                <td>
                  <div class="summary-lines">
                    <div v-for="item in statusPlanningItems(r)" :key="item.label" class="summary-line">
                      <span class="summary-label">{{ item.label }}</span>
                      <span class="summary-value">{{ item.value }}</span>
                    </div>
                  </div>
                </td>

                <td class="text-nowrap">
                  <span class="badge rounded-pill" :class="r.sudahDilayani ? 'bg-success' : 'bg-secondary'">
                    <i class="bi" :class="r.sudahDilayani ? 'bi-check2-circle' : 'bi-clock'"></i>
                    {{ r.sudahDilayani ? 'Sudah' : 'Belum' }}
                  </span>
                </td>
              </tr>

              <tr v-if="rows.length === 0">
                <td colspan="8" class="text-center text-muted py-4">
                  <i class="bi bi-inbox me-2"></i> Tidak ada data.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
          <div class="text-muted small">
            Menampilkan {{ rows.length }} data • MTBS: {{ rowsMTBS.length }} • MTBM: {{ rowsMTBM.length }} • Belum modul: {{ rowsWithoutModule.length }}
          </div>

          <div class="d-flex gap-2">
            <button
              class="btn btn-outline-secondary btn-sm fw-semibold"
              @click="prevPage"
              :disabled="loading || !rowsMeta || rowsMeta.current_page <= 1"
            >
              <i class="bi bi-chevron-left me-1"></i> Prev
            </button>
            <button
              class="btn btn-outline-secondary btn-sm fw-semibold"
              @click="nextPage"
              :disabled="loading || !rowsMeta || !rowsMeta.next_page_url"
            >
              Next <i class="bi bi-chevron-right ms-1"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'
import ExcelJS from 'exceljs'
import { saveAs } from 'file-saver'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  puskesmasList: { type: Array, default: () => [] },
})

const loading = ref(false)
const rows = ref([])
const rowsMeta = ref(null)
const aggregat = ref({ laki_laki: 0, perempuan: 0, total: 0, module_total: 0, without_module_total: 0 })
const debug = ref(null)
const joinedTables = ref([])

// Jangan pakai new Date().toISOString().slice(0, 10) untuk tanggal default.
// toISOString() memakai UTC dan bisa menghasilkan tanggal kemarin pada zona Asia/Jakarta.
const localDateYmd = (date = new Date()) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const todayYmd = localDateYmd()

const f = ref({
  date_from: props.filters?.date_from || todayYmd,
  date_to: props.filters?.date_to || todayYmd,
  kdPoli: props.filters?.kdPoli ?? '003',
  puskesmas_id: props.filters?.puskesmas_id ?? '',
  data_scope: props.filters?.data_scope === 'module' ? 'module' : 'all',
  keyword: props.filters?.keyword ?? '',
  per_page: Number(props.filters?.per_page ?? 10),
  page: 1,
})

const mtbsPrefixes = ['mtbs_s__', 'mtbs_o__', 'mtbs_a__', 'mtbs_pl__', 'mtbs_st__', 'mtbs_st2__', 'mtbs_gz__', 'mtbs_im__', 'mtbs_im_old__', 'mtbs_dx__', 'mtbs_al__']
const mtbmPrefixes = ['mtbm_s__', 'mtbm_o__', 'mtbm_a__', 'mtbm_pl__', 'mtbm_st__', 'mtbm_gz__', 'mtbm_im__', 'mtbm_dx__', 'mtbm_al__']

const formatDate = (d) => (d ? String(d).slice(0, 10) : '-')

const isFilled = (v) => v !== null && v !== undefined && v !== ''

const pickRaw = (obj, keys) => {
  for (const k of keys) {
    const v = obj?.[k]
    if (isFilled(v)) return v
  }
  return null
}

const pick = (obj, keys) => {
  const v = pickRaw(obj, keys)
  return isFilled(v) ? pretty(v) : '-'
}

const pretty = (v) => {
  if (!isFilled(v)) return '-'
  if (typeof v === 'boolean') return v ? 'Ya' : 'Tidak'
  if (typeof v === 'number') return String(v)
  if (Array.isArray(v)) return v.map(pretty).join(', ')
  if (typeof v === 'object') return JSON.stringify(v)

  const s = String(v)
  if (!s) return '-'
  try {
    const x = JSON.parse(s)
    if (Array.isArray(x)) return x.map(pretty).join(', ')
    if (typeof x === 'object' && x !== null) return JSON.stringify(x)
    return String(x)
  } catch {
    return s
  }
}

const textOrJson = (v) => pretty(v)

const truthyDb = (v) => v === true || v === 1 || v === '1' || String(v).toLowerCase() === 'ya' || String(v).toLowerCase() === 'true'

const badgeClass = (v) => {
  const s = String(v || '').toLowerCase()
  if (s.includes('merah') || s.includes('berat') || s.includes('bahaya')) return 'bg-danger'
  if (s.includes('kuning') || s.includes('sedang') || s.includes('ringan')) return 'bg-warning text-dark'
  if (s.includes('hijau') || s.includes('normal') || s.includes('baik')) return 'bg-success'
  return 'bg-secondary'
}

const hasAnyPrefix = (r, prefixes) => {
  const keys = Object.keys(r || {})
  return keys.some(k => prefixes.some(p => k.startsWith(p)) && isFilled(r[k]) && !k.endsWith('__id'))
}

const isMTBS = (r) => truthyDb(r?.has_mtbs_data) || hasAnyPrefix(r, mtbsPrefixes)
const isMTBM = (r) => truthyDb(r?.has_mtbm_data) || hasAnyPrefix(r, mtbmPrefixes)

const rowsMTBS = computed(() => rows.value.filter(isMTBS))
const rowsMTBM = computed(() => rows.value.filter(isMTBM))
const rowsWithoutModule = computed(() => rows.value.filter(r => !isMTBS(r) && !isMTBM(r)))

const selectedPuskesmasName = computed(() => {
  if (!f.value.puskesmas_id) return 'Semua'
  const found = props.puskesmasList.find(x => String(x.id) === String(f.value.puskesmas_id))
  return found?.nama ?? `ID ${f.value.puskesmas_id}`
})


const umurText = (r) => {
  const lahir = r?.TGL_LHR
  const kunj = r?.tglKunjungan || r?.tglPelayanan
  if (!lahir || !kunj) return '-'
  try {
    const a = new Date(lahir)
    const b = new Date(kunj)
    if (Number.isNaN(a.getTime()) || Number.isNaN(b.getTime())) return '-'
    let months = (b.getFullYear() - a.getFullYear()) * 12 + (b.getMonth() - a.getMonth())
    if (b.getDate() < a.getDate()) months -= 1
    if (months < 0) return '-'
    if (months < 1) {
      const days = Math.max(0, Math.floor((b - a) / 86400000))
      return `${days} hari`
    }
    const y = Math.floor(months / 12)
    const m = months % 12
    if (y > 0) return `${y} th ${m} bln`
    return `${m} bln`
  } catch {
    return '-'
  }
}

const collectPrefix = (r, prefix, options = {}) => {
  const exclude = options.exclude || ['id', 'kunjungan_id', 'pasien_id', 'created_at', 'updated_at', 'created_by', 'updated_by']
  const onlyStartsWith = options.onlyStartsWith || null
  const items = []

  for (const [key, value] of Object.entries(r || {})) {
    if (!key.startsWith(prefix) || !isFilled(value)) continue
    const field = key.replace(prefix, '')
    if (exclude.includes(field)) continue
    if (onlyStartsWith && !field.startsWith(onlyStartsWith)) continue
    items.push(`${field}: ${pretty(value)}`)
  }
  return items.join(' | ') || '-'
}

const collectKlasifikasi = (r, prefix) => {
  const items = []
  for (const [key, value] of Object.entries(r || {})) {
    if (!key.startsWith(prefix) || !isFilled(value)) continue
    const field = key.replace(prefix, '')
    if (field.startsWith('klas') || field.includes('klasifikasi') || field.includes('status_kegawatan')) {
      items.push(`${field}: ${pretty(value)}`)
    }
  }
  return items.join(' | ') || '-'
}

const mtbmKlasifikasiText = (r) => collectKlasifikasi(r, 'mtbm_a__')

const pickImunisasi = (r, module) => {
  if (module === 'mtbs') {
    return pick(r, [
      'mtbs_im__status_imunisasi',
      'mtbs_im__vaksin_tercatat',
      'mtbs_im__tindak_lanjut',
      'mtbs_im_old__status_imunisasi',
      'mtbs_im_old__jenis_imunisasi',
      'mtbs_im_old__nama_imunisasi',
      'mtbs_s__riwayat_imunisasi',
    ])
  }

  return pick(r, [
    'mtbm_im__status_imunisasi',
    'mtbm_im__jenis_imunisasi',
    'mtbm_im__nama_imunisasi',
    'mtbm_im__vaksin',
    'mtbm_s__riwayat_imunisasi',
  ])
}

const statusPulangText = (r) => pick(r, [
  'mtbs_st__status_pulang', 'mtbs_st2__status_pulang', 'mtbm_st__status_pulang',
  'mtbs_st__status', 'mtbs_st2__status', 'mtbm_st__status',
])

const planningText = (r) => {
  const mtbs = collectPrefix(r, 'mtbs_pl__')
  const mtbm = collectPrefix(r, 'mtbm_pl__')
  if (mtbs !== '-' && mtbm !== '-') return `MTBS: ${mtbs} || MTBM: ${mtbm}`
  if (mtbs !== '-') return mtbs
  if (mtbm !== '-') return mtbm
  return '-'
}

const diagnosaText = (r) => pick(r, [
  'mtbs_dx__nmDiag', 'mtbs_dx__namaDiagnosa', 'mtbs_dx__nama_diagnosa', 'mtbs_dx__diagnosa',
  'mtbm_dx__nmDiag', 'mtbm_dx__namaDiagnosa', 'mtbm_dx__nama_diagnosa', 'mtbm_dx__diagnosa',
])

const isDashText = (v) => {
  const s = pretty(v).trim()
  return !s || s === '-' || s.toLowerCase() === 'null' || s.toLowerCase() === 'undefined' || s === '[]' || s === '{}'
}

const humanize = (v) => {
  if (isDashText(v)) return '-'
  let s = pretty(v)
  s = s.replace(/[{}_\[\]"]/g, ' ')
    .replace(/:/g, ': ')
    .replace(/_/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
  if (!s) return '-'
  return s.charAt(0).toUpperCase() + s.slice(1)
}

const compact = (v, max = 130) => {
  const s = humanize(v)
  if (s === '-') return '-'
  return s.length > max ? `${s.slice(0, max).trim()}…` : s
}

const addItem = (arr, label, value, max = 130) => {
  const s = compact(value, max)
  if (s !== '-') arr.push({ label, value: s })
}

const vitalItems = (r) => {
  const items = []
  addItem(items, 'RR', pickRaw(r, ['mtbs_o__rr', 'mtbm_o__rr']), 30)
  addItem(items, 'Suhu', pickRaw(r, ['mtbs_o__suhu', 'mtbm_o__suhu']), 30)
  addItem(items, 'SpO2', pickRaw(r, ['mtbs_o__spo2', 'mtbm_o__spo2']), 30)
  addItem(items, 'BB', pickRaw(r, ['mtbs_o__bb', 'mtbm_o__bb', 'mtbs_gz__bb', 'mtbm_gz__bb']), 30)
  addItem(items, 'TB/PB', pickRaw(r, ['mtbs_o__tb', 'mtbm_o__tb_pb', 'mtbm_o__tb', 'mtbs_gz__tb', 'mtbm_gz__tb']), 30)
  addItem(items, 'LILA', pickRaw(r, ['mtbs_o__lila', 'mtbm_o__lila', 'mtbs_gz__lila', 'mtbm_gz__lila']), 30)
  addItem(items, 'LK', pickRaw(r, ['mtbs_o__lk', 'mtbm_o__lk']), 30)
  return items.length ? items : [{ label: 'Data', value: '-' }]
}

const mtbsSummaryItems = (r) => {
  const items = []
  addItem(items, 'Keluhan', pickRaw(r, ['mtbs_s__keluhan_utama']))
  addItem(items, 'Global', pickRaw(r, ['mtbs_a__klasifikasi_global']), 160)
  addItem(items, 'Status', pickRaw(r, ['mtbs_a__status_kegawatan']))
  addItem(items, 'Gizi', pickRaw(r, ['mtbs_a__gizi', 'mtbs_gz__klasifikasi']))
  addItem(items, 'Imunisasi', pickImunisasi(r, 'mtbs'))
  addItem(items, 'Anemia', pickRaw(r, ['mtbs_a__anemia']))
  return items.length ? items : [{ label: 'Data', value: '-' }]
}

const mtbmSummaryItems = (r) => {
  const items = []
  addItem(items, 'Keluhan', pickRaw(r, ['mtbm_s__keluhan_utama']))
  addItem(items, 'Global', pickRaw(r, ['mtbm_a__klasifikasi_global']))
  addItem(items, 'Status', pickRaw(r, ['mtbm_a__status_kegawatan', 'mtbm_o__status_ringkas']))
  addItem(items, 'Infeksi', pickRaw(r, ['mtbm_a__klas_infeksi']))
  addItem(items, 'Ikterus', pickRaw(r, ['mtbm_a__klas_ikterus']))
  addItem(items, 'Diare', pickRaw(r, ['mtbm_a__klas_diare']))
  addItem(items, 'Menyusu/BB', pickRaw(r, ['mtbm_a__klas_menyusu_bb']))
  addItem(items, 'Imunisasi', pickImunisasi(r, 'mtbm'))
  return items.length ? items : [{ label: 'Data', value: '-' }]
}

const parseJsonMaybe = (v) => {
  if (!isFilled(v)) return null
  if (typeof v === 'object') return v
  try { return JSON.parse(String(v)) } catch { return null }
}

const obatSummary = (v) => {
  if (!isFilled(v)) return '-'
  const parsed = parseJsonMaybe(v)
  const arr = Array.isArray(parsed) ? parsed : (parsed ? [parsed] : [])
  if (arr.length) {
    return arr.map((o) => {
      if (!o || typeof o !== 'object') return humanize(o)
      const nama = o.nama || o.nama_obat || o.obat || o.kode_obat || '-'
      const dosis = o.dosis ? ` (${o.dosis})` : ''
      const lama = o.lama ? ` ${o.lama} hari` : ''
      return `${nama}${dosis}${lama}`.trim()
    }).filter(x => x && x !== '-').join(', ') || '-'
  }
  return compact(v, 170)
}

const planningSummary = (r) => {
  const parts = []

  const mtbsTindakan = pickRaw(r, ['mtbs_pl__tindakan_segera'])
  const mtbsObat = pickRaw(r, ['mtbs_pl__pengobatan'])
  const mtbsEdukasi = pickRaw(r, ['mtbs_pl__edukasi', 'mtbs_pl__catatan_edukasi'])
  const mtbsUlang = pickRaw(r, ['mtbs_pl__kunjungan_ulang_hari'])

  if (!isDashText(mtbsTindakan)) parts.push(`Tindakan: ${compact(mtbsTindakan, 120)}`)
  if (!isDashText(mtbsObat)) parts.push(`Obat: ${compact(obatSummary(mtbsObat), 170)}`)
  if (!isDashText(mtbsEdukasi)) parts.push(`Edukasi: ${compact(mtbsEdukasi, 100)}`)
  if (!isDashText(mtbsUlang)) parts.push(`Ulang: ${mtbsUlang} hari`)

  const mtbmKeputusan = pickRaw(r, ['mtbm_pl__keputusan'])
  const mtbmCatatan = pickRaw(r, ['mtbm_pl__catatan_planning'])
  const mtbmKontrol = pickRaw(r, ['mtbm_pl__kontrol_ulang'])
  const mtbmRujuk = pickRaw(r, ['mtbm_pl__rujuk_alasan'])

  if (!isDashText(mtbmKeputusan)) parts.push(`Keputusan: ${compact(mtbmKeputusan, 100)}`)
  if (!isDashText(mtbmCatatan)) parts.push(`Catatan: ${compact(mtbmCatatan, 100)}`)
  if (!isDashText(mtbmKontrol)) parts.push(`Kontrol: ${compact(mtbmKontrol, 80)}`)
  if (!isDashText(mtbmRujuk)) parts.push(`Rujuk: ${compact(mtbmRujuk, 100)}`)

  return parts.join(' | ') || '-'
}

const statusPlanningItems = (r) => {
  const items = []
  addItem(items, 'Status pulang', statusPulangText(r), 120)
  addItem(items, 'Planning', planningSummary(r), 240)
  addItem(items, 'Diagnosa', diagnosaText(r), 150)
  return items.length ? items : [{ label: 'Data', value: '-' }]
}

const normalizeDateRange = () => {
  const today = localDateYmd()
  let from = String(f.value.date_from || '').trim()
  let to = String(f.value.date_to || '').trim()

  // Jika keduanya kosong, gunakan hari ini. Jika hanya satu terisi, jadikan satu hari.
  if (!from && !to) {
    from = today
    to = today
  } else if (!from) {
    from = to
  } else if (!to) {
    to = from
  }

  // input[type=date] menghasilkan YYYY-MM-DD, sehingga perbandingan string aman.
  if (from > to) [from, to] = [to, from]

  f.value.date_from = from
  f.value.date_to = to
}

const fetchData = async () => {
  normalizeDateRange()
  loading.value = true
  try {
    const res = await axios.get(route('laporan.mtbm_mtbs.data'), { params: { ...f.value } })
    const payload = res.data?.data || {}

    aggregat.value = payload.aggregat || { laki_laki: 0, perempuan: 0, total: 0 }
    debug.value = payload.debug || null
    joinedTables.value = payload.joined_tables || []

    // Sinkronkan tanggal yang benar-benar dipakai backend, termasuk bila rentang ditukar.
    if (payload.debug?.date_from) f.value.date_from = payload.debug.date_from
    if (payload.debug?.date_to) f.value.date_to = payload.debug.date_to
    if (payload.debug?.data_scope) f.value.data_scope = payload.debug.data_scope

    const paginator = payload.rows || {}
    rows.value = paginator.data || []
    rowsMeta.value = {
      current_page: paginator.current_page,
      per_page: paginator.per_page,
      next_page_url: paginator.next_page_url,
      prev_page_url: paginator.prev_page_url,
    }
  } catch (e) {
    console.error('AXIOS ERROR:', e)
    console.log('SERVER DATA:', e?.response?.data)

    if (e?.code === 'ECONNABORTED' || e?.message === 'Request aborted') {
      alert('Request laporan dibatalkan/terlalu berat. Pakai controller FAST terbaru ini; tampilan list sudah dibuat ringan, sedangkan export tetap lengkap.')
      return
    }

    alert(e?.response?.data?.error || 'Gagal memuat laporan. Cek console browser dan response Laravel.')
  } finally {
    loading.value = false
  }
}

const applyFilter = () => {
  f.value.page = 1
  fetchData()
}

const resetFilter = () => {
  f.value.keyword = ''
  f.value.kdPoli = '003'
  f.value.puskesmas_id = ''
  f.value.data_scope = 'all'
  f.value.per_page = 10
  f.value.page = 1
  const today = localDateYmd()
  f.value.date_from = today
  f.value.date_to = today
  fetchData()
}

const nextPage = () => {
  if (!rowsMeta.value?.next_page_url) return
  f.value.page = (rowsMeta.value.current_page || 1) + 1
  fetchData()
}

const prevPage = () => {
  if (!rowsMeta.value || rowsMeta.value.current_page <= 1) return
  f.value.page = (rowsMeta.value.current_page || 1) - 1
  fetchData()
}

const safeFile = (s) => String(s || 'all').replace(/[^0-9a-zA-Z_-]/g, '')
const excelSafe = (v) => {
  const s = pretty(v)
  return s.length > 32000 ? `${s.slice(0, 32000)}...` : s
}

const styleHeader = (ws) => {
  ws.getRow(1).font = { bold: true }
  ws.views = [{ state: 'frozen', ySplit: 1 }]
  if ((ws.columns || []).length > 0) {
    ws.autoFilter = {
      from: { row: 1, column: 1 },
      to: { row: 1, column: ws.columns.length },
    }
  }
}

const addRows = (ws, rowsData) => {
  rowsData.forEach(row => ws.addRow(row))
  ws.eachRow((row) => {
    row.eachCell((cell) => {
      cell.alignment = { vertical: 'top', wrapText: true }
    })
  })
}

const rekapRow = (r) => ({
  status_data_modul: isMTBS(r) || isMTBM(r) ? 'Ada data modul' : 'Belum ada data modul',
  jenis_layanan: [isMTBS(r) ? 'MTBS' : null, isMTBM(r) ? 'MTBM' : null].filter(Boolean).join(' + ') || '-',
  tanggal_pelayanan: formatDate(r.tglPelayanan),
  tanggal_kunjungan: formatDate(r.tglKunjungan),
  kunjungan_id: excelSafe(r.kunjungan_id),
  id_loket: excelSafe(r.idLoket),
  no_mr: excelSafe(r.NO_MR),
  nama: excelSafe(r.NAMA_LGKP),
  nik: excelSafe(r.NIK),
  jenis_kelamin: excelSafe(pickRaw(r, ['JENIS_KLMIN', 'JENIS_KELAMIN', 'JK'])),
  tanggal_lahir: formatDate(r.TGL_LHR),
  umur: umurText(r),
  poli: excelSafe(r.nmPoli || r.kdPoli),
  puskesmas: excelSafe(r.puskesmas_nama),
  alamat: excelSafe(r.alamat),
  rt: excelSafe(r.no_rt),
  rw: excelSafe(r.no_rw),
  kelurahan: excelSafe(r.nama_kel),
  kecamatan: excelSafe(r.nama_kec),
  kabupaten: excelSafe(r.nama_kab),
  provinsi: excelSafe(r.nama_prop),
  sudah_dilayani: r.sudahDilayani ? 'Sudah' : 'Belum',

  rr: excelSafe(pickRaw(r, ['mtbs_o__rr', 'mtbm_o__rr'])),
  suhu: excelSafe(pickRaw(r, ['mtbs_o__suhu', 'mtbm_o__suhu'])),
  spo2: excelSafe(pickRaw(r, ['mtbs_o__spo2', 'mtbm_o__spo2'])),
  bb: excelSafe(pickRaw(r, ['mtbs_o__bb', 'mtbm_o__bb', 'mtbs_gz__bb', 'mtbm_gz__bb'])),
  tb: excelSafe(pickRaw(r, ['mtbs_o__tb', 'mtbm_o__tb', 'mtbs_gz__tb', 'mtbm_gz__tb'])),
  lila: excelSafe(pickRaw(r, ['mtbs_o__lila', 'mtbm_o__lila', 'mtbs_gz__lila', 'mtbm_gz__lila'])),
  lk: excelSafe(pickRaw(r, ['mtbs_o__lk', 'mtbm_o__lk'])),

  mtbs_subjektif_lengkap: excelSafe(collectPrefix(r, 'mtbs_s__')),
  mtbs_objektif_lengkap: excelSafe(collectPrefix(r, 'mtbs_o__')),
  mtbs_assessment_klasifikasi_lengkap: excelSafe(collectKlasifikasi(r, 'mtbs_a__')),
  mtbs_assessment_semua: excelSafe(collectPrefix(r, 'mtbs_a__')),
  mtbs_planning_lengkap: excelSafe(collectPrefix(r, 'mtbs_pl__')),
  mtbs_status_pasien_lengkap: excelSafe(`${collectPrefix(r, 'mtbs_st__')} ${collectPrefix(r, 'mtbs_st2__')}`.trim()),
  mtbs_gizi_lengkap: excelSafe(collectPrefix(r, 'mtbs_gz__')),
  mtbs_imunisasi_lengkap: excelSafe(collectPrefix(r, 'mtbs_im__')),
  mtbs_diagnosa_medis_lengkap: excelSafe(collectPrefix(r, 'mtbs_dx__')),
  mtbs_alergi_lengkap: excelSafe(collectPrefix(r, 'mtbs_al__')),

  mtbm_subjektif_lengkap: excelSafe(collectPrefix(r, 'mtbm_s__')),
  mtbm_objektif_lengkap: excelSafe(collectPrefix(r, 'mtbm_o__')),
  mtbm_assessment_klasifikasi_lengkap: excelSafe(collectKlasifikasi(r, 'mtbm_a__')),
  mtbm_assessment_semua: excelSafe(collectPrefix(r, 'mtbm_a__')),
  mtbm_planning_lengkap: excelSafe(collectPrefix(r, 'mtbm_pl__')),
  mtbm_status_pasien_lengkap: excelSafe(collectPrefix(r, 'mtbm_st__')),
  mtbm_gizi_lengkap: excelSafe(collectPrefix(r, 'mtbm_gz__')),
  mtbm_imunisasi_lengkap: excelSafe(collectPrefix(r, 'mtbm_im__')),
  mtbm_diagnosa_medis_lengkap: excelSafe(collectPrefix(r, 'mtbm_dx__')),
  mtbm_alergi_lengkap: excelSafe(collectPrefix(r, 'mtbm_al__')),
})

const mtbsRingkasRow = (r) => ({
  tanggal: formatDate(r.tglPelayanan),
  no_mr: excelSafe(r.NO_MR),
  nama: excelSafe(r.NAMA_LGKP),
  nik: excelSafe(r.NIK),
  umur: umurText(r),
  puskesmas: excelSafe(r.puskesmas_nama),
  keluhan: excelSafe(textOrJson(pickRaw(r, ['mtbs_s__keluhan_utama']))),
  keluhan_lain: excelSafe(pickRaw(r, ['mtbs_s__keluhan_lain'])),
  rr: excelSafe(pickRaw(r, ['mtbs_o__rr'])),
  suhu: excelSafe(pickRaw(r, ['mtbs_o__suhu'])),
  spo2: excelSafe(pickRaw(r, ['mtbs_o__spo2'])),
  bb: excelSafe(pickRaw(r, ['mtbs_o__bb', 'mtbs_gz__bb'])),
  tb: excelSafe(pickRaw(r, ['mtbs_o__tb', 'mtbs_gz__tb'])),
  lila: excelSafe(pickRaw(r, ['mtbs_o__lila', 'mtbs_gz__lila'])),
  tanda_bahaya: excelSafe(textOrJson(pickRaw(r, ['mtbs_o__tanda_bahaya']))),
  status_kegawatan: excelSafe(pickRaw(r, ['mtbs_a__status_kegawatan'])),
  klasifikasi_global: excelSafe(textOrJson(pickRaw(r, ['mtbs_a__klasifikasi_global']))),
  klasifikasi_gizi: excelSafe(pickRaw(r, ['mtbs_a__gizi', 'mtbs_gz__klasifikasi'])),
  status_pulang: excelSafe(statusPulangText(r)),
  planning: excelSafe(collectPrefix(r, 'mtbs_pl__')),
  imunisasi: excelSafe(collectPrefix(r, 'mtbs_im__')),
  gizi: excelSafe(collectPrefix(r, 'mtbs_gz__')),
  diagnosa: excelSafe(collectPrefix(r, 'mtbs_dx__')),
})

const mtbmRingkasRow = (r) => ({
  tanggal: formatDate(r.tglPelayanan),
  no_mr: excelSafe(r.NO_MR),
  nama: excelSafe(r.NAMA_LGKP),
  nik: excelSafe(r.NIK),
  umur: umurText(r),
  puskesmas: excelSafe(r.puskesmas_nama),
  keluhan: excelSafe(textOrJson(pickRaw(r, ['mtbm_s__keluhan_utama']))),
  keluhan_lain: excelSafe(pickRaw(r, ['mtbm_s__keluhan_lain'])),
  rr: excelSafe(pickRaw(r, ['mtbm_o__rr'])),
  suhu: excelSafe(pickRaw(r, ['mtbm_o__suhu'])),
  spo2: excelSafe(pickRaw(r, ['mtbm_o__spo2'])),
  bb: excelSafe(pickRaw(r, ['mtbm_o__bb', 'mtbm_gz__bb'])),
  tb: excelSafe(pickRaw(r, ['mtbm_o__tb', 'mtbm_gz__tb'])),
  lila: excelSafe(pickRaw(r, ['mtbm_o__lila', 'mtbm_gz__lila'])),
  status_kegawatan: excelSafe(pickRaw(r, ['mtbm_a__status_kegawatan'])),
  global: excelSafe(pickRaw(r, ['mtbm_a__klasifikasi_global'])),
  klasifikasi_lengkap: excelSafe(collectKlasifikasi(r, 'mtbm_a__')),
  status_pulang: excelSafe(statusPulangText(r)),
  planning: excelSafe(collectPrefix(r, 'mtbm_pl__')),
  imunisasi: excelSafe(collectPrefix(r, 'mtbm_im__')),
  gizi: excelSafe(collectPrefix(r, 'mtbm_gz__')),
  diagnosa: excelSafe(collectPrefix(r, 'mtbm_dx__')),
})

const createSheetFromObjects = (wb, name, dataRows) => {
  const ws = wb.addWorksheet(name)
  const first = dataRows[0] || { info: 'Tidak ada data' }
  const keys = Object.keys(first)
  ws.columns = keys.map(k => ({
    header: k,
    key: k,
    width: Math.min(55, Math.max(12, k.length + 2)),
  }))
  styleHeader(ws)
  addRows(ws, dataRows.length ? dataRows : [first])
  return ws
}

const genderKey = (r) => {
  const raw = pickRaw(r, [
    'JENIS_KLMIN',
    'JENIS_KELAMIN',
    'JK',
    'jenis_kelamin',
    'mtbs_s__jenis_kelamin',
    'mtbm_s__jenis_kelamin',
  ])

  const ss = String(raw ?? '').trim().toUpperCase()
  if (['1', 'L', 'LAKI-LAKI', 'LAKI LAKI', 'LK', 'M', 'MALE'].includes(ss)) return 'L'
  if (['2', 'P', 'PEREMPUAN', 'PR', 'F', 'FEMALE'].includes(ss)) return 'P'
  return 'N'
}

const ageMonthsNumber = (r) => {
  const direct = pickRaw(r, [
    'umur_bulan',
    'mtbs_s__umur_bulan_total',
    'mtbm_s__umur_bulan_total',
    'mtbs_gz__umur_bulan',
    'mtbm_gz__umur_bulan',
  ])
  if (direct !== null && direct !== undefined && direct !== '' && !Number.isNaN(Number(direct))) {
    return Number(direct)
  }

  const tahun = pickRaw(r, ['mtbs_s__umur_tahun', 'mtbm_s__umur_tahun'])
  const bulan = pickRaw(r, ['mtbs_s__umur_bulan', 'mtbm_s__umur_bulan'])
  if (!Number.isNaN(Number(tahun)) || !Number.isNaN(Number(bulan))) {
    return (Number(tahun || 0) * 12) + Number(bulan || 0)
  }

  const lahir = r?.TGL_LHR
  const kunj = r?.tglKunjungan || r?.tglPelayanan
  if (!lahir || !kunj) return null

  const a = new Date(lahir)
  const b = new Date(kunj)
  if (Number.isNaN(a.getTime()) || Number.isNaN(b.getTime())) return null

  let months = (b.getFullYear() - a.getFullYear()) * 12 + (b.getMonth() - a.getMonth())
  if (b.getDate() < a.getDate()) months -= 1
  return months >= 0 ? months : null
}

const isBalitaSakitRow = (r) => {
  const months = ageMonthsNumber(r)
  if (months !== null) return months >= 2 && months < 60
  return isMTBS(r)
}

const isBayiMudaSakitRow = (r) => {
  const months = ageMonthsNumber(r)
  if (months !== null) return months >= 0 && months < 2
  return isMTBM(r)
}

const visitKey = (r, index = 0) => {
  return String(
    r?.kunjungan_id
    ?? r?.idpelayanan
    ?? r?.idPelayanan
    ?? r?.id_loket
    ?? r?.idLoket
    ?? `${r?.NO_MR || ''}|${r?.NIK || ''}|${r?.tglKunjungan || r?.tglPelayanan || ''}|${index}`
  )
}

const uniqueVisits = (list) => {
  const map = new Map()

  ;(list || []).forEach((r, index) => {
    const key = visitKey(r, index)
    if (!map.has(key)) map.set(key, r)
  })

  return Array.from(map.values())
}

const desaLabel = (r) => {
  const raw = pickRaw(r, [
    'nama_kel',
    'NAMA_KEL',
    'desa',
    'nama_desa',
    'kelurahan',
    'nama_kelurahan',
    'kelurahan_nama',
  ])

  const text = String(raw || 'TANPA DESA').trim()
  return text ? text.toUpperCase() : 'TANPA DESA'
}

const countLpn = (list) => {
  const out = { L: 0, P: 0, N: 0 }

  list.forEach((r) => {
    const g = genderKey(r)
    if (g === 'L') out.L += 1
    else if (g === 'P') out.P += 1
    out.N += 1
  })

  return out
}

const persenData = (atas, bawah) => {
  const a = Number(atas || 0)
  const b = Number(bawah || 0)
  if (!b) return '-'
  return Math.round((a / b) * 100)
}

const persenTotal = (atas, bawah) => {
  const a = Number(atas || 0)
  const b = Number(bawah || 0)
  if (!b) return '-'
  const val = Math.round((a / b) * 1000) / 10
  return Number.isInteger(val) ? val : val
}

const officialValue = (v) => {
  if (v === null || v === undefined || v === '' || Number(v) === 0) return '-'
  return v
}

const applyOfficialCellStyle = (cell, options = {}) => {
  cell.alignment = {
    horizontal: options.horizontal || 'center',
    vertical: 'middle',
    wrapText: true,
  }

  cell.border = {
    top: { style: 'thin' },
    left: { style: 'thin' },
    bottom: { style: 'thin' },
    right: { style: 'thin' },
  }

  cell.font = {
    ...(cell.font || {}),
    bold: !!options.bold,
    size: options.size || 11,
  }

  if (options.fill) {
    cell.fill = {
      type: 'pattern',
      pattern: 'solid',
      fgColor: { argb: options.fill },
    }
  }
}

const buildDesaOfficialRows = (rawRows, module) => {
  const unique = uniqueVisits(rawRows)
  const isTarget = module === 'mtbs' ? isMTBS : isMTBM
  const isSakit = module === 'mtbs' ? isBalitaSakitRow : isBayiMudaSakitRow

  const desaMap = new Map()

  unique.forEach((r) => {
    const desa = desaLabel(r)
    if (!desaMap.has(desa)) desaMap.set(desa, [])
    desaMap.get(desa).push(r)
  })

  const rowsDesa = []
  let no = 1

  Array.from(desaMap.entries())
    .sort(([a], [b]) => String(a).localeCompare(String(b)))
    .forEach(([desa, list]) => {
      const sakit = countLpn(list.filter(isSakit))
      const layanan = countLpn(list.filter(isTarget))

      if (sakit.N <= 0 && layanan.N <= 0) return

      rowsDesa.push({
        no,
        desa,
        sakitL: sakit.L,
        sakitP: sakit.P,
        sakitN: sakit.N,
        layananL: layanan.L,
        persenL: percentDisplay(layanan.L, sakit.L),
        layananP: layanan.P,
        persenP: percentDisplay(layanan.P, sakit.P),
        layananN: layanan.N,
        persenN: percentDisplay(layanan.N, sakit.N),
      })

      no += 1
    })

  return rowsDesa
}

const totalOfficialRows = (rowsDesa) => {
  const total = rowsDesa.reduce((acc, row) => {
    acc.sakitL += Number(row.sakitL || 0)
    acc.sakitP += Number(row.sakitP || 0)
    acc.sakitN += Number(row.sakitN || 0)
    acc.layananL += Number(row.layananL || 0)
    acc.layananP += Number(row.layananP || 0)
    acc.layananN += Number(row.layananN || 0)
    return acc
  }, { sakitL: 0, sakitP: 0, sakitN: 0, layananL: 0, layananP: 0, layananN: 0 })

  return {
    ...total,
    persenL: percentDisplay(total.layananL, total.sakitL, true),
    persenP: percentDisplay(total.layananP, total.sakitP, true),
    persenN: percentDisplay(total.layananN, total.sakitN, true),
  }
}

const bulanTahunLabel = () => {
  // Ambil bulan/tahun dari filter laporan. Kalau filter kosong, pakai tanggal hari ini
  // supaya FORMAT LAPORAN tetap terisi, tidak kosong.
  const d = f.value.date_from || f.value.date_to || localDateYmd()
  const date = new Date(d)

  if (Number.isNaN(date.getTime())) {
    const today = new Date()
    return {
      bulan: today.toLocaleString('id-ID', { month: 'long' }).toUpperCase(),
      tahun: String(today.getFullYear()),
    }
  }

  return {
    bulan: date.toLocaleString('id-ID', { month: 'long' }).toUpperCase(),
    tahun: String(date.getFullYear()),
  }
}

const writeMetaLaporan = (ws) => {
  const { bulan, tahun } = bulanTahunLabel()
  const namaPuskesmas = selectedPuskesmasName.value && selectedPuskesmasName.value !== 'Semua'
    ? selectedPuskesmasName.value
    : 'SEMUA PUSKESMAS'

  // Bagian atas persis template: label di kolom A, isi di kolom B.
  ws.getCell('A1').value = 'FORMAT LAPORAN'
  ws.getCell('A2').value = 'Puskesmas'
  ws.getCell('B2').value = namaPuskesmas
  ws.getCell('A3').value = 'Bulan'
  ws.getCell('B3').value = bulan
  ws.getCell('A4').value = 'Tahun'
  ws.getCell('B4').value = tahun

  // Biar tampil seperti formulir: label tebal, isi juga tebal, tapi tidak mengganggu tabel.
  for (let r = 1; r <= 4; r += 1) {
    ws.getCell(r, 1).font = { bold: true, size: 12 }
    ws.getCell(r, 2).font = { bold: true, size: 12 }
    ws.getCell(r, 1).alignment = { vertical: 'middle', horizontal: 'left' }
    ws.getCell(r, 2).alignment = { vertical: 'middle', horizontal: 'left' }
    ws.getRow(r).height = 22
  }

  ws.getRow(5).height = 20
  ws.getRow(6).height = 20
  ws.getRow(7).height = 20
}

const countDisplay = (v) => {
  const n = Number(v || 0)
  return n > 0 ? n : ''
}

const totalDisplay = (v) => {
  const n = Number(v || 0)
  return n > 0 ? n : '-'
}

const percentDisplay = (atas, bawah, total = false) => {
  const a = Number(atas || 0)
  const b = Number(bawah || 0)

  if (b > 0 && a <= 0) return '-'
  if (b <= 0) return '-'

  const raw = (a / b) * 100
  const val = total ? Math.round(raw * 10) / 10 : Math.round(raw)
  return Number.isInteger(val) ? val : val
}

const applyReportBorder = (cell) => {
  cell.border = {
    top: { style: 'thin' },
    left: { style: 'thin' },
    bottom: { style: 'thin' },
    right: { style: 'thin' },
  }
}

const applyHeaderPink = (cell) => {
  applyReportBorder(cell)
  cell.fill = {
    type: 'pattern',
    pattern: 'solid',
    fgColor: { argb: 'FFE6B8B7' },
  }
  cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true }
  cell.font = { bold: true, size: 11 }
}

const applyBodyCell = (cell, align = 'center') => {
  applyReportBorder(cell)
  cell.alignment = { horizontal: align, vertical: 'middle', wrapText: true }
  cell.font = { size: 11 }
}

const applyTotalCell = (cell) => {
  applyReportBorder(cell)
  cell.fill = {
    type: 'pattern',
    pattern: 'solid',
    fgColor: { argb: 'FF92D050' },
  }
  cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true }
  cell.font = { bold: true, size: 11 }
}

const writeOfficialAgregatSheet = (wb, module, rawRows) => {
  const title = module === 'mtbs' ? 'MTBS' : 'MTBM'
  const sakitTitle = module === 'mtbs' ? 'Jumlah Balita Sakit' : 'Jumlah Bayi Muda Sakit'
  const layananTitle = module === 'mtbs' ? 'Jumlah Balita di MTBS' : 'Jumlah Bayi Muda di MTBM'
  const startNo = module === 'mtbs' ? 66 : 75

  const ws = wb.addWorksheet(title)

  ws.columns = [
    { width: 6 },
    { width: 20 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
    { width: 12 },
  ]

  ws.pageSetup = {
    paperSize: 9,
    orientation: 'landscape',
    fitToPage: true,
    fitToWidth: 1,
    fitToHeight: 0,
    printArea: 'A1:K1',
    margins: {
      left: 0.25,
      right: 0.25,
      top: 0.5,
      bottom: 0.5,
      header: 0.2,
      footer: 0.2,
    },
  }

  ws.views = [{ state: 'frozen', ySplit: 12 }]

  writeMetaLaporan(ws)

  // Struktur persis seperti contoh:
  // row 8-10 : NO. / Desa / MTBS atau MTBM + header group
  // row 11   : nomor kolom 1 2 66 67 ...
  // row 12   : keterangan rumus persen
  const top = 8
  const groupRow = 9
  const labelRow = 10
  const numberRow = 11
  const formulaRow = 12

  ws.getRow(top).height = 22
  ws.getRow(groupRow).height = 22
  ws.getRow(labelRow).height = 36
  ws.getRow(numberRow).height = 22
  ws.getRow(formulaRow).height = 26

  // ✅ Jangan merge sampai row 11. Row 11 dipakai nomor kolom 1 dan 2.
  ws.mergeCells('A8:A10')
  ws.getCell('A8').value = 'NO.'

  ws.mergeCells('B8:B10')
  ws.getCell('B8').value = 'Desa'

  ws.mergeCells('C8:K8')
  ws.getCell('C8').value = title

  ws.mergeCells('C9:E9')
  ws.getCell('C9').value = sakitTitle

  ws.mergeCells('F9:K9')
  ws.getCell('F9').value = layananTitle

  ws.getCell('C10').value = 'L'
  ws.getCell('D10').value = 'P'
  ws.getCell('E10').value = 'N'
  ws.getCell('F10').value = 'L'
  ws.getCell('G10').value = '%'
  ws.getCell('H10').value = 'P'
  ws.getCell('I10').value = '%'
  ws.getCell('J10').value = 'N'
  ws.getCell('K10').value = '%'

  ws.getCell('A11').value = 1
  ws.getCell('B11').value = 2
  ws.getCell('C11').value = startNo
  ws.getCell('D11').value = startNo + 1
  ws.getCell('E11').value = startNo + 2
  ws.getCell('F11').value = startNo + 3
  ws.getCell('G11').value = startNo + 4
  ws.getCell('H11').value = startNo + 5
  ws.getCell('I11').value = startNo + 6
  ws.getCell('J11').value = startNo + 7
  ws.getCell('K11').value = startNo + 8

  ws.getCell('G12').value = `(${startNo + 3}/${startNo}*100)`
  ws.getCell('I12').value = `(${startNo + 5}/${startNo + 1}*100)`
  ws.getCell('K12').value = `(${startNo + 7}/${startNo + 2}*100)`

  for (let r = 8; r <= 12; r += 1) {
    for (let c = 1; c <= 11; c += 1) {
      applyHeaderPink(ws.getCell(r, c))
    }
  }

  const rowsDesa = buildDesaOfficialRows(rawRows, module)
  let rowIndex = 13

  rowsDesa.forEach((item) => {
    const row = ws.getRow(rowIndex)
    row.height = 22

    row.getCell(1).value = item.no
    row.getCell(2).value = item.desa
    row.getCell(3).value = countDisplay(item.sakitL)
    row.getCell(4).value = countDisplay(item.sakitP)
    row.getCell(5).value = totalDisplay(item.sakitN)
    row.getCell(6).value = countDisplay(item.layananL)
    row.getCell(7).value = item.persenL
    row.getCell(8).value = countDisplay(item.layananP)
    row.getCell(9).value = item.persenP
    row.getCell(10).value = countDisplay(item.layananN)
    row.getCell(11).value = item.persenN

    for (let c = 1; c <= 11; c += 1) {
      applyBodyCell(row.getCell(c), c === 2 ? 'left' : 'center')
    }

    rowIndex += 1
  })

  const total = totalOfficialRows(rowsDesa)
  const totalRow = ws.getRow(rowIndex)
  totalRow.height = 22

  ws.mergeCells(rowIndex, 1, rowIndex, 2)
  totalRow.getCell(1).value = 'TOTAL'
  totalRow.getCell(3).value = totalDisplay(total.sakitL)
  totalRow.getCell(4).value = totalDisplay(total.sakitP)
  totalRow.getCell(5).value = totalDisplay(total.sakitN)
  totalRow.getCell(6).value = totalDisplay(total.layananL)
  totalRow.getCell(7).value = total.persenL
  totalRow.getCell(8).value = totalDisplay(total.layananP)
  totalRow.getCell(9).value = total.persenP
  totalRow.getCell(10).value = totalDisplay(total.layananN)
  totalRow.getCell(11).value = total.persenN

  for (let c = 1; c <= 11; c += 1) {
    applyTotalCell(totalRow.getCell(c))
  }

  ws.pageSetup.printArea = `A1:K${rowIndex}`
  return ws
}

const exportAggregatXLSX = async () => {
  loading.value = true
  try {
    // Pakai export_detail supaya bisa dibentuk per DESA seperti format laporan resmi.
    const res = await axios.get(route('laporan.mtbm_mtbs.export_detail'), {
      params: { ...f.value, limit: 100000 },
    })

    const rawRows = res.data?.data?.rows || []

    if (!rawRows.length) {
      alert('Tidak ada data untuk diexport.')
      return
    }

    const wb = new ExcelJS.Workbook()
    wb.creator = 'SIMPUS'
    wb.created = new Date()

    writeOfficialAgregatSheet(wb, 'mtbs', rawRows)
    writeOfficialAgregatSheet(wb, 'mtbm', rawRows)

    const buf = await wb.xlsx.writeBuffer()
    const filename = `format_laporan_mtbm_mtbs_${safeFile(f.value.date_from)}_sd_${safeFile(f.value.date_to)}_${safeFile(selectedPuskesmasName.value)}.xlsx`
    saveAs(new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), filename)
  } catch (e) {
    console.error('EXPORT FORMAT LAPORAN FAILED:', e)
    console.log('SERVER DATA:', e?.response?.data)
    alert('Export format laporan gagal. Cek console browser.')
  } finally {
    loading.value = false
  }
}

const exportDetailXLSX = async () => {
  loading.value = true
  try {
    const res = await axios.get(route('laporan.mtbm_mtbs.export_detail'), { params: { ...f.value, limit: 100000 } })
    const rawRows = res.data?.data?.rows || []
    const info = res.data?.data?.info || {}

    if (!rawRows.length) {
      alert('Tidak ada data untuk diexport.')
      return
    }

    const wb = new ExcelJS.Workbook()

    createSheetFromObjects(wb, 'Rekap Lengkap', rawRows.map(rekapRow))
    createSheetFromObjects(wb, 'MTBS Ringkas', rawRows.filter(isMTBS).map(mtbsRingkasRow))
    createSheetFromObjects(wb, 'MTBM Ringkas', rawRows.filter(isMTBM).map(mtbmRingkasRow))

    const wsRaw = wb.addWorksheet('RAW Join Semua Kolom')
    const rawKeys = Array.from(new Set(rawRows.flatMap(r => Object.keys(r || {}))))
    wsRaw.columns = rawKeys.map(k => ({ header: k, key: k, width: Math.min(45, Math.max(12, k.length + 2)) }))
    styleHeader(wsRaw)
    rawRows.forEach((r) => {
      const obj = {}
      for (const k of rawKeys) obj[k] = excelSafe(r[k]) === '-' ? '' : excelSafe(r[k])
      wsRaw.addRow(obj)
    })

    const wsInfo = wb.addWorksheet('Info Export')
    wsInfo.columns = [
      { header: 'Item', key: 'item', width: 30 },
      { header: 'Nilai', key: 'nilai', width: 60 },
    ]
    styleHeader(wsInfo)
    addRows(wsInfo, [
      { item: 'Puskesmas', nilai: info.puskesmas_nama || selectedPuskesmasName.value },
      { item: 'Periode', nilai: `${info.date_from || '-'} s/d ${info.date_to || '-'}` },
      { item: 'Poli', nilai: info.kdPoli || '-' },
      { item: 'Cakupan Data', nilai: info.data_scope === 'module' ? 'Sudah ada MTBS/MTBM' : 'Semua kunjungan KIA' },
      { item: 'Limit', nilai: info.limit || '-' },
      { item: 'Jumlah Row Export', nilai: rawRows.length },
      { item: 'Tabel Join', nilai: (info.joined_tables || []).map(t => `${t.label} (${t.table} => ${t.prefix})`).join(' | ') },
    ])

    const buf = await wb.xlsx.writeBuffer()
    const filename = `detail_lengkap_mtbm_mtbs_${safeFile(f.value.date_from)}_sd_${safeFile(f.value.date_to)}_${safeFile(selectedPuskesmasName.value)}.xlsx`
    saveAs(new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), filename)
  } catch (e) {
    console.error('EXPORT DETAIL FAILED:', e)
    console.log('SERVER DATA:', e?.response?.data)
    alert('Export detail gagal. Cek console browser.')
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchData())
</script>

<style scoped>
.dashboard-wrap{
  background: radial-gradient(1000px 400px at 10% 0%, rgba(13,110,253,.08), transparent 60%),
              radial-gradient(900px 350px at 90% 10%, rgba(25,135,84,.08), transparent 55%),
              radial-gradient(800px 300px at 40% 100%, rgba(214,51,132,.07), transparent 55%);
  border-radius: 16px;
}

.hero-card{ border-radius: 18px; }
.hero-bg{
  background: linear-gradient(135deg, #0d6efd 0%, #6610f2 55%, #d63384 100%);
}
.hero-icon{
  width: 44px; height: 44px;
  border-radius: 14px;
  background: rgba(255,255,255,.18);
  color: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,.12);
}
.kbd{
  padding: .2rem .35rem;
  border: 1px solid rgba(0,0,0,.15);
  border-bottom-width: 2px;
  border-radius: .35rem;
  background: #fff;
  font-size: .75rem;
}
.filter-dot{
  width: 10px; height: 10px; border-radius: 999px;
  background: linear-gradient(135deg, #0d6efd, #20c997);
  display: inline-block;
}
.stat-card{
  position: relative;
  overflow: hidden;
  min-height: 118px;
}
.stat-card::after{
  content:"";
  position:absolute;
  inset:auto -30% -60% auto;
  width: 220px;
  height: 220px;
  border-radius: 999px;
  opacity: .14;
  transform: rotate(15deg);
}
.stat-blue{ border-left: 6px solid #0d6efd; }
.stat-blue::after{ background:#0d6efd; }
.stat-pink{ border-left: 6px solid #d63384; }
.stat-pink::after{ background:#d63384; }
.stat-green{ border-left: 6px solid #198754; }
.stat-green::after{ background:#198754; }
.stat-teal{ border-left: 6px solid #20c997; }
.stat-teal::after{ background:#20c997; }
.mini-card{
  min-height: 82px;
}
.table-modern{
  border-radius: 14px;
  overflow: auto;
  border: 1px solid rgba(0,0,0,.06);
  max-height: 70vh;
}
.table-head-sticky th{
  position: sticky;
  top: 0;
  z-index: 2;
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}
.table td, .table th{
  vertical-align: top;
}
.table-hover tbody tr:hover{
  background: rgba(13,110,253,.04);
}

.report-card{
  overflow: hidden;
}
.report-table{
  font-size: .88rem;
}
.report-table th{
  white-space: nowrap;
  font-size: .78rem;
  letter-spacing: .02em;
  text-transform: uppercase;
  color: #495057;
}
.report-table td{
  border-color: rgba(0,0,0,.06);
}
.patient-name{
  line-height: 1.25;
}
.patient-meta{
  display: flex;
  flex-direction: column;
  gap: 2px;
  color: #6c757d;
  font-size: .78rem;
  margin-top: 4px;
}
.chip-wrap{
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.info-chip{
  display: inline-flex;
  gap: 4px;
  align-items: center;
  padding: 4px 8px;
  border-radius: 999px;
  background: #f8f9fa;
  border: 1px solid rgba(0,0,0,.08);
  font-size: .78rem;
  white-space: nowrap;
}
.summary-stack{
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.summary-box{
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 12px;
  padding: 8px 10px;
  background: #fff;
}
.mtbs-box{
  border-left: 4px solid #0d6efd;
}
.mtbm-box{
  border-left: 4px solid #198754;
}
.summary-title{
  font-weight: 700;
  font-size: .8rem;
  margin-bottom: 5px;
}
.summary-lines{
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.summary-line{
  display: grid;
  grid-template-columns: 92px 1fr;
  gap: 8px;
  align-items: start;
}
.summary-label{
  color: #6c757d;
  font-size: .76rem;
  font-weight: 700;
}
.summary-value{
  font-size: .82rem;
  line-height: 1.35;
  word-break: break-word;
}

</style>
