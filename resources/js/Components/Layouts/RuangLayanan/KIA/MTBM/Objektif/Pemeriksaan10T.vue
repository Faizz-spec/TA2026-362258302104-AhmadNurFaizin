<template>
  <h5 class="fw-semibold text-success">Pemeriksaan 10T</h5>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Berat Janin (USG)</label>
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Masukkan Nilai" />
                <span class="input-group-text">g/dl</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">HIV Test</label>
              <select class="form-select">
                <option value="Reaktif">Reaktif</option>
                <option value="Non Reaktif">Non Reaktif</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi Reaktif</label>
              <textarea rows="3" class="form-control" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Syphilis Test</label>
              <select class="form-select">
                <option value="Reaktif">Reaktif</option>
                <option value="Non Reaktif">Non Reaktif</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi Syphilis</label>
              <textarea rows="3" class="form-control" placeholder="Masukkan Deskripsi"></textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Hepatitis Test</label>
              <select class="form-select">
                <option value="Reaktif">Reaktif</option>
                <option value="Non Reaktif">Non Reaktif</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi Hepatitis</label>
              <textarea rows="3" class="form-control" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Pemeriksaan Gula Darah</label>
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Masukkan Nilai" />
                  <span class="input-group-text">mg/dl</span>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Pemeriksaan Protein Urine</label>
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Masukkan Nilai" />
                  <span class="input-group-text">mg/dl</span>
                </div>  
              </div>
            </div>
            <div class="mt-2 text-start">
              <button type="submit" class="btn btn-success w-50 fw-semibold">Simpan</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data berhasil disimpan!');
  };
</script>
