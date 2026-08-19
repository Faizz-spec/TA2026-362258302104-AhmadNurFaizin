<template>
  <h5 class="fw-semibold text-success">Pemeriksaan USG</h5>
  <h5 class="text-danger">Ingat untuk form dengan blok abu wajib tidak diisi!!</h5>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Trimester</label>
                <select class="form-select">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                </select>
              </div>
              <h5 class="fw-bold mt-2">Pemeriksaan USG</h5>
              <div class="row mb-1">
                <div class="col-6">
                  <label class="form-label fw-semibold">Gestasional Sac (Gs) Diameter</label>
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="GS Diamater" />
                    <span class="input-group-text">CM</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Crown Rump Length (CRL)</label>
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Crown Rump Length" />
                    <span class="input-group-text">CM</span>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Denyut Jantung Janin (DJJ)</label>
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Denyut Jantung Janin" />
                    <span class="input-group-text">/min</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Usia Kehamilan (USG)</label>
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Usia Kehamilan" />
                    <span class="input-group-text">Week</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal TPHT</label>
                <input type="date" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Letak Janin</label>
                <select class="form-select">
                  <option value="Infrauteri">Infrauteri</option>
                  <option value="Exfrauteri">Exfrauteri</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Taksiran Persalinan</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Biparietal Diameter (BPD)</label>
                <div class="input-group">
                  <input type="number" disabled class="form-control" />
                  <span class="input-group-text">CM</span>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Head Circumference (HC)</label>
                <div class="input-group">
                  <input type="number" disabled class="form-control" />
                  <span class="input-group-text">CM</span>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Abdominal Circumference (AC)</label>
                <div class="input-group">
                  <input type="number" disabled class="form-control" />
                  <span class="input-group-text">CM</span>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Femur Length (FL)</label>
                <div class="input-group">
                  <input type="number" disabled class="form-control" />
                  <span class="input-group-text">CM</span>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Berat Janin (USG)</label>
              <div class="input-group">
                <input type="number" disabled class="form-control" />
                <span class="input-group-text">G</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Janin</label>
              <select disabled class="form-select">
                <option value="Normal">Normal</option>
                <option value="Tidak Normal">Tidak Normal</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Deskripsi Janin</label>
              <textarea
                disabled
                class="form-control"
                rows="3"
                placeholder="Masukkan Deskripsi"
              ></textarea>
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
