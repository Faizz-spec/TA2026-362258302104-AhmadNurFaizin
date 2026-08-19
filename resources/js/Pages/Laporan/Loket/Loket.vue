/* 
resources\js\Pages\Laporan\LoketLoket.vue
*/

<template>
  <AppLayout title="Template Components">
    <div class="container py-4" style="font-family: 'Segoe UI', sans-serif;">

      <div class="card mb-4 shadow-sm">
        <!-- Header gradient -->
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(135deg, #0d6efd, #20c997); transition: 0.3s;">
          Filter Data Laporan Loket
        </div>

        <div class="card-body">
          <form>
            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select class="form-select">
                  <option>WONGSOREJO</option>
                </select>
              </div>
            </div>

            <!-- Laporan -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Laporan</label>
              <div class="col-md-6">
                <select v-model="selectedLaporan" class="form-select">
                  <option disabled value="">Pilih</option>
                  <option value="register-kunjungan">1. Register kunjungan pasien</option>
                  <option value="register-sehat">2. Register kunjungan pasien sehat</option>
                  <option value="laporan-bulanan">3. LAPORAN BULANAN DATA KUNJUNGAN</option>
                </select>
              </div>
            </div>

            <!-- Tanggal Awal -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tanggal Awal</label>
              <div class="col-md-6">
                <input v-model="tglAwal" type="date" class="form-control" />
              </div>
            </div>

            <!-- Tanggal Akhir -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tanggal Akhir</label>
              <div class="col-md-6">
                <input v-model="tglAkhir" type="date" class="form-control" />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-6">
                <select v-model="selectedUnit" class="form-select">
                  <option>- Pilih -</option>
                </select>
              </div>
            </div>

            <!-- Sub Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Sub Unit</label>
              <div class="col-md-6">
                <select v-model="selectedSubUnit" class="form-select">
                  <option>- Pilih -</option>
                </select>
              </div>
            </div>

            <!-- Desa -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Desa</label>
              <div class="col-md-6">
                <select v-model="selectedDesa" class="form-select">
                  <option>- SEMUA -</option>
                </select>
              </div>
            </div>

            <!-- Tombol -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <button
                  type="button"
                  @click="handleTampilkanData"
                  class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded">
                  <i class="bi bi-printer-fill me-1"></i>
                  Tampilkan Data
                </button>

                <button
                  type="button"
                  class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded">
                  <i class="bi bi-file-earmark-excel-fill me-1"></i>
                  Download Excel
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
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import '@/../css/laporan-css/form-styles.css'; // sesuaikan path-nya
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const selectedLaporan = ref('')
const tglAwal = ref('')
const tglAkhir = ref('')
const selectedUnit = ref('')
const selectedSubUnit = ref('')
const selectedDesa = ref('')

const handleTampilkanData = () => {
  if (!selectedLaporan.value) {
    alert('Pilih jenis laporan dulu!')
    return
  }

  router.visit(route('laporan.loket.tampilkan-laporan-loket', {
    jenis: selectedLaporan.value,
    tglAwal: tglAwal.value,
    tglAkhir: tglAkhir.value,
    unit: selectedUnit.value,
    subunit: selectedSubUnit.value,
    desa: selectedDesa.value,
  }))
}
</script>

