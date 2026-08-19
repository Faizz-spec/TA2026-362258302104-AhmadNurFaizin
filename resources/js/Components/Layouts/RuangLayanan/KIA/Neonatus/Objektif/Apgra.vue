<template>
  <div class="row g-4">
    <div class="col-md-4">
      <ApgarForm :menit="1" @update="updateApgar" />
    </div>
    <div class="col-md-4">
      <ApgarForm :menit="5" @update="updateApgar" />
    </div>
    <div class="col-md-4">
      <ApgarForm :menit="10" @update="updateApgar" />
    </div>
  </div>
  <button class="btn btn-success w-100 mt-4" type="button" @click="saveForm">
    <i class="bi bi-save me-1"></i> Simpan Data
  </button>
  <div class="mt-4 card p-3 shadow-sm">
    <h6>Rekap Nilai Apgar</h6>
    <table class="table table-bordered mt-2">
      <thead>
        <tr>
          <th>Menit</th>
          <th>Total</th>
          <th>Klasifikasi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(data, menit) in apgarResults" :key="menit">
          <td>{{ menit }}</td>
          <td>{{ data.total }}</td>
          <td>{{ data.klasifikasi }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
  import { reactive } from 'vue';

  import ApgarForm from './Form/FormApgra.vue';
  // Tempat simpan hasil tiap form
  const apgarResults = reactive({});

  // Update setiap kali form berubah
  const updateApgar = (data) => {
    apgarResults[data.menit] = {
      total: data.total,
      klasifikasi: data.klasifikasi,
    };
  };

  // Simpan data (contohnya alert dulu)
  const saveForm = () => {
    console.log('Data tersimpan:', apgarResults);
    alert('Data Apgar berhasil disimpan!');
  };
</script>
