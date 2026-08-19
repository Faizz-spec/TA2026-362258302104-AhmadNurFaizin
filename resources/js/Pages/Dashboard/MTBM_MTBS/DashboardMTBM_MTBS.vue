<template>

<div class="dashboard-wrap">
      <!-- HERO HEADER -->
      <div class="card hero-card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        <div class="hero-bg p-3 p-md-4">
          <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <div class="hero-icon d-flex align-items-center justify-content-center">
                <i class="bi bi-activity fs-4"></i>
              </div>
              <div>
                <h4 class="mb-1 fw-bold text-white">Dashboard MTBM + MTBS</h4>
                <div class="text-white-50 small">
                  Ringkasan cepat: KPI • Tren • Breakdown • Kasus Prioritas
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-light btn-sm fw-semibold" @click="fetchData" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-arrow-repeat me-2"></i>
                {{ loading ? 'Memuat...' : 'Refresh' }}
              </button>
            </div>
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-calendar3 me-1"></i>
              {{ f.date_from || '-' }} s/d {{ f.date_to || '-' }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-diagram-3 me-1"></i>
              Poli: {{ f.kdPoli || '-' }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-hospital me-1"></i>
              Pusk: {{ selectedPuskesmasName }}
            </span>
            <span class="badge rounded-pill text-bg-dark bg-opacity-25">
              <i class="bi bi-check2-square me-1"></i>
              Dilayani: {{ f.served }}
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
            Enter di keyword = apply cepat
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

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Poli</label>
            <input type="text" class="form-control form-control-sm" v-model="f.kdPoli" />
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">Puskesmas</label>
            <select
              v-model="f.puskId"
              class="form-select form-select-sm"
              :disabled="loading || puskesmasOptions.length === 0"
              @change="applyFilter"
            >
              <option value="">Semua Puskesmas</option>
              <option
                v-for="u in puskesmasOptions"
                :key="u.value"
                :value="u.value"
              >
                {{ u.label }}
              </option>
            </select>
            <small v-if="puskesmasOptions.length === 0" class="text-danger">
              Daftar puskesmas belum ditemukan dari tabel unit_profiles.
            </small>
          </div>

          <div class="col-12 col-md-1">
            <label class="form-label fw-semibold">Dilayani</label>
            <select class="form-select form-select-sm" v-model="f.served">
              <option value="all">Semua</option>
              <option value="served">Sudah</option>
              <option value="unserved">Belum</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label fw-semibold">Keyword</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input
                type="text"
                class="form-control"
                v-model="f.keyword"
                placeholder="Nama / MR / NIK"
                @keyup.enter="applyFilter"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-2 flex-wrap">
            <button class="btn btn-primary btn-sm fw-semibold" @click="applyFilter" :disabled="loading">
              <i class="bi bi-funnel me-2"></i>Terapkan
            </button>
            <button class="btn btn-outline-danger btn-sm fw-semibold" @click="resetFilter" :disabled="loading">
              <i class="bi bi-x-circle me-2"></i>Reset
            </button>
          </div>
        </div>

        <div class="text-muted small mt-2" v-if="debug">
          <i class="bi bi-bug me-1"></i>
          Periode: {{ debug.date_from }} s/d {{ debug.date_to }}
          | Poli: {{ debug.kdPoli }}
          | PuskId: {{ debug.puskId || '-' }}
          | Dilayani: {{ debug.served }}
          | Opsi Puskesmas: {{ debug.puskesmas_count ?? puskesmasOptions.length }}
        </div>
      </div>

      <!-- KPI -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-3">
          <div class="card stat-card stat-blue border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Total Kunjungan</div>
                <div class="fs-3 fw-bold">{{ kpi.total ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-bar-chart-line"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-indigo border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">MTBS Terisi</div>
                <div class="fs-3 fw-bold">{{ kpi.mtbs_filled ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-clipboard2-check"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-green border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">MTBM Terisi</div>
                <div class="fs-3 fw-bold">{{ kpi.mtbm_filled ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-clipboard2-pulse"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-red border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Belum Dilayani</div>
                <div class="fs-3 fw-bold">{{ kpi.unserved ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-sky border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Laki-laki</div>
                <div class="fs-3 fw-bold">{{ kpi.laki_laki ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-gender-male"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-pink border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Perempuan</div>
                <div class="fs-3 fw-bold">{{ kpi.perempuan ?? 0 }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-gender-female"></i></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card stat-card stat-amber border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Rata-rata Umur</div>
                <div class="fs-3 fw-bold">{{ kpi.avg_umur ?? '-' }}</div>
              </div>
              <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
          </div>
        </div>


      </div>

      <!-- CHARTS: TREN -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Tren Kunjungan per Hari (MTBS vs MTBM)</div>
              <span class="badge rounded-pill text-bg-light border">Line</span>
            </div>
            <div class="ratio ratio-16x9 chart-wrap">
              <canvas ref="chartTrendRef"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Tren Kegawatan per Hari (Stacked)</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-16x9 chart-wrap">
              <canvas ref="chartSevRef"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- BREAKDOWN / TOP -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top 10 Puskesmas</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartTopPuskRef"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top 10 Klasifikasi Global MTBS</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartTopMtbsRef"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top 10 Keluhan Utama (MTBM)</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartTopKeluhanRef"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- MTBM DETAIL TOP 3 -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top Klas Infeksi (MTBM)</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartMtbmInfeksiRef"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top Klas Diare (MTBM)</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartMtbmDiareRef"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Top Klas Menyusu/BB (MTBM)</div>
              <span class="badge rounded-pill text-bg-light border">Bar</span>
            </div>
            <div class="ratio ratio-4x3 chart-wrap">
              <canvas ref="chartMtbmMenyusuRef"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE PRIORITAS -->
      <div class="card border-0 shadow-sm rounded-4 p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <div class="fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-diamond text-danger"></i>
            Kasus Prioritas (MTBM merah / MTBS berat)
          </div>
          <div class="text-muted small">Max 50 data terbaru</div>
        </div>

        <div class="table-responsive table-modern">
          <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-head-sticky">
              <tr>
                <th>Tanggal</th>
                <th>MR</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Poli</th>
                <th>Puskesmas</th>
                <th>RR</th>
                <th>Suhu</th>
                <th>SpO2</th>
                <th>MTBM Global</th>
                <th>MTBS Status</th>
                <th>Sudah Dilayani</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="r in prioritas" :key="r.kunjungan_id">
                <td class="text-nowrap">{{ formatDate(r.tglPelayanan) }}</td>
                <td class="text-nowrap">{{ r.NO_MR }}</td>
                <td class="fw-semibold">{{ r.NAMA_LGKP }}</td>
                <td class="text-nowrap">{{ r.NIK }}</td>
                <td class="text-nowrap">{{ r.nmPoli ?? '-' }}</td>
                <td>{{ r.puskesmas ?? '-' }}</td>
                <td class="text-nowrap">{{ pickAny(r, ['mtbs_rr', 'mtbm_rr']) }}</td>
                <td class="text-nowrap">{{ pickAny(r, ['mtbs_suhu', 'mtbm_suhu']) }}</td>
                <td class="text-nowrap">{{ pickAny(r, ['mtbs_spo2', 'mtbm_spo2']) }}</td>
                <td class="text-nowrap">
                  <span class="badge" :class="badgeClass(r.mtbm_global)">
                    {{ r.mtbm_global ?? '-' }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-info text-dark">{{ r.mtbs_status ?? '-' }}</span>
                </td>
                <td class="text-nowrap">
                  <span class="badge" :class="r.sudahDilayani ? 'bg-success' : 'bg-secondary'">
                    <i class="bi" :class="r.sudahDilayani ? 'bi-check2-circle' : 'bi-clock'"></i>
                    {{ r.sudahDilayani ? 'Sudah' : 'Belum' }}
                  </span>
                </td>
              </tr>

              <tr v-if="prioritas.length === 0">
                <td colspan="12" class="text-center text-muted py-4">
                  <i class="bi bi-inbox me-2"></i> Tidak ada kasus prioritas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-muted small mt-2" v-if="debug">
          Collation: {{ debug.collation_forced }}
        </div>
      </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'

import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

const page = usePage()

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  puskesmas: { type: Array, default: () => [] },

  // ini nerima dari parent:
  // :start-date="filters.start_date"
  // :end-date="filters.end_date"
  startDate: { type: String, default: '' },
  endDate: { type: String, default: '' },
})

let componentAlive = true

const loading = ref(false)
const debug = ref(null)
const puskesmasFromApi = ref([])

const normalizePuskesmas = (items) => {
  if (!Array.isArray(items)) return []

  const unique = new Map()

  items.forEach((item) => {
    const rawValue = item?.value
      ?? item?.unit_id
      ?? item?.id

    const rawLabel = item?.label
      ?? item?.nama_unit
      ?? item?.nama

    if (
      rawValue === undefined
      || rawValue === null
      || String(rawValue).trim() === ''
      || rawLabel === undefined
      || rawLabel === null
      || String(rawLabel).trim() === ''
    ) {
      return
    }

    const value = String(rawValue).trim()

    unique.set(value, {
      value,
      label: String(rawLabel).trim(),
      unit_id: item?.unit_id ?? item?.id ?? rawValue,
      kode_puskesmas: item?.kode_puskesmas ?? null,
    })
  })

  return Array.from(unique.values()).sort((a, b) => (
    a.label.localeCompare(b.label, 'id')
  ))
}

const puskesmasOptions = computed(() => {
  const candidates = [
    ...puskesmasFromApi.value,
    ...(Array.isArray(props.puskesmas) ? props.puskesmas : []),
    ...(Array.isArray(page.props?.puskesmas) ? page.props.puskesmas : []),
  ]

  return normalizePuskesmas(candidates)
})

const kpi = ref({
  total: 0,
  mtbs_filled: 0,
  mtbm_filled: 0,
  unserved: 0,
  laki_laki: 0,
  perempuan: 0,
  avg_umur: null,
})

const trend = ref([])
const severity = ref([])
const top = ref({
  puskesmas: [],
  mtbs_klasifikasi_global: [],
  mtbm_infeksi: [],
  mtbm_diare: [],
  mtbm_menyusu_bb: [],
  keluhan_utama: [],
})

const prioritas = ref([])

const initialFilters = page.props?.filters || props.filters || {}

const f = ref({
  // Parent memakai start_date/end_date, API MTBS memakai date_from/date_to.
  date_from: props.startDate || initialFilters?.date_from || initialFilters?.start_date || '',
  date_to: props.endDate || initialFilters?.date_to || initialFilters?.end_date || '',
  kdPoli: String(initialFilters?.kdPoli ?? '003'),
  puskId: initialFilters?.puskId === null || initialFilters?.puskId === undefined
    ? ''
    : String(initialFilters.puskId),
  served: String(initialFilters?.served ?? 'all'),
  keyword: initialFilters?.keyword ?? '',
})

const selectedPuskesmasName = computed(() => {
  if (!f.value.puskId) return 'Semua Puskesmas'

  return puskesmasOptions.value.find(
    (item) => item.value === String(f.value.puskId),
  )?.label || `Unit ID ${f.value.puskId}`
})

const formatDate = (d) => (d ? String(d).slice(0, 10) : '-')

const pickAny = (obj, keys, fallback = '-') => {
  for (const k of keys) {
    const v = obj?.[k]
    if (v !== null && v !== undefined && v !== '') return v
  }
  return fallback
}

const badgeClass = (v) => {
  const s = String(v || '').toLowerCase()

  if (s === 'merah') return 'bg-danger'
  if (s === 'kuning') return 'bg-warning text-dark'
  if (s === 'hijau') return 'bg-success'

  return 'bg-secondary'
}

// ===== CHART refs & instances =====
const chartTrendRef = ref(null)
const chartSevRef = ref(null)
const chartTopPuskRef = ref(null)
const chartTopMtbsRef = ref(null)
const chartTopKeluhanRef = ref(null)
const chartMtbmInfeksiRef = ref(null)
const chartMtbmDiareRef = ref(null)
const chartMtbmMenyusuRef = ref(null)

let cTrend = null
let cSev = null
let cTopPusk = null
let cTopMtbs = null
let cTopKeluhan = null
let cInf = null
let cDia = null
let cMen = null

const chartRefs = [
  chartTrendRef,
  chartSevRef,
  chartTopPuskRef,
  chartTopMtbsRef,
  chartTopKeluhanRef,
  chartMtbmInfeksiRef,
  chartMtbmDiareRef,
  chartMtbmMenyusuRef,
]

const destroyCharts = () => {
  ;[cTrend, cSev, cTopPusk, cTopMtbs, cTopKeluhan, cInf, cDia, cMen].forEach((c) => {
    if (c) c.destroy()
  })

  cTrend = null
  cSev = null
  cTopPusk = null
  cTopMtbs = null
  cTopKeluhan = null
  cInf = null
  cDia = null
  cMen = null
}

const isCanvasReady = () => {
  return componentAlive && chartRefs.every((r) => !!r.value)
}

const createSafeChart = (canvasRef, config) => {
  if (!componentAlive) return null

  if (!canvasRef?.value) {
    console.warn('Canvas chart belum tersedia, chart dilewati sementara.')
    return null
  }

  return new Chart(canvasRef.value, config)
}

const baseOptions = () => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { labels: { boxWidth: 10, usePointStyle: true } },
    tooltip: { intersect: false, mode: 'index' },
  },
  scales: {
    y: { beginAtZero: true, ticks: { precision: 0 } },
  },
})

const buildCharts = async () => {
  await nextTick()

  if (!isCanvasReady()) {
    console.warn('Canvas chart MTBM/MTBS belum siap atau component sudah unmount.')
    return
  }

  destroyCharts()

  // ===== Trend line =====
  const labelsTrend = trend.value.map(x => x.tgl)
  const dataMtbs = trend.value.map(x => Number(x.mtbs || 0))
  const dataMtbm = trend.value.map(x => Number(x.mtbm || 0))

  cTrend = createSafeChart(chartTrendRef, {
    type: 'line',
    data: {
      labels: labelsTrend,
      datasets: [
        {
          label: 'MTBS',
          data: dataMtbs,
          tension: 0.25,
          borderColor: '#0d6efd',
          backgroundColor: 'rgba(13,110,253,.15)',
          pointRadius: 2,
          fill: true,
        },
        {
          label: 'MTBM',
          data: dataMtbm,
          tension: 0.25,
          borderColor: '#198754',
          backgroundColor: 'rgba(25,135,84,.15)',
          pointRadius: 2,
          fill: true,
        },
      ],
    },
    options: {
      ...baseOptions(),
    },
  })

  // ===== Severity stacked =====
  const labelsSev = severity.value.map(x => x.tgl)

  cSev = createSafeChart(chartSevRef, {
    type: 'bar',
    data: {
      labels: labelsSev,
      datasets: [
        {
          label: 'MTBM Merah',
          data: severity.value.map(x => Number(x.mtbm_merah || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(220,53,69,.65)',
        },
        {
          label: 'MTBM Kuning',
          data: severity.value.map(x => Number(x.mtbm_kuning || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(255,193,7,.70)',
        },
        {
          label: 'MTBM Hijau',
          data: severity.value.map(x => Number(x.mtbm_hijau || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(25,135,84,.65)',
        },
        {
          label: 'MTBS Berat',
          data: severity.value.map(x => Number(x.mtbs_berat || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(102,16,242,.65)',
        },
        {
          label: 'MTBS Sedang',
          data: severity.value.map(x => Number(x.mtbs_sedang || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(13,202,240,.60)',
        },
        {
          label: 'MTBS Ringan',
          data: severity.value.map(x => Number(x.mtbs_ringan || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(253,126,20,.65)',
        },
      ],
    },
    options: {
      ...baseOptions(),
      scales: {
        x: { stacked: true, ticks: { maxRotation: 0, autoSkip: true } },
        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } },
      },
    },
  })

  // ===== helper TOP BAR =====
  const makeTopBar = (canvasRef, labels, vals, labelName, color) => {
    return createSafeChart(canvasRef, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: labelName,
          data: vals,
          backgroundColor: color,
          borderRadius: 8,
        }],
      },
      options: {
        ...baseOptions(),
        plugins: {
          ...baseOptions().plugins,
          legend: { display: false },
        },
        scales: {
          x: { ticks: { autoSkip: true, maxRotation: 0 } },
          y: { beginAtZero: true, ticks: { precision: 0 } },
        },
      },
    })
  }

  cTopPusk = makeTopBar(
    chartTopPuskRef,
    (top.value.puskesmas || []).map(x => x.puskesmas),
    (top.value.puskesmas || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(13,110,253,.65)'
  )

  cTopMtbs = makeTopBar(
    chartTopMtbsRef,
    (top.value.mtbs_klasifikasi_global || []).map(x => x.label),
    (top.value.mtbs_klasifikasi_global || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(102,16,242,.60)'
  )

  cTopKeluhan = makeTopBar(
    chartTopKeluhanRef,
    (top.value.keluhan_utama || []).map(x => x.label),
    (top.value.keluhan_utama || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(214,51,132,.60)'
  )

  cInf = makeTopBar(
    chartMtbmInfeksiRef,
    (top.value.mtbm_infeksi || []).map(x => x.label),
    (top.value.mtbm_infeksi || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(25,135,84,.65)'
  )

  cDia = makeTopBar(
    chartMtbmDiareRef,
    (top.value.mtbm_diare || []).map(x => x.label),
    (top.value.mtbm_diare || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(255,193,7,.70)'
  )

  cMen = makeTopBar(
    chartMtbmMenyusuRef,
    (top.value.mtbm_menyusu_bb || []).map(x => x.label),
    (top.value.mtbm_menyusu_bb || []).map(x => Number(x.total || 0)),
    'Total',
    'rgba(13,202,240,.60)'
  )
}

let requestController = null
let requestNumber = 0

const fetchData = async () => {
  if (!f.value.date_from || !f.value.date_to) {
    console.warn('Tanggal dashboard MTBS/MTBM belum lengkap.')
    return
  }

  if (f.value.date_from > f.value.date_to) {
    alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir.')
    return
  }

  requestController?.abort()
  requestController = new AbortController()
  const currentRequest = ++requestNumber

  loading.value = true

  try {
    const res = await axios.get(route('dashboard.mtbm_mtbs.data'), {
      signal: requestController.signal,
      params: {
        ...f.value,
        puskId: f.value.puskId || '',
        kdPoli: String(f.value.kdPoli || '003'),
      },
    })

    const payload = res?.data?.data ?? res?.data ?? {}

    // Abaikan respons lama jika user mengganti filter dengan cepat.
    if (!componentAlive || currentRequest !== requestNumber) return

    if (Array.isArray(payload.puskesmas)) {
      puskesmasFromApi.value = payload.puskesmas
    }

    kpi.value = payload.kpi || {
      total: 0,
      mtbs_filled: 0,
      mtbm_filled: 0,
      unserved: 0,
      laki_laki: 0,
      perempuan: 0,
      avg_umur: null,
    }

    trend.value = payload.trend || []
    severity.value = payload.severity || []

    top.value = {
      puskesmas: payload.top?.puskesmas || [],
      mtbs_klasifikasi_global: payload.top?.mtbs_klasifikasi_global || [],
      mtbm_infeksi: payload.top?.mtbm_infeksi || [],
      mtbm_diare: payload.top?.mtbm_diare || [],
      mtbm_menyusu_bb: payload.top?.mtbm_menyusu_bb || [],
      keluhan_utama: payload.top?.keluhan_utama || [],
    }

    prioritas.value = payload.prioritas || []
    debug.value = payload.debug || null

    await nextTick()

    if (!componentAlive) return

    await buildCharts()
  } catch (e) {
    if (e?.code === 'ERR_CANCELED' || e?.name === 'CanceledError') return

    console.error('DASHBOARD ERROR:', e)
    console.log('SERVER DATA:', e?.response?.data ?? null)
    alert('Gagal memuat dashboard. Cek console.')
  } finally {
    if (componentAlive && currentRequest === requestNumber) {
      loading.value = false
    }
  }
}

const applyFilter = () => fetchData()

const resetFilter = () => {
  f.value.date_from = props.startDate || ''
  f.value.date_to = props.endDate || ''
  f.value.kdPoli = '003'
  f.value.puskId = ''
  f.value.served = 'all'
  f.value.keyword = ''

  fetchData()
}

watch(
  () => [props.startDate, props.endDate],
  ([newStart, newEnd], [oldStart, oldEnd]) => {
    if (!newStart || !newEnd) return
    if (newStart === oldStart && newEnd === oldEnd) return

    f.value.date_from = newStart
    f.value.date_to = newEnd

    fetchData()
  }
)

onMounted(() => {
  componentAlive = true
  fetchData()
})

onBeforeUnmount(() => {
  componentAlive = false
  requestController?.abort()
  destroyCharts()
})
</script>

<style scoped>
/* background lembut */
.dashboard-wrap{
  background:
    radial-gradient(1000px 400px at 10% 0%, rgba(13,110,253,.08), transparent 60%),
    radial-gradient(900px 350px at 90% 10%, rgba(25,135,84,.08), transparent 55%),
    radial-gradient(800px 300px at 40% 100%, rgba(214,51,132,.07), transparent 55%);
  border-radius: 16px;
}

/* HERO */
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
.filter-dot{
  width: 10px; height: 10px; border-radius: 999px;
  background: linear-gradient(135deg, #0d6efd, #20c997);
  display: inline-block;
}

/* KPI cards */
.stat-card{
  position: relative;
  overflow: hidden;
  border-left: 6px solid rgba(0,0,0,.08);
}
.stat-card::after{
  content:"";
  position:absolute;
  inset:auto -30% -60% auto;
  width: 220px;
  height: 220px;
  border-radius: 999px;
  opacity: .18;
  transform: rotate(15deg);
}
.stat-icon{
  width: 44px; height: 44px;
  border-radius: 14px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size: 1.25rem;
  color: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,.12);
}
.stat-blue{ border-left-color: #0d6efd; }
.stat-blue::after{ background:#0d6efd; }
.stat-blue .stat-icon{ background: linear-gradient(135deg,#0d6efd,#20c997); }

.stat-indigo{ border-left-color: #6610f2; }
.stat-indigo::after{ background:#6610f2; }
.stat-indigo .stat-icon{ background: linear-gradient(135deg,#6610f2,#0dcaf0); }

.stat-green{ border-left-color: #198754; }
.stat-green::after{ background:#198754; }
.stat-green .stat-icon{ background: linear-gradient(135deg,#198754,#0dcaf0); }

.stat-red{ border-left-color: #dc3545; }
.stat-red::after{ background:#dc3545; }
.stat-red .stat-icon{ background: linear-gradient(135deg,#dc3545,#fd7e14); }

.stat-sky{ border-left-color: #0dcaf0; }
.stat-sky::after{ background:#0dcaf0; }
.stat-sky .stat-icon{ background: linear-gradient(135deg,#0dcaf0,#0d6efd); }

.stat-pink{ border-left-color: #d63384; }
.stat-pink::after{ background:#d63384; }
.stat-pink .stat-icon{ background: linear-gradient(135deg,#d63384,#fd7e14); }

.stat-amber{ border-left-color: #ffc107; }
.stat-amber::after{ background:#ffc107; }
.stat-amber .stat-icon{ background: linear-gradient(135deg,#ffc107,#20c997); }

.stat-gray{ border-left-color: #6c757d; }
.stat-gray::after{ background:#6c757d; }
.stat-gray .stat-icon{ background: linear-gradient(135deg,#6c757d,#0d6efd); }

/* Chart wrap */
.chart-wrap{
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,.06);
  background: linear-gradient(180deg, rgba(13,110,253,.03), rgba(25,135,84,.02));
}

/* Table modern */
.table-modern{
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,.06);
}
.table-head-sticky th{
  position: sticky;
  top: 0;
  z-index: 1;
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}
.table-hover tbody tr:hover{
  background: rgba(13,110,253,.04);
}
</style>
