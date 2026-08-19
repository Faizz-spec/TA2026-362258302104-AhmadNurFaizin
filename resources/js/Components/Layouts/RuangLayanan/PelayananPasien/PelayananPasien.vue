  <template>
    <div class="p-2 rounded-4 mt-2">
      <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
        <!-- Header Section -->
        <div class="d-flex align-items-center mb-4">
          <div class="bg-primary bg-opacity-10 rounded-circle px-2 me-3">
            <i class="bi bi-person-fill text-primary fs-1"></i>
          </div>
          <div>
            <h1 class="mb-1 fw-bold h5">{{ dataPasien.NAMA_LGKP }}</h1>
          </div>
        </div>

        <div class="row g-3">
          <!-- Column 1 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i :class="dataPasien.jenis_klmin === 1 ? 'bi bi-gender-male text-primary' : 'bi bi-gender-female text-primary'"
                  class="me-2"></i>
                <h6 class="mb-0 text-muted">Jk / Umur</h6>
              </div>
              <p class="mb-0 fw-semibold"> {{ dataPasien.jenis_klmin === 1 ? 'L' : "P" }} {{ dataPasien.umur }} Tahun -
                {{
                  dataPasien.umur_bulan }} Bulan - {{ dataPasien.umur_hari }} Hari</p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                <h6 class="mb-0 text-muted">Alamat</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.alamat }}
                {{ 'RT ' + dataPasien.no_rt }}
                {{ 'RW ' + dataPasien.no_rw }}
                {{ 'Desa ' + dataPasien.nama_kel }}
                {{ 'Kec ' + dataPasien.nama_kec }}
                {{ dataPasien.nama_kab }}
                {{dataPasien.nama_prop }}</p>
            </div>
          </div>

          <!-- Column 2 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-heart-pulse-fill text-success me-2"></i>
                <h6 class="mb-0 text-muted">Jenis/Poli</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.kunjSakit = true ? 'Kunjungan Sakit' : 'Kunjungan Sehat' }} ({{
                dataPasien.nmPoli }} <template v-if="dataPasien.poliAwal"> / {{ dataPasien.poliAwal.nmPoli
                  }}</template>)
              </p>
            </div>
          </div>

          <!-- Column 3 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-check-fill text-success me-2"></i>
                <h6 class="mb-0 text-muted">Tanggal Kunjungan</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.tglKunjungan }}</p>
            </div>
          </div>

          <!-- Column 4 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-credit-card-fill text-info me-2"></i>
                <h6 class="mb-0 text-muted">No. RM / NIK</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.NO_MR }} / {{ dataPasien.NIK }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-credit-card-fill text-info me-2"></i>
                <h6 class="mb-0 text-muted">No. BPJS/Provider</h6>
              </div>
              <p class="mb-0 fw-semibold">/</p>
            </div>
          </div>
        </div>
      </div>

      <div class="quick-actions mb-2">
        <div class="action-grid">
          <Link :href="route('ruang-layanan.surat-keterangan-list', {
            idPoli: props.dataPasien.kdPoli,
            idPelayanan: props.idPelayanan
          })" class="action-card doc-action">
          <div class="action-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="action-label">Surat Keterangan</div>
          </Link>

          <Link :href="route('ruang-layanan.surat-rujuk', {
            idPoli: props.dataPasien.kdPoli,
            idPelayanan: props.idPelayanan
          })" class="action-card doc-action">
          <div class="action-icon">
            <i class="bi bi-send"></i>
          </div>
          <div class="action-label">Surat Rujukan</div>
          </Link>

          <Link :href="route('ruang-layanan.riwayat-pasien', {
            idPoli: props.dataPasien.kdPoli,
            idPasien: props.dataPasien.ID
          })" class="action-card history-action">
          <div class="action-icon">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="action-label">Riwayat Pasien</div>
          </Link>

          <Link :href="route('ruang-layanan.cppt', {
            idPoli: props.dataPasien.kdPoli,
            idPasien: props.dataPasien.ID
          })" class="action-card medical-action">
          <div class="action-icon">
            <i class="bi bi-file-text"></i>
          </div>
          <div class="action-label">CPPT</div>
          </Link>

          <button @click="openModalUkk" class="action-card medical-action" data-bs-toggle="modal"
            data-bs-target="#exampleModal">
            <div class="action-icon">
              <i class="bi bi-clipboard2-heart"></i>
            </div>
            <div class="action-label">UKK</div>
          </button>

          <button v-if="!isMelayani && props.dataPasien.kdPoli !== '097'" @click.prevent="mulaiPemeriksaanPasien"
            class="action-card start-action pulse-animation">
            <div class="action-icon">
              <i class="bi bi-person-check"></i>
            </div>
            <div class="action-label">Mulai Pemeriksaan</div>
          </button>
        </div>
      </div>
    </div>
    <div v-if="isMelayani" class="mt-4 p-2">
      <slot></slot>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitUkk" action="">
              <div class="d-flex gap-5">
                <div>
                  <div class="mb-3">
                    <label for="ukk-pekerjaan" class="form-label">Pekerjaan</label>
                    <input type="text" class="form-control" id="ukk-pekerjaan" aria-describedby="emailHelp"
                      v-model="formUkk.pekerjaan">
                  </div>
                  <div class="mb-3">
                    <label for="ukk-tipe-kerja" class="form-label">Tipe Kerja</label>
                    <select class="form-select" id="ukk-tipe-kerja" v-model="formUkk.tipe_kerja">
                      <option value="" selected>--Pilih--</option>
                      <option value="true">Formal</option>
                      <option value="false">Informal</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="ukk-tempat-kerja" class="form-label">Tempat Kerja</label>
                    <input type="text" class="form-control" id="ukk-tempat-kerja" aria-describedby="emailHelp"
                      v-model="formUkk.tempat_kerja">
                  </div>
                </div>
                <div>
                  <div class="mb-3">
                    <label for="ukk-lama-kerja" class="form-label">Lama Kerja</label>
                    <input type="text" class="form-control" id="ukk-lama-kerja" aria-describedby="emailHelp"
                      v-model="formUkk.lama_kerja">
                  </div>
                  <div class="mb-3">
                    <label for="ukk-jenis-ukk" class="form-label">Jenis UKK</label>
                    <select class="form-select" id="ukk-jenis-ukk" v-model="formUkk.jenis_ukk">
                      <option value="" selected>--Pilih--</option>
                      <option v-for="value in jenisUkk" :key="value.id_jenis" :value="value.id_jenis">
                        {{ value.jenis_ukk }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-3 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </template>

<script setup>
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Link } from '@inertiajs/vue3'
import axios from 'axios';
import * as bootstrap from 'bootstrap'
import Swal from 'sweetalert2';

