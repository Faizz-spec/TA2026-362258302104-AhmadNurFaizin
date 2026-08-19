<template>
  <div class="card shadow-sm border-0 p-4 form-card">
    <div class="card-body">
      <h5 class="mb-3 text-success">Riwayat Kematian Bayi Lahir Mati</h5>
      <hr />
      <form @submit.prevent="submitForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold">Jenis Kematian Bayi/Janin</label>
                <select class="form-select">
                  <option value="Lahir Mati">Lahir Mati</option>
                  <option value="Lahir Mati Saat Persalinan">Lahir Mati Saat Persalinan</option>
                  <option value="Kematian Neonatus">Kematian Neonatus</option>
                </select>
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
                    <button type="button" class="btn btn-info">Cari</button>
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
              <div class="row mb-3">
                <label class="form-label fw-bold">Code Dugaan Kematian Bayi Lahir Mati</label>
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
                    <button type="button" class="btn btn-info">Cari</button>
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
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold">Tempat Meninggal</label>
                <select name="" id="" class="form-select">
                  <option value="1">Tempat Meninggal Faskes</option>
                  <option value="2">Tempat Meninggal Non Faskes</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Alamat Meninggal Faskes / Non Faskes</label>
                <textarea
                  class="form-control"
                  rows="3"
                  placeholder="Tuliskan Alamat...."
                ></textarea>
              </div>
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
        <h5 class="mb-3 mt-4 text-success">Riwayat Persalinan</h5>
        <hr />
        <div class="row g-4">
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
                    <button type="button" class="btn btn-info">Cari</button>
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
              <div class="row mb-3">
                <label class="form-label fw-bold">Berat Lahir</label>
                <div class="input-group">
                  <input type="number" class="form-control" />
                  <span class="input-group-text">Gram</span>
                </div>
              </div>
            </div>
          </div>
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
              <div class="mb-3">
                <label class="form-label fw-bold">Cara Persalinan</label>
                <select class="form-select">
                  <option value="1">Persalinan Pervaginam</option>
                  <option value="2">Persalinan Perabdominam</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <h5 class="mb-3 mt-4 text-success">Riwayat Persalinan</h5>
        <hr />
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label for="" class="form-label fw-bold">Usia Kehamilan Dalam Minggu</label>
                <input type="number" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="" class="form-label fw-bold">Jumlah Anak Hidup</label>
                <input type="number" class="form-control" />
              </div>
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
                <label for="" class="form-label fw-bold">Jenis Kehamilan Kembar</label>
                <select class="form-select">
                  <option value="1">Single Pregnancy</option>
                  <option value="2">Kembar 2</option>
                  <option value="3">Kembar 3</option>
                  <option value="4">Kehamilan Ganda Empat atau Lebih</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <h5 class="mb-3 mt-4 text-success">Data Ibu</h5>
        <hr />
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label for="" class="form-labfel fw-bold">Umur Ibu Ketika Meninggal</label>
                <input type="number" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Lama Tinggal</label>
                <select class="form-select">
                  <option value="1">6 bulan ke atas</option>
                  <option value="2">Di bawah 6 bulan</option>
                </select>
              </div>
              <div class="mb-2 d-flex justify-content-start">
                <button type="submit" class="mt-3 btn btn-success px-4 shadow-sm w-50">
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
  import { ref } from 'vue';

  const form = ref({
    nama_ibu: '',
    usia_kehamilan: '',
    tanggal_kematian: '',
  });

  const emit = defineEmits(['close']);

  const submitForm = () => {
    console.log('Data Bayi Lahir Mati:', form.value);
    alert('Data berhasil disimpan!');
    form.value = { nama_ibu: '', usia_kehamilan: '', tanggal_kematian: '' };
    emit('close');
  };
</script>

<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
