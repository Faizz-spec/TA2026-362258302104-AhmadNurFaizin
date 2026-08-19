<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";

// ✅ Props dari backend
const props = defineProps({
  obatList: Array,
  today: String,
});

// ✅ State pencarian
const search = ref("");

// ✅ Data hasil filter berdasarkan search
const filteredObat = computed(() => {
  if (!search.value) return props.obatList;
  return props.obatList.filter((obat) =>
    obat.NAMA.toLowerCase().includes(search.value.toLowerCase()) ||
    obat.KODE_OBAT.toLowerCase().includes(search.value.toLowerCase())
  );
});

// ✅ Navigasi tombol
const goTambah = () => {
  router.visit("/farmasi/master-obat/tambah"); // ✔ sesuai route Laravel
};
const goBack = () => {
  router.visit("/farmasi");
};
</script>

<template>
  <div class="container-fluid py-3">

    <!-- CARD 1 : Judul + Tanggal + Tombol -->
    <div class="card shadow-sm mb-3 border-0">
      <div class="card-body d-flex justify-content-between align-items-center" 
           style="background-color: rgba(11,195,208,0.66); border-radius: 0.5rem;">
        
        <!-- Kiri -->
        <div>
          <!-- Judul reguler -->
          <h4 class="mb-1 fw-normal">Jumlah Stok Farmasi Hari Ini</h4>

          <!-- Tanggal di dalam card kecil -->
          <div class="card p-2 shadow-sm" style="background-color: #f8f9fa; border-radius: 0.4rem; width: fit-content;">
            <small class="text-muted">Tanggal: {{ props.today }}</small>
          </div>
        </div>

        <!-- Kanan -->
        <div>
          <button class="btn btn-light border me-2 text-dark fw-semibold" @click="goTambah">
            ➕ Tambah Obat
          </button>
          <button class="btn btn-light border text-dark fw-semibold" @click="goBack">
            ⬅️ Kembali
          </button>
        </div>
      </div>
    </div>

    <!-- CARD 2 : Search + Tabel -->
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <!-- Search -->
        <div class="row mb-3">
          <div class="col-md-6">
            <input
              type="text"
              v-model="search"
              class="form-control"
              placeholder="🔍 Cari Obat..."
            />
          </div>
        </div>

        <!-- Tabel Data -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th style="width: 15%">Kode Obat</th>
                <th>Nama Obat</th>
                <th style="width: 15%">Satuan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="obat in filteredObat" :key="obat.OBAT_ID">
                <td>{{ obat.KODE_OBAT }}</td>
                <td>{{ obat.NAMA }}</td>
                <td>{{ obat.SATUAN }}</td>
              </tr>
              <tr v-if="filteredObat.length === 0">
                <td colspan="3" class="text-center text-muted">
                  Tidak ada data obat ditemukan
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</template>
