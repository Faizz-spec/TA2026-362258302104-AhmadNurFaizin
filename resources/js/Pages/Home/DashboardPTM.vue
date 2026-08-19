<template>
  <AppLayout title="Dashboard PTM">
    <div class="db">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="topbar-left">
          <div class="topbar-eyebrow">
            <span class="pulse-dot"></span>
            Penyakit Tidak Menular · Live
          </div>
          <div class="topbar-title-row">
            <div class="topbar-title">Dashboard PTM</div>
            <div class="nav-tabs">
              <Link :href="route('dashboard')" class="nav-tab nav-tab-kunjungan">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kunjungan
              </Link>
              <span class="nav-tab nav-tab-active">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                PTM
                <span class="nav-tab-badge">8 Penyakit</span>
              </span>
            </div>
          </div>
        </div>
        <div class="topbar-right">
          <div class="filter-row">
            <div class="date-group">
              <span class="date-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              </span>
              <input type="date" class="date-input" v-model="filters.start_date" />
              <span class="sep">→</span>
              <input type="date" class="date-input" v-model="filters.end_date" />
            </div>
            <button class="btn-apply" :disabled="!isRangeValid" @click="applyFilter">Tampilkan</button>
            <button class="btn-reset" @click="resetFilter">Reset</button>
          </div>
          <span v-if="validationMsg" class="filter-error">{{ validationMsg }}</span>
        </div>
      </div>

      <!-- PERIOD LABEL -->
      <div class="period-label">
        <span class="period-tag">{{ filters.start_date }}</span>
        <span class="period-divider">–</span>
        <span class="period-tag">{{ filters.end_date }}</span>
      </div>

      <!-- METRIC CARDS -->
      <div class="metrics">
        <div class="metric-card metric-total">
          <div class="metric-icon-wrap total-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">Total Kasus PTM</div>
            <div class="metric-value">{{ totalPtm.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">dalam periode ini</div>
          </div>
        </div>

        <div
          v-for="ptm in ptmList"
          :key="ptm.key"
          class="metric-card"
          :class="{ 'active-filter': activeFilter === ptm.key }"
          @click="toggleFilter(ptm.key)"
          style="cursor:pointer"
        >
          <div class="metric-icon-wrap" :style="{ background: ptm.bgIcon, color: ptm.color }">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" v-html="ptm.icon"></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">{{ ptm.label }}</div>
            <div class="metric-value" :style="{ color: ptm.color }">
              {{ (summary[ptm.key] ?? 0).toLocaleString('id-ID') }}
            </div>
            <div class="metric-sub">{{ pct(summary[ptm.key] ?? 0, totalPtm) }}% dari total</div>
          </div>
          <div class="metric-bar-track">
            <div class="metric-bar-fill" :style="{ width: pct(summary[ptm.key] ?? 0, totalPtm) + '%', background: ptm.color }"></div>
          </div>
          <div v-if="activeFilter === ptm.key" class="active-badge">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
        </div>
      </div>

      <!-- HINT -->
      <div class="hint-row">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        Klik kartu penyakit untuk menampilkan / menyembunyikan tren-nya pada grafik
      </div>

      <!-- LINE CHART -->
      <div class="section">
        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Tren Kasus PTM Per Hari</div>
              <div class="panel-sub">Distribusi harian per jenis penyakit tidak menular</div>
            </div>
            <div class="chart-legend">
              <div
                v-for="ptm in visiblePtm"
                :key="ptm.key"
                class="legend-pill"
                :style="{ '--c': ptm.color }"
              >
                <span class="legend-dot"></span>
                {{ ptm.label }}
              </div>
            </div>
          </div>
          <div class="chart-wrap">
            <canvas ref="lineRef"></canvas>
          </div>
        </div>
      </div>

      <!-- SUMMARY TABLE -->
      <div class="section">
        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Ringkasan PTM</div>
              <div class="panel-sub">Total kasus per jenis penyakit dalam periode ini</div>
            </div>
          </div>
          <div class="ptm-table">
            <div class="ptm-row ptm-head">
              <div class="ptm-col-rank">#</div>
              <div class="ptm-col-name">Penyakit</div>
              <div class="ptm-col-bar"></div>
              <div class="ptm-col-total">Total</div>
              <div class="ptm-col-pct">%</div>
            </div>
            <div
              v-for="(ptm, idx) in sortedPtm"
              :key="ptm.key"
              class="ptm-row"
              :class="{ 'ptm-row-top': idx < 3 }"
            >
              <div class="ptm-col-rank">
                <span class="rank-badge" :class="'rank-' + (idx + 1)">{{ idx + 1 }}</span>
              </div>
              <div class="ptm-col-name">
                <span class="ptm-dot" :style="{ background: ptm.color }"></span>
                {{ ptm.label }}
              </div>
              <div class="ptm-col-bar">
                <div class="tbar-track">
                  <div class="tbar-fill" :style="{ width: pct(summary[ptm.key] ?? 0, maxPtmVal) + '%', background: ptm.color }"></div>
                </div>
              </div>
              <div class="ptm-col-total">{{ (summary[ptm.key] ?? 0).toLocaleString('id-ID') }}</div>
              <div class="ptm-col-pct">
                <span class="pct-badge">{{ pct(summary[ptm.key] ?? 0, totalPtm) }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'

const page    = usePage()
const initial = page.props

// ── Filter ───────────────────────────────────────────────────────────
const today        = initial.serverNow ?? new Date().toISOString().slice(0, 10)
const defaultStart = initial.filters?.start_date ?? today
const defaultEnd   = initial.filters?.end_date   ?? today

const filters       = ref({ start_date: defaultStart, end_date: defaultEnd })
const validationMsg = ref('')

const isRangeValid = computed(() => {
  const { start_date: s, end_date: e } = filters.value
  return !!s && !!e && s <= e
})

// ── PTM config ───────────────────────────────────────────────────────
const ptmList = [
  {
    key: 'hipertensi',
    label: 'Hipertensi',
    color: '#E05C5C',
    bgIcon: 'rgba(224,92,92,0.12)',
    icon: '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'
  },
  {
    key: 'diabetes',
    label: 'Diabetes Mellitus',
    color: '#E89D2A',
    bgIcon: 'rgba(232,157,42,0.12)',
    icon: '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/>'
  },
  {
    key: 'obesitas',
    label: 'Obesitas',
    color: '#7C5CBF',
    bgIcon: 'rgba(124,92,191,0.12)',
    icon: '<circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>'
  },
  {
    key: 'kolesterol',
    label: 'Kolesterol',
    color: '#3B9E82',
    bgIcon: 'rgba(59,158,130,0.12)',
    icon: '<path d="M4.5 9.5a7.5 7.5 0 0 1 15 0c0 7-7.5 11-7.5 11S4.5 16.5 4.5 9.5z"/>'
  },
  {
    key: 'stroke',
    label: 'Stroke',
    color: '#D95FA0',
    bgIcon: 'rgba(217,95,160,0.12)',
    icon: '<path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v0A2.5 2.5 0 0 1 9.5 7H4"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v0"/><path d="M12 7v5"/><path d="M8 17h8"/><path d="M12 12l-4 5"/><path d="M12 12l4 5"/>'
  },
  {
    key: 'jantung',
    label: 'Jantung',
    color: '#E04040',
    bgIcon: 'rgba(224,64,64,0.12)',
    icon: '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>'
  },
  {
    key: 'kanker',
    label: 'Kanker',
    color: '#5872C0',
    bgIcon: 'rgba(88,114,192,0.12)',
    icon: '<circle cx="12" cy="12" r="2"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>'
  },
  {
    key: 'gagal_ginjal',
    label: 'Gagal Ginjal',
    color: '#4A9EC4',
    bgIcon: 'rgba(74,158,196,0.12)',
    icon: '<path d="M12 2C8 2 5 5.5 5 9c0 5.25 4 9 7 13 3-4 7-7.75 7-13 0-3.5-3-7-7-7z"/>'
  },
]

// ── Server data ───────────────────────────────────────────────────────
const perDayAll = ref(initial.perDayAll ?? [])   // array of { date, hipertensi, diabetes, ... }
const summary   = ref(initial.summary   ?? {})   // { hipertensi: N, diabetes: N, ... }

// ── Active filter (which lines to show) ───────────────────────────────
const activeFilter = ref(null)   // null = semua ditampilkan

function toggleFilter(key) {
  activeFilter.value = activeFilter.value === key ? null : key
  refreshLine()
}

// ── Computed ──────────────────────────────────────────────────────────
const totalPtm = computed(() =>
  ptmList.reduce((s, p) => s + (summary.value[p.key] ?? 0), 0)
)

const maxPtmVal = computed(() =>
  Math.max(1, ...ptmList.map(p => summary.value[p.key] ?? 0))
)

const sortedPtm = computed(() =>
  [...ptmList].sort((a, b) => (summary.value[b.key] ?? 0) - (summary.value[a.key] ?? 0))
)

const visiblePtm = computed(() =>
  activeFilter.value ? ptmList.filter(p => p.key === activeFilter.value) : ptmList
)

function pct(val, total) {
  if (!total) return '0,0'
  return ((val / total) * 100).toFixed(1).replace('.', ',')
}

// ── Filter actions ────────────────────────────────────────────────────
function applyFilter() {
  validationMsg.value = ''
  if (!isRangeValid.value) {
    validationMsg.value = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
    return
  }
  const { start_date: s, end_date: e } = filters.value
  router.get(route('ptm.dashboard'), { start_date: s, end_date: e }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: () => {
      const p = usePage().props
      if (p.filters?.start_date) filters.value.start_date = p.filters.start_date
      if (p.filters?.end_date)   filters.value.end_date   = p.filters.end_date
      perDayAll.value = p.perDayAll ?? []
      summary.value   = p.summary   ?? {}
      refreshLine()
    }
  })
}

function resetFilter() {
  filters.value.start_date = defaultStart
  filters.value.end_date   = defaultEnd
  activeFilter.value = null
  applyFilter()
}

// ── Chart ─────────────────────────────────────────────────────────────
const lineRef  = ref(null)
let ChartLib   = null
let lineChart  = null

function buildLineConfig() {
  const days   = perDayAll.value
  const labels = days.map(d => d.date)
  const shown  = activeFilter.value
    ? ptmList.filter(p => p.key === activeFilter.value)
    : ptmList

  const datasets = shown.map(ptm => ({
    label: ptm.label,
    data: days.map(d => d[ptm.key] ?? 0),
    borderColor: ptm.color,
    backgroundColor: ptm.color + '18',
    borderWidth: 2,
    pointRadius: days.length <= 31 ? 3 : 0,
    pointHoverRadius: 5,
    pointBackgroundColor: ptm.color,
    tension: 0.35,
    fill: shown.length === 1,
  }))

  const gridColor = 'rgba(0,0,0,0.05)'
  const tickColor = 'rgba(0,0,0,0.38)'
  const n         = days.length
  const maxTicks  = Math.min(14, Math.max(6, Math.floor(120 / Math.log2(Math.max(4, n)))))

  return {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15,23,42,0.92)',
          titleColor: '#e2e8f0',
          bodyColor: '#94a3b8',
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            footer: items => 'Total: ' + items.reduce((s, i) => s + i.parsed.y, 0)
          }
        }
      },
      scales: {
        x: {
          grid: { color: gridColor },
          border: { display: false },
          ticks: {
            color: tickColor,
            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
            autoSkip: true,
            maxTicksLimit: maxTicks,
            maxRotation: 0,
            callback(value, index) {
              const step = Math.max(1, Math.ceil(n / maxTicks))
              return (index % step === 0) ? (days[index]?.date ?? '') : ''
            }
          }
        },
        y: {
          beginAtZero: true,
          grace: '8%',
          grid: { color: gridColor },
          border: { display: false },
          ticks: {
            color: tickColor,
            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
            precision: 0
          }
        }
      },
      animation: { duration: 400, easing: 'easeOutQuart' }
    }
  }
}

