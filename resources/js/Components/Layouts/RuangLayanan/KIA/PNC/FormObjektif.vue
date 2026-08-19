<template>
  <div>
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-success mb-0">Form Pelayanan Nifas</h5>
    </div>
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-section">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Pemeriksaan Nifas Ke</label>
                  <select class="form-select">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                  </select>
                </div>
                <div class="row mb-3">
                  <h6 class="mb-2 form-label fw-semibold">Tekanan Darah (mmHg)</h6>
                  <div class="col-6">
                    <label class="form-label fw-semibold">Sistolik</label>
                    <input type="number" class="form-control" />
                  </div>
                  <div class="col-6">
                    <label class="form-label fw-semibold">Diastolik</label>
                    <input type="number" class="form-control" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-4">
                    <label class="form-label fw-semibold">Nadi</label>
                    <div class="input-group">
                      <input type="number" class="form-control" />
                      <span class="input-group-text">/menit</span>
                    </div>
                  </div>
                  <div class="col-4">
                    <label class="form-label fw-semibold">Suhu</label>
                    <div class="input-group">
                      <input type="number" class="form-control" />
                      <span class="input-group-text">&deg;C</span>
                    </div>
                  </div>
                  <div class="col-4">
                    <label class="form-label fw-semibold">Pernapasan</label>
                    <div class="input-group">
                      <input type="number" class="form-control" />
                      <span class="input-group-text">/menit</span>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Pendarahan Pervaginal</label>
                  <input type="number" class="form-control" />
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Jumlah Pendarahan</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">mL</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-section">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Kondisi Payudara</label>
                  <select class="form-select">
                    <option value="" selected>Select...</option>
                    <option value="1">Sweelling breast</option>
                    <option value="2">Dischard from nipple</option>
                    <option value="3">Pain of brest</option>
                    <option value="4">Breast normal</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="kontraksiUteri" class="form-label fw-semibold">Kontraksi Uteri</label>
                  <select class="form-select">
                    <option value="ya">Iya</option>
                    <option value="tidak">Tidak</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="warnaLokhia" class="form-label fw-semibold">Warna Lokhia</label>
                  <select class="form-select">
                    <option value="" selected>Select...</option>
                    <option value="1">Merah</option>
                    <option value="2">Kecoklatan</option>
                    <option value="3">Kuning</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="produksiASI" class="form-label fw-semibold">Produksi ASI</label>
                  <select class="form-select">
                    <option value="" selected>Select...</option>
                    <option value="1">Produksi ASI ada</option>
                    <option value="2">Produksi ASI ada tapi sedikit</option>
                    <option value="3">Produksi ASI tidak ada</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="konseling" class="form-label fw-semibold"
                    >Konseling Perawatan Bayi</label
                  >
                  <select class="form-select">
                    <option value="" selected>Select...</option>
                    <option value="1">Completed</option>
                    <option value="2">not Done</option>
                  </select>
                </div>
                <div class="mb-2 text-end">
                  <button
                    type="button"
                    @click="saveForm"
                    class="btn btn-success px-4 shadow-sm mt-2"
                  >
                    <i class="bi bi-save me-1"></i> Simpan Data
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue';
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
