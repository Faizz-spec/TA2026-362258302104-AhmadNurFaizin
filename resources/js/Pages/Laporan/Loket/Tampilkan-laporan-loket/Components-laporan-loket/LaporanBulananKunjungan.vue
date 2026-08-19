<template>
  <div class="mt-3">
    <h6 class="fw-bold text-center">LAPORAN BULANAN DATA KUNJUNGAN</h6>

    <div class="mb-3">
      <div>Unit : PUSKESMAS</div>
      <div>Nama Unit : REKAP GABUNGAN</div>
      <div>Tanggal : 02-12-2024 - 05-08-2025</div>
    </div>

    <table class="table table-bordered table-sm text-center align-middle">
      <thead>
        <tr>
          <th rowspan="3">KATEGORI PASIEN</th>
          <th colspan="12">JUMLAH KUNJUNGAN LOKET</th>
          <th rowspan="3">UMUR > 55 DIRUJUK</th>
          <th colspan="9">KUNJUNGAN KASUS RAWAT JALAN</th>
          <th colspan="6">KUNJUNGAN PASIEN</th>
        </tr>
        <tr>
          <th colspan="3">BARU</th>
          <th colspan="3">LAMA</th>
          <th colspan="3">BARU > 55</th>
          <th colspan="3">LAMA > 55</th>
          <th colspan="3">BARU</th>
          <th colspan="3">LAMA</th>
          <th colspan="3">BARU > 55</th>
          <th colspan="3">LAMA > 55</th>
          <th colspan="3">SEHAT</th>
          <th colspan="3">SAKIT</th>
        </tr>
        <tr>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
          <th>L</th><th>P</th><th>JML</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, index) in laporan" :key="index">
          <td>{{ row.kategori }}</td>
          <td>{{ row.loketBaru.L }}</td><td>{{ row.loketBaru.P }}</td><td>{{ row.loketBaru.JML }}</td>
          <td>{{ row.loketLama.L }}</td><td>{{ row.loketLama.P }}</td><td>{{ row.loketLama.JML }}</td>
          <td>{{ row.loketBaru55.L }}</td><td>{{ row.loketBaru55.P }}</td><td>{{ row.loketBaru55.JML }}</td>
          <td>{{ row.loketLama55.L }}</td><td>{{ row.loketLama55.P }}</td><td>{{ row.loketLama55.JML }}</td>
          <td>{{ row.rjBaru.L }}</td><td>{{ row.rjBaru.P }}</td><td>{{ row.rjBaru.JML }}</td>
          <td>{{ row.rjLama.L }}</td><td>{{ row.rjLama.P }}</td><td>{{ row.rjLama.JML }}</td>
          <td>{{ row.rjBaru55.L }}</td><td>{{ row.rjBaru55.P }}</td><td>{{ row.rjBaru55.JML }}</td>
          <td>{{ row.rjLama55.L }}</td><td>{{ row.rjLama55.P }}</td><td>{{ row.rjLama55.JML }}</td>
          <td>{{ row.sehat.L }}</td><td>{{ row.sehat.P }}</td><td>{{ row.sehat.JML }}</td>
          <td>{{ row.sakit.L }}</td><td>{{ row.sakit.P }}</td><td>{{ row.sakit.JML }}</td>
        </tr>
      </tbody>
    </table>

    <div class="text-end mt-4">
      <div>Banyuwangi, 06-08-2025</div>
      <div>Mengetahui</div>
      <div class="fw-bold">Kepala PUSKESMAS WONGSOREJO</div>
      <br /><br />
      <div>NS.H.M.Shadiq. S.Kep.M.MKes</div>
      <div>NIP. 19641110 198502 1 002</div>
    </div>
  </div>
</template>

<script setup>
const laporan = [
  {
    kategori: '2. NON BPJS',
    loketBaru: { L: 9, P: 9, JML: 18 },
    loketLama: { L: 25, P: 59, JML: 84 },
    loketBaru55: { L: 0, P: 0, JML: 0 },
    loketLama55: { L: 0, P: 0, JML: 0 },
    rjBaru: { L: 5, P: 23, JML: 28 },
    rjLama: { L: 6, P: 11, JML: 17 },
    rjBaru55: { L: 0, P: 0, JML: 0 },
    rjLama55: { L: 0, P: 0, JML: 0 },
    sehat: { L: 2, P: 4, JML: 6 },
    sakit: { L: 32, P: 64, JML: 96 },
  },
  {
    kategori: '3. BAYAR',
    loketBaru: { L: 0, P: 0, JML: 0 },
    loketLama: { L: 0, P: 1, JML: 1 },
    loketBaru55: { L: 1, P: 0, JML: 1 },
    loketLama55: { L: 0, P: 0, JML: 0 },
    rjBaru: { L: 0, P: 0, JML: 0 },
    rjLama: { L: 0, P: 0, JML: 0 },
    rjBaru55: { L: 0, P: 0, JML: 0 },
    rjLama55: { L: 0, P: 0, JML: 0 },
    sehat: { L: 0, P: 0, JML: 0 },
    sakit: { L: 1, P: 1, JML: 2 },
  },
];
</script>

<style scoped>
table th,
table td {
  vertical-align: middle !important;
  font-size: 13px;
}
</style>