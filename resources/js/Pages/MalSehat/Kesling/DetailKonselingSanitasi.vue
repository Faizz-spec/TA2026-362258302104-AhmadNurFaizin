<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
    noKunjungan: String,
    nama: String,
})

function kembali() {
    router.get('/mal-sehat/kesling/konseling-sanitasi')
}

// TAB STATE
const activeTab = ref('subjective')

// ===== SUBJECTIVE =====
const tglAnamnesa = ref(new Date().toISOString().slice(0, 19).replace('T', ' '))
const keluhanUtama = ref('')
const keluhanTambahan = ref('')
const riwayatSekarang = ref('')
const riwayatDahulu = ref('')
const riwayatKeluarga = ref('')
const alergiMakanan = ref('Tidak Ada Alergi')
const alergiObat = ref('Tidak Ada Alergi')
const keteranganAlergi = ref('')
const tindakanPernah = ref('')
const obatSering = ref('')
const obatKonsumsi = ref('')

// ===== OBJECTIVE =====

// Pemeriksaan Fisik
const tanggalAnamnesa = ref("");
const keadaanUmum = ref("");
const kesadaran = ref("");
const imt = ref("");
const keteranganIMT = ref("");
const tinggiBadan = ref("");
const beratBadan = ref("");
const lingkarPerut = ref("");
const lingkarLengan = ref("");
const sistole = ref("");
const diastole = ref("");
const respRate = ref("");
const heartRate = ref("");
const suhu = ref("");

// Status Generalis
const jantung = ref("");
const ketJantung = ref("");
const pulmo = ref("");
const ketPulmo = ref("");
const abdomenAtas = ref("");
const ketAbdomenAtas = ref("");
const abdomenBawah = ref("");
const ketAbdomenBawah = ref("");
const ekstrimitasAtas = ref("");
const ketEkstrimitasAtas = ref("");
const kepala = ref("");
const ketKepala = ref("");
const mata = ref("");
const ketMata = ref("");
const telinga = ref("");
const ketTelinga = ref("");
const leher = ref("");
const ketLeher = ref("");

const simpanData = () => {
  alert("Data berhasil disimpan!");
};

function tambahTindakan() {
    if (kodeTindakan.value && namaTindakan.value) {
        listTindakan.value.push({
            kode: kodeTindakan.value,
            nama: namaTindakan.value,
            namaInd: namaTindakanInd.value,
            keterangan: keteranganTindakan.value,
            harga: 0,
            bayar: 0,
            poli: 'Umum',
            ketGigi: '',
            createdBy: 'Admin',
        })

        // Reset input
        kodeTindakan.value = ''
        namaTindakan.value = ''
        namaTindakanInd.value = ''
        keteranganTindakan.value = ''
    }
}
const statusPulang = ref('Berobat Jalan')
const tenagaMedis = ref('')
const daftarTenagaMedis = ref(['dr. Andi', 'dr. Budi', 'dr. Siti']) // Dummy, bisa diambil dari props atau API
const daftarRujukan = ref([
    {
        asalPoli: 'Konseling',
        keterangan: '',
        poliTujuan: '',
        tenagaMedis: '',
        createdBy: 'user1',
        mulai: '2025-08-12 09:02:52',
        selesai: '',
    }
])


const form = ref({
    general: {
        keluhanUmum: '',
        riwayatPenyakit: '',
        riwayatPengobatan: ''
    },
    // ...bagian form lain seperti subjective, objective, dll
})

function simpanGeneral() {
    console.log("Data General Status:", form.value.general)
    // Nanti bisa diganti kirim ke server
}


