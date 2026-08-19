    <template>
        <div class="container surat p-3">
            <!-- Header -->
            <div class="row align-items-center text-center mb-2">
                <div class="col-2 text-end">
                    <img :src="logoUrl" class="img-fluid" style="max-width:100px;" />
                </div>
                <div class="col-8">
                    <h5 class="mb-0 fw-bold">PEMERINTAH KABUPATEN BANYUWANGI</h5>
                    <h5 class="mb-0 fw-bold">DINAS KESEHATAN</h5>
                    <h5 class="mb-0 fw-bold">{{ unit.nama_unit }}</h5>
                    <p class="mb-0 small">{{ unit.alamat }}</p>
                </div>
                <div class="col-2"></div>
            </div>

            <hr class="border border-dark border-2 opacity-100 my-2" />
            <!-- Judul Surat -->
            <div class="text-center my-2">
                <h6 class="fw-bold text-decoration-underline mb-1">SURAT RUJUKAN</h6>
            </div>

            <div>
                <div class="row">
                    <div class="col-8">
                        <div class="row">
                            <div class="col-4">
                                Nomor
                            </div>
                            <div class="col-8">
                                : {{ suratRujuk.no_surat }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                Lampiran
                            </div>
                            <div class="col-8">
                                : 1 Berkas
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                Perihal
                            </div>
                            <div class="col-8">
                                : Rujukan pasien
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <span>
                            Kepada Yth. <br>
                            {{ suratRujuk.poli_rujukan.nmPoli }} <br>
                            {{suratRujuk.provider.nmProvider }} <br>
                            Di Banyuwangi
                        </span>
                    </div>
                </div>
                <p>Bersama ini kami kirimkan penderita :</p>
                <div>
                    <div class="row">
                        <div class="col-3">
                            Nama
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.NAMA_LGKP }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Tgl Lahir / Umur
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.TMPT_LHR }} / {{ dataPasien.TGL_LHR }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Jenis Kelamin
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.JENIS_KLMIN == 2 ? 'Perempuan' : 'Laki-Laki' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Alamat / RT / RW
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.ALAMAT }}
                            <div class="ms-2">
                                <div class="row">
                                    <div class="col-3">
                                        RT/RW
                                    </div>
                                    <div class="col-9">
                                        : {{ dataPasien.NO_RT }} / {{ dataPasien.NO_RW }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Desa/Kel
                                    </div>
                                    <div class="col-9">
                                        : {{ dataPasien.NAMA_KEL }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Kecamatan
                                    </div>
                                    <div class="col-9">
                                        : {{ dataPasien.NAMA_KEC }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Kabupaten
                                    </div>
                                    <div class="col-9">
                                        : {{ dataPasien.NAMA_KAB }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            No. RM
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.NO_MR }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            No. KTP / Domisili
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.NIK }} / {{ dataPasien.ALAMAT }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            No. Telp / HP yang dapat dihubungi
                        </div>
                        <div class="col-9">
                            : {{ dataPasien.PHONE }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Keluhan Utama
                        </div>
                        <div class="col-9">
                            : {{ dataAnamnesa.keluhan }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Hasil Pemeriksaan
                        </div>
                        <div class="col-9">

                            <div class="ms-2">
                                <div class="row">
                                    <div class="col-3">
                                        Kondisi
                                    </div>
                                    <div class="col-9">
                                        : ALFIN
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Kesadaran
                                    </div>
                                    <div class="col-9">
                                        : {{ dataAnamnesa.kesadaran?.nmSadar }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Sistole
                                    </div>
                                    <div class="col-9">
                                        : {{ dataAnamnesa.sistole }} mmHg
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Diastole
                                    </div>
                                    <div class="col-9">
                                        : {{ dataAnamnesa.diastole }} mmHg
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Respirate Rate
                                    </div>
                                    <div class="col-9">
                                        : {{ dataAnamnesa.respRate }} /minute
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Heart Rate
                                    </div>
                                    <div class="col-9">
                                        : {{ dataAnamnesa.heartRate }} bpm
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Diagnosa Sementara
                        </div>
                        <div class="col-9" v-for="value in diagnosa">
                            : {{ value.nmDiagnosa }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Therapy yang sudah diberikan
                        </div>
                        <div class="col-9">
                            : {{ dataAnamnesa.terapi }}
                        </div>
                    </div>

                </div>
            </div>
            <div>
                Mohon pemeriksaan / Pengobatan dan perawatan selanjutnya.
                Atas bantuan dan kerjasamanya kami ucapkan terima kasih.
            </div>


            <!-- Tanda Tangan -->
            <div class="text-end mt-0">
                <p class="mb-0">Banyuwangi, {{ formattedDate }}</p>
                <p class="mb-0">Dokter Pemeriksa</p>
                <div style="height: 40px;"></div>
                <p class="mb-0">{{ dokter.nmDokter }}</p>
            </div>
        </div>
    </template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
const page = usePage();

const dataPasien = page.props.dataPasien
const suratRujuk = page.props.suratRujuk
const dataAnamnesa = page.props.dataAnamnesa
const unit = page.props.unit
const dokter = page.props.dokter
const Kecamatan = page.props.kecamatan
const diagnosa = page.props.diagnosa
console.log('data pasien', dataPasien)
console.log('surat', suratRujuk)

const today = new Date();
const options = { day: "numeric", month: "long", year: "numeric" };
const formattedDate = today.toLocaleDateString("id-ID", options);
const logoUrl = "/images/logo-dinkes.png";

onMounted(()=>{
    print()
})
function print() {
    setTimeout(() => window.print(), 500);
}
</script>

<style scoped>
.surat {
    max-width: 700px;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
    color: #000;
}
</style>
