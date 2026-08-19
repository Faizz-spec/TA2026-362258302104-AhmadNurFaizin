<template>
  <div class="container">
    <div class="card">
      <div class="border rounded rounded-bottom-0 shadow-sm d-flex gap-4 p-3">
        <a href="" class="text-decoration-none text-primary"
          :class="{ 'text-primary fw-bold': currrentSubTabPlanning === 'tindakan' }"
          @click.prevent="currrentSubTabPlanning = 'tindakan'">Tindakan ></a>
        <a href="" class="text-decoration-none text-primary"
          :class="{ 'text-primary fw-bold': currrentSubTabPlanning === 'pengobatan' }"
          @click.prevent="currrentSubTabPlanning = 'pengobatan'">Pengobatan ></a>
      </div>
      <div class="card-body" v-if="currrentSubTabPlanning === 'tindakan'">
        <form @submit.prevent="submitForm" action="">
          <slot></slot>
          <!-- Kode Tindakan + Tombol -->
          <div class="mb-3 row align-items-center">
            <label class="col-sm-2 col-form-label fw-bold">Kode Tindakan</label>
            <div class="col-sm-4">
              <div class="input-group">
                <input type="text" class="form-control bg-light" disabled v-model="form.kode_tindakan" />
                <button type="button" class="btn btn-info" @click="openModal()">Cari</button>
                <button type="button" class="btn btn-danger ">Del</button>
              </div>
            </div>
          </div>

          <!-- Nama Tindakan -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label fw-bold">Nama Tindakan</label>
            <div class="col-sm-4">
              <input type="text" class="form-control bg-light" disabled v-model="form.nama_tindakan" />
            </div>
          </div>

          <!-- Nama Tindakan (Ind) -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label fw-bold">Nama Tindakan (Ind)</label>
            <div class="col-sm-4">
              <input type="text" class="form-control bg-light" disabled v-model="form.nama_tindakan_indonesia" />
            </div>
          </div>

          <!-- Keterangan -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label fw-bold">Keterangan</label>
            <div class="col-sm-4">
              <textarea class="form-control" rows="2" v-model="form.keterangan_tindakan"></textarea>
            </div>
          </div>

          <!-- Tombol Simpan -->
          <div class="row">
            <div class=" col-sm-4">
              <button type="submit" class="btn btn-success">Simpan</button>
            </div>
          </div>
        </form>
        <hr>
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-sm text-center">
            <thead class="table-primary">
              <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Tindakan</th>
                <th>Peraturan</th>
                <th>Harga</th>
                <th>Bayar</th>
                <th>Poli</th>
                <th>Keterangan</th>
                <th>Ket gigi</th>
                <th>Created by</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in simpusTindakan" :key="item.idTindakan">
                <td>{{ index + 1 }}</td>
                <td>{{ item.kdTindakan }}</td>
                <td>{{ item.nmTindakan }}</td>
                <td>{{ item.peraturan }}</td>
                <td>{{ item.harga }}</td>
                <td>{{ item.bayar }}</td>
                <td>{{ item.kdPoli }}</td>
                <td>{{ item.keterangan }}</td>
                <td>{{ item.ketGigi }}</td>
                <td>{{ item.createdBy }}</td>
                <td>
                  <button class="btn btn-sm btn-danger" @click="hapusDataTindakan(item.idTindakan)">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-body" v-if="currrentSubTabPlanning === 'pengobatan'">
        <form action="" @submit.prevent="submitFormPengobatan">
          <div class="my-4">
            <div class="mb-3 row align-items-center">
              <label class="col-sm-2 col-form-label fw-bold">Puyer/Bukan puyer</label>
              <div class="col-sm-4">
                <select class="form-select" v-model="FormObat.jenis_obat">
                  <option value="0">BUKAN PUYER</option>
                  <option value="1">PUYER</option>
                </select>
              </div>
            </div>
            <div v-if="isPuyer">
              <div class="mb-3 row align-items-center">
                <label class="col-sm-2 col-form-label fw-bold">Jumlah puyer</label>
                <div class="col-sm-4">
                  <input type="number" v-model="FormObat.jumlah_puyer">
                </div>
              </div>
              <div class="mb-3 row align-items-center">
                <label class="col-sm-2 col-form-label fw-bold">Dosis pakai puyer</label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Jumlah" v-model="FormObat.dosis_pakai" />
                    <span class="input-group-text">x Sehari, Setiap</span>
                    <input type="number" class="form-control" placeholder="Jam" v-model="FormObat.dosis_pakai_jam" />
                    <span class="input-group-text">Jam Sekali</span>
                  </div>
                </div>
              </div>

              <div class="mb-3 row align-items-center">
                <label class="col-sm-2 col-form-label fw-bold">Waktu</label>
                <div class="col-sm-4">
                  <div class="d-flex gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Pagi" v-model="FormObat.waktu">
                      <label class="form-check-label">Pagi</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Siang" v-model="FormObat.waktu">
                      <label class="form-check-label"> Siang</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Malam" v-model="FormObat.waktu">
                      <label class="form-check-label"> Malam</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3 row align-items-center">
                <label class="col-sm-2 col-form-label fw-bold">Kondisi</label>
                <div class="col-sm-4">
                  <div class="d-flex gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Baik" v-model="FormObat.kondisi">
                      <label class="form-check-label">Sebelum makan</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Sedang" v-model="FormObat.kondisi">
                      <label class="form-check-label">Saat makan</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Lemah" v-model="FormObat.kondisi">
                      <label class="form-check-label">Sesudah makan</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>

          </div>
        </form>
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
              <!-- LOOP: resep -->
              <template v-for="resep in props.simpusResepObat" :key="resep.id_resep">
                <!-- Header baris hijau per resep -->
                <tr class="table-success align-middle">
                  <td class="fw-bold">{{ resep.poli }}</td>
                  <td class="text-start fw-bold">
                    {{ resep.kategori == '1' ? 'Puyer' : 'Bukan Puyer' }}
                  </td>
                  <td class="fw-bold">
                    <template v-if="resep.kategori == '1'">
                      {{ resep.jumlah_puyer }} Puyer
                    </template>
                    <template v-else>-</template>
                  </td>
                  <td class="fw-bold text-start">
                    <template v-if="resep.kategori == '1'">
                      {{ resep.dosis_pakai_puyer || '-' }}
                      <template v-if="resep.tiapJam"> || Setiap {{ resep.tiapJam }} jam sekali</template>
                      <template v-if="resep.waktu"> || Waktu: {{ resep.waktu }}</template>
                      <template v-if="resep.kondisi"> || Kondisi: {{ resep.kondisi }}</template>
                    </template>
                    <template v-else>-</template>
                  </td>
                  <td class="text-nowrap">
                    <!-- Tombol untuk resep -->
                    <button class="btn btn-sm btn-success me-2" @click="openModalMasterObat(resep)">
                      <i class="bi bi-plus-circle"></i> Tambah Obat
                    </button>
                    <button class="btn btn-sm btn-danger" @click="hapusResep(resep)">
                      <i class="bi bi-trash"></i> Hapus Resep
                    </button>
                  </td>
                </tr>

                <!-- LOOP: detail dalam resep -->
                <tr v-for="detail in resep.simpus_detail_resep_obat" :key="detail.id_resep_detail" class="align-middle">
                  <td>{{ detail.poli }}</td>
                  <td class="text-start">
                    {{ detail.master_obat?.NAMA || '-' }}
                  </td>
                  <td>
                    <template v-if="resep.kategori == '1'">
                      {{ detail.jumlah }}
                    </template>
                    <template v-else>
                      {{ detail.jumlah }} {{ detail.unit_details || detail.satuan || '' }}
                    </template>
                  </td>
                  <td class="text-start">
                    <template v-if="resep.kategori == '1'">
                      <!-- Kosong karena dosis sudah ada di header resep -->
                      <em class="text-muted">Ikuti dosis resep</em>
                    </template>
                    <template v-else>
                      {{ detail.dosis_pakai || '-' }}
                      <template v-if="detail.tiapJam">, Setiap {{ detail.tiapJam }} Jam sekali</template>
                      <template v-if="detail.waktu">, waktu: {{ detail.waktu }}</template>
                      <template v-if="detail.kondisi">, kondisi: {{ detail.kondisi }}</template>
                    </template>
                  </td>
                  <td>
                    <!-- Tombol untuk detail -->
                    <button class="btn btn-sm btn-outline-danger" @click="hapusResepDetail(detail)">
                      <i class="bi bi-x-circle"></i> Hapus Obat
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>

          </table>
        </div>
      </div>
      <!-- Modal Tindakan -->
      <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header flex-column">
              <div class="w-100 mb-4 d-flex justify-content-end">
                <button type="button" class="btn-close" @click="showModal = false"></button>
              </div>
              <div class="w-100 d-flex align-items-center">
                <h5 class="modal-title mb-0">Pilih Tindakan</h5>
                <form class="d-flex ms-auto">
                  <input type="text" v-model="keyword" @input="searchTindakan" id="search-diagnosa"
                    class="form-control form-control-sm me-2" placeholder="Cari diagnosa...">
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
                    <th>IND (TRANSLATE)</th>
                    <th>ACTION</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in masterTindakan.data" :key="index">
                    <td>{{ item.kode }}</td>
                    <td>{{ item.nama_tindakan }}</td>
                    <td>{{ item.nama_tindakan_indonesia }}</td>
                    <td>
                      <button class="btn btn-info btn-sm" @click="pilihMasterTindakan(item)">Pilih</button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- pagination di luar tabel -->
              <div class="mt-2 d-flex flex-wrap gap-2">
                <button v-for="(link, i) in masterTindakan.links" :key="i" v-html="link.label" class="btn btn-sm"
                  :class="{
                    'btn-primary': link.active,
                    'btn-outline-primary': !link.active,
                    'disabled': !link.url
                  }" @click="link.url && fetchPage(link.url)" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Master Obat -->
      <div v-if="showModalMasterObat" class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header flex-column">
              <div class="w-100 mb-4 d-flex justify-content-end">
                <button type="button" class="btn-close" @click="showModalMasterObat = false"></button>
              </div>
              <div class="w-100 d-flex align-items-center">
                <h5 class="modal-title mb-0">Pilih Obat</h5>
                <form class="d-flex ms-auto">
                  <input type="text" v-model="keyword" id="search-diagnosa" class="form-control form-control-sm me-2"
                    placeholder="Cari diagnosa...">
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
                    <th>SATUAN</th>
                    <th>ACTION</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in masterObat.data" :key="index">
                    <td>{{ item.KODE_OBAT }}</td>
                    <td>{{ item.NAMA }}</td>
                    <td>{{ item.SATUAN }}</td>
                    <td>
                      <button class="btn btn-info btn-sm" @click="pilihMasterObat(item)">Pilih</button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- pagination di luar tabel -->
              <div class="mt-2 d-flex flex-wrap gap-2">
                <button v-for="(link, i) in masterObat.links" :key="i" v-html="link.label" class="btn btn-sm" :class="{
                  'btn-primary': link.active,
                  'btn-outline-primary': !link.active,
                  'disabled': !link.url
                }" @click="link.url && fetchPageMasterObat(link.url)" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Tambah Obat Puyer -->
      <div v-if="showModalTambahObatPuyer" class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered"> <!-- modal besar & di tengah -->
          <div class="modal-content rounded-4 shadow-lg">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
              <h5 class="modal-title fw-bold">Detail Obat</h5>
              <button type="button" class="btn-close btn-close-white"
                @click="showModalTambahObatPuyer = false"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
              <table class="table table-borderless align-middle">
                <tbody>
                  <tr>
                    <th class="w-25 text-secondary">Kode Obat</th>
                    <td class="fw-semibold">: {{ selectedObat.KODE_OBAT }}</td>
                  </tr>
                  <tr>
                    <th class="text-secondary">Nama Obat</th>
                    <td class="fw-semibold">: {{ selectedObat.NAMA }}</td>
                  </tr>
                  <tr>
                    <th class="text-secondary">Satuan</th>
                    <td class="fw-semibold">: {{ selectedObat.SATUAN }}</td>
                  </tr>
                  <tr>
                    <th class="text-secondary">Jumlah</th>
                    <td>
                      <div class="d-flex align-items-center">
                        <input type="number" v-model="FormDetailResepObat.jumlah_obat"
                          class="form-control form-control-sm w-25 me-2">
                        <small class="text-danger fst-italic">gunakan titik (.) untuk desimal</small>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="showModalTambahObatPuyer = false">
                Kembali
              </button>
              <button class="btn btn-primary rounded-pill px-4" @click="submitFormPengobatanDetail">
                Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Tambah Obat Bukan Puyer -->
      <div v-if="showModalTambahObatBukanPuyer" class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content rounded-4 shadow-lg">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
              <h5 class="modal-title fw-bold">Detail Obat</h5>
              <button type="button" class="btn-close btn-close-white"
                @click="  showModalTambahObatBukanPuyer = false"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
              <table class="table table-borderless align-middle">
                <tbody>
                  <tr>
                    <td class="fw-bold" style="width: 180px;">Kode Obat</td>
                    <td>:</td>
                    <td>{{ selectedObat.KODE_OBAT }}</td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Nama Obat</td>
                    <td>:</td>
                    <td> {{ selectedObat.NAMA }}</td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Satuan</td>
                    <td>:</td>
                    <td>{{ selectedObat.SATUAN }}</td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Jumlah</td>
                    <td>:</td>
                    <td>
                      <input type="number" step="0.01" class="form-control d-inline-block w-auto"
                        v-model="FormDetailResepObat.jumlah_obat">
                      <small class="text-danger ms-2">gunakan titik (.) untuk input jumlah desimal</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Dosis Pakai</td>
                    <td>:</td>
                    <td>
                      <input type="text" class="form-control d-inline-block w-auto"
                        v-model="FormDetailResepObat.dosis_pakai">
                      , setiap
                      <input type="number" class="form-control d-inline-block w-auto"
                        v-model="FormDetailResepObat.dosis_pakai_jam">
                      Jam sekali
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Waktu konsumsi</td>
                    <td>:</td>
                    <td>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="pagi"
                          v-model="FormDetailResepObat.waktu">
                        <label class="form-check-label">pagi</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="siang"
                          v-model="FormDetailResepObat.waktu">
                        <label class="form-check-label">siang</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="malam"
                          v-model="FormDetailResepObat.waktu">
                        <label class="form-check-label">malam</label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Kondisi konsumsi</td>
                    <td>:</td>
                    <td>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="sebelum"
                          v-model="FormDetailResepObat.kondisi">
                        <label class="form-check-label">sebelum makan</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="saat"
                          v-model="FormDetailResepObat.kondisi">
                        <label class="form-check-label">saat makan</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="setelah"
                          v-model="FormDetailResepObat.kondisi">
                        <label class="form-check-label">setelah makan</label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Keterangan</td>
                    <td>:</td>
                    <td>
                      <input type="text" class="form-control" v-model="FormDetailResepObat.keterangan">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
              <button class="btn btn-outline-secondary rounded-pill px-4"
                @click=" showModalTambahObatBukanPuyer = false">
                Kembali
              </button>
              <button class="btn btn-primary rounded-pill px-4" @click="submitFormPengobatanDetail">
                Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';
