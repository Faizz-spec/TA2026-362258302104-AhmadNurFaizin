  <template>
    <div>
      <div class="row">
        <form @submit.prevent="submitForm" class="col-6">
          <div>
            <div class="text-decoration-underline fw-bold t mb-4">
              <p class="bg-warning d-inline-block px-2">Diagnosa Medis</p>
            </div>
            <div class="row mb-3">
              <div class="col-3">
                <input type="text" class="form-control" placeholder="" disabled v-model="form.kode_diagnosa">
              </div>
              <div class="col-9">
                <div class="input-group">
                  <input type="text" class="form-control bg-light" disabled v-model="form.nama_diagnosa" />
                  <button type="button" class="btn btn-info" @click="openModal()">Cari</button>

                  <button type="button" class="btn btn-danger ">Del</button>
                </div>
              </div>
            </div>
            <div class="row  mb-3">
              <div class="col-3 d-flex flex-column">
                <label for="" class="fw-bold"> Alergi Makan</label>
                <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.alergi_makanan">
              </div>
              <div class="col-3  d-flex flex-column">
                <label for="" class="fw-bold"> Alergi Obat</label>
                <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.alergi_obat">
              </div>
              <div class="col-6 d-flex flex-column">
                <label for="" class="fw-bold"> Keterangan Alergi</label>
                <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.keterangan_alergi">
              </div>
            </div>
            <div class="row  mb-3">
              <div class="col-3  d-flex flex-column">
                <label for="" class="fw-bold">Kunjungan Khusus</label>
                <select class="form-control" name="" id="" v-model="form.kunjungan_khusus">
                  <option value="" selected>-Pilih-</option>
                  <option :value="item.id" v-for="item in diagnosaKasus">{{ item.kasus }}</option>
                </select>
              </div>
              <div class="col-9  d-flex flex-column">
                <label for="" class="fw-bold">Keterangan</label>
                <input class="form-control" type="text" v-model="form.keterangan_kunjungan">
              </div>

            </div>
            <button type="submit" class="btn btn-success">
              <i class="far fa-envelope me-2"></i>SIMPAN DIAGNOSA MEDIS
            </button>
          </div>
        </form>

        <div class="col-6">

          <div class="row mb-4">
            <div class="col-5">
              <div class="text-decoration-underline fw-bold t mb-4">
                <p class="bg-warning d-inline-block px-2">Pemeriksaan Penunjang</p>
              </div>
              <Link href="/surat-keterangan" class="btn btn-info">
              <i class="fas fa-bars me-2"></i>Laboratorium
              </Link>
            </div>
            <div class="col-7 ">
              <div class="text-decoration-underline fw-bold t mb-4">
                <p class="bg-warning d-inline-block px-2">Form Lanjutan</p>
              </div>
              <div class="d-flex gap-3 flex-wrap">
                <Link href="/surat-keterangan" class="btn btn-secondary">
                <i class="far fa-envelope me-2"></i>Diare
                </Link>
                <Link href="/surat-keterangan" class="btn btn-secondary">
                <i class="far fa-envelope me-2"></i>Katarak
                </Link>
                <Link href="/surat-keterangan" class="btn btn-secondary">
                <i class="far fa-envelope me-2"></i>PTM
                </Link>
                <Link href="/surat-keterangan" class="btn btn-secondary">
                <i class="far fa-envelope me-2"></i>Hipertensi
                </Link>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="text-decoration-underline fw-bold t mb-4">
              <p class="bg-warning d-inline-block px-2">Diagnosa Keperawatan</p>
            </div>

            <select class="form-control" name="" id="">
              <option value="">pilih</option>
            </select>
            <button href="/surat-keterangan" class="btn btn-success mt-4">
              <i class="far fa-envelope me-2"></i>Simpan Diagnosa Keperawatan
            </button>
          </div>

        </div>

        <div class="row mt-4">
          <div class="col-6">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Diagnosa Medis</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in simpusDataDiagnosaMedis" :key="item.idDiagnosa">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.nmDiagnosa }}</td>
                  <td>{{ item.keterangan }}</td>
                  <td>{{ item.master_diagnosa_kasus.kasus }}</td>
                  <td>{{ item.simpus_poli_f_k_t_p.nmPoli }}</td>
                  <td>
                    <button class="btn btn-sm btn-danger" @click="hapusDiagnosa(item.idDiagnosa)">Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-6">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Diagnosa Asuhan Keperawatan</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header flex-column">
            <div class="w-100 mb-4 d-flex justify-content-end">
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="w-100 d-flex align-items-center">
              <h5 class="modal-title mb-0">Pilih Diagnosa</h5>

              <form class="d-flex ms-auto">
                <input type="text" v-model="keyword" id="search-diagnosa" class="form-control form-control-sm me-2"
                  placeholder="Cari diagnosa..." @input="searchDiagnosa">
                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
              </form>
            </div>
          </div>

          <div class="modal-body">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>KODE</th>
                  <th>NAMA</th>
                  <th>KETERANGAN</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in diagnosaMedis.data" :key="index">
                  <td>{{ item.kdDiag }}</td>
                  <td>{{ item.nmDiag }}</td>
                  <td></td>
                  <td>
                    <button class="btn btn-info btn-sm" @click="pilihDiagnosa(item)">Pilih</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="mt-2 d-flex flex-wrap gap-2">
              <button v-for="(link, i) in diagnosaMedis.links" :key="i" v-html="link.label" class="btn btn-sm" :class="{
                'btn-primary': link.active,
                'btn-outline-primary': !link.active,
                'disabled': !link.url
              }" @click="link.url && fetchPage(link.url)" />
            </div>

          </div>
        </div>
      </div>
    </div>
  </template>
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';

