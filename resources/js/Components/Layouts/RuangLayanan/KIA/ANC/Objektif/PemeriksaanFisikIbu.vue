<template>
  <h5 class="fw-semibold text-success">Pemeriksaan Fisik Ibu</h5>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-section">
              <div class="mb-3">
                <label class="form-label fw-bold d-block">Konjungtiva</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Tidak Normal">Tidak Normal</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold d-block">Sklera</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Tidak Normal">Tidak Normal</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold d-block">Leher</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Tidak Normal">Tidak Normal</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold d-block">Gigi dan Mulut</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Tidak Normal">Tidak Normal</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold d-block">Tungkai</label>
                <select class="form-select">
                  <option value="Normal">Normal</option>
                  <option value="Tidak Normal">Tidak Normal</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
              </div>
            </div>
          </div>
          <!-- Form Kanan -->
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-bold d-block">THT</label>
              <select class="form-select">
                <option value="Normal">Normal</option>
                <option value="Tidak Normal">Tidak Normal</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold d-block">Dada Jantung</label>
              <select class="form-select">
                <option value="Normal">Normal</option>
                <option value="Tidak Normal">Tidak Normal</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold d-block">Dada Paru-Paru</label>
              <select class="form-select">
                <option value="Normal">Normal</option>
                <option value="Tidak Normal">Tidak Normal</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold d-block">Perut</label>
              <select class="form-select">
                <option value="Normal">Normal</option>
                <option value="Tidak Normal">Tidak Normal</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Deskripsi</label>
              <textarea class="form-control" rows="3" placeholder="Masukkan Deskripsi"></textarea>
            </div>
            <!-- Tombol Simpan -->
            <div class="mt-4 text-start">
              <button type="submit" class="btn btn-success w-50 fw-semibold">Simpan</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
  const saveForm = () => {
    alert('Data berhasil disimpan!');
  };
</script>
