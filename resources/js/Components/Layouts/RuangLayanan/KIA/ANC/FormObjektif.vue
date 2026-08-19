<template>
  <div>
    <!-- Tombol navigasi antar form -->
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeObjANC === 'pemeriksaanIbu' }"
        @click.prevent="toggleForm('pemeriksaanIbu')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pemeriksaan Ibu</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeObjANC === 'pemeriksaanFisikIbu' }"
        @click.prevent="toggleForm('pemeriksaanFisikIbu')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pemeriksaan Fisik Ibu</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeObjANC === 'pemeriksaanUSG' }"
        @click.prevent="toggleForm('pemeriksaanUSG')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pemeriksaan USG</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeObjANC === 'pemeriksaanJanin' }"
        @click.prevent="toggleForm('pemeriksaanJanin')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pemeriksaan Janin</div>
      </a>

      <a
        href="#"
        class="action-card medical-action"
        :class="{ 'active-card': activeObjANC === 'pemeriksaan10T' }"
        @click.prevent="toggleForm('pemeriksaan10T')"
      >
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Pemeriksaan 10T</div>
      </a>
    </div>

    <!-- Tempat munculnya form yang aktif -->
    <div class="mt-4">
      <component :is="activeComponent" v-if="activeComponent" />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import PemeriksaanIbu from './Objektif/PemeriksaanIbu.vue';
  import PemeriksaanFisikIbu from './Objektif/PemeriksaanFisikIbu.vue';
  import PemeriksaanUSG from './Objektif/PemeriksaanUSG.vue';
  import PemeriksaanJanin from './Objektif/PemeriksaanJanin.vue';
  import Pemeriksaan10T from './Objektif/Pemeriksaan10T.vue';

  // const activeForm = ref(null);

  // Ambil tab terakhir dari localStorage
  const activeObjANC = ref(localStorage.getItem('activeObjANC') || 'pemeriksaanIbu');

  // Simpan kembali jika user ganti tab
  watch(activeObjANC, (val) => {
    localStorage.setItem('activeObjANC', val);
  });

  // Fungsi toggle form
  const toggleForm = (form) => {
    activeObjANC.value = form;
  };

  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeObjANC.value) {
      case 'pemeriksaanIbu':
        return PemeriksaanIbu;
      case 'pemeriksaanFisikIbu':
        return PemeriksaanFisikIbu;
      case 'pemeriksaanUSG':
        return PemeriksaanUSG;
      case 'pemeriksaanJanin':
        return PemeriksaanJanin;
      case 'pemeriksaan10T':
        return Pemeriksaan10T;
      default:
        return PemeriksaanIbu;
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
</style>
