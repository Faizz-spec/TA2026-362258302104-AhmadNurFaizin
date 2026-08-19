<template>
  <div class="card shadow-sm border-0 rounded-3">
    <!-- Header -->
    <div
      class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top-3"
    >
      <h6 class="mb-0 fw-bold">
        <i class="fas fa-file-alt me-2"></i>Daftar Surat Keterangan
      </h6>
      <Link
        href="/promkes/diagnosa/list"
        class="btn btn-light btn-sm fw-semibold shadow-sm"
      >
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Diagnosa
      </Link>
    </div>

    <!-- Body -->
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-secondary">Data Surat</h6>
        <button class="btn btn-success btn-sm shadow-sm" @click="showForm = true">
          <i class="fas fa-plus-circle me-2"></i>Tambah Data
        </button>
      </div>

      <!-- Modal Form -->
      <FormSuratKeterangan
        :show="showForm"
        @close="showForm = false"
        @save="handleSave"
      />

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover shadow-sm">
          <thead class="table-success text-center">
            <tr>
              <th style="width: 50px">No</th>
              <th style="width: 120px">Tanggal</th>
              <th>Jenis Surat</th>
              <th>No Surat</th>
              <th>Keperluan</th>
              <th style="width: 100px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dataSurat.length === 0">
              <td colspan="6" class="text-center text-muted py-3">
                <i class="fas fa-inbox me-2"></i>Belum ada data surat
              </td>
            </tr>

            <tr v-for="(item, index) in dataSurat" :key="index">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ item.tanggal }}</td>
              <td>{{ formatJenisSurat(item.jenis_surat) }}</td>
              <td>{{ item.no_surat }}</td>
              <td>{{ item.keperluan }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import AppLayouts from "@/Components/Layouts/AppLayouts.vue";
import FormSuratKeterangan from "@/Components/Layouts/MalSehat/PelayananPasien/FormSuratKeterangan.vue";

defineOptions({ layout: AppLayouts });

const showForm = ref(false);
const dataSurat = ref([]); // sementara kosong

function handleSave(data) {
  console.log("Data tersimpan:", data);
  dataSurat.value.push({
    tanggal: new Date().toLocaleDateString("id-ID"),
    jenis_surat: data.jenis_surat,
    no_surat: data.no_surat,
    keperluan: data.keperluan,
  });
  showForm.value = false;
}

function formatJenisSurat(jenis) {
  const mapping = {
    surat_keterangan_sehat: "Surat Keterangan Sehat",
    surat_keterangan_dokter: "Surat Keterangan Dokter",
    surat_keterangan_catin: "Surat Keterangan Catin",
    surat_keterangan_calon_haji: "Surat Keterangan Calon Haji",
    surat_keterangan_kematian: "Surat Keterangan Kematian",
    surat_keterangan_rapid_test: "Surat Keterangan Rapid Test",
  };
  return mapping[jenis] || "-";
}
</script>

<style scoped>
.table thead th {
  vertical-align: middle;
  font-size: 0.9rem;
}
.table tbody td {
  font-size: 0.9rem;
}
.table-hover tbody tr:hover {
  background-color: #f8fdf8;
}
</style>
