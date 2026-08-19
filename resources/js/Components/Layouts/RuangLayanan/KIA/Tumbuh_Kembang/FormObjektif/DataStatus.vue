<template>
  <h6 class="mb-3 fw-semibold text-success">
    Pengiriman Data Status Pertumbuhan dan Perkembangan, serta Data Lainnya
  </h6>
  <div class="col-md-6">
    <form @submit.prevent="saveForm">
      <div class="row g-4">
        <div class="form-section">
          <div class="mb-3">
            <label for="statusPertumbuhan" class="form-label fw-semibold"
              >1. Status Pertumbuhan</label
            >
            <select class="form-select">
              <option value="">Select</option>
              <option value="Sesuai Usia">Sesuai Usia</option>
              <option value="Tidak Sesuai Usia"></option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi...."></textarea>
          </div>
          <div class="mb-3">
            <label for="statusPerkembangan" class="form-label fw-semibold"
              >2. Status Perkembangan</label
            >
            <select class="form-select">
              <option value="">Select</option>
              <option value="Sesuai Usia">Sesuai Usia</option>
              <option value="Tidak Sesuai Usia"></option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi...."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">3. Keterangan Tambahan</label>
            <textarea class="form-control" rows="2" ></textarea>
          </div>
          <div class="mb-2 text-start">
            <button type="submit" class="btn btn-success w-100 px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
