<template>
    <div class="card m-4 rounded-4 rounded-bottom-0">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between p-3 rounded-4 rounded-bottom-0"
            style="background: linear-gradient(135deg, #3b82f6, #10b981);">
            <h1 class="fs-5 text-white mb-0">Form Surat Rujukan</h1>

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

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">No MR</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa.loket.simpus_pasien.NO_MR ?? '-'" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Nama</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa.loket.simpus_pasien.NAMA_LGKP ?? '-'" disabled>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Tempat Lahir</label>
                                    <input type="text" class="form-control"
                                        v-model.trim="props.dataAnamnesa.loket.simpus_pasien.TMPT_LHR"
                                        placeholder="Isi tempat lahir" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Tgl Lahir</label>
                                    <input type="date" class="form-control"
                                        v-model="props.dataAnamnesa.loket.simpus_pasien.TGL_LHR" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Agama</label>
                                <input type="text" class="form-control"
                                    v-model.trim="props.dataAnamnesa.loket.simpus_pasien.AGAMA" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Pekerjaan</label>
                                <input type="text" class="form-control"
                                    v-model.trim="props.dataAnamnesa.loket.simpus_pasien.JENIS_PKRJN" disabled>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Alamat</label>
                                <input type="text" class="form-control"
                                    :value="props.dataAnamnesa.loket.simpus_pasien.ALAMAT" disabled>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Kecamatan</label>
                                    <input type="text" class="form-control"
                                        :value="props.dataAnamnesa.loket.simpus_pasien.setup_kab.NAMA_KAB ?? ''"
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Kel/Desa</label>
                                    <input type="text" class="form-control"
                                        :value="props.dataAnamnesa.loket.simpus_pasien.setup_kel.NAMA_KEL ?? ''"
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

                    <div class="row g-3">
                        <div class="col-md-6">

                            <!-- Jenis Surat -->
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Jenis Surat</label>
                                <select class="form-select" v-model="form.id_jns_surat">
                                    <option value="" selected>--Pilih--</option>
                                    <option :key="item.ID_JNS_SURAT" :value="item.ID_JNS_SURAT"
                                        v-for="item in props.jenisSurat">{{ item.SURAT
                                        }}</option>

                                </select>
                            </div>

                            <!-- No Surat -->
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">No Surat</label>
                                <input type="text" class="form-control" v-model.trim="form.no_surat"
                                    placeholder="No surat rujukan">
                            </div>

                            <!-- Tanggal Ijin -->
                            <div class="mb-2" v-if="form.id_jns_surat === 2">
                                <label class="form-label">Tanggal Ijin</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control" v-model="form.tgl_ijin_awal">
                                    <span class="fw-bold">s/d</span>
                                    <input type="date" class="form-control" v-model="form.tgl_ijin_akhir">
                                </div>
                            </div>
                            <!-- Tanggal/Jam Kematian -->
                            <div class="mb-2" v-if="form.id_jns_surat === 5">
                                <label class="form-label small text-muted mb-1">Tanggal / Jam Kematian</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control" v-model="form.tgl_kematian">
                                    <input type="time" class="form-control" v-model="form.jam_kematian">
                                </div>
                            </div>
                            <!-- Keterangan Kematian -->
                            <div class="mb-2" v-if="form.id_jns_surat === 5">
                                <label class="form-label small text-muted mb-1">Keterangan Kematian</label>
                                <textarea class="form-control" v-model="form.ket_kematian" rows="3"></textarea>
                            </div>

                            <!-- Keperluan -->
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Keperluan</label>
                                <textarea type="text" class="form-control" v-model.trim="form.keperluan"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- error bag -->
                    <div v-if="hasErrors" class="alert alert-warning mt-3 rounded-3">
                        <ul class="mb-0 small">
                            <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
                        </ul>
                    </div>
                </div>
                <!-- Data Pemeriksaan -->
                <div class="bg-white rounded-4 shadow-sm p-4 mt-4"
                    v-if="form.id_jns_surat !== 5 && form.id_jns_surat !== 6">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-heart-pulse text-primary"></i> Data Pemeriksaan
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Tinggi Badan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.tinggiBadan">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Berat Badan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.beratBadan">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Respiratory Rate</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="form.respRate">
                                    <span class="input-group-text">/minute</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Heart Rate</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="form.heartRate">
                                    <span class="input-group-text">bpm</span>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Sistole</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.sistole">
                                        <span class="input-group-text">mmHg</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Diastole</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="form.diastole">
                                        <span class="input-group-text">mmHg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Suhu</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="form.suhu">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Mata Ka/Ki</label>
                                <select class="form-select" v-model="form.mata_ka_ki">
                                    <option value="Baik">Baik</option>
                                    <option value="Tidak Baik">Tidak Baik</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Telinga Ka/Ki</label>
                                <select class="form-select" v-model="form.telinga_ka_ki">
                                    <option value="Baik">Baik</option>
                                    <option value="Tidak Baik">Tidak Baik</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Tes Buta Warna</label>
                                <select class="form-select" v-model="form.test_buta_warna">
                                    <option value="Normal">Normal</option>
                                    <option value="Partial">partial</option>
                                    <option value="Total">Total</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Keterangan Lain</label>
                                <textarea class="form-control" v-model="form.keterangan"></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Hasil Pemeriksaan</label>
                                <textarea class="form-control" v-model="form.hasil_pemeriksaan"></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Dokter Jaga</label>
                                <select class="form-select" v-model="form.tenagaMedis">
                                    <option value="" selected>--Pilih Dokter --</option>
                                    <option v-for="item in props.tenagaMedisAskep" :value="item.idDokter"> {{
                                        item.nmDokter }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <Link :href="backRoute" class="btn w-100 btn-outline-secondary rounded-3">
                        Kembali
                        </Link>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" :disabled="formProcessing"
                            class="btn btn-info text-white w-100 rounded-3">
                            <span v-if="!formProcessing"><i class="bi bi-save me-1"></i> Simpan</span>
                            <span v-else>Menyimpan…</span>
                        </button>
                    </div>
                </div>


            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    suket: Object,
    dataAnamnesa: Object,
    idPoli: String,
    idPelayanan: String,
    jenisSurat: Object,
    tenagaMedisAskep: Array,
})

