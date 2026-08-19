<template>
  <AppLayout title="Farmasi">
    <div class="container py-4">

      <!-- Header -->
      <div class="card shadow-lg mb-4 rounded-3">
        <div class="card-body text-white p-4" style="background-color: #28c2d1; border-radius: 10px 10px 0 0;">
          <h3 class="mb-3 fw-bold">FARMASI - JUMLAH RESEP HARI INI</h3>
          <span class="badge bg-light text-dark px-4 py-2 rounded-pill fs-6">
            {{ today }}
          </span>
        </div>
      </div>

      <!-- Menu Cards -->
      <div class="row g-4">
        <div
          class="col-12 col-sm-6 col-lg-3"
          v-for="menu in menus"
          :key="menu.title"
        >
          <Link :href="menu.link" class="text-decoration-none">
            <div class="card h-100 text-center shadow-sm border-0 hover-card">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="mb-3">
                  <div
                    class="d-inline-flex align-items-center justify-content-center rounded-3 p-3"
                    style="background-color: #28c2d1; width:60px; height:60px;"
                  >
                    <i :class="menu.icon" class="fs-3 text-white"></i>
                  </div>
                </div>
                <h6 class="fw-bold text-dark">{{ menu.title }}</h6>
                <span class="text-muted small">{{ menu.subtitle }}</span>
              </div>
            </div>
          </Link>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { Link } from '@inertiajs/vue3'

const today = new Date().toLocaleDateString('id-ID', {
  weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

const menus = [
  {
    title: 'Master Obat',
    link: '/farmasi/master-obat',
    icon: 'bi bi-capsule',
    subtitle: ''
  },
  {
    title: 'Resep Langsung',
    link: '/farmasi/pengeluaran-langsung',
    icon: 'bi bi-prescription2',
    subtitle: ''
  },
  {
    title: 'Pelayanan Resep dari Poli',
    link: '/farmasi/pelayanan-resep',
    icon: 'bi bi-hospital',
    subtitle: ''
  },
  {
    title: 'Laporan Farmasi',
    link: '/farmasi/laporan',
    icon: 'bi bi-file-earmark-bar-graph',
    subtitle: ''
  }
]
</script>

<style scoped>
.hover-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>
