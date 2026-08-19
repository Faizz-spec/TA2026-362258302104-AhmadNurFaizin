<template>
  <div class="card shadow-sm p-3 border-1">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Inisiasi Menyusui Dini</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Jika Iya, Waktu Menyusui Dini</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Kurang Dari 1 Jam</option>
                  <option value="Tidak">Lebih Dari 1 Jam</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Perawatan Tali Pusar</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Identifikasi Kuning</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold"
                  >Apakah dilakukan pemeriksaan kesehatan dengan pendekatan MTBM</label
                >
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">PPA</label>
                <textarea rows="2" class="form-control"></textarea>
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
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
