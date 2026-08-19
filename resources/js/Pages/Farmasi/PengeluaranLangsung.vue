<template>
  <div class="container mt-4">
    <!-- HEADER -->
    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
      <div class="card-body py-4" style="background-color: #28c2d1; color: white;">
        
        <!-- Baris Judul -->
        <h5 class="fw-bold mb-3">Jumlah Resep Langsung Puskesmas Hari Ini</h5>
        
        <!-- Baris Tanggal dan Tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <span class="badge bg-light text-dark px-4 py-2 rounded-pill fs-6">
            {{ today }}
          </span>

          <Link
            href="/farmasi"
            class="btn btn-light btn-sm fw-semibold px-4 shadow-sm"
          >
            Kembali
          </Link>
        </div>
      </div>
    </div>

    <!-- CARD FILTER DATA -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body p-4">
        <h6 class="fw-bold mb-3 text-primary">Filter Data</h6>

        <div class="row g-3">
          <!-- Unit -->
          <div class="col-12">
            <label class="form-label fw-semibold">Unit</label>
            <select v-model="filters.unit" class="form-control border-1 shadow-sm-sm">
              <option value="">-- Pilih Unit --</option>
              <option v-for="u in props.units" :key="u.id" :value="u.id">
                {{ u.nama }}
              </option>
            </select>
          </div>

          <!-- Sub Unit -->
          <div class="col-12">
            <label class="form-label fw-semibold">Sub Unit</label>
            <select v-model="filters.subunit" class="form-select">
              <option value="">-- Pilih Sub Unit --</option>
              <option
                v-for="su in subUnits"
                :key="su.id_detail"
                :value="su.id_detail"
              >
                {{ su.nama_unit }}
              </option>
            </select>
          </div>

          <!-- Periode -->
          <div class="col-12">
            <label class="form-label fw-semibold">Periode</label>
            <input type="date" v-model="form.tanggal" class="form-control border-1 shadow-sm-sm" />
          </div>

          <!-- Keperluan -->
          <div class="col-12">
            <label class="form-label fw-semibold">Keperluan</label>
            <select v-model="filters.keperluan" class="form-control border-1 shadow-sm-sm">
              <option value="">-- Pilih Keperluan --</option>
              <option v-for="k in props.keperluan" :key="k.id" :value="k.nama">
                {{ k.nama }}
              </option>
            </select>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-start gap-3 mt-4 flex-wrap">
          <button
            type="button"
            class="btn px-4 py-2 fw-semibold text-white"
            style="background-color: #28c2d1; border: none;"
            @click="tampilkanData"
          >
            Tampilkan Data
          </button>
          <button
            type="button"
            class="btn px-4 py-2 fw-semibold text-white"
            style="background-color: #21a9b8; border: none;"
            @click="simpanData"
          >
            Simpan Data
          </button>
        </div>
      </div>
    </div>
    
    <!-- CARD TABEL -->
    <div class="card shadow-sm border-0 rounded-4 mb-5">
      <div class="card-body p-4">
        <h6 class="fw-bold mb-3 text-primary">Data Pengeluaran Langsung</h6>

        <table
          class="table table-bordered table-striped align-middle text-center mb-0"
        >
          <thead class="table-light">
            <tr>
              <th style="width: 5%">No</th>
              <th style="width: 20%">Tanggal</th>
              <th style="width: 25%">Unit</th>
              <th style="width: 25%">Keperluan</th>
              <th style="width: 15%">Jumlah Obat</th>
              <th style="width: 10%">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in props.dataTable" :key="row.id_peng_langsung">
              <td>{{ index + 1 }}</td>
              <td>{{ row.tanggal }}</td>
              <td>{{ row.unit }}</td>
              <td>{{ row.keperluan }}</td>
              <td>{{ row.jumlah_obat }}</td>
              <td>
                <button class="btn btn-sm btn-info text-white px-3 me-1">Detail</button>
                <button class="btn btn-sm btn-danger px-3">Hapus</button>
              </td>
            </tr>
            <tr v-if="!props.dataTable.length">
              <td colspan="6" class="text-muted text-center">Tidak ada data</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  units: Array,
  subUnits: Array,
  keperluan: Array,
  dataTable: Array,
  filters: Object
})

const today = new Date().toLocaleDateString('id-ID', {
  weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

const filters = ref({
  unit: props.filters.unit || '',
  subunit: props.filters.subunit || '',
  periode: props.filters.periode || '',
  keperluan: props.filters.keperluan || ''
})

const form = ref({
  tanggal: ''
})

const subUnits = ref(props.subUnits || [])

watch(
  () => filters.value.unit,
  async (newUnit) => {
    if (!newUnit) {
      subUnits.value = []
      filters.value.subunit = ''
      return
    }

    try {
      const res = await axios.get('/farmasi/get-sub-units', {
        params: { unit: newUnit }
      })
      subUnits.value = res.data
      filters.value.subunit = ''
    } catch (err) {
      console.error('Gagal memuat sub-unit:', err)
    }
  }
)

const tampilkanData = () => {
  router.get('/farmasi/pengeluaran-langsung', filters.value, {
    preserveState: true
  })
}

const simpanData = async () => {
  try {
    const payload = {
      tanggal: form.value.tanggal,
      unitId: filters.value.unit,
      pengMasterId: filters.value.subunit,
      keperluan: filters.value.keperluan
    }

    const res = await axios.post('/farmasi/pengeluaran-langsung', payload)
    alert(res.data.message)
    tampilkanData()
  } catch (err) {
    console.error('Gagal menyimpan data:', err)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}
</script>

<style scoped>
.form-control {
  border-radius: 10px;
  padding: 0.5rem 0.75rem;
}

.card {
  border-radius: 15px;
}

.btn {
  border-radius: 8px;
  transition: all 0.2s ease;
}
.btn:hover {
  transform: translateY(-2px);
  opacity: 0.9;
}
</style>
