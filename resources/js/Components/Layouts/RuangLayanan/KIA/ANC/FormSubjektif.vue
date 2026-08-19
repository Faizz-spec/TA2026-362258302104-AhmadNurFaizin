<template>
  <div>
    <!-- Tombol navigasi antar form -->
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeForm === 'dataKunjungan' }"
        @click.prevent="toggleForm('dataKunjungan')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Data Kunjungan</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeForm === 'pemantauan' }"
        @click.prevent="toggleForm('pemantauan')"
      >
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">Pemantauan Dan Riwayat</div>
      </a>
    </div>
    <!-- Tempat munculnya form yang aktif -->
    <div class="mt-4">
      <component
        :DataPasien="props.DataPasien"
        :KunjunganAnc="props.KunjunganAnc"
        :diagnosa="props.diagnosa"
        :riwayat="props.riwayat"
        :is="activeComponent"
        v-if="activeComponent"
      />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import FormDataKunjungan from './Subjektif/FormDataKunjungan.vue';
  import FormPemantauan from './Subjektif/FormPemantauan.vue';

  // Ambil tab terakhir dari localStorage
  const activeForm = ref(localStorage.getItem('activeForm') || 'pemeriksaanIbu');

  // Simpan kembali jika user ganti tab
  watch(activeForm, (val) => {
    localStorage.setItem('activeForm', val);
  });
  // Fungsi toggle form
  const toggleForm = (form) => {
    activeForm.value = form;
  };

  const props = defineProps({
    DataPasien: Object,
    KunjunganAnc: Array,
    diagnosa: Array,
    riwayat: Array,
  });

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeForm.value) {
      case 'dataKunjungan':
        return FormDataKunjungan;
      case 'pemantauan':
        return FormPemantauan;
      default:
        return FormDataKunjungan;
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
