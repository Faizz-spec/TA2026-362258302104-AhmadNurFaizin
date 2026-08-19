<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

// Props pasien dari server
const props = defineProps({
  pasien: {
    type: Array,
    default: () => [
      {
        tanggal: '2025-08-11',
        no_urut: '001',
        no_mr: '0139183',
        nama: 'DENIS HIDAYAT',
        nik: '3510161002880001',
        alamat: 'PERUM. GRIYA PESONA KARANGREJO BLOK DE-15 RT 1 RW 4',
        desa: 'Banyuwangi-Karangrejo',
        bpjs: '0001234567890',
        kunjungan: 'KJ-2025-001',
        poli: 'Umum',
        status: 'Belum Dilayani'
      }
    ]
  }
})

// Filter
const tanggal = ref(new Date().toISOString().split('T')[0])
const kategoriUnit = ref('[ PUSKESMAS ] WONGSOREJO')

// Aksi tombol
function tampilkanData() {
  console.log('Tampilkan data untuk', tanggal.value, kategoriUnit.value)
}

function kembali() {
  router.get('/mal-sehat')
}

function bukaKunjungan(pasien) {
  if (pasien.status === 'Belum Dilayani') {
    // arahkan ke route sehat.pelayanan
    router.get(route('sehat.pelayanan', { no_mr: pasien.no_mr }))
  } else {
    alert('Pasien sudah dilayani.')
  }
}

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})
</script>

<template>
  <div class="container my-3">
    <!-- Header -->
    <div class="p-4 rounded mb-4 text-white d-flex justify-content-between align-items-center shadow"
      style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
      <div>
        <h1 class="h4 mb-2 fw-bold">Layanan - Sehat</h1>

      </div>
      <button class="btn btn-light btn-sm fw-semibold" @click="kembali">← Kembali</button>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">📅 Filter Data</h6>
        <div class="row mb-3 align-items-center">
          <label class="col-sm-2 col-form-label fw-semibold">Tanggal Kunjungan</label>
          <div class="col-sm-4">
            <input type="date" v-model="tanggal" class="form-control rounded-3 shadow-sm" />
          </div>
        </div>
        <div class="row mb-3 align-items-center">
          <label class="col-sm-2 col-form-label fw-semibold">Kategori Unit</label>
          <div class="col-sm-4">
            <select class="form-select rounded-3 shadow-sm" v-model="kategoriUnit">
              <option>[ PUSKESMAS ] WONGSOREJO</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2 offset-sm-2">
            <button class="btn w-100 text-white fw-semibold rounded-3 shadow-sm" style="background-color:#2563eb;"
              @click="tampilkanData">
              🔍 Tampilkan Data
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <!-- Table Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <label class="mb-0">
              Show
              <select class="form-select d-inline-block w-auto mx-1 rounded-3 shadow-sm">
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
              <input type="search" class="form-control d-inline-block w-auto ms-1 rounded-3 shadow-sm" />
            </label>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center table-bordered">
            <thead class="table-primary">
              <tr>
                <th>TANGGAL<br />NO. URUT</th>
                <th>NO. MR</th>
                <th>NAMA<br />NIK</th>
                <th>ALAMAT<br />DESA</th>
                <th>NO. BPJS<br />NO. KUNJUNGAN</th>
                <th>POLI</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in props.pasien" :key="p.no_mr">
                <td>{{ p.tanggal }}<br />{{ p.no_urut }}</td>
                <td>{{ p.no_mr }}</td>
                <td>{{ p.nama }}<br />{{ p.nik }}</td>
                <td>{{ p.alamat }}<br />{{ p.desa }}</td>
                <td>{{ p.bpjs }}<br />{{ p.kunjungan }}</td>
                <td>{{ p.poli }}</td>
                <td>
                  <button class="btn btn-success btn-sm rounded-pill shadow-sm" @click="bukaKunjungan(p)">
                    {{ p.status }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">
            Showing {{ props.pasien.length }} to {{ props.pasien.length }} of {{ props.pasien.length }} entries
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
  </div>
</template>
