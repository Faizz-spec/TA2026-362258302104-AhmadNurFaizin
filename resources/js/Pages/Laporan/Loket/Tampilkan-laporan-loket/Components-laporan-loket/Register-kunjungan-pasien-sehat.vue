<template>
  <div class="container mt-3">
    <h5 class="fw-bold text-center mb-4">LAPORAN REGISTER KUNJUNGAN PASIEN SEHAT</h5>

    <div class="mb-3 small">
      <div><strong>Unit :</strong> SEMUA UNIT</div>
      <div><strong>Nama Unit :</strong> REKAP GABUNGAN - PUSKESMAS WONGSOREJO</div>
      <div><strong>Tanggal :</strong> 02-12-2024 - 05-08-2025</div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm text-center align-middle table-hover">
<thead class="align-middle fw-semibold text-dark text-center text-nowrap">
  <tr>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">NO</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">TGL KUNJ</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">NAMA</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">ALAMAT</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">NO BPJS</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">NO PASIEN</th>
    <th rowspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">L/P</th>
    <th colspan="10" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">GOLONGAN UMUR (TAHUN)</th>
    <th colspan="3" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">TIPE PASIEN</th>
    <th colspan="7" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">LAYANAN</th>
    <th colspan="2" style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">STATUS</th>
  </tr>
  <tr>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">&lt;1</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">1-4</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">5-9</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">10-14</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">15-19</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">20-44</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">45-54</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">55-59</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">60-69</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">&gt;70</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">BPJS</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">NON BPJS</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">BAYAR</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">BP</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">GIGI</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">KIA</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">LAB</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">UGD</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">KB</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">Kunj. Sehat</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">BARU</th>
    <th style="background-color:#cfe2ff !important; border:1px solid #dee2e6 !important;">LAMA</th>
  </tr>
</thead>

        <tbody>
          <tr v-for="(row, index) in rows" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ row.tanggal }}</td>
            <td>{{ row.nama }}</td>
            <td class="text-start text-truncate" style="max-width: 250px;">{{ row.alamat }}</td>
            <td>{{ row.bpjs }}</td>
            <td>{{ row.noPasien }}</td>
            <td>{{ row.lp }}</td>
            <td v-for="(u, i) in umurKolom" :key="i">
              {{ row.umur === u ? '✓' : '' }}
            </td>
            <td>{{ row.tipePasien === 'BPJS' ? '✓' : '' }}</td>
            <td>{{ row.tipePasien === 'NON BPJS' ? '✓' : '' }}</td>
            <td></td>
            <td></td>
            <td>✓</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ row.status === 'BARU' ? '✓' : '' }}</td>
            <td>{{ row.status === 'LAMA' ? '✓' : '' }}</td>
          </tr>

          <!-- Baris jumlah -->
          <tr class="fw-bold">
            <td colspan="15" class="text-end">JUMLAH</td>
            <td>0</td>
            <td>4</td>
            <td colspan="8"></td>
            <td>2</td>
            <td>2</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-end mt-4 small">
      <div>Banyuwangi, 06-08-2025</div>
      <div class="mb-1">Mengetahui,</div>
      <div class="fw-bold">Kepala PUSKESMAS WONGSOREJO</div>
      <br /><br />
      <div>NS.H.M.Shadiq. S.Kep.M.MKes</div>
      <div>NIP. 19641110 198502 1 002</div>
    </div>
  </div>
</template>


<script setup>
const umurKolom = ['<1', '1-4', '5-9', '10-14', '15-19', '20-44', '45-54', '55-59', '60-69'];

const rows = [
  {
    tanggal: '05-08-2025',
    nama: 'DENIS HIDAYAT',
    alamat: 'PERUM. GRIYA PESONA KARANGREJO BLOK DE-15 - Karangrejo - Banyuwangi',
    bpjs: '0139183',
    noPasien: '0139183',
    lp: 'L',
    umur: '20-44',
    tipePasien: 'NON BPJS',
    status: 'LAMA',
  },
  {
    tanggal: '22-07-2025',
    nama: 'AJENG NINA RISKI',
    alamat: 'DUSUN KRAJAN II - Setail - Genteng',
    bpjs: '0139184',
    noPasien: '0139184',
    lp: 'P',
    umur: '20-44',
    tipePasien: 'NON BPJS',
    status: 'BARU',
  },
  {
    tanggal: '22-07-2025',
    nama: 'AJENG NINA RISKI',
    alamat: 'DUSUN KRAJAN II - Setail - Genteng',
    bpjs: '0139184',
    noPasien: '0139184',
    lp: 'P',
    umur: '20-44',
    tipePasien: 'NON BPJS',
    status: 'LAMA',
  },
  {
    tanggal: '04-08-2025',
    nama: 'DENIS HIDAYAT',
    alamat: 'PERUM. GRIYA PESONA KARANGREJO BLOK DE-15 - Karangrejo - Banyuwangi',
    bpjs: '0139183',
    noPasien: '0139183',
    lp: 'L',
    umur: '20-44',
    tipePasien: 'NON BPJS',
    status: 'BARU',
  },
];
</script>