import { computed, watch } from 'vue';

const currrentSubTabPlanning = ref('tindakan');
const keyword = ref('');
const showModal = ref(false);
const showModalMasterObat = ref(false);
const showModalTambahObatPuyer = ref(false);
const showModalTambahObatBukanPuyer = ref(false);
const masterTindakan = ref({ data: [], links: [] });
const masterObat = ref({ data: [], links: [] });
const selectedObat = ref({});

const props = defineProps({
  dataPasien: Object,
  simpusTindakan: Object,
  keterangangigi: String,
  routePlanningTindakan: String,
  simpusResepObat: Array,
});
const emit = defineEmits(['dataAnamnesa-update']);
console.log('data resep obat :', props.simpusResepObat);

function pilihMasterTindakan(item) {
  form.kode_tindakan = item.kode
  form.nama_tindakan = item.nama_tindakan
  form.nama_tindakan_indonesia = item.nama_tindakan_indonesia
  showModal.value = false
}
function pilihMasterObat(item) {
  selectedObat.value = item;
  FormDetailResepObat.obat_id = item.OBAT_ID
  showModalMasterObat.value = false;
  if (FormDetailResepObat.kategori === '1') {
    showModalTambahObatPuyer.value = true;
    showModalTambahObatBukanPuyer.value = false;
  } else {
    showModalTambahObatBukanPuyer.value = true;
    showModalTambahObatPuyer.value = false;
  }
}

