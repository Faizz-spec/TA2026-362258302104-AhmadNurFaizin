<template>
  <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-success mb-0">Pengiriman Data Lokasi Kematian</h5>
  </div>


      <!-- FORM UTAMA -->
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <div class="form-section bg-white bg-opacity-10 shadow-sm">
              <div class="mb-3">
                <label class="form-label d-block fw-semibold">Tempat Meninggal</label>
                <select class="form-select">
                  <option value="Tempat Meninggal Faskes">Tempat Meninggal Faskes</option>
                  <option value="Tempat Meninggal Non Faskes">Tempat Meninggal Non Faskes</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Alamat Kematian</label>
                <textarea class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <h6 class="fw-bold">Riwayat Kematian Ibu</h6>
                <label class="form-label fw-semibold">Umur Saat Meninggal</label>
                <input type="number" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal dan Jam Kematian</label>
                <div class="row">
                  <div class="col-6">
                    <input type="date" class="form-control" />
                  </div>
                  <div class="col-6">
                    <input type="time" class="form-control" />
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Masa Terjadi Kematian Ibu</label>
                <select class="form-select">
                  <option value="Saat Hamil">Saat Hamil</option>
                  <option value="Saat Melahirkan">Saat Melahirkan</option>
                  <option value="Nifas / Pasca Keguguran">Nifas / Pasca Keguguran</option>
                </select>
              </div>

              <div class="row mb-3">
                <label class="form-label fw-semibold">Code Dugaan Kematian Ibu</label>
                <div class="col-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Kode"
                    disabled
                    v-model="form.kode_complikasi"
                  />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      placeholder="Nama Diagnosa"
                      disabled
                      v-model="form.nama_complikasi"
                    />
                    <button type="button" class="btn btn-info" @click="showModal = true">Cari</button>
                    <button type="button" class="btn btn-danger" @click="hapusForm">Del</button>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
            <div class="form-section bg-white shadow-sm">
              <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Tempat Meninggal</label>
                <select class="form-select">
                  <option value="RS Pemerintah">RS Pemerintah</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi (Jika Lainnya)</label>
                <textarea
                  class="form-control"
                  rows="4"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label fw-semibold">Gravida</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Partus</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
                <div class="col-4">
                  <label class="form-label fw-semibold">Abortus</label>
                  <select class="form-select">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Usia Kehamilan</label>
                <div class="input-group">
                  <input type="number" class="form-control" />
                  <span class="input-group-text">Week</span>
                </div>
              </div>

              <div class="mb-1">
                <label class="form-label fw-semibold">Periode Nifas</label>
                <input type="number" class="form-control" />
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

     <DiagnosaModal
      :show="showModal"
      :diagnosa="diagnosa"
      @close="showModal = false"
      @select="pilihDiagnosa"
    />
</template>

<script setup>
  import { ref, computed } from 'vue';
  import DiagnosaModal from '../../DiagnosaModal.vue';

  const showModal = ref(false);
  const keyword = ref('');
  const form = ref({
    kode_complikasi: '',
    nama_complikasi: '',
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

  // Simpan form
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>

<style scoped>
  .form-section {
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
