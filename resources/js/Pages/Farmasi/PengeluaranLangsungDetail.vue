<template>
  <div class="container py-4">
    <h3 class="mb-4">Pengeluaran Langsung Detail</h3>

    <!-- Tabel -->
    <table v-if="data.length > 0" class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>No</th>
          <th>Unit</th>
          <th>Sub Unit</th>
          <th>Tanggal</th>
          <th>Keperluan</th>
          <th>Jumlah Obat</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, index) in data" :key="row.id_peng_langsung">
          <td>{{ index + 1 }}</td>
          <td>{{ row.unitId ?? '-' }}</td>
          <td>{{ row.subUnitId ?? '-' }}</td>
          <td>{{ row.tanggal }}</td>
          <td>{{ row.keterangan }}</td>
          <td>{{ row.jumlah ?? 0 }}</td>
          <td>
            <button class="btn btn-info btn-sm">Detail</button>
            <button class="btn btn-danger btn-sm">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-else class="alert alert-secondary mt-3">
      Tidak ada data untuk filter ini.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";

defineProps({
  filters: Object,
  data: Array
})
</script>
