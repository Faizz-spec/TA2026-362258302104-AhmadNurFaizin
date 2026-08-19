<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const tanggal = ref(new Date().toISOString().split('T')[0])
const kategoriUnit = ref('[ PUSKESMAS ] WONGSOREJO')

function tampilkanData() {
  console.log('Tampilkan data untuk', tanggal.value, kategoriUnit.value)
}

function kembali() {
  router.get('/mal-sehat')
}
</script>

<template>
  <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
    <!-- HEADER -->
    <div
      class="d-flex justify-content-between align-items-center px-4 py-3 text-white"
      style="background: linear-gradient(135deg, #60a5fa, #3b82f6);"
    >
      <h5 class="mb-0 fw-bold">🏠 Home Visit</h5>
      <button class="btn btn-light btn-sm" @click="kembali">← Kembali</button>
    </div>

    <!-- FILTER -->
    <div class="card-body border-bottom">
      <h6 class="fw-bold mb-3">📅 Filter Data</h6>
      <div class="row mb-2 align-items-center">
        <label class="col-sm-2 col-form-label fw-semibold">Tanggal kunjungan</label>
        <div class="col-sm-4">
          <input type="date" v-model="tanggal" class="form-control rounded-3" />
        </div>
      </div>
      <div class="row mb-2 align-items-center">
        <label class="col-sm-2 col-form-label fw-semibold">Kategori Unit</label>
        <div class="col-sm-4">
          <select class="form-select rounded-3" v-model="kategoriUnit">
            <option>[ PUSKESMAS ] WONGSOREJO</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-2 offset-sm-2">
          <button
            class="btn w-100 text-white fw-semibold rounded-3"
            style="background-color:#3b82f6;"
            @click="tampilkanData"
          >
            🔍 Tampilkan Data
          </button>
        </div>
      </div>
    </div>

    <!-- TABEL -->
    <div class="card-body">
      <!-- Table Controls -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <label class="mb-0">
            Show
            <select class="form-select d-inline-block w-auto mx-1 rounded-3">
              <option>10</option>
              <option>25</option>
              <option>50</option>
              <option>100</option>
            </select>
            entries
          </label>
        </div>
        <div>
          <label class="mb-0">
            Search:
            <input type="search" class="form-control d-inline-block w-auto ms-1 rounded-3" />
          </label>
        </div>
      </div>

      <!-- Data Table -->
      <table class="table table-hover table-bordered align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>TANGGAL<br />NO. URUT</th>
            <th>NO. MR</th>
            <th>NAMA<br />NIK</th>
            <th>ALAMAT<br />KECAMATAN-DESA</th>
            <th>NO. BPJS<br />NO. KUNJUNGAN</th>
            <th>POLI</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <!-- Contoh data pasien -->
          <tr>
            <td>2025-08-11<br />001</td>
            <td>0139183</td>
            <td>DENIS HIDAYAT<br />3510161002880001</td>
            <td>PERUM. GRIYA PESONA KARANGREJO BLOK DE-15 RT 1 RW 4<br />Banyuwangi-Karangrejo</td>
            <td>0001234567890<br />KJ-2025-001</td>
            <td>Umum</td>
            <td><span class="badge bg-success">Belum Dilayani</span></td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Info -->
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Showing 1 to 1 of 1 entries
        </small>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled"><a class="page-link">Previous</a></li>
            <li class="page-item active"><a class="page-link">1</a></li>
            <li class="page-item disabled"><a class="page-link">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>
