<template>
  <h6 class="mb-3 fw-semibold text-success">
    Pengiriman Data SDIDTK (Stimulasi, Deteksi, dan Intervensi Dini Tumbuh Kembang)
  </h6>
  <form @submit.prevent="saveForm">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="sudahdiPeriksa" class="form-label fw-semibold"
              >1. Apakah Sudah di Periksa SDIDTK</label
            >
            <select class="form-select">
              <option disabled>Select</option>
              <option value="Iya">Iya</option>
              <option value="Tidak">Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="kpsp" class="form-label fw-semibold"
              >2. Kuesioner Pra Skrining Perkembangan (KPSP)</label
            >
            <select class="form-select">
              <option>Select</option>
              <option value="Sesuai">Sesuai</option>
              <option value="Meragukan">Meragukan</option>
              <option value="Penyimpangan">Penyimpangan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="tdd" class="form-label fw-semibold">3. Tes Daya Dengar (TDD)</label>
            <select class="form-select">
              <option value="Select">Select</option>
              <option value="Normal">Normal</option>
              <option value="Risiko">Risiko</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="tdl" class="form-label fw-semibold">3. Tes Daya Lihat (TDL)</label>
            <select class="form-select">
              <option value="Select">Select</option>
              <option value="Normal">Normal</option>
              <option value="Risiko">Risiko</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="kmpe" class="form-label fw-semibold"
              >5. Kuesioner Masalah Perilaku dan Emosional (KMPE)</label
            >
            <select class="form-select">
              <option value="Select">Select</option>
              <option value="Normal">Normal</option>
              <option value="Risiko">Risiko</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="mchatr" class="form-label fw-semibold">6. Autisme (M-CHAT-R)</label>
            <select class="form-select">
              <option>Select</option>
              <option value="Risiko Tinggi Autisme">Risiko Tinggi Autisme</option>
              <option value="Risiko Autisme">Risiko Autisme</option>
              <option value="Normal">Normal</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="gpph" class="form-label fw-semibold"
              >7. Gangguan Pemusatan Perhatian dan Hiperaktivitas (GPPH)</label
            >
            <select class="form-select">
              <option value="Select">Select</option>
              <option value="Normal">Normal</option>
              <option value="Risiko">Risiko</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="catatan" class="form-label fw-semibold">8. Catatan Tambahan</label>
            <textarea class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-2 text-start">
            <button type="submit" class="btn btn-success w-100 px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
