<template>
  <div class="container-fluid py-4">
    <div class="row g-5">
      <div class="col-12 col-lg-6">
        <form @submit.prevent="submitForm" class="mb-4">
          <!-- Judul -->
          <div class="fw-bold mb-4">
                <h6 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Diagnosa Medis</h6>
              </div>


          <!-- Diagnosa -->
          <div class="row mb-3">
            <div class="col-12 col-md-3">
              <input type="text" class="form-control" disabled v-model="form.kode_diagnosa" />
            </div>
            <div class="col-12 col-md-9">
              <div class="input-group">
                <input type="text" class="form-control bg-light" disabled v-model="form.nama_diagnosa" />
                <button type="button" class="btn btn-info" @click="openModal">Cari</button>
                <button type="button" class="btn btn-danger" @click="delDiagnosaMedis">Del</button>
                <div v-if="form.errors.nama_diagnosa || form.errors.kode_diagnosa" class="invalid-feedback d-block">
                  {{ form.errors.nama_diagnosa }}
                </div>
              </div>
            </div>
          </div>

          <!-- Alergi -->
          <div class="row mb-3">
            <div class="col-12 col-md-3 d-flex flex-column">
              <label class="fw-bold">Alergi Makan</label>
              <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.alergi_makanan" />
            </div>
            <div class="col-12 col-md-3 d-flex flex-column">
              <label class="fw-bold">Alergi Obat</label>
              <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.alergi_obat" />
            </div>
            <div class="col-12 col-md-6 d-flex flex-column">
              <label class="fw-bold">Keterangan Alergi</label>
              <input class="form-control bg-warning bg-opacity-75" type="text" v-model="form.keterangan_alergi" />
            </div>
          </div>

          <!-- Kunjungan -->
          <div class="row mb-3">
            <div class="col-12 col-md-3 d-flex flex-column">
              <label class="fw-bold">Kunjungan Khusus</label>
              <select class="form-select" v-model="form.kunjungan_khusus">
                <option value="" selected>-Pilih-</option>
                <option v-for="item in diagnosaKasus" :key="item.id" :value="item.id">
                  {{ item.kasus }}
                </option>
              </select>
              <div v-if="form.errors.kunjungan_khusus" class="invalid-feedback d-block">
                {{ form.errors.kunjungan_khusus }}
              </div>
            </div>
            <div class="col-12 col-md-9 d-flex flex-column">
              <label class="fw-bold">Keterangan</label>
              <input class="form-control" type="text" v-model="form.keterangan_kunjungan" />
            </div>
          </div>

          <!-- Tombol Simpan -->
          <button type="submit" class="btn btn-success w-30">
            <i class="far fa-envelope me-2"></i>Simpan Diagnosa Medis
          </button>
        </form>

        <!-- Table Diagnosa Medis -->
        <div class="table-responsive">
          <table class="table table-bordered table-sm align-middle">
            <thead class="table-primary">
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
              <tr v-for="(item, index) in simpusDataDiagnosaMedis" :key="item.idDiagnosa"
                :class="{ 'table-warning': item.simpus_diagnosa?.klb === 1 && item.master_diagnosa_kasus.id === 3 }">
                <td>{{ index + 1 }}</td>
                <td>{{ item.nmDiagnosa }}</td>
                <td>{{ item.keterangan }}</td>
                <td>{{ item.master_diagnosa_kasus?.kasus }}</td>
                <td>{{ item.simpus_poli_f_k_t_p.nmPoli }}</td>
                <td>
                  <button class="btn btn-sm btn-danger" @click="hapusDiagnosa(item.idDiagnosa)">
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span class="fw-bold mt-2" style="text-align: justify; display: block;">
          Info : Jika muncul warna kuning di daftar diagnosa pasien, mohon cek kembali. Karena warna kuning menandakan
          ada diagnosa KLB. Jika salah input, mohon diperbaiki. Jika memang diagnosa nya bisa diabaikan. syarat warna
          kuning di daftar diagnosa, "Jika Diagnosa KLB dan Kasus Baru"
        </span>
      </div>

      <!-- ==================== KOLOM KANAN ==================== -->
      <div class="col-12 col-lg-6">
        <!-- Pemeriksaan Penunjang & Form Lanjutan -->
        <div class="row mb-5 g-3">
          <div class="col-12 col-md-5">
            <div class="fw-bold mb-4">
                <h6 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Pemeriksaan Penunjang</h6>
              </div>

            <Link :href="route('ruang-layanan.form-laborat', {
              idPoli: props.idPoli,
              idLoket: props.idLoket,
              idPelayanan: props.idPelayanan
            })" class="btn btn-secondary w-30">
            <i class="fas fa-bars me-2"></i>Laboratorium
            </Link>
          </div>
          <!-- <div class="col-12 col-md-7">
            <p class="fw-bold text-decoration-underline mb-4">
              <span class="bg-warning px-2 d-inline-block">Form Lanjutan</span>
            </p>
            <div class="d-flex flex-wrap gap-2">
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
          </div> -->
        </div>

        <!-- Diagnosa Keperawatan -->
        <div>
           <div class="fw-bold mb-4">
                <h6 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Diagnosa Keperawatan</h6>
              </div>


          <form @submit.prevent="submitFormDiagnosaKeperawatan" class="row g-2 align-items-center mb-4">
            <div class="col-12 col-md-9">
              <select class="form-select" v-model="formDiagnosaKeperawatan.kode_diagnosa" @change="updateNamaDiagnosa">
                <option value="">-- Pilih --</option>
                <option v-for="item in props.MasterDiagnosaKeperawatan" :key="item.id" :value="item.id">
                  {{ item.nmDiag }}
                </option>
              </select>
              <div v-if="formDiagnosaKeperawatan.errors.nama_diagnosa" class="invalid-feedback d-block">
                {{ formDiagnosaKeperawatan.errors.nama_diagnosa }}
              </div>
            </div>
            <div class="col-12 col-md-3">
              <button type="submit" class="btn btn-success w-100">
                <i class="far fa-envelope me-2"></i>Simpan
              </button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
              <thead class="table-primary">
                <tr>
                  <th>No</th>
                  <th>Nama Diagnosa Asuhan Keperawatan</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in props.diagnosaKeperawatan" :key="item.idDiagnosa">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.nmDiagnosa }}</td>
                  <td>
                    <button class="btn btn-sm btn-danger" @click="hapusDiagnosaKeperawatan(item.idDiagnosa)">
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
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
                  placeholder="Cari diagnosa..." @input="searchDiagnosa" />
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
                  <td>{{ item.non_spes == '1' ? 'Non Spesial' : '' }}</td>
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
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';
import Swal from 'sweetalert2'
const props = defineProps({
  diagnosaKasus: Array,
  dataPasien: Object,
  simpusDataDiagnosaMedis: Array,
  routeDiagnosaMedis: String,
  AlergiPasien: Object,
  MasterDiagnosaKeperawatan: Object,
  diagnosaKeperawatan: Object,
  idPelayanan: String,
  idLoket: String,
  idPoli: String
});
const keyword = ref('');
const showModal = ref(false);
const diagnosaMedis = ref({ data: [], links: [] });
const emit = defineEmits(['dataAnamnesa-update']);
console.log('simpusDataDiagnosaMedis  :', props.simpusDataDiagnosaMedis);
console.log('alergi', props.AlergiPasien)
const form = useForm({
  kode_diagnosa: '',
  nama_diagnosa: '',
  kunjungan_khusus: '',
  keterangan_kunjungan: '',
  kdPoli: props.dataPasien?.kdPoli ?? '',
  alergi_makanan: props.AlergiPasien?.alergi_makanan?.namaAlergiBpjs ?? '',
  alergi_obat: props.AlergiPasien?.alergi_obat?.namaAlergiBpjs ?? '',
  keterangan_alergi: props.AlergiPasien?.keterangan,
});

