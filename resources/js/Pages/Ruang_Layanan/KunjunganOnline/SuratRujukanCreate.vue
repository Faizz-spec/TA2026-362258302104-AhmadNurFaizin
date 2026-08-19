<template>
  <AppLayouts>
   <SuratRujukanForm :idPoli="idPoli" :idPelayanan="idPelayanan"  :data-anamnesa="dataAnamnesa"
            :tenaga-medis-askep="tenagaMedisAskep" :poli-fktl="poliFktl" :provider="provider" :surat="surat"></SuratRujukanForm>
  </AppLayouts>
</template>

<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue'
import SuratRujukanForm from '../../../Components/Layouts/RuangLayanan/SuratRujukan/SuratRujukanForm.vue'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()

const idPoli = props.idPoli
const idPelayanan = props.idPelayanan
const dataAnamnesa = props.dataAnamnesa
const tenagaMedisAskep = props.tenagaMedisAskep
const poliFktl = props.poliFktl
const provider = props.provider
const surat = props.surat
</script>
