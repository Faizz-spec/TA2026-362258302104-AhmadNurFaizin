<template>
  <AppLayouts>
    <div class="card m-4  rounded-4 rounded-bottom-0">
      <div class="card-header bg-info d-flex justify-content-between p-3  rounded-4 rounded-bottom-0"
        style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="fs-5 text-white">SANITASI</h1>
        <Link :href="backRoute" class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
        <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
        </Link>
      </div>
      <div class="card-body">
        <PelayananPasien @ubah-melayani="isMelayani = $event" :dataPasien="DataPasien" :dataAnamnesa="DataAnamnesa"
          :id-pelayanan="idPelayanan" :pelayanan=pelayanan>
          <FormPelayananSanitasi :id-pelayanan="idPelayanan" :AlergiPasien="AlergiPasien" :sanitasi="sanitasi"></FormPelayananSanitasi>
        </PelayananPasien>
      </div>
    </div>
  </AppLayouts>
</template>
<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import PelayananPasien from '../../../Components/Layouts/RuangLayanan/PelayananPasien/PelayananPasien.vue';
import FormPelayananSanitasi from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananSanitasi.vue';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const isMelayani = ref(false);
const currentTab = ref('subjective');
const page = usePage();
const DataAnamnesa = computed(() => page.props.DataAnamnesa);
const DataPasien = computed(() => page.props.DataPasien);
const idPelayanan = computed(() => page.props.idPelayanan);
const AlergiPasien = computed(() => page.props.AlergiPasien);
const pelayanan =computed(() => page.props.pelayanan);
const sanitasi =computed(() => page.props.sanitasi);
const refreshDataAnamnesa = () => {
  router.reload({
    only: ['DataAnamnesa', 'SimpusDataDiagnosaMedis', 'SimpusTindakan', 'DataRujuk'],
    preserveState: true,
    preserveScroll: true
  })
}
</script>

<style>
.tab-item {
  border: none;
  background: transparent;
  font-weight: 500;
  border-radius: 0;
  position: relative;
  transition: all 0.3s ease;
  color: #6c757d;
}

.tab-item:hover {
  color: #495057 !important;
}

.tab-item.active {
  color: #0d6efd !important;
  font-weight: 600;
}

.tab-indicator {
  position: absolute;
  bottom: -16px;
  left: 0;
  height: 3px;
  background-color: #0d6efd;
  transition: all 0.3s ease;
  z-index: 1;
  border-radius: 3px 3px 0 0;
}
</style>