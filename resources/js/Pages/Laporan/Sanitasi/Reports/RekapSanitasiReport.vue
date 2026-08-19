<template>
  <AppLayout title="Laporan Sanitasi">
    <div class="container-fluid py-3" style="font-family:'Segoe UI', sans-serif;">
      <div class="text-center fw-bold mb-2">LAPORAN SANITASI</div>
      <div class="mb-3" style="font-size:13px;">
        <div>Unit : {{ header.unit }}</div>
        <div>Nama Unit : {{ header.nama_unit }}</div>
        <div>Tanggal : {{ header.awal }} - {{ header.akhir }}</div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
          <thead>
            <tr class="text-white" style="background:#22b8cf;">
              <th>No</th><th>Desa</th><th>Jumlah Kunjungan Pasien</th>
              <th>Kasus Berbasis Lingkungan</th><th>% (4/3x100%)</th>
              <th>Pasien yg Konsul ke Klinik Sanitasi</th><th>% (6/4x100%)</th>
              <th>Keluarga Binaan</th><th>Keluarga Risti Kasus Berbasis Lingk.</th><th>% (9/8x100%)</th>
              <th>Tindak Lanjut Kasus yg Konsul di Klinik Sanitasi</th><th>% (11/6x100%)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!rows || rows.length===0">
              <td class="text-center" colspan="12">Tidak ada data</td>
            </tr>
            <tr v-for="(r,i) in rows" :key="i">
              <td class="text-center">{{ i+1 }}</td>
              <td>{{ r.desa }}</td>
              <td class="text-center">{{ r.jml_kunjungan }}</td>
              <td class="text-center">{{ r.kasus_lingkungan }}</td>
              <td class="text-center">{{ pct(r.kasus_lingkungan, r.jml_kunjungan) }}</td>
              <td class="text-center">{{ r.konsul_klinik }}</td>
              <td class="text-center">{{ pct(r.konsul_klinik, r.kasus_lingkungan) }}</td>
              <td class="text-center">{{ r.keluarga_binaan }}</td>
              <td class="text-center">{{ r.keluarga_risiko }}</td>
              <td class="text-center">{{ pct(r.keluarga_risiko, r.keluarga_binaan) }}</td>
              <td class="text-center">{{ r.tindak_lanjut }}</td>
              <td class="text-center">{{ pct(r.tindak_lanjut, r.konsul_klinik) }}</td>
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
function pct(a,b){ if(!b) return '0.00%'; return ((a*100)/b).toFixed(2)+'%' }
</script>
