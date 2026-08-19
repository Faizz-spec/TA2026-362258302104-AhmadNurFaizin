<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const layananKesga = ref([
  { nama: 'Konseling Catin', file: 'KonselingCatin', jumlah: 0 },
  { nama: 'KonsPel Calon Jamaah Haji', file: 'KonselingHaji', jumlah: 0 },
  { nama: 'KonsPel Imunisasi', file: 'KonselingImunisasi', jumlah: 0 },
  { nama: 'Konseling Kesehatan Bayi dan Anak', file: 'KonselingAnak', jumlah: 0 },
  { nama: 'Konseling Kesehatan Ibu', file: 'KonselingIbu', jumlah: 0 },
  { nama: 'Konseling Kesehatan KB', file: 'KonselingKB', jumlah: 0 },
  { nama: 'KonGiz Stunting', file: 'KonsultasiGizi', jumlah: 0 },
  { nama: 'Konsultasi Kesehatan Lansia', file: 'KonsultasiLansia', jumlah: 0 },
])

function navigateToLayanan(file) {
  router.get(`/mal-sehat/kesga/${file.toLowerCase()}`)
}

function kembali() {
  router.get('/mal-sehat')
}

function getIconClass(nama) {
  if (nama.includes('Catin')) {
    return { icon: 'bi bi-heart-fill', bg: '#ec4899' }
  } else if (nama.includes('Haji')) {
    return { icon: 'bi bi-moon-stars', bg: '#f59e0b' }
  } else if (nama.includes('Imunisasi')) {
    return { icon: 'bi bi-syringe', bg: '#3b82f6' }
  } else if (nama.includes('Bayi') || nama.includes('Anak')) {
    return { icon: 'bi bi-emoji-smile', bg: '#fbbf24' }
  } else if (nama.includes('Ibu')) {
    return { icon: 'bi bi-person-heart', bg: '#f472b6' }
  } else if (nama.includes('KB')) {
    return { icon: 'bi bi-people', bg: '#10b981' }
  } else if (nama.includes('Gizi')) {
    return { icon: 'bi bi-basket-fill', bg: '#84cc16' }
  } else if (nama.includes('Lansia')) {
    return { icon: 'bi bi-person', bg: '#6b7280' }
  }
  return { icon: 'bi bi-info-circle', bg: '#6b7280' }
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
        style="background: linear-gradient(135deg, #f59e0b, #ec4899);"
      >
        <div>
          <h1 class="h4 mb-2">Layanan - Kesehatan Keluarga</h1>
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
          class="col-6 col-md-4 col-lg-3"
          v-for="item in layananKesga"
          :key="item.file"
        >
          <div
            class="card shadow cursor-pointer"
            @click="navigateToLayanan(item.file)"
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