function simpanForm() {
    router.post('/mal-sehat/kesling/konseling-sanitasi/store', {
        // Subjective
        tglAnamnesa: tglAnamnesa.value,
        keluhanUtama: keluhanUtama.value,
        keluhanTambahan: keluhanTambahan.value,
        riwayatSekarang: riwayatSekarang.value,
        riwayatDahulu: riwayatDahulu.value,
        riwayatKeluarga: riwayatKeluarga.value,
        alergiMakanan: alergiMakanan.value,
        alergiObat: alergiObat.value,
        keteranganAlergi: keteranganAlergi.value,
        tindakanPernah: tindakanPernah.value,
        obatSering: obatSering.value,
        obatKonsumsi: obatKonsumsi.value,

        // Objective
        keadaanUmum: keadaanUmum.value,
        kesadaran: kesadaran.value,
        imt: imt.value,
        keteranganIMT: keteranganIMT.value,
        tinggiBadan: tinggiBadan.value,
        beratBadan: beratBadan.value,
        lingkarPerut: lingkarPerut.value,
        lingkarLengan: lingkarLengan.value,
        sistole: sistole.value,
        diastole: diastole.value,
        respRate: respRate.value,
        heartRate: heartRate.value,
        suhu: suhu.value,

        // Tambahan status
        statusPemeriksaan: 'Proses Dilayani'
    }, {
        onSuccess: () => {
            router.get('/mal-sehat/kesling/konseling-sanitasi')
        }
    })
}
function simpanStatusPasien() {
    router.post('/mal-sehat/kesling/konseling-sanitasi/status', {
        statusPulang: statusPulang.value,
        tenagaMedis: tenagaMedis.value,
        noKunjungan: props.noKunjungan
    }, {
        onSuccess: () => {
            // Refresh halaman atau pindah ke halaman lain
            router.get('/mal-sehat/kesling/konseling-sanitasi')
        }
    })
}
</script>

