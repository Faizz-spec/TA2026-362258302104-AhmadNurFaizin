<template>
  <h6 class="mb-3 fw-semibold text-success">Pengiriman Data Antropometri</h6>
  <form @submit.prevent="saveForm">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="waktuPengukuran" class="form-label fw-semibold"
              >1. Tanggal dan Waktu Pengukuran</label
            >
            <input type="datetime-local" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="bb" class="form-label fw-semibold">2. Berat Badan</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="kenaikanbb" class="form-label fw-semibold"
              >3. Kenaikan Berat Badan Adekuat</label
            >
            <select class="form-select">
              <option value="">Select</option>
              <option value="Iya">Iya</option>
              <option value="Tidak">Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="pb" class="form-label fw-semibold">4. Panjang Badan</label>
            <input type="text" class="form-control" />
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="tb" class="form-label fw-semibold">5. Tinggi Badan</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="lila" class="form-label fw-semibold">6. Lingkar Lengan Atas (LiLA)</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="lk" class="form-label fw-semibold">7. Lingkar Kepala</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="catatan" class="form-label fw-semibold">8. Catatan Tambahan</label>
            <textarea class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-2 text-end">
            <button type="submit" class="btn btn-success px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
