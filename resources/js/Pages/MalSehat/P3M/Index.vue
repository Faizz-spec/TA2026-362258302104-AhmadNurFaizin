<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

function kembali() {
  router.get('/mal-sehat')
}

const entries = ref([
  { nama: 'Konseling HIV dan AIDS', jumlah: 0 },
  { nama: 'Konseling LROA', jumlah: 0 },
  { nama: 'Konseling Penyakit TB', jumlah: 0 },
])

function getIconClass(nama) {
  if (nama.includes('HIV')) {
    return { icon: 'bi bi-ribbon', bg: '#ef4444' } // Merah HIV
  } else if (nama.includes('LROA')) {
    return { icon: 'bi bi-capsule-pill', bg: '#3b82f6' } // Biru obat
  } else if (nama.includes('TB')) {
    return { icon: 'bi bi-lungs', bg: '#10b981' } // Hijau paru
  }
  return { icon: 'bi bi-info-circle', bg: '#6b7280' }
}

function goToLayanan(nama) {
  if (nama.includes('HIV')) {
    router.get('/mal-sehat/p3m/konselinghivaids')
  } else if (nama.includes('LROA')) {
    router.get('/mal-sehat/p3m/konselinglroa')
  } else if (nama.includes('TB')) {
    router.get('/mal-sehat/p3m/konselingtb')
  }
}

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})
</script>

<template>
    <div class="container my-2">
      <!-- Header -->
      <div
        class="p-4 rounded mb-4 text-white d-flex justify-content-between align-items-center"
        style="background: linear-gradient(135deg, #ef4444, #10b981);"
      >
        <div>
          <h1 class="h4 mb-2">Layanan - Pencegahan dan Pemberantasan Penyakit Menular</h1>
          <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2">
            <i class="fas fa-calendar-alt me-2"></i>
            {{ currentDate }}
          </div>
        </div>
        <button class="btn btn-light btn-sm" @click="kembali">← Kembali</button>
      </div>

      <!-- Cards -->
      <div class="row g-4">
        <div
          v-for="item in entries"
          :key="item.nama"
          class="col-6 col-md-4 col-lg-3"
        >
          <div
            class="card shadow cursor-pointer"
            @click="goToLayanan(item.nama)"
          >
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