const props = defineProps({
  isMelayani: Boolean,
  dataPasien: Object,
  dataAnamnesa: Array,
  idPelayanan: String,
  pelayanan: Object,
  klsuter: String
});

console.log('data pasien', props.pelayanan);

const isMelayani = ref(props.pelayanan.sudahDilayani !== 0 || props.dataPasien.kdPoli === '097')
const emit = defineEmits(['ubah-melayani', 'ubah-dataAnamnesa',]);
const jenisUkk = ref(null);
const ukk = ref(null);

watch(isMelayani, (val) => {
  emit('ubah-melayani', val)
})

watch(() => props.isMelayani, (val) => {
  isMelayani.value = val
})

function openModalUkk() {
  fetchUkk(route('ruang-layanan.get-ukk', { idLoket: props.dataPasien.idLoket }))
}

function fetchUkk(url) {
  if (!url) return;

  // const relativeUrl = url.startsWith('http')
  //   ? new URL(url).pathname + new URL(url).search
  //   : url;
  axios.get(url)
    .then(res => {
      jenisUkk.value = res.data.jenisUkk;
      ukk.value = res.data.ukk
      formUkk.pekerjaan = props.dataPasien?.DESCRIP ?? ukk.value?.pekerjaan ?? '';
      formUkk.tipe_kerja = ukk.value?.tipeKerja ?? '';
      formUkk.tempat_kerja = ukk.value?.tempatKerja ?? '';
      formUkk.lama_kerja = ukk.value?.lamaKerja ?? '';
      formUkk.jenis_ukk = ukk.value?.jenisUKK ?? '';
      console.log('jenis ukk', res.data)
      console.log('fetching URL:', url);
    })
    .catch(err => console.error(err));
}

function mulaiPemeriksaanPasien() {
  const now = new Date();
  const formattedDateTime = now.toISOString().slice(0, 16);
  router.post(route('ruang-layanan-umum.mulai-pemeriksaan-pasien'), {
    idLoket: props.dataPasien.idLoket,
    tglKunjungan: formattedDateTime,
    idPelayanan: props.idPelayanan
  }, {
    preserveScroll: true,
    onSuccess: (page) => {
      isMelayani.value = true;
      emit('ubah-melayani', false);
      Swal?.fire({
        title: 'Sukses',
        text: 'Mulai melayani pasien',
        icon: 'success',
        timer: 1600,
        showConfirmButton: false
      });
    }
  });
}

const formUkk = useForm({
  pekerjaan: '',
  tipe_kerja: '',
  tempat_kerja: '',
  lama_kerja: '',
  jenis_ukk: ''
})

function submitUkk() {
  formUkk.post(route('ruang-layanan.simpan-ukk', {
    idLoket: props.dataPasien.idLoket
  }), {
    onSuccess: () => {
      const modalElement = document.getElementById('exampleModal')
      const modalInstance = bootstrap.Modal.getInstance(modalElement)
      modalInstance.hide()
      alert('sukses menyimpan data');
    }
  });
}
</script>

<style>
.section-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}

.action-card {
  background: white;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(0, 0, 0, 0.05);
  cursor: pointer;
  text-decoration: none;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.action-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.action-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--dark-color);
}

.doc-action .action-icon {
  background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
}

.history-action .action-icon {
  background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
}

.medical-action .action-icon {
  background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
}

.start-action .action-icon {
  background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);
}

.start-action {
  border: 2px dashed rgba(248, 150, 30, 0.5);
  background: rgba(248, 150, 30, 0.1);
}

.pulse-animation {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(248, 150, 30, 0.4);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(248, 150, 30, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(248, 150, 30, 0);
  }
}
</style>
