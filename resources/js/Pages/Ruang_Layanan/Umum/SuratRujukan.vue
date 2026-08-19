<template>
  <AppLayouts>
    <SuratRujukan :idPoli="idPoli" :idPelayanan="idPelayanan" :suratRujuks="suratRujuks"></SuratRujukan>
  </AppLayouts>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue'
import SuratRujukan from '../../../Components/Layouts/RuangLayanan/SuratRujukan/SuratRujukan.vue'

const { props } = usePage()
const idPoli = props.idPoli
const idPelayanan = props.idPelayanan
const suratRujuks =props.suratRujuks
console.log('id', idPelayanan)
</script>
