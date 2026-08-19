<template>
  <div class="card shadow-sm border-0 p-3">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Plasenta</label>
                <select class="form-select">
                  <option value="Lengkap">Lengkap</option>
                  <option value="Tidak Lengkap">Tidak Lengkap</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Tindakan Penanganan</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Pendarahan</label>
                <select class="form-select">
                  <option value="Ada">Ada</option>
                  <option value="Tidak Ada">Tidak Ada</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Tindakan Yang Dilakukan</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-semibold">Masalah Lain</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Penatalaksanaan Masalah Lain</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Hasilnya</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi ..."></textarea>
              </div>
              <div class="mb-2 text-end">
                <button type="submit" class="btn btn-success px-4 shadow-sm mt-2 me-2">
                  <i class="bi bi-save me-1"></i> Simpan
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
  import { route } from 'ziggy-js';
  import { router } from '@inertiajs/vue3';
  import Apgar from '../Apgar.vue';
  import Bayi from '../Bayi.vue';


  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
