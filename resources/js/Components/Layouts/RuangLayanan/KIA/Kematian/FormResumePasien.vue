<template>
  <div class="row mb-2">
    <div class="col-md-6">
      <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
        <h5 class="fw-semibold text-success mb-1">Resumen Medis Kematian Maternal dan Perinatal</h5>
      </div>
    </div>
    <div class="col-md-6">
      <div
        v-for="data in DataPasien"
        :key="data.ID"
        class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center"
      >
        <h5 class="fw-semibold text-success mb-1">Tanggal Kunjungan: {{ data.tglKunjungan }}</h5>
      </div>
    </div>
  </div>
  <div class="d-flex gap-3 flex-wrap">
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeKematian === 'laporanKematian' }"
      @click.prevent="toggleForm('laporanKematian')"
    >
      <div class="action-label">Laporan Kematian</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeKematian === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-label">Assessment</div>
    </a>
  </div>
  <hr />
  <div class="mt-3">
    <div v-if="activeResumeKematian === 'laporanKematian'" class="p-2">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Laporan Kematian Ibu</h5>
            <hr class="mb-3" />
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="2" class="text-center text-muted">Data belum diisi Nakes</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Laporan Kematian Bayi Lahir Mati</h5>
            <hr class="mb-3" />
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="2" class="text-center text-muted">Data belum diisi Nakes</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="row mt-4">
            <div class="col-auto">
              <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
            </div>
            <div class="col-auto">
              <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Laporan Kematian Bayi Lahir Hidup</h5>
            <hr class="mb-3" />
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="2" class="text-center text-muted">Data belum diisi Nakes</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div v-if="activeResumeKematian === 'assessment'" class="p-2">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Diagnosa Medis</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="8" class="text-center text-muted">Tidak ada Data assessment</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card shadow-sm border-0 p-2">
            <div class="card-body">
              <p class="fw-semibold text-danger">
                Info : Jika muncul warna kuning di daftar diagnosa pasien, mohon cek kembali. Karena
                warna kuning menandakan ada diagnosa KLB. Jika salah input, mohon diperbaiki. Jika
                memang diagnosa nya bisa diabaikan. syarat warna kuning di daftar diagnosa, "Jika
                Diagnosa KLB dan Kasus Baru"
              </p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-auto">
              <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
            </div>
            <div class="col-auto">
              <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama Diagnosa Kasus Keperawatan</th>
                <th>Keterangan</th>
                <th>Poli</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada Data assessment</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, watch } from 'vue';

  const props = defineProps({
    DataPasien: Array,
  });
  const activeResumeKematian = ref(
    localStorage.getItem('activeResumeKematian') || 'laporanKematian'
  );

  // Simpan kembali jika user ganti tab
  watch(activeResumeKematian, (val) => {
    localStorage.setItem('activeResume', val);
  });
  const toggleForm = (form) => {
    activeResumeKematian.value = form;
  };
</script>
<style scoped>
  .action-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 8px;
    background: #fbfbfc;
    color: #10b981;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
  }

  .action-card:hover {
    background: #e9f2ff;
    color: #10b981;
  }

  .active-card {
    background: #10b981;
    color: #fff;
  }

  .action-label {
    font-weight: 500;
  }
</style>
