<template>
    <AppLayouts>
        <div class="app-shell">
            <div class="card m-4 rounded-4 shadow-sm">
                <!-- Header -->
                <div class="card-header d-flex justify-content-between align-items-center p-3 rounded-4 rounded-bottom-0"
                    style="background: linear-gradient(135deg, #3b82f6, #10b981);">
                    <h1 class="m-0 fs-5 text-white">Laboratorium — Pemeriksaan Pasien</h1>
                    <button class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
                        <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
                    </button>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- ================== KIRI ================== -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <!-- Bagian Identitas Pasien -->
                                <div class="card border-0 shadow-sm rounded-4 mb-4">
                                    <div class="card-body p-4 bg-light-subtle border-start border-4 border-primary">
                                        <h5 class="fw-bold text-primary text-uppercase mb-3">Denis Hidayat</h5>
                                        <h6 class="mb-3">IHS : {{ pasien.IHS_NUMBER }}</h6>

                                        <div class="small">
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">Tgl kunjungan</div>
                                                <div class="col-8">{{ pelayanan.tglPelayanan }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">Jenis kelamin</div>
                                                <div class="col-8">{{ pasien.JENIS_KLMIN == 1 ? 'Laki-Laki' :
                                                    'Perempuan' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">Tanggal lahir</div>
                                                <div class="col-8">{{ pasien.TGL_LHR }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">Jenis/Poli</div>
                                                <div class="col-8">Umum</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">NIK / No. RM</div>
                                                <div class="col-8">{{ pasien.NIK }} / {{ pasien.NO_MR }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">No. BPJS</div>
                                                <div class="col-8">-</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-4 text-dark fw-bold">Alamat</div>
                                                <div class="col-8">{{ pasien.ALAMAT }}</div>
                                            </div>
                                        </div>

                                        <div class="text-start mt-3">
                                            <button class="btn btn-success fw-semibold rounded-pill shadow-sm px-4">
                                                <i class="bi bi-journal-text me-1"></i> Riwayat Pasien
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian Hasil Pemeriksaan -->
                                <div class="card border-0 shadow-sm rounded-4">
                                    <div class="card-body bg-light p-4">
                                        <h6 class="fw-bold mb-3">
                                            Hasil Pemeriksaan Objective
                                        </h6>

                                        <div class="row small">
                                            <!-- Kolom Fisik -->
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-danger fw-bold mb-3">Fisik</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Keadaan umum:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.keadaanUmum ?? '-' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Kesadaran:</span>
                                                        Compos mentis
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Tinggi badan:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.tinggiBadan ?? '-' }}
                                                        cm
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Berat badan:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.beratBadan ?? '-' }} kg
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Lingkar perut:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.lingkarPerut ?? '-' }}
                                                        cm
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Tinggi lutut:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.tinggiLutut ?? '-' }}
                                                        cm
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Kolom Tekanan Darah -->
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-danger fw-bold mb-3">Tekanan Darah</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Sistole:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.sistole ?? '-' }} mmHg
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Diastole:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.diastole ?? '-' }} mmHg
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Resp. rate:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.respRate ?? '-' }} bpm
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Heart rate:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.heartRate ?? '-' }} bpm
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-semibold text-dark d-inline-block"
                                                            style="width: 140px;">Suhu:</span>
                                                        {{ pelayanan.simpus_loket.anamnesa?.[0]?.suhu ?? '-' }} °C
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ================== KANAN ================== -->
                        <div class="col-lg-8">
                            <!-- Form Permohonan -->
                            <div class="border rounded-4 mb-4 shadow-sm">
                                <!-- Header -->
                                <div
                                    class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold">Form Permohonan Laboratorium Pasien</h6>
                                    <button class="btn btn-primary btn-sm">Kembali</button>
                                </div>

                                <!-- Body -->
                                <form @submit.prevent="submit" class="p-3 small bg-light rounded-4 shadow-sm">
                                    <div class="row g-3">
                                        <!-- Tanggal dibuat -->
                                        <div class="col-md-4">
                                            <label for="tgl_dibuat" class="form-label fw-semibold">Tgl dibuat</label>
                                            <input type="datetime-local" id="tgl_dibuat" name="tgl_dibuat"
                                                class="form-control" v-model="form.tgl_dibuat">
                                        </div>

                                        <!-- Tenaga medis -->
                                        <div class="col-md-4">
                                            <label for="tenaga_medis" class="form-label fw-semibold">Tenaga Medis
                                                Dokter</label>
                                            <select id="tenaga_medis" name="tenaga_medis" class="form-select"
                                                v-model="form.tenaga_medis">
                                                <option value="">- pilih -</option>
                                                <option :value=item.idDokter v-for="item in tenagaMedisAskep"
                                                    :key=item.idDokter>{{ item.nmDokter }}</option>
                                            </select>
                                        </div>

                                        <!-- Radio button hasil lab -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold mb-1">Pasien sudah memiliki hasil lab
                                                luar faskes?</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check">
                                                    <input type="radio" name="hasil_lab" id="belum" value="belum"
                                                        class="form-check-input" checked
                                                        v-model="form.hasil_lab_luar_faskes">
                                                    <label for="belum" class="form-check-label">Belum</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" name="hasil_lab" id="sudah" value="sudah"
                                                        class="form-check-input" v-model="form.hasil_lab_luar_faskes">
                                                    <label for="sudah" class="form-check-label">Sudah</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Alasan dirujuk -->
                                        <div class="col-12">
                                            <label for="alasan" class="form-label fw-semibold">Alasan dirujuk</label>
                                            <textarea id="alasan" name="alasan" class="form-control" rows="3"
                                                placeholder="Tuliskan alasan rujukan pasien..."
                                                v-model="form.alasan"></textarea>
                                        </div>

                                        <!-- Tombol -->
                                        <div class="col-12 text-end mt-2">
                                            <button type="submit" class="btn btn-success btn-sm px-3">
                                                <i class="bi bi-plus-circle me-1"></i> Buat Permohonan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Data Permohonan -->
                            <div class="border rounded-4 mb-4">
                                <div class="p-3 border-bottom bg-light">
                                    <h6 class="m-0 fw-bold">Data Permohonan</h6>
                                </div>
                                <div class="p-3 table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>No.</th>
                                                <th>Tgl dibuat</th>
                                                <th>Tenaga Medis Perujuk</th>
                                                <th>Tenaga Medis Pemeriksa</th>
                                                <th>Poli</th>
                                                <th>Alasan</th>
                                                <th>Hasil lab luar faskes</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="loading">
                                            <tr>
                                                <td colspan="9" class="text-center">Memuat data...</td>
                                            </tr>
                                        </tbody>

                                        <tbody v-else>
                                            <tr v-for="(item, index) in PermohonanLabs" :key="item.idPermohonan"
                                                class="text-center">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.tglDibuat }}</td>
                                                <td>{{ item.tenaga_medis.nmDokter }}</td>
                                                <td>-</td>
                                                <td>{{ item.nmPoli ?? '-' }}</td>
                                                <td>{{ item.alasanDirujuk }}</td>
                                                <td>{{ item.hasilLabLuarFaskes == 1 ? 'Ya' : 'Tidak' }}</td>
                                                <td>
                                                    <span class="badge bg-danger" v-if="item.statusLayanan == 0">Belum
                                                        dilayani</span>
                                                    <span class="badge bg-success" v-if="item.statusLayanan != 0">Sudah dilayani</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger me-1"><i
                                                            class="bi bi-trash"></i></button>
                                                    <button class="btn btn-sm btn-primary me-1"><i
                                                            class="bi bi-plus"></i></button>
                                                    <button class="btn btn-sm btn-success"><i
                                                            class="bi bi-printer"></i></button>
                                                </td>
                                            </tr>

                                            <tr v-if="PermohonanLabs.length === 0">
                                                <td colspan="9" class="text-center text-muted">Belum ada permohonan
                                                    laborat</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            <!-- Jenis Pemeriksaan -->
                            <div class="border rounded-4">
                                <div
                                    class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold">Jenis Pemeriksaan Laborat</h6>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-clockwise"></i> Reload
                                    </button>
                                </div>

                                <div class="p-3">
                                    <table class="table table-bordered align-middle mb-3">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Kode</th>
                                                <th>Nama pemeriksaan</th>
                                                <th>Nilai Normal/kritis</th>
                                                <th>Nilai Lab (satuan)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td>1</td>
                                                <td>HB001</td>
                                                <td>Hemoglobin</td>
                                                <td>13–17 g/dL</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input class="form-control" value="15" />
                                                        <span class="input-group-text">g/dL</span>
                                                    </div>
                                                </td>
                                                <td><button class="btn btn-outline-danger btn-sm"><i
                                                            class="bi bi-trash"></i></button></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-end">
                                                    <button class="btn btn-success btn-sm">
                                                        <i class="bi bi-check2-square"></i> UPDATE NILAI LAB
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayouts>
</template>

