<template>
  <div>
    <div class="p-1">
      <!-- Tombol navigasi antar form -->
      <div class="d-flex gap-3 flex-wrap">
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeFormRiwayatGizi === 'riwayatGizi' }"
          @click.prevent="toggleForm('riwayatGizi')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Riwayat Gizi</div>
        </a>
        <a
          href="#"
          class="action-card medical-action"
          :class="{ 'active-card': activeFormRiwayatGizi === 'riwayatMakanan' }"
          @click.prevent="toggleForm('riwayatMakanan')"
        >
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Riwayat Makanan</div>
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
  import RiwayatGizi from './RiwayatGizi.vue';
  import RiwayatMakanan from './RiwayatMakanan.vue';

  const props = defineProps({
    diagnosa: Array,
  });

  // Ambil tab terakhir dari localStorage
  const activeFormRiwayatGizi = ref(
    localStorage.getItem('activeFormRiwayatGizi') || 'riwayatGizi'
  );

  // Simpan kembali jika user ganti tab
  watch(activeFormRiwayatGizi, (val) => {
    localStorage.setItem('activeFormRiwayatGizi', val);
  });
  // Fungsi toggle form
  const toggleForm = (form) => {
    activeFormRiwayatGizi.value = form;
  };
  // Menentukan komponen aktif berdasarkan state
  const activeComponent = computed(() => {
    switch (activeFormRiwayatGizi.value) {
      case 'riwayatGizi':
        return RiwayatGizi;
      case 'riwayatMakanan':
        return RiwayatMakanan;
      default:
        return RiwayatGizi;
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
