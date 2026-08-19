<template>
  <div class="row mb-2">
    <div class="col-md-6">
      <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
        <h5 class="fw-semibold text-success mb-1">Resumen Medis Intranatal Care</h5>
      </div>
    </div>
    <div class="col-md-6">
      <div
        v-for="data in DataPasien"
        :key="data.ID"
        class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center"
      >
        <h5 class="fw-semibold text-success mb-1">Tanggal Kunjungan: {{ data.tglKunjungan }}</h5>
      </div>
    </div>
  </div>
  <div class="d-flex gap-3 flex-wrap">
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeInc === 'objektif' }"
      @click.prevent="toggleForm('objektif')"
    >
      <div class="action-label">Objektif</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeInc === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-icon"><i class="bi bi-person-check"></i></div>
      <div class="action-label">Assessment</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeInc === 'imunisasi' }"
      @click.prevent="toggleForm('imunisasi')"
    >
      <div class="action-label">Imunisasi</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeInc === 'planning' }"
      @click.prevent="toggleForm('planning')"
    >
      <div class="action-label">Planning</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeInc === 'status_pasien' }"
      @click.prevent="toggleForm('status_pasien')"
    >
      <div class="action-label">Status Pasien</div>
    </a>
  </div>
  <hr>
  <div class="mt-3">
    <div v-if="activeResumeInc === 'objektif'" class="p-2">
      <div class="col-md-6">
        <h5 class="fw-semibold">Riwayat Persalinan</h5>
        <hr class="mb-3" />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Usia Kehamilan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Tanggal Persalinan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Waktu Persalinan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Gravida</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Partus</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Abortus</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-2">
        <h5 class="fw-semibold">Status Pelayanan Persalinan</h5>
        <hr class="mb-3" />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Keadaan Ibu</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Deskripsi (Lainnya)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Penolong Persalinan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Deskripsi (Lainnya)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Cara Persalinan</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Kala 1</h5>
        <hr class="mb-3" />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Patogram Melalui Waspada</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Deskripsi</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Masalah Lain</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Penatalaksanaan Masalah</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Hasilnya</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Kala 2</h5>
        <hr class="mb-3" />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Pantogram Melalui Waspada</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Indikasi (Ya)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Gawat Janin</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Tindakan Yang Dilakukan (Ya)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Distosia Bahu</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Tindakan Yang Dilakukan (Ya)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Masalah Lain</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Penatalaksanaan Masalah</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Hasilnya</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Kala 3</h5>
        <hr class="mb-3" />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Plasenta</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Tindakan Penanganan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Pendarahan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Tindakan yang dilakukan</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Masalah Lain</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Penatalaksanaan Masalah</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Hasilnya</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Kala 4</h5>
        <hr />
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Jam Ke</th>
              <th>Waktu</th>
              <th>Tekanan Darah</th>
              <th>Nadi</th>
              <th>Tinggi Fundus</th>
              <th>Kontraksi Uterus</th>
              <th>Kandung Kemih</th>
              <th>Pendarahan</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="text-muted text-center">Tidak ada data pemeriksaan</td>
            </tr>
          </tbody>
        </table>
        <div class="mb-3">
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Pemeriksaan</th>
                <th>Hasil</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Masalah Kala IV</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Penatalaksanaan Masalah</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Hasilnya</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mb-3">
          <h5 class="fw-semibold">Data Apgar 1 Menit</h5>
          <hr />
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Penampilan</th>
                <th>Nadi</th>
                <th>Refleks</th>
                <th>Aktivitas</th>
                <th>Pernapasan</th>
                <th>Total skor</th>
                <th>Klasifikasi</th>
                <th>Rentang Skor</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Menit 1</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mb-3">
          <h5 class="fw-semibold">Data Apgar 5 Menit</h5>
          <hr />
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Penampilan</th>
                <th>Nadi</th>
                <th>Refleks</th>
                <th>Aktivitas</th>
                <th>Pernapasan</th>
                <th>Total skor</th>
                <th>Klasifikasi</th>
                <th>Rentang Skor</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Menit 5</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mb-3">
          <h5 class="fw-semibold">Data Apgar 10 Menit</h5>
          <hr />
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Penampilan</th>
                <th>Nadi</th>
                <th>Refleks</th>
                <th>Aktivitas</th>
                <th>Pernapasan</th>
                <th>Total skor</th>
                <th>Klasifikasi</th>
                <th>Rentang Skor</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Menit 10</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mb-3">
          <h5 class="fw-semibold">Data Bayi</h5>
          <hr />
          <div class="col-md-6">
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Parameter</th>
                  <th>Nilai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Berat Bayi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Interpretasi Berat Bayi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Panjang Bayi Lahir</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Lokasi Persalinan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Inisiasi Menyusui Dini</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Jika Iya, Inisiasi Menyusui Dini</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-if="activeResumeInc === 'assessment'" class="p-2">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Diagnosa Medis</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="8" class="text-center text-muted">Tidak ada Data assessment</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card shadow-sm border-0 p-2">
            <div class="card-body">
              <p class="fw-semibold text-danger">
                Info : Jika muncul warna kuning di daftar diagnosa pasien, mohon cek kembali. Karena
                warna kuning menandakan ada diagnosa KLB. Jika salah input, mohon diperbaiki. Jika
                memang diagnosa nya bisa diabaikan. syarat warna kuning di daftar diagnosa, "Jika
                Diagnosa KLB dan Kasus Baru"
              </p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-auto">
              <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
            </div>
            <div class="col-auto">
              <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama Diagnosa Kasus Keperawatan</th>
                <th>Keterangan</th>
                <th>Poli</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada Data assessment</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div v-if="activeResumeInc === 'imunisasi'" class="p-2">
      <table class="table table-hover table-striped table-sm">
        <thead>
          <tr>
            <th>No.</th>
            <th>Tanggal Imunisasi</th>
            <th>Umur</th>
            <th>Imunisasi</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="text-muted text-center">Tidak ada data Imunisasi</td>
          </tr>
        </tbody>
      </table>
      <div class="row mt-4">
        <div class="col-auto">
          <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
        </div>
      </div>
    </div>
    <div v-if="activeResumeInc === 'planning'" class="p-2">
      <table class="table table-hover table-striped table-sm">
        <thead>
          <tr>
            <th>No.</th>
            <th>Kode</th>
            <th>Nama Tindakan</th>
            <th>Peraturan</th>
            <th>Harga</th>
            <th>Bayar</th>
            <th>Poli</th>
            <th>Keterangan</th>
            <th>Ket Gigi</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="12" class="text-muted text-center">Tidak ada data tindakan</td>
          </tr>
        </tbody>
      </table>
      <div class="row mt-4">
        <div class="col-auto">
          <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
        </div>
      </div>
    </div>
    <div v-if="activeResumeInc === 'status_pasien'" class="p-2">
      <table class="table table-hover table-striped table-sm">
        <thead>
          <tr>
            <th>No.</th>
            <th>Asal Poli</th>
            <th>Keterangan</th>
            <th>Poli Tujuan</th>
            <th>Tenaga Medis</th>
            <th>Created By</th>
            <th>Mulai Melayani</th>
            <th>Selesai Melayani</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="9" class="text-muted text-center">Tidak ada data</td>
          </tr>
        </tbody>
      </table>
      <div class="row mt-4">
        <div class="col-auto">
          <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, watch } from 'vue';

  const props = defineProps({
    DataPasien: Array,
  });
  const activeResumeInc = ref(localStorage.getItem('activeResumeInc') || 'objektif');

  // Simpan kembali jika user ganti tab
  watch(activeResumeInc, (val) => {
    localStorage.setItem('activeResumeInc', val);
  });
  const toggleForm = (form) => {
    activeResumeInc.value = form;
  };
</script>
<style scoped>
  .action-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 8px;
    background: #fbfbfc;
    color: #10b981;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
  }

  .action-card:hover {
    background: #e9f2ff;
    color: #10b981;
  }

  .active-card {
    background: #10b981;
    color: #fff;
  }

  .action-label {
    font-weight: 500;
  }
</style>
