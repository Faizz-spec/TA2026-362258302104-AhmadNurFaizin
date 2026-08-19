<template>
  <AppLayout title="Rekap Balita Sakit">
    <div class="container-fluid py-3">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h4 class="mb-0">Rekap Balita Sakit</h4>
          <div class="text-muted small">Gabungan MTBM + MTBS (L / P / Total)</div>
        </div>

        <button class="btn btn-outline-secondary btn-sm" @click="fetchData" :disabled="loading">
          {{ loading ? 'Memuat...' : 'Refresh' }}
        </button>
      </div>

      <!-- FILTER -->
      <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">Tahun Dari</label>
            <input
              type="number"
              class="form-control"
              v-model.number="f.year_from"
              min="2000"
              max="2100"
            />
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">Tahun Sampai</label>
            <input
              type="number"
              class="form-control"
              v-model.number="f.year_to"
              min="2000"
              max="2100"
            />
          </div>

          <!-- Kalau kamu mau tetap pakai poli, biarin. Kalau nggak perlu, hapus blok ini -->
          <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">Poli</label>
            <input type="text" class="form-control" v-model="f.kdPoli" />
          </div>

          <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100" @click="applyFilter" :disabled="loading">
              Terapkan
            </button>
            <button class="btn btn-outline-danger w-100" @click="resetFilter" :disabled="loading">
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- HASIL -->
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small">Laki-laki</div>
            <div class="fs-3 fw-bold">{{ agg.laki_laki ?? 0 }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small">Perempuan</div>
            <div class="fs-3 fw-bold">{{ agg.perempuan ?? 0 }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="text-muted small">Total</div>
            <div class="fs-3 fw-bold">{{ agg.total ?? 0 }}</div>
          </div>
        </div>
      </div>

      <div class="text-muted small mt-3" v-if="debug">
        Periode: {{ debug.date_from }} s/d {{ debug.date_to }} | Gender col: {{ debug.gender_col_used || '-' }} | Poli: {{ debug.kdPoli }}
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'

const props = defineProps({
  filters: Object,
})

const loading = ref(false)
const agg = ref({ laki_laki: 0, perempuan: 0, total: 0 })
const debug = ref(null)

// default: tahun ini dan 1 tahun terakhir (silakan ubah)
const thisYear = new Date().getFullYear()

const f = ref({
  year_from: props.filters?.year_from ?? (thisYear - 1),
  year_to: props.filters?.year_to ?? thisYear,
  kdPoli: props.filters?.kdPoli ?? '003',
})

const fetchData = async () => {
  loading.value = true
  try {
    const res = await axios.get(route('laporan.mtbm.data'), { params: { ...f.value } })
    const payload = res.data?.data || {}
    agg.value = payload.aggregat || { laki_laki: 0, perempuan: 0, total: 0 }
    debug.value = payload.debug || null
  } catch (e) {
    console.error('AXIOS ERROR:', e)
    console.log('STATUS:', e?.response?.status)
    console.log('SERVER DATA:', e?.response?.data)
    alert('Gagal memuat rekap. Cek console.')
  } finally {
    loading.value = false
  }
}

const applyFilter = () => {
  fetchData()
}

const resetFilter = () => {
  f.value.year_from = thisYear - 1
  f.value.year_to = thisYear
  f.value.kdPoli = '003'
  fetchData()
}

onMounted(() => fetchData())
</script>