function refreshLine() {
  if (!ChartLib || !lineRef.value) return
  if (lineChart) { lineChart.destroy(); lineChart = null }
  lineChart = new ChartLib(lineRef.value, buildLineConfig())
}

onMounted(async () => {
  ChartLib = (await import('chart.js/auto')).default
  refreshLine()
})

watch(() => perDayAll.value, () => refreshLine())
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

/* ── Base ── */
.db {
  font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
  color: #0f172a;
  background: #f5f6fa;
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

/* ── Nav tabs ── */
.topbar-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.nav-tabs {
  display: flex;
  align-items: center;
  gap: 4px;
  background: #f1f5f9;
  border-radius: 10px;
  padding: 3px;
}
.nav-tab {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12.5px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 8px;
  color: #64748b;
  text-decoration: none;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
  user-select: none;
}
.nav-tab:hover:not(.nav-tab-active) {
  background: rgba(255,255,255,0.7);
  color: #334155;
}
.nav-tab-active {
  background: #fff;
  color: #E05C5C;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  cursor: default;
}
.nav-tab-kunjungan:hover {
  background: #fff;
  color: #185FA5;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}
.nav-tab-badge {
  font-size: 10px;
  font-weight: 700;
  background: rgba(224,92,92,0.12);
  color: #E05C5C;
  padding: 1px 6px;
  border-radius: 20px;
  letter-spacing: .2px;
}

/* ── Topbar ── */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  padding: 1.25rem 1.75rem;
  background: #fff;
  border-bottom: 1px solid #e8eaf0;
  position: sticky;
  top: 0;
  z-index: 20;
}
.topbar-eyebrow {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .8px;
  text-transform: uppercase;
  color: #64748b;
  margin-bottom: 4px;
}
.pulse-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #f97316;
  box-shadow: 0 0 0 0 rgba(249,115,22,0.4);
  animation: pulse 2s infinite;
  flex-shrink: 0;
}
@keyframes pulse {
  0%   { box-shadow: 0 0 0 0 rgba(249,115,22,0.5); }
  70%  { box-shadow: 0 0 0 6px rgba(249,115,22,0); }
  100% { box-shadow: 0 0 0 0 rgba(249,115,22,0); }
}
.topbar-title {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.4px;
  color: #0f172a;
}
.topbar-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

