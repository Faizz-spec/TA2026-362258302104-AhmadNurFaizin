<template>
  <AppLayouts>
    <div class="container my-4">
      <div
        class="p-4 rounded mb-4 text-white"
        style="background: linear-gradient(135deg, #3b82f6, #10b981)"
      >
        <h1 class="h5 mb-2">
          JUMLAH PASIEN RUANG LAYANAN PUSKESMAS WONGSOREJO HARI INI (KLUSTER 3)
        </h1>
        <div class="bg-white bg-opacity-25 d-inline-block px-3 py-2 rounded-pill mt-3 fs-6">
          <i class="fas fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </div>
      </div>
      <div class="row g-4">
        <div
          class="col-12 col-sm-6 col-lg-4 col-xl-3"
          v-for="poli in dataLayanan"
          :key="poli.kdPoli"
        >
          <Link
            :href="route(poli.link, { idPoli: poli.kdPoli, kluster: '3' })"
            class="text-decoration-none"
          >
            <div class="card shadow">
              <div class="card-body">
                <div
                  class="mb-3 rounded-4 d-flex justify-content-center"
                  :style="{ width: '60px', height: '60px', backgroundColor: poli.bg }"
                >
                  <i :class="poli.icon" class="text-white fs-2"></i>
                </div>
                <h5 class="card-title mb-2 fs-6">{{ poli.nama }}</h5>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">Total Pasien</small>
                  <strong class="fs-5">{{ poli.jumlah }}</strong>
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

  const listPoli = props.listPoli;
  const totalPasienUmum = props.totalPasienUmum;
  const totalPasienKB = props.totalPasienKB;
  const totalPasienPTM = props.totalPasienPTM;
  console.log(listPoli);

  const currentDate = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });

  const dataLayanan = [
    {
      nama: 'Umum',
      jumlah: totalPasienUmum,
      kdPoli: '001',
      link: 'ruang-layanan.index',
      icon: 'bi bi-person-fill',
      bg: '#3b82f6',
    },
    {
      nama: 'KB',
      jumlah: totalPasienKB,
      kdPoli: '008',
      link: 'ruang-layanan.index',
      icon: 'bi bi-people-fill',
      bg: '#3b82f6',
    },
    {
      nama: 'POLI LANSIA',
      kdPoli: '001',
      jumlah: 0,
      link: 'ruang-layanan.index',
      icon: 'bi bi-gender-female',
      bg: '#14b8a6',
    },
    {
      nama: 'Skrining PTM',
      jumlah: totalPasienPTM,
      kdPoli: '006',
      link: 'ruang-layanan.ptm',
      icon: 'bi bi-people-fill',
      bg: '#3b82f6',
    },
  ];
</script>

<style></style>