<template>
    <div class="card m-2 shadow-sm border-0 rounded-4">
        <!-- Header -->
        <div class="card-header p-3 text-white d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0"
            style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
            <h1 class="m-0 fs-4">Kunjungan Sehat [{{ props.nama }}]</h1>
            <button class="btn btn-light btn-sm" @click="kembali">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </button>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs custom-tabs px-3 pt-3 border-bottom-0">
            <li class="nav-item" v-for="tab in ['subjective', 'objective', 'assessment', 'planning', 'status']"
                :key="tab">
                <button class="nav-link px-4 py-2 fw-semibold" :class="{ active: activeTab === tab }"
                    @click="activeTab = tab">
                    {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                </button>
            </li>
        </ul>

        <!-- SUBJECTIVE -->
        <div v-show="activeTab === 'subjective'" class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 shadow-sm bg-white h-100">
                        <h6 class="fw-semibold text-secondary mb-3">
                            <i class="fas fa-notes-medical me-2 text-primary"></i> Riwayat Keluhan
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Tgl Anamnesa</label>
                            <input type="text" class="form-control form-control-sm" v-model="tglAnamnesa" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Keluhan Utama / Keperluan</label>
                            <textarea class="form-control form-control-sm" rows="2" v-model="keluhanUtama"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Keluhan Tambahan</label>
                            <textarea class="form-control form-control-sm" rows="2"
                                v-model="keluhanTambahan"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Riwayat Penyakit Sekarang</label>
                            <textarea class="form-control form-control-sm" rows="2"
                                v-model="riwayatSekarang"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Riwayat Penyakit Dahulu</label>
                            <textarea class="form-control form-control-sm" rows="2" v-model="riwayatDahulu"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Riwayat Penyakit Keluarga</label>
                            <textarea class="form-control form-control-sm" rows="2"
                                v-model="riwayatKeluarga"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border rounded-3 shadow-sm bg-white h-100">
                        <h6 class="fw-semibold text-secondary mb-3">
                            <i class="fas fa-syringe me-2 text-primary"></i> Alergi & Terapi
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Alergi Makanan</label>
                            <select class="form-select form-select-sm" v-model="alergiMakanan">
                                <option>Tidak Ada Alergi</option>
                                <option>Ada Alergi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Alergi Obat</label>
                            <select class="form-select form-select-sm" v-model="alergiObat">
                                <option>Tidak Ada Alergi</option>
                                <option>Ada Alergi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Keterangan Alergi</label>
                            <textarea class="form-control form-control-sm" rows="2"
                                v-model="keteranganAlergi"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Tindakan / Terapi yang Pernah Dijalani</label>
                            <textarea class="form-control form-control-sm" rows="2" v-model="tindakanPernah"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Obat yang Sering Digunakan</label>
                            <textarea class="form-control form-control-sm" rows="2" v-model="obatSering"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Obat yang Sering Dikonsumsi</label>
                            <textarea class="form-control form-control-sm" rows="2" v-model="obatKonsumsi"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OBJECTIVE -->
  <div class="card">
    <div class="card-header fw-bold">Pemeriksaan Pasien</div>
    <div class="card-body">

      <!-- Tab Menu -->
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <button class="nav-link"
                  :class="{ active: activeTab === 'fisik', 'text-danger': activeTab !== 'fisik' }"
                  @click="activeTab = 'fisik'">
            Pemeriksaan Fisik
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link"
                  :class="{ active: activeTab === 'generalis', 'text-danger': activeTab !== 'generalis' }"
                  @click="activeTab = 'generalis'">
            Status Generalis
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link"
                  :class="{ active: activeTab === 'tenaga', 'text-danger': activeTab !== 'tenaga' }"
                  @click="activeTab = 'tenaga'">
            Tenaga Medis
          </button>
        </li>
      </ul>

      <!-- ================= TAB PEMERIKSAAN FISIK ================= -->
      <div v-show="activeTab === 'fisik'">
        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-md-6">
            <div class="mb-3">
              <label class="fw-semibold">Tanggal Anamnesa</label>
              <input type="datetime-local" class="form-control" v-model="tanggalAnamnesa">
            </div>

            <div class="mb-3">
              <label class="fw-semibold">Keadaan Umum</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" value="Baik" v-model="keadaanUmum">
                <label class="form-check-label">Baik</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" value="Sedang" v-model="keadaanUmum">
                <label class="form-check-label">Sedang</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" value="Lemah" v-model="keadaanUmum">
                <label class="form-check-label">Lemah</label>
              </div>
            </div>

            <div class="mb-3">
              <label class="fw-semibold">Kesadaran</label>
              <select class="form-select" v-model="kesadaran">
                <option>Compos mentis</option>
                <option>Somnolen</option>
                <option>Stupor</option>
                <option>Koma</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="fw-semibold">IMT</label>
              <input type="text" class="form-control bg-warning" v-model="imt">
            </div>

            <div class="mb-3">
              <label class="fw-semibold">Keterangan IMT</label>
              <input type="text" class="form-control bg-warning" v-model="keteranganIMT">
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-md-6">
            <div class="mb-3"><label class="fw-semibold">Tinggi Badan (cm)</label><input type="number" class="form-control" v-model="tinggiBadan"></div>
            <div class="mb-3"><label class="fw-semibold">Berat Badan (kg)</label><input type="number" class="form-control" v-model="beratBadan"></div>
            <div class="mb-3"><label class="fw-semibold">Lingkar Perut (cm)</label><input type="number" class="form-control" v-model="lingkarPerut"></div>
            <div class="mb-3"><label class="fw-semibold">Lingkar Lengan (cm)</label><input type="number" class="form-control" v-model="lingkarLengan"></div>
            <div class="mb-3"><label class="fw-semibold">Sistole (mmHg)</label><input type="number" class="form-control" v-model="sistole"></div>
            <div class="mb-3"><label class="fw-semibold">Diastole (mmHg)</label><input type="number" class="form-control" v-model="diastole"></div>
            <div class="mb-3"><label class="fw-semibold">Resp. Rate (bpm)</label><input type="number" class="form-control" v-model="respRate"></div>
            <div class="mb-3"><label class="fw-semibold">Heart Rate (bpm)</label><input type="number" class="form-control" v-model="heartRate"></div>
            <div class="mb-3"><label class="fw-semibold">Suhu (°C)</label><input type="number" step="0.1" class="form-control" v-model="suhu"></div>
          </div>
        </div>
      </div>

      <!-- ================= TAB STATUS GENERALIS ================= -->
      <div v-show="activeTab === 'generalis'">
        <div class="row g-4">
          <!-- Thorax -->
          <div class="col-md-6">
            <h6 class="fw-bold">THORAX</h6>
            <div class="mb-3"><label>Jantung</label>
              <select class="form-select" v-model="jantung">
                <option>NORMAL</option>
                <option>Tidak Normal</option>
              </select>
              <input type="text" class="form-control mt-1" v-model="ketJantung" placeholder="Keterangan">
            </div>
            <div class="mb-3"><label>Pulmo</label>
              <select class="form-select" v-model="pulmo">
                <option>NORMAL</option>
                <option>Tidak Normal</option>
              </select>
              <input type="text" class="form-control mt-1" v-model="ketPulmo" placeholder="Keterangan">
            </div>

            <h6 class="fw-bold">ABDOMEN</h6>
            <div class="mb-3"><label>Atas</label><select class="form-select" v-model="abdomenAtas"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketAbdomenAtas" placeholder="Keterangan"></div>
            <div class="mb-3"><label>Bawah</label><select class="form-select" v-model="abdomenBawah"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketAbdomenBawah" placeholder="Keterangan"></div>

            <h6 class="fw-bold">EKTRIMITAS</h6>
            <div class="mb-3"><label>Atas</label><select class="form-select" v-model="ekstrimitasAtas"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketEkstrimitasAtas" placeholder="Keterangan"></div>
          </div>

          <!-- Kepala -->
          <div class="col-md-6">
            <h6 class="fw-bold">KEPALA</h6>
            <div class="mb-3"><label>Kepala</label><select class="form-select" v-model="kepala"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketKepala" placeholder="Keterangan"></div>
            <div class="mb-3"><label>Mata</label><select class="form-select" v-model="mata"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketMata" placeholder="Keterangan"></div>
            <div class="mb-3"><label>Telinga</label><select class="form-select" v-model="telinga"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketTelinga" placeholder="Keterangan"></div>
            <div class="mb-3"><label>Leher</label><select class="form-select" v-model="leher"><option>NORMAL</option></select><input type="text" class="form-control mt-1" v-model="ketLeher" placeholder="Keterangan"></div>
          </div>
        </div>
      </div>

      <!-- ================= TAB TENAGA MEDIS ================= -->
      <div v-show="activeTab === 'tenaga'">
        <div class="mb-3">
          <label class="fw-semibold">Tenaga Medis Askep</label>
          <select class="form-select" v-model="tenagaMedis">
            <option value="">Pilih</option>
            <option>Dokter</option>
            <option>Perawat</option>
          </select>
        </div>
        <button class="btn btn-success w-100" @click="simpanData">Simpan</button>
      </div>

    </div>
  </div>

        <!-- ASSESSMENT -->
        <div v-show="activeTab === 'assessment'" class="card-body">

            <!-- Diagnosa Medis -->
            <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                <h5 class="fw-bold text-danger mb-3">
                    <i class="fas fa-notes-medical me-2"></i> Diagnosa Medis
                </h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label class="fw-semibold">Alergi Makanan</label>
                        <select class="form-select bg-warning" v-model="alergiMakanan">
                            <option>Tidak Ada Alergi</option>
                            <option>Ada Alergi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-semibold">Alergi Obat</label>
                        <select class="form-select bg-warning" v-model="alergiObat">
                            <option>Tidak Ada Alergi</option>
                            <option>Ada Alergi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Keterangan Alergi</label>
                        <input type="text" class="form-control bg-warning" v-model="keteranganAlergi" />
                    </div>
                    <div class="col-md-2">
                        <label class="fw-semibold">Kunjungan Kasus <span class="text-danger">*</span></label>
                        <select class="form-select">
                            <option value="">- Pilih -</option>
                            <option>Kasus Baru</option>
                            <option>Kunjungan Ulang</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-semibold">Keterangan</label>
                        <input type="text" class="form-control" />
                    </div>
                </div>
                <button class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan Diagnosa Medis
                </button>
            </div>

            <!-- Pemeriksaan Penunjang -->
            <div class="mb-4">
                <h6 class="fw-bold text-danger d-inline me-3">
                    <i class="fas fa-vials me-1"></i> Pemeriksaan Penunjang
                </h6>
                <button class="btn btn-primary btn-sm">
                    <i class="fas fa-vial me-1"></i> Laboratorium
                </button>
            </div>

            <!-- Form Lanjutan -->
            <div class="mb-4">
                <h6 class="fw-bold text-danger mb-2">
                    <i class="fas fa-list-alt me-1"></i> Form Lanjutan
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-secondary btn-sm">Diare</button>
                    <button class="btn btn-outline-secondary btn-sm">Katarak</button>
                    <button class="btn btn-outline-secondary btn-sm">PTM</button>
                    <button class="btn btn-outline-secondary btn-sm">Hipertensi</button>
                </div>
            </div>

            <!-- Diagnosa Keperawatan -->
            <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="fas fa-user-nurse me-1"></i> Diagnosa Keperawatan
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <select class="form-select">
                            <option>- Pilih -</option>
                            <option>Nyeri Akut</option>
                            <option>Risiko Infeksi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Simpan Diagnosa Keperawatan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Diagnosa -->
            <div class="row g-3">
                <div class="col-md-6">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>No</th>
                                <th>Nama Diagnosa Medis</th>
                                <th>Keterangan</th>
                                <th>Kasus</th>
                                <th>Poli</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle me-1"></i> Belum ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>No</th>
                                <th>Nama Diagnosa Asuhan Keperawatan</th>
                                <th>Keterangan</th>
                                <th>Poli</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle me-1"></i> Belum ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Info -->
            <div class="alert alert-warning mt-3 mb-0" style="font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Jika muncul warna kuning di daftar diagnosa pasien, mohon cek kembali.
                Warna kuning menandakan ada diagnosa KLB.
                Jika salah input, mohon diperbaiki. Jika memang diagnosanya bisa diabaikan.
            </div>
        </div>

        <!-- PLANNING -->
        <div v-show="activeTab === 'planning'" class="card-body">

            <!-- Sub Tab -->
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#">
                        <i class="fas fa-stethoscope me-1"></i> Tindakan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#">
                        <i class="fas fa-pills me-1"></i> Pengobatan
                    </a>
                </li>
            </ul>

            <!-- Form Tindakan -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Tindakan
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">

                        <!-- Kode Tindakan -->
                        <label class="fw-semibold">Kode Tindakan</label>
                        <div class="d-flex">
                            <input type="text" class="form-control" v-model="kodeTindakan"
                                placeholder="Masukkan kode..." />
                            <button class="btn btn-info btn-sm ms-2" title="Cari">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-danger btn-sm ms-1" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <!-- Nama Tindakan -->
                        <label class="fw-semibold mt-3">Nama Tindakan</label>
                        <textarea class="form-control" v-model="namaTindakan" rows="2"
                            placeholder="Masukkan nama tindakan"></textarea>

                        <!-- Nama Tindakan (Ind) -->
                        <label class="fw-semibold mt-3">Nama Tindakan (Ind)</label>
                        <textarea class="form-control" v-model="namaTindakanInd" rows="2"
                            placeholder="Masukkan nama tindakan dalam bahasa Indonesia"></textarea>

                        <!-- Keterangan -->
                        <label class="fw-semibold mt-3">Keterangan</label>
                        <textarea class="form-control" v-model="keteranganTindakan" rows="2"
                            placeholder="Masukkan keterangan tambahan"></textarea>

                        <!-- Tombol Simpan -->
                        <button class="btn btn-primary mt-3" @click="tambahTindakan">
                            <i class="fas fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Tindakan -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Tindakan</th>
                                    <th>Peraturan</th>
                                    <th>Harga</th>
                                    <th>Bayar</th>
                                    <th>Poli</th>
                                    <th>Keterangan</th>
                                    <th>Ket Gigi</th>
                                    <th>Created By</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listTindakan" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.kode }}</td>
                                    <td>{{ item.nama }}</td>
                                    <td>-</td>
                                    <td>{{ item.harga }}</td>
                                    <td>{{ item.bayar }}</td>
                                    <td>{{ item.poli }}</td>
                                    <td>{{ item.keterangan }}</td>
                                    <td>{{ item.ketGigi }}</td>
                                    <td>{{ item.createdBy }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-danger btn-sm"
                                            @click="listTindakan.splice(index, 1)" title="Hapus tindakan">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="listTindakan.length === 0">
                                    <td colspan="11" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada tindakan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- STATUS PASIEN -->
        <div v-show="activeTab === 'status'" class="card-body">
            <h5 class="fw-bold text-danger mb-4">
                <i class="fas fa-hospital-user me-2"></i> Status Pasien & Rujukan
            </h5>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="fas fa-edit me-1"></i> Update Status Pasien
                </h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="fw-semibold">Status Pulang</label>
                        <select class="form-select" v-model="statusPulang">
                            <option value="">- pilih status -</option>
                            <option>Berobat Jalan</option>
                            <option>Rujuk</option>
                            <option>Pulang</option>
                            <option>Rawat Inap</option>
                            <option>Rujuk Internal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-semibold">Tenaga Medis</label>
                        <select class="form-select" v-model="tenagaMedis">
                            <option value="">- pilih tenaga medis -</option>
                            <option v-for="(tenaga, index) in daftarTenagaMedis" :key="index">
                                {{ tenaga }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-success w-100" @click="simpanStatusPasien">
                            <i class="fas fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Asal Poli</th>
                                    <th>Keterangan</th>
                                    <th>Poli Tujuan</th>
                                    <th>Tenaga Medis</th>
                                    <th>Created By</th>
                                    <th>Mulai Melayani</th>
                                    <th>Selesai Melayani</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in daftarRujukan" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td><span class="badge bg-success">{{ item.asalPoli }}</span></td>
                                    <td>{{ item.keterangan }}</td>
                                    <td>{{ item.poliTujuan }}</td>
                                    <td>{{ item.tenagaMedis }}</td>
                                    <td>{{ item.createdBy }}</td>
                                    <td>{{ item.mulai }}</td>
                                    <td>{{ item.selesai }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm" title="Kembali ke Poli Awal">
                                            <i class="fas fa-arrow-left me-1"></i> Poli Awal
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="daftarRujukan.length === 0">
                                    <td colspan="9" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i> Tidak ada data rujukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="text-end p-3">
            <button type="button" class="btn btn-success" @click="simpanForm">
                <i class="fas fa-save me-1"></i> SIMPAN
            </button>
        </div>
    </div>
</template>

<style scoped>
.custom-tabs .nav-link {
    color: #4b5563;
    border: none;
    border-bottom: 2px solid transparent;
    background-color: transparent;
    transition: all 0.2s ease-in-out;
}

.custom-tabs .nav-link:hover {
    color: #1f2937;
    border-bottom: 2px solid #9ca3af;
}

.custom-tabs .nav-link.active {
    color: #1f2937;
    border-bottom: 3px solid #1e40af;
    font-weight: 600;
    background-color: transparent;
}

.form-label {
    font-size: 0.875rem;
    color: #374151;
}

.table thead th {
    white-space: nowrap;
}
</style>
