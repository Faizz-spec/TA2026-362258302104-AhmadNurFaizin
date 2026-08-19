<template>
  <AppLayout title="Template Components">
    <div class="container py-4">
      <div class="row mb-4">
        <div class="col">
          <h1 class="fw-bold">Halaman ZIGY TES</h1>
          <p class="text-muted">Reusable components for your application</p>
        </div>
      </div>




<div class="card mb-4">
  <div class="card-body">
    <h5 class="mb-3 fw-bold">Filter Data Laporan Gigi</h5>
    <form>
      <div class="row mb-2">
        <div class="col-md-2 fw-bold">Puskesmas</div>
        <div class="col-md-5">
          <select class="form-select">
            <option>WONGSOREJO</option>
          </select>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2 fw-bold">Laporan</div>
        <div class="col-md-5">
          <select class="form-select">
            <option>- Pilih -</option>
          </select>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2 fw-bold">Tgl Awal</div>
        <div class="col-md-5">
          <input type="date" class="form-control" />
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2 fw-bold">Tgl Akhir</div>
        <div class="col-md-5">
          <input type="date" class="form-control" />
        </div>
      </div>
      <div class="row mb-2 align-items-start">
        <div class="col-md-2 fw-bold">Unit</div>
        <div class="col-md-3">
          <select class="form-select mb-2">
            <option>- Pilih -</option>
          </select>
          <select class="form-select mb-2">
            <option>- Pilih -</option>
          </select>
          <select class="form-select">
            <option>- SEMUA -</option>
          </select>
        </div>
        <div class="col-md-2 fw-bold" style="margin-left: 10px;">Sub Unit<br/><br/>Desa</div>
      </div>
      <div class="mt-4">
        <button class="btn btn-primary">
          <i class="bi bi-printer me-1"></i> Tampilkan Data
        </button>
      </div>
    </form>
  </div>
</div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title fw-bold">Buttons</h5>
              <p class="card-text text-muted">Various button styles and variants.</p>
              <Link href="/template/button" class="btn btn-outline-primary">View Buttons</Link>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { Link } from '@inertiajs/vue3'
</script>