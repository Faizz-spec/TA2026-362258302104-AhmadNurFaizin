<template>
  <div class="bg-white p-3 rounded-4 shadow-sm">
    <!-- Header identitas pasien -->
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>
        <div><strong>Tgl reg.</strong> : {{ dataPasien.tgl_reg }}</div>
        <div><strong>No MR</strong> : {{ dataPasien.no_mr }}</div>
        <div><strong>Nama</strong> : {{ dataPasien.nama }}</div>
      </div>
      <div class="text-end">
        <div><strong>NIK</strong> : {{ dataPasien.nik || '-' }}</div>
        <div>
          <strong>Alamat</strong> : 
          {{ dataPasien.alamat }},
          Desa {{ dataPasien.kel }}, Kec. {{ dataPasien.kec }},
          {{ dataPasien.kab }} - {{ dataPasien.prop }}
        </div>
      </div>
    </div>

    <h5 class="text-center mb-3 fw-bold">Data Riwayat Kesehatan ( Medical Record ) Pasien</h5>

    <!-- Tabel riwayat -->
    <div class="table-responsive">
      <table class="table table-bordered align-top">
        <thead class="table-light">
          <tr class="text-center">
            <th style="width:40px">No</th>
            <th style="width:110px">Tgl Kunjung</th>
            <th style="width:180px">Lokasi</th>
            <th style="width:140px">Status pasien</th>
            <th style="width:260px">Anamnesa</th>
            <th style="width:220px">Lab</th>
            <th style="width:220px">Diagnosa</th>
            <th style="width:220px">Tindakan</th>
            <th style="width:240px">Obat</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in riwayat" :key="row.no">
            <td class="text-center">{{ row.no }}</td>
            <td class="text-center">{{ row.tgl }}</td>
            <td>{{ row.lokasi }}</td>
            <td>{{ row.status }}</td>
            <td style="white-space: pre-wrap">{{ row.anamnesa }}</td>
            <td style="white-space: pre-wrap">{{ row.lab }}</td>
            <td style="white-space: pre-wrap">{{ row.diagnosa }}</td>
            <td style="white-space: pre-wrap">{{ row.tindakan }}</td>
            <td style="white-space: pre-wrap">{{ row.obat }}</td>
          </tr>
          <tr v-if="!riwayat?.length">
            <td colspan="9" class="text-center text-muted">Belum ada riwayat.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Tombol cetak -->
    <div class="d-flex justify-content-end gap-2">
      <button class="btn btn-outline-secondary btn-sm" @click="window.print()">Cetak</button>
    </div>
  </div>
</template>

<script setup>

const props = defineProps({
  dataPasien: { type: Object, required: true },
  riwayat: { type: Array, required: true },
})

</script>

<style scoped>
@media print {
  .btn, .card-header { display: none !important; }
  .table { font-size: 11px; }
  .bg-white { box-shadow: none !important; }
  body { background: #fff; }
}
</style>
