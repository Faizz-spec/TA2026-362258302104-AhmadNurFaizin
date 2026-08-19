<template>
  <div class="card shadow-sm p-3 border-1">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Lahir</label>
                <input type="date" class="form-control" />
              </div>
              <label class="form-label fw-semibold">Jam Lahir</label>
              <div class="card mb-3 shadow-sm p-2 border-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-4">
                      <label class="form-label">Jam</label>
                      <input type="number" class="form-control" placeholder="00" />
                    </div>
                    <div class="col-4">
                      <label class="form-label">Menit</label>
                      <input type="number" class="form-control" placeholder="00" />
                    </div>
                    <div class="col-4">
                      <label class="form-label">Detik</label>
                      <input type="number" class="form-control" placeholder="00" />
                    </div>
                  </div>
                </div>
              </div>
              <label class="form-label fw-semibold">Data Bayi Saat Lahir</label>
              <div class="card mb-3 shadow-sm border-0 p-2">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-4">
                      <label class="form-label fw-semibold">Berat Badan</label>
                      <div class="input-group">
                        <input type="number" class="form-control" />
                        <span class="input-group-text">Gram</span>
                      </div>
                    </div>
                    <div class="col-4">
                      <label class="form-label fw-semibold">Panjang Bayi</label>
                      <div class="input-group">
                        <input type="number" class="form-control" />
                        <span class="input-group-text">CM</span>
                      </div>
                    </div>
                    <div class="col-4">
                      <label class="form-label fw-semibold">Lingkar Kepala</label>
                      <div class="input-group">
                        <input type="number" class="form-control" />
                        <span class="input-group-text">CM</span>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-6">
                      <label class="form-label fw-semibold">Golongan Darah</label>
                      <select class="form-select">
                        <option value="">Select..</option>
                        <option value="O">O</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="form-label fw-semibold">Rhesus</label>
                      <select class="form-select">
                        <option value="">Select..</option>
                        <option value="Positif">pos</option>
                        <option value="Negatif">neg</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label fw-semibold">Frekuensi Denyut Nadi</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">/min</span>
                  </div>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Pernapasan</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">/min</span>
                  </div>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Suhu</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">C</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Imunisasi HBIG</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Ibu Hepatitis</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Tanggal Terkena Hepatitis</label>
                  <input type="date" class="form-control" />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Jam</label>
                  <input type="time" class="form-control" />
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Imunisasi Hepatitis B</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Inisiasi Menyusui Dini</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Jika Iya, Waktu Menyusui Dini</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Kurang Dari 1 Jam</option>
                  <option value="Tidak">Lebih Dari 1 Jam</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Apakah Diberikan Resusitas</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Perawatan Tali Pusar</label>
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold"
                  >Skiring Hipotioid Kongenital (Bila belum diberkan)</label
                >
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold"
                  >Apakah dilakukan pemeriksaan kesehatan dengan pendekatan MTBM</label
                >
                <select class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea rows="2" class="form-control"></textarea>
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
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
