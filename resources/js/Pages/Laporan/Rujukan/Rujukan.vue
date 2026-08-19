<template>
  <AppLayout title="Data Pasien Rujukan">
    <div class="container py-4" style="font-family:'Segoe UI',sans-serif;">
      <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold text-white" style="background:linear-gradient(135deg,#0d6efd,#20c997);">
          Data Pasien Rujukan
        </div>

        <div class="card-body">
          <form @submit.prevent="submit">
            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select v-model="form.pusk_id" class="form-select">
                  <option :value="null">- Semua -</option>
                  <option v-for="p in puskesmasOptions" :key="p.value" :value="p.value">{{ p.label }}</option>
                </select>
              </div>
            </div>

            <!-- Tanggal Rujukan -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tanggal Rujukan</label>
              <div class="col-md-6">
                <input v-model="form.tgl" type="date" class="form-control" />
              </div>
            </div>

            <!-- Jenis Kepesertaan -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Jenis Kepesertaan</label>
              <div class="col-md-6">
                <select v-model="form.kepesertaan" class="form-select">
                  <option :value="null">- Semua -</option>
                  <option value="UMUM">UMUM</option>
                  <option value="BPJS">BPJS</option>
                </select>
              </div>
            </div>

            <!-- Unit (RS Tujuan) -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-6">
                <select v-model="form.unit" class="form-select">
                  <option :value="null">- Semua -</option>
                  <option v-for="u in unitOptions" :key="u.value" :value="u.value">{{ u.label }}</option>
                </select>
              </div>
            </div>

            <!-- Tombol -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <button type="submit" class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded">
                  Tampilkan
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabel -->
      <div class="card mt-4">
        <div class="card-body py-4 px-4">
          <div class="row gx-2 gy-2 mb-3 align-items-center">
            <div class="col-md-6 d-flex align-items-center">
              <label class="me-1 small">Show</label>
              <select v-model.number="form.per_page" @change="submit" class="form-select form-select-sm me-2" style="width:60px;">
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
              </select>
              <label class="small">entries</label>
            </div>
            <div class="col-md-6 text-md-end">
              <label class="me-1 small">Search:</label>
              <input v-model.lazy="form.search" @change="submit" type="text" class="form-control form-control-sm d-inline-block" style="width:180px;">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-center">
              <thead class="table-success text-nowrap">
                <tr>
                  <th>NO</th>
                  <th>TGL RUJUKAN</th>
                  <th>NIK</th>
                  <th>NAMA</th>
                  <th>NAMA RUMAH SAKIT</th>
                  <th>POLI TUJUAN</th>
                  <th>KEPESERTAAN</th>
                  <th>STATUS</th>
                  <th>DILAYANI</th>
                </tr>
              </thead>

              <tbody v-if="rows.length">
                <tr v-for="(row, i) in rows" :key="i">
                  <td>{{ ((rujukan?.current_page ?? 1) - 1) * (rujukan?.per_page ?? 10) + i + 1 }}</td>
                  <td>{{ row.tgl_rujuk }}</td>
                  <td class="text-nowrap">{{ row.nik }}</td>
                  <td class="text-start">{{ row.nama }}</td>
                  <td class="text-start">{{ row.nama_rs }}</td>
                  <td class="text-start">{{ row.poli_tujuan }}</td>
                  <td>{{ row.kepesertaan }}</td>
                  <td>{{ row.status }}</td>
                  <td>{{ row.dilayani }}</td>
                </tr>
              </tbody>

              <tbody v-else>
                <tr>
                  <td colspan="9" class="text-center small">No matching records found</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Footer Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-2 small" v-if="rujukan && rujukan.data">
            <div>
              Showing {{ rujukan.from ?? 0 }} to {{ rujukan.to ?? 0 }} of {{ rujukan.total ?? 0 }} entries
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary btn-sm"
                      :disabled="!rujukan.prev_page_url"
                      @click="go(rujukan.prev_page_url)">Previous</button>
              <button class="btn btn-outline-secondary btn-sm"
                      :disabled="!rujukan.next_page_url"
                      @click="go(rujukan.next_page_url)">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import '@/../css/laporan-css/form-styles.css'

const props = defineProps({
  puskesmasOptions: { type: Array, default: () => [] },
  unitOptions: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  rujukan: { type: Object, default: () => ({}) },
})

const form = reactive({
  pusk_id: props.filters?.pusk_id ?? null,
  tgl: props.filters?.tgl ?? null,
  kepesertaan: props.filters?.kepesertaan ?? null,
  unit: props.filters?.unit ?? null,
  search: props.filters?.search ?? null,
  per_page: Number(props.filters?.per_page ?? 10),
})

const rujukan = computed(() => props.rujukan || {})
const rows = computed(() => Array.isArray(rujukan.value?.data) ? rujukan.value.data : [])

function submit () {
  // gunakan nama route yang terdaftar: 'laporan.rujukan'
  router.get(route('laporan.rujukan'), form, {
    preserveState: true,
    replace: true,
  })
}

function go (url) {
  router.visit(url, { preserveState: true, replace: true })
}
</script>

<style scoped>
.btn-gradient {
  background: linear-gradient(135deg, #0d6efd, #20c997);
  transition: .2s ease;
}
.btn-gradient:hover { opacity:.9; }
</style>
