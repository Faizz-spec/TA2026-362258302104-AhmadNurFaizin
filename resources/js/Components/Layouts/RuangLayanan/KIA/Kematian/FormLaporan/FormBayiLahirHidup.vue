<template>
  <div class="card shadow-sm border-0 p-4 form-card">
    <div class="card-body">
      <h5 class="mb-3 text-success">Riwayat Kematian Bayi Lahir Hidup</h5>
      <hr />
      <form @submit.prevent="submitForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold">Lokasi Tempat Bersalin</label>
                <input type="text" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Alamat Meninggal</label>
                <input type="text" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Jenis Kematian Bayi/Janin</label>
                <select class="form-select">
                  <option value="Lahir Mati">Lahir Mati</option>
                  <option value="Lahir Mati Saat Persalinan">Lahir Mati Saat Persalinan</option>
                  <option value="Kematian Neonatus">Kematian Neonatus</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Usia Saat Meninggal</label>
              <div class="input-group">
                <input type="number" class="form-control" />
                <span class="input-group-text">Menit</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Berat Saat Meninggal</label>
              <div class="input-group">
                <input type="number" class="form-control" />
                <span class="input-group-text">Gram</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Panjang Saat Meninggal</label>
              <div class="input-group">
                <input type="number" class="form-control" />
                <span class="input-group-text">cm</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal dan Jam Kematian</label>
              <div class="row">
                <div class="col-6">
                  <input type="date" class="form-control" />
                </div>
                <div class="col-6">
                  <input type="time" class="form-control" />
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-section">
              <div class="row mb-3">
                <label class="form-label fw-bold">Code Dugaan Kematian</label>
                <div class="col-3">
                  <input type="text" class="form-control" placeholder="Kode" disabled />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      placeholder="Nama Diagnosa"
                      disabled
                    />
                    <button type="button" @click="showModalDugaan = true" class="btn btn-info">
                      Cari
                    </button>
                    <button type="button" class="btn btn-danger">Del</button>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
              <!-- Form kondisi ibu  -->
              <div class="row mb-3">
                <label class="form-label fw-bold">Kondisi Ibu Mempengaruhi Janin</label>
                <div class="col-3">
                  <input type="text" class="form-control" placeholder="Kode" disabled />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      placeholder="Nama Diagnosa"
                      disabled
                    />
                    <button type="button" @click="showModalKondisi = true" class="btn btn-info">Cari</button>
                    <button type="button" class="btn btn-danger">Del</button>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
              <!-- Jenis Tempat Meninggal -->
              <div class="mb-3">
                <label class="form-label fw-bold">Jenis Tempat Meninggal</label>
                <select class="form-select">
                  <option value="RS Pemerintah">RS Pemerintah</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi (Jika Lainnya)</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <h5 class="mb-3 mt-4 text-success">Riwayat Kematian Bayi Lahir Hidup</h5>
        <hr />
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label fw-bold">Gravida</label>
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
                  <label class="form-label fw-bold">Partus</label>
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
                  <label class="form-label fw-bold">Abortus</label>
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
                <label class="form-label fw-bold">Usia Saat Meninggal</label>
                <div class="input-group">
                  <input type="number" class="form-control" />
                  <span class="input-group-text">Minggu</span>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Jumlah Anak Hidup</label>
                <input type="number" class="form-control" />
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-bold">Jenis Kehamilan</label>
              <select class="form-select">
                <option value="Tunggal">Tunggal</option>
                <option value="Kembar 2">Kembar 2</option>
                <option value="Kembar 3">Kembar 3</option>
                <option value="Kehamilan Ganda">Kehamilan Ganda 4 atau Lebih</option>
              </select>
            </div>
          </div>
        </div>
        <h5 class="mb-3 mt-4 text-success fw-semiold">Riwayat Persalinan</h5>
        <hr />
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold">Janin Yang Meninggal (Untuk Bayi Kembar)</label>
                <select class="form-select">
                  <option value="1">Janin yang meninggal adalah janin urutan pertama</option>
                  <option value="2">Janin yang meninggal adalah janin urutan kedua</option>
                  <option value="3">Janin yang meninggal adalah janin urutan ketiga</option>
                  <option value="4">Janin yang meninggal adalah janin urutan keempat</option>
                </select>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label fw-bold">Berat Lahir</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">Gram</span>
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label fw-bold">Lingkar Kepala</label>
                  <div class="input-group">
                    <input type="number" class="form-control" />
                    <span class="input-group-text">CM</span>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <label class="form-label fw-bold">Code Dugaan Kematian Ibu</label>
                <div class="col-3">
                  <input type="text" class="form-control" placeholder="Kode" disabled />
                </div>
                <div class="col-9">
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control bg-light"
                      placeholder="Nama Diagnosa"
                      disabled
                    />
                    <button type="button" @click="showModalIbu = true" class="btn btn-info">Cari</button>
                    <button type="button" class="btn btn-danger">Del</button>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
              <div class="mb-2 d-flex justify-content-start">
                <button type="submit" class="mt-3 btn btn-success px-4 shadow-sm w-50">
                  <i class="bi bi-save me-1"></i> Simpan Data
                </button>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold">Jenis Tempat Meninggal</label>
                <select class="form-select">
                  <option value="RS Pemerintah">RS Pemerintah</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi (Jika Lainnya)</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Deskripsi...."
                ></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Jenis Persalinan</label>
                <select class="form-select">
                  <option value="1">Persalinan Pervaginam</option>
                  <option value="2">Persalinan Perabdominam</option>
                </select>
              </div>
              <div class="mb-2 d-flex justify-content-start">
                <button type="button" class="mt-1 btn btn-primary px-4 shadow-sm w-50">
                  <i class="bi bi-save me-1"></i> Apgar Detail
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <DiagnosaModal
      :show="showModalDugaan"
      :diagnosa="diagnosa"
      @close="showModalDugaan = false"
      @select="pilihDiagnosa"
    />

    <!-- Modal Pilih Riwayat Pribadi -->
    <DiagnosaModal
      :show="showModalKondisi"
      :diagnosa="diagnosa"
      @close="showModalKondisi= false"
      @select="pilihRiwayat"
    />

    <DiagnosaModal
      :show="showModalIbu"
      :diagnosa="diagnosa"
      @close="showModalIbu = false"
      @select="pilihRiwayatKel"
    />
  </div>
</template>

<script setup>
  import { ref } from 'vue';
  import DiagnosaModal from '../../DiagnosaModal.vue';

  const showModalDugaan = ref(false);
  const showModalIbu = ref(false);
  const showModalKondisi = ref(false);

  const props = defineProps({
    diagnosa: Array,
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

  const form = ref({
    nama_bayi: '',
    tanggal_lahir: '',
    tanggal_kematian: '',
  });

  const submitForm = () => {
    console.log('Data Bayi Lahir Hidup:', form.value);
    alert('Data berhasil disimpan!');
    form.value = { nama_bayi: '', tanggal_lahir: '', tanggal_kematian: '' };
    // Emit event untuk menutup form
    emit('close');
  };

  const emit = defineEmits(['close']);
</script>

<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
