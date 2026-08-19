<template>
  <AppLayout title="Home">
    <div class="db">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="topbar-left">
          <div class="topbar-eyebrow">
            <span class="pulse-dot"></span>
            Rawat Jalan · Live
          </div>
          <div class="topbar-title-row">
            <div class="topbar-title">Dashboard Kunjungan</div>
            <div class="nav-tabs">
              <span class="nav-tab nav-tab-active">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kunjungan
              </span>
              <Link :href="route('ptm.dashboard')" class="nav-tab nav-tab-ptm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                PTM
                <span class="nav-tab-badge">8 Penyakit</span>
              </Link>
            </div>
          </div>
        </div>
        <div class="topbar-right">
          <!-- FILTER -->
          <div class="filter-row">
            <div class="date-group">
              <span class="date-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              </span>
              <input type="date" class="date-input" v-model="filters.start_date" />
              <span class="sep">→</span>
              <input type="date" class="date-input" v-model="filters.end_date" />
            </div>
            <button class="btn-apply" :disabled="!isRangeValid" @click="applyFilter">
              Tampilkan
            </button>
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
        <div class="metric-card metric-main">
          <div class="metric-icon-wrap main-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">Total Kunjungan</div>
            <div class="metric-value">{{ totalVisit.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">dalam periode ini</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-wrap" style="background: rgba(55,138,221,0.12); color:#378ADD">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">Laki-laki</div>
            <div class="metric-value" style="color:#378ADD">{{ gender.male.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">{{ pct(gender.male, totalVisit) }}% dari total</div>
          </div>
          <div class="metric-bar-track">
            <div class="metric-bar-fill" :style="{ width: pct(gender.male, totalVisit) + '%', background: '#378ADD' }"></div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-wrap" style="background: rgba(212,83,126,0.12); color:#D4537E">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">Perempuan</div>
            <div class="metric-value" style="color:#D4537E">{{ gender.female.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">{{ pct(gender.female, totalVisit) }}% dari total</div>
          </div>
          <div class="metric-bar-track">
            <div class="metric-bar-fill" :style="{ width: pct(gender.female, totalVisit) + '%', background: '#D4537E' }"></div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-wrap" style="background: rgba(59,109,17,0.12); color:#3B6D11">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">BPJS</div>
            <div class="metric-value" style="color:#3B6D11">{{ payment.bpjs.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">{{ pct(payment.bpjs, totalVisit) }}% dari total</div>
          </div>
          <div class="metric-bar-track">
            <div class="metric-bar-fill" :style="{ width: pct(payment.bpjs, totalVisit) + '%', background: '#3B6D11' }"></div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-wrap" style="background: rgba(186,117,23,0.12); color:#BA7517">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14" stroke-linecap="round"/><circle cx="12" cy="12" r="9"/></svg>
          </div>
          <div class="metric-body">
            <div class="metric-label">Pasien Baru</div>
            <div class="metric-value" style="color:#BA7517">{{ visit.baru.toLocaleString('id-ID') }}</div>
            <div class="metric-sub">{{ pct(visit.baru, totalVisit) }}% dari total</div>
          </div>
          <div class="metric-bar-track">
            <div class="metric-bar-fill" :style="{ width: pct(visit.baru, totalVisit) + '%', background: '#BA7517' }"></div>
          </div>
        </div>
      </div>

      <!-- BAR CHART (full width) -->
      <div class="section">
        <div class="panel chart-panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Kunjungan Per Hari</div>
              <div class="panel-sub">Distribusi harian berdasarkan jenis kelamin</div>
            </div>
            <div class="chart-legend">
              <div class="legend-pill" style="--c:#378ADD">
                <span class="legend-dot"></span>
                Laki-laki
                <strong>{{ totalMaleRange }}</strong>
              </div>
              <div class="legend-pill" style="--c:#D4537E">
                <span class="legend-dot"></span>
                Perempuan
                <strong>{{ totalFemaleRange }}</strong>
              </div>
            </div>
          </div>
          <div class="chart-wrap">
            <canvas ref="barRef"></canvas>
          </div>
        </div>
      </div>

      <!-- DONUT CARDS (4-kolom horizontal) -->
      <div class="donuts-row">

        <div class="donut-card">
          <div class="donut-header">
            <div class="donut-title">Jenis Kelamin</div>
            <div class="donut-total">{{ (gender.male + gender.female).toLocaleString('id-ID') }}</div>
          </div>
          <div class="donut-body">
            <div class="donut-wrap">
              <canvas ref="donutGenderRef"></canvas>
            </div>
            <div class="donut-legend">
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#378ADD"></span>
                <span class="dleg-label">Laki-laki</span>
                <span class="dleg-val">{{ gender.male.toLocaleString('id-ID') }}</span>
              </div>
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#D4537E"></span>
                <span class="dleg-label">Perempuan</span>
                <span class="dleg-val">{{ gender.female.toLocaleString('id-ID') }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="donut-card">
          <div class="donut-header">
            <div class="donut-title">Pembiayaan</div>
            <div class="donut-total">{{ (payment.bpjs + payment.non_bpjs).toLocaleString('id-ID') }}</div>
          </div>
          <div class="donut-body">
            <div class="donut-wrap">
              <canvas ref="donutPaymentRef"></canvas>
            </div>
            <div class="donut-legend">
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#3B6D11"></span>
                <span class="dleg-label">BPJS</span>
                <span class="dleg-val">{{ payment.bpjs.toLocaleString('id-ID') }}</span>
              </div>
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#888780"></span>
                <span class="dleg-label">Non-BPJS</span>
                <span class="dleg-val">{{ payment.non_bpjs.toLocaleString('id-ID') }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="donut-card">
          <div class="donut-header">
            <div class="donut-title">Tipe Kunjungan</div>
            <div class="donut-total">{{ (visit.baru + visit.lama).toLocaleString('id-ID') }}</div>
          </div>
          <div class="donut-body">
            <div class="donut-wrap">
              <canvas ref="donutVisitRef"></canvas>
            </div>
            <div class="donut-legend">
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#185FA5"></span>
                <span class="dleg-label">Baru</span>
                <span class="dleg-val">{{ visit.baru.toLocaleString('id-ID') }}</span>
              </div>
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#5DCAA5"></span>
                <span class="dleg-label">Lama</span>
                <span class="dleg-val">{{ visit.lama.toLocaleString('id-ID') }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="donut-card">
          <div class="donut-header">
            <div class="donut-title">Status Rujukan</div>
            <div class="donut-total">{{ (referral.internal + referral.rujukan).toLocaleString('id-ID') }}</div>
          </div>
          <div class="donut-body">
            <div class="donut-wrap">
              <canvas ref="donutReferralRef"></canvas>
            </div>
            <div class="donut-legend">
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#534AB7"></span>
                <span class="dleg-label">Internal</span>
                <span class="dleg-val">{{ referral.internal.toLocaleString('id-ID') }}</span>
              </div>
              <div class="donut-leg-item">
                <span class="dleg-color" style="background:#EF9F27"></span>
                <span class="dleg-label">Rujukan</span>
                <span class="dleg-val">{{ referral.rujukan.toLocaleString('id-ID') }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- TOP 10 PENYAKIT -->
      <div class="section">
        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">10 Penyakit Terbesar</div>
              <div class="panel-sub">Berdasarkan jumlah kunjungan dalam periode ini</div>
            </div>
          </div>
          <div class="disease-list">
            <div v-for="(row, idx) in topDiseases" :key="idx" class="disease-row">
              <div class="dis-rank" :class="idx < 3 ? 'top-' + (idx+1) : ''">{{ idx + 1 }}</div>
              <div class="dis-code">{{ row.kode }}</div>
              <div class="dis-info">
                <div class="dis-name">{{ row.nama }}</div>
                <div class="dis-bar-track">
                  <div class="dis-bar-fill" :style="{ width: barWidth(row.total) + '%' }"></div>
                </div>
              </div>
              <div class="dis-total">{{ row.total.toLocaleString('id-ID') }}</div>
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

// ── Filter ──────────────────────────────────────────────────────────
const today        = initial.serverNow ?? new Date().toISOString().slice(0, 10)
const defaultStart = initial.filters?.start_date ?? today
const defaultEnd   = initial.filters?.end_date   ?? today

const filters       = ref({ start_date: defaultStart, end_date: defaultEnd })
const validationMsg = ref('')

const isRangeValid = computed(() => {
  const { start_date: s, end_date: e } = filters.value
  return !!s && !!e && s <= e
})

// ── Server data ──────────────────────────────────────────────────────
const perDayAll   = ref(initial.perDayAll   ?? [])
const gender      = ref(initial.gender      ?? { male: 0, female: 0 })
const payment     = ref(initial.payment     ?? { bpjs: 0, non_bpjs: 0 })
const visit       = ref(initial.visit       ?? { baru: 0, lama: 0 })
const referral    = ref(initial.referral    ?? { internal: 0, rujukan: 0 })
const topDiseases = ref(initial.topDiseases ?? [])

// ── Computed ─────────────────────────────────────────────────────────
const perDay           = computed(() => perDayAll.value)
const totalMaleRange   = computed(() => perDay.value.reduce((s, d) => s + (d.male   || 0), 0))
const totalFemaleRange = computed(() => perDay.value.reduce((s, d) => s + (d.female || 0), 0))
const totalVisit       = computed(() => gender.value.male + gender.value.female)

const maxDiseaseTotal = computed(() =>
  topDiseases.value.length ? Math.max(...topDiseases.value.map(r => r.total)) : 1
)

function pct(val, total) {
  if (!total) return '0,0'
  return ((val / total) * 100).toFixed(1).replace('.', ',')
}

function barWidth(total) {
  return Math.round((total / maxDiseaseTotal.value) * 100)
}

// ── Filter actions ───────────────────────────────────────────────────
function applyFilter() {
  validationMsg.value = ''
  if (!isRangeValid.value) {
    validationMsg.value = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
    return
  }
  const { start_date: s, end_date: e } = filters.value
  router.get(route('home.home'), { start_date: s, end_date: e }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: () => {
      const p = usePage().props
      if (p.filters?.start_date) filters.value.start_date = p.filters.start_date
      if (p.filters?.end_date)   filters.value.end_date   = p.filters.end_date
      perDayAll.value   = p.perDayAll   ?? []
      gender.value      = p.gender      ?? { male: 0, female: 0 }
      payment.value     = p.payment     ?? { bpjs: 0, non_bpjs: 0 }
      visit.value       = p.visit       ?? { baru: 0, lama: 0 }
      referral.value    = p.referral    ?? { internal: 0, rujukan: 0 }
      topDiseases.value = p.topDiseases ?? []
      refreshBar()
      updateDonuts()
    }
  })
}

function resetFilter() {
  filters.value.start_date = defaultStart
  filters.value.end_date   = defaultEnd
  applyFilter()
}

// ── Chart refs ───────────────────────────────────────────────────────
const barRef           = ref(null)
const donutGenderRef   = ref(null)
const donutPaymentRef  = ref(null)
const donutVisitRef    = ref(null)
const donutReferralRef = ref(null)

let ChartLib          = null
let barChart          = null
let donutGenderChart  = null
let donutPaymentChart = null
let donutVisitChart   = null
let donutReferralChart= null

// ── Bar chart ────────────────────────────────────────────────────────
function dynamicBarSizing(count) {
  const barPct  = Math.max(0.15, Math.min(0.85, 12 / Math.max(1, count)))
  const catPct  = Math.max(0.4,  Math.min(0.9,  18 / Math.max(1, count)))
  const maxTicks= Math.min(14, Math.max(6, Math.floor(120 / Math.log2(Math.max(4, count)))))
  return { barPct, catPct, maxTicks }
}

function buildBarConfig() {
  const n = perDay.value.length
  const { barPct, catPct, maxTicks } = dynamicBarSizing(n)
  const gridColor = 'rgba(0,0,0,0.05)'
  const tickColor = 'rgba(0,0,0,0.38)'

  return {
    type: 'bar',
    data: {
      labels: perDay.value.map(d => d.date),
      datasets: [
        {
          label: 'Laki-laki',
          data: perDay.value.map(d => d.male),
          backgroundColor: '#378ADD',
          borderWidth: 0,
          barPercentage: barPct,
          categoryPercentage: catPct,
          stack: 'v',
          borderRadius: 0
        },
        {
          label: 'Perempuan',
          data: perDay.value.map(d => d.female),
          backgroundColor: '#D4537E',
          borderWidth: 0,
          barPercentage: barPct,
          categoryPercentage: catPct,
          stack: 'v',
          borderRadius: { topLeft: 5, topRight: 5 }
        }
      ]
    },
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
            footer: (items) => 'Total: ' + items.reduce((s, i) => s + i.parsed.y, 0)
          }
        }
      },
      scales: {
        x: {
          stacked: true,
          grid: { color: gridColor },
          border: { display: false },
          ticks: {
            color: tickColor,
            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
            autoSkip: true,
            maxTicksLimit: maxTicks,
            maxRotation: 0,
            callback: function (value, index) {
              const step = Math.max(1, Math.ceil(n / maxTicks))
              return (index % step === 0) ? (perDay.value[index]?.date ?? '') : ''
            }
          }
        },
        y: {
          stacked: true,
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

function refreshBar() {
  if (!ChartLib || !barRef.value) return
  if (barChart) { barChart.destroy(); barChart = null }
  barChart = new ChartLib(barRef.value, buildBarConfig())
}

// ── Donut helpers ─────────────────────────────────────────────────────
function donutOpts() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '72%',
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: 'rgba(15,23,42,0.92)',
        titleColor: '#e2e8f0',
        bodyColor: '#94a3b8',
        padding: 10,
        cornerRadius: 8,
        callbacks: { label: c => ` ${c.label}: ${c.parsed.toLocaleString('id-ID')}` }
      }
    },
    animation: { duration: 400, easing: 'easeOutQuart' }
  }
}

function updateDonuts() {
  if (donutGenderChart) {
    donutGenderChart.data.datasets[0].data = [gender.value.male, gender.value.female]
    donutGenderChart.update()
  }
  if (donutPaymentChart) {
    donutPaymentChart.data.datasets[0].data = [payment.value.bpjs, payment.value.non_bpjs]
    donutPaymentChart.update()
  }
  if (donutVisitChart) {
    donutVisitChart.data.datasets[0].data = [visit.value.baru, visit.value.lama]
    donutVisitChart.update()
  }
  if (donutReferralChart) {
    donutReferralChart.data.datasets[0].data = [referral.value.internal, referral.value.rujukan]
    donutReferralChart.update()
  }
}

// ── Lifecycle ─────────────────────────────────────────────────────────
onMounted(async () => {
  ChartLib = (await import('chart.js/auto')).default

  refreshBar()

  donutGenderChart = new ChartLib(donutGenderRef.value, {
    type: 'doughnut',
    data: {
      labels: ['Laki-laki', 'Perempuan'],
      datasets: [{ data: [gender.value.male, gender.value.female], backgroundColor: ['#378ADD', '#D4537E'], borderWidth: 0, hoverOffset: 4 }]
    },
    options: donutOpts()
  })

  donutPaymentChart = new ChartLib(donutPaymentRef.value, {
    type: 'doughnut',
    data: {
      labels: ['BPJS', 'Non-BPJS'],
      datasets: [{ data: [payment.value.bpjs, payment.value.non_bpjs], backgroundColor: ['#3B6D11', '#888780'], borderWidth: 0, hoverOffset: 4 }]
    },
    options: donutOpts()
  })

  donutVisitChart = new ChartLib(donutVisitRef.value, {
    type: 'doughnut',
    data: {
      labels: ['Baru', 'Lama'],
      datasets: [{ data: [visit.value.baru, visit.value.lama], backgroundColor: ['#185FA5', '#5DCAA5'], borderWidth: 0, hoverOffset: 4 }]
    },
    options: donutOpts()
  })

  donutReferralChart = new ChartLib(donutReferralRef.value, {
    type: 'doughnut',
    data: {
      labels: ['Internal', 'Rujukan'],
      datasets: [{ data: [referral.value.internal, referral.value.rujukan], backgroundColor: ['#534AB7', '#EF9F27'], borderWidth: 0, hoverOffset: 4 }]
    },
    options: donutOpts()
  })
})

watch(perDay, () => refreshBar())
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

/* ── Root ── */
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
  color: #185FA5;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  cursor: default;
}
.nav-tab-ptm:hover {
  background: #fff;
  color: #E05C5C;
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
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 0 rgba(34,197,94,0.4);
  animation: pulse 2s infinite;
  flex-shrink: 0;
}
@keyframes pulse {
  0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
  70%  { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
  100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
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
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.date-group {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 12px;
}
.date-icon {
  color: #94a3b8;
  display: flex;
  align-items: center;
}
.date-input {
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  border: none;
  background: transparent;
  color: #334155;
  outline: none;
  padding: 0;
}
.date-input:focus { color: #185FA5; }
.sep {
  color: #94a3b8;
  font-size: 12px;
  font-weight: 600;
}
.btn-apply {
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  padding: 7px 18px;
  border-radius: 9px;
  border: none;
  background: #185FA5;
  color: #fff;
  cursor: pointer;
  transition: background .15s, opacity .15s, transform .1s;
  letter-spacing: .2px;
}
.btn-apply:not(:disabled):hover { background: #1a6fbc; transform: translateY(-1px); }
.btn-apply:not(:disabled):active { transform: translateY(0); }
.btn-apply:disabled { opacity: .4; cursor: not-allowed; }
.btn-reset {
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  padding: 7px 14px;
  border-radius: 9px;
  border: 1px solid #e2e8f0;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  transition: all .15s;
}
.btn-reset:hover { background: #f8fafc; border-color: #cbd5e1; }
.filter-error {
  font-size: 11.5px;
  color: #D4537E;
  font-weight: 500;
}

/* ── Period label ── */
.period-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 1.75rem;
}
.period-tag {
  font-size: 12px;
  font-weight: 600;
  color: #185FA5;
  background: rgba(24,95,165,0.08);
  padding: 3px 10px;
  border-radius: 6px;
}
.period-divider {
  color: #94a3b8;
  font-size: 13px;
}

/* ── Metric cards ── */
.metrics {
  display: grid;
  grid-template-columns: 1.35fr repeat(4, 1fr);
  gap: 12px;
  padding: 0 1.75rem 1.25rem;
}
@media (max-width: 1100px) {
  .metrics { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 700px) {
  .metrics { grid-template-columns: 1fr 1fr; }
}

.metric-card {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: box-shadow .2s, transform .2s;
  position: relative;
  overflow: hidden;
}
.metric-card:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.07);
  transform: translateY(-2px);
}
.metric-main {
  flex-direction: row;
  align-items: center;
  gap: 14px;
  background: linear-gradient(135deg, #185FA5 0%, #1a79d4 100%);
  border-color: transparent;
}
.metric-main::after {
  content: '';
  position: absolute;
  right: -20px;
  top: -20px;
  width: 100px;
  height: 100px;
  background: rgba(255,255,255,0.07);
  border-radius: 50%;
}
.metric-main .metric-label { color: rgba(255,255,255,0.75); }
.metric-main .metric-value { color: #fff; font-size: 32px; }
.metric-main .metric-sub   { color: rgba(255,255,255,0.6); }

.metric-icon-wrap {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.main-icon {
  background: rgba(255,255,255,0.18);
  color: #fff;
}

.metric-body { flex: 1; min-width: 0; }
.metric-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #64748b;
  margin-bottom: 4px;
}
.metric-value {
  font-size: 26px;
  font-weight: 700;
  letter-spacing: -1px;
  line-height: 1;
  color: #0f172a;
}
.metric-sub { font-size: 11px; color: #94a3b8; margin-top: 3px; }

.metric-bar-track {
  width: 100%;
  height: 3px;
  background: #f1f5f9;
  border-radius: 99px;
  overflow: hidden;
}
.metric-bar-fill {
  height: 100%;
  border-radius: 99px;
  transition: width .5s ease;
}

/* ── Section wrapper ── */
.section {
  padding: 0 1.75rem 1.25rem;
}

/* ── Donut row (4-kolom horizontal) ── */
.donuts-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  padding: 0 1.75rem 1.25rem;
}
@media (max-width: 1100px) {
  .donuts-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
  .donuts-row { grid-template-columns: 1fr; }
}

/* ── Panel ── */
.panel {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 16px;
  overflow: hidden;
}
.panel-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  padding: 16px 20px 14px;
  border-bottom: 1px solid #f1f5f9;
}
.panel-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.2px;
}
.panel-sub {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}

/* ── Chart ── */
.chart-panel { height: 100%; }
.chart-wrap {
  position: relative;
  width: 100%;
  height: 260px;
  padding: 12px 16px 8px;
}
.chart-wrap canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}
.chart-legend {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}
.legend-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 4px 10px;
}
.legend-pill .legend-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--c);
  flex-shrink: 0;
}
.legend-pill strong {
  font-weight: 700;
  color: #334155;
  margin-left: 2px;
}



.donut-card {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  padding: 14px 16px;
  transition: box-shadow .2s;
}
.donut-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.donut-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 10px;
}
.donut-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #64748b;
}
.donut-total {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}
.donut-body {
  display: flex;
  align-items: center;
  gap: 12px;
}
.donut-wrap {
  position: relative;
  width: 80px;
  height: 80px;
  flex-shrink: 0;
}
.donut-wrap canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}
.donut-legend {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.donut-leg-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
}
.dleg-color {
  width: 8px;
  height: 8px;
  border-radius: 3px;
  flex-shrink: 0;
}
.dleg-label {
  flex: 1;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dleg-val {
  font-weight: 700;
  color: #0f172a;
  font-size: 12px;
}



/* ── Disease list ── */
.disease-list { padding: 8px 0; }
.disease-row {
  display: grid;
  grid-template-columns: 32px 68px 1fr 64px;
  align-items: center;
  gap: 10px;
  padding: 9px 20px;
  transition: background .15s;
}
.disease-row:hover { background: #f8fafc; }
.dis-rank {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
}
.top-1 { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; }
.top-2 { background: linear-gradient(135deg, #64748b, #94a3b8); color: #fff; }
.top-3 { background: linear-gradient(135deg, #b45309, #d97706); color: #fff; }

.dis-code {
  font-size: 11px;
  font-family: 'Courier New', monospace;
  font-weight: 700;
  background: #eff6ff;
  color: #185FA5;
  padding: 3px 8px;
  border-radius: 6px;
  text-align: center;
  white-space: nowrap;
}
.dis-info { min-width: 0; }
.dis-name {
  font-size: 13px;
  font-weight: 500;
  color: #334155;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dis-bar-track {
  height: 3px;
  background: #f1f5f9;
  border-radius: 99px;
  overflow: hidden;
  margin-top: 5px;
}
.dis-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #185FA5, #378ADD);
  border-radius: 99px;
  transition: width .4s ease;
}
.dis-total {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  text-align: right;
}
</style>