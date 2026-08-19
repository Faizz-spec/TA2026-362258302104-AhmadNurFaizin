<template>
    <div class="card m-4 rounded-4 rounded-bottom-0">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between p-3 rounded-4 rounded-bottom-0"
            style="background: linear-gradient(135deg, #3b82f6, #10b981);">
            <h1 class="fs-5 text-white mb-0">Form Surat Keterangan</h1>

            <Link :href="backRoute" class="btn bg-white bg-opacity-25 border btn-sm text-white">
            <i class="bi bi-arrow-left me-1"></i> Kembali
            </Link>
        </div>

        <!-- Body -->
        <div class="card-body">
            <form @submit.prevent="submit" class="space-y-3">
                <!-- Data Pasien -->
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard text-primary"></i> Data Pasien
                    </h6>

                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small mb-1 fw-semibold">No MR</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa.loket.simpus_pasien.NO_MR ?? '-'" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small mb-1 fw-semibold">Nama</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa.loket.simpus_pasien.NAMA_LGKP ?? '-'" disabled>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Tempat Lahir</label>
                                    <input type="text" class="form-control"
                                        v-model.trim="props.dataAnamnesa.loket.simpus_pasien.TMPT_LHR"
                                        placeholder="Isi tempat lahir" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Tgl Lahir</label>
                                    <input  class="form-control"
                                        :value="formatDate(props.dataAnamnesa.loket.simpus_pasien.TGL_LHR)" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label  small mb-1 fw-semibold">Agama</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa?.loket.simpus_pasien?.AGAMA" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label  small mb-1 fw-semibold">Pekerjaan</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa?.loket.simpus_pasien?.pkrjnmaster?.DESCRIP" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label  small mb-1 fw-semibold">Alamat</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa?.loket.simpus_pasien?.ALAMAT" disabled>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-semibold">Kecamatan</label>
                                    <input type="text" class="form-control"
                                        :value="props.dataAnamnesa?.loket.simpus_pasien.setup_kab.NAMA_KAB ?? ''"
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Kel/Desa</label>
                                    <input type="text" class="form-control"
                                        :value="props.dataAnamnesa?.loket.simpus_pasien?.setup_kel?.NAMA_KEL ?? ''"
                                        disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Surat -->
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-paper text-primary"></i> Data Surat
                    </h6>

                    <div class="row g-5">
                        <div class="col-md-6">

                            <!-- Jenis Surat -->
                            <div class="mb-2">
                                <label class="form-label  small mb-1 fw-semibold">Jenis Surat</label>
                                <select class="form-select" v-model="form.id_jns_surat">
                                    <option value="" selected>--Pilih--</option>
                                    <option :value="item.ID_JNS_SURAT" v-for="item in props.jenisSurat">{{ item.SURAT
                                        }}</option>

                                </select>
                                <div v-if="form.errors.id_jns_surat" class="invalid-feedback d-block">
                                    {{ form.errors.id_jns_surat }}
                                </div>
                            </div>

                            <!-- No Surat -->
                            <div class="mb-2">
                                <label class="form-label small mb-1 fw-semibold">No Surat</label>
                                <input type="text" class="form-control" v-model.trim="form.no_surat"
                                    placeholder="No surat rujukan">
                                <div v-if="form.errors.no_surat" class="invalid-feedback d-block">
                                    {{ form.errors.no_surat }}
                                </div>
                            </div>

                            <!-- Tanggal Ijin -->
                            <div class="mb-2" v-if="form.id_jns_surat === 2">
                                <label class="form-label  small mb-1 fw-semibold">Tanggal Ijin</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control" v-model="form.tgl_ijin_awal">
                                    <span class="fw-bold">s/d</span>
                                    <input type="date" class="form-control" v-model="form.tgl_ijin_akhir">
                                </div>
                            </div>
                            <!-- Tanggal/Jam Kematian -->
                            <div class="mb-2" v-if="form.id_jns_surat === 5">
                                <label class="form-label  small mb-1 fw-semibold">Tanggal / Jam Kematian</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control" v-model="form.tgl_kematian">
                                    <input type="time" class="form-control" v-model="form.jam_kematian">
                                </div>
                            </div>
                            <!-- Keterangan Kematian -->
                            <div class="mb-2" v-if="form.id_jns_surat === 5">
                                <label class="form-label s small mb-1 fw-semibold">Keterangan Kematian</label>
                                <textarea class="form-control" v-model="form.ket_kematian" rows="3"></textarea>
                            </div>

                            <!-- Keperluan -->
                            <div class="mb-2">
                                <label class="form-label small mb-1 fw-semibold">Keperluan</label>
                                <textarea type="text" class="form-control" v-model.trim="form.keperluan"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Data Pemeriksaan -->
                <div class="bg-white rounded-4 shadow-sm px-4 py-5 my-4"
                    v-if="form.id_jns_surat !== 5 && form.id_jns_surat !== 6 && form.id_jns_surat !== ''">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-heart-pulse text-primary"></i> Data Pemeriksaan
                    </h6>

                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="row g-4 mb-3">
                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-semibold">Tinggi Badan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.tinggiBadan">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>


                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-semibold">Berat Badan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.beratBadan">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Respiratory Rate</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.respRate">
                                        <span class="input-group-text">/minute</span>
                                    </div>
                                </div>


                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Heart Rate</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.heartRate">
                                        <span class="input-group-text">bpm</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-3">
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Sistole</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.sistole">
                                        <span class="input-group-text">mmHg</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-semibold">Diastole</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.diastole">
                                        <span class="input-group-text">mmHg</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-3">
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Suhu</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.suhu">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Mata Ka/Ki</label>
                                    <select class="form-select" v-model="form.mata_ka_ki">
                                        <option value="" selected>--Pilih--</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Tidak Baik">Tidak Baik</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-4 mb-3">
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Telinga Ka/Ki</label>
                                    <select class="form-select" v-model="form.telinga_ka_ki">
                                        <option value="" selected>--Pilih--</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Tidak Baik">Tidak Baik</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label  small mb-1 fw-semibold">Tes Buta Warna</label>
                                    <select class="form-select" v-model="form.test_buta_warna">
                                        <option value="" selected>--Pilih--</option>
                                        <option value="Normal">Normal</option>
                                        <option value="Partial">partial</option>
                                        <option value="Total">Total</option>
                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="mb-3">
                                <label class="form-label small mb-1 fw-semibold">Keterangan Lain</label>
                                <textarea class="form-control" v-model="form.ket_lain"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label  small mb-1 fw-semibold">Hasil Pemeriksaan</label>
                                <textarea class="form-control" v-model="form.hasil_pemeriksaan"></textarea>
                                <div v-if="form.errors.hasil_pemeriksaan" class="invalid-feedback d-block">
                                    {{ form.errors.hasil_pemeriksaan }}
                                </div>
                            </div>


                            <div class="mb-3">
                                <label class="form-label  small mb-1 fw-semibold">Dokter Jaga</label>
                                <select class="form-select" v-model="form.tenagaMedis">
                                    <option value="" selected>--Pilih Dokter --</option>
                                    <option v-for="item in props.tenagaMedisAskep" :value="item.idDokter"> {{
                                        item.nmDokter }}</option>
                                </select>
                                <div v-if="form.errors.tenagaMedis" class="invalid-feedback d-block">
                                    {{ form.errors.tenagaMedis }}
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="submit"
                                        class="btn btn-primary text-white rounded-3">
                                        <span><i class="bi bi-save me-1"></i> Simpan</span>
                                       
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Actions -->


            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    dataPasien: { type: Object, required: true },     // { idLoket, NO_MR, NAMA_LGKP, ... }
    backRoute: { type: String, required: true },     // url untuk tombol kembali
    saveRoute: { type: String, required: true },     // route POST simpan rujukan
    rumahSakitOptions: { type: Array, default: () => [] }, // [{kdppk, nama_unit}]
    poliOptions: { type: Array, default: () => [] }, // [{kdPoli, nmPoli}]
    dokterOptions: { type: Array, default: () => [] }, // [{kode, nama}],
    jenisSurat: Array,
    dataAnamnesa: Object,
    tenagaMedisAskep: Array,
    idPelayanan: String,
    idPoli: String
})

