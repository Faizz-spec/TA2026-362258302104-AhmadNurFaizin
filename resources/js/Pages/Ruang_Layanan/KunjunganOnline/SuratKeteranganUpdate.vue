<template>
    <AppLayouts>
        <SuratKeteranganUpdate :suket="suket" :data-anamnesa="dataAnamnesa" :id-poli="idPoli"
            :id-pelayanan="idPelayanan" :jenis-surat="jenisSurat" :tenaga-medis-askep="tenagaMedisAskep">

        </SuratKeteranganUpdate>
    </AppLayouts>

</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import SuratKeteranganUpdate from '../../../Components/Layouts/RuangLayanan/Suket/SuratKeteranganUpdate.vue';
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
const { props } = usePage();
const suket = props.suket
const dataAnamnesa = props.dataAnamnesa
const idPoli = props.idPoli
const idPelayanan = props.idPelayanan
const jenisSurat = props.jenisSurat
const tenagaMedisAskep = props.tenagaMedisAskep
console.log('idpelayanan', idPelayanan)
</script>