<template>
  <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-success mb-0">Form Pengiriman Data Bayi / Balita</h5>
  </div>
  <div class="card shadow-sm border-0 p-4 form-card">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="col-md-6">
          <div class="form-section">
            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal Lahir</label>
              <input type="date" class="form-control" />
            </div>
            <div class="row mb-3">
              <div class="col-4">
                <label class="form-label fw-bold">Jam</label>
                <input type="number" class="form-control" placeholder="00" />
              </div>
              <div class="col-4">
                <label class="form-label fw-bold">Menit</label>
                <input type="number" class="form-control" placeholder="00" />
              </div>
              <div class="col-4">
                <label class="form-label fw-bold">Second</label>
                <input type="number" class="form-control" placeholder="00" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Kab / Kota Kelahiran</label>
              <select class="form-select">
                <option value="Banyuwangi">Banyuwangi</option>
                <option value="Jember">Jember</option>
                <option value="Denpasar">Denpasar</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Jenis Kelamin</label>
              <select class="form-select">
                <option value="1">Laki-laki</option>
                <option value="0">Perempuan</option>
              </select>
            </div>
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-bold">Anak Ke</label>
                <input type="number" class="form-control" />
              </div>
              <div class="col-6">
                <label class="form-label fw-bold">Gestasi</label>
                <div class="input-group">
                  <input type="number" class="form-control" />
                  <span class="input-group-text">Week</span>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="mt-2 btn btn-success px-4 shadow-sm w-50">
                <i class="bi bi-save me-1"></i> Simpan Data
              </button>
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
    console.log('Data Obstetri:', form.value);
    alert('Data Obstetri berhasil disimpan!');
  };
</script>
<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
