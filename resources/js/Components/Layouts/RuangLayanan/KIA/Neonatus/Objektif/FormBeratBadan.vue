<template>

      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-2">
                <label class="form-label fw-semibold"
                  >Form Pemeriksaan Berat Badan dan Pemberian ASI</label
                >
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="1">Iya</option>
                  <option value="2">Tidak</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">BB / U</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="sangatKurang">Berat Badan Sangat Kurang</option>
                  <option value="underweight">Under weight</option>
                  <option value="normalweight">Normal weight</option>
                  <option value="resikoOverweight">Resiko Berat Badan Lebih</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-2">
                <label class="form-label fw-semibold">PB/U dan TB/U</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="1">Very Short</option>
                  <option value="2">Short Stature for Age</option>
                  <option value="3">Body Height Normal for Age</option>
                  <option value="4">Tall for Age</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">BB/PB dan BB/TB</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="GiziBuruk">Gizi Buruk</option>
                  <option value="Undernourished">Undernourished</option>
                  <option value="WellNourished">Well nourished</option>
                  <option value="RisikoGiziLebih">Risiko Gizi Lebih</option>
                  <option value="Overweight">Overweight</option>
                  <option value="Obese">Obese</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Deskripsi</label>
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
<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
