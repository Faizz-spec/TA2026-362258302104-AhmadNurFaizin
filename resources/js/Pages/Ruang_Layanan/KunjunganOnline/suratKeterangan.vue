<template>
   <AppLayouts>
      <suketList create-route='ruang-layanan.create-surat-keterangan' :id-poli="idPoli"
         :id-pelayanan="idPelayanan" :suket-list="dataSuketList" @suket-list-update="refreshData">

      </suketList>
   </AppLayouts>
</template>
<script setup>
import { router, usePage } from '@inertiajs/vue3';
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import { computed } from 'vue';
import suketList from '../../../Components/Layouts/RuangLayanan/Suket/suketList.vue';
const page = usePage();

const idPoli = page.props.idPoli;
const idPelayanan = page.props.idPelayanan
const dataSuketList = computed(()=>page.props.suketList);

function refreshData() {
   router.reload({
      only: ['suketList'],
      preserveState: true,
      preserveScroll: true
   })
}
</script>