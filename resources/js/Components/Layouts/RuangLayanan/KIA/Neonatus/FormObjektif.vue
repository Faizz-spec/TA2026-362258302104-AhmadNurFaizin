<template>
  <div>
    <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-success mb-0">Form Pengiriman Data Bayi / Balita</h5>
    </div>
    <div class="card shadow-sm p-4 border-0 form-card">
      <div class="card-body">
        <!-- Tombol navigasi antar form -->
        <div class="d-flex gap-3 flex-wrap">
          <a
            href="#"
            class="action-card medical-action"
            :class="{ 'active-card': activeFormNeonatus === 'pemeriksaanApgra' }"
            @click.prevent="toggleForm('pemeriksaanApgra')"
          >
            <div class="action-icon"><i class="bi bi-person-check"></i></div>
            <div class="action-label">Pemeriksaan APGRA >></div>
          </a>
          <a
            href="#"
            class="action-card medical-action"
            :class="{ 'active-card': activeFormNeonatus === 'NeonatalEsensial' }"
            @click.prevent="toggleForm('NeonatalEsensial')"
          >
            <div class="action-icon"><i class="bi bi-person-check"></i></div>
            <div class="action-label">Neonatal Esensial</div>
          </a>
          <a
            href="#"
            class="action-card medical-action"
            :class="{ 'active-card': activeFormNeonatus === 'headtoToe' }"
            @click.prevent="toggleForm('headtoToe')"
          >
            <div class="action-icon"><i class="bi bi-person-check"></i></div>
            <div class="action-label">Pemeriksaan Head to Toe</div>
          </a>
          <a
            href="#"
            class="action-card medical-action"
            :class="{ 'active-card': activeFormNeonatus === 'beratBadanASI' }"
            @click.prevent="toggleForm('beratBadanASI')"
          >
            <div class="action-icon"><i class="bi bi-person-check"></i></div>
            <div class="action-label">Pemeriksaan Berat Badan dan Pemberian ASI</div>
          </a>
        </div>

        <hr />

        <!-- Tempat munculnya form yang aktif -->
        <div class="mt-4">
          <component :is="activeComponent" v-if="activeComponent" :diagnosa="props.diagnosa" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import Apgra from './Objektif/Apgra.vue';
  import FormBeratBadan from './Objektif/FormBeratBadan.vue';
  import FormHeadtoToe from './Objektif/FormHeadtoToe.vue';
  import FormNeonatus from './Objektif/Neonatus.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  const activeFormNeonatus = ref(localStorage.getItem('activeFormNeonatus') || 'pemeriksaanApgra');

  // Simpan kembali jika user ganti tab
  watch(activeFormNeonatus, (val) => {
    localStorage.setItem('activeFormNeonatus', val);
  });
  const toggleForm = (form) => {
    activeFormNeonatus.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeFormNeonatus.value) {
      case 'pemeriksaanApgra':
        return Apgra;
      case 'NeonatalEsensial':
        return FormNeonatus;
      case 'headtoToe':
        return FormHeadtoToe;
      case 'beratBadanASI':
        return FormBeratBadan;
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
