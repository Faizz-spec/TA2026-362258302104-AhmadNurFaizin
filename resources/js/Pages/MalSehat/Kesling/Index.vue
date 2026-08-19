<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })

function kembali() {
  router.get('/mal-sehat')
}

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const entries = ref([
  { nama: 'Konseling Sanitasi', jumlah: 0 },
  { nama: 'RockPort Calon Jamaah Haji', jumlah: 0 },
  { nama: 'Pengukuran Kebugaran Anak SD', jumlah: 0 },
])

function getIconClass(nama) {
  if (nama.includes('Sanitasi')) {
    return { icon: 'bi bi-shield-check', bg: '#3b82f6' }
  } else if (nama.includes('Kebugaran Anak')) {
    return { icon: 'bi bi-emoji-smile', bg: '#facc15' }
  } else if (nama.includes('Kebugaran')) {
    return { icon: 'bi bi-heart-pulse', bg: '#10b981' }
  }
  return { icon: 'bi bi-info-circle', bg: '#6b7280' }
}

function goToLayanan(nama) {
  if (nama.includes('Sanitasi')) {
    router.get('/mal-sehat/kesling/konseling-sanitasi')
  } else if (nama.includes('Calon Jamaah Haji')) {
    router.get('/mal-sehat/kesling/pengukuran-kebugaran-haji')
  } else if (nama.includes('Anak SD')) {
    router.get('/mal-sehat/kesling/pengukuran-kebugaran-anak')
  }
}
</script>

<template>
    <div class="container my-2">
      <div class="p-4 rounded mb-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <div>
          <h1 class="h4 mb-2">Layanan - Kesehatan Lingkungan</h1>
          <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2">
            <i class="fas fa-calendar-alt me-2"></i>
            {{ currentDate }}
          </div>
        </div>
        <button class="btn btn-light btn-sm" @click="kembali">← Kembali</button>
      </div>

      <div class="row g-4">
        <div 
          class="col-6 col-md-4 col-lg-3"
          v-for="item in entries"
          :key="item.nama"
        >
          <div class="card shadow cursor-pointer" @click="goToLayanan(item.nama)">
            <div class="card-body text-center">
              <div
                class="mb-3 rounded-4 d-flex justify-content-center align-items-center mx-auto"
                :style="{ width: '60px', height: '60px', backgroundColor: getIconClass(item.nama).bg }"
              >
                <i :class="getIconClass(item.nama).icon" class="text-white fs-2"></i>
              </div>
              <h6 class="card-title mb-2">{{ item.nama }}</h6>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Pasien</small>
                <strong class="fs-4">{{ item.jumlah }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
