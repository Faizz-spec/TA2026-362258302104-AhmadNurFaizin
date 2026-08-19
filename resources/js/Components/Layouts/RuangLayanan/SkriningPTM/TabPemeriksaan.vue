<template>
  <div>
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
        <component :is="currentForm" :DataPasien="DataPasien" :formData="formPemeriksaan" :tindakan="props.tindakan" :DataTindakan="props.DataTindakan" :TenagaMedis="props.TenagaMedis" />
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, computed, reactive } from 'vue';
  import { Link } from '@inertiajs/vue3';

  // Import form components
  import FormSubjektif from './FormSubjektif.vue';
  import FormObjektif from './FormObjektif.vue';
  import FormAssessment from './FormPemeriksaan/FormAssessment.vue';
  import FormPlanning from './FormPlanning.vue';
  import FormStatusPasien from './FormPemeriksaan/FormStatusPasien.vue';
  import FormResumePasien from './FormPemeriksaan/FormResumePasien.vue';

  // Props
  const props = defineProps({
    DataPasien: Object,
    tindakan: Array,
    DataTindakan: Array,
    TenagaMedis: Array,
  });

  // Tabs list
  const tabs = [
    { name: 'subjektif', label: 'Subjektif' },
    { name: 'objektif', label: 'Objektif' },
    { name: 'assessment', label: 'Assessment' },
    { name: 'planning', label: 'Planning' },
    { name: 'status_pasien', label: 'Status Pasien' },
  ];

  const selectedTab = ref('subjektif');

  const formPemeriksaan = reactive({
    subjektif: {},
    objektif: {},
    assessment: {},
    planning: {},
    status_pasien: {},
  });

  const currentForm = computed(() => {
    const map = {
      subjektif: FormSubjektif,
      objektif: FormObjektif,
      assessment: FormAssessment,
      planning: FormPlanning,
      status_pasien: FormStatusPasien,
      kirim_satu_sehat: FormResumePasien,
    };

    return map[selectedTab.value] || FormSubjektif;
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
