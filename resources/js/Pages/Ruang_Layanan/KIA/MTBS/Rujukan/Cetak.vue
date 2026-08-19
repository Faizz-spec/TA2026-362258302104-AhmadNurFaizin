<template>
  <div class="page-wrap">
    <div class="toolbar no-print">
      <Link
        :href="route('mtbs.rujukan.index')"
        class="btn btn-light border"
      >
        Kembali
      </Link>

      <Link
        :href="route('mtbs.rujukan.create', surat.kunjungan_id)"
        class="btn btn-warning"
      >
        Edit
      </Link>

      <button
        type="button"
        class="btn btn-primary"
        @click="printSurat"
      >
        Print
      </button>
    </div>

    <div class="surat">
      <!-- =========================
           KOP SURAT
      ========================== -->
      <header class="kop-surat">
        <!-- Logo Kabupaten Banyuwangi -->
        <div class="logo-wrapper">
          <img
            :src="logoBanyuwangi"
            alt="Logo Kabupaten Banyuwangi"
            class="logo-kop logo-kabupaten"
          >
        </div>

        <!-- Identitas Puskesmas -->
        <div class="kop-text">
          <h3>PEMERINTAH KABUPATEN BANYUWANGI</h3>
          <h3>DINAS KESEHATAN</h3>
          <h2>UPTD PUSKESMAS WONGSOREJO</h2>

          <p>
            Jl. Raya Situbondo No. 4, Dusun Kebunrejo, Desa Alasrejo,
            Kecamatan Wongsorejo
          </p>

          <p>
            Kabupaten Banyuwangi, Jawa Timur 68453
          </p>

          <p>
            Telepon: (0333) 461486
          </p>
        </div>

        <!-- Logo Puskesmas -->
        <div class="logo-wrapper">
          <img
            :src="logoPuskesmas"
            alt="Logo Puskesmas Wongsorejo"
            class="logo-kop logo-puskesmas"
          >
        </div>
      </header>

      <div class="garis-kop">
        <div class="garis-tebal"></div>
        <div class="garis-tipis"></div>
      </div>

      <!-- =========================
           JUDUL SURAT
      ========================== -->
      <section class="identitas-surat">
        <h4 class="judul">
          SURAT PENGANTAR RUJUKAN
        </h4>

        <p class="nomor-surat">
          Nomor: {{ surat.no_surat || '-' }}
        </p>
      </section>

      <!-- =========================
           TUJUAN SURAT
      ========================== -->
      <section class="tujuan-surat">
        <div class="perihal">
          <table>
            <tbody>
              <tr>
                <td>Perihal</td>
                <td>:</td>
                <td>Surat Pengantar Rujukan</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="penerima">
          <p>Kepada Yth.</p>

          <p class="nama-rumah-sakit">
            {{ surat.rumah_sakit || '-' }}
          </p>

          <p v-if="surat.poli_tujuan">
            Poli {{ surat.poli_tujuan }}
          </p>

          <p>di Tempat</p>
        </div>
      </section>

      <!-- =========================
           ISI SURAT
      ========================== -->
      <section class="isi-surat">
        <p>Dengan hormat,</p>

        <p>
          Bersama ini kami kirimkan seorang pasien untuk mendapatkan
          pemeriksaan, konsultasi, dan penanganan lebih lanjut:
        </p>

        <!-- Identitas pasien -->
        <table class="data-table">
          <tbody>
            <tr>
              <td class="label">Nama</td>
              <td class="separator">:</td>
              <td>{{ surat.NAMA_LGKP || '-' }}</td>
            </tr>

            <tr>
              <td class="label">Nomor Rekam Medis</td>
              <td class="separator">:</td>
              <td>{{ surat.NO_MR || '-' }}</td>
            </tr>

            <tr>
              <td class="label">NIK</td>
              <td class="separator">:</td>
              <td>{{ surat.NIK || '-' }}</td>
            </tr>

            <tr>
              <td class="label">Umur</td>
              <td class="separator">:</td>
              <td>{{ surat.umur_label || '-' }}</td>
            </tr>

            <tr>
              <td class="label">Jenis Kelamin</td>
              <td class="separator">:</td>
              <td>{{ surat.jenis_kelamin_label || '-' }}</td>
            </tr>

            <tr>
              <td class="label">Alamat</td>
              <td class="separator">:</td>
              <td>{{ alamatLengkap }}</td>
            </tr>
          </tbody>
        </table>

        <p class="pengantar-pemeriksaan">
          Berdasarkan pemeriksaan yang telah kami lakukan, diperoleh hasil
          sebagai berikut:
        </p>

        <!-- Hasil pemeriksaan -->
        <ol class="hasil-pemeriksaan">
          <li>
            <strong>Anamnesis</strong>

            <div class="hasil-text">
              {{ surat.anamnesa || '-' }}
            </div>
          </li>

          <li>
            <strong>Pemeriksaan Fisik</strong>

            <div class="hasil-text">
              {{ surat.pemeriksaan_fisik || '-' }}
            </div>
          </li>

          <li>
            <strong>Diagnosis Sementara</strong>

            <div class="hasil-text">
              {{ surat.diagnosa_sementara || '-' }}
            </div>
          </li>
        </ol>

        <div
          v-if="surat.catatan"
          class="catatan"
        >
          <strong>Catatan:</strong>

          <div>
            {{ surat.catatan }}
          </div>
        </div>

        <p class="penutup">
          Demikian surat pengantar rujukan ini kami sampaikan. Mohon
          pemeriksaan, konsultasi, dan perawatan selanjutnya. Atas bantuan
          dan kerja samanya, kami ucapkan terima kasih.
        </p>
      </section>

      <!-- =========================
           TANDA TANGAN
      ========================== -->
      <footer class="ttd-wrap">
        <div class="ttd-box">
          <p>Yang menerima rujukan,</p>

          <div class="ruang-tanda-tangan"></div>

          <p class="nama-ttd">
            (........................................)
          </p>
        </div>

        <div class="ttd-box">
          <p>Wongsorejo, {{ tanggalCetak }}</p>
          <p>Dokter UPTD Puskesmas Wongsorejo</p>

          <div class="ruang-tanda-tangan"></div>

          <p class="nama-dokter">
            {{ surat.dokter_jaga || 'Dokter Jaga' }}
          </p>

          <p v-if="surat.sip_dokter">
            SIP. {{ surat.sip_dokter }}
          </p>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import AppLayouts from '../../../../../Components/Layouts/AppLayouts.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({
  layout: AppLayouts,
})

