<template>
  <div class="objektif-form">
    <div class="objektif-toolbar">
      <div>
        <p class="objektif-kicker">Objektif</p>
        <h3>Pendaftaran Kunjungan Peserta Skrining</h3>
      </div>
      <div class="segmented-control" role="tablist" aria-label="Planning">
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormObjektif === 'metabolik' }"
          @click="toggleForm('metabolik')"
        >
          <i class="bi bi-person-check"></i>
          <span>Metabolik & Kardiovaskular</span>
        </button>
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormObjektif === 'indera' }"
          @click="toggleForm('indera')"
        >
          <i class="bi bi-exclamation-triangle"></i>
          <span>Gangguan Indera</span>
        </button>
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormObjektif === 'genetik' }"
          @click="toggleForm('genetik')"
        >
          <i class="bi bi-exclamation-triangle"></i>
          <span>Kronis & Genetik</span>
        </button>
      </div>
    </div>

    <div v-if="activeFormObjektif === 'metabolik'" class="fade-in">
      <FormMetabolik :DataPasien="props.DataPasien" :formData="props.formData" />
    </div>
    <div v-if="activeFormObjektif === 'indera'" class="fade-in">
      <FormIndera :DataPasien="props.DataPasien" :formData="props.formData" />
    </div>
    <div v-if="activeFormObjektif === 'genetik'" class="fade-in">
      <FormGenetik :DataPasien="props.DataPasien" :formData="props.formData" />
    </div>

    <div class="form-actions">
      <div class="save-status" :class="{ success: saveStatus === 'ready' }">
        {{ saveMessage }}
      </div>
      <button type="button" class="save-button" :disabled="isSaving" @click="saveObjektif">
        <i class="bi" :class="isSaving ? 'bi-arrow-repeat' : 'bi-save'"></i>
        <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Objektif' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import FormMetabolik from './FormPemeriksaan/FormAntropometri.vue';
  import FormIndera from './FormPemeriksaan/FormIndera.vue';
  import FormGenetik from './FormPemeriksaan/FormGenetik.vue';

  const activeFormObjektif = ref('metabolik');
  const isSaving = ref(false);
  const saveStatus = ref('idle');

  const toggleForm = (form) => {
    activeFormObjektif.value = form;
  };

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
  });

  const emit = defineEmits(['save-objektif']);

  const saveMessage = computed(() => {
    if (saveStatus.value === 'ready') {
      return 'Data objektif siap disimpan.';
    }

    return 'Simpan setelah data metabolik, indera, dan genetik selesai diisi.';
  });

  const saveObjektif = () => {
    isSaving.value = true;

    emit('save-objektif', {
      DataPasien: props.DataPasien,
      objektif: props.formData?.objektif || {},
    });

    window.setTimeout(() => {
      isSaving.value = false;
      saveStatus.value = 'ready';
    }, 400);
  };
</script>

<style scoped>
  .objektif-form {
    display: grid;
    gap: 18px;
  }

  .objektif-toolbar,
  .panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .objektif-toolbar {
    padding-bottom: 16px;
    border-bottom: 1px solid #e5edf0;
  }

  .objektif-kicker {
    margin: 0 0 4px;
    color: #64748b;
    font-size: 0.76rem;
    font-weight: 750;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .objektif-toolbar h3 {
    margin: 0;
    color: #0f172a;
    font-size: 1.2rem;
    font-weight: 750;
  }

  .segmented-control {
    display: inline-flex;
    gap: 4px;
    padding: 4px;
    border: 1px solid #cfd9e3;
    border-radius: 8px;
    background: #f8fafc;
  }

  .segment-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 36px;
    padding: 7px 12px;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: #475569;
    font-size: 0.86rem;
    font-weight: 700;
  }

  .segment-button.active {
    background: #0f6b4c;
    color: #ffffff;
    box-shadow: 0 8px 18px rgba(15, 107, 76, 0.18);
  }

  .fade-in {
    animation: fadeIn 0.3s ease;
  }

  .form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    padding-top: 16px;
    border-top: 1px solid #e5edf0;
    flex-wrap: wrap;
  }

  .save-status {
    color: #64748b;
    font-size: 0.86rem;
    font-weight: 600;
  }

  .save-status.success {
    color: #0f6b4c;
  }

  .save-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 40px;
    padding: 9px 16px;
    border: 0;
    border-radius: 8px;
    background: #0f6b4c;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 750;
    box-shadow: 0 8px 18px rgba(15, 107, 76, 0.18);
  }

  .save-button:disabled {
    cursor: not-allowed;
    opacity: 0.72;
  }

  .save-button .bi-arrow-repeat {
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  @media (max-width: 576px) {
    .form-actions {
      align-items: stretch;
      flex-direction: column;
    }

    .save-button {
      width: 100%;
    }
  }
</style>
