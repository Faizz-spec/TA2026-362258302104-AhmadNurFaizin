<template>
  <div class="container my-4">
    <!-- Header -->
    <div
      class="card-header d-flex justify-content-between align-items-center p-3 mb-4"
      style="background: linear-gradient(135deg, #4682B4, #5A9BD5);"
    >
      <h5 class="text-white fw-bold m-0">
        <i class="fas fa-stethoscope me-2"></i> Rujukan
      </h5>
    </div>

    <!-- FORM -->
    <form class="mb-5">
      <!-- Status Pulang -->
      <div class="row g-3 align-items-center mb-4">
        <label class="col-sm-2 col-form-label text-end fw-bold">
          Status Pulang
        </label>
        <div class="col-sm-4">
          <select
            name="kdStatusPulang"
            class="form-select bg-primary bg-opacity-10 border-primary"
            onchange="statuspulang(),get_tacc()"
          >
            <option value="3">Berobat Jalan</option>
            <option value="7">Meninggal</option>
            <option value="11">Pulang Paksa</option>
            <option value="5">Rujuk Internal</option>
            <option value="4">Rujuk Lanjut (Rujuk Vertikal Pcare)</option>
            <option value="6">Rujuk Lanjut Rumah Sakit (Bukan BPJS)</option>
            <option value="12">Rujuk Rumah Sakit</option>
          </select>
        </div>
      </div>

      <!-- Hidden fields -->
      <div id="statuspulang">
        <input type="hidden" name="kdProviderRujukLanjut" value="" />
        <input type="hidden" name="kdPoliRujukLanjut" value="" />
        <input type="hidden" name="kdPoliRujukInternal" value="" />
      </div>

      <!-- Tenaga Medis -->
      <div class="row g-3 align-items-center mb-4">
        <label class="col-sm-2 col-form-label text-end fw-bold">
          Tenaga Medis
        </label>
        <div class="col-sm-4">
          <select
            name="tenagaMedis"
            class="form-select bg-primary bg-opacity-10 border-primary"
          >
            <option value="0">- pilih -</option>
            <option value="115822">Practitioner 4</option>
            <option value="303434">Practitioner 1</option>
            <option value="304202">Practitioner 2</option>
            <option value="306919">Practitioner 3</option>
          </select>
        </div>
      </div>

      <!-- Tombol Simpan -->
      <div class="row mb-3">
        <div class="col-sm-4 offset-sm-2">
          <button
            type="button"
            class="btn btn-success shadow-sm px-4"
            onclick="simpan_rujuk_cek()"
          >
            💾 Simpan
          </button>
        </div>
      </div>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle text-center shadow-sm">
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
            <td><span class="badge bg-success bg-gradient">Umum</span></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>user1</td>
            <td>2025-07-29 08:17:36</td>
            <td>-</td>
            <td>
              <button class="btn btn-outline-primary btn-sm shadow-sm">
                Poli awal
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
