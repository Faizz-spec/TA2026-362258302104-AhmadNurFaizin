<template>
  <AppLayout title="Button Components">
    <div class="container py-4">
      <div class="row mb-4">
        <div class="col">
          <h1 class="fw-bold">Button Components</h1>
          <p class="text-muted">Various button styles for your application</p>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Solid Buttons</h5>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-primary">Primary</button>
            <button class="btn btn-secondary">Secondary</button>
            <button class="btn btn-success">Success</button>
            <button class="btn btn-danger">Danger</button>
            <button class="btn btn-warning">Warning</button>
            <button class="btn btn-info">Info</button>
            <button class="btn btn-light">Light</button>
            <button class="btn btn-dark">Dark</button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Outline Buttons</h5>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-primary">Primary</button>
            <button class="btn btn-outline-secondary">Secondary</button>
            <button class="btn btn-outline-success">Success</button>
            <button class="btn btn-outline-danger">Danger</button>
            <button class="btn btn-outline-warning">Warning</button>
            <button class="btn btn-outline-info">Info</button>
            <button class="btn btn-outline-light">Light</button>
            <button class="btn btn-outline-dark">Dark</button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Button Sizes</h5>
          <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <button class="btn btn-primary btn-sm">Small</button>
            <button class="btn btn-primary">Default</button>
            <button class="btn btn-primary btn-lg">Large</button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Button States</h5>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-primary">Normal</button>
            <button class="btn btn-primary" disabled>Disabled</button>
            <button class="btn btn-primary">
              <span
                class="spinner-border spinner-border-sm"
                role="status"
                aria-hidden="true"
              ></span>
              Loading...
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import { Link } from '@inertiajs/vue3';
</script>
