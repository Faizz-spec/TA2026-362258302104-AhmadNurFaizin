<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

//Entries Layanan
const entries = ref([
  { nama: 'Kesehatan Peduli Remaja', jumlah: 0 },
])

// Tanggal saat ini
const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

// Tombol kembali
function kembali() {
  router.get('/mal-sehat')
}

// Aksi ketika klik layanan
function goToLayanan(nama) {
  if (nama.includes('Remaja')) {
    router.get(route('mal-sehat.promkes.kesehatanpeduliremaja'))
  }
}

// Icon & background per layanan
function getIconClass(nama) {
  if (nama.includes('Remaja')) {
    return { icon: 'bi bi-person-hearts', bg: '#4682B4' } // senada dengan header
  }
  return { icon: 'bi bi-info-circle', bg: '#5A9BD5' } // biru muda
}
</script>

<template>
  <div class="card m-2 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div
      class="card-header d-flex justify-content-between align-items-center p-4 rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #4682B4, #8EB6C3);"
    >
      <div>
        <h1 class="fs-4 m-0 text-white">Layanan - Promosi Kesehatan & PMK</h1>
        <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2 text-white fw-semibold">
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <button
        class="btn bg-white bg-opacity-25 border-0 btn-sm text-white fw-semibold shadow-sm"
        @click="kembali"
      >
        Kembali
      </button>
    </div>

    <!-- Table Card -->
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center table-bordered">
          <thead style="background: #5A9BD5; color: white;">
            <tr>
              <th>Layanan</th>
              <th>Total Pasien</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in entries" :key="item.nama">
              <td class="text-start ps-3">
                <div class="d-flex align-items-center gap-2">
                  <div
                    class="rounded-3 d-flex justify-content-center align-items-center"
                    :style="{ width: '35px', height: '35px', backgroundColor: getIconClass(item.nama).bg }"
                  >
                    <i :class="getIconClass(item.nama).icon" class="text-white fs-6"></i>
                  </div>
                  <span class="fw-bold text-dark">{{ item.nama }}</span>
                </div>
              </td>
              <td class="fw-semibold text-secondary">{{ item.jumlah }}</td>
              <td>
                <button
                  class="btn btn-sm rounded-pill shadow-sm text-white"
                  style="background: linear-gradient(135deg, #4682B4, #5A9BD5);"
                  @click="goToLayanan(item.nama)"
                >
                  Buka
                </button>
              </td>
            </tr>
            <tr v-if="entries.length === 0">
              <td colspan="3" class="text-center text-muted py-3">
                <i class="fas fa-info-circle me-2"></i> Belum ada layanan
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
