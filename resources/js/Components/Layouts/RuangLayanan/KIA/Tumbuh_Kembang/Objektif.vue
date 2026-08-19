<template>
  <div>
    <div class="p-3">
      <!-- Tombol navigasi antar form -->
      <div class="d-flex gap-3 flex-wrap">
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeObjTKembang === 'dataAntropometri' }"
          @click.prevent="toggleForm('dataAntropometri')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Pengiriman Data Antropometri</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeObjTKembang === 'dataStimulasi' }"
          @click.prevent="toggleForm('dataStimulasi')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">
            Pengiriman Data SDIDTK (Stimulasi, Deteksi, dan Intervensi Dini Tumbuh Kembang)
          </div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeObjTKembang === 'dataStatus' }"
          @click.prevent="toggleForm('dataStatus')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">
            Pengiriman Data Status Pertumbuhan dan Perkembangan, serta Data Lainnya
          </div>
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
  import DataAntropometri from './FormObjektif/DataAntropometri.vue';
  import DataStatus from './FormObjektif/DataStatus.vue';
  import DataStimulasi from './FormObjektif/DataStimulasi.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  // Ambil tab terakhir dari localStorage
  const activeObjTKembang = ref(localStorage.getItem('activeObjTKembang') || 'dataAntropometri');

  // Simpan kembali jika user ganti tab
  watch(activeObjTKembang, (val) => {
    localStorage.setItem('activeObjTKembang', val);
  });
  // Fungsi toggle form
  const toggleForm = (form) => {
    activeObjTKembang.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeObjTKembang.value) {
      case 'dataAntropometri':
        return DataAntropometri;
      case 'dataStatus':
        return DataStatus;
      case 'dataStimulasi':
        return DataStimulasi;
      default:
        return DataAntropometri;
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
    background: #fbfbfc;
    color: #141414;
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
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>
