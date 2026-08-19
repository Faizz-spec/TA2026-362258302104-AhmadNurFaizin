<template>
  <div class="row mb-2">
    <div class="col-md-6">
      <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
        <h5 class="fw-semibold text-success mb-1">Resumen Medis Postnatal Care</h5>
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
      :class="{ 'active-card': activeResumePnc === 'subjektif' }"
      @click.prevent="toggleForm('subjektif')"
    >
      <div class="action-icon"><i class="bi bi-person-check"></i></div>
      <div class="action-label">Subjektif</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumePnc === 'objektif' }"
      @click.prevent="toggleForm('objektif')"
    >
      <div class="action-icon"><i class="bi bi-person-check"></i></div>
      <div class="action-label">Objektif</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumePnc === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-label">Assessment</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumePnc === 'imunisasi' }"
      @click.prevent="toggleForm('imunisasi')"
    >
      <div class="action-label">Imunisasi</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumePnc === 'planning' }"
      @click.prevent="toggleForm('planning')"
    >
      <div class="action-label">Planning</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumePnc === 'status_pasien' }"
      @click.prevent="toggleForm('status_pasien')"
    >
      <div class="action-label">Status Pasien</div>
    </a>
  </div>
  <hr>
  <div class="mt-3">
    <div v-if="activeResumePnc === 'subjektif'" class="p-2">
      <h5 class="fw-semibold mb-2">Riwayat Persalinan</h5>
      <hr class="mb-3" />
      <div class="col-md-6">
        <div class="mb-3">
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Atribut</th>
                <th>Nilai</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Tanggal Persalinan</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Penolong Persalinan</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Lokasi Persalinan</td>
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
        <div class="row mt-4">
          <div class="col-auto">
            <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="activeResumePnc === 'objektif'" class="p-2">
      <h5 class="fw-semibold mb-2">Pelayanan Nifas</h5>
      <hr class="mb-3" />
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Atribut</th>
                  <th>Nilai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Pemeriksaan Nifas ke</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Tekanan Darah (mmHg)</td>
                  <td>
                    <p class="mb-1">Sistolik:</p>
                    <p class="mb-1">Diastolik:</p>
                  </td>
                </tr>
                <tr>
                  <td>Suhu</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Pernapasan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Pendarahan Pervaginal</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Jumlah Pendarahan</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th>Atribut</th>
                  <th>Nilai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Kondisi Payudara</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Kontraksi Uteri</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Warna Lokhia</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Produksi ASI</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Kondisi Perawatan Bayi Baru Lahir</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="row mt-4">
            <div class="col-auto">
              <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="activeResumePnc === 'assessment'" class="p-2">
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
    <div v-if="activeResumePnc === 'imunisasi'" class="p-2">
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
    <div v-if="activeResumePnc === 'planning'" class="p-2">
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
    <div v-if="activeResumePnc === 'status_pasien'" class="p-2">
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
  const activeResumePnc = ref(localStorage.getItem('activeResumePnc') || 'subjektif');

  // Simpan kembali jika user ganti tab
  watch(activeResumePnc, (val) => {
    localStorage.setItem('activeResumePnc', val);
  });
  const toggleForm = (form) => {
    activeResumePnc.value = form;
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
