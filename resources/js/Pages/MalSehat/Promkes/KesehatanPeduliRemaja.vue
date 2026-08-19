<template>
  <div class="card m-2 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="card-header p-4 d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #4682B4, #8EB6C3);">
      <div>
        <h5 class="m-0 fs-4 text-white">Pelayanan Kesehatan Peduli Remaja</h5>
        <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2 text-white fw-semibold">
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <button class="btn bg-white bg-opacity-25 border-0 btn-sm text-white fw-semibold shadow-sm" @click="kembali">
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
            <input type="date" v-model="tanggal" class="form-control rounded-3 shadow-sm border-dark" />
          </div>
        </div>
        <div class="row mb-3">
          <label class="col-sm-2 col-form-label fw-semibold">Kategori Unit</label>
          <div class="col-sm-4">
            <select class="form-select rounded-3 shadow-sm border-dark" v-model="kategoriUnit">
              <option v-for="u in units" :key="u.nama_unit" :value="u.nama_unit">
                {{ u.nama_unit }}
              </option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2 offset-sm-2">
            <button class="btn w-100 text-white fw-semibold rounded-3 shadow-sm"
              style="background: linear-gradient(135deg, #4682B4, #8EB6C3);" @click="tampilkanData">
              Tampilkan Data
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center table-bordered">
            <thead style="background: #5A9BD5; color: white;">
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
                <td>{{ p.created }}<br />{{ p.id }}</td>
                <td>{{ p.no_mr }}</td>
                <td>{{ p.nama }}<br />{{ p.nik }}</td>
                <td>{{ p.alamat }}<br />{{ p.desa }}</td>
                <td>{{ p.bpjs }}<br />{{ p.kunjungan }}</td>
                <td>{{ p.poli }}</td>
                <td>
                  <button class="btn btn-sm rounded-pill shadow-sm text-white"
                    style="background: linear-gradient(135deg, #4682B4, #8EB6C3);" @click="bukaKunjungan(p)">
                    {{ p.status ? p.status : 'Belum Dilayani' }}
                  </button>
                </td>
              </tr>
              <tr v-if="somearray?.length === 0">
                <td colspan="7" class="text-center text-muted py-3">
                  <i class="fas fa-info-circle me-2"></i> Belum ada data pasien
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AppLayouts from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayouts })

const props = defineProps({
  units: Array,
  pasien: Array,
  somearray: []
})

const tanggal = ref(new Date().toISOString().split('T')[0])
const kategoriUnit = ref('[ PUSKESMAS ] WONGSOREJO')

function tampilkanData() {
  console.log('Tampilkan data untuk', tanggal.value, kategoriUnit.value)
}

function kembali() {
  router.get('/mal-sehat/promkes')
}

function bukaKunjungan(p) {
  router.get(route('mal-sehat.promkes.kesehatanpeduliremaja.pelayanan', { no_mr: p.no_mr }))
}

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})
</script>