const form = useForm({
  kode_tindakan: props.simpusTindakan?.kode_tindakan ?? '',
  nama_tindakan: props.simpusTindakan?.nmTindakan ?? '',
  nama_tindakan_indonesia: props.simpusTindakan?.nmTindakanId ?? '',
  keterangan_tindakan: props.simpusTindakan?.keterangan ?? '',
  loketId: props.dataPasien?.idLoket ?? '',
  kdPoli: props.dataPasien?.kdPoli ?? '',
  keterangangigi: props.keterangangigi ?? ''
});
watch(() => props.keterangangigi, (baru) => {
  form.keterangangigi = baru
});

const FormObat = useForm({
  jenis_obat: '0',
  jumlah_puyer: '',
  dosis_pakai_jam: '',
  dosis_pakai: '',
  waktu: [],
  kondisi: [],
  loketId: props.dataPasien?.idLoket ?? '',
});

const FormDetailResepObat = useForm({
  jumlah_obat: '',
  resep_id: '',
  obat_id: selectedObat?.OBAT_ID ?? '',
  poli: props?.dataPasien.nmPoli ?? ' ',
  kategori: '',
  dosis_pakai: '',
  dosis_pakai_jam: '',
  waktu: [],
  kondisi: [],
  keterangan: ''
})

