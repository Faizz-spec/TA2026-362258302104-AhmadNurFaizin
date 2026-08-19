<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'

defineOptions({ layout: AppLayout })

// tanggal sekarang
const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

// data layanan asli, tambah warna bg lingkaran icon
const layanan = [
  { nama: "Kesling", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/toilet.png", link: "/mal-sehat/kesling", bg: "#3b82f6" },
  { nama: "Kesga", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/pregnant.png", link: "/mal-sehat/kesga", bg: "#10b981" },
  { nama: "PTM", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/heart-with-pulse--v1.png", link: "/mal-sehat/ptm", bg: "#facc15" },
  { nama: "P3M", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/stethoscope.png", link: "/mal-sehat/p3m", bg: "#14b8a6" },
  { nama: "Yankes primer", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/hospital-room.png", link: "/mal-sehat/yankes-primer", bg: "#f472b6" },
  { nama: "Farmasi", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/pill.png", link: "/mal-sehat/farmasi", bg: "#a78bfa" },
  { nama: "Biakes", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/virus.png", link: "/mal-sehat/biakes", bg: "#fb923c" },
  { nama: "Promkes & PM", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/megaphone.png", link: "/mal-sehat/promkes", bg: "#0ea5e9" },
  { nama: "Home Visit", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/home.png", link: "/mal-sehat/home-visit", bg: "#22d3ee" },
  { nama: "Sehat", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/health-book.png", link: "/mal-sehat/sehat", bg: "#2dd4bf" },
  { nama: "Rapid Test", jumlah: 0, icon: "https://img.icons8.com/ios-filled/50/ffffff/test-tube.png", link: "/mal-sehat/rapid-test", bg: "#fbbf24" },
]
</script>

<template>
  <div class="container my-2">
    <!-- Header dengan gradasi -->
    <div class="p-4 rounded mb-4 text-white" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
      <h1 class="h3 mb-2">MAL SEHAT - JUMLAH PASIEN HARI INI</h1>
      <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-2">
        <i class="fas fa-calendar-alt me-2"></i>
        {{ currentDate }}
      </div>
    </div>

    <!-- Kartu layanan -->
    <div class="row g-4">
      <div 
        class="col-6 col-md-4 col-lg-3" 
        v-for="item in layanan" 
        :key="item.nama"
      >
        <Link :href="item.link" class="text-decoration-none">
          <div class="card shadow h-100">
            <div class="card-body">
              <div 
                class="mb-3 rounded-4 d-flex justify-content-center align-items-center"
                :style="{ width: '60px', height: '60px', backgroundColor: item.bg }"
              >
                <img :src="item.icon" alt="icon" style="width: 32px; height: 32px;" />
              </div>
              <h5 class="card-title mb-2">{{ item.nama }}</h5>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Pasien</small>
                <strong class="fs-4">{{ item.jumlah }}</strong>
              </div>
            </div>
          </div>
        </Link>
      </div>
    </div>
  </div>
</template>
