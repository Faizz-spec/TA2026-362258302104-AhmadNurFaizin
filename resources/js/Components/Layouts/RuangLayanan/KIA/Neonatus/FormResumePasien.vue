<template>
  <div class="row mb-2">
    <div class="col-md-6">
      <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
        <h5 class="fw-semibold text-success mb-1">Resumen Medis Neonatus</h5>
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
      :class="{ 'active-card': activeResumeNeonatus === 'subjektif' }"
      @click.prevent="toggleForm('subjektif')"
    >
      <div class="action-label">Subjektif</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeNeonatus === 'objektif' }"
      @click.prevent="toggleForm('objektif')"
    >
      <div class="action-label">Objektif</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeNeonatus === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-label">Assessment</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeNeonatus === 'imunisasi' }"
      @click.prevent="toggleForm('imunisasi')"
    >
      <div class="action-label">Imunisasi</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeNeonatus === 'planning' }"
      @click.prevent="toggleForm('planning')"
    >
      <div class="action-label">Planning</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeNeonatus === 'status_pasien' }"
      @click.prevent="toggleForm('status_pasien')"
    >
      <div class="action-label">Status Pasien</div>
    </a>
  </div>
  <hr />
  <div class="mt-3">
    <div v-if="activeResumeNeonatus === 'subjektif'" class="p-2">
      <h5 class="fw-semibold">Pemeriksaan Data Bayi</h5>
      <hr />
      <div class="col-md-6">
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Pemeriksaan</th>
              <th>Hasil</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Tanggal Lahir</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Jam Lahir</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Kabupaten / Kota Kelahiran</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Jenis Kelamin</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Anak Ke</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Usia Gestasi</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <div class="col-auto">
            <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="activeResumeNeonatus === 'objektif'" class="p-2">
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
        <h5 class="fw-semibold">Pemeriksaan KN 0</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Pemeriksaan KN 1</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Pemeriksaan KN 2</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Pemeriksaan KN 3</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Pemeriksaan Fisik Head to Toe</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mb-3">
        <h5 class="fw-semibold">Pemeriksaan Berat Badan dan Pemberian ASI</h5>
        <hr />
        <p>Belum Dilakukan Pemeriksaan</p>
      </div>
      <div class="mt-4">
        <div class="col-auto">
          <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
        </div>
      </div>
    </div>

    <div v-if="activeResumeNeonatus === 'assessment'" class="p-2">
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
    <div v-if="activeResumeNeonatus === 'imunisasi'" class="p-2">
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
    <div v-if="activeResumeNeonatus === 'planning'" class="p-2">
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
    <div v-if="activeResumeNeonatus === 'status_pasien'" class="p-2">
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
  const activeResumeNeonatus = ref(localStorage.getItem('') || 'objektif');

  // Simpan kembali jika user ganti tab
  watch(activeResumeNeonatus, (val) => {
    localStorage.setItem('activeResumeNeonatus', val);
  });
  const toggleForm = (form) => {
    activeResumeNeonatus.value = form;
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
