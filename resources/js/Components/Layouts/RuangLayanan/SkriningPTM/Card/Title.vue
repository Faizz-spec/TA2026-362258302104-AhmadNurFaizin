<template>
  <section class="section-title">
    <i class="bi bi-clipboard2-pulse"></i>
    <h2>{{ title }}</h2>
  </section>
</template>
<script setup>
    const props = defineProps({
        title: String,
    });
</script>
<style scoped>
.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 16px;
    margin-bottom: 12px;
    padding: 14px 16px;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    background: #bbf7d0;
    color: #0f6b4c;
  }

  .section-title i {
    font-size: 1.1rem;
  }

  .section-title h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 750;
  }
</style>