const formDiagnosaKeperawatan = useForm({
  kode_diagnosa: '',
  nama_diagnosa: '',
  kdPoli: props.idPoli ?? ''
})
console.log('formnya', formDiagnosaKeperawatan);


function openModal() {
  showModal.value = true;
  fetchPage(route('api.diagnosa-medis'));
}

function pilihDiagnosa(item) {
  form.kode_diagnosa = item.kdDiag;
  form.nama_diagnosa = item.nmDiag;
  showModal.value = false;
}
function updateNamaDiagnosa() {
  const selected = props.MasterDiagnosaKeperawatan.find(
    (item) => item.id === formDiagnosaKeperawatan.kode_diagnosa
  );
  formDiagnosaKeperawatan.nama_diagnosa = selected ? selected.nmDiag : '';
}


function submitForm() {
  form.post(route(props.routeDiagnosaMedis, {
    idLoket: props.idLoket,
    idPelayanan: props.idPelayanan
  }), {
    preserveScroll: true,
    onSuccess: () => {
      Swal?.fire({
        title: 'Sukses',
        text: 'Data Diagnosa Medis Tersimpan!',
        icon: 'success',
        timer: 1600,
        showConfirmButton: false
      });
      emit('dataAnamnesa-update');
    },
  });
}

function submitFormDiagnosaKeperawatan() {
  formDiagnosaKeperawatan.post(route('ruang-layanan.diagnosa-keperawatan', {
    idLoket: props.idLoket,
    idPelayanan: props.idPelayanan
  }), {
     preserveScroll: true,
    onSuccess: () => {
      Swal?.fire({
        title: 'Sukses',
        text: 'Data Diagnosa Medis Tersimpan!',
        icon: 'success',
        timer: 1600,
        showConfirmButton: false
      });
      emit('dataAnamnesa-update');
    },
  });
}
function searchDiagnosa() {
  fetchPage(route('api.diagnosa-medis', { search: keyword.value }));
}

function fetchPage(url) {
  // if (!url) return;

  // const relativeUrl = url.startsWith('http')
  //   ? new URL(url).pathname + new URL(url).search
  //   : url;
  axios.get(url)
    .then(res => {
      diagnosaMedis.value = res.data;
    })
    .catch(err => console.error(err));
}

function hapusDiagnosa(idDiagnosa) {
  form.delete(route('ruang-layanan.remove-diagnosa-medis',
    idDiagnosa
  ), {
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

function hapusDiagnosaKeperawatan(idDiagnosa) {
  form.delete(route('ruang-layanan.remove-diagnosa-keperawatan', idDiagnosa), {
    data: {
      _method: 'delete',
    },
    preserveScroll: true,
    onSuccess: () => {
      alert("Diagnosa keperawatan dihapus");
      emit('dataAnamnesa-update');
    },
  });
}

function delDiagnosaMedis() {
  form.nama_diagnosa = '',
    form.kode_diagnosa = ''
}
</script>
