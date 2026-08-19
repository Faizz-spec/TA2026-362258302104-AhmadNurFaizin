<template>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <div class="row g-4">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Keadaan Ibu</label>
                <select class="form-select">
                  <option value="Sehat">Sehat</option>
                  <option value="Sakit">Sakit</option>
                  <option value="Pendarahan">Pendarahan</option>
                  <option value="Demam">Demam</option>
                  <option value="Kejang">Kejang</option>
                  <option value="Lokhia">Lokhia Berbau</option>
                  <option value="Lainnya">Lain-lain</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Penolong Persalinan</label>
                <select class="form-select">
                  <option value="Keluarga">Keluarga</option>
                  <option value="Dukun">Dukun</option>
                  <option value="Bidan">Bidan</option>
                  <option value="Dokter">Dokter</option>
                  <option value="Dokter Spesialis">Dokter Spesialis</option>
                  <option value="Lainnya">Lain-Lain</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi (Lainnya)</label>
                <textarea class="form-control" rows="2  "></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Cara Persalinan</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Vakum">Vakum</option>
                  <option value="Forceps">Forceps</option>
                  <option value="Sectio Caesaria">Sectio Caesaria</option>
                </select>
              </div>
              <div class="text-start">
                <button type="submit" class="btn btn-success px-4 shadow-sm mt-2">
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
