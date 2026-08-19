<template>
  <div>
    <!-- Header -->
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-danger mb-1">Pelayanan Antenatal Care / Pelayanan Ibu Hamil</h5>

      <button class="btn btn-danger btn-sm ms-auto">Akhiri ANC</button>
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
        <button
          class="btn btn-sehat btn-sm fw-semibold ml-auto"
          @click="selectedTab = 'kirim_satu_sehat'"
        >
          Kirim Satu Sehat
        </button>
      </div>

      <!-- Dynamic Form -->
      <div class="card-body bg-white">
        <component
          :is="currentForm"
          :DataPasien="DataPasien"
          :DataDiagnosa="DataDiagnosa"
          :KunjunganAnc="KunjunganAnc"
          :diagnosa="diagnosa"
          :AlergiMakanan="AlergiMakanan"
          :AlergiObat="AlergiObat"
          :diagnosaKeperawatan="diagnosaKeperawatan"
          :riwayat="riwayat"
          :tindakan="tindakan"
        />
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, computed, watch } from 'vue';
  import { Link } from '@inertiajs/vue3';

  // Import form components
  import FormObstetri from './FormObstetri.vue';
  import FormSubjektif from './FormSubjektif.vue';
  import FormObjektif from './FormObjektif.vue';
  import FormAssessment from '../FormAssessment.vue';
  import FormPlanning from '../FormPlanning.vue';
  import FormImunisasi from '../FormImunisasi.vue';
  import FormStatusPasien from '../FormStatusPasien.vue';
  import FormResumePasien from './FormResumePasien.vue';

  // Props
  const props = defineProps({
    DataPasien: Array,
    diagnosa: Array,
    tindakan: Array,
    riwayat: Array,
    diagnosaKeperawatan: Array,
    AlergiMakanan: Array,
    AlergiObat: Array,
    KunjunganAnc: Array,
    DataDiagnosa: Array,
  });

  // Tabs list
  const tabs = [
    { name: 'obstetri', label: 'Obstetri' },
    { name: 'subjektif', label: 'Subjektif' },
    { name: 'objektif', label: 'Objektif' },
    { name: 'assessment', label: 'Assessment' },
    { name: 'imunisasi', label: 'Imunisasi' },
    { name: 'planning', label: 'Planning' },
    { name: 'status_pasien', label: 'Status Pasien' },
  ];

  const selectedTab = ref(localStorage.getItem('selectedTab') || 'obstetri');

  // Simpan ke localStorage saat berubah
  watch(selectedTab, (val) => {
    localStorage.setItem('selectedTab', val);
  });

  // Tentukan form yang aktif
  const currentForm = computed(() => {
    const map = {
      obstetri: FormObstetri,
      subjektif: FormSubjektif,
      objektif: FormObjektif,
      assessment: FormAssessment,
      imunisasi: FormImunisasi,
      planning: FormPlanning,
      status_pasien: FormStatusPasien,
      kirim_satu_sehat: FormResumePasien,
    };

    return map[selectedTab.value] || FormObstetri;
  });
</script>
<style scoped>
  /* Tab Buttons */
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

  .ml-auto {
    margin-left: auto;
  }

  .btn-sehat {
    background: #ffffff;
    color: #10b981;
  }

  .btn-tab.active {
    background: #ffffff;
    color: #10b981;
  }

  /* Card */
  .card {
    border-radius: 10px;
  }

  /* Gradient header background */
  .bg-bottom {
    background: #10b981;
  }
</style>
