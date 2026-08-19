<template>
  <div class="container my-2">
    <div class="text-decoration-underline fw-bold t mb-4">
      <p class="bg-warning d-inline-block px-2 rounded">Rujukan</p>
    </div>
    <!-- FORM -->
    <form>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label fw-bold">Status Pulang</label>
        <div class="col-sm-4">
          <select class="form-select" v-model="form.status_pulang">
            <option v-for="item in statusPulang" :key="item.kdStatusPulang" :value="item.kdStatusPulang">
              {{ item.nmStatusPulang }}
            </option>
          </select>
        </div>
      </div>

      <div class="row mb-3" v-if="statusPulangLabel === 'Rujuk Internal'">
        <label class="col-sm-2 col-form-label fw-bold">Poli Rujuk Internal</label>
        <div class="col-sm-4">
          <select class="form-select">
            <option selected>Berobat Jalan</option>
            <option>Rujuk</option>
            <option>Pulang Paksa</option>
          </select>
        </div>
      </div>

      <div class="row mb-3"
        v-if="statusPulangLabel === 'Rujuk Lanjut (Rujuk Vertikal Pcare)' || statusPulangLabel === 'Rujuk Lanjut Rumah Sakit (Bukan BPJS)'">
        <label class="col-sm-2 col-form-label fw-bold">PPK Rujukan</label>
        <div class="col-sm-4">
          <input type="text" class="form-control">
        </div>
      </div>
      <div class="row mb-3" v-if="statusPulangLabel === 'Rujuk Lanjut Rumah Sakit (Bukan BPJS)'">
        <label class="col-sm-2 col-form-label fw-bold">Nama Poli</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" disabled>
        </div>
      </div>
      <div class="row mb-3" v-if="statusPulangLabel === 'Rujuk Lanjut Rumah Sakit (Bukan BPJS)'">
        <label class="col-sm-2 col-form-label fw-bold">Nama Dokter</label>
        <div class="col-sm-4">
          <select class="form-select">
            <option selected>- pilih -</option>
            <option>dr. Agus</option>
            <option>dr. Sari</option>
          </select>
        </div>
      </div>

      <div class="row mb-3" v-if="statusPulangLabel === 'Rujuk Lanjut (Rujuk Vertikal Pcare)'">
        <label class="col-sm-2 col-form-label fw-bold">Spesialis/Subspesialis</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" disabled>
        </div>
      </div>

      <div class="row mb-3"
        v-if="statusPulangLabel === 'Rujuk Lanjut (Rujuk Vertikal Pcare)' || statusPulangLabel === 'Rujuk Lanjut Rumah Sakit (Bukan BPJS)'">
        <label class="col-sm-2 col-form-label fw-bold">Catatan</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" disabled>
        </div>
      </div>

      <div class="row mb-3"
        v-if="statusPulangLabel === 'Rujuk Lanjut (Rujuk Vertikal Pcare)' || statusPulangLabel === 'Rujuk Lanjut Rumah Sakit (Bukan BPJS)'">
        <label class="col-sm-2 col-form-label fw-bold">Tgl Rencana Berkunjung</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" disabled>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-2 col-form-label fw-bold">Tenaga Medis</label>
        <div class="col-sm-4">
          <select class="form-select">
            <option selected>- pilih -</option>
            <option>dr. Agus</option>
            <option>dr. Sari</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>

    <!-- TABLE -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-sm align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>No</th>
            <th>Asal Poli</th>
            <th>Keterangan</th>
            <th>Poli Tujuan</th>
            <th>Tenaga Medis</th>
            <th>Created By</th>
            <th>Mulai melayani</th>
            <th>Selesai melayani</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><span class="badge bg-primary bg-opacity-75">Umum</span></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>user1</td>
            <td>2025-07-29 08:17:36</td>
            <td>-</td>
            <td><button class="btn btn-outline-secondary btn-sm">Poli awal</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({
  statusPulang: Array
})
const form = useForm({
  status_pulang: ''
});

const statusPulangLabel = computed(() => {
  const found = props.statusPulang.find(
    (item) => item.kdStatusPulang === form.status_pulang
  )
  return found ? found.nmStatusPulang : ''
})
</script>