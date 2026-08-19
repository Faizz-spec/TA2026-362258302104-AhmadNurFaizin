<template>
  <AppLayouts>
    <FormAnamnesa title="Poli BP Umum" :backRoute :unitList :rows :kluster=kluster :kdPoli="idPoli">

    </FormAnamnesa>
  </AppLayouts>
</template>
<script setup>
import { ref } from 'vue'
import { route } from 'ziggy-js';
import { usePage } from '@inertiajs/vue3';
import FormAnamnesa from '../../../Components/Layouts/RuangLayanan/DataPasien.vue';
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';

const {props} = usePage();
const DataUnit = props.DataUnit;
const DataPasien = props.DataPasien;
const kluster = props.kluster;
const idPoli = props.kdPoli;
const backRoute = 'ruang-layanan.pelayanan';

console.log('data pasien',DataPasien);
const unitList = DataUnit.map(item => {
  const kategori = item.data_master_unit.kategori
  const nama = item.nama_unit
  return {
    id : item.id_detail,
    data : `[ ${kategori} ] ${nama}`
  }
})
const rows = DataPasien;
</script>