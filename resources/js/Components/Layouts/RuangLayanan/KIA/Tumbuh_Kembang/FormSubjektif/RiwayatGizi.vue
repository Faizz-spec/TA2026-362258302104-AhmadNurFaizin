<template>
  <h6 class="mb-3 fw-semibold text-success">Riwayat Terkait Gizi</h6>
  <form @submit.prevent="saveForm">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="bb" class="form-label fw-semibold">1. Berat Badan</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="tb" class="form-label fw-semibold">2. Tinggi Badan</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="imt" class="form-label fw-semibold">3. Indeks Masa Tubuh (IMT)</label>
            <input type="text" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="statusGizi" class="form-label fw-semibold">4. Status Gizi</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Gizi Baik</option>
              <option>Gizi Kurang</option>
              <option>Gizi Lebih</option>
              <option>Obesitas</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="penyakitGizi" class="form-label fw-semibold"
              >5. Riwayat Penyakit yang Terkait Dengan Gizi</label
            >
            <select class="form-select">
              <option>Select..</option>
              <option>Iya</option>
              <option>Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="penyakitGizi" class="form-label fw-semibold"
              >6. Apakah pasien rutin mengonsumsi suplemen atau vitamin</label
            >
            <select class="form-select">
              <option>Select..</option>
              <option>Iya</option>
              <option>Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="penyakitGizi" class="form-label fw-semibold"
              >7. Jenis suplemen atau vitamin yang dikonsumsi</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Vitamin A</option>
              <option>Vitamin D</option>
              <option>Zat Besi</option>
              <option>Kalsium</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-2 text-start">
            <button type="submit" class="btn btn-success w-100 px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
