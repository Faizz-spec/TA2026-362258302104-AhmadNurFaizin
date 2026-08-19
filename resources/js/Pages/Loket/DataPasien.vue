  <template>
    <div class="card m-4  shadow-sm border-0  rounded-4 rounded-bottom-0">
      <!-- Header -->
      <div
        class="card-header p-4 text-black d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0"
        style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="m-0 fs-4 text-white">{{ title }}</h1>
        <Link :href="backRoute" class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
        <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
        </Link>
      </div>

      <!-- Filter Form -->
      <div class="card mt-4 mx-4 shadow-sm">
        <div class="card-header r bg-transparent border-0 py-3"
          style="background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
          <h5 class="m-0 fw-semibold text-slate-700 d-flex align-items-center">
            <span class="rounded-5 bg-primary p-2 me-3">
              <i class="fas fa-sliders text-white"></i>
            </span>
            Filter Data
          </h5>
        </div>

        <div class="card-body ">
          <form class="row gx-3 gy-2 align-items-end">
            <!-- Tanggal Kunjungan -->
            <div class="col-md-5">
              <label class="form-label fw-semibold">Tanggal Kunjungan</label>
              <input type="date" class="form-control" v-model="tanggal_kunjungan">
            </div>

            <!-- Kategori Unit -->
            <div class="col-md-5">
              <label class="form-label fw-semibold">Kategori Unit</label>
              <select class="form-select" v-model="selectedUnit">
                <option value="">Pilih Unit</option>
                <option v-for="(unit, index) in unitList" :key="index" :value="unit">{{ unit }}</option>
              </select>
            </div>

            <!-- Tombol Tampilkan -->
            <div class="col-md-2 d-flex align-items-end ">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Tampilkan Data
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- Data Table -->
      <div class="card mt-4 m-4 shadow-sm">
        <div class="card-header my-2 border-0 bg-white d-flex justify-content-between align-items-center ">
          <div>
            <label class="me-2">Show</label>
            <select class="form-select d-inline w-auto" style="width: 80px">
              <option>10</option>
              <option>25</option>
            </select>
            <span class="ms-2">entries</span>
          </div>
          <form>
            <div class="input-group">
              <input type="search" class="form-control" placeholder="Search..." />
              <i class="bi bi-search input-group-text bg-white"></i>

            </div>
          </form>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 text-center">
              <thead class="table align-middle">
                <tr>
                  <th class="text-center">NO</th>
                  <th>TANGGAL NO. URUT BPJS</th>
                  <th class="text-center">NO. MR</th>
                  <th>NAMA NIK</th>
                  <th>ALAMAT KECAMATAN-DESA</th>
                  <th>NO. BPJS NO. KUNJUNGAN</th>
                  <th>POLI</th>
                  <th class="text-center">AKSI</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr v-for="(item, index) in rows" :key="index" class="text-center">
                  <td class="text-center">{{ index + 1 }}</td>
                  <td>{{ item.tglKunjungan }}</td>
                  <td class="text-center">{{ item.NO_MR }}</td>
                  <td>{{ item.NAMA_LGKP }} <br></br>{{ item.NIK }}</td>
                  <td class="" style="max-width: 200px">
                    {{ item.alamat }}
                    {{ 'RT ' + item.no_rt }}
                    {{ 'RW ' + item.no_rw }}
                    {{ item.nama_kel }}
                    {{ item.nama_kec }}
                    {{ item.nama_kab }}
                    {{ item.nama_prop }}</td>
                  <td></td>
                  <td>

                    <span class="badge bg-primary bg-opacity-75">
                      {{ item.nmPoli }}
                    </span>
                  </td>

                  <td class="text-center">
                    <Link :href="route(backRoute, item.idLoket)" class="btn px-3 d-flex gap-3 align-items-center">
                    <span class="btn btn-sm btn-danger">
                      <i class="fas fa-times-circle me-1"></i> Belum Dilayani
                    </span>
                    <span class="btn btn-sm btn-warning">
                      <i class="fas fa-spinner fa-spin me-1"></i> Proses Dilayani
                    </span>
                    <span class="btn btn-sm btn-success">
                      <i class="fas fa-check-circle me-1"></i> Selesai Dilayani
                    </span>
                    </Link>
                  </td>
                </tr>
                <tr v-if="rows.length === 0">
                  <td colspan="7" class="text-center py-4 text-muted">Tidak ada data ditemukan</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { route } from 'ziggy-js';

const props = defineProps({
  title: String,
  backRoute: String,
  unitList: Array,
  rows: Array
});


const emit = defineEmits(['filter'])

const tanggal_kunjungan = ref('')
const selectedUnit = ref('')

onMounted(() => {
  const today = new Date().toISOString().split('T')[0]
  tanggal_kunjungan.value = today
})

</script>
<style></style>