const isPuyer = computed(() => FormObat.jenis_obat === '1')
console.log('boolean', isPuyer);

function openModal() {
  showModal.value = true;
  fetchPage(route('ruang-layanan.master-tindakan'));
}

function openModalMasterObat(item) {
  showModalMasterObat.value = true;
  fetchPageMasterObat(route('ruang-layanan.master-obat'));
  console.log('id resepnya',);
  FormDetailResepObat.resep_id = item.id_resep;
  FormDetailResepObat.kategori = item.kategori;
}

function searchTindakan() {
  fetchPage(route('ruang-layanan.master-tindakan', { search: keyword.value }));
}

function fetchPage(url) {
  if (!url) return;

  const relativeUrl = url.startsWith('http')
    ? new URL(url).pathname + new URL(url).search
    : url;
  axios.get(relativeUrl)
    .then(res => {
      masterTindakan.value = res.data;
    })
    .catch(err => console.error(err));
}

function fetchPageMasterObat(url) {
  if (!url) return;
  const relativeUrl = url.startsWith('http')
    ? new URL(url).pathname + new URL(url).search
    : url;
  axios.get(relativeUrl)
    .then(res => {
      masterObat.value = res.data;
      console.log('data master obat', masterObat.value)
    })
    .catch(err => console.error(err));
}
function submitForm() {
  form.post(route(props.routePlanningTindakan), {
    preserveScroll: true,
    onSuccess: () => {
      alert("Plannngin tersimpan");
      emit('dataAnamnesa-update');
    },
  })
}

function submitFormPengobatan() {
  FormObat.post(route('ruang-layanan-gigi.set-PlanningPengobatan'), {
    preserveScroll: true,
    onSuccess: () => {
      alert("Data Obat tersimpan");
      emit('dataAnamnesa-update');
    },
  });
}

function submitFormPengobatanDetail() {
  FormDetailResepObat.post(route('ruang-layanan-gigi.set-PlanningPengobatanDetail'), {
    preserveScroll: true,
    onSuccess: () => {
      alert("Obat berhasil disimpan tersimpan");
      emit('dataAnamnesa-update');
      showModalTambahObatPuyer.value = false;
      showModalTambahObatBukanPuyer.value = false;
    },
  });
}
function hapusDataTindakan(id) {
  form.delete(route('ruang-layanan-gigi.remove-data-tindakan', id), {
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