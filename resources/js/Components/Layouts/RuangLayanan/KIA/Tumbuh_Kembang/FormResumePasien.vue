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
      :class="{ 'active-card': activeResumeTKembang === 'subjektif' }"
      @click.prevent="toggleForm('subjektif')"
    >
      <div class="action-label">Subjektif</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeTKembang === 'objektif' }"
      @click.prevent="toggleForm('objektif')"
    >
      <div class="action-label">Objektif</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeTKembang === 'assessment' }"
      @click.prevent="toggleForm('assessment')"
    >
      <div class="action-label">Assessment</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeTKembang === 'imunisasi' }"
      @click.prevent="toggleForm('imunisasi')"
    >
      <div class="action-label">Imunisasi</div>
    </a>

    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeTKembang === 'planning' }"
      @click.prevent="toggleForm('planning')"
    >
      <div class="action-label">Planning</div>
    </a>
    <a
      href="#"
      class="action-card medical-action"
      :class="{ 'active-card': activeResumeTKembang === 'status_pasien' }"
      @click.prevent="toggleForm('status_pasien')"
    >
      <div class="action-label">Status Pasien</div>
    </a>
  </div>

  <hr />
  <div class="mt-4">
    <div v-if="activeResumeTKembang === 'subjektif'" class="p-2">
      <h5 class="fw-semibold">Pemeriksaan Anamnesis</h5>
      <hr />
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Tanggal dan Waktu Pemeriksaan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Keluhan Utama</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Penyakit Sekarang</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Penyakit Sebelumnya</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Pengobatan Sebelumnya</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Alergi</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Riwayat Keluarga</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Gaya Hidup</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Riwayat Psikolosial</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Pemeriksaan Fisik Sederhana</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Catatan Tambahan</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <h5 class="fw-semibold">Pemeriksaan Gizi</h5>
      <hr />
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Berat Badan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Tinggi Badan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Indeks Masa Tubuh (IMT)</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Status Gizi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Riwayat Penyakit Yang Terkait dengan Gizi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Apakah pasien rutin mengonsumsi suplemen atau vitamin</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Jenis suplemen/vitamin yang dikonsumsi</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <h5 class="fw-semibold">Data Makanan</h5>
      <hr />
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Sarapan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Makan Siang</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Makan Malam</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Cemilan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Karbohidrat</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Protein</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Sayuran</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Buah - Buahan</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Produk Susu</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Konsumsi Air Putih per Hari</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Konsumsi Minuman Manis</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Konsumsi minuman berkafein (kopi, teh)</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Apakah pasien memiliki alergi terhadap makanan tertentu</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>Apakah pasien menjalani diet khusus</td>
                  <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <h5 class="fw-semibold">Data Asi</h5>
      <hr />
      <div class="col-md-6">
        <div class="mb-3">
          <table class="table table-striped table-hover table-sm">
            <thead>
              <tr>
                <th>Pemeriksaan</th>
                <th>Hasil</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Apakah bayi dapat ASI Ekslusif</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Deskripsi</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Apakah bayi mendapat makanan pendamping asi</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Deskripsi</td>
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
    <div v-if="activeResumeTKembang === 'objektif'" class="p-2">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Antropometri</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td>Tanggal dan Waktu Pengukuran</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Berat Badan (BB)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Kenaikan Berat Badan Adekuat</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Panjang Badan (PB)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Tinggi Badan (TB)</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Lingkar Lengan Atas (LiLA)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Lingkar Kepala (LK)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Catatan Tambahan</td>
                    <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan SDIDTK</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td>Status Apakah diPeriksa SDIDTK</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Kuesioner Pra Skrining Perkembangan (KPSP)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td> Tes Daya Dengar (TDD)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Tes Daya Lihat (TDL)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Kuesioner Masalah Perilaku dan Emosional (KMPE)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Autisme (M-CHAT-R)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Gangguan Pemusatan Perhatian dan Hiperaktivitas (GPPH)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Catatan Tambahan</td>
                    <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Status Pertumbuhan</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Pemeriksaan</th>
                  <th>Hasil</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td>Status Pertumbuhan</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Deskripsi</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Status Perkembangan</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Deskripsi</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Keterangan Tambahan</td>
                    <td>-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Panjang Badan</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Umur (Bulan)</th>
                  <th>Panjang Badan (CM)</th>
                  <th>Z-Score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td colspan="4" class="text-muted text-center">Belum ada Data Bayi</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Berat Badan</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
             <thead>
                <tr>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Umur (Bulan)</th>
                  <th>Berat Badan (KG)</th>
                  <th>Z-Score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td colspan="4" class="text-muted text-center">Belum ada Data Bayi</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Lingkar Kepala</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Umur (Bulan)</th>
                  <th>Lingkar Kepala (CM)</th>
                  <th>Z-Score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td colspan="4" class="text-muted text-center">Belum ada Data Bayi</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mb-3">
            <h5 class="fw-semibold">Pemeriksaan Tinggi Badan</h5>
            <hr />
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Umur (Bulan)</th>
                  <th>Tinggi Badan (CM)</th>
                  <th>Z-Score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td colspan="4" class="text-muted text-center">Belum ada Data Bayi</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-4">
            <div class="col-auto">
              <button class="btn btn-primary fw-semibold px-3">Kirim Satu Sehat</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="activeResumeTKembang === 'assessment'" class="p-2">
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
    <div v-if="activeResumeTKembang === 'imunisasi'" class="p-2">
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
    <div v-if="activeResumeTKembang === 'planning'" class="p-2">
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
    <div v-if="activeForm === 'status_pasien'" class="p-2">
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
  const activeResumeTKembang = ref(localStorage.getItem('activeResumeTKembang') || 'obstetri');

  // Simpan kembali jika user ganti tab
  watch(activeResumeTKembang, (val) => {
    localStorage.setItem('activeResumeTKembang', val);
  });
  const toggleForm = (form) => {
    activeResumeTKembang.value = form;
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
