<template>
  <div>
    <!-- Navigasi Sub Form -->
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeForm === 'dataKunjungan' }"
        @click.prevent="activeForm = 'dataKunjungan'"
      >
        <div class="action-icon">
          <i class="bi bi-clipboard-heart"></i>
        </div>
        <div class="action-label">Subjektif MTBS</div>
      </a>
    </div>

    <!-- Isi Form -->
    <div class="mt-4">
      <component
        :is="activeComponent"
        :DataPasien="DataPasien"
        v-if="activeComponent"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FormDataKunjungan from './Subjektif/FormDataKunjungan.vue';

const props = defineProps({
  DataPasien: {
    type: Array,
    default: () => [],
  },
});

const activeForm = ref(
  localStorage.getItem('subjektifForm') || 'dataKunjungan'
);

watch(activeForm, (val) => {
  localStorage.setItem('subjektifForm', val);
});

const activeComponent = computed(() => {
  return FormDataKunjungan;
});
</script>
