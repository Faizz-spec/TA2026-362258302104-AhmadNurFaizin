<template>
  <footer class="footer-bg text-dark py-4 mt-auto" >
    <div class="container">
      <div class="text-center">
        <small>&copy; {{ new Date().getFullYear() }} Simpuswangi. All rights reserved.</small>
      </div>
    </div>
  </footer>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
  footer a:hover {
    color: white !important;
    transition: color 0.3s ease;
  }
  .footer-bg {
    background-color: rgb(255, 255, 255);
  }
</style>
