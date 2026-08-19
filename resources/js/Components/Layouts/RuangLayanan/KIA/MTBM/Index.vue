<template>
  <div>
    <!-- Header -->
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-danger mb-1">MTBM</h5>

      <button class="btn btn-danger btn-sm ms-auto"></button>
    </div>

    <div class="card border-0 shadow-sm rounded-0">
      <!-- Tabs Navigation -->
      <div class="d-flex align-items-center bg-bottom p-2 rounded-top-3 border-bottom">
        <button
          v-for="tab in tabs"
          :key="tab.name"
          class="btn-tab"
          :class="{ active: selectedTab === tab.name }"
          @click="selectedTab = tab.name"
        >
          {{ tab.label }}
        </button>

        <!--
          FIX: sebelumnya tombol ini cuma ganti selectedTab = 'kirim_satu_sehat'
          dan nampilin FormKirimSatuSehat.vue SEBAGAI TAB DI HALAMAN INI.
          Masalahnya: <component :is="currentForm"> di bawah cuma nge-bind
          DataPasien/DataDiagnosa/dkk - idPoli & idPelayanan TIDAK PERNAH
          di-passing ke komponen itu, jadi selalu undefined di sana.

          MTBSatusehatController.php dan MTBM_Satusehat_Preview.vue yang sudah
          dibuat itu didesain sebagai HALAMAN INERTIA PENUH (menerima idPoli/
          idPelayanan/preview langsung dari server lewat Inertia::render()),
          bukan tab. Jadi sekarang tombol ini NAVIGASI ke halaman itu lewat
          router.get() - persis pola yang sudah terbukti jalan di MTBS.
        -->
        <button
          class="btn btn-sehat btn-sm fw-semibold ml-auto"
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

        <!-- fallback -->
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
   (TANPA OBSTETRI)
================================ */
// ❗ Pastikan path sesuai struktur folder kamu
import FormSubjektif from './FormSubjektif.vue'
import FormObjektif from './FormObjektif.vue'
import FormAssessment from './FormAssessment.vue'
import FormPlanning from './FormPlanning.vue' // <-- kalau punyamu ada di ../, ganti ke "../FormPlanning.vue"
import FormImunisasi from './FormImunisasi.vue'
import FormStatusPasien from './FormStatusPasien.vue'
// FormKirimSatuSehat TIDAK dipakai lagi di sini - "Kirim Satu Sehat" sekarang
// navigasi ke halaman Satusehat/Preview.vue lewat router.get(), bukan tab lokal.
// Filenya gak dihapus, cuma gak di-import/dipakai lagi dari halaman ini.

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
   TAB LIST (TANPA OBSTETRI, TANPA KIRIM SATU SEHAT)
================================ */
const tabs = [
  { name: 'subjektif', label: 'Subjektif' },
  { name: 'objektif', label: 'Objektif' },
  { name: 'assessment', label: 'Assessment' },
  { name: 'imunisasi', label: 'Imunisasi' },
  { name: 'planning', label: 'Planning' },
  { name: 'status_pasien', label: 'Status Pasien' },
]

/* ===============================
   TAB STATE
   - guard supaya kalau localStorage masih nyimpen tab lama yang sudah
     tidak ada ('obstetri' atau 'kirim_satu_sehat') -> auto pindah ke subjektif
================================ */
const selectedTab = ref(localStorage.getItem('selectedTab') || 'subjektif')

const isValidTab = (name) => tabs.some((t) => t.name === name)
if (!isValidTab(selectedTab.value)) {
  selectedTab.value = 'subjektif'
  localStorage.setItem('selectedTab', 'subjektif')
}

watch(selectedTab, (val) => {
  localStorage.setItem('selectedTab', val)
})

/* ===============================
   FORM MAP (TANPA OBSTETRI, TANPA KIRIM SATU SEHAT)
================================ */
const formMap = {
  subjektif: FormSubjektif,
  objektif: FormObjektif,
  assessment: FormAssessment,
  imunisasi: FormImunisasi,
  planning: FormPlanning,
  status_pasien: FormStatusPasien,
}

/* ===============================
   CURRENT FORM
================================ */
const currentForm = computed(() => formMap[selectedTab.value] ?? null)

/* ===============================
   GO TO SATU SEHAT PREVIEW
   Pola disamakan persis dengan MTBS (yang sudah terbukti jalan): ambil
   idpelayanan & kdPoli dari DataPasien[0], lalu router.get() ke halaman
   Satusehat/Preview.vue (bukan switch tab lokal seperti sebelumnya).
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

  router.get(`/mtbm/${pasien.kdPoli}/${pasien.idpelayanan}/satusehat-preview`)
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

.ml-auto {
  margin-left: auto;
}

.card {
  border-radius: 10px;
}

.bg-bottom {
  background: #10b981;
}
</style>
