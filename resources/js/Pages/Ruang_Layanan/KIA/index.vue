<template>
  <AppLayouts>
    <div class="container my-4">
      <div class="p-4 rounded mb-4 text-white" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="h3 mb-2">
          Ruang Layanan KIA4234
        </h1>
        <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2">
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <div class="row g-4">
        <div class="col-6 col-md-4 col-lg-3" v-for="layanan in dataLayanan" :key="layanan.nama">
          <Link :href="route(layanan.link)" class="text-decoration-none">
          <div class="card shadow ">
            <div class="card-body ">
              <div class="mb-3 rounded-4 d-flex justify-content-center"
                :style="{ width: '60px', height: '60px', backgroundColor: (layanan.bg) }">
                <i :class="layanan.icon" class="text-white fs-2"></i>
              </div>
              <h5 class="card-title mb-2">{{ layanan.nama }}</h5>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Pasien</small>
                <strong class="fs-4">{{ layanan.jumlah }}</strong>
              </div>
            </div>
          </div>
          </Link>
        </div>
      </div>
    </div>
  </AppLayouts>
</template>
<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';

const { props } = usePage();
const totalPasienkia = props.totalPasienKIA ?? 0
const totalPasienMTBS = props.totalPasienMTBS ?? 0
const totalPasienMTBM = props.totalPasienMTBM ?? 0

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const dataLayanan = [
  { nama: 'ANC', jumlah: totalPasienkia, link: 'ruang-layanan.anc', icon: 'bi bi-person-fill', bg: '#3b82f6' },
  { nama: 'Tumbuh Kembang', jumlah: totalPasienkia, link: 'ruang-layanan.tkembang', icon: 'bi bi-clipboard-check', bg: '#10b981' },
  { nama: 'Neonatus', jumlah: totalPasienkia, link: 'ruang-layanan.neonatus', icon: 'bi bi-hospital', bg: '#facc15' },
  { nama: 'INC', jumlah: totalPasienkia, link: 'ruang-layanan.inc', icon: 'bi bi-people-fill', bg: '#3b82f6' },
  { nama: 'Kematian', jumlah: totalPasienkia, link: 'ruang-layanan.kematian', icon: 'bi bi-droplet-half', bg: '#14b8a6' },
  { nama: 'PNC', jumlah: totalPasienkia, link: 'ruang-layanan.pnc', icon: 'bi bi-heart-pulse', bg: '#facc15' },
  { nama: 'MTBS', jumlah: totalPasienMTBS, link: 'ruang-layanan.mtbs', icon: 'bi bi-activity', bg: '#14b8a6' },
  { nama: 'MTBM', jumlah: totalPasienMTBM, link: 'ruang-layanan.mtbm', icon: 'bi bi-bandaid-fill', bg: '#8b5cf6' },

]

</script>

<style>

</style>
