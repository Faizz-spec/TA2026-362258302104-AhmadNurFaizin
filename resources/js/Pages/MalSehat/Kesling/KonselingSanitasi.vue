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
const dataPasien = ref([
  {
    tanggal: '2025-08-11',
    noUrut: '001',
    noMr: 'MR12345',
    nama: 'Ajeng Nina',
    nik: '3172012345678901',
    alamat: 'Wongsorejo',
    desa: 'Wongsorejo Village',
    noBpjs: 'BPJS123456',
    noKunjungan: 'KUNJ20250811-001',
    poli: 'Poli Umum',
    status: 'Belum Dilayani'
  },
  {
    tanggal: '2025-08-11',
    noUrut: '002',
    noMr: 'MR12346',
    nama: 'Budi Santoso',
    nik: '3172012345678902',
    alamat: 'Wongsorejo',
    desa: 'Wongsorejo Village',
    noBpjs: 'BPJS123457',
    noKunjungan: 'KUNJ20250811-002',
    poli: 'Poli Anak',
    status: 'Belum Dilayani'
  }
])

function goToDetail(item) {
  // Ubah status jadi "Proses Dilayani"
  item.status = 'Proses Dilayani'

  // Kirim ke halaman detail
  router.get(`/mal-sehat/kesling/detail/${item.noKunjungan}`, {
    nama: item.nama
  })
}

// Simulasi selesai pelayanan (nanti trigger ini dari halaman detail)
function selesaiPelayanan(noKunjungan) {
  const pasien = dataPasien.value.find(p => p.noKunjungan === noKunjungan)
  if (pasien) {
    pasien.status = 'Sudah Dilayani'
  }
}
function kembali() {
  router.get('/mal-sehat/kesling')
}
</script>

<template>
  <div class="card m-4 shadow-sm border-0 rounded-4 rounded-bottom-0">
    <!-- Header -->
    <div class="card-header p-4 text-black d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #3b82f6, #10b981);">
      <h1 class="m-0 fs-4 text-white">Konseling Sanitasi</h1>
      <button class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white" @click="kembali">
        <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
      </button>
    </div>

    <!-- Filter Form -->
    <div class="card mt-4 mx-4 shadow-sm">
      <div class="card-header bg-transparent border-0 py-3"
        style="background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
        <h5 class="m-0 fw-semibold text-slate-700 d-flex align-items-center">
          <span class="rounded-5 bg-primary p-2 me-3">
            <i class="fas fa-sliders text-white"></i>
          </span>
          Filter Data
        </h5>
      </div>

      <div class="card-body">
        <form class="row gx-3 gy-2 align-items-end" @submit.prevent="tampilkanData">
          <!-- Tanggal Kunjungan -->
          <div class="col-md-5">
            <label class="form-label fw-semibold">Tanggal Kunjungan</label>
            <input type="date" class="form-control" v-model="tanggal" />
          </div>

          <!-- Kategori Unit -->
          <div class="col-md-5">
            <label class="form-label fw-semibold">Kategori Unit</label>
            <select class="form-select" v-model="kategoriUnit">
              <option>[ PUSKESMAS ] WONGSOREJO</option>
              <!-- Tambahkan opsi lain jika perlu -->
            </select>
          </div>

          <!-- Tombol Tampilkan -->
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search me-1"></i> Tampilkan Data
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Data Table -->
    <div class="card mt-4 mx-4 shadow-sm">
      <div class="card-header my-2 border-0 bg-white d-flex justify-content-between align-items-center">
        <div>
          <label class="me-2">Show</label>
          <select class="form-select d-inline w-auto" style="width: 80px">
            <option>10</option>
            <option>25</option>
          </select>
          <span class="ms-2">entries</span>
        </div>
        <form>
          <div class="input-group">
            <input type="search" class="form-control" placeholder="Search..." />
            <i class="bi bi-search input-group-text bg-white"></i>
          </div>
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered text-center">
            <thead>
              <tr>
                <th>TANGGAL<br />NO. URUT</th>
                <th>NO. MR</th>
                <th>NAMA<br />NIK</th>
                <th>ALAMAT<br />KECAMATAN-DESA</th>
                <th>NO. BPJS<br />NO. KUNJUNGAN</th>
                <th>POLI</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in dataPasien" :key="item.noKunjungan">
                <td>{{ item.tanggal }}<br />{{ item.noUrut }}</td>
                <td>{{ item.noMr }}</td>
                <td>{{ item.nama }}<br />{{ item.nik }}</td>
                <td>{{ item.alamat }}<br />{{ item.desa }}</td>
                <td>{{ item.noBpjs }}<br />{{ item.noKunjungan }}</td>
                <td>{{ item.poli }}</td>
                <td :class="{
                  'text-warning fw-semibold': item.status === 'Belum Dilayani',
                  'text-primary fw-semibold': item.status === 'Proses Dilayani',
                  'text-success fw-semibold': item.status === 'Sudah Dilayani'
                }" style="cursor: pointer" @click="goToDetail(item)">
                  {{ item.status }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
