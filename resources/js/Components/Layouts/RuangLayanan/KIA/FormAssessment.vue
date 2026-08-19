<template>
  <div>
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeFormAssesment === 'diagnosa' }"
        @click.prevent="toggleForm('diagnosa')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Diagnosa22</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeFormAssesment === 'skrining' }"
        @click.prevent="toggleForm('skrining')"
      >
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">Skrining</div>
      </a>
    </div>

    <div class="mt-4">
      <KeepAlive>
        <component
          :is="activeComponent"
          :key="activeFormAssesment"
          :DataPasien="props.DataPasien"
          :DataDiagnosa="props.DataDiagnosa"
          :diagnosa="props.diagnosa"
          :diagnosaKeperawatan="props.diagnosaKeperawatan"
          :AlergiMakanan="props.AlergiMakanan"
          :AlergiObat="props.AlergiObat"
        />
      </KeepAlive>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, KeepAlive } from 'vue'
import Diagnosa from './Form/Diagnosa.vue'
import Skrining from './Form/Skrining.vue'

const props = defineProps({
  DataPasien: Array,
  DataDiagnosa: Array,
  diagnosa: Array,
  diagnosaKeperawatan: Array,
  AlergiMakanan: Array,
  AlergiObat: Array,
})

const activeFormAssesment = ref(
  localStorage.getItem('activeFormAssesment') || 'diagnosa'
)

watch(activeFormAssesment, (val) => {
  localStorage.setItem('activeFormAssesment', val)
})

const toggleForm = (form) => {
  activeFormAssesment.value = form
}

const activeComponent = computed(() => {
  if (activeFormAssesment.value === 'skrining') return Skrining
  return Diagnosa
})
</script>

<style scoped>
.action-card {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: 8px;
  background: #f9fafb;
  color: #333;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
}

.action-card:hover {
  background: #e9f2ff;
  color: #10b981;
}

.active-card {
  background: #10b981;
  color: #fff;
}

.action-icon {
  font-size: 1.25rem;
  color: inherit;
}

.action-label {
  font-weight: 500;
}
</style>