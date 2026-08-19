<template>
  <AppLayout title="Card Components">
    <div class="container py-4">
      <div class="row mb-4">
        <div class="col">
          <h1 class="fw-bold">Card Components</h1>
          <p class="text-muted">Card components with different styles</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Basic Card</h5>
              <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the
                card's content.
              </p>
              <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm">
            <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="..." />
            <div class="card-body">
              <h5 class="card-title">Card with Image</h5>
              <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the
                card's content.
              </p>
              <a href="#" class="btn btn-outline-primary">View Details</a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Primary Card</h5>
              <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the
                card's content.
              </p>
              <a href="#" class="btn btn-light">Action</a>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">Card with Header</h5>
            </div>
            <div class="card-body">
              <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the
                card's content.
              </p>
            </div>
            <div class="card-footer bg-light">
              <small class="text-muted">Last updated 3 mins ago</small>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border-primary shadow-sm">
            <div class="card-header bg-primary text-white">
              <h5 class="card-title mb-0">Primary Border Card</h5>
            </div>
            <div class="card-body">
              <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the
                card's content.
              </p>
              <a href="#" class="btn btn-primary">Action</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
</script>
