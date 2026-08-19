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
  router.get('/mal-sehat/ptm')
}
</script>

<template>
  <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <!-- HEADER -->
    <div class="card-header d-flex justify-content-between align-items-center text-white fw-semibold fs-5 px-4 py-3"
      style="background-color: #87CEEB;">
      <span>Skrining Faktor Risiko</span>
      <button class="btn btn-sm btn-light" @click="kembali">Kembali</button>
    </div>

    <!-- FILTER -->
    <div class="card-body border-bottom">
      <h6 class="fw-bold mb-3">Filter Data</h6>
      <div class="row mb-1 align-items-center">
        <label class="col-sm-2 col-form-label fw-semibold">Tanggal kunjungan</label>
        <div class="col-sm-4">
          <input type="date" v-model="tanggal" class="form-control" />
        </div>
      </div>
      <div class="row mb-1 align-items-center">
        <label class="col-sm-2 col-form-label fw-semibold">Kategori Unit</label>
        <div class="col-sm-4">
          <select class="form-select" v-model="kategoriUnit">
            <option>[ PUSKESMAS ] WONGSOREJO</option>
            <!-- Tambahkan opsi lain jika perlu -->
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-2 offset-sm-2">
          <button class="btn btn-sm text-white w-100" style="background-color:#87CEEB;" @click="tampilkanData">
            Tampilkan Data
          </button>
        </div>
      </div>
    </div>

    <!-- TABEL -->
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <label>
            Show
            <select class="form-select d-inline-block w-auto mx-1">
              <option>10</option>
              <option>25</option>
              <option>50</option>
              <option>100</option>
            </select>
            entries
          </label>
        </div>
        <div>
          <label>
            Search:
            <input type="search" class="form-control d-inline-block w-auto ms-1" />
          </label>
        </div>
      </div>

      <table class="table table-bordered table-hover table-sm text-center align-middle">
        <thead class="table-light">
          <tr>
            <th>TANGGAL<br />NO. URUT</th>
            <th>NO. MR</th>
            <th>NAMA<br />NIK</th>
            <th>ALAMAT<br />KECAMATAN-DESA</th>
            <th>NO. BPJS<br />NO. KUNJUNGAN</th>
            <th>POLI</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="8" class="text-muted py-4">No matching records found</td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center">
        <div>
          Showing 0 to 0 of 0 entries (filtered from NaN total entries)
        </div>
        <div>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item disabled"><a class="page-link">Previous</a></li>
              <li class="page-item disabled"><a class="page-link">Next</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>
