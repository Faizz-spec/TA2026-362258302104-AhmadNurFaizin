<template>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <div class="row g-4">
            <div class="form-section">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Patogram Melalui Garis Waspada</label>
                    <select class="form-select">
                        <option value="Iya">Iya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Masalah Lain</label>
                    <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Penatalaksanaan Masalah Lain</label>
                    <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Hasilnya</label>
                    <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
                </div>
                <div class="mb-2 text-start">
                <button type="submit" class="btn btn-success px-4 shadow-sm mt-3">
                  <i class="bi bi-save me-1"></i> Simpan Data
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
