<template>
  <AppLayouts>
    <FormAnamnesa title="Kematian Maternal dan Perinatal" :backRoute :unitList :rows>

    </FormAnamnesa>
  </AppLayouts>
</template>
<script setup>
import { ref } from 'vue'
import { route } from 'ziggy-js';
import { usePage } from '@inertiajs/vue3';
import FormAnamnesa from '../../../../Components/Layouts/RuangLayanan/KIA/DataPasien.vue';
import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue';

const {props} = usePage();
const DataUnit = props.DataUnit;
const DataPasien = props.DataPasien;
const backRoute = 'ruang-layanan-kematian.pelayanan';


console.log('data pasien',DataPasien);
const unitList = DataUnit.map(item => {
  const kategori = item.data_master_unit.kategori
  const nama = item.nama_unit
  return `[ ${kategori} ] ${nama}`
})
const rows = DataPasien;
</script>