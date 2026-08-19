<template>
  <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-danger mb-1">Pelayanan Postnatal Care / Pelayanan Ibu Nifas</h5>
  </div>
  <div class="card border-0 shadow-sm rounded-3">
    <!-- Tabs -->
    <div class="d-flex text-white align-items-center border-bottom bg-bottom p-2 rounded-top-3">
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
        :DataPasien="props.DataPasien"
        :DataDiagnosa="props.DataDiagnosa"
        :KunjunganAnc="props.KunjunganAnc"
        :diagnosa="props.diagnosa"
        :AlergiMakanan="props.AlergiMakanan"
        :AlergiObat="props.AlergiObat"
        :diagnosaKeperawatan="props.diagnosaKeperawatan"
        :riwayat="props.riwayat"
        :tindakan="props.tindakan"
        :is="currentForm"
      />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';

  // import form
  import FormSubjektif from './FormSubjektif.vue';
  import FormObjektif from './FormObjektif.vue';
  import FormAssessment from '../FormAssessment.vue';
  import FormPlanning from '../FormPlanning.vue';
  import FormImunisasi from '../FormImunisasi.vue';
  import FormStatusPasien from '../FormStatusPasien.vue';
  import FormResumePasien from './FormResumePasien.vue';

  const tabs = [
    { name: 'subjektif', label: 'Subjektif' },
    { name: 'objektif', label: 'Objektif' },
    { name: 'assessment', label: 'Assessment' },
    { name: 'imunisasi', label: 'Imunisasi' },
    { name: 'planning', label: 'Planning' },
    { name: 'status_pasien', label: 'Status Pasien' },
  ];

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

  const selectedTab = ref('subjektif');

  // Tentukan form yang aktif
  const currentForm = computed(() => {
    const map = {
      subjektif: FormSubjektif,
      objektif: FormObjektif,
      assessment: FormAssessment,
      imunisasi: FormImunisasi,
      planning: FormPlanning,
      status_pasien: FormStatusPasien,
      kirim_satu_sehat: FormResumePasien,
    };

    return map[selectedTab.value] || FormSubjektif;
  });
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
  .ml-auto {
    margin-left: auto;
  }
</style>
