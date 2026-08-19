<template>
  <AppLayouts>
    <div class="container my-4">
      <div class="p-4 rounded mb-4 text-white" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="h3 mb-2">
          JUMLAH PASIEN RUANG LAYANAN PUSKESMAS WONGSOREJO HARI INI
        </h1>
        <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2">
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <div class="row g-4 mb-4">
        <div class="col-md-4" v-for="poli in dataLayanan" :key="listPoli.id">
          <Link :href="route('ruang-layanan.index' , {idPoli : poli.kdPoli})" class="text-decoration-none">
          <div class="card shadow ">
            <div class="card-body ">
              <div class="mb-3 rounded-4 d-flex justify-content-center"
                :style="{ width: '60px', height: '60px', backgroundColor: poli.bg }">
                <i :class="poli.icon" class="text-white fs-2"></i>
              </div>
              <h5 class="card-title mb-2">{{ poli.nama }}</h5>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Pasien</small>
                <strong class="fs-4">{{ poli.jumlah }}</strong>
              </div>
            </div>
          </div>
          </Link>
        </div>
      </div>
        <div class="row g-4">
        <div class="col-md-4 " >
          <Link :href="route('ruang-layanan.laborat')" class="text-decoration-none">
          <div class="card shadow ">
            <div class="card-body ">
              <div class="mb-3 rounded-4 d-flex justify-content-center"
                :style="{ width: '60px', height: '60px', backgroundColor: '#facc15' }">
                <i class="bi bi-eyedropper text-white fs-2"></i>
              </div>
              <h5 class="card-title mb-2">Laborat</h5>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Pasien</small>
                <strong class="fs-4">10</strong>
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
import { route } from 'ziggy-js';
const { props } = usePage();

const listPoli = props.listPoli
const totalPasienUGD = props.totalPasienUGD
const totalPasienGigi = props.totalPasienGigi
console.log(listPoli)

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const dataLayanan = [
  { nama: 'Gigi', jumlah: totalPasienGigi,kdPoli:'002', link: 'ruang-layanan.index', icon: 'bi bi-clipboard-check', bg: '#10b981' },
  { nama: 'UGD', jumlah: totalPasienUGD, kdPoli:'005',link: 'ruang-layanan.index', icon: 'bi bi-hospital', bg: '#facc15' },
  { nama: 'Rawat Inap', jumlah: 0, kdPoli:'001',link: 'ruang-layanan.rawat-inap', icon: 'bi bi-heart-pulse', bg: '#facc15' },
]
</script>

<style></style>