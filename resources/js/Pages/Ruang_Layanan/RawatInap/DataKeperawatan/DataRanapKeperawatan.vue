<template>
  <AppLayouts>
    <DataPasien
      title="RANAP PERAWATAN"
      backRoute="ruang-layanan.pelayanan"
      :unitList="unitList"
      :rows="rows"
      :kluster="null"
      :kdPoli="kdPoli"
    >
    </DataPasien>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue';
  import DataPasien from '../../../../Components/Layouts/RuangLayanan/DataPasien.vue';

  // Props dari controller
  const props = defineProps({
    DataUnit: Array,
    DataPasien: Array,
    kdPoli: String,
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
