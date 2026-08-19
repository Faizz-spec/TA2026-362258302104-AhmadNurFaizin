<template>
    <FormCetakSuratRujukan :suratRujuk="suratRujuk" :data-pasien="dataPasien" :data-anamnesa="dataAnamnesa" :unit="unit">

    </FormCetakSuratRujukan>

</template>
<script setup>
import { usePage } from '@inertiajs/vue3';
import FormCetakSuratRujukan from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormCetakSuratRujukan.vue';
const page = usePage();
const suratRujuk =  page.props.suratRujuk
const dataPasien = page.props.dataPasien
const dataAnamnesa = page.props.dataAnamnesa
const unit = page.props.unit
</script>