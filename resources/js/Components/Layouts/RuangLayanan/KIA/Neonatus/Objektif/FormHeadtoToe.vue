<template>
  <div class="card shadow-sm p-4 border-0 form-card">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-2">
                <label class="form-label fw-bold">Kulit</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Mata</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Mulut</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Kaki</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Reflek</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-2">
                <label class="form-label fw-bold">Perut</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Punggung</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Lubang Anus</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Alat Kelamin</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Tangan</label>
                <select class="form-select">
                  <option value="1">Dalam Batas Normal</option>
                  <option value="2">Abnormal</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">Deskripsi</label>
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
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
