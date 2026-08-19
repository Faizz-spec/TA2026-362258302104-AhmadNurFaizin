<template>
  <div>
    <div class="col-md-6 bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
      <h5 class="fw-semibold text-success mb-0">Form Data Persalinan dan Status Obstetri</h5>
    </div>
    <div class="col-md-6">
      <div class="card border-0 p-3 shadow-sm">
        <div class="card-body">
            <h6 class="mb-3">Data Riwayat Persalinan yang Lalu</h6>
            <div class="mb-3">
              <label class="form-label fw-semibold">Tanggal Persalinan</label>
              <input type="date" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Penolong Persalinan</label>
              <select class="form-select">
                <option selected>Select...</option>
                <option value="303071001">Keluarga</option>
                <option value="OV000012">Dukun</option>
                <option value="309453006">Bidan</option>
                <option value="309343006">Dokter</option>
                <option value="11935004">Dokter Spesialis</option>
                <option value="249215002">Lokhia Berbau</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Lokasi Persalinan</label>
              <select class="form-select">
                <option selected>Select...</option>
                <option value="264362003">Rumah</option>
                <option value="OT000001">Polindes</option>
                <option value="OT000002">Pustu</option>
                <option value="104">Rumah Sakit Ibu dan Anak</option>
                <option value="102">Puskesmas</option>
                <option value="103">Klinik</option>
                <option value="OT000008">Rumah Bersalin</option>
                <option value="82242000">Rumah Sakit Ibu dan Anak</option>
                <option value="OT000003">RS ODHA</option>
                <option value="OT000004">Bidan Praktek Mandiri</option>
              </select>
            </div>
            <div class="row mb-3">
              <div class="col-4">
                <label class="form-label fw-semibold">Gravida</label>
                <select class="form-select">
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
                </select>
              </div>
              <div class="col-4">
                <label class="form-label fw-semibold">Partus</label>
                <select class="form-select">
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
                </select>
              </div>
              <div class="col-4">
                <label class="form-label fw-semibold">Abortus</label>
                <select class="form-select">
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
                </select>
              </div>
            </div>
            <div class="mb-2 text-end">
              <button type="button" @click="saveForm" class="btn btn-success px-4 shadow-sm mt-2">
                <i class="bi bi-save me-1"></i> Simpan Data
              </button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
