<template>
  <div>
    <div class="p-3">
      <!-- Tombol navigasi antar form -->
      <div class="d-flex gap-3 flex-wrap">
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeSubTKembang === 'dataAnamnesis' }"
          @click.prevent="toggleForm('dataAnamnesis')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Anamnesis</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeSubTKembang === 'dataRiwayatGizi' }"
          @click.prevent="toggleForm('dataRiwayatGizi')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Riwayat Gizi Terkait Konsumsi Makanan</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeSubTKembang === 'dataAsi' }"
          @click.prevent="toggleForm('dataAsi')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Pengiriman Data Asi</div>
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
  import DataAnamnesis from './FormSubjektif/DataAnamnesis.vue';
  import DataRiwayatGizi from './FormSubjektif/DataRiwayat.vue';
  import DataAsi from './FormSubjektif/DataAsi.vue';

  const props = defineProps({
    diagnosa: Array,
  });
  // Ambil tab terakhir dari localStorage
  const activeSubTKembang = ref(localStorage.getItem('activeSubTKembang') || 'dataAnamnesis');

  // Simpan kembali jika user ganti tab
  watch(activeSubTKembang, (val) => {
    localStorage.setItem('activeSubTKembang', val);
  });
  // Fungsi toggle form
  const toggleForm = (form) => {
    activeSubTKembang.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeSubTKembang.value) {
      case 'dataAnamnesis':
        return DataAnamnesis;
      case 'dataRiwayatGizi':
        return DataRiwayatGizi;
      case 'dataAsi':
        return DataAsi;
      default:
        return DataAnamnesis;
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
