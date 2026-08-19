<template>
  <div>
    <!-- Navigasi Objektif -->
    <div class="d-flex gap-3 flex-wrap">
      <a
        href="#"
        class="action-card"
        :class="{ 'active-card': activeForm === 'mtbs' }"
        @click.prevent="activeForm = 'mtbs'"
      >
        <div class="action-icon">
          <i class="bi bi-clipboard-pulse"></i>
        </div>
        <div class="action-label">Objektif MTBM</div>
      </a>
    </div>

    <!-- Isi Form -->
    <div class="mt-4">
      <component :is="activeComponent" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FormObjektifMTBS from './Objektif/FormObjektifMTBM.vue';

const activeForm = ref('mtbm');

const activeComponent = FormObjektifMTBS;
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
}

.active-card {
  background: #10b981;
  color: #fff;
}

.action-icon {
  font-size: 1.25rem;
}
</style>
