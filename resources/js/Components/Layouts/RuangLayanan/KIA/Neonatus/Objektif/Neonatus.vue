<template>
  <div>
    <!-- Tombol Aksi -->
    <div class="d-flex gap-3 flex-wrap mb-4">
      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeNeonatus === 'kn-0' }"
        @click.prevent="toggleForm('kn-0')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">0 - 6 Jam</div>
      </a>

      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeNeonatus === 'kn-1' }"
        @click.prevent="toggleForm('kn-1')"
      >
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">6 - 48 Jam (KN1)</div>
      </a>
      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeNeonatus === 'kn-2' }"
        @click.prevent="toggleForm('kn-2')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">3 - 7 Hari (KN2)</div>
      </a>

      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeNeonatus === 'kn-3' }"
        @click.prevent="toggleForm('kn-3')"
      >
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">8 - 28 Hari (KN3)</div>
      </a>
    </div>

    <!-- Form dinamis -->
    <div v-if="activeNeonatus === 'kn-0'">
      <Form_KN />
    </div>

    <div v-if="activeNeonatus === 'kn-1'">
      <Form_KN1 />
    </div>
    <div v-if="activeNeonatus === 'kn-2'">
      <Form_KN2 />
    </div>
    <div v-if="activeNeonatus === 'kn-3'">
      <Form_KN3 />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';

  // Import komponen form dari file lain
  import Form_KN from './Form/Form_KN.vue';
  import Form_KN1 from './Form/Form_KN1.vue';
  import Form_KN2 from './Form/Form_KN2.vue';
  import Form_KN3 from './Form/Form_KN3.vue';

  const activeNeonatus = ref(localStorage.getItem('activeNeonatus') || 'kn-0');

  // Simpan kembali jika user ganti tab
  watch(activeNeonatus, (val) => {
    localStorage.setItem('activeNeonatus', val);
  });
  const toggleForm = (form) => {
    activeNeonatus.value = form;
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
