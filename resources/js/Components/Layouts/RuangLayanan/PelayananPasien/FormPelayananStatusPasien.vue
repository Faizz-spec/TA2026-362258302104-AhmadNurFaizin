<template>
  <div class="container my-2">
    <div class="alert alert-warning d-flex align-items-start gap-3 shadow-sm rounded-3 p-3 mb-4 border-0"
      v-if="diagnosaMedisNonSpesialistik.length !== 0">
      <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
      <div>
        <h6 class="fw-bold mb-1 text-dark">Peringatan</h6>
        <p class="mb-1 text-dark">Terdapat diagnosa <span class="fw-bold text-danger">Non Spesialistik</span> yang
          digunakan. </p>
        <p class="mb-0 fst-italic text-secondary fw-bold" v-for="value in diagnosaMedisNonSpesialistik">
          {{ value.kdDiagnosa }}-{{ value.nmDiagnosa }}
        </p>
      </div>
    </div>

    <!-- <div class="text-decoration-underline fw-bold mb-4">
      <p class="bg-warning d-inline-block px-2 rounded">Rujukan</p>
    </div> -->
    <!-- FORM -->
    <form @submit.prevent="submit_rujukan">
      <div class="row ">
        <div class="col-6">
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-bold">Status Pulang</label>
            <div class="col-sm-7">
              <select class="form-select" v-model="form.status_pulang">
                <option v-for="item in statusPulang" :key="item.kdStatusPulang" :value="item.kdStatusPulang">
                  {{ item.nmStatusPulang }}
                </option>
              </select>
              <div v-if="form.errors.status_pulang" class="invalid-feedback d-block">
                {{ form.errors.status_pulang }}
              </div>
            </div>
          </div>

          <div class="row mb-3" v-if="statusPulangLabel === '5'">
            <label class="col-sm-4 col-form-label fw-bold">Poli Rujuk Internal</label>
            <div class="col-sm-7">
              <select class="form-select" v-model="form.poli_rujuk_internal">
                <option value="" selected>-- Pilih --</option>
                <option v-for="value in props.poliRujukInternal" :value="value.kdPoli">{{ value.nmPoli }}</option>
              </select>
              <div v-if="form.errors.poli_rujuk_internal" class="invalid-feedback d-block">
                {{ form.errors.poli_rujuk_internal }}
              </div>
            </div>
          </div>

          <div class="row mb-3" v-if="statusPulangLabel === '4' || statusPulangLabel === '6'">
            <label class="col-sm-4 col-form-label fw-bold">PPK Rujukan</label>
            <div class="col-sm-7">
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="row mb-3" v-if="statusPulangLabel === '6'">
            <label class="col-sm-4 col-form-label fw-bold">Nama Poli</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled>
            </div>
          </div>
          <div class="row mb-3" v-if="statusPulangLabel === '6'">
            <label class="col-sm-4 col-form-label fw-bold">Nama Dokter</label>
            <div class="col-sm-7">
              <select class="form-select">
                <option selected>- pilih -</option>
                <option>dr. Agus</option>
                <option>dr. Sari</option>
              </select>
            </div>
          </div>

          <div class="row mb-3" v-if="statusPulangLabel === '4'">
            <label class="col-sm-4 col-form-label fw-bold">Spesialis/Subspesialis</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled>
            </div>
          </div>

          <div class="row mb-3" v-if="statusPulangLabel === '4' || statusPulangLabel === '6'">
            <label class="col-sm-4 col-form-label fw-bold">Catatan</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled>
            </div>
          </div>

          <div class="row mb-3" v-if="statusPulangLabel === '4' || statusPulangLabel === '6'">
            <label class="col-sm-4 col-form-label fw-bold">Tgl Rencana Berkunjung</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled>
              <button type="button" class="mt-3 btn btn-info" @click="cariRujukan" data-bs-toggle="modal"
                data-bs-target="#formRujukLanjut">Cari Rujukan</button>
            </div>

          </div>

          <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-bold">Tenaga Medis</label>
            <div class="col-sm-7">
              <select class="form-select" v-model="form.tenaga_medis">
                <option selected>- pilih -</option>
                <option v-for="item in TenagaMedis">{{ item.nmDokter }}</option>
              </select>
              <div v-if="form.errors.tenaga_medis" class="invalid-feedback d-block">
                {{ form.errors.tenaga_medis }}
              </div>
            </div>
          </div>

          <div class="row">
            <div>
              <button type="submit" class="btn btn-success">Simpan</button>
            </div>
          </div>

        </div>
        <div class="col-6" v-if="diagnosaMedisNonSpesialistik.length !== 0">
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-bold">Kode Tacc</label>
            <div class="col-sm-7">
              <select class="form-select" v-model="form.status_pulang">
                <option v-for="item in masterTacc" :key="item.kdTacc" :value="item.kdTacc">
                  {{ item.nmTacc }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- TABLE -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-sm align-middle text-center">
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
        <tbody v-if="loading">
          <tr>
            <td colspan="9" class="text-center">Memuat data...</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="(item, index) in Pelayanan" :key="item.idPelayanan">
            <td>{{ index + 1 }}</td>
            <td><span class="badge bg-primary bg-opacity-75"> {{ item.simpus_poli.nmPoli }}</span></td>
            <td>{{ item.status_pulang?.nmStatusPulang ?? '' }}</td>
            <td>{{ item.poli_tujuan?.nmPoli ?? '' }}</td>
            <td>{{ item.tenagaMedis ?? '' }}</td>
            <td>{{ item.createdBy ?? '' }}</td>
            <td>{{ item.tglPelayanan ?? '' }}</td>
            <td>{{ item.endTIme ?? '' }}</td>
            <td>
              <template v-if="item.kdStatusPulang === '4' || item.kdStatusPulang === '6'">
                <button class="btn btn-warning btn-xs" @click="batalRujukLanjut(item.loketId, item.idPelayanan)">
                  Batal Rujuk Lanjut
                </button>
              </template>
              <template v-else>
                <template v-if="(item.pelIdSebelum == null || item.pelIdSebelum == '0') && item.kdStatusPulang != '3'">
                  <button class="btn btn-outline btn-sm" disabled>
                    Poli Awal
                  </button>
                </template>

                <template v-else-if="item.kdStatusPulang == '3'">
                  <button class="btn btn-sm btn-warning" @click="batal_berobat_jalan">
                    Batal Berobat Jalan
                  </button>
                </template>
                <template v-else>
                  <template v-if="item.idpelayanan === cekAkhirPelayanan.idpelayanan">
                    <button @click="hapusRujuk(route('ruang-layanan.hapusRujuk', item.idpelayanan))" type="button"
                      class="btn btn-danger btn-sm">
                      Batal Rujuk
                    </button>
                  </template>
                  <template v-else>
                    <button class="btn btn-outline btn-sm" disabled>
                      Batalkan
                    </button>
                  </template>
                </template>

              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="formRujukLanjut" tabindex="-1" aria-labelledby="formRujukLanjutLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="formRujukLanjutLabel">Form Rujukan Lanjut</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>


        <!-- Body -->
        <div class="modal-body">
          <div class="btn-group">
            <button class="btn btn-info" v-if="statusPulangLabel === '4'" @click="isRujukLanjutKhusus = true">Faskes
              Khusus</button>
            <button class="btn btn-primary" v-if="statusPulangLabel === '4'" @click="isRujukLanjutKhusus = false">Faskes
              Spesialis</button>
          </div>
          <form @submit.prevent="submitUkk">
            <div class="mb-3 row align-items-center" v-if="statusPulangLabel === '4' && isRujukLanjutKhusus">
              <label class="col-sm-5 col-form-label fw-semibold">Kategori</label>
              <div class="col-sm-7">
                <select class="form-select" @change="filterSubSpesialis($event.target.value)">
                  <option v-for="value in dataSpesialis" :value="value.kdSpesialis">{{ value.nmSpesialis }}</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row align-items-center" v-if="statusPulangLabel === '6'">
              <label class="col-sm-5 col-form-label fw-semibold">Nama Poli</label>
              <div class="col-sm-7">
                <select class="form-select" @change="filterSubSpesialis($event.target.value)">
                  <option v-for="value in dataSpesialis" :value="value.kdSpesialis">{{ value.nmSpesialis }}</option>
                </select>
              </div>
            </div>
            <!-- Spesialis -->
            <div class="mb-3 row align-items-center" v-if="statusPulangLabel === '4' && !isRujukLanjutKhusus">
              <label class="col-sm-5 col-form-label fw-semibold">Spesialis</label>
              <div class="col-sm-7">
                <select class="form-select" @change="filterSubSpesialis($event.target.value)">
                  <option v-for="value in dataSpesialis" :value="value.kdSpesialis">{{ value.nmSpesialis }}</option>
                </select>
              </div>
            </div>

            <!-- Sub Spesialis -->
            <div class="mb-3 row align-items-center" v-if="statusPulangLabel === '4' && !isRujukLanjutKhusus">
              <label class="col-sm-5 col-form-label fw-semibold">Sub Spesialis</label>
              <div class="col-sm-7">
                <select class="form-select">
                  <option value="" v-for="value in filterDataSubSpesialis">{{ value.nmSubSpesialis }}</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row align-items-center" v-if="statusPulangLabel === '4' && isRujukLanjutKhusus">
              <label class="col-sm-5 col-form-label fw-semibold">Alasan</label>
              <div class="col-sm-7">
                <input type="text" class="form-control">
              </div>
            </div>

            <!-- Sarana -->
            <div class="mb-3 row align-items-center" v-if="!isRujukLanjutKhusus">
              <label class="col-sm-5 col-form-label fw-semibold">Sarana</label>
              <div class="col-sm-7">
                <select class="form-select">
                  <option :value="value.kdSarana" v-for="value in spesialisSarana">{{ value.nmSarana }}</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="mb-3 row align-items-center">
              <label class="col-sm-5 col-form-label fw-semibold">Tgl Rencana Berkunjung</label>
              <div class="col-sm-7">
                <input type="date" class="form-control" />
              </div>
            </div>

            <!-- Footer buttons -->
            <div class="d-flex justify-content-end gap-2 mt-4">
              <button type="submit" class="btn btn-success">
                Cari Faskes Spesialis
              </button>
            </div>
          </form>
          <div class="mt-5">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">No</th>
                  <th scope="col">Faskes</th>
                  <th scope="col">Kelas</th>
                  <th scope="col">Cabang</th>
                  <th scope="col">Alamat</th>
                  <th scope="col">Telp</th>
                  <th scope="col">Jarak</th>
                  <th scope="col">Total</th>
                  <th scope="col">Kap</th>
                  <th scope="col">%</th>
                  <th scope="col">Jadwal</th>
                  <th scope="col">Pilih</th>
                </tr>
              </thead>
              <!-- <tbody>
                <tr>
                  <th scope="row">1</th>
                  <td>Mark</td>
                  <td>Otto</td>
                  <td>@mdo</td>
                </tr>
                <tr>
                  <th scope="row">2</th>
                  <td>Jacob</td>
                  <td>Thornton</td>
                  <td>@fat</td>
                </tr>
                <tr>
                  <th scope="row">3</th>
                  <td>John</td>
                  <td>Doe</td>
                  <td>@social</td>
                </tr>
              </tbody> -->
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

</template>

<script setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';
const props = defineProps({
  statusPulang: Array,
  DataRujuk: Array,
  TenagaMedis: Object,
  idPelayanan: String,
  idLoket: String,
  poliRujukInternal: Array,
  masterTacc: Array,
  simpusDataDiagnosaMedis: Array,
  idPoli: String
})
const emit = defineEmits(['dataRujuk-update'])
const DataRujuk = props.DataRujuk ?? '';
const TenagaMedis = props.TenagaMedis ?? '';
const Pelayanan = ref(null);
const loading = ref(true)
const cekAkhirPelayanan = ref(null);
const dataSpesialis = ref([])
const dataSubSpesialis = ref([])
const filterDataSubSpesialis = ref([])
const spesialisSarana = ref([])
const diagnosaMedisNonSpesialistik = ref([])
const isRujukLanjutKhusus = ref(true)

console.log('panjang diagnosa spesial', props.simpusDataDiagnosaMedis);

onMounted(() => {
  fetchPelayanan(route('ruang-layanan.ambilPelayanan', { idLoket: props.idLoket, idPelayanan: props.idPelayanan }));
  cekDiagnosaMedisNonSpesialistik();
  console.log('Pelayanan', Pelayanan);

});
const form = useForm({
  status_pulang: '',
  tenaga_medis: '',
  poli_rujuk_internal: ''
});

const statusPulangLabel = computed(() => {
  const found = props.statusPulang.find(
    (item) => item.kdStatusPulang === form.status_pulang
  )
  console.log(found?.kdStatusPulang, 'kd status pulang')
  return found ? found.kdStatusPulang : ''
})

function submit_rujukan() {
  if (props.idPoli === form.poli_rujuk_internal) {
    Swal?.fire({
      title: 'Warning',
      text: 'Poli Tujuan Harus Berbeda',
      icon: 'warning',
      timer: 1600,
      showConfirmButton: false
    });
  } else {
    form.post(route('ruang-layanan.simpanRujukan', {
      idLoket: props.idLoket,
      idPelayanan: props.idPelayanan
    }), {
      preserveScroll: true,
      onSuccess: () => {
        Swal?.fire({
          title: 'sukses',
          text: 'Berhasil Tersimpan',
          icon: 'success',
          timer: 1600,
          showConfirmButton: false
        });
        loading.value = true
        fetchPelayanan(route('ruang-layanan.ambilPelayanan', { idLoket: props.idLoket, idPelayanan: props.idPelayanan }))
      },
    });
  }

}

function fetchPelayanan(url) {
  console.log(route('ruang-layanan.ambilPelayanan', { idLoket: props.idLoket, idPelayanan: props.idPelayanan }))
  if (!url) return;

  const relativeUrl = url.startsWith('http')
    ? new URL(url).pathname + new URL(url).search
    : url;

  axios.get(relativeUrl)
    .then(res => {
      Pelayanan.value = res.data.pelayanan;
      cekAkhirPelayanan.value = res.data.cekAkhirPelayanan
      console.log('Hasil fetch pelayanan :', res.data.pelayanan);
    })
    .catch(err => console.error(err))
    .finally(() => {
      loading.value = false;
      console.log('selesai', loading.value)
    });
}

function hapusRujuk(url) {
  if (!url) return;

  const relativeUrl = url.startsWith('http')
    ? new URL(url).pathname + new URL(url).search
    : url;

  axios.delete(relativeUrl)
    .then(res => {
    })
    .catch(err => console.error(err))
    .finally(() => {
      loading.value = true
      fetchPelayanan(route('ruang-layanan.ambilPelayanan', { idLoket: props.idLoket, idPelayanan: props.idPelayanan }))
    });
}

function batal_berobat_jalan() {
  console.log('idLoket:', props.idLoket, 'idPelayanan:', props.idPelayanan);
  Swal?.fire({
    title: 'Warning',
    text: 'Batal berobat jalan',
    icon: 'warning',
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: 'Ya, batalkan!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      axios.get(
        route('ruang-layanan.batal-berobat-jalan', {
          idLoket: props.idLoket,
          idpelayanan: props.idPelayanan
        })
      )
        .then(res => {
          Swal?.fire({
            title: 'Sukses',
            text: 'Sukses batal berobat jalan',
            icon: 'success',
            timer: 1600,
            showConfirmButton: false
          })

          // alert(res.data.message);
        })
        .catch(err => {
          console.error('Error detail:', err.response?.data || err.message);
          alert('Terjadi kesalahan');
        }).finally(() => {
          loading.value = true
          fetchPelayanan(route('ruang-layanan.ambilPelayanan', { idLoket: props.idLoket, idPelayanan: props.idPelayanan }))
        });

    }
  });


}

function cariRujukan() {
  axios.get(route('ruang-layanan.popUpFormRujukLanjut'))
    .then(res => {
      // alert('hai')
      dataSpesialis.value = res.data.spesialis
      dataSubSpesialis.value = res.data.subspesialis
      filterDataSubSpesialis.value = res.data.subspesialis
      spesialisSarana.value = res.data.spesialisSarana
      console.log('Response dataformrujuklanjut:', res.data.subspesialis);
    })
    .catch(err => {
      console.log('error:', err);
    })
}

function filterSubSpesialis(id) {
  console.log(id)
  filterDataSubSpesialis.value = dataSubSpesialis.value.filter(
    (item) => String(item.kdSpesialis) == String(id)
  )
  console.log(filterDataSubSpesialis.value)
}

function cekDiagnosaMedisNonSpesialistik() {
  const data = props.simpusDataDiagnosaMedis || []; // fallback array kosong

  diagnosaMedisNonSpesialistik.value = data.filter(
    (item) => item?.simpus_diagnosa?.non_spes === 1
  );
}


function cek() {
  alert('hai')

}
</script>