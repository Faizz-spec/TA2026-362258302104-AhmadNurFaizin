<template>
  <div v-if="show" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header bg-success text-white p-2 d-flex justify-content-between">
        <h5 class="mb-0">Form Surat Keterangan</h5>
        <button class="btn btn-sm btn-light" @click="close">X</button>
      </div>

      <div class="modal-body p-3">
        <!-- Data Pasien -->
        <h6 class="fw-bold"><u>Data Pasien</u></h6>
        <div class="row mb-2">
          <div class="col-md-6">
            <label>No MR</label>
            <input type="text" class="form-control" v-model="form.no_mr" readonly>
          </div>
          <div class="col-md-6">
            <label>Nama</label>
            <input type="text" class="form-control" v-model="form.nama" readonly>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-md-6">
            <label>Tempat Lahir</label>
            <input type="text" class="form-control bg-warning" v-model="form.tempat">
          </div>
          <div class="col-md-6">
            <label>Tanggal Lahir</label>
            <input type="date" class="form-control" v-model="form.tgl_lahir">
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-md-6">
            <label>Agama</label>
            <input type="text" class="form-control" v-model="form.agama">
          </div>
          <div class="col-md-6">
            <label>Pekerjaan</label>
            <input type="text" class="form-control" v-model="form.pekerjaan">
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-md-6">
            <label>Alamat</label>
            <input type="text" class="form-control" v-model="form.alamat">
          </div>
          <div class="col-md-3">
            <label>Desa</label>
            <input type="text" class="form-control" v-model="form.desa">
          </div>
          <div class="col-md-3">
            <label>Kecamatan</label>
            <input type="text" class="form-control" v-model="form.kecamatan">
          </div>
        </div>

        <!-- Data Surat -->
        <h6 class="fw-bold mt-3"><u>Data Surat</u></h6>
        <div class="row mb-2">
          <div class="col-md-6">
            <label>Jenis Surat</label>
            <select class="form-control" v-model="form.jenis_surat">
              <option value="">Pilih Jenis Surat</option>
              <option value="surat_keterangan_sehat">Surat Keterangan Sehat</option>
              <option value="surat_keterangan_dokter">Surat Keterangan Dokter</option>
              <option value="surat_keterangan_catin">Surat Keterangan Catin</option>
              <option value="surat_keterangan_calon_haji">Surat Keterangan Calon Haji</option>
              <option value="surat_keterangan_kematian">Surat Keterangan Kematian</option>
              <option value="surat_keterangan_rapid_test">Surat Keterangan Rapid Test</option>
            </select>
          </div>
          <div class="col-md-6">
            <label>No Surat</label>
            <input type="text" class="form-control" v-model="form.no_surat">
          </div>
        </div>
        <!-- Tampilkan hanya jika jenis surat = surat_keterangan_dokter -->
        <div class="row mb-3" v-if="form.jenis_surat === 'surat_keterangan_dokter'">
          <div class="col-md-2">
            <label>Tanggal Ijin</label>
            <input type="date" class="form-control" v-model="form.tanggal_awal">
          </div>
          <div class="col-md-2 d-flex align-items-end justify-content-center">
            <span class="fw-bold">s/d</span>
          </div>
          <div class="col-md-2">
            <label>&nbsp;</label>
            <input type="date" class="form-control" v-model="form.tanggal_akhir">
          </div>
        </div>
        <!-- Tampilkan hanya jika jenis surat = surat_keterangan_kematian -->
        <div class="row mb-3" v-if="form.jenis_surat === 'surat_keterangan_kematian'">
          <div class="col-md-4">
            <label>Tanggal/Jam Kematian</label>
            <input type="datetime-local" class="form-control" v-model="form.tanggal_jam_kematian">
          </div>
          <div class="col-md-8">
            <label>Keterangan Kematian</label>
            <textarea class="form-control" rows="2" v-model="form.keterangan_kematian"
              placeholder="Tuliskan keterangan penyebab atau kondisi kematian..."></textarea>
          </div>
        </div>
        <div class="mb-3 col-md-6">
          <label>Keperluan</label>
          <textarea class="form-control" rows="2" v-model="form.keperluan"></textarea>
        </div>

        <!-- Form Tambahan: Surat Keterangan Dokter -->
        <div v-if="form.jenis_surat === 'surat_keterangan_dokter'" class="mt-4 border-top pt-3">
          <h6 class="fw-bold mb-3 text-primary">
            <u>Data Pemeriksaan</u>
          </h6>

          <!-- Baris 1: TB, BB, RR, HR -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Tinggi Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.tinggi_badan" placeholder="0" />
                <span class="input-group-text">cm</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Berat Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.berat_badan" placeholder="0" />
                <span class="input-group-text">kg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Respiratory Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.respiratory_rate" placeholder="0" />
                <span class="input-group-text">/menit</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Heart Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.heart_rate" placeholder="0" />
                <span class="input-group-text">bpm</span>
              </div>
            </div>
          </div>

          <!-- Baris 2: Tekanan darah & Suhu -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Tekanan Darah</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.sistol" placeholder="Sistol" />
                <span class="input-group-text">/</span>
                <input type="number" class="form-control" v-model="form.diastole" placeholder="Diastole" />
                <span class="input-group-text">mmHg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Suhu Tubuh</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.suhu" placeholder="0" />
                <span class="input-group-text">°C</span>
              </div>
            </div>
          </div>

          <!-- Baris 3: Mata, Telinga, Buta Warna -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Mata (Ka/Ki)</label>
              <select class="form-select" v-model="form.mata">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Telinga (Ka/Ki)</label>
              <select class="form-select" v-model="form.telinga">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Tes Buta Warna</label>
              <select class="form-select" v-model="form.tes_buta_warna">
                <option value="">Pilih</option>
                <option value="Normal">Normal</option>
                <option value="Buta Warna Parsial">Parsial</option>
                <option value="Buta Warna Total">Total</option>
              </select>
            </div>
          </div>

          <!-- Baris 4: Keterangan dan Hasil -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Keterangan Lain</label>
              <textarea class="form-control" rows="2" v-model="form.keterangan_lain"
                placeholder="Masukkan keterangan tambahan"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Hasil Pemeriksaan</label>
              <textarea class="form-control" rows="2" v-model="form.hasil_pemeriksaan"
                placeholder="Tuliskan hasil pemeriksaan"></textarea>
            </div>
          </div>

          <!-- Dokter -->
          <div class="col-md-4 mb-2">
            <label class="form-label">Dokter Jaga</label>
            <select class="form-select" v-model="form.dokter_jaga">
              <option value="">Pilih Dokter</option>
              <option value="Practitioner 1">Practitioner 1</option>
              <option value="Practitioner 2">Practitioner 2</option>
              <option value="Practitioner 3">Practitioner 3</option>
              <option value="Practitioner 4">Practitioner 4</option>
            </select>
          </div>
        </div>

        <!-- Form Tambahan: Surat Keterangan Catin -->
        <div v-if="form.jenis_surat === 'surat_keterangan_catin'" class="mt-4 border-top pt-3">
          <h6 class="fw-bold mb-3 text-primary">
            <u>Data Pemeriksaan</u>
          </h6>

          <!-- Baris 1: TB, BB, RR, HR -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Tinggi Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.tinggi_badan" placeholder="0" />
                <span class="input-group-text">cm</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Berat Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.berat_badan" placeholder="0" />
                <span class="input-group-text">kg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Respiratory Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.respiratory_rate" placeholder="0" />
                <span class="input-group-text">/menit</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Heart Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.heart_rate" placeholder="0" />
                <span class="input-group-text">bpm</span>
              </div>
            </div>
          </div>

          <!-- Baris 2: Tekanan darah & Suhu -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Tekanan Darah</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.sistol" placeholder="Sistol" />
                <span class="input-group-text">/</span>
                <input type="number" class="form-control" v-model="form.diastole" placeholder="Diastole" />
                <span class="input-group-text">mmHg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Suhu Tubuh</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.suhu" placeholder="0" />
                <span class="input-group-text">°C</span>
              </div>
            </div>
          </div>

          <!-- Baris 3: Mata, Telinga, Buta Warna -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Mata (Ka/Ki)</label>
              <select class="form-select" v-model="form.mata">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Telinga (Ka/Ki)</label>
              <select class="form-select" v-model="form.telinga">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Tes Buta Warna</label>
              <select class="form-select" v-model="form.tes_buta_warna">
                <option value="">Pilih</option>
                <option value="Normal">Normal</option>
                <option value="Buta Warna Parsial">Parsial</option>
                <option value="Buta Warna Total">Total</option>
              </select>
            </div>
          </div>

          <!-- Baris 4: Keterangan dan Hasil -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Keterangan Lain</label>
              <textarea class="form-control" rows="2" v-model="form.keterangan_lain"
                placeholder="Masukkan keterangan tambahan"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Hasil Pemeriksaan</label>
              <textarea class="form-control" rows="2" v-model="form.hasil_pemeriksaan"
                placeholder="Tuliskan hasil pemeriksaan"></textarea>
            </div>
          </div>

          <!-- Dokter -->
          <div class="col-md-4 mb-2">
            <label class="form-label">Dokter Jaga</label>
            <select class="form-select" v-model="form.dokter_jaga">
              <option value="">Pilih Dokter</option>
              <option value="Practitioner 1">Practitioner 1</option>
              <option value="Practitioner 2">Practitioner 2</option>
              <option value="Practitioner 3">Practitioner 3</option>
              <option value="Practitioner 4">Practitioner 4</option>
            </select>
          </div>
        </div>

        <!-- Form Tambahan: Surat Keterangan Calon Haji -->
        <div v-if="form.jenis_surat === 'surat_keterangan_calon_haji'" class="mt-4 border-top pt-3">
          <h6 class="fw-bold mb-3 text-primary">
            <u>Data Pemeriksaan</u>
          </h6>

          <!-- Baris 1: TB, BB, RR, HR -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Tinggi Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.tinggi_badan" placeholder="0" />
                <span class="input-group-text">cm</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Berat Badan</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.berat_badan" placeholder="0" />
                <span class="input-group-text">kg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Respiratory Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.respiratory_rate" placeholder="0" />
                <span class="input-group-text">/menit</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Heart Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.heart_rate" placeholder="0" />
                <span class="input-group-text">bpm</span>
              </div>
            </div>
          </div>

          <!-- Baris 2: Tekanan darah & Suhu -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Tekanan Darah</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.sistol" placeholder="Sistol" />
                <span class="input-group-text">/</span>
                <input type="number" class="form-control" v-model="form.diastole" placeholder="Diastole" />
                <span class="input-group-text">mmHg</span>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Suhu Tubuh</label>
              <div class="input-group">
                <input type="number" class="form-control" v-model="form.suhu" placeholder="0" />
                <span class="input-group-text">°C</span>
              </div>
            </div>
          </div>

          <!-- Baris 3: Mata, Telinga, Buta Warna -->
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label class="form-label">Mata (Ka/Ki)</label>
              <select class="form-select" v-model="form.mata">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Telinga (Ka/Ki)</label>
              <select class="form-select" v-model="form.telinga">
                <option value="">Pilih</option>
                <option value="Baik">Baik</option>
                <option value="Tidak Baik">Tidak Baik</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Tes Buta Warna</label>
              <select class="form-select" v-model="form.tes_buta_warna">
                <option value="">Pilih</option>
                <option value="Normal">Normal</option>
                <option value="Buta Warna Parsial">Parsial</option>
                <option value="Buta Warna Total">Total</option>
              </select>
            </div>
          </div>

          <!-- Baris 4: Keterangan dan Hasil -->
          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Keterangan Lain</label>
              <textarea class="form-control" rows="2" v-model="form.keterangan_lain"
                placeholder="Masukkan keterangan tambahan"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Hasil Pemeriksaan</label>
              <textarea class="form-control" rows="2" v-model="form.hasil_pemeriksaan"
                placeholder="Tuliskan hasil pemeriksaan"></textarea>
            </div>
          </div>

          <!-- Dokter -->
          <div class="col-md-4 mb-2">
            <label class="form-label">Dokter Jaga</label>
            <select class="form-select" v-model="form.dokter_jaga">
              <option value="">Pilih Dokter</option>
              <option value="Practitioner 1">Practitioner 1</option>
              <option value="Practitioner 2">Practitioner 2</option>
              <option value="Practitioner 3">Practitioner 3</option>
              <option value="Practitioner 4">Practitioner 4</option>
            </select>
          </div>
        </div>

        <!-- Action -->
        <div class="d-flex justify-content-between">
          <button class="btn btn-secondary" @click="close">Kembali</button>
          <button class="btn btn-primary" @click="submit">Simpan</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, defineProps, defineEmits } from "vue";

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(["close", "save"]);