const props = defineProps({
  surat: {
    type: Object,
    default: () => ({}),
  },
})

/*
|--------------------------------------------------------------------------
| Lokasi Logo
|--------------------------------------------------------------------------
| public/images/logo-banyuwangi.png
| public/images/logo-puskesmas.png
*/
const logoBanyuwangi = '/images/logo-banyuwangi.png'
const logoPuskesmas = '/images/logo-puskesmas.png'

const surat = computed(() => {
  return props.surat || {}
})

const alamatLengkap = computed(() => {
  const data = surat.value

  const bagianAlamat = [
    data.alamat,
    data.no_rt ? `RT ${data.no_rt}` : '',
    data.no_rw ? `RW ${data.no_rw}` : '',
    data.nama_kel ? `Desa/Kel. ${data.nama_kel}` : '',
    data.nama_kec ? `Kec. ${data.nama_kec}` : '',
    data.nama_kab ? `Kab. ${data.nama_kab}` : '',
    data.nama_prop || '',
  ]

  return bagianAlamat
    .filter((bagian) => bagian && bagian !== '-')
    .join(', ') || '-'
})

const tanggalCetak = computed(() => {
  const tanggal = surat.value.tanggal_rujuk

  if (!tanggal) {
    return '-'
  }

  const tanggalString = String(tanggal).substring(0, 10)
  const objekTanggal = new Date(`${tanggalString}T00:00:00`)

  if (Number.isNaN(objekTanggal.getTime())) {
    return tanggal
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  }).format(objekTanggal)
})

