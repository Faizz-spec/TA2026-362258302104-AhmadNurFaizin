<template>
  <div>
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-success mb-0">Pemeriksaan Fisik Pasien / Objektif</h5>
    </div>
    <div class="p-3">
      <!-- Tombol navigasi antar form -->
      <div class="d-flex gap-3 flex-wrap">
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForminc === 'dataPersalinan' }"
          @click.prevent="toggleForm('dataPersalinan')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Persalinan >></div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForminc === 'pengisianKala' }"
          @click.prevent="toggleForm('pengisianKala')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Pengisian Kala >></div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForminc === 'pelayananPersalinan' }"
          @click.prevent="toggleForm('pelayananPersalinan')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Pelayanan Persalinan >></div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForminc === 'dataApgar' }"
          @click.prevent="toggleForm('dataApgar')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Apgar >></div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForminc === 'dataBayi' }"
          @click.prevent="toggleForm('dataBayi')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Bayi >></div>
        </a>
      </div>

      <hr />

      <!-- Tempat munculnya form yang aktif -->
      <div class="mt-4">
        <component :is="activeComponent" v-if="activeComponent" :diagnosa="props.diagnosa" />
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import DataPersalinan from './Objektif/DataPersalinan.vue';
  import PelayananPersalinan from './Objektif/PelayananPersalinan.vue';
  import Kala from './Objektif/PengisianKala.vue';
  import Apgar from './Objektif/Apgar.vue';
  import Bayi from './Objektif/Bayi.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  // state aktif (diagnosa / skrining)
  const activeForminc = ref(localStorage.getItem('activeForminc') || 'dataPersalinan');

  // Simpan kembali jika user ganti tab
  watch(activeForminc, (val) => {
    localStorage.setItem('activeForminc', val);
  });
  const toggleForm = (form) => {
    activeForminc.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeForminc.value) {
      case 'dataPersalinan':
        return DataPersalinan;
      case 'pengisianKala':
        return Kala;
      case 'pelayananPersalinan':
        return PelayananPersalinan;
      case 'dataApgar':
        return Apgar;
      case 'dataBayi':
        return Bayi;
      default:
        return null;
    }
  });
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
