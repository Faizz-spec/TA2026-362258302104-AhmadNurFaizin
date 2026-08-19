<template>
  <AppLayouts>
    <div class="card m-4  rounded-4 rounded-bottom-0">
      <div class="card-header bg-info d-flex justify-content-between p-3  rounded-4 rounded-bottom-0"
        style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="fs-5 text-white">Kunjungan Online</h1>
<Link :href="route('kunj-online.index', { idPoli: '999' })"
      class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
  <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
</Link>

      </div>
      <div class="card-body">
        <PelayananPasien @ubah-melayani="isMelayani = $event" :dataPasien="DataPasien" :dataAnamnesa="DataAnamnesa"
          :id-pelayanan="idPelayanan" :pelayanan=pelayanan>
          <div class="shadow-sm rounded-5">
            <NavigasiFormPemeriksaan :currentTab="currentTab" @change-currentTab="currentTab = $event">
            </NavigasiFormPemeriksaan>
            <div class="m-4 pb-4 row gx-5">
              <FormPelayananSubjective v-if="currentTab === 'subjective'" :idPasien="DataPasien.ID"
                :idLoket="DataPasien.idLoket" :dataAnamnesa="DataAnamnesa" :masterAlergi="MasterAlergi"
                @dataAnamnesa-update="refreshDataAnamnesa" :routeName="routeNameFormSubjective"
                :AlergiPasien="AlergiPasien">
              </FormPelayananSubjective>
              <FormPelayananObjective v-if="currentTab === 'objective'" :currrentSub=true halaman="KunjunganOnline"
                :dataKesadaran="DataKesadaran" :dataAnamnesa="DataAnamnesa" @dataAnamnesa-update="refreshDataAnamnesa"
                :routeName="routeNameFormObjective" :tenaga-medis-askep="TenagaMedisAskep">
              </FormPelayananObjective>
<FormPelayananAssesment
  v-if="currentTab === 'assesment'"
  :diagnosaKasus="DiagnosaKasus"
  :alergiPasien="AlergiPasien"
  :dataPasien="DataPasien"
  :simpusDataDiagnosaMedis="SimpusDataDiagnosaMedis"
  routeDiagnosaMedis="ruang-layanan.set-diagnosa-medis"
  @dataAnamnesa-update="refreshDataAnamnesa"
  :idPelayanan="idPelayanan"
  :idLoket="DataPasien.idLoket"
  :idPoli="idPoli"
  :MasterDiagnosaKeperawatan="MasterDiagnosaKeperawatan"
  :diagnosaKeperawatan="diagnosaKeperawatan"
/>

              <FormPelayananPlanning v-if="currentTab === 'planning'" :dataTindakan="DataTindakan"
                :dataPasien="DataPasien" :simpusTindakan="SimpusTindakan" :idPelayanan="idPelayanan" :idLoket="DataPasien.idLoket"  :idPoli="idPoli"  :simpusResepObat="simpusResepObat"
                route-resep-obat="ruang-layanan.set-resep-obat"
                routePlanningTindakan="ruang-layanan.simpan-Tindakan" route-detail-resep-obat="ruang-layanan.set-detail-resep">
              </FormPelayananPlanning>
<FormPelayananStatusPasien
  v-if="currentTab === 'status_pasien'"
  @dataRujuk-update="refreshDataAnamnesa"
  :idLoket="DataPasien.idLoket"
  :idPelayanan="idPelayanan"
  :statusPulang="statusPulang"
  :DataRujuk="DataRujuk"
  :TenagaMedisAskep="TenagaMedisAskep"
  :poliRujukInternal="poliRujukInternal"
/>

            </div>
          </div>
        </PelayananPasien>
      </div>
    </div>
  </AppLayouts>
</template>
<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import PelayananPasien from '../../../Components/Layouts/RuangLayanan/PelayananPasien/PelayananPasien.vue';
import FormPelayananSubjective from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananSubjective.vue';
import FormPelayananObjective from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananObjective.vue';
import FormPelayananAssesment from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananAssesment.vue';
import FormPelayananPlanning from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananPlanning.vue';
import FormPelayananStatusPasien from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananStatusPasien.vue';
import NavigasiFormPemeriksaan from '../../../Components/Layouts/RuangLayanan/PelayananPasien/NavigasiFormPemeriksaan.vue';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const isMelayani = ref(false);
const currentTab = ref('subjective');
const page = usePage();
const routeNameFormSubjective = 'kunj-online.setAnamnesaSubjective';
const routeNameFormObjective  = 'kunj-online.setAnamnesaObjective';
const DataAnamnesa = computed(() => page.props.DataAnamnesa);
const DataPasien = computed(() => page.props.DataPasien);
const DataKesadaran = computed(() => page.props.DataKesadaran);
const DiagnosaKasus = computed(() => page.props.DiagnosaKasus);
const MasterAlergi = computed(() => page.props.MasterAlergi);
const DataTindakan = computed(() => page.props.DataTindakan);
const SimpusDataDiagnosaMedis = computed(() => page.props.SimpusDataDiagnosa);
const SimpusTindakan = computed(() => page.props.SimpusTindakan);
const AlergiPasien = computed(() => page.props.AlergiPasien);
const statusPulang = computed(() => page.props.StatusPulang);
const idPelayanan = computed(() => page.props.idPelayanan);
const TenagaMedisAskep = computed(() => page.props.TenagaMedisAskep);
const MasterDiagnosaKeperawatan = computed(() => page.props.MasterDiagnosaKeperawatan);
const DataRujuk = computed(() => page.props.DataRujuk);
const idPoli =  computed(() => page.props.idPoli);
const simpusResepObat = computed(() => page.props.SimpusResepObat);
const diagnosaKeperawatan = computed(() => page.props.diagnosaKeperawatan);
const poliRujukInternal =computed(() => page.props.poliRujukInternal);
const pelayanan =computed(() => page.props.pelayanan);
console.log('idPelayanannanysashjbasdsdsdsdid', page.props.poliRujukInternal);

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