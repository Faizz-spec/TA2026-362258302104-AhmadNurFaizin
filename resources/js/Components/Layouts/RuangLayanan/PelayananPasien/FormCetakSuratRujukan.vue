<template>
    <div class="container surat p-4" style="max-width: 800px;">

        <!-- Header -->
        <div class="row align-items-center mb-2">
            <div class="col-2 text-end">
                <img :src="logoUrl" alt="Logo" class="img-fluid" style="max-width: 90px;" />
            </div>
            <div class="col-8 text-center">
                <h5 class="fw-bold mb-0">PEMERINTAH KABUPATEN BANYUWANGI</h5>
                <h5 class="fw-bold mb-0">DINAS KESEHATAN</h5>
                <h5 class="fw-bold mb-0">{{ props.unit.nama_unit.toUpperCase() }}</h5>
                <p class="small mb-0">{{ props.unit.alamat }}</p>
            </div>
            <div class="col-2"></div>
        </div>

        <hr class="border border-dark border-2 opacity-100 my-2" />

        <!-- Judul -->
        <div class="text-center mb-3">
            <h5 class="fw-bold text-decoration-underline mb-1">SURAT RUJUKAN</h5>
        </div>

        <!-- Info Surat -->
        <table class="table table-borderless table-sm mb-2">
            <tbody>
                <tr>
                    <td class="w-25">Nomor</td>
                    <td>: {{ props.suratRujuk.no_surat }}</td>
                    <td>Kepada Yth.</td>
                    <td>{{ props.suratRujuk.poli_rujukan.nmPoli }}</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>: 1 Berkas</td>
                    <td>{{ props.suratRujuk.provider.nmProvider }}</td>
                    <td>Di BANYUWANGI</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>: Rujukan Pasien</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <p class="mt-3 mb-1">Bersama ini kami kirimkan penderita:</p>

        <!-- Data Pasien -->
        <table class="table table-borderless table-sm">
            <tbody>
                <tr>
                    <td class="w-25">Nama</td>
                    <td>: {{ props.dataPasien.NAMA_LGKP }}</td>
                </tr>
                <tr>
                    <td>Tgl Lahir / Umur</td>
                    <td>: {{ formatTanggal(props.dataPasien.TGL_LHR) }} / {{ props.dataPasien.NAMA_LGKP }} tahun</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: {{ props.dataPasien.JENIS_KLMIN == 1 ? 'Laki - laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>Alamat / RT / RW</td>
                    <td>
                        : {{ props.dataPasien.ALAMAT }}<br />
                        RT/RW : {{ props.dataPasien.NO_RT }}/{{ props.dataPasien.NO_RW }}<br />
                        Desa/Kel : {{ props.dataPasien.NAMA_KEL }}<br />
                        Kecamatan : {{ props.dataPasien.NAMA_KEC }}<br />
                        Kabupaten : {{ props.dataPasien.NAMA_KAB }}
                    </td>
                </tr>
                <tr>
                    <td>No. RM</td>
                    <td>: {{ props.dataPasien.NO_MR }}</td>
                </tr>
                <tr>
                    <td>No. KTP / Domisili</td>
                    <td>: {{ props.dataPasien.NIK }} / {{ props.dataPasien.ALAMAT }}</td>
                </tr>
                <tr>
                    <td>No. Telp / HP</td>
                    <td>: {{ props.dataPasien.PHONE }}</td>
                </tr>
                <tr>
                    <td>Keluhan Utama</td>
                    <td>: {{ props.dataAnamnesa.keluhan }}</td>
                </tr>
                <tr>
                    <td>Hasil Pemeriksaan</td>
                    <td>
                        : Kondisi :  {{ props.dataAnamnesa.kondisi ?? '' }}<br />
                        Kesadaran : {{ props.dataAnamnesa.kesadaran.nmSadar }}<br />
                        Sistole : {{  props.dataAnamnesa.sistole }} mmHg, Diastole : {{  props.dataAnamnesa.diastole }} mmHg<br />
                        Respirate Rate : {{  props.dataAnamnesa.respRate }} /menit, Heart Rate : {{  props.dataAnamnesa.heartRate }} bpm
                    </td>
                </tr>
                <tr>
                    <td>Diagnosa Sementara</td>
                    <td>: {{ props.dataAnamnesa.loket.diagnosa.nmDiagnosa }} </td>
                </tr>
                <tr>
                    <td>Therapy yang sudah diberikan</td>
                    <td>: {{ props.dataAnamnesa.terapi ?? '' }}</td>
                </tr>
            </tbody>
        </table>

        <p class="mt-2">
            Mohon pemeriksaan / pengobatan dan perawatan selanjutnya.<br />
            Atas bantuan dan kerjasamanya kami ucapkan terima kasih.
        </p>

        <!-- Tanda Tangan -->
        <div class="text-end mt-4">
            <p class="mb-0">Banyuwangi, {{ formatTanggal(new Date()) }}</p>
            <p class="mb-0">Dokter Pemeriksa</p>
            <div style="height: 70px;"></div>
            <p class="fw-bold mb-0">{{ props.suratRujuk.tenaga_medis.nmDokter }}</p>
            <p class="mb-0">NIP. {{  props.suratRujuk.tenaga_medis.NIP }}</p>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps(
    {
        suratRujuk: Object,
        dataPasien : Object,
        dataAnamnesa : Object,
        unit : Object
    }
)

console.log('data pasien',props.dataPasien)
console.log('data anamnesa', props.dataAnamnesa)
console.log(props.suratRujuk)
const logoUrl = '/images/logo-dinkes.png'

// Format tanggal Indonesia
const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
const formatTanggal = (tgl) => {
    const date = new Date(tgl)
    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`
}
</script>

<style scoped>
.surat {
    font-family: "Times New Roman", serif;
    color: #000;
    background: #fff;
    line-height: 1.4;
}

.table td {
    padding: 2px 4px !important;
    vertical-align: top;
}

hr {
    margin-top: 4px;
    margin-bottom: 8px;
}

@media print {

    body,
    html {
        background: white !important;
        -webkit-print-color-adjust: exact;
        margin: 0;
        padding: 0;
    }

    .container.surat {
        box-shadow: none !important;
        margin: 0;
    }
}
</style>
