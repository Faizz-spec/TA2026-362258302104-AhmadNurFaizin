<template>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <h6 class="mb-3 fw-semibold">Data Kunjungan Persalinan</h6>
        <form @submit.prevent="saveForm">
          <div class="row g-4">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Usia Kehamilan</label>
                <div class="input-group">
                  <input type="number" class="form-control" />
                  <span class="input-group-text">Minggu</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Tanggal Persalinan</label>
                  <input type="date" class="form-control" />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Jam</label>
                  <input type="time" class="form-control" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label fw-semibold">Gravida</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Partus</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Abortus</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
              </div>
              <div class="mb-2 text-start">
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
