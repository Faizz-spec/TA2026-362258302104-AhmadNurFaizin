<template>
  <AppLayout title="Table Components">
    <div class="container py-4">
      <div class="row mb-4">
        <div class="col">
          <h1 class="fw-bold">Table Components</h1>
          <p class="text-muted">Data table examples with sorting and pagination</p>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Basic Table</h5>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Role</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="i in 5" :key="i">
                  <th scope="row">{{ i }}</th>
                  <td>User {{ i }}</td>
                  <td>user{{ i }}@example.com</td>
                  <td>
                    <span class="badge bg-primary">User</span>
                  </td>
                  <td>
                    <span class="badge bg-success">Active</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Striped Table</h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">First</th>
                  <th scope="col">Last</th>
                  <th scope="col">Handle</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="i in 5" :key="i">
                  <th scope="row">{{ i }}</th>
                  <td>Mark {{ i }}</td>
                  <td>Otto</td>
                  <td>@mdo</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">Bordered Table</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Product</th>
                  <th scope="col">Price</th>
                  <th scope="col">Stock</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="i in 5" :key="i">
                  <th scope="row">{{ i }}</th>
                  <td>Product {{ i }}</td>
                  <td>${{ i * 10 }}</td>
                  <td>{{ i * 5 }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
</script>
