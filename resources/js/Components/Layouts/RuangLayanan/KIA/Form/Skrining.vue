<template>
  <form @submit.prevent="saveSkrining">
    <div class="row g-4 mb-3">
      <div class="col-md-6">
        <div class="card shadow-sm p-2 border-0">
          <div class="card-body">
            <div class="mb-3">
              <label for="skrining" class="form-label fw-semibold">Skrining</label>
              <select class="form-select">
                <option value="">Skrining Tuberkolosis</option>
                <option value="">Skrining Masalah Kesehatan</option>
                <option value="">Skrining Kesehatan Gigi dan Mulut</option>
                <option value="">Skrining Anemia</option>
                <option value="">Skrining Pre-Eklamsia</option>
              </select>
            </div>
            <div class="mb-2">
              <h5>Berdasarkan Anamnesia</h5>
              <p>
                Gejala Penyakit TB bergantung pada lokasi lesi sehingga dapat menunjukkan
                manifestasi klinis sebagai berikut
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3">
          <div class="card-body">
            <div class="mb-2">
              <label for="batuk2minggu" class="form-label fw-semibold"
                >1. Batuk &ge; 2 Minggu</label
              >
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="batukBerdahak" class="form-label fw-semibold">2. Batuk Berdahak</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="batukBerdahakDarah" class="form-label fw-semibold"
                >3. Batuk Berdahat Dapat Bercampur Darah</label
              >
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="batukNyeri" class="form-label fw-semibold">4. Dapat Disertai Nyeri</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="sesakNafas" class="form-label fw-semibold">5. Sesak Nafas</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3">
          <div class="card-body">
            <div class="mb-2">
              <label for="melaise" class="form-label fw-semibold">1. Melaise</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="penurunanBB" class="form-label fw-semibold"
                >2. Penurunan Berat Badan</label
              >
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="nafsuMakan" class="form-label fw-semibold"
                >3. Menurunnya Nafsu Makan</label
              >
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="menggigil" class="form-label fw-semibold">4. Menggigil</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="demam" class="form-label fw-semibold">5. Demam</label>
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="berkeringat" class="form-label fw-semibold"
                >6. Berkeringat di Malam Hari</label
              >
              <select class="form-select">
                <option>Select...</option>
                <option value="Iya">Iya</option>
                <option value="Tidak">Tidak</option>
              </select>
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-success w-100 px-4 shadow-sm mt-2">
                <i class="bi bi-save me-1"></i> Simpan Data
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
  const saveSkrining = () => {
    alert('Data disimpan!');
  };
</script>