<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';
import { onMounted, ref } from 'vue';
const page = usePage();

const pasien = page.props.Pasien;
const pelayanan = page.props.Pelayanan
const tenagaMedisAskep = page.props.tenagaMedisAskep
const PermohonanLabs = ref(null);
const loading = ref(true)
console.log('labnyaaaaaaaaaaaaaa', PermohonanLabs.value)
const form = useForm({
    tgl_dibuat: '',
    tenaga_medis: '',
    hasil_lab_luar_faskes: '',
    alasan: '',
    idPelayanan: pelayanan.idpelayanan ?? '',
    idPoli: pelayanan.kdPoli,
    idPasien: pelayanan.simpus_loket.pasienId
});
onMounted(() => {
    fetchPermohonanLab(route('ruang-layanan.getPermonanLab', { idLoket: pelayanan.loketId }));
});

function submit() {
    form.post(route('ruang-layanan.simpan-permohonan-lab', {
        idLoket: pelayanan.loketId
    }), {
        onSuccess: () => {
            alert('sukses')
            fetchPermohonanLab(route('ruang-layanan.getPermonanLab', { idLoket: pelayanan.loketId }));
        }
    });
}
function fetchPermohonanLab(url) {
    if (!url) return;

    const relativeUrl = url.startsWith('http')
        ? new URL(url).pathname + new URL(url).search
        : url;

    axios.get(relativeUrl)
        .then(res => {
            PermohonanLabs.value = res.data.PermohonanLabs;
            console.log('Hasil fetch Permohonan Lab:', res.data.PermohonanLabs);
        })
        .catch(err => console.error(err))
        .finally(() => {
            loading.value = false;
            console.log('selesai', loading.value)
        });
}
</script>

