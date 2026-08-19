<template>
  <AppLayouts>
    <DataPasien
      title="RANAP PENERIMAAN"
      :backRoute="'/ruang_layanan/simpus/poli/' + kdPoli"
      :unitList="unitList"
      :rows="rows"
      :kluster="null"
      :kdPoli="kdPoli"
      :initialFilters="{ tglKunjungan: tgl, unit: unit }"
    >
    </DataPasien>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue';
  import DataPasien from '../../../../Components/Layouts/RuangLayanan/DataPasien.vue';
  import { route } from 'ziggy-js';

  // Props dari controller
  const props = defineProps({
    DataUnit: Array,
    DataPasien: Array,
    kdPoli: String,
    tgl: String,
    unit: String,
  });

  const unitList = props.DataUnit.map((item) => {
    const kategori = item.data_master_unit.kategori;
    const nama = item.nama_unit;
    return {
      id: item.id_detail,
      data: `[ ${kategori} ] ${nama}`,
    };
  });
  const rows = props.DataPasien;
</script>
