<template>
  <AppLayouts>
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <!-- ganti dari 8 jadi 10 -->
          <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
              <!-- Bagian Judul & Form -->
              <div class="row mb-4">
                <!-- Kiri: Judul -->
                <div class="col-md-3">
                  <h6 class="fw-semibold">Data Pasien</h6>
                  <p class="text-muted small">Lengkapi data pasien sesuai identitas resmi.</p>
                  <hr />
                </div>

                <!-- Kanan: Form -->
                <div class="col-md-9">
                  <div class="row g-3">
                    <!-- Baris 1 -->
                    <div class="col-md-6">
                      <label class="form-label">NIK</label>
                      <div class="input-group">
                        <input type="text" class="form-control" />
                        <button class="btn btn-info text-white">CEK..!</button>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">No. BPJS</label>
                      <div class="input-group">
                        <input type="text" class="form-control" />
                        <button class="btn btn-info text-white">CEK..!</button>
                      </div>
                    </div>

                    <!-- Baris 2 -->
                    <div class="col-md-6">
                      <label class="form-label">Kode Provider</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">IHS Pasien</label>
                      <input type="text" class="form-control" />
                    </div>

                    <!-- Baris 3 -->
                    <div class="col-md-6">
                      <label class="form-label">Tempat Lahir</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Tanggal Lahir</label>
                      <input type="date" class="form-control" />
                    </div>

                    <!-- Baris 4 -->
                    <div class="col-md-6">
                      <label class="form-label">Jenis Kelamin</label>
                      <select class="form-select">
                        <option>-- Pilih --</option>
                        <option>Laki-laki</option>
                        <option>Perempuan</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Agama</label>
                      <input type="text" class="form-control" />
                    </div>

                    <!-- Baris 5 -->
                    <div class="col-md-6">
                      <label class="form-label">Jenis Pekerjaan</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Hubungan Keluarga</label>
                      <input type="text" class="form-control" />
                    </div>

                    <!-- Baris 6 -->
                    <div class="col-md-6">
                      <label class="form-label">No. KK</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nama KK</label>
                      <input type="text" class="form-control" />
                    </div>

                    <!-- Alamat -->
                    <div class="col-12">
                      <label class="form-label">Alamat</label>
                      <textarea class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label">Provinsi</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Kabupaten</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Kecamatan</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Desa/Kelurahan</label>
                      <input type="text" class="form-control" />
                    </div>

                    <div class="col-md-2">
                      <label class="form-label">RT</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">RW</label>
                      <input type="text" class="form-control" />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">No. HP</label>
                      <input type="text" class="form-control" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tombol -->
              <div class="d-flex justify-content-end border-top pt-3">
                <button class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayouts>
</template>
<script setup>
  import AppLayouts from '@/Components/Layouts/AppLayouts.vue';
</script>
