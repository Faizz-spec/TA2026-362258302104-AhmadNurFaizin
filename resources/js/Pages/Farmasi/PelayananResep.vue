<script setup>
import { ref, reactive, computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();

// Props dari Controller
const units = ref(props.units ?? []);
const subUnits = ref(props.subUnits ?? []);
const pasien = ref(props.data ?? []);

const filters = reactive({
  unit: props.filters?.unit ?? "",
  sub_unit: props.filters?.sub_unit ?? "",
  periode: props.filters?.periode ?? "",
});

// Helper: Sub Unit terfilter berdasarkan Unit yang dipilih
const filteredSubUnits = computed(() => {
  if (!filters.unit) return subUnits.value;
  return subUnits.value.filter((s) => s.id_kategori == filters.unit);
});

// Reload halaman dengan filter
const applyFilter = () => {
  const params = new URLSearchParams(filters).toString();
  window.location.href = `/farmasi/pelayanan-resep?${params}`;
};

// Fungsi tombol kembali
const goBack = () => {
  window.location.href = "/farmasi";
};
</script>

<template>
  <div class="container py-4">
    <!-- Card Judul -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body bg-info text-white rounded-top-4">
        <h3 class="fw-semibold mb-3" style="margin-left: 10px;">
          Jumlah Resep Pelayanan Poli Hari Ini2 ya
        </h3>

        <div class="d-flex justify-content-between align-items-center">
          <div>
            <span
              class="badge bg-light text-dark px-4 py-2 rounded-pill"
              style="margin-left: 10px;"
            >
              {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }) }}
            </span>
          </div>
          <button class="btn btn-light shadow-sm" @click="goBack">
            Kembali
          </button>
        </div>
      </div>
    </div>

    <!-- Filter Data -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Filter Data</h5>

        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-medium">Unit</label>
            <select v-model="filters.unit" class="form-select">
              <option value="">-- Pilih Unit --</option>
              <option v-for="u in units" :key="u.id" :value="u.id">
                {{ u.nama }}
              </option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Sub Unit</label>
            <select v-model="filters.sub_unit" class="form-select">
              <option value="">-- Pilih Sub Unit --</option>
              <option v-for="s in filteredSubUnits" :key="s.id" :value="s.id">
                {{ s.nama }}
              </option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Periode</label>
            <input type="date" v-model="filters.periode" class="form-control" />
          </div>

          <div class="col-12">
            <button class="btn btn-info text-white px-4 shadow-sm" @click="applyFilter">
              Tampilkan Data
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Daftar Pasien Farmasi</h6>
        <div v-if="pasien.length">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>No RM</th>
                <th>Pasien</th>
                <th>Alamat</th>
                <th>Poli</th>
                <th>Diagnosa</th>
                <th>Obat</th>
                <th>Jumlah</th>
                <th>Dosis</th>
                <th>Stok Unit</th>
                <th>Status Resep</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in pasien" :key="row.id">
                <td>{{ row.no_rm }}</td>
                <td>{{ row.pasien }}</td>
                <td>{{ row.alamat }}</td>
                <td>{{ row.poli }}</td>
                <td>{{ row.diagnosa }}</td>
                <td>{{ row.nama_obat }}</td>
                <td>{{ row.jumlah }}</td>
                <td>{{ row.dosis }}</td>
                <td>{{ row.stok_unit }}</td>
                <td>{{ row.status_resep }}</td>
                <td>{{ row.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="alert alert-info mb-0">
          Tidak ada data ditemukan.
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media (max-width: 768px) {
  h3 {
    font-size: 1.25rem;
  }
  .badge {
    font-size: 0.85rem;
  }
  .btn {
    font-size: 0.9rem;
  }
}
</style>
