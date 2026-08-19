<template>
  <div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
      <h5 class="fw-semibold text-success">Pemantauan dan Pendampingan</h5>
    </div>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <!-- FORM UTAMA -->
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
                  <input class="form-check-input" type="radio" name="rapat-jarak" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="rapat-jarak" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label d-block"
                  >3. Terlalu tua (kehamilan di atas 35 tahun)
                </label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="terlalu-tua" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="terlalu-tua" value="tidak" />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label d-block">4. Terlalu sering melahirkan (anak > 3) </label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="terlalu-sering" value="iya" />
                  <label class="form-check-label" for="yes">Iya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="terlalu-sering"
                    value="tidak"
                  />
                  <label class="form-check-label" for="tidak">Tidak</label>
                </div>
              </div>

              <!-- Bagian Complikasi -->
              <h5 class="fw-bold mb-2">A. Komplikasi Penyulit Kehamilan</h5>

              <label class="form-label fw-semibold d-block">Komplikasi</label>
              <div class="row mb-3">
                <div class="col-3">
                  <input type="text" class="form-control" disabled v-model="form.kode_complikasi" />
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
                  <input type="text" class="form-control" disabled v-model="form.kode_riwayat" />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      disabled
                      v-model="form.nama_riwayat"
                    />
                    <button type="button" class="btn btn-info" @click="showModal1 = true">
                      Cari
                    </button>
                    <button type="button" class="btn btn-danger" @click="hapusRiwayat">Del</button>
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
                <label class="form-label fw-semibold">Deskripsi Komplikasi</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi Komplikasi"
                ></textarea>
              </div>
              <label class="form-label fw-semibold d-block">C. Riwayat Penyakit Keluarga</label>
              <div class="row mb-3">
                <div class="col-3">
                  <input
                    type="text"
                    class="form-control"
                    disabled
                    v-model="form.kode_riwayat_kel"
                  />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      disabled
                      v-model="form.nama_riwayat_kel"
                    />
                    <button type="button" class="btn btn-info" @click="showModal2 = true">
                      Cari
                    </button>
                    <button type="button" class="btn btn-danger" @click="hapusRiwayatKel">
                      Del
                    </button>
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
                <label class="form-label fw-semibold">Deskripsi Komplikasi</label>
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
                <label class="form-label fw-semibold">Status Merokok</label>
                <select class="form-select">
                  <option value="Aktif">Aktif</option>
                  <option value="Pasif">Pasif</option>
                  <option value="Tidak Merokok">Tidak Merokok</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi Status Merokok"
                ></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Status Alkohol</label>
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
                <label class="form-label fw-semibold">Apakah Disabilitas</label>
                <select class="form-select">
                  <option value="Aktif">Aktif</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Apakah Mengikuti Kelas Hamil</label>
                <select class="form-select">
                  <option value="Ya">Ya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan Deskripsi"></textarea>
              </div>
              <div class="mb-2">
                <button type="submit" class="btn btn-success px-4 shadow-sm mt-4">
                  <i class="bi bi-save me-1"></i> Simpan Data
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- MODAL PILIH DIAGNOSA -->
    <DiagnosaModal
      :show="showModal"
      :diagnosa="diagnosa"
      @close="showModal = false"
      @select="pilihDiagnosa"
    />

    <!-- Modal Pilih Riwayat Pribadi -->
    <DiagnosaModal
      :show="showModal1"
      :diagnosa="diagnosa"
      @close="showModal1 = false"
      @select="pilihRiwayat"
    />

    <DiagnosaModal
      :show="showModal2"
      :diagnosa="diagnosa"
      @close="showModal2 = false"
      @select="pilihRiwayatKel"
    />
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import DiagnosaModal from '../../DiagnosaModal.vue';

  // State utama
  const showModal = ref(false);
  const showModal1 = ref(false);
  const showModal2 = ref(false);
  const keyword = ref('');
  const form = ref({
    kode_complikasi: '',
    kode_riwayat: '',
    kode_riwayat_kel: '',
    nama_complikasi: '',
    nama_riwayat_kel: '',
  });

  // Props dari Laravel
  const props = defineProps({
    diagnosa: Array,
    riwayat: Array,
  });

  // Pilih diagnosa dari modal
  const pilihDiagnosa = (item) => {
    form.value.kode_complikasi = item.kdDiag;
    form.value.nama_complikasi = item.nmDiag;
    showModal.value = false;
  };

  // Reset form
  const hapusForm = () => {
    form.value.kode_complikasi = '';
    form.value.nama_complikasi = '';
  };

  // Pilih diagnosa dari modal
  const pilihRiwayat = (item) => {
    form.value.kode_riwayat = item.code;
    form.value.nama_riwayat = item.value_set;
    showModal1.value = false;
  };

  // Reset form
  const hapusRiwayat = () => {
    form.value.kode_riwayat = '';
    form.value.nama_riwayat = '';
  };

  const pilihRiwayatKel = (item) => {
    form.value.kode_riwayat_kel = item.code;
    form.value.nama_riwayat_kel = item.value_set;
    showModal2.value = false;
  };

  // Reset form
  const hapusRiwayatKel = () => {
    form.value.kode_riwayat_kel = '';
    form.value.nama_riwayat_kel = '';
  };

  // Simpan form
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>

<style scoped>
  .form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 16px;
  }
  .table th,
  .table td {
    font-size: 0.9rem;
  }
  .pagination .page-link {
    cursor: pointer;
  }
</style>
