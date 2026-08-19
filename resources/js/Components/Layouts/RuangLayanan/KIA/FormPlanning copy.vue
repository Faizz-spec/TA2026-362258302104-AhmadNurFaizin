<template>
  <div>
    <h5 class="fw-bold mb-3">Pemeriksaan Pasien / Objektifww</h5>

    <!-- Tombol navigasi antar form -->
    <div class="d-flex gap-3 flex-wrap">
      <a href="#" class="action-card medical-action" @click.prevent="toggleForm('tindakan')">
        <div class="action-icon"><i class="bi bi-person-check"></i></div>
        <div class="action-label">Tindakan</div>
      </a>

      <a href="#" class="action-card medical-action" @click.prevent="toggleForm('pengobatan')">
        <div class="action-icon"><i class="bi bi-activity"></i></div>
        <div class="action-label">Pengobatan</div>
      </a>
    </div>
    <!-- Tempat munculnya form yang aktif -->
    <div class="mt-4">
      <div v-if="activeForm === 'tindakan'" class="p-2">
        <h5 class="fw-semibold text-danger">Tindakan</h5>
        <div class="card border-0 shadow-sm rounded-4 p-3">
          <!-- Kode Tindakan -->
          <div class="row mb-2">
            <label class="col-sm-2 col-form-label">Kode Tindakan</label>
            <div class="col-sm-6 d-flex">
              <input
                type="text"
                class="form-control"
                disabled
                v-model="form.kode_tindakan"
                placeholder="Masukkan kode tindakan"
              />
              <button class="btn btn-info btn-sm ms-2 text-white" @click="showModal = true">
                Cari
              </button>
              <button class="btn btn-danger btn-sm ms-1" @click="hapusForm">Del</button>
            </div>
          </div>

          <!-- Nama Tindakan -->
          <div class="row mb-2">
            <label class="col-sm-2 col-form-label">Nama Tindakan</label>
            <div class="col-sm-6">
              <textarea
                disabled
                class="form-control"
                rows="1"
                v-model="form.nama_tindakan"
              ></textarea>
            </div>
          </div>

          <!-- Nama Tindakan (Ind) -->
          <div class="row mb-2">
            <label class="col-sm-2 col-form-label">Nama Tindakan (Ind)</label>
            <div class="col-sm-6">
              <textarea
                disabled
                class="form-control"
                rows="1"
                v-model="form.nama_tindakan_ind"
              ></textarea>
            </div>
          </div>

          <!-- Keterangan -->
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Keterangan</label>
            <div class="col-sm-6">
              <textarea class="form-control" rows="1" v-model="form.keterangan"></textarea>
            </div>
          </div>

          <!-- Tombol Simpan -->
          <div class="row mb-4">
            <div class="col-sm-2"></div>
            <div class="col-sm-6">
              <button class="btn btn-primary btn-sm" @click="simpanData">Simpan</button>
            </div>
          </div>

          <hr />

          <!-- Tabel Data Tindakan -->
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Tindakan</th>
                  <th>Peraturan</th>
                  <th>Harga</th>
                  <th>Bayar</th>
                  <th>Poli</th>
                  <th>Keterangan</th>
                  <th>Ket Gigi</th>
                  <th>Created By</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, i) in dataTindakan" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ item.kode }}</td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.peraturan }}</td>
                  <td>{{ item.harga }}</td>
                  <td>{{ item.bayar }}</td>
                  <td>{{ item.poli }}</td>
                  <td>{{ item.keterangan }}</td>
                  <td>{{ item.ketGigi }}</td>
                  <td>{{ item.createdBy }}</td>
                  <td>
                    <button class="btn btn-outline-secondary btn-sm">Edit</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Modal Pilih Tindakan -->
        <TindakanModal
          :show="showModal"
          :tindakan="tindakan"
          @close="showModal = false"
          @select="pilihTindakan"
        />
      </div>
      <div v-if="activeForm === 'pengobatan'" class="p-2">
        <h5 class="fw-semibold text-danger">Pengobatan</h5>
        <div class="card border-0 shadow-sm rounded-4 p-3">
          <!-- Form Input -->
          <div class="row mb-2 align-items-center">
            <label class="fw-semibold col-sm-2 col-form-label">Puyer / Bukan Puyer</label>
            <div class="col-sm-3">
              <select class="form-select" v-model="jenisPuyer">
                <option value="Puyer">PUYER</option>
                <option value="Bukan Puyer">BUKAN PUYER</option>
              </select>
            </div>
          </div>

          <!-- Form ini hanya muncul kalau jenisPuyer = 'Puyer' -->
          <div v-if="jenisPuyer === 'Puyer'">
            <div class="row mb-2 align-items-center">
              <label class="fw-semibold col-sm-2 col-form-label">Jumlah Puyer</label>
              <div class="col-sm-2">
                <input type="number" class="form-control" v-model="jumlahPuyer" />
              </div>
            </div>

            <div class="row mb-2 align-items-center">
              <label class="fw-semibold col-sm-2 col-form-label">Dosis Pakai Puyer</label>
              <div class="col-sm-5 d-flex align-items-center">
                <input type="number" class="form-control w-auto" v-model="dosisPerHari" />
                <span class="fw-semibold mx-2">x Sehari, setiap</span>
                <input type="number" class="form-control w-auto" v-model="intervalJam" />
                <span class="fw-semibold ms-2">Jam Sekali</span>
              </div>
            </div>

            <div class="row mb-2">
              <label class="fw-semibold col-sm-2 col-form-label">Waktu</label>
              <div class="col-sm-6 d-flex align-items-center">
                <div
                  class="fw-semibold form-check me-3"
                  v-for="w in ['pagi', 'siang', 'malam']"
                  :key="w"
                >
                  <input class="form-check-input" type="checkbox" :value="w" v-model="waktu" />
                  <label class="form-check-label text-capitalize">{{ w }}</label>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label class="fw-semibold col-sm-2 col-form-label">Kondisi</label>
              <div class="col-sm-6 d-flex align-items-center">
                <div
                  class="fw-semibold form-check me-3"
                  v-for="k in ['sebelum makan', 'saat makan', 'setelah makan']"
                  :key="k"
                >
                  <input class="form-check-input" type="checkbox" :value="k" v-model="kondisi" />
                  <label class="form-check-label text-capitalize">{{ k }}</label>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-sm-2"></div>
            <div class="col-sm-6">
              <button class="btn btn-success btn-sm" @click="simpanResep">Simpan Resep</button>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive mt-3">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Poli</th>
                  <th>Jenis / Nama Puyer</th>
                  <th>Jumlah Obat / Puyer</th>
                  <th>Dosis Pakai</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataResep" :key="index">
                  <td>{{ item.poli }}</td>
                  <td>{{ item.jenis }}</td>
                  <td>{{ item.jumlah }}</td>
                  <td>{{ item.dosis }}</td>
                  <td>
                    <button class="btn btn-outline-primary me-2 btn-sm" @click="hapusResep(index)">
                      Tambah
                    </button>
                    <button class="btn btn-outline-danger btn-sm" @click="hapusResep(index)">
                      Hapus
                    </button>
                  </td>
                </tr>
                <tr v-if="dataResep.length === 0">
                  <td colspan="5" class="text-center text-muted">Belum ada resep</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import TindakanModal from './TindakanModal.vue';

  // Cek Form Aktif
  const activeForm = ref('tindakan');
  const toggleForm = (form) => {
    activeForm.value = activeForm.value === form ? null : form;
  };

  const props = defineProps({
    tindakan: Array,
  });

  // Tindakan
  const form = ref({
    kode_tindakan: '',
    nama_tindakan: '',
    nama_tindakan_ind: '',
    keterangan: '',
  });

  console.log('Item pertama:', props.tindakan[0]);

  // Modal control
  const showModal = ref(false);

  // Pilih tindakan dari modal
  const pilihTindakan = (item) => {
    form.value.kode_tindakan = item.kdTindakan;
    form.value.nama_tindakan = item.nmTindakan;
    form.value.nama_tindakan_ind = item.nmTindakanInd;
    showModal.value = false;
  };

  // Hapus form
  const hapusForm = () => {
    form.value.kode_tindakan = '';
    form.value.nama_tindakan = '';
    form.value.nama_tindakan_ind = '';
    form.value.keterangan = '';
  };

  // Data contoh tabel utama
  const dataTindakan = ref([
    {
      kode: 'TDK001',
      nama: 'Pemeriksaan Umum',
      peraturan: 'Standar',
      harga: '50.000',
      bayar: 'Tunai',
      poli: 'Umum',
      keterangan: '',
      ketGigi: '-',
      createdBy: 'user1',
    },
  ]);

  console.log(props.tindakan[0]);

  const simpanData = () => {
    alert('Data tindakan berhasil disimpan!');
  };

  // Pengobatan
  const jenisPuyer = ref('Bukan Puyer');
  const jumlahPuyer = ref('');
  const dosisPerHari = ref('');
  const intervalJam = ref('');
  const waktu = ref([]);
  const kondisi = ref([]);
  const dataResep = ref([]);

  // 👇 Watcher untuk reset form ketika bukan puyer
  watch(jenisPuyer, (val) => {
    if (val === 'Bukan Puyer') {
      jumlahPuyer.value = '';
      dosisPerHari.value = '';
      intervalJam.value = '';
      waktu.value = [];
      kondisi.value = [];
    }
  });

  const simpanResep = () => {
    dataResep.value.push({
      poli: 'KIA',
      jenis: jenisPuyer.value,
      jumlah: jenisPuyer.value === 'Puyer' ? jumlahPuyer.value : '-',
      dosis:
        jenisPuyer.value === 'Puyer' ? `${dosisPerHari.value}x / ${intervalJam.value} jam` : '-',
    });
    alert('Resep berhasil disimpan!');
  };

  const hapusResep = (index) => {
    dataResep.value.splice(index, 1);
  };
</script>

<style scoped>
  .action-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 8px;
    background: #f9fafb;
    color: #333;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
  }

  .action-card:hover {
    background: #e9f2ff;
    color: #0d6efd;
  }

  .action-icon {
    font-size: 1.25rem;
    color: inherit;
  }

  .action-label {
    font-weight: 500;
  }
</style>
