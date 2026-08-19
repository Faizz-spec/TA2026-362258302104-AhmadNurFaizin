<script setup>
  import { usePage } from '@inertiajs/vue3';

  const page = usePage();
  const welcomeText = page.props.welcomeText;
</script>

<template>
  <div>
    <h1>{{ welcomeText }}</h1>
    <p>This is Home.vue rendered by Inertia + Vue.</p>
  </div>
</template>
