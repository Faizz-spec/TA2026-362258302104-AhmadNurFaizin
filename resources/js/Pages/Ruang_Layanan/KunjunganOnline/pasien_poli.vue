<template>
  <AppLayouts>
    <FormAnamnesa
      title="Poli Kunjungan Online"
      :backRoute="backRoute"
      :unitList="unitList"
      :rows="rows"
    />
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
const backRoute = route('kunj-online.index', { idPoli: '999' })


console.log('data pasien',DataPasien);
const unitList = DataUnit.map(item => {
  const kategori = item.data_master_unit.kategori
  const nama = item.nama_unit
  return `[${kategori}] ${nama}`   // ← pakai string biasa atau backtick, bukan ${} di string biasa
})

const rows = DataPasien;
</script>