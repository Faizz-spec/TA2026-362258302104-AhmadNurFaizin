<template>
  <div>
    <div class="p-3">
      <!-- Tombol navigasi antar form -->
      <div class="d-flex gap-3 flex-wrap">
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForm === 'dataFisik' }"
          @click.prevent="toggleForm('dataFisik')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Data Fisik</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForm === 'grafikPBU' }"
          @click.prevent="toggleForm('grafikPBU')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">PB / U</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForm === 'grafikBBU' }"
          @click.prevent="toggleForm('grafikBBU')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">BB / U</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForm === 'grafikTBU' }"
          @click.prevent="toggleForm('grafikTBU')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">TB / U</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeForm === 'grafikLKU' }"
          @click.prevent="toggleForm('grafikLKU')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">LK / U</div>
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
  import DataFisik from './Grafik/DataFisik.vue';
  import GrafikPBU from './Grafik/GrafikPBU.vue';
  import GrafikBBU from './Grafik/GrafikBBU.vue';
  import GrafikTBU from './Grafik/GrafikTBU.vue';
  import GrafikLKU from './Grafik/GrafikLKU.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  // Ambil tab terakhir dari localStorage
  const activeForm = ref(localStorage.getItem('activeForm') || 'dataFisik');

  // Simpan kembali jika user ganti tab
  watch(activeForm, (val) => {
    localStorage.setItem('activeForm', val);
  });
  // Fungsi toggle form
  const toggleForm = (form) => {
    activeForm.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeForm.value) {
      case 'dataFisik':
        return DataFisik;
      case 'grafikPBU':
        return GrafikPBU;
      case 'grafikBBU':
        return GrafikBBU;
      case 'grafikTBU':
        return GrafikTBU;
      case 'grafikLKU':
        return GrafikLKU;
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
