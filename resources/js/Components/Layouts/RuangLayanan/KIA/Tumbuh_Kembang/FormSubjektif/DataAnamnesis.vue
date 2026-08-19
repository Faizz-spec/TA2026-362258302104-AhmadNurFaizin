<template>
  <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-success mb-0">Pengiriman Data Anamnesis</h5>
  </div>
  <div class="card shadow-sm border-0 p-3">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label for="tanggalAnamnesis" class="form-label fw-semibold"
                  >1. Tanggal dan waktu pemeriksaan Anamnesis</label
                >
                <input type="date" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="keluhanUtama" class="form-label fw-semibold">2. Keluhan Utama</label>
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="keluhanUtama" class="form-label fw-semibold"
                  >3. Riwayat Penyakit Sekarang</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="keluhanUtama" class="form-label fw-semibold"
                  >4. Riwayat Penyakit Sebelumnya</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="keluhanUtama" class="form-label fw-semibold"
                  >5. Riwayat Pengobatan Sebelumnya</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="keluhanUtama" class="form-label fw-semibold">6. Riwayat Alergi</label>
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label for="riwayatKeluarga" class="form-label fw-semibold"
                  >7. Riwayat Keluarga</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="riwayatGayaHidup" class="form-label fw-semibold"
                  >8. Riwayat Gaya Hidup</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="riwatarPsikososial" class="form-label fw-semibold"
                  >9. Riwayat Psikososial</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="pemeriksaanFisik" class="form-label fw-semibold"
                  >10. Pemeriksaan Fisik Sederhana</label
                >
                <select class="form-select">
                  <option>Select...</option>
                  <option>Dalam batas normal</option>
                  <option>Abnormal</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="catatanTambahan" class="form-label fw-semibold"
                  >11. Catatan Tambahan</label
                >
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2 text-start">
                <button type="button" class="btn btn-success w-100 px-4 shadow-sm mt-2">
                  <i class="bi bi-save me-1"></i> Simpan Data
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
