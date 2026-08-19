<template>
  <AppLayout title="Laporan UGD">
    <div class="container py-4" style="font-family: 'Segoe UI', sans-serif;">

      <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(135deg, #0d6efd, #20c997); transition: 0.3s;">
          Filter Data Laporan UGD
        </div>

        <div class="card-body">
          <!-- 1 route GET (preview di halaman ini kalau pakai submit) -->
          <form @submit.prevent="submit">
            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.pusk_id">
                  <option :value="null">- Pilih -</option>
                  <option v-for="p in puskesmasOptions" :key="p.id" :value="p.id">
                    {{ p.nama }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Laporan (placeholder kalau nanti punya varian) -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Laporan</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.jenis">
                  <option value="">Register Rawat Jalan UGD</option>
                </select>
              </div>
            </div>

            <!-- Tgl Awal -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Awal</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.awal" />
              </div>
            </div>

            <!-- Tgl Akhir -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Akhir</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.akhir" />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-3">
                <select class="form-select" v-model="form.unit_id">
                  <option :value="null">- Pilih -</option>
                  <option v-for="u in unitOptions" :key="u.id" :value="u.id">
                    {{ u.nama }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Sub Unit -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Sub Unit</label>
              <div class="col-md-3">
                <select class="form-select" v-model="form.subunit_id" :disabled="!form.unit_id">
                  <option :value="null">- Pilih -</option>
                  <option v-for="s in filteredSubUnits" :key="s.id" :value="s.id">
                    {{ s.nama }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Tombol -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <!-- Buka halaman print di tab baru -->
                <button
                  type="button"
                  @click="openPrint"
                  class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded"
                >
                  <i class="bi bi-printer me-1"></i> Tampilkan Data
                </button>

                <!-- Download Excel (sementara JSON sesuai controller) -->
                <button
                  type="button"
                  @click="downloadExcel"
                  class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded"
                >
                  <i class="bi bi-download me-1"></i> Download Excel
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- OPSIONAL: preview hasil jika rows terisi -->
      <div v-if="rows.length" class="card">
        <div class="card-header fw-bold">Hasil</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Tanggal</th>
                  <th>No MR</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Umur</th>
                  <th>Sex</th>
                  <th>Alamat</th>
                  <th>Kesadaran</th>
                  <th>TD</th>
                  <th>RR/HR</th>
                  <th>Keluhan</th>
                  <th>Kategori</th>
                  <th>Kasus</th>
                  <th>Status Pulang</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(r, i) in rows" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ r.tanggal }}</td>
                  <td>{{ r.no_mr }}</td>
                  <td>{{ r.nik }}</td>
                  <td>{{ r.nama_pasien }}</td>
                  <td>{{ r.umur }}</td>
                  <td>{{ r.sex }}</td>
                  <td>{{ r.alamat }}</td>
                  <td>{{ r.kesadaran }}</td>
                  <td>{{ r.sistole }}/{{ r.diastole }}</td>
                  <td>{{ r.rr }}/{{ r.hr }}</td>
                  <td>{{ r.keluhan_utama }}</td>
                  <td>{{ r.kategori_bayar }}</td>
                  <td>{{ r.kasus }}</td>
                  <td>{{ r.status_pulang }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import '@/../css/laporan-css/form-styles.css'

// ===== props reaktif dari Inertia =====
const p = usePage()
const puskesmasOptions = computed(() => p.props.puskesmasOptions ?? [])
const unitOptions      = computed(() => p.props.unitOptions ?? [])
const subUnitOptions   = computed(() => p.props.subUnitOptions ?? [])
const rows             = computed(() => p.props.rows ?? [])
const initialFilters   = computed(() => p.props.filters ?? {})

// ===== state form (bootstrap dari filters supaya ke-restore) =====
const form = ref({
  pusk_id:     initialFilters.value.pusk_id ?? null,
  unit_id:     initialFilters.value.unit_id ?? null,
  subunit_id:  initialFilters.value.subunit_id ?? null,
  awal:        initialFilters.value.awal ?? null,
  akhir:       initialFilters.value.akhir ?? null,
  jenis:       initialFilters.value.jenis ?? 'register-ugd',
})

// ===== Sub Unit ter-filter oleh Unit =====
const filteredSubUnits = computed(() => {
  if (!form.value.unit_id) return []
  return subUnitOptions.value.filter(s => String(s.unit_id) === String(form.value.unit_id))
})

// reset subunit kalau unit diganti
watch(() => form.value.unit_id, () => { form.value.subunit_id = null })

// ===== helper: buat querystring =====
function qs (obj) {
  const sp = new URLSearchParams()
  Object.entries(obj || {}).forEach(([k, v]) => {
    if (v !== null && v !== undefined && v !== '') sp.set(k, v)
  })
  const s = sp.toString()
  return s ? `?${s}` : ''
}

// ===== Actions =====
// Preview di halaman ini (opsional)
function submit () {
  router.get('/laporan/ugd', form.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

// Buka halaman print di tab baru (tanpa Ziggy)
function openPrint () {
  const url = '/laporan/ugd' + qs({ ...form.value, view: 'print' })
  window.open(url, '_blank')
}

// Download Excel (sementara JSON sesuai controller)
function downloadExcel () {
  const url = '/laporan/ugd' + qs({ ...form.value, export: 'excel' })
  window.open(url, '_blank')
}
</script>

<style scoped>
.btn-gradient { background: linear-gradient(135deg, #0d6efd, #20c997); }
</style>
