<template>
  <AppLayout title="Laporan Register Sanitasi">
    <div class="container-fluid py-3" style="font-family:'Segoe UI', sans-serif;">
      <div class="text-center fw-bold mb-2">LAPORAN REGISTER SANITASI</div>
      <div class="mb-3" style="font-size:13px;">
        <div>Unit : {{ header.unit }}</div>
        <div>Nama Unit : {{ header.nama_unit }}</div>
        <div>Tanggal : {{ header.awal }} - {{ header.akhir }}</div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
          <thead>
            <tr class="text-white" style="background:#22b8cf;">
              <th>NO</th><th>TANGGAL</th><th>NAMA</th><th>ALAMAT</th><th>NO BPJS</th>
              <th>NO PASIEN</th><th>L/P</th><th>UMUR</th><th>HASIL WAWANCARA</th>
              <th>TINDAKAN / SARAN</th><th>INTERFENSI</th><th>K. BINAAN</th><th>K. RISTI</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!rows || rows.length===0">
              <td class="text-center" colspan="13">Tidak ada data</td>
            </tr>
            <tr v-for="(r,i) in rows" :key="i">
              <td class="text-center">{{ i+1 }}</td>
              <td class="text-center">{{ r.tanggal }}</td>
              <td>{{ r.nama }}</td>
              <td>{{ r.alamat }}</td>
              <td class="text-center">{{ r.no_bpjs }}</td>
              <td class="text-center">{{ r.no_pasien }}</td>
              <td class="text-center">{{ r.gender }}</td>
              <td class="text-center">{{ r.umur }}</td>
              <td style="white-space:pre-wrap;">{{ r.hasil_wawancara }}</td>
              <td style="white-space:pre-wrap;">{{ r.tindakan_saran }}</td>
              <td class="text-center">{{ r.intervensi }}</td>
              <td class="text-center">{{ r.kader_binaan }}</td>
              <td class="text-center">{{ r.kader_risiko }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'

const props = defineProps({
  header: { type: Object, default: () => ({ unit:'', nama_unit:'', awal:'', akhir:'' }) },
  rows:   { type: Array,  default: () => [] }
})
</script>
