<template>
  <div class="card shadow-sm border-0 p-3">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Waktu Ke</label>
                  <select class="form-select">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Waktu</label>
                  <input type="time" class="form-control" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Tekanan Darah</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">mmHg</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Nadi</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">/menit</span>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Tinggi Fundus Urteri</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">ml</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Pendarahan</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">ml</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Kontraksi Uterus</label>
                <input type="text" class="form-control" placeholder="jelaskan" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Kandung Kemih</label>
                <input type="text" class="form-control" placeholder="jelaskan" />
              </div>
              <div class="mb-2 text-end">
                <button type="submit" class="btn btn-success px-4 w-100 shadow-sm mt-2">
                  <i class="bi bi-save me-1"></i> Simpan Data
                </button>
              </div>
            </div>
          </div>
          <table class="table">
            <thead>
              <td class="fw-semibold">Jam Ke</td>
              <td class="fw-semibold">Waktu</td>
              <td class="fw-semibold">Tekanan Darah</td>
              <td class="fw-semibold">Nadi</td>
              <td class="fw-semibold">Tinggi Fundus</td>
              <td class="fw-semibold">Kontraksi Uterus</td>
              <td class="fw-semibold">Kandung Kemih</td>
              <td class="fw-semibold">Pendarahan</td>
              <td class="fw-semibold">Aksi</td>
            </thead>
            <tr>
              <td>1</td>
            </tr>
          </table>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Masalah Kala IV</label>
                <textarea class="form-control" rows="3" placeholder="Enter..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Penatalaksanaan Masalah</label>
                <textarea class="form-control" rows="3" placeholder="Enter..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Hasilnya</label>
                <textarea class="form-control" rows="3" placeholder="Enter..."></textarea>
              </div>
              <div class="mb-2 text-start">
                <button type="button" class="btn btn-success w-100 px-4 shadow-sm mt-2">
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
