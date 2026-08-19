<template>
  <AppLayouts>
    <div class="container my-4">
      <!-- === Tambahan Klaster === -->
      <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-md-4" v-for="(klaster, i) in klasterList" :key="i">
          <div class="card text-white shadow border-0" :style="{ background: klaster.gradient }">
            <div class="card-body position-relative">
              <i :class="['bi', klaster.icon, 'position-absolute', 'top-0', 'end-0', 'm-3', 'fs-2', 'opacity-75']"></i>
              <h5 class="fw-semibold">{{ klaster.nama }}</h5>
              <div class="fs-5 fw-bold">{{ klaster.jumlah }}</div>
              <Link :href="route('ruang-layanan.poli-kluster', {kluster : klaster.id})" class="text-white-50 small text-decoration-none">More info <i
                  class="bi bi-arrow-right"></i></Link>
            </div>
          </div>
        </div>
      </div>
      <!-- === Akhir Tambahan Klaster === -->

      <!-- Daftar Poli -->
      <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-md-4" v-for="poli in dataLayanan" :key="listPoli.id">
          <Link :href="route('ruang-layanan.index', { idPoli: poli.kdPoli })" class="text-decoration-none">
          <div class="card shadow">
            <div class="card-body">
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
    </div>
  </AppLayouts>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayouts from '../../Components/Layouts/AppLayouts.vue';
import { route } from 'ziggy-js';
import {onMounted, ref, computed} from 'vue';
import axios from 'axios';
const { props } = usePage();

const listPoli = props.listPoli
const jumlahPasienKlusterPoli = ref({})
console.log(listPoli)

onMounted(()=>{
  axios.get(route('ruang-layanan.totalPasienKlusterAndPoli'))
  .then(res => {
    console.log('datanya',res.data);
    jumlahPasienKlusterPoli.value = res.data;
    console.log('data kluster 3', jumlahPasienKlusterPoli.value.jumlahPasienKluster3)
  })
})

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const dataLayanan = computed(()=>
  [
  { nama: 'Sanitasi', jumlah:  jumlahPasienKlusterPoli.value.jumlahPasienSanitasi,  kdPoli :'097', link: 'ruang-layanan.index', icon: 'bi bi-droplet-half', bg: '#14b8a6' },
  { nama: 'Kunjungan Online', jumlah:  jumlahPasienKlusterPoli.value.jumlahPasienKunjOnline, kdPoli :'999', link: 'ruang-layanan.kunjungan-online', icon: 'bi bi-chat-dots', bg: '#3b82f6' },
  { nama: 'Gizi', jumlah:  jumlahPasienKlusterPoli.value.jumlahPasienGizi,  kdPoli :'997', icon: 'bi bi-egg-fried', bg: '#10b981' },

]
)

// === Data Klaster (statis) ===
const klasterList = computed(() => 
  [
  {
    nama: 'Klaster 2',
    jumlah: jumlahPasienKlusterPoli.value.jumlahPasienKluster2,
    gradient: 'linear-gradient(135deg, #22c55e, #16a34a)',
    icon: 'bi-house-fill',
    id : 2
  },
  {
    nama: 'Klaster 3',
    jumlah: jumlahPasienKlusterPoli.value.jumlahPasienKluster3,
    gradient: 'linear-gradient(135deg, #0ea5e9, #3b82f6)',
    icon: 'bi-hospital',
    id: 3
  },
  {
    nama: 'Lintas Klaster',
    jumlah: jumlahPasienKlusterPoli.value.jumlahPasienLintasKluster,
    gradient: 'linear-gradient(135deg, #f59e0b, #d97706)',
    icon: 'bi-people-fill',
    id : 4
  },
]
)
</script>

<style scoped>

</style>
