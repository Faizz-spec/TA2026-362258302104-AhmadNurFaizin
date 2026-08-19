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
      <div class="row g-4">
        <div class="col-6 col-md-4 col-lg-3" v-for="poli in listPoliWithStyle" :key="listPoli.id">
          <Link :href="route('ruang-layanan.index' , {idPoli : poli.kdPoli})" class="text-decoration-none">
          <div class="card shadow ">
            <div class="card-body ">
              <div class="mb-3 rounded-4 d-flex justify-content-center"
                :style="{ width: '60px', height: '60px', backgroundColor: poli.bg }">
                <i :class="poli.icon" class="text-white fs-2"></i>
              </div>
              <h5 class="card-title mb-2">{{ poli.nmPoli }}</h5>
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
import AppLayouts from '../../Components/Layouts/AppLayouts.vue';
import { route } from 'ziggy-js';
const { props } = usePage();

const listPoli = props.listPoli
console.log(listPoli)

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const poliStyles = {
  'Umum': { bg: '#3b82f6', icon: 'bi bi-person-fill' },
  'Gigi': { bg: '#10b981', icon: 'bi bi-clipboard-check' },
  'UGD': { bg: '#facc15', icon: 'bi bi-hospital' },
  'KB': { bg: '#6366f1', icon: 'bi bi-people-fill' },
  'Sanitasi': { bg: '#14b8a6', icon: 'bi bi-droplet-half' },
  'Rawat Inap': { bg: '#ef4444', icon: 'bi bi-heart-pulse' },
  'Kunjungan Online': { bg: '#3b82f6', icon: 'bi bi-chat-dots' },
  'Gizi': { bg: '#22c55e', icon: 'bi bi-egg-fried' },
  'KIA': { bg: '#f97316', icon: 'bi bi-gender-female' },
  'Skrining PTM': {bg: '#f97316', icon: 'bi bi-gender-female'},
}

  const listPoliWithStyle = listPoli.map(poli => ({
    ...poli,
      bg: poliStyles[poli.nmPoli]?.bg || '#9ca3af', 
      icon :  poliStyles[poli.nmPoli]?.icon || 'bi bi-hospital',
  }))
</script>

<style></style>
