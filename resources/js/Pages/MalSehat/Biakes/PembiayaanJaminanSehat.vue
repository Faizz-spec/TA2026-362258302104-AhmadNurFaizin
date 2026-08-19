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
  router.get('/mal-sehat/biakes')
}

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})
</script>

<template>
  <div class="card m-2 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div
      class="card-header p-4 d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #4682B4, #ADD8E6);"
    >
      <div>
        <h5 class="m-0 fs-4 text-white">Konsultasi Pembiayaan & Jaminan Kesehatan</h5>
        <div
          class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2 text-white fw-semibold"
        >
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <button
        class="btn bg-white bg-opacity-25 border-0 btn-sm text-white fw-semibold shadow-sm"
        @click="kembali"
      >
        Kembali
      </button>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3 text-dark">Filter Data</h6>
        <div class="row mb-3">
          <label class="col-sm-2 col-form-label fw-semibold">Tanggal Kunjungan</label>
          <div class="col-sm-4">
            <input
              type="date"
              v-model="tanggal"
              class="form-control rounded-3 shadow-sm border-dark"
            />
          </div>
        </div>
        <div class="row mb-3">
          <label class="col-sm-2 col-form-label fw-semibold">Kategori Unit</label>
          <div class="col-sm-4">
            <select
              class="form-select rounded-3 shadow-sm border-dark"
              v-model="kategoriUnit"
            >
              <option>[ PUSKESMAS ] WONGSOREJO</option>
              <!-- Tambahkan opsi lain jika perlu -->
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2 offset-sm-2">
            <button
              class="btn w-100 text-white fw-semibold rounded-3 shadow-sm"
              style="background: linear-gradient(135deg, #4682B4, #5A9BD5);"
              @click="tampilkanData"
            >
              Tampilkan Data
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0 rounded-4">
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

        <div class="table-responsive">
          <table class="table table-hover align-middle text-center table-bordered">
            <thead style="background: #5A9BD5; color: white;">
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
                <td colspan="8" class="text-center text-muted py-3">
                  <i class="fas fa-info-circle me-2"></i> No matching records found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
          <div>Showing 0 to 0 of 0 entries (filtered from NaN total entries)</div>
          <div>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled">
                  <a class="page-link">Previous</a>
                </li>
                <li class="page-item disabled">
                  <a class="page-link">Next</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