const form = reactive({
  no_mr: "0139185",
  nama: "MOH. ALDI ROHMATULLOH",
  tempat: "BANYUWANGI",
  tgl_lahir: "2002-12-24",
  agama: "Islam",
  pekerjaan: "PELAJAR/MAHASISWA",
  alamat: "DSN GARIT",
  desa: "Singojuruh",
  kecamatan: "Alasmalang",
  jenis_surat: "",
  no_surat: "",
  keperluan: "",
  // field baru khusus surat dokter
  tinggi_badan: "",
  berat_badan: "",
  respiratory_rate: "",
  heart_rate: "",
  sistol: "",
  diastole: "",
  suhu: "",
  mata: "",
  telinga: "",
  tes_buta_warna: "",
  keterangan_lain: "",
  hasil_pemeriksaan: "",
  dokter_jaga: "",
});

function close() {
  emit("close");
}

function submit() {
  emit("save", { ...form });
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 80px;
  z-index: 999;
}

.modal-container {
  background: #fff;
  border-radius: 14px;
  width: 95%;
  max-width: 1400px;
  /* diperlebar dari 1000px ke 1400px */
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  animation: fadeIn 0.3s ease;
}

.modal-body {
  max-height: 78vh;
  overflow-y: auto;
}

label {
  font-weight: 600;
  font-size: 0.9rem;
}

input,
select,
textarea {
  font-size: 0.9rem;
  border-radius: 8px;
}

button {
  border-radius: 8px;
}

/* spasi antar-row lebih rapi */
.row.g-3 {
  row-gap: 0.8rem;
}

/* Animasi halus */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
