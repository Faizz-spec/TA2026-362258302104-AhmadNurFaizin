<template>
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <form @submit.prevent="saveForm">
          <h5 class="mb-4 fw-semibold text-success">Data Kunjungan</h5>
          <div class="row g-3">
            <div class="mb-2">
              <label class="form-label">Tanggal Kunjungan</label>
              <input v-model="form.tgl_kunjungan" type="date" class="form-control" />
            </div>

            <div class="mb-2">
              <label class="form-label fw-semibold">Usia Kehamilan</label>
              <input
                v-model="form.usia_hamil"
                type="text"
                class="form-control"
                placeholder="Masukkan usia kehamilan"
              />
            </div>
            <div class="mb-2">
              <label class="form-label fw-semibold">Trimester</label>
              <select v-model="form.trimester" class="form-select">
                <option value="">Pilih Trimester</option>
                <option value="1">Trimester 1</option>
                <option value="2">Trimester 2</option>
                <option value="3">Trimester 3</option>
              </select>
            </div>
            <div class="mb-2 text-start">
              <button type="button" class="btn btn-success w-50 px-4 shadow-sm mt-2">
                <i class="bi bi-save me-1"></i> Simpan Data
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue';
  import { route } from 'ziggy-js';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    DataPasien: Array,
    KunjunganAnc: Array,
  });

  const form = ref({
    tgl_kunjungan: '',
    trimester: '',
    usia_hamil: '',
    loketId: props.DataPasien[0].idLoket,
    pasienId: props.DataPasien[0].ID,
  });

  const saveForm = () => {
    router.post(route('ruang-layanan-anc.kunjunganANC'), form.value, {
      onSuccess: () => {
        alert('Data Kunjungan berhasil disimpan!');
        form.value = {
          tanggal_kunjungan: '',
          usia_kehamilan: '',
          trimester: '',
        };
      },
      onError: (errors) => {
        console.error(errors);
        alert('Gagal menyimpan data.');
      },
    });
  };
</script>

<style scoped>
  .card {
    border-radius: 12px;
  }
  .form-label {
    font-weight: 600;
    color: #333;
  }
</style>
