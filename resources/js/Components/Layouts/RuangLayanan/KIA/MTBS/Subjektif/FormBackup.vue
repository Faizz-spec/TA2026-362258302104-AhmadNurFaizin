<template>
  <h5 class="fw-semibold text-danger">Pemantauan dan Pendampingan</h5>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label d-block"
                  >1. Terlalu muda usia melahirkan dibawah 20 tahun</label
                >
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label d-block"
                  >2. Terlalu rapat jarak kelahiran (< 2 tahun)
                </label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label d-block"
                  >3. Terlalu tua (kehamilan di atas 35 tahun)
                </label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label d-block">4. Terlalu sering melahirkan (anak > 3) </label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="usiamuda" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>

              <h5 class="fw-bold mb-2">A. Complikasi Penyulit Kehamilan</h5>
              <label class="form-label fw-semibold d-block">Complikasi</label>
              <div class="row mb-3">
                <div class="col-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder=""
                    disabled
                    v-model="form.kode_complikasi"
                  />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      disabled
                      v-model="form.nama_complikasi"
                    />
                    <button type="button" class="btn btn-info" @click="showModal = true">
                      Cari
                    </button>

                    <button type="button" class="btn btn-danger" @click="hapusForm">Del</button>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi Komplikasi</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi Komplikasi"
                ></textarea>
              </div>
              <label class="form-label fw-semibold d-block"
                >B. Riwayat Penyakit Menular Pribadi</label
              >
              <div class="row mb-3">
                <div class="col-3">
                  <input type="text" class="form-control" placeholder="" disabled />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light" disabled />
                    <button type="button" class="btn btn-info" @click="showModal = true">
                      Cari
                    </button>

                    <button type="button" class="btn btn-danger" @click="hapusForm">Del</button>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Sumber</label>
                <select class="form-select">
                  <option value="NonClinic">Non Clinic</option>
                  <option value="Clinic">Clinic</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi Komplikasi"
                ></textarea>
              </div>
              <label class="form-label fw-semibold d-block">C. Riwayat Penyakit Keluarga</label>
              <div class="row mb-3">
                <div class="col-3">
                  <input type="text" class="form-control" placeholder="" disabled />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light" disabled />
                    <button type="button" class="btn btn-info" @click="openModal()">Cari</button>

                    <button type="button" class="btn btn-danger">Del</button>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Sumber</label>
                <select class="form-select">
                  <option value="NonClinic">Non Clinic</option>
                  <option value="Clinic">Clinic</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi Komplikasi"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label">Status Merokok</label>
                <select class="form-select">
                  <option value="Aktif">Aktif</option>
                  <option value="Pasif">Pasif</option>
                  <option value="Pasif">Tidak Merokok</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Status Alkohol</label>
                <select class="form-select">
                  <option value="Aktif">Aktif</option>
                  <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Apakah Disabilitas</label>
                <select class="form-select">
                  <option value="Aktif">Aktif</option>
                  <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Apakah Mengikuti Kelas Hamil</label>
                <select class="form-select">
                  <option value="Ya">Ya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
            </div>
            <div class="mt-1 text-end">
              <button type="submit" class="btn btn-success px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Data
              </button>
            </div>
          </div>

          <!-- Tombol Simpan -->
        </div>
      </form>
    </div>

    <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5)">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header d-flex justify-content-between align-items-center">
            <h5>Pilih Diagnosa</h5>
            <button class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body">
            <input
              type="text"
              v-model="keyword"
              class="form-control mb-3"
              placeholder="Cari diagnosa..."
            />
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>KODE</th>
                  <th>NAMA</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in diagnosa" :key="item.id">
                  <td>{{ item.kdDiag }}</td>
                  <td>{{ item.nmDiag }}</td>
                  <td>
                    <button class="btn btn-info btn-sm" @click="pilihDiagnosa(item)">Pilih</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';

  const showModal = ref(false);
  const keyword = ref('');

  // Form utama
  const form = ref({
    kode_complikasi: '',
    nama_complikasi: '',
  });

  const props = defineProps({
    diagnosa: Array,
  });

  const pilihDiagnosa = (item) => {
    form.value.kode_complikasi = item.kdDiag;
    form.value.nama_complikasi = item.nmDiag;
    showModal.value = false;
  };

  const hapusForm = () => {
    form.value.kode_complikasi = '';
    form.value.nama_complikasi = '';
  };

  const simpanDiagnosaMedis = () => {
    daftarDiagnosaMedis.value.push({ ...form.value });
    alert('Diagnosa Medis Disimpan');
  };

  const hapusDiagnosa = (index) => {
    daftarDiagnosaMedis.value.splice(index, 1);
  };

  const saveForm = () => {
    alert(`Data disimpan!`);
  };
</script>

<style scoped>
  .form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 16px;
  }
  .form-label {
    font-weight: 600;
    color: #333;
  }
</style>