/* ── Filter ── */
.filter-row {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.date-group {
  display: flex; align-items: center; gap: 6px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 12px;
}
.date-icon { color: #94a3b8; display: flex; align-items: center; }
.date-input {
  font-family: inherit; font-size: 13px; font-weight: 500;
  border: none; background: transparent; color: #334155; outline: none; padding: 0;
}
.date-input:focus { color: #185FA5; }
.sep { color: #94a3b8; font-size: 12px; font-weight: 600; }
.btn-apply {
  font-family: inherit; font-size: 13px; font-weight: 600;
  padding: 7px 18px; border-radius: 9px; border: none;
  background: #185FA5; color: #fff; cursor: pointer;
  transition: background .15s, transform .1s;
}
.btn-apply:not(:disabled):hover { background: #1a6fbc; transform: translateY(-1px); }
.btn-apply:not(:disabled):active { transform: translateY(0); }
.btn-apply:disabled { opacity: .4; cursor: not-allowed; }
.btn-reset {
  font-family: inherit; font-size: 13px; font-weight: 500;
  padding: 7px 14px; border-radius: 9px;
  border: 1px solid #e2e8f0; background: transparent;
  color: #64748b; cursor: pointer; transition: all .15s;
}
.btn-reset:hover { background: #f8fafc; border-color: #cbd5e1; }
.filter-error { font-size: 11.5px; color: #E05C5C; font-weight: 500; }

/* ── Period label ── */
.period-label {
  display: flex; align-items: center; gap: 6px;
  padding: 10px 1.75rem;
}
.period-tag {
  font-size: 12px; font-weight: 600; color: #185FA5;
  background: rgba(24,95,165,0.08); padding: 3px 10px; border-radius: 6px;
}
.period-divider { color: #94a3b8; font-size: 13px; }

/* ── Hint ── */
.hint-row {
  display: flex; align-items: center; gap: 5px;
  font-size: 11.5px; color: #94a3b8;
  padding: 0 1.75rem 10px;
}

/* ── Metric cards ── */
.metrics {
  display: grid;
  grid-template-columns: 1.3fr repeat(4, 1fr);
  gap: 10px;
  padding: 0 1.75rem 1rem;
}
@media (max-width: 1300px) { .metrics { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 900px)  { .metrics { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .metrics { grid-template-columns: 1fr 1fr; } }

/* Row 2 — cards 5–8 occupy cols 1–4 normally via auto-fill */
/* Force wrap: the 5th to 8th ptm cards auto-wrap onto next row */

.metric-card {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  position: relative;
  overflow: hidden;
  transition: box-shadow .2s, transform .2s, border-color .2s;
}
.metric-card:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,0.07);
  transform: translateY(-2px);
}
.metric-card.active-filter {
  border-color: currentColor;
  box-shadow: 0 0 0 2px currentColor;
}

/* Total card */
.metric-total {
  flex-direction: row;
  align-items: center;
  gap: 14px;
  background: linear-gradient(135deg, #0f2d5e 0%, #185FA5 100%);
  border-color: transparent;
  grid-row: span 2;
}
.metric-total::after {
  content: '';
  position: absolute; right: -24px; bottom: -24px;
  width: 110px; height: 110px;
  background: rgba(255,255,255,0.06);
  border-radius: 50%;
}
.metric-total .metric-label { color: rgba(255,255,255,0.7); }
.metric-total .metric-value { color: #fff; font-size: 34px; letter-spacing: -1.5px; }
.metric-total .metric-sub   { color: rgba(255,255,255,0.5); }

.metric-icon-wrap {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.total-icon { background: rgba(255,255,255,0.15); color: #fff; }

.metric-body { flex: 1; min-width: 0; }
.metric-label {
  font-size: 11px; font-weight: 600; text-transform: uppercase;
  letter-spacing: .6px; color: #64748b; margin-bottom: 3px;
}
.metric-value {
  font-size: 22px; font-weight: 700; letter-spacing: -0.8px;
  line-height: 1.1; color: #0f172a;
}
.metric-sub { font-size: 11px; color: #94a3b8; margin-top: 1px; }

.metric-bar-track {
  width: 100%; height: 3px; background: #f1f5f9;
  border-radius: 99px; overflow: hidden;
}
.metric-bar-fill {
  height: 100%; border-radius: 99px; transition: width .5s ease;
}

.active-badge {
  position: absolute; top: 10px; right: 10px;
  width: 18px; height: 18px; border-radius: 50%;
  background: #22c55e; color: #fff;
  display: flex; align-items: center; justify-content: center;
}

/* ── Section ── */
.section { padding: 0 1.75rem 1.25rem; }

/* ── Panel ── */
.panel {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 16px;
  overflow: hidden;
}
.panel-header {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 12px;
  flex-wrap: wrap;
  padding: 16px 20px 14px;
  border-bottom: 1px solid #f1f5f9;
}
.panel-title { font-size: 14px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px; }
.panel-sub   { font-size: 12px; color: #94a3b8; margin-top: 2px; }

/* ── Legend pills ── */
.chart-legend { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.legend-pill {
  display: flex; align-items: center; gap: 5px;
  font-size: 11.5px; color: #64748b; font-weight: 500;
  background: #f8fafc; border: 1px solid #e2e8f0;
  border-radius: 20px; padding: 3px 9px;
}
.legend-pill .legend-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--c); flex-shrink: 0;
}

/* ── Chart ── */
.chart-wrap {
  position: relative; width: 100%; height: 300px;
  padding: 12px 16px 8px;
}
.chart-wrap canvas {
  display: block; width: 100% !important; height: 100% !important;
}

/* ── PTM Table ── */
.ptm-table { padding: 6px 0 4px; }
.ptm-row {
  display: grid;
  grid-template-columns: 44px 180px 1fr 80px 70px;
  align-items: center;
  gap: 10px;
  padding: 9px 20px;
  transition: background .15s;
}
.ptm-row:not(.ptm-head):hover { background: #f8fafc; }
.ptm-head {
  font-size: 10.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .6px; color: #94a3b8;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 8px;
}

.ptm-col-rank  { display: flex; align-items: center; justify-content: center; }
.ptm-col-name  { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: #334155; }
.ptm-col-bar   { min-width: 0; }
.ptm-col-total { font-size: 13px; font-weight: 700; color: #0f172a; text-align: right; }
.ptm-col-pct   { text-align: right; }

.rank-badge {
  width: 26px; height: 26px; border-radius: 7px;
  background: #f1f5f9; display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; color: #94a3b8;
}
.rank-1 { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; }
.rank-2 { background: linear-gradient(135deg, #64748b, #94a3b8); color: #fff; }
.rank-3 { background: linear-gradient(135deg, #b45309, #d97706); color: #fff; }

.ptm-dot {
  width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0;
}

.tbar-track {
  height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden;
}
.tbar-fill {
  height: 100%; border-radius: 99px; transition: width .4s ease; opacity: .85;
}

.pct-badge {
  font-size: 11.5px; font-weight: 600; color: #64748b;
  background: #f1f5f9; padding: 2px 8px; border-radius: 6px;
}

@media (max-width: 768px) {
  .ptm-row {
    grid-template-columns: 36px 1fr 60px 56px;
  }
  .ptm-col-bar { display: none; }
}
</style>