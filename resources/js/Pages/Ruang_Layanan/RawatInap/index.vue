<template>
  <AppLayouts>
    <div class="container my-4">
      <h5 class="mb-4">POLI RAWAT INAP</h5>
      <div class="row g-4 ">
        <div class="col-md-3" v-for="layanan in dataLayanan" :key="layanan.nama">
          <Link :href="route(layanan.link)" class="text-decoration-none text-dark">
            <div class="card shadow-sm rounded overflow-hidden text-center">
              <!-- Gambar atas -->
              <div class="bg-primary text-white d-flex justify-content-center align-items-center" style="height: 120px;">
              <i :class="layanan.icon"></i>
              </div>
              <!-- Konten bawah -->
              <div class="p-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold fs-5">{{ layanan.nama }}</span>
                <span class="badge bg-secondary fs-5">{{ layanan.jumlah }}</span>
              </div>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </AppLayouts>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
const dataLayanan = [
  { nama: 'Penerimaan Pasien', jumlah: 1, link: 'ruang-layanan.rawat-inap.penerimaan-pasien' , icon: 'bi bi-person-plus fs-1'},
  { nama: 'Data Keperawatan', jumlah: 0, link: 'ruang-layanan.rawat-inap.perawatan' , icon:"bi bi-clipboard-heart fs-1"},
  { nama: 'Pasien Keluar', jumlah: 0, link: 'ruang-layanan.rawat-inap.pengeluaran', icon:"bi bi-box-arrow-right  fs-1" },
]
</script>
