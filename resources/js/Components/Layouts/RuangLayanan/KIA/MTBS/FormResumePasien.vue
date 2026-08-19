<template>
  <div class="row mb-2">
    <div class="col-md-6">
      <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
        <h5 class="fw-semibold text-success mb-1">Resumen Medis Antenatal Care</h5>
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
      :class="{ 'active-card': activeResume === 'obstetri' }"
      @click.prevent="toggleForm('obstetri')"
    >
      <div class="action-label">Obstetri</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'subjektif' }"
      @click.prevent="toggleForm('subjektif')"
    >
      <div class="action-label">Subjektif</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'objektif' }"
      @click.prevent="toggleForm('objektif')"
    >
      <div class="action-label">Objektif</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-label">Assessment</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'imunisasi' }"
      @click.prevent="toggleForm('imunisasi')"
    >
      <div class="action-label">Imunisasi</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'planning' }"
      @click.prevent="toggleForm('planning')"
    >
      <div class="action-label">Planning</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResume === 'status_pasien' }"
      @click.prevent="toggleForm('status_pasien')"
    >
      <div class="action-label">Status Pasien</div>
    </a>
  </div>

  <hr />

  <!-- Tempat munculnya form yang aktif -->
  <div class="mt-3">
    <div v-if="activeResume === 'obstetri'" class="p-2">
      <!-- Judul Section -->
      <h5 class="text-success fw-semibold mb-2">Observation</h5>

      <!-- Pemeriksaan Obstetri -->
      <h6 class="fw-semibold mb-2">A. Pemeriksaan Obstetri</h6>

      <div class="row mb-3">
        <!-- Kolom kiri -->
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
              <tr>
                <td>Tanggal HPHT</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Berat Badan Sebelum Hamil</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Tinggi Badan Sebelum Hamil</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Kolom kanan -->
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
                <td>Berat Badan Target</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Indeks Masa Tubuh</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Jarak Hamil</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Status Imunisasi Tetanus</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Status Imunisasi -->
      <div class="mb-3">
        <h6 class="fw-semibold mb-2">B. Status Imunisasi Tetanus</h6>
        <table class="table table-hover table-striped table-sm mb-0">
          <thead>
            <tr>
              <th>Dosis</th>
              <th>Tanggal</th>
              <th>No. Batch</th>
              <th>Nama Vaksin</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center text-muted">Tidak ada data status imunisasi</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tombol Aksi -->
      <div class="row mt-4">
        <div class="col-auto">
          <button class="btn btn-success fw-semibold px-3">Simpan Pelayanan</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
        </div>
      </div>
    </div>

    <div v-if="activeResume === 'subjektif'" class="p-2">
      <div class="col-md-6">
        <h5 class="fw-semibold mb-2">Pemeriksaan Kunjungan</h5>
        <hr class="mb-3" />
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
                <td>Tanggal Kunjungan</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Usia Kehamilan</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Trimester Ke</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <h5 class="fw-semibold mb-2">Pemeriksaan Pemantauan dan Riwayat</h5>
      <hr />
      <div class="col-md-6">
        <div class="mb-3">
          <h6 class="fw-semibold mb-1">Pemantauan dan Pendampingan</h6>
          <table class="table table-hover table-striped table-sm">
            <thead>
              <tr>
                <th>Pemeriksaan</th>
                <th>Hasil</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Terlalu muda usia melahirkan dibawah 20 tahun</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Terlalu rapat jarak kelahiran (< 2 tahun)</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Terlalu tua (kehamilan di atas 35 tahun)</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Terlalu sering melahirkan (anak > 3)</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Status Merokok</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Status Alkohol</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Apakah Disabilitas</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Apakah Mengikuti Kelas Ibu Hamil</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-3">
        <h6 class="fw-semibold mb-2">A. Komplikasi Penyulit Kehamilan</h6>
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Code Komplikasi</th>
              <th>Nama Komplikasi</th>
              <th>Sumber</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center text-muted">Tidak ada Data Komplikasi</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h6 class="fw-semibold mb-2">B. Riwayat Penyakit Menular Pribadi</h6>
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Code Komplikasi</th>
              <th>Nama Komplikasi</th>
              <th>Sumber</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center text-muted">Tidak ada Data Komplikasi</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <h6 class="fw-semibold mb-2">C. Riwayat Penyakit Keluarga</h6>
        <table class="table table-hover table-striped table-sm">
          <thead>
            <tr>
              <th>Code Komplikasi</th>
              <th>Nama Komplikasi</th>
              <th>Sumber</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center text-muted">Tidak ada Data Komplikasi</td>
            </tr>
          </tbody>
        </table>
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
    <div v-if="activeResume === 'objektif'" class="p-2">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Pemeriksaan Kunjungan</h5>
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
                  <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Pemeriksaan USG</h5>
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
                  <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Pemeriksaan Fisik Ibu</h5>
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
                  <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Pemeriksaan Janin</h5>
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
                  <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Pemeriksaan 10T</h5>
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
                  <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                </tr>
              </tbody>
            </table>
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
      </div>
    </div>
    <div v-if="activeResume === 'assessment'" class="p-2">
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
    <div v-if="activeResume === 'imunisasi'" class="p-2">
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
    <div v-if="activeResume === 'planning'" class="p-2">
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
    <div v-if="activeResume === 'status_pasien'" class="p-2">
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
  import { ref, computed, watch } from 'vue';

  const props = defineProps({
    DataPasien: Array,
  });
  const activeResume = ref(localStorage.getItem('activeResume') || 'obstetri');

  // Simpan kembali jika user ganti tab
  watch(activeResume, (val) => {
    localStorage.setItem('activeResume', val);
  });
  const toggleForm = (form) => {
    activeResume.value = form;
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
