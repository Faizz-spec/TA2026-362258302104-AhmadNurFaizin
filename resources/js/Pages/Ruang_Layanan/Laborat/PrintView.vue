<template>
  <div class="print-sheet">
    <!-- Header -->
    <div class="head">
      <img src="/logo.png" alt="logo" class="logo" />
      <div class="title">
        <div>PEMERINTAH KABUPATEN BANYUWANGI</div>
        <div>DINAS KESEHATAN</div>
        <div>PUSKESMAS WONGSOREJO</div>
        <div class="sub">JL. Raya Situbondo No.04, Dsn. Kebunrejo RT 01 / RW 01</div>
      </div>
    </div>

    <h3 class="report-title">LEMBAR HASIL LABORATORIUM</h3>

    <!-- Identitas -->
    <table class="info">
      <tr>
        <td>Nama</td><td>: {{ pasien.NAMA_LGKP || '-' }}</td>
        <td>No reg lab</td><td>: {{ order?.no_reg_lab || '-' }}</td>
      </tr>
      <tr>
        <td>Tgl Lahir</td><td>: {{ pasien.TGL_LHR || '-' }}</td>
        <td>Tgl periksa</td><td>: {{ order?.tgl_dibuat || '-' }}</td>
      </tr>
      <tr>
        <td>Jenis kelamin</td><td>: {{ pasien.jenis_klmin || '-' }}</td>
        <td>Spec diambil jam</td><td>: {{ order?.sample_diambil_at || '-' }}</td>
      </tr>
      <tr>
        <td>Alamat</td><td>: {{ alamatLengkap }}</td>
        <td>Spec selesai jam</td><td>: {{ order?.sample_selesai_at || '-' }}</td>
      </tr>
      <tr>
        <td>No RM</td><td>: {{ pasien.NO_MR || '-' }}</td>
        <td>Unit pengirim</td><td>: {{ order?.unit_pengirim || 'Umum' }}</td>
      </tr>
    </table>

    <!-- Tabel pemeriksaan -->
    <div class="section">HEMATOLOGI</div>
    <table class="table">
      <thead>
        <tr>
          <th>No.</th>
          <th>Kode</th>
          <th>Nama pemeriksaan</th>
          <th>Nilai Normal/kritis</th>
          <th>Nilai Lab (satuan)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(r,i) in rows" :key="r.detail_id">
          <td>{{ i+1 }}</td>
          <td>{{ r.kode }}</td>
          <td>{{ r.nama_pemeriksaan }}</td>
          <td>{{ r.nilai_normal_kritis }}</td>
          <td>{{ hasil[r.detail_id] }} {{ r.satuan }}</td>
        </tr>
        <tr v-if="rows.length===0">
          <td colspan="5" class="text-center">Belum ada data</td>
        </tr>
      </tbody>
    </table>

    <!-- Tanda tangan -->
    <div class="sign">
      <div class="col">Dokter<br><br><br>____________</div>
      <div class="col">Pemeriksa<br><br><br>____________</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  pasien: Object,
  order: Object,
  rows: Array,
  hasil: Object,
});

const alamatLengkap = computed(() => {
  const p = props.pasien || {};
  return [p.alamat, `RT ${p.no_rt||'-'}`, `RW ${p.no_rw||'-'}`, p.nama_kel, p.nama_kec, p.nama_kab]
    .filter(Boolean).join(', ');
});
</script>

<style scoped>
.print-sheet { font-family:"Times New Roman",serif; color:#000; }
.head { display:flex; align-items:center; border-bottom:2px solid #000; padding-bottom:6px; }
.logo { width:60px; height:60px; }
.title { flex:1; text-align:center; font-size:14pt; font-weight:bold; }
.sub { font-size:10pt; font-weight:normal; }
.report-title { text-align:center; margin:12px 0; text-decoration:underline; }
.info { width:100%; font-size:11pt; margin-bottom:10px; }
.info td { padding:2px 4px; vertical-align:top; }
.table { width:100%; border-collapse:collapse; margin-top:8px; font-size:11pt; }
.table th,.table td { border:1px solid #000; padding:4px; }
.section { font-weight:bold; margin-top:12px; }
.sign { display:flex; justify-content:space-between; margin-top:30px; font-size:11pt; }
@media screen { .print-sheet { display:none; } }
@media print {
  body * { visibility:hidden; }
  .print-sheet, .print-sheet * { visibility:visible; }
  .print-sheet { position:absolute; left:0; top:0; width:100%; }
}
</style>