const props = defineProps({
  diagnosaKasus: Array,
  dataPasien: Object,
  simpusDataDiagnosaMedis: Array,
  routeDiagnosaMedis: String,
  AlergiPasien: Object
});
const keyword = ref('');
const showModal = ref(false);
const diagnosaMedis = ref({ data: [], links: [] });
const emit = defineEmits(['dataAnamnesa-update']);
console.log('data diagnosa medis :', props.simpusDataDiagnosaMedis);

const form = useForm({
  kode_diagnosa: '',
  nama_diagnosa: '',
  kunjungan_khusus: '',
  keterangan_kunjungan: '',
  pelayananId: 1,
  loketId: props.dataPasien?.idLoket ?? 1,
  kdPoli: props.dataPasien?.kdPoli ?? '',
  alergi_makanan: props.AlergiPasien[0]?.alergi_makanan.namaAlergiBpjs ?? '',
  alergi_obat: props.AlergiPasien[0]?.alergi_obat.namaAlergiBpjs ?? '',
  keterangan_alergi: props.AlergiPasien[0]?.keterangan,
});

function openModal() {
  showModal.value = true;
  fetchPage(route('api.diagnosa-medis'));
}

function pilihDiagnosa(item) {
  form.kode_diagnosa = item.kdDiag;
  form.nama_diagnosa = item.nmDiag;
  showModal.value = false;
}

function submitForm() {
  form.post(route(props.routeDiagnosaMedis), {
    preserveScroll: true,
    onSuccess: () => {
      alert("Diagnosa Medis tersimpan");
      emit('dataAnamnesa-update');
    },
  });
}
function searchDiagnosa() {
  fetchPage(route('api.diagnosa-medis', { search: keyword.value }));
}

function fetchPage(url) {
  if (!url) return;

  const relativeUrl = url.startsWith('http')
    ? new URL(url).pathname + new URL(url).search
    : url;
  axios.get(relativeUrl)
    .then(res => {
      diagnosaMedis.value = res.data;
    })
    .catch(err => console.error(err));
}

function hapusDiagnosa(item) {
  form.delete(route('ruang-layanan-gigi.remove-diagnosa-medis', item), {
    data: {
      _method: 'delete',
    },
    preserveScroll: true,
    onSuccess: () => {
      alert("Diagnosa Medis dihapus");
      emit('dataAnamnesa-update');
    },
  });

}
</script>
