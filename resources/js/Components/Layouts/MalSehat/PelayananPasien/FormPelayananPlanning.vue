<template>
  <div class="container">
    <div class="card shadow-sm rounded-4 border-0">
      <!-- Header Tab -->
      <div class="card-header d-flex gap-4 p-3 border-0 rounded-top-4"
        style="background: linear-gradient(135deg, #4682B4, #5A9BD5);">
        <a href="" class="text-decoration-none" :class="{
          'text-white fw-bold': currrentSubTabPlanning === 'tindakan',
          'text-light': currrentSubTabPlanning !== 'tindakan'
        }" @click.prevent="currrentSubTabPlanning = 'tindakan'">
          Tindakan >
        </a>

        <a href="" class="text-decoration-none" :class="{
          'text-white fw-bold': currrentSubTabPlanning === 'pengobatan',
          'text-light': currrentSubTabPlanning !== 'pengobatan'
        }" @click.prevent="currrentSubTabPlanning = 'pengobatan'">
          Pengobatan >
        </a>
      </div>

      <!-- Tab: Tindakan -->
      <div class="card-body" v-if="currrentSubTabPlanning === 'tindakan'">
        <form>
          <!-- Input Group -->
          <div class="mb-3 row align-items-center">
            <label class="col-sm-2 col-form-label text-end fw-bold">Kode Tindakan</label>
            <div class="col-sm-4">
              <div class="input-group">
                <input type="text" class="form-control bg-light" :value="selected.kode" disabled />
                <button type="button" class="btn btn-primary" @click="showModal = true">Cari</button>
                <button type="button" class="btn btn-outline-danger" @click="hapusKode">Del</button>
              </div>
            </div>
          </div>

          <!-- Nama Tindakan -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label text-end fw-bold">Nama Tindakan</label>
            <div class="col-sm-4">
              <input type="text" class="form-control bg-light" :value="selected.nama" disabled />
            </div>
          </div>

          <!-- Nama Tindakan (Ind) -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label text-end fw-bold">Nama Tindakan (Ind)</label>
            <div class="col-sm-4">
              <input type="text" class="form-control bg-light" :value="selected.ind" disabled />
            </div>
          </div>

          <!-- Keterangan -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label text-end fw-bold">Keterangan</label>
            <div class="col-sm-4">
              <textarea class="form-control bg-primary bg-opacity-25 shadow-sm rounded-3" rows="2"></textarea>
            </div>
          </div>

          <!-- Tombol Simpan -->
          <div class="row">
            <div class="col-sm-4">
              <button type="submit" class="btn btn-success">Simpan</button>
            </div>
          </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Pilih Tindakan</h5>
                <button type="button" class="btn-close" @click="showModal = false"></button>
              </div>
              <div class="modal-body">
                <table class="table table-bordered table-striped">
                  <thead class="table-light">
                    <tr>
                      <th>KODE</th>
                      <th>NAMA TINDAKAN</th>
                      <th>IND (TRANSLATE)</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in tindakan" :key="item.kode">
                      <td>{{ item.kode }}</td>
                      <td>{{ item.nama }}</td>
                      <td>{{ item.ind }}</td>
                      <td>
                        <button class="btn btn-sm btn-info" @click="pilihKode(item)">Pilih</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-backdrop fade show" v-if="showModal"></div>
      </div>

      <!-- Tab: Pengobatan -->
      <div class="card-body" v-if="currrentSubTabPlanning === 'pengobatan'">
        <form>
          <div class="my-4">
            <!-- Pilihan Puyer -->
            <div class="row mb-3 align-items-center">
              <div class="col-sm-2 text-end fw-bold">Puyer/Bukan puyer</div>
              <div class="col-sm-4">
                <select class="form-select" id="ketPuyer" onchange="getPuyer()">
                  <option value="0" selected>BUKAN PUYER</option>
                  <option value="1">PUYER</option>
                </select>
                <input type="hidden" name="nama_puyer" id="nama_puyer" value="puyer" />
              </div>
            </div>

            <!-- Jumlah Puyer -->
            <div class="row mb-3 align-items-center" id="jumlahpuyerdiv">
              <div class="col-sm-2 text-end fw-bold">Jumlah Puyer</div>
              <div class="col-sm-4">
                <input type="text" name="jumlah_puyer" id="jumlah_puyer" class="form-control" style="width:100px"
                  onkeypress="return hanyaAngka(event)" />
              </div>
            </div>

            <!-- Dosis Puyer -->
            <div class="row mb-3 align-items-center" id="dosispakaipuyerdiv">
              <div class="col-sm-2 text-end fw-bold">Dosis pakai puyer</div>
              <div class="col-sm-4 d-flex align-items-center">
                <input type="text" name="dosis_pakai_puyer" id="dosis_pakai_puyer" class="form-control me-2"
                  style="width:80px" onkeypress="return hanyaAngka(event)" /> x Sehari,
                setiap
                <input type="text" name="tiapJam" id="tiapJam" class="form-control ms-2" style="width:80px"
                  onkeypress="return hanyaAngka(event)" /> Jam sekali
              </div>
            </div>

            <!-- Tambahan: Waktu -->
            <div class="row mb-3 align-items-center" id="tambahanPuyerDiv">
              <div class="col-sm-2 text-end fw-bold">Waktu</div>
              <div class="col-sm-6">
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="waktu[]" value="pagi" id="pagi">
                  <label class="form-check-label" for="pagi">Pagi</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="waktu[]" value="siang" id="siang">
                  <label class="form-check-label" for="siang">Siang</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="waktu[]" value="malam" id="malam">
                  <label class="form-check-label" for="malam">Malam</label>
                </div>
              </div>
            </div>

            <!-- Tambahan: Kondisi -->
            <div class="row mb-3 align-items-center">
              <div class="col-sm-2 text-end fw-bold">Kondisi</div>
              <div class="col-sm-6">
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="kondisi[]" value="sebelum makan" id="sebelum">
                  <label class="form-check-label" for="sebelum">Sebelum makan</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="kondisi[]" value="saat makan" id="saat">
                  <label class="form-check-label" for="saat">Saat makan</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="checkbox" class="form-check-input" name="kondisi[]" value="setelah makan" id="setelah">
                  <label class="form-check-label" for="setelah">Setelah makan</label>
                </div>
              </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="row">
              <div class="col-sm-12 text-end">
                <button type="button" class="btn btn-success" onclick="simpanResep()">
                  <i class="far fa-save me-2"></i>Simpan Resep
                </button>
              </div>
            </div>
          </div>
        </form>

        <hr />

        <!-- Table Pengobatan -->
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-sm text-center">
            <thead class="table-primary">
              <tr>
                <th>Poli</th>
                <th>Jenis/Nama Puyer</th>
                <th>Jumlah Obat/Puyer</th>
                <th>Dosis Pakai</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data rows here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const currrentSubTabPlanning = ref("tindakan");
const showModal = ref(false);

// state data tindakan
const tindakan = ref([]);

// nilai yang dipilih
const selected = ref({
  kode: "",
  nama: "",
  ind: ""
});

// fetch data dari API
const loadTindakan = async () => {
  try {
    const res = await axios.get("/mal-sehat/promkes/tindakan/list");
    tindakan.value = res.data.map(item => ({
      kode: item.kode,
      nama: item.nama_tindakan,
      ind: item.nama_tindakan_indonesia,
      harga: item.harga,
      kategori: item.kategori,
      deskripsi: item.deskripsi
    }));
  } catch (error) {
    console.error("Gagal load data tindakan:", error);
  }
};

// pilih kode dari modal
const pilihKode = (item) => {
  selected.value = { ...item };
  showModal.value = false;
};

// hapus pilihan
const hapusKode = () => {
  selected.value = { kode: "", nama: "", ind: "" };
};

// load saat komponen mount
onMounted(() => {
  loadTindakan();
});
</script>
