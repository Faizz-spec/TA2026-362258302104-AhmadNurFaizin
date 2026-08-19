<template>
  <div class="container py-4">
    <div class="header-blue p-3 rounded-top d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-0 text-white">Laporan Farmasi Hari Ini</h2>
        <p class="text-white mb-0">Kamis, 27 November 2025</p>
      </div>
      <a href="/farmasi" class="btn btn-light">Kembali</a>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white">
        <h5 class="mb-0">Filter Data</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="filterLaporan">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Laporan</label>
              <select v-model="form.laporan" class="form-select" required>
                <option value="">-- Pilih Jenis Laporan --</option>
                <option>Laporan register pasien</option>
                <option>Laporan jumlah pengeluaran langsung</option>
                <option>Laporan jumlah pemakaian harian</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Unit</label>
              <select v-model="form.unit" class="form-select" required>
                <option value="">-- Pilih Unit --</option>
                <option>SEMUA UNIT</option>
                <option>PUSKESMAS</option>
                <option>PUSTU</option>
                <option>POLINDES</option>
                <option>POSYANDU</option>
                <option>POSKESDES</option>
                <option>PUSLING</option>
                <option>POSKESTREN</option>
                <option>PONKESDES</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Sub Unit</label>
              <select v-model="form.subUnit" class="form-select">
                <option value="">-- Pilih Sub Unit --</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Awal</label>
              <input type="date" v-model="form.tanggalAwal" class="form-control" required />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Tanggal Akhir</label>
              <input type="date" v-model="form.tanggalAkhir" class="form-control" required />
            </div>
            <div class="col-md-6">
              </div>
          </div>

          <div class="d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-info text-white">
              Tampilkan Data
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm mt-4">
      <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Pasien Farmasi</h5>
      </div>
      <div class="card-body">
        <p class="text-center text-muted">Tidak ada data ditemukan.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import 'vue-good-table-next/dist/vue-good-table-next.css'

const form = ref({
  laporan: '',
  tanggalAwal: '',
  tanggalAkhir: '',
  unit: '',
  subUnit: ''
})

const filterLaporan = () => {
  console.log('Filter data', form.value)
}
</script>

<style scoped>
.header-blue {
  background-color: #00bcd4; /* Warna biru muda */
}
</style>