<template>
  <div class="d-flex flex-column min-vh-100">
    <Navbar />
    <main class="flex-grow-1 py-4">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
  import Navbar from '@/Components/Layouts/Navbar.vue';
  import Footer from '@/Components/Layouts/Footer.vue';
</script>

<style scoped>
  main {
    background-color: #f8f9fa;
  }
</style>
