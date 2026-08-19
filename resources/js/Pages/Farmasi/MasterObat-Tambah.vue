<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Tambah Obat</h4>
      <a href="/farmasi/master-obat" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
      </a>
    </div>

    <div class="card p-3">
      <form @submit.prevent="submitForm">
        <div class="mb-3">
          <label class="form-label">Kode Obat</label>
          <input type="text" v-model="form.KODE_OBAT" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Obat</label>
          <input type="text" v-model="form.NAMA" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Satuan</label>
          <input type="text" v-model="form.SATUAN" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
  KODE_OBAT: "",
  NAMA: "",
  SATUAN: ""
});

function submitForm() {
  form.post("/farmasi/master-obat");
}
</script>
