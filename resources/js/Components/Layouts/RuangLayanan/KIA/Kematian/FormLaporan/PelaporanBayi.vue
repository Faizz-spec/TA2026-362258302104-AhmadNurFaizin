<template>
  <div>
    <!-- Tombol Aksi -->
    <div class="d-flex gap-3 flex-wrap mb-4">
      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeFormKematianBayi === 'bayi-lahir-hidup' }"
        @click.prevent="toggleForm('bayi-lahir-hidup')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Kematian Bayi Lahir Hidup</div>
      </a>

      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeFormKematianBayi === 'bayi-lahir-mati' }"
        @click.prevent="toggleForm('bayi-lahir-mati')"
      >
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">Kematian Bayi Lahir Mati</div>
      </a>
    </div>

    <!-- Form dinamis -->
    <div v-if="activeFormKematianBayi === 'bayi-lahir-hidup'">
      <FormBayiLahirHidup @close="activeFormKematianBayi = 'bayi-lahir-hidup'" :diagnosa="props.diagnosa" />
    </div>

    <div v-if="activeFormKematianBayi === 'bayi-lahir-mati'">
      <FormBayiLahirMati @close="activeFormKematianBayi = 'bayi-lahir-mati'" />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';

  // Import komponen form dari file lain
  import FormBayiLahirHidup from './FormBayiLahirHidup.vue';
  import FormBayiLahirMati from './FormBayiLairMati.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  const activeFormKematianBayi = ref(
    localStorage.getItem('activeFormKematianBayi') || 'bayi-lahir-hidup'
  );

  // Simpan kembali jika user ganti tab
  watch(activeFormKematianBayi, (val) => {
    localStorage.setItem('activeFormKematianBayi', val);
  });
  const toggleForm = (form) => {
    activeFormKematianBayi.value = form;
  };
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
