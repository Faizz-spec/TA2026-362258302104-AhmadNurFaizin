<template>
  <h5 class="fw-semibold text-success">Pemeriksaan Janin</h5>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Denyut Jantung Janin</label>
              <div class="input-group">
                <input type="number" class="form-control" placeholder="{beats}/min" />
                <span class="input-group-text">{beats}/min</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Kendala Terhadap PAP</label>
              <select class="form-select">
                <option value="Masuk Panggul">Masuk Panggul</option>
                <option value="Belum Masuk Panggul">Belum Masuk Panggul</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Tulis Deskripsi..."></textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Taksiran Berat Janin</label>
              <div class="input-group">
                <input type="number" class="form-control" placeholder="Taksiran Berat" />
                <span class="input-group-text">G</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Presentasi</label>
              <select class="form-select">
                <option value="Presentasi Kepala">Presentasi Kepala</option>
                <option value="Presentasi Bokong">Presentasi Bokong</option>
                <option value="Letak Lintang">Letak Lintang</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Tulis Deskripsi..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Abdominal Circumference (AC)</label>
              <input type="text" class="form-control" placeholder="Abdomina Circumference" />
            </div>
            <div class="text-start">
              <button type="submit" class="btn btn-success w-50 fw-semibold">Simpan</button>
            </div>
          </div>
        </div>
        <!-- Tombol Simpan -->
      </form>
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data berhasil disimpan!');
  };
</script>
