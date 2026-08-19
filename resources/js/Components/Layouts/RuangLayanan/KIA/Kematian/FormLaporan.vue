<template>
  <div>
    <!-- Tombol navigasi antar form -->
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeFormKematian === 'pelaporanIbu' }"
        @click.prevent="toggleForm('pelaporanIbu')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pelaporan Kematian Ibu</div>
      </a>
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeFormKematian === 'pelaporanBayi' }"
        @click.prevent="toggleForm('pelaporanBayi')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pelaporan Kematian Bayi</div>
      </a>
    </div>

    <hr />

    <!-- Tempat munculnya form yang aktif -->
    <div class="mt-4">
      <component :is="activeComponent" v-if="activeComponent" :diagnosa="props.diagnosa" />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import PelaporanIbu from './FormLaporan/PelaporanIbu.vue';
  import PelaporanBayi from './FormLaporan/PelaporanBayi.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  // state aktif (diagnosa / skrining)
  const activeFormKematian = ref(localStorage.getItem('activeFormKematian') || 'pelaporanIbu');

  // Simpan kembali jika user ganti tab
  watch(activeFormKematian, (val) => {
    localStorage.setItem('activeFormKematian', val);
  });
  const toggleForm = (form) => {
    activeFormKematian.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeFormKematian.value) {
      case 'pelaporanIbu':
        return PelaporanIbu;
      case 'pelaporanBayi':
        return PelaporanBayi;
      default:
        return PelaporanIbu;
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
