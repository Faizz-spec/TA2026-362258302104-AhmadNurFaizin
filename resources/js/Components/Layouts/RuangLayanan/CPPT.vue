<template>
  <div class="bg-white p-3 rounded-4 shadow-sm">
    <!-- Header identitas -->
    <div class="border rounded p-3 mb-3">
      <div class="row">
        <div class="col-md-6">
          <div><strong>No Rekam Medik</strong> : {{ dataPasien.no_rm || '-' }}</div>
          <div><strong>Nama Pasien</strong> : {{ dataPasien.nama || '-' }}</div>
          <div><strong>Tanggal Lahir</strong> : {{ dataPasien.tgl_lhr || '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>NIK</strong> : {{ dataPasien.nik || '-' }}</div>
          <div><strong>Alamat</strong> :
            {{ dataPasien.alamat }} - {{ dataPasien.kab }} - {{ dataPasien.prop }}
          </div>
          <div><strong>Status</strong> : {{ dataPasien.status || '—' }}</div>
        </div>
      </div>
    </div>

    <h5 class="text-center fw-bold mb-3">CATATAN PERKEMBANGAN PASIEN TERINTEGRASI<br/><small>(Ditulis dengan prinsip SOAP)</small></h5>

    <div class="table-responsive">
      <table class="table table-bordered align-top">
        <thead>
          <tr class="text-center" style="background:#d9f2f2;">
            <th style="width:120px">Tanggal/Jam</th>
            <th style="width:220px">Unit<br/>Ruang layanan</th>
            <th style="width:320px">ANAMNESA DAN PEMERIKSAAN<br/><small>(Subjective - Objective)</small></th>
            <th style="width:280px">DIAGNOSA &amp; KODE ICD X<br/><small>(Assessment)</small></th>
            <th style="width:280px">PERENCANAAN LAYANAN<br/><small>(Planning)</small></th>
            <th style="width:160px">Nama &amp; Paraf Petugas</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(r, idx) in items" :key="idx">
            <td class="text-center">{{ r.tgl }}</td>
            <td style="white-space: pre-wrap"><em>{{ r.unit }}</em></td>
            <td style="white-space: pre-wrap">
              <div><em>*Subjective*</em></div>
              <div>{{ r.S || '—' }}</div>
              <div class="mt-2"><em>*Objective*</em></div>
              <div>{{ r.O || '—' }}</div>
            </td>
            <td style="white-space: pre-wrap">{{ r.A || '—' }}</td>
            <td style="white-space: pre-wrap">{{ r.P || '—' }}</td>
            <td style="white-space: pre-wrap">{{ r.petugas || '—' }}</td>
          </tr>
          <tr v-if="!items?.length">
            <td colspan="6" class="text-center text-muted">Belum ada catatan.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
      <button class="btn btn-outline-secondary btn-sm" @click="window.print()">Cetak</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  dataPasien: { type: Object, required: true },
  items: { type: Array, required: true },
})
</script>

<style scoped>
@media print {
  .btn, .card-header { display: none !important; }
  .table { font-size: 11px; }
  .bg-white { box-shadow: none !important; }
  body { background:#fff; }
}
</style>
