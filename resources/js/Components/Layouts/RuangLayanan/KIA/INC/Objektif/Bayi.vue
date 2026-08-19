<template>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-3">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <div class="row g-4">
            <div class="form-section">
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Berat Bayi</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">Gram</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Panjang Bayi Lahir</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">CM</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Interpretasi Berat Bayi</label>
                <select class="form-select">
                  <option value="" disabled selected>Choose...</option>
                  <option value="276613009">BBLB (Bayi Berat Lahir Besar) [>=4000gr]</option>
                  <option value="276712009">
                    BBLC (Bayi Berat Lahir Cukup) [2500gr s/d 3999gr]
                  </option>
                  <option value="276610007">
                    BBLR (Bayi Berat Lahir Rendah) [1500gr s/d 2499gr]
                  </option>
                  <option value="276611006">
                    BBLSR (Bayi Berat Lahir Sangat Rendah) [1000gr s/d 1499gr]
                  </option>
                  <option value="276612004">
                    BLASR (Bayi Berat Lahir Amat Sangat Rendah) [< 1000gr]
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Lokasi Persalinan</label>
                <select class="form-select">
                  <option value="264362003">Rumah</option>
                  <option value="OT000001">Polindes</option>
                  <option value="OT000002">Pustu</option>
                  <option value="104">Rumah Sakit Ibu dan Anak</option>
                  <option value="102">Puskesmas</option>
                  <option value="103">Klinik</option>
                  <option value="OT000008">Rumah Bersalin</option>
                  <option value="82242000">Rumah Sakit Ibu dan Anak</option>
                  <option value="OT000003">RS ODHA</option>
                  <option value="OT000004">Bidan Praktek Mandiri</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Inisiasi Menyusui Dini</label>
                <select v-model="inisiasiMenyusui" class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Iya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3" v-if="inisiasiMenyusui === 'Iya'">
                <label class="form-label fw-semibold">Jika Iya, Waktu Menyusui Dini</label>
                <select v-model="waktuMenyusui" class="form-select">
                  <option value="">Select...</option>
                  <option value="Iya">Kurang Dari 1 Jam</option>
                  <option value="Tidak">Lebih Dari 1 Jam</option>
                </select>
              </div>
              <div class="mb-2 text-center">
                <button type="submit" class="btn btn-success px-4 shadow-sm mt-3">
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
  import { ref } from 'vue';

  const inisiasiMenyusui = ref('');
  const waktuMenyusui = ref('');
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