const printSurat = () => {
  window.print()
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.page-wrap {
  min-height: 100vh;
  padding: 20px;
  background: #e9eef4;
}

.toolbar {
  display: flex;
  max-width: 800px;
  margin: 0 auto 12px;
  gap: 8px;
  justify-content: flex-end;
}

.surat {
  width: 800px;
  min-height: 1120px;
  margin: 0 auto;
  padding: 32px 50px 45px;
  background: #ffffff;
  color: #111111;
  font-family: "Times New Roman", Times, serif;
  font-size: 14px;
  line-height: 1.45;
  box-shadow: 0 2px 12px rgb(0 0 0 / 10%);
}

/* =========================
   KOP SURAT
========================= */

.kop-surat {
  display: grid;
  grid-template-columns: 95px minmax(0, 1fr) 95px;
  align-items: center;
  gap: 10px;
  min-height: 120px;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-kop {
  display: block;
  width: auto;
  max-width: 82px;
  height: auto;
  max-height: 95px;
  object-fit: contain;
}

.logo-kabupaten {
  max-width: 78px;
}

.logo-puskesmas {
  max-width: 82px;
}

.kop-text {
  min-width: 0;
  text-align: center;
}

.kop-text h2,
.kop-text h3,
.kop-text p {
  margin: 0;
}

.kop-text h3 {
  font-size: 17px;
  font-weight: 700;
  line-height: 1.2;
}

.kop-text h2 {
  margin-top: 2px;
  font-size: 20px;
  font-weight: 700;
  line-height: 1.2;
}

.kop-text p {
  font-size: 12px;
  line-height: 1.35;
}

.garis-kop {
  margin-top: 7px;
  margin-bottom: 20px;
}

.garis-tebal {
  height: 3px;
  background: #111111;
}

.garis-tipis {
  height: 1px;
  margin-top: 2px;
  background: #111111;
}

/* =========================
   JUDUL SURAT
========================= */

.identitas-surat {
  margin-bottom: 20px;
  text-align: center;
}

.judul {
  display: inline-block;
  margin: 0;
  border-bottom: 1px solid #111111;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.3px;
}

.nomor-surat {
  margin: 3px 0 0;
}

/* =========================
   TUJUAN SURAT
========================= */

.tujuan-surat {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 30px;
  margin-bottom: 22px;
}

.perihal table {
  border-collapse: collapse;
}

.perihal td {
  padding: 0 5px 0 0;
  vertical-align: top;
}

.penerima {
  padding-left: 25px;
}

.penerima p {
  margin: 0;
}

.nama-rumah-sakit {
  font-weight: 700;
}

/* =========================
   ISI SURAT
========================= */

.isi-surat p {
  margin: 0 0 12px;
  text-align: justify;
}

.data-table {
  width: 100%;
  margin: 8px 0 20px;
  border-collapse: collapse;
}

.data-table td {
  padding: 2px 4px;
  vertical-align: top;
}

.data-table .label {
  width: 165px;
  padding-left: 18px;
}

.data-table .separator {
  width: 15px;
  text-align: center;
}

.pengantar-pemeriksaan {
  margin-top: 16px !important;
}

.hasil-pemeriksaan {
  margin: 8px 0 20px;
  padding-left: 35px;
}

.hasil-pemeriksaan li {
  margin-bottom: 12px;
  padding-left: 3px;
}

.hasil-text {
  min-height: 22px;
  margin-top: 3px;
  padding-left: 2px;
  white-space: pre-line;
  text-align: justify;
}

.catatan {
  margin: 14px 0;
  padding: 8px 10px;
  border: 1px solid #444444;
  white-space: pre-line;
}

.penutup {
  margin-top: 22px !important;
  text-indent: 35px;
}

/* =========================
   TANDA TANGAN
========================= */

.ttd-wrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  margin-top: 55px;
  text-align: center;
}

.ttd-box p {
  margin: 0;
}

.ruang-tanda-tangan {
  height: 75px;
}

.nama-ttd {
  white-space: nowrap;
}

.nama-dokter {
  display: inline-block;
  min-width: 190px;
  padding-bottom: 1px;
  border-bottom: 1px solid #111111;
  font-weight: 700;
}

/* =========================
   PRINT A4
========================= */

@page {
  size: A4 portrait;
  margin: 12mm 15mm;
}

@media print {
  .no-print {
    display: none !important;
  }

  .page-wrap {
    min-height: auto;
    padding: 0;
    background: #ffffff;
  }

  .surat {
    width: 100%;
    min-height: auto;
    margin: 0;
    padding: 0;
    box-shadow: none;
    font-size: 11pt;
  }

  .kop-surat {
    grid-template-columns: 90px minmax(0, 1fr) 90px;
  }

  .logo-kop {
    max-width: 78px;
    max-height: 90px;
  }

  .kop-text h3 {
    font-size: 13.5pt;
  }

  .kop-text h2 {
    font-size: 15.5pt;
  }

  .kop-text p {
    font-size: 9pt;
  }

  .judul {
    font-size: 13pt;
  }

  .ttd-wrap {
    break-inside: avoid;
    page-break-inside: avoid;
  }
}
</style>