<template>
  <div class="col-12 d-flex align-items-center">
    <label class="col-md-2 fw-bold">Laporan</label>
    <div class="col-md-4">
      <div class="alert alert-info py-1 px-2" v-if="jenis">
        Laporan: {{ jenis }}
      </div>
    </div>
  </div>

  <div class="col-12 mt-3" />

  <!-- Render komponen laporan -->
  <div v-if="selectedComponent">
    <component :is="selectedComponent" :data-kunjungan="dataKunjungan" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { defineProps } from 'vue'

// Props dari Inertia Laravel
const props = defineProps({
  jenis: String,
  dataKunjungan: Array,
})

// Import komponen laporan
import RegisterKunjunganPasien from './Components-laporan-loket/Register-kunjungan-pasien.vue'
import RegisterKunjunganPasienSehat from './Components-laporan-loket/Register-kunjungan-pasien-sehat.vue'
import LaporanBulananKunjungan from './Components-laporan-loket/LaporanBulananKunjungan.vue'

// Mapping jenis → komponen
const laporanComponents = {
  'register-kunjungan': RegisterKunjunganPasien,
  'register-sehat': RegisterKunjunganPasienSehat,
  'laporan-bulanan': LaporanBulananKunjungan,
}

const selectedComponent = computed(() => {
  return laporanComponents[props.jenis] || null
})

const dataKunjungan = computed(() => props.dataKunjungan)
</script>
