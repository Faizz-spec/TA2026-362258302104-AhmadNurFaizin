<template>
  <div
    v-if="show"
    class="modal fade show d-block"
    tabindex="-1"
    style="background: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center py-4">
          <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem"></i>
          <h5 class="fw-semibold mt-3">{{ title }}</h5>
          <p class="text-muted">{{ message }}</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button class="btn btn-secondary btn-sm" @click="$emit('close')">Batal</button>
          <button class="btn btn-danger btn-sm" @click="$emit('confirm')">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    show: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Hapus Data?',
    },
    message: {
      type: String,
      default: 'Data yang dihapus tidak dapat dikembalikan.',
    },
  });

  defineEmits(['close', 'confirm']);
</script>
