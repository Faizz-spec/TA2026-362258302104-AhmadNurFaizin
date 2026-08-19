<template>
  <AppLayout title="Jumlah Kasus Sanitasi Berbasis Lingkungan">
    <div class="container-fluid py-3" style="font-family:'Segoe UI', sans-serif;">
      <div class="text-center fw-bold mb-2">JUMLAH KASUS SANITASI BERBASIS LINGKUNGAN</div>
      <div class="mb-3" style="font-size:13px;">
        <div>Unit : {{ header.unit }}</div>
        <div>Nama Unit : {{ header.nama_unit }}</div>
        <div>Tanggal : {{ header.awal }} - {{ header.akhir }}</div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
          <thead>
            <tr class="text-white" style="background:#22b8cf;">
              <th rowspan="2">No</th><th rowspan="2">Desa</th>
              <th colspan="2">Diare</th>
              <th colspan="2">Kecacingan</th>
              <th colspan="2">DHF</th>
              <th colspan="2">Filariasis</th>
              <th colspan="2">Malaria</th>
              <th colspan="2">TB Paru</th>
              <th colspan="2">Kusta</th>
              <th colspan="2">Kulit</th>
              <th colspan="2">ISPA</th>
            </tr>
            <tr class="text-white" style="background:#22b8cf;">
              <th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th>
              <th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th>
              <th>L</th><th>P</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!rows || rows.length===0">
              <td class="text-center" colspan="20">Tidak ada data</td>
            </tr>
            <tr v-for="(r,i) in rows" :key="i">
              <td class="text-center">{{ i+1 }}</td>
              <td>{{ r.desa }}</td>
              <td class="text-center">{{ r.DIARE_L }}</td><td class="text-center">{{ r.DIARE_P }}</td>
              <td class="text-center">{{ r.KECACINGAN_L }}</td><td class="text-center">{{ r.KECACINGAN_P }}</td>
              <td class="text-center">{{ r.DHF_L }}</td><td class="text-center">{{ r.DHF_P }}</td>
              <td class="text-center">{{ r.FILARIASIS_L }}</td><td class="text-center">{{ r.FILARIASIS_P }}</td>
              <td class="text-center">{{ r.MALARIA_L }}</td><td class="text-center">{{ r.MALARIA_P }}</td>
              <td class="text-center">{{ r.TB_L }}</td><td class="text-center">{{ r.TB_P }}</td>
              <td class="text-center">{{ r.KUSTA_L }}</td><td class="text-center">{{ r.KUSTA_P }}</td>
              <td class="text-center">{{ r.KULIT_L }}</td><td class="text-center">{{ r.KULIT_P }}</td>
              <td class="text-center">{{ r.ISPA_L }}</td><td class="text-center">{{ r.ISPA_P }}</td>
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

