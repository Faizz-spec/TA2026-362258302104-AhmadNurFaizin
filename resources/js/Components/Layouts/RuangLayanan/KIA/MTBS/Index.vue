<template>
  <div>
    <!-- Header -->
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-danger mb-1">
        MTBS
      </h5>


    </div>

    <div class="card border-0 shadow-sm rounded-0">
      <!-- Tabs Navigation -->
      <div class="d-flex align-items-center bg-bottom p-2 rounded-top-3 border-bottom flex-wrap">
        <button
          v-for="tab in tabs"
          :key="tab.name"
          class="btn-tab"
          :class="{ active: selectedTab === tab.name }"
          @click="selectedTab = tab.name"
        >
          {{ tab.label }}
        </button>

        <button
          class="btn btn-sehat btn-sm fw-semibold ms-auto"
          @click="goToSatusehat"
        >
          Kirim Satu Sehat
        </button>
      </div>

      <!-- Dynamic Form -->
      <div class="card-body bg-white">
        <component
          v-if="currentForm"
          :is="currentForm"
          :DataPasien="DataPasien"
          :DataDiagnosa="DataDiagnosa"
          :KunjunganAnc="KunjunganAnc"
          :diagnosa="diagnosa"
          :diagnosaKeperawatan="diagnosaKeperawatan"
          :AlergiMakanan="AlergiMakanan"
          :AlergiObat="AlergiObat"
          :riwayat="riwayat"
          :tindakan="tindakan"
        />

        <div v-else class="text-muted text-center py-5">
          Form tidak tersedia
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

/* ===============================
   IMPORT FORM COMPONENTS
================================ */
import FormSubjektif from './FormSubjektif.vue'
import FormObjektif from './FormObjektif.vue'
import FormAssessment from './FormAssessment.vue'
import FormPlanning from '../FormPlanning.vue'
import FormImunisasi from '../FormImunisasi.vue'
import FormStatusPasien from '../FormStatusPasien.vue'
import Gizi from './Gizi.vue'

/* ===============================
   PROPS
================================ */
const props = defineProps({
  DataPasien: { type: Array, default: () => [] },
  diagnosa: { type: Array, default: () => [] },
  diagnosaKeperawatan: { type: Array, default: () => [] },
  tindakan: { type: Array, default: () => [] },
  riwayat: { type: Array, default: () => [] },
  AlergiMakanan: { type: Array, default: () => [] },
  AlergiObat: { type: Array, default: () => [] },
  KunjunganAnc: { type: Array, default: () => [] },
  DataDiagnosa: { type: Array, default: () => [] },
})

/* ===============================
   TAB LIST
================================ */
const tabs = [
  { name: 'subjektif', label: 'Subjektif' },
  { name: 'objektif', label: 'Objektif' },
  { name: 'assessment', label: 'Assessment' },
  { name: 'gizi', label: 'Gizi' },
  { name: 'imunisasi', label: 'Imunisasi' },
  { name: 'planning', label: 'Planning' },
  { name: 'status_pasien', label: 'Status Pasien' },
]

/* ===============================
   TAB STATE
================================ */
const defaultTab = 'subjektif'
const allowedTabs = tabs.map(tab => tab.name)
const savedTab = localStorage.getItem('selectedTab')

const selectedTab = ref(
  allowedTabs.includes(savedTab) ? savedTab : defaultTab
)

watch(selectedTab, (val) => {
  if (allowedTabs.includes(val)) {
    localStorage.setItem('selectedTab', val)
  } else {
    localStorage.setItem('selectedTab', defaultTab)
  }
})

/* ===============================
   FORM MAP
================================ */
const formMap = {
  subjektif: FormSubjektif,
  objektif: FormObjektif,
  assessment: FormAssessment,
  gizi: Gizi,
  imunisasi: FormImunisasi,
  planning: FormPlanning,
  status_pasien: FormStatusPasien,
}

/* ===============================
   CURRENT FORM
================================ */
const currentForm = computed(() => {
  return formMap[selectedTab.value] ?? formMap[defaultTab]
})

/* ===============================
   GO TO SATU SEHAT PREVIEW
================================ */
const goToSatusehat = () => {
  if (!props.DataPasien || props.DataPasien.length === 0) {
    alert('Data pasien kosong')
    return
  }

  const pasien = props.DataPasien[0]

  if (!pasien.idpelayanan || !pasien.kdPoli) {
    console.log('Data pasien:', pasien)
    alert('Data idPelayanan / kdPoli tidak ditemukan')
    return
  }

  router.get(`/mtbs/${pasien.kdPoli}/${pasien.idpelayanan}/satusehat-preview`)
}
</script>

<style scoped>
.btn-tab {
  background: transparent;
  margin: 2px;
  border: none;
  padding: 8px 14px;
  font-weight: 600;
  color: #ffffff;
  border-radius: 6px;
  transition: 0.2s;
}

.btn-tab.active {
  background: #ffffff;
  color: #10b981;
}

.btn-sehat {
  background: #ffffff;
  color: #10b981;
}

.card {
  border-radius: 10px;
}

.bg-bottom {
  background: #10b981;
}
</style>