console.log('Data anamnesa Suket', props.dataAnamnesa)
const form = useForm({
    id_jns_surat: '',
    id_pelayanan: props.idPelayanan ?? '',
    no_surat: '',
    keperluan: props.dataAnamnesa?.keluhan ?? '',
    keterangan: '',
    hasil_pemeriksaan: props.dataAnamnesa?.hasil_pemeriksaan ?? '',
    mata_ka_ki: props.dataAnamnesa?.mata_ka_ki ?? '',
    telinga_ka_ki: props.dataAnamnesa?.telinga_ka_ki ?? '',
    test_buta_warna: props.dataAnamnesa?.test_buta_warna ?? '',
    tgl_ijin_awal: '',
    tgl_ijin_akhir: '',
    tgl_kematian: '',
    jam_kematian: '',
    ket_kematian: '',
    tenagaMedis: props.dataAnamnesa?.tenagaMedis ?? '',
    idAnamnesa: props.dataAnamnesa?.idAnamnesa,

    beratBadan: props.dataAnamnesa?.beratBadan ?? '',
    tinggiBadan: props.dataAnamnesa?.tinggiBadan ?? '',
    respRate: props.dataAnamnesa?.respRate ?? '',
    heartRate: props.dataAnamnesa?.heartRate ?? '',
    suhu: props.dataAnamnesa?.suhu ?? '',
    sistole: props.dataAnamnesa?.sistole ?? '',
    diastole: props.dataAnamnesa?.diastole ?? '',
    ket_lain: props.dataAnamnesa?.ket_lain ?? '',
    tempat_lahir: props.dataAnamnesa?.tempat_lahir ?? '',
    tgl_lahir: props.dataAnamnesa?.tgl_lahir ?? '',
    agama: props.dataAnamnesa?.agama ?? '',
    pekerjaan: props.dataAnamnesa?.pekerjaan ?? '',
})
function formatDate(tgl) {
    if (!tgl) return "-"
    const date = new Date(tgl);
    console.log(tgl)
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });
}

function submit() {
    form.post(route('ruang-layanan.simpanSuket', {
        idPoli: props.idPoli
    }), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('ruang-layanan.surat-keterangan-list', {
                idPoli: props.idPoli,
                idPelayanan: props.idPelayanan
            }))
        },

    })
}
</script>

<style scoped>
.form-label {
    font-weight: 600;
}

.card-body {
    background: #f8fafc;
    border-radius: 0 0 1rem 1rem;
}
</style>