<style>
@page {
    size: A4;
    margin: 14mm 12mm;
}

/* HANYA untuk mode print — tampilkan cuma .print-sheet */
@media print {

    /* Sembunyikan SEMUA konten halaman */
    body * {
        visibility: hidden !important;
    }

    /* Tampilkan hanya lembar cetak + isinya */
    .print-sheet,
    .print-sheet * {
        visibility: visible !important;
    }

    /* Posisikan lembar cetak memenuhi halaman */
    .print-sheet {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        z-index: 9999;
    }

    /* Jaga-jaga: matikan layout/header/footers app kalau ada */
    .app-shell,
    header,
    .navbar,
    .sidebar,
    .card-header,
    footer {
        display: none !important;
    }
}

/* Default: sembunyikan print-sheet saat layar biasa */
@media screen {
    .print-sheet {
        display: none;
    }
}

/* ====== Gaya print-sheet ====== */
.print-sheet {
    font-family: "Times New Roman", serif;
    color: #000;
    width: 190mm;
    margin: 0 auto;
    font-size: 12pt;
}

.ps-head {
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
}

.ps-logo {
    width: 58px;
    height: 58px;
    object-fit: contain;
}

.ps-title {
    flex: 1;
    text-align: center;
    font-weight: bold;
    font-size: 14pt;
    line-height: 1.25;
}

.ps-sub {
    font-size: 10pt;
    font-weight: normal;
}

.ps-report-title {
    text-align: center;
    margin: 12px 0 8px;
    text-decoration: underline;
}

.ps-info {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
}

.ps-info td {
    padding: 2px 4px;
    vertical-align: top;
}

.ps-section {
    margin-top: 12px;
    font-weight: bold;
    background: #f3f4f6;
    border: 1px solid #000;
    border-bottom: 0;
    padding: 4px 6px;
}

.ps-table {
    width: 100%;
    border-collapse: collapse;
}

.ps-table th,
.ps-table td {
    border: 1px solid #000;
    padding: 6px;
}

.ps-table th {
    text-align: center;
    font-weight: bold;
}

.t-center {
    text-align: center;
}

.t-small {
    font-size: 10pt;
}

.muted {
    color: #555;
}

.ps-sign {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.ps-sign .col {
    width: 48%;
    text-align: center;
}

.ps-footnote {
    margin-top: 14px;
    font-size: 10pt;
    color: #444;
}

.w-1 {
    width: 36px;
}

.w-2 {
    width: 90px;
}
</style>
<style scoped>
.gradient-btn {
    background: linear-gradient(135deg, #3b82f6, #10b981) !important;
    border: none !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.gradient-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 .5rem 1rem rgba(16, 185, 129, .15);
}

.table th,
.table td {
    vertical-align: middle;
}

/* --- Simple Modal --- */
.modal-mask {
    position: fixed;
    z-index: 1050;
    inset: 0;
    background: rgba(0, 0, 0, .45);
    display: grid;
    place-items: center;
    padding: 1rem;
}

.modal-card {
    width: 100%;
    max-width: 720px;
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, .2);
    overflow: hidden;
}

.modal-card.modal-xl {
    max-width: 1100px;
}

/* yang daftar master */
.modal-header,
.modal-footer {
    padding: .75rem 1rem;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-body {
    padding: 1rem;
}

.btn-close {
    border: 0;
    background: transparent;
}
</style>
