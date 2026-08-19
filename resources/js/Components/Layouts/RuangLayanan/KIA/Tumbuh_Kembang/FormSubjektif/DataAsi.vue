<template>
  <div class="col-md-6 bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-success mb-0">Pengiriman Data Asi</h5>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <div class="mb-3">
            <label for="asiEksklusif" class="form-label fw-semibold"
              >1. Apakah Bayi Dapat Asi Eksklusif</label
            >
            <select class="form-select">
              <option disabled>Select</option>
              <option value="Iya">Iya</option>
              <option value="Tidak">Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="asiEksklusif" class="form-label fw-semibold"
              >2. Apakah Bayi Mendapatkan Makanan Pendamping ASI</label
            >
            <select class="form-select">
              <option disabled>Select</option>
              <option value="Iya">Iya</option>
              <option value="Tidak">Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-2 text-start">
            <button type="button" class="btn btn-success w-100 px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
