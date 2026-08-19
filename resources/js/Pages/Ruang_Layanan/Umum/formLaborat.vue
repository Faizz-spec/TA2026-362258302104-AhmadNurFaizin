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
                                        <h5 class="fw-bold text-primary text-uppercase mb-3">{{ pasien.NAMA_LGKP }}</h5>
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
                            <div v-if="showMaster" class="modal-mask" @click.self="showMaster = false">
                                <div class="modal-card modal-xl">
                                    <div class="modal-header">
                                        <h6 class="m-0">Daftar Jenis Pemeriksaan</h6>
                                        <button class="btn-close" @click="showMaster = false"></button>
                                    </div>


                                    <div class="col-lg-6 px-4 mb-3">
                                        <label class="form-label fw-semibold text-secondary mb-1">
                                            <i class="bi bi-funnel me-1 text-primary"></i>
                                            Cari Jenis Pemeriksaan
                                        </label>

                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-primary text-white border-0 px-3">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" class="form-control border-0 ps-1"
                                                v-model="master.search"
                                                placeholder="Ketik nama atau kode pemeriksaan..."
                                                @keyup.enter="doSearchAll">
                                            <button class="btn btn-primary px-3 fw-semibold" @click="doSearchAll"
                                                style="border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                                                Cari / Tampilkan Semua
                                            </button>
                                        </div>
                                    </div>
                                    <div class="modal-body">

                                        <div class="row g-3 align-items-end mb-3 px-3">
                                            <div class="col-lg-6">
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <button v-for="ph in paketHeaders" :key="'h-' + ph.header"
                                                        class="btn btn-outline-success btn-sm"
                                                        :class="{ 'active': selectedHeader && selectedHeader.header === ph.header }"
                                                        @click="pickHeader(ph)" :disabled="!order">
                                                        {{ ph.header_name || ('Header ' + ph.header) }}
                                                        <span class="badge text-bg-light ms-1">{{ ph.jumlah }}</span>
                                                    </button>
                                                </div>
                                                <div v-if="selectedHeader" class="d-flex flex-wrap gap-2">
                                                    <button v-for="sh in paketSubs" v-if="false"
                                                        :key="'s-' + sh.sub_header"
                                                        class="btn btn-outline-secondary btn-sm"
                                                        @click="addPaketSub(selectedHeader, sh)" :disabled="!order">
                                                        Sub {{ sh.sub_header }} <span
                                                            class="badge text-bg-light ms-1">{{ sh.jumlah }}</span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive border rounded">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th style="width:120px;">Kode</th>
                                                        <th>Jenis Pemeriksaan</th>
                                                        <th style="width:120px;">Satuan</th>
                                                        <th style="width:260px;">Nilai normal / kritis</th>
                                                        <th style="width:180px;">Hasil Lab</th>
                                                        <th style="width:60px;" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="browse.loading">
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4">Memuat...</td>
                                                    </tr>
                                                </tbody>

                                                <tbody v-else-if="!groupedItems.length">
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">Data kosong.
                                                        </td>
                                                    </tr>
                                                </tbody>

                                                <!-- Kelompok per kategori -->
                                                <tbody v-else v-for="(g, gi) in groupedItems" :key="'grp-' + gi">
                                                    <!-- Judul kategori -->
                                                    <tr class="table-secondary">
                                                        <td colspan="6" class="fw-bold text-uppercase">
                                                            {{ g.kategori }}
                                                        </td>
                                                    </tr>

                                                    <!-- Baris item dalam kategori -->
                                                    <tr v-for="(it, idx) in g.items" :key="'m-' + it.id">
                                                        <td class="text-muted" style="width:120px;">{{ it.kode || '-' }}
                                                        </td>
                                                        <td class="fw-semibold">{{ it.nama || '-' }}</td>
                                                        <td style="width:120px;">{{ it.satuan || '-' }}</td>
                                                        <td class="small" style="width:260px;">
                                                            <div class="text-muted white-pre-line">{{
                                                                it.nilaiNormalKritis || '-' }}</div>
                                                        </td>
                                                        <td style="width:180px;">
                                                            <input class="form-control form-control-sm"
                                                                v-model="valueMap[it.id]"
                                                                placeholder="isian opsional" />
                                                        </td>
                                                        <td class="text-center" style="width:60px;">
                                                            <input type="checkbox" class="form-check-input"
                                                                v-model="selectedMap[it.id]" />
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>

                                        <!-- pagination simple -->
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="small text-muted">
                                                Total: {{ master.meta.total || 0 }}
                                            </div>
                                            <div class="btn-group">
                                                <button v-for="(ln, i) in master.links" :key="i" class="btn btn-sm"
                                                    :class="['btn-outline-secondary', ln.active ? 'active' : '']"
                                                    :disabled="!ln.url" @click="gotoLink(ln.url)">
                                                    {{ ln.label }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer d-flex justify-content-between align-items-center">

                                        <!-- Bagian kiri -->
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-light" @click="showMaster = false">Close</button>
                                        </div>

                                        <!-- Bagian kanan -->
                                        <div class="d-flex gap-2 align-items-center">

                                            <!-- Tambah Semua Header (hanya muncul jika header dipilih) -->
                                            <button v-if="selectedHeader"
                                                class="btn btn-success gradient-btn btn-sm text-white fw-semibold"
                                                @click="addPaketHeader(selectedHeader)" :disabled="!order"
                                                title="Tambahkan semua item di header ini">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Tambah SEMUA
                                                ({{ selectedHeader.header_name || ('Header ' + selectedHeader.header)
                                                }})
                                            </button>


                                            <!-- Tombol Simpan -->
                                            <button class="btn btn-success" :disabled="!order" @click="submitSelected">
                                                <i class="bi bi-check2-square me-1"></i> SIMPAN
                                            </button>

                                        </div>
                                    </div>

                                </div>
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
                                                <td>{{ item.tenaga_medis?.nmDokter }}</td>
                                                <td>-</td>
                                                <td>{{ item.nmPoli ?? '-' }}</td>
                                                <td>{{ item.alasanDirujuk }}</td>
                                                <td>{{ item.hasilLabLuarFaskes == 1 ? 'Ya' : 'Tidak' }}</td>
                                                <td>
                                                    <span class="badge bg-danger" v-if="item.statusLayanan == 0">Belum
                                                        dilayani</span>
                                                    <span class="badge bg-success" v-if="item.statusLayanan != 0">Sudah
                                                        dilayani</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger me-1"><i
                                                            class="bi bi-trash"></i></button>
                                                    <button class="btn btn-sm btn-primary me-1"
                                                        @click="openMasterModal"><i class="bi bi-plus"></i></button>
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
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="m-0">Jenis Pemeriksaan Laborat</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- ICON PRINT -->


                                        <button class="btn btn-sm btn-outline-secondary" @click="reloadPage">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reload
                                        </button>

                                        <!-- NEW: Hapus semua -->
                                        <button class="btn btn-danger btn-sm shadow-sm rounded-3 px-3 fw-semibold"
                                            @click="hapusSemua" :disabled="!order || rows.length === 0"
                                            title="Hapus semua item pemeriksaan dalam permohonan ini">
                                            <i class="bi bi-trash3 me-2"></i> Hapus Semua
                                        </button>



                                    </div>
                                </div>

                                <div class="p-3">
                                    <div class="row g-3 mb-3">

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th style="width:56px;">No</th>
                                                    <th style="width:120px;">Kode</th>
                                                    <th>Nama pemeriksaan</th>

                                                    <th style="width:220px;">Nilai Normal/kritis</th>
                                                    <th style="width:240px;">Nilai Lab (satuan)</th>
                                                    <th style="width:100px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="groupedRows.length">
                                                <!-- loop per kategori -->
                                                <template v-for="(g, gi) in groupedRows" :key="'grp-main-'+gi">
                                                    <!-- baris judul kategori -->
                                                    <tr class="table-secondary">
                                                        <!-- 6 kolom: No, Kode, Nama, Nilai normal, Nilai Lab, Aksi -->
                                                        <td colspan="6" class="fw-bold text-uppercase">
                                                            {{ g.kategori }}
                                                        </td>
                                                    </tr>

                                                    <!-- baris item dalam kategori -->
                                                    <tr v-for="(row, i) in g.items" :key="row.detail_id">
                                                        <td class="text-center">{{ i + 1 }}</td>
                                                        <td class="text-center">{{ row.kode }}</td>
                                                        <td>{{ row.nama_pemeriksaan }}</td>

                                                        <td class="small">
                                                            <div class="text-muted white-pre-line">
                                                                {{ row.nilai_normal_kritis || '-' }}
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    v-model="hasil[row.detail_id]"
                                                                    :placeholder="row.satuan ? `(${row.satuan})` : ''" />
                                                                <span class="input-group-text">{{ row.satuan || '-'
                                                                    }}</span>
                                                            </div>
                                                        </td>

                                                        <td class="text-center">
                                                            <button class="btn btn-outline-danger"
                                                                @click="hapusRow(row)" :disabled="!order"
                                                                title="Hapus item ini">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>

                                            <tbody v-else>
                                                <tr>
                                                    <!-- 6 kolom juga -->
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        Belum ada item pemeriksaan.
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>

                                    </div>

                                    <br />
                                    <div class="text-end">
                                        <button class="btn btn-sm btn-success" @click="submitNilai" :disabled="!order">
                                            <i class="bi bi-check2-square me-1"></i> UPDATE NILAI LAB
                                        </button>
                                    </div>

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
import { computed, reactive, ref, watch, isRef, onMounted } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const pasien = page.props.Pasien;
const pelayanan = page.props.Pelayanan
const tenagaMedisAskep = page.props.tenagaMedisAskep
const DataPemeriksaan = page.props.DataPemeriksaan
const idLoket = page.props.idLoket
const PermohonanLabs = ref(null);
const loading = ref(true)
const pageProps = computed(() => (isRef(page?.props) ? (page.props.value || {}) : (page?.props || {})));

// Props dari Inertia
const order = computed(() => PermohonanLabs || null);
const rows = computed(() => pageProps.value.DataPemeriksaan || []);
const objective = computed(() => pageProps.value.DataObjective || null);
const paketHeaders = ref([]);
const paketSubs = ref([]);
const selectedHeader = ref(null);
const selectedKategori = ref(null); 

console.log('labnyaaaaaaaaaaaaaa', DataPemeriksaan)
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

async function loadPaketHeaders() {
    try {
        const url = route('ruang-layanan.laborat.param.headers');
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        paketHeaders.value = await res.json(); // [{header, header_name, jumlah}]
    } catch (e) {
        console.error(e);
        paketHeaders.value = [];
    }
}


function addPaketHeader(headerObj) {
    if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
    const payload = {
        loketId: pelayanan.loketId || '',
        permohonanId: permId.value,
        pelayananId: order.value?.idPelayanan || '',
    };
    router.post(route('ruang-layanan.laborat.param.simpan', { header: headerObj.header }), payload, {
        preserveScroll: true,
        onError: (errs) => {
            showErrors(errs);
            toast('error', 'Gagal menambahkan semua item');
        },
        onSuccess: () => {
            const nama = headerObj.header_name || ('Header ' + headerObj.header);
            toast('success', 'Tambah semua berhasil', nama);
            closeMaster();       // <—— tutup modal di sini
            reloadPage();
        }
    });
}

function hapusSemua() {
   // alert('hapus?')
    if (!order.value || !permId.value) return;

    const ok = confirm(`Hapus SEMUA item pemeriksaan pada permohonan #${permId.value}?`);
    if (!ok) return;

    const payload = { order_id: permId.value };

    router.post(route('ruang-layanan.laborat.hapusSemuaTindakan'), payload, {
        preserveScroll: true,
        onError: (errs) => showErrors(errs),
        onSuccess: () => {
            reloadPage();
            try {
                Swal?.fire({
                    title: 'Selesai',
                    text: 'Semua item pemeriksaan berhasil dihapus.',
                    icon: 'success',
                    timer: 1600,
                    showConfirmButton: false
                });
            } catch { }
        }
    });
}


async function loadParamItems(header, sub = null) {
    try {
        const base = route('ruang-layanan.laborat.param.items', { header });
        const url = new URL(base, window.location.origin);
        if (sub !== null && sub !== undefined) url.searchParams.set('sub_header', String(sub));

        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        const items = await res.json();

        // mapping ke bentuk master.items biar tabelnya tetap jalan
        master.items = (items || []).map(x => ({
            id: x.id_parameter,
            idPemeriksaan: x.id_parameter,
            kode: x.kode,
            nama: x.nama,
            nmPemeriksaanInd: x.nama, // optional
            satuan: x.satuan,
            nilaiNormalKritis: x.nilai_normal_kritis,
        }));
        master.meta = { total: master.items.length };
        master.links = [];
    } catch (e) {
        console.error(e);
        master.items = [];
        master.meta = { total: 0 };
        master.links = [];
    }
}

function submitNilai() {
    if (permId.value === null) {
        alert('Permohonan ID tidak valid.\nSilakan muat ulang halaman.');
        return;
    }
    const payload = {
        order_id: permId.value,
        hasil: Object.entries(hasil.value).map(([detail_id, nilai]) => ({
            detail_id: toInt(detail_id),
            nilai
        })).filter(x => x.detail_id !== null)
    };
    router.post(route('ruang-layanan.laborat.updateNilaiLab'), payload, {
        preserveScroll: true,
        onError: (errs) => showErrors(errs),
        onSuccess: () => {
            alert('✅ Nilai lab berhasil disimpan');
            reloadPage();
        }
    });
}

function addPaketSub(headerObj, sub) {
    if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
    const form = new FormData();
    form.append('loketId', idLoket || '');
    form.append('permohonanId', permId.value);
    if (order.value?.idPelayanan) form.append('pelayananId', order.value.idPelayanan);
    form.append('sub_header', sub.sub_header);

    fetch(route('ruang-layanan.laborat.param.simpan', { header: headerObj.header }), {
        method: 'POST',
        body: form,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then((r) => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            toast('success', 'Tambah sub berhasil', 'Sub ' + sub.sub_header);
            reloadPage();
        })
        .catch((e) => {
            console.error(e);
            toast('error', 'Gagal menambahkan sub');
        });
}

// Saat modal master dibuka, sekalian load header paket

// ===== Helpers umum =====
function toInt(x) {
    if (x === null || x === undefined) return null;
    const n = typeof x === 'string' ? parseInt(x.trim(), 10) : Number(x);
    return Number.isFinite(n) && Number.isInteger(n) ? n : null;
}
function toStr(x) {
    if (x === null || x === undefined) return null;
    const s = String(x).trim();
    return s.length ? s : null;
}

// Ambil ID permohonan sebagai **STRING** (PMH001, dsb.)
const permId = computed(() => {
    const o = order.value || {};
    const fetched = PermohonanLabs.value?.[0] || {}; // ambil objek pertama hasil fetch
    const cands = [
        o?.order_id,
        o?.orderId,
        o?.idPermohonan,
        o?.id,
        pageProps.value?.DataPermohonan?.order_id,
        pageProps.value?.DataPermohonan?.idPermohonan,
        fetched?.idPermohonan,
    ];
    for (const c of cands) {
        const s = toStr(c);
        if (s) return s;
    }
    console.warn('[Laborat] Tidak menemukan permohonan ID.', o, cands);
    return null;
});


function showErrors(errs) {
    try {
        const list = Object.values(errs || {}).flat().map(String);
        if (list.length) alert(list.join('\n'));
    } catch {
        alert('Terjadi kesalahan. Coba lagi.');
    }
}


// ===== Waktu sample & pemeriksa =====
const formWaktu = reactive({
    order_id: null,
    sample_diambil_at: '',
    sample_selesai_at: '',
    tenaga_medis_pemeriksa: '',
});
watch(order, (o) => {
    if (!o) return;
    formWaktu.order_id = toStr(o.order_id ?? o.orderId ?? o.idPermohonan ?? o.id ?? null);
    formWaktu.sample_diambil_at = toInputDateTime(o.sample_diambil_at || o.sampleDiambil || '');
    formWaktu.sample_selesai_at = toInputDateTime(o.sample_selesai_at || o.sampleSelesai || '');
    formWaktu.tenaga_medis_pemeriksa = o.tenaga_medis_pemeriksa || o.tenagaMedisPemeriksa || '';
}, { immediate: true });

const hasil = ref({});
watch(rows, (list) => {
    const map = {};
    (list || []).forEach(r => {
        map[r.detail_id] = (r.nilai_lab !== undefined && r.nilai_lab !== null) ? r.nilai_lab : '';
    });
    hasil.value = map;
}, { immediate: true });

function toInputDateTime(val) {
    if (!val) return '';
    return String(val).replace(' ', 'T').slice(0, 16);
}

function reloadPage() {
    router.reload({
        only: ['DataPemeriksaan'],
        preserveState: true,
        preserveScroll: true,
    });
}
const permForm = reactive({
    idLoket: '',
    idPasien: '',
    idPelayanan: '',
    hasilLabLuarFaskes: '',
    kdPoli: '',
    tenagaMedisPengirim: '',
    alasanDirujuk: '',
    tglDibuat: defaultNow(),
});

// Prefill otomatis
watch(pasien, (p) => {
    permForm.idLoket = p?.idLoket || '';
}, { immediate: true });
watch(order, (o) => {
    permForm.idPasien = o?.pasien_id ?? '';
}, { immediate: true });

function defaultNow() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
// ===== MASTER PEMERIKSAAN (modal) =====
const showMaster = ref(false);
const master = reactive({
    search: '',
    items: [],
    meta: { total: 0 },
    links: [],
    loading: false,
});
const selectedMap = reactive({});
const valueMap = reactive({});

// ==== STATE UNTUK BROWSE SEMUA ====
master.search = '';                 // sudah ada di state mu
const browse = reactive({
    filters: { header: null, sub_header: null }, // untuk paket filter
    loading: false,
});
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


// ambil data ke tabel (parameter_uji) — bisa search + paket filter
async function loadParams(page = 1) {
    browse.loading = true;
    try {
        const url = new URL(route('ruang-layanan.laborat.param.browse'), window.location.origin);
        if (master.search) url.searchParams.set('search', master.search);
        if (browse.filters.header != null) url.searchParams.set('header', String(browse.filters.header));
        if (browse.filters.sub_header != null) url.searchParams.set('sub_header', String(browse.filters.sub_header));

        // <<< TAMBAHAN: filter kategori
        if (selectedKategori.value != null) {
            url.searchParams.set('kategori', String(selectedKategori.value));
        }

        url.searchParams.set('page', String(page));
        url.searchParams.set('per_page', '25');

        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        master.items = (data?.data || []).map((x) => ({
            id: x.id_parameter,
            kode: x.kode,
            nama: x.nama,
            satuan: x.satuan,
            nilaiNormalKritis: x.nilai_normal_kritis,
            // ⬇️ ambil kategori dari apa pun nama field-nya
            kategori: x.kategori_nama ?? x.nama_kategori ?? x.kategori ?? '',
        }));

        master.meta = data?.meta || {};
        master.links = data?.links || [];
    } catch (e) {
        console.error(e);
        master.items = []; master.meta = { total: 0 }; master.links = [];
    } finally {
        browse.loading = false;
    }
}

// klik paging
function gotoLink(url) {
    if (!url) return;
    const u = new URL(url, window.location.origin);
    const p = u.searchParams.get('page') || '1';
    loadParams(Number(p));
}
// tombol: CARI / TAMPILKAN SEMUA
function doSearchAll() {
    browse.filters.header = null;
    browse.filters.sub_header = null;
    loadParams(1);
}

// ----- Paket (header/sub) -----
async function pickHeader(hdr) {
    selectedHeader.value = hdr;
    // tampilkan isi paket di tabel
    browse.filters.header = hdr.header;  // filter ke isi paket
    browse.filters.sub_header = null;
    await loadParams(1);

    // (tetap load daftar sub kalau suatu saat kamu butuh tombol sub)
    try {
        const url = route('ruang-layanan.laborat.param.subheaders', { header: hdr.header });
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        paketSubs.value = await res.json();
    } catch { paketSubs.value = []; }
}
// === Toast helper ===
function toast(icon, title, text = '') {
    try {
        Swal.fire({
            icon, title, text,
            timer: 1600,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true,
        });
    } catch { }
}

const groupedItems = computed(() => {
    const groups = new Map();
    for (const it of master.items) {
        const key = it.kategori || '';
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(it);
    }
    // urutkan kategori (opsional)
    return Array.from(groups, ([kategori, items]) => ({ kategori, items }));
});


// SIMPAN pilihan manual (checkbox)
function submitSelected() {
    if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
    const pilih = Object.entries(selectedMap)
        .filter(([, v]) => !!v)
        .map(([k]) => toInt(k))
        .filter((n) => n !== null);

    if (pilih.length === 0) { alert('Pilih minimal satu pemeriksaan.'); return; }

    const payload = {
        loketId: idLoket || '',
        permohonanId: permId.value,
        pelayananId: order.value?.idPelayanan || '',
        ids: pilih,
        nilaiLab: valueMap,
    };
   // alert('haloo');
    router.post(route('ruang-layanan.laborat.param.simpanTerpilih'), payload, {
        
        preserveScroll: true,
        onError: (errs) => {
            showErrors(errs);
            toast('error', 'Gagal menambahkan item terpilih');
        },
        onSuccess: () => {
            showMaster.value = false;
            toast('success', 'Tambah pemeriksaan berhasil', `${pilih.length} item ditambahkan`);
            reloadPage();
        }
    });
}
function closeMaster() {
    showMaster.value = false;
    // opsional: bersihkan pilihan biar fresh saat buka lagi
    Object.keys(selectedMap).forEach(k => delete selectedMap[k]);
    Object.keys(valueMap).forEach(k => delete valueMap[k]);
    selectedHeader.value = null;
    paketSubs.value = [];
}



// PASTIKAN hanya SATU versi ini
function openMasterModal() {
    //  alert('hai')
    if (!order.value) return;
    // reset dulu
    Object.keys(selectedMap).forEach(k => delete selectedMap[k]);
    Object.keys(valueMap).forEach(k => delete valueMap[k]);
    selectedHeader.value = null;
    paketSubs.value = [];

    showMaster.value = true;
    if (!paketHeaders.value.length) loadPaketHeaders();
    doSearchAll();
}
const groupedRows = computed(() => {
    const list = rows.value || [];
    const groups = new Map();

    for (const r of list) {
        const key = r.kategori || r.nama_kategori || '';
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(r);
    }

    // ubah Map jadi array [{kategori, items: [...]}, ...]
    return Array.from(groups, ([kategori, items]) => ({ kategori, items }));
});
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

/* Tombol print versi besar & cantik */
.btn-print {
    border: 0 !important;
    transition: transform .15s ease, box-shadow .15s ease, filter .2s ease;
}

.btn-print:hover {
    transform: translateY(-1px);
    box-shadow: 0 .75rem 1.5rem rgba(16, 185, 129, .18);
    filter: saturate(1.05);
}

.btn-print:active {
    transform: translateY(0);
    box-shadow: 0 .25rem .75rem rgba(16, 185, 129, .22);
}

.btn-print:focus-visible {
    outline: none;
    box-shadow:
        0 0 0 .2rem rgba(59, 130, 246, .25),
        /* biru */
        0 0 0 .45rem rgba(16, 185, 129, .18);
    /* hijau */
}

/* Gradient-nya selaras dengan tema kamu */
.gradient-btn {
    background: linear-gradient(135deg, #3b82f6, #10b981) !important;
    color: #fff !important;
}

.btn-print[disabled] {
    opacity: .6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
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

/* Tombol merah besar dan cantik */
.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    /* merah gradien */
    border: none;
    color: #fff !important;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(239, 68, 68, 0.25);
}

.btn-danger:active {
    transform: translateY(0);
    box-shadow: 0 0.3rem 0.6rem rgba(185, 28, 28, 0.3);
}

.btn-danger:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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

/* --- Simple Modal (update) --- */
.modal-mask {
    position: fixed;
    z-index: 1050;
    inset: 0;
    background: rgba(0, 0, 0, .45);
    display: grid;
    place-items: center;
    padding: 1rem;
    /* biar aman kalau konten tinggi */
    overflow: auto;
}

.modal-card {
    /* lebar responsif */
    width: min(96vw, 720px);
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, .2);
    overflow: hidden;

    /* KUNCI: jadikan flex column & batasi tinggi */
    display: flex;
    flex-direction: column;
    max-height: 90vh;
    /* cegah full-screen */
}

.modal-card.modal-xl {
    width: min(96vw, 1100px);
    /* modal master lebih lebar */
    max-height: 90vh;
    /* tetap dibatasi tinggi */
}

/* header/footer tetap ukuran kontennya saja */
.modal-header,
.modal-footer {
    padding: .75rem 1rem;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 0 0 auto;
}

/* KUNCI: body yang scroll */
.modal-body {
    padding: 1rem;
    flex: 1 1 auto;
    min-height: 0;
    /* penting supaya flex child boleh overflow */
    overflow: auto;
    /* scroll di dalam modal */
}

/* opsional: sedikit tweak mobile */
@media (max-width: 576px) {

    .modal-card,
    .modal-card.modal-xl {
        border-radius: .75rem;
    }
}

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
