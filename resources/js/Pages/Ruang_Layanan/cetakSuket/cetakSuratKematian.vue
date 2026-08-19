<template>
    <div class="container surat p-3">
        <!-- Header -->
        <div class="row align-items-center text-center mb-2">
            <div class="col-2 text-end">
                <img :src="logoUrl" class="img-fluid" style="max-width:100px;" />
            </div>
            <div class="col-8">
                <h5 class="mb-0 fw-bold text-uppercase">PEMERINTAH KABUPATEN BANYUWANGI</h5>
                <h5 class="mb-0 fw-bold text-uppercase">DINAS KESEHATAN</h5>
                <h5 class="mb-0 fw-bold text-uppercase">{{ unit.nama_unit }}</h5>
                <p class="mb-0 small">{{ unit.alamat }}</p>
            </div>
            <div class="col-2"></div>
        </div>

        <hr class="border border-dark border-2 opacity-100 my-2" />

        <!-- Judul Surat -->
        <div class="text-center my-2">
            <h6 class="fw-bold text-decoration-underline mb-1">SURAT KETERANGAN KEMATIAN</h6>
            <p class="mb-0">Nomor {{ suket.no_surat }}</p>
        </div>

        <!-- Intro -->
        <p class="mb-2">
            Kami yang bertanda tangan di bawah ini Dokter {{ unit.nama_unit }}, Kecamatan
            {{ Kecamatan.NAMA_KEC }}, Kabupaten Banyuwangi, dengan ini menerangkan bahwa:
        </p>

        <!-- Data Pasien -->
        <table class="table table-borderless table-sm m-0">
            <tbody>
                <tr>
                    <td class="w-25">Nama</td>
                    <td>: {{ dataPasien.NAMA_LGKP }}</td>
                </tr>
                <tr>
                    <td>Tempat, Tgl Lahir</td>
                    <td>: {{ dataPasien.TMPT_LHR }}, {{ dataPasien.TGL_LHR }}</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: {{ dataPasien.JENIS_KLMIN == 1 ? 'Laki-Laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>Warga Negara</td>
                    <td>: Indonesia</td>
                </tr>
                <tr>
                    <td>Agama</td>
                    <td>: {{ dataPasien.AGAMA }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>: {{ dataPasien.JENIS_PKRJN }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td>
                        : {{ dataPasien.ALAMAT }}<br />
                        <table style="border-collapse: collapse;" class="ms-2 mt-1">
                            <tr>
                                <td style="width: 40%;">RT/RW</td>
                                <td>: {{ dataPasien.NO_RT }}/{{ dataPasien.NO_RW }}</td>
                            </tr>
                            <tr>
                                <td style="width: 40%;">Desa/Kel</td>
                                <td>: {{ dataPasien.NAMA_KEL }}</td>
                            </tr>
                            <tr>
                                <td style="width: 40%;">Kecamatan</td>
                                <td>: {{ dataPasien.NAMA_KEC }}</td>
                            </tr>
                            <tr>
                                <td style="width: 40%;">Kabupaten</td>
                                <td>: {{ dataPasien.NAMA_KAB }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Keperluan</td>
                    <td>: {{ suket.keperluan }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Penutup -->
        <p class="mt-3 mb-2">
            Pada tanggal {{ formatDate(suket.tgl_kematian) }} pukul {{ suket.jam_kematian }} WIB,
            yang bersangkutan dinyatakan <strong class="text-uppercase">meninggal dunia</strong>.<br />
            Demikian surat keterangan kematian ini kami terbitkan untuk dapat digunakan sebagaimana mestinya.
        </p>

        <!-- Tanda Tangan -->
        <!-- <div class="text-end mt-0">
            <p class="mb-0">Banyuwangi, {{ formattedDate }}</p>
            <p class="mb-0">Dokter</p>
        </div> -->
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import {onMounted } from 'vue';
const page = usePage();

const dataPasien = page.props.dataPasien
const suket = page.props.suket
const dataAnamnesa = page.props.dataAnamnesa
const unit = page.props.unit
const Kecamatan = page.props.kecamatan
console.log('data pasien', dataPasien)
console.log('suket', suket)

const today = new Date();
const options = { day: "numeric", month: "long", year: "numeric" };
const formattedDate = today.toLocaleDateString("id-ID", options);
const logoUrl = "/images/logo-dinkes.png";

function formatDate(dateStr) {
  if (!dateStr) return "-";
  const [year, month, day] = dateStr.split("-");
  return `${day}-${month}-${year}`;
}

onMounted(()=>{
    print()
})
function print(){
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
