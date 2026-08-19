<template>
  <AppLayout title="Laporan Sanitasi">
    <div class="container py-4" style="font-family: 'Segoe UI', sans-serif;">
      <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(135deg, #0d6efd, #20c997);">
          Filter Data Laporan Sanitasi
        </div>

        <div class="card-body">
          <form @submit.prevent="onSubmit">
            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.pusk_id">
                  <option value="">- Pilih -</option>
                  <option v-for="p in puskesmasOptions" :key="p.id" :value="p.id">{{ p.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Laporan -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Laporan</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.jenis">
                  <option value="">- Pilih -</option>
                  <option value="REGISTER">LAPORAN REGISTER SANITASI</option>
                  <option value="REKAP">LAPORAN SANITASI</option>
                  <option value="KASUS">LAPORAN KASUS</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Awal</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.awal" required />
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Akhir</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.akhir" required />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.unit_id">
                  <option value="">- Pilih -</option>
                  <option v-for="u in unitOptions" :key="u.id" :value="u.id">{{ u.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Sub Unit (terfilter oleh Unit) -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Sub Unit</label>
              <div class="col-md-6">
                <select class="form-select"
                        v-model="form.sub_unit_id"
                        :disabled="!form.unit_id">
                  <option value="">- Pilih -</option>
                  <option v-for="s in subUnitOptionsFiltered" :key="s.id" :value="s.id">
                    {{ s.nama }}
                  </option>
                </select>
                <small v-if="!form.unit_id" class="text-muted">Pilih Unit dulu.</small>
              </div>
            </div>

            <!-- Desa -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Desa</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.desa_id">
                  <option value="">- SEMUA -</option>
                  <option v-for="d in desaOptions" :key="d.id" :value="d.id">{{ d.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Action -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <button type="submit" class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded">
                  <i class="bi bi-printer me-1"></i> Tampilkan Data
                </button>

                <button type="button"
                        class="btn btn-secondary px-4 py-2 fw-semibold rounded"
                        @click="downloadExcel">
                  <i class="bi bi-download me-1"></i> Download Excel
                </button>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import '@/../css/laporan-css/form-styles.css'

const props = defineProps({
  puskesmasOptions: { type: Array, default: () => [] },
  unitOptions:      { type: Array, default: () => [] },
  subUnitOptions:   { type: Array, default: () => [] }, // berisi {id, unit_id, nama}
  desaOptions:      { type: Array, default: () => [] },
})

const form = reactive({
  pusk_id: '',
  jenis: '',
  awal: '',
  akhir: '',
  unit_id: '',
  sub_unit_id: '',
  desa_id: '',
})

// Filter sub unit sesuai unit terpilih (bandingkan sebagai string biar aman)
const subUnitOptionsFiltered = computed(() => {
  if (!form.unit_id) return []
  return props.subUnitOptions.filter(s => String(s.unit_id) === String(form.unit_id))
})

// Kosongkan sub_unit ketika unit berubah
watch(() => form.unit_id, () => {
  form.sub_unit_id = ''
})

// Tampilkan data
function onSubmit () {
  if (!form.jenis) { alert('Pilih jenis laporan.'); return }
  const params = { ...form }
  if (form.jenis === 'REGISTER') router.get(route('laporan.sanitasi.register'), params)
  if (form.jenis === 'REKAP')    router.get(route('laporan.sanitasi.laporan'), params)
  if (form.jenis === 'KASUS')    router.get(route('laporan.sanitasi.kasus'), params)
}

// Download Excel/CSV via query download=excel
function downloadExcel () {
  if (!form.jenis) { alert('Pilih jenis laporan.'); return }
  if (!form.awal || !form.akhir) { alert('Isi tanggal awal & akhir.'); return }

  const routeName =
    form.jenis === 'REGISTER' ? 'laporan.sanitasi.register' :
    form.jenis === 'REKAP'    ? 'laporan.sanitasi.laporan'  :
                                'laporan.sanitasi.kasus'

  const baseUrl = route(routeName)
  const qs = new URLSearchParams({
    pusk_id:     form.pusk_id || '',
    unit_id:     form.unit_id || '',
    sub_unit_id: form.sub_unit_id || '',
    desa_id:     form.desa_id || '',
    awal:        form.awal,
    akhir:       form.akhir,
    download:    'excel',
  }).toString()

  window.location.href = `${baseUrl}?${qs}`
}
</script>