console.log('idPElayanananan', props.idPelayanan)
const form = useForm({
    idSurat: props.suket.id_surat,
    id_jns_surat: props.suket.id_jns_surat ?? '',
    id_pelayanan: props.idPelayanan ?? '',
    no_surat: props.suket.no_surat ?? '',
    keperluan: props.suket.keperluan ?? '',
    hasil_pemeriksaan: props.suket.hasil_pemeriksaan ?? '',
    mata_ka_ki: props.suket?.mata_ka_ki ?? '',
    telinga_ka_ki: props.suket?.telinga_ka_ki ?? '',
    test_buta_warna: props.suket?.test_buta_warna ?? '',
    tgl_ijin_awal: props.suket.tgl_ijin_awal ?? '',
    tgl_ijin_akhir: props.suket.tgl_ijin_akhir ?? '',
    tgl_kematian: props.suket.tgl_kematian ?? '',
    jam_kematian: props.suket.jam_kematian ?? '',
    ket_kematian: props.suket.ket_kematian ?? '',
    tenagaMedis: props.suket.tenagaMedis ?? '',
    idAnamnesa: props.dataAnamnesa.idAnamnesa ?? '',

    beratBadan: props.dataAnamnesa?.beratBadan ?? '',
    tinggiBadan: props.dataAnamnesa?.tinggiBadan ?? '',
    respRate: props.dataAnamnesa?.respRate ?? '',
    heartRate: props.dataAnamnesa?.heartRate ?? '',
    suhu: props.dataAnamnesa?.suhu ?? '',
    sistole: props.dataAnamnesa?.sistole ?? '',
    diastole: props.dataAnamnesa?.diastole ?? '',
    keterangan: props.suket.keterangan ?? '',
    tempat_lahir: props.dataAnamnesa?.tempat_lahir ?? '',
    tgl_lahir: props.dataAnamnesa?.tgl_lahir ?? '',
    agama: props.dataAnamnesa?.agama ?? '',
    pekerjaan: props.dataAnamnesa?.pekerjaan ?? '',
})
function submit() {
    form.post(route('ruang-layanan.update-suket'), {
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
