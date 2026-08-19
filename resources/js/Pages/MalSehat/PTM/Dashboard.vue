<template>
  <AppLayout title="Dashboard PTM">
    <div class="container-fluid py-4" style="font-family: 'Segoe UI', sans-serif">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h4 class="mb-1">Dashboard PTM</h4>
            <p class="mb-0 text-muted">Statistik awal untuk layanan Penyakit Tidak Menular.</p>
          </div>
          <button class="btn btn-outline-secondary btn-sm" @click="goBack">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </button>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-1 text-primary">0</div>
                <div class="text-muted">Total Skrining PTM</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-1 text-success">0</div>
                <div class="text-muted">Pasien Risiko Tinggi</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-1 text-warning">0</div>
                <div class="text-muted">Konseling Berhenti Merokok</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-1 text-danger">0</div>
                <div class="text-muted">Pasien dengan komorbid</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header fw-bold">Ringkasan PTM</div>
        <div class="card-body">
          <p class="mb-3">
            Halaman ini adalah template dashboard PTM. Tambahkan data dan visualisasi yang
            diperlukan sesuai struktur backend PTM.
          </p>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card border-0 shadow-sm p-3">
                <h6 class="mb-2">Rangkuman</h6>
                <p class="text-muted mb-0">
                  Gunakan halaman ini untuk menampilkan ringkasan skrining PTM, risiko pasien, dan
                  tren kunjungan PTM.
                </p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm p-3">
                <h6 class="mb-2">Aksi Cepat</h6>
                <p class="text-muted mb-0">
                  Integrasikan grafik rekap, tabel kondisi PTM, dan filter tanggal untuk tampilan
                  statistik yang lebih baik.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import { router } from '@inertiajs/vue3';

  defineOptions({ layout: AppLayout });

  function goBack() {
    router.get('/mal-sehat/ptm');
  }
</script>

<style scoped>
  .card .fs-1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
</style>
