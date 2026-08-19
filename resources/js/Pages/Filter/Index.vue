<template>
  <AppLayout>
    <div class="container py-4">
      <div class="d-flex justify-content-end mb-3 gap-2">
        <Link :href="route('filter.dev')" class="btn text-white"> Dev This Page </Link>
      </div>
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="text-header">
            <h5 class="text-primary fw-semibold fs-3 mt-2 mb-3 ms-2 gap-4">
              <i class="bi bi-funnel-fill"></i> Filter Laporan
            </h5>
          </div>
          <hr />
          <div class="container">
            <form>
              <div class="row">
                <!-- Kolom Pertama -->
                <!--Form Puskesmas-->
                <div class="col-md-4">
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <label class="col-form-label fw-semibold mb-0">Puskesmas</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectPuskesmas">
                        <option>Wongsorejo</option>
                      </select>
                    </div>
                  </div>

                  <!--Form Tanggal Awal & Akhir-->
                  <div class="row mb-2 align-items-center">
                    <label class="col-sm-4 col-form-label fw-semibold">Tanggal Awal</label>
                    <div class="col-sm-8">
                      <input type="date" class="form-control" />
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-sm-4 col-form-label fw-semibold">Tanggal Akhir</label>
                    <div class="col-sm-8">
                      <input type="date" class="form-control" />
                    </div>
                  </div>

                  <!--Form Tempat Kunjungan-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        class="form-check-input me-2 mt-2"
                        id="cekKasus"
                        v-model="aktif.tmptKunjungan"
                      />
                      <label for="tempatKunjungan" class="col-form-label fw-semibold mb-0">
                        Tempat Kunjungan
                      </label>
                    </div>
                    <div class="col-sm-8">
                      <div class="mb-2">
                        <select
                          class="form-select"
                          id="selectTempat"
                          v-model="selectedKategori"
                          :disabled="!aktif.tmptKunjungan"
                        >
                          <option>-- Pilih Tempat --</option>
                          <option
                            v-for="kategori in tempat_kunjungan"
                            :key="kategori.id_kategori"
                            :value="kategori.id_kategori"
                          >
                            {{ kategori.kategori }}
                          </option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <select
                          id="selectUnit"
                          class="form-select"
                          v-model="selectedUnit"
                          :disabled="!selectedKategori || !aktif.tmptKunjungan"
                        >
                          <option value="">-- Pilih Unit --</option>
                          <option
                            v-for="unit in filteredUnits"
                            :key="unit.id_detail"
                            :value="unit.id_detail"
                          >
                            {{ unit.nama_unit }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!--Form Kunjungan Kasus-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        class="form-check-input me-2 mt-2"
                        id="cekKasus"
                        v-model="aktif.kasus"
                      />
                      <label for="cekKasus" class="col-form-label fw-semibold mb-0"
                        >Kunjungan Kasus</label
                      >
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectKasus" :disabled="!aktif.kasus">
                        <option>--Pilih--</option>
                        <option
                          v-for="kunjungan in kunj_kasus"
                          :key="kunjungan.id"
                          :value="kunjungan.id"
                        >
                          {{ kunjungan.kasus }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Kunjungan-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        class="form-check-input me-2 mt-2"
                        id="cekKunjungan"
                        v-model="aktif.kunjungan"
                      />
                      <label for="cekKunjungan" class="col-form-label fw-semibold">Kunjungan</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select
                        class="form-select mb-2"
                        id="selectKunjungan"
                        :disabled="!aktif.kunjungan"
                      >
                        <option>--Pilih--</option>
                        <option>Lama</option>
                        <option>Kasus Baru</option>
                      </select>
                    </div>
                  </div>
                  <hr />
                </div>

                <!-- Kolom Kedua -->
                <!--Form Nama-->
                <div class="col-md-4">
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        class="form-check-input me-2"
                        id="cekNama"
                        v-model="aktif.nama"
                      />
                      <label for="cekNama" class="col-form-label mb-0 fw-semibold">Nama</label>
                    </div>
                    <div class="col-sm-8">
                      <input
                        type="text"
                        class="form-control"
                        id="selectNama"
                        :disabled="!aktif.nama"
                      />
                    </div>
                  </div>

                  <!--Form Umur-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        class="form-check-input me-2"
                        id="cekUmur"
                        v-model="aktif.umur"
                      />
                      <label for="cekUmur" class="col-form-label fw-semibold mb-0">Umur</label>
                    </div>
                    <div class="col-auto d-flex">
                      <input
                        type="text"
                        class="form-control text-center"
                        placeholder="0"
                        style="width: 60px"
                        :disabled="!aktif.umur"
                      />
                    </div>
                    <div class="col-auto fw-semibold">s/d</div>
                    <div class="col-auto">
                      <input
                        type="text"
                        class="form-control text-center"
                        placeholder="0"
                        style="width: 60px"
                        :disabled="!aktif.umur"
                      />
                    </div>
                  </div>

                  <!--Form Jenis Kelamin-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekJK"
                        class="form-check-input me-2"
                        v-model="aktif.JK"
                      />
                      <label for="cekJK" class="col-form-label mb-0 fw-semibold"
                        >Jenis Kelamin</label
                      >
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectJK" :disabled="!aktif.JK">
                        <option>--Pilih--</option>
                        <option>Laki-Laki</option>
                        <option>Perempuan</option>
                      </select>
                    </div>
                  </div>

                  <!--Form Asal Wilayah-->
                  <div class="row mb-2">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekAsal"
                        class="form-check-input me-2"
                        v-model="aktif.asal"
                      />
                      <label class="col-form-label fw-semibold">Asal</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectAsal" :disabled="!aktif.asal">
                        <option>--Pilih--</option>
                        <option v-for="asl in asal" :key="asl.id_wilayah" :value="asl.id_wilayah">
                          {{ asl.wilayah }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Kecamatan-->
                  <div class="row mb-2">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekKecamatan"
                        class="form-check-input me-2"
                        v-model="aktif.kecamatan"
                      />
                      <label class="col-form-label fw-semibold">Kecamatan</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select
                        class="form-select"
                        id="selectKecamatan"
                        v-model="selectedKecamatan"
                        :disabled="!aktif.kecamatan"
                      >
                        <option>--Pilih--</option>
                        <option v-for="kec in kecamatan" :key="kec.NO_KEC" :value="kec.NO_KEC">
                          {{ kec.NAMA_KEC }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Desa-->
                  <div class="row mb-2">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="selectDesa"
                        class="form-check-input me-2"
                        v-model="aktif.desa"
                      />
                      <label class="col-form-label fw-semibold">Desa</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select
                        class="form-select"
                        id="selectDesa"
                        v-model="selectedDesa"
                        :disabled="!aktif.desa"
                      >
                        <option>--Pilih--</option>
                        <option
                          v-for="desa in filteredDesa"
                          :key="desa.NO_KEL"
                          :value="desa.NO_KEL"
                        >
                          {{ desa.NAMA_KEL }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Kepesertaan-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekKepesertaan"
                        class="form-check-input me-2"
                        v-model="aktif.kepesertaan"
                      />
                      <label class="col-form-label fw-semibold">Kepesertaan</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select
                        class="form-select"
                        id="selectKepesertaan"
                        :disabled="!aktif.kepesertaan"
                      >
                        <option>--Pilih--</option>
                        <option>BPJS</option>
                        <option>Non BPJS</option>
                      </select>
                    </div>
                  </div>

                  <!--Form Kategori-->
                  <div class="row mb-2">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekKategori"
                        class="form-check-input me-2"
                        v-model="aktif.kategori"
                      />
                      <label class="col-form-label fw-semibold">Kategori</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectKategori" :disabled="!aktif.kategori">
                        <option></option>
                        <option>Non BPJS</option>
                        <option>JKN PBI</option>
                        <option>Non JKN PBI</option>
                      </select>
                    </div>
                  </div>
                  <hr />
                </div>
                <!-- Kolom Ketiga -->
                <!--Form Unit-->
                <div class="col-md-4">
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekRujuk"
                        class="form-check-input me-2"
                        v-model="aktif.unit"
                      />
                      <label class="col-form-label fw-semibold">Unit</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectRujuk" :disabled="!aktif.unit">
                        <option></option>
                        <option v-for="unt in unit" :key="unt.kdPoli" :value="unt.kdPoli">
                          {{ unt.nmPoli }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Rujuk Lanjut-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekRujuk"
                        class="form-check-input me-2"
                        v-model="aktif.rujukLanjut"
                      />
                      <label class="col-form-label fw-semibold">Rujuk Lanjut</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select class="form-select" id="selectRujuk" :disabled="!aktif.rujukLanjut">
                        <option></option>
                        <option
                          v-for="provider in providers"
                          :key="provider.kdProvider"
                          :value="provider.kdProvider"
                        >
                          {{ provider.nmProvider }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!--Form Isi Diagnosa-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekIsiDiagnosa"
                        class="form-check-input me-2"
                        v-model="aktif.isiDiagnosa"
                      />
                      <label class="col-form-label fw-semibold">Isi Diagnosa</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <select
                        class="form-select"
                        id="selectIsiDiagnosa"
                        :disabled="!aktif.isiDiagnosa"
                      >
                        <option>--Pilih--</option>
                        <option>Diagnosa Kosong</option>
                      </select>
                    </div>
                  </div>

                  <!--Form Diagnosa-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekDiagnosa"
                        class="form-check-input me-2"
                        v-model="aktif.diagnosa"
                      />
                      <label class="col-form-label fw-semibold">Diagnosa</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <input
                        type="text"
                        class="form-control"
                        id="selectDiagnosa"
                        v-model="selectedTindakan"
                        @click="isModalOpen = true"
                        readonly
                        :disabled="!aktif.diagnosa"
                      />
                      <Modal
                        :show="isModalOpen"
                        title="Pilih Diagnosa"
                        :items="dataTindakan"
                        @close="isModalOpen = false"
                        @select="handleSelect"
                      />
                    </div>
                  </div>

                  <!--Form Tindakan-->
                  <div class="row mb-2 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                      <input
                        type="checkbox"
                        id="cekTindakan"
                        class="form-check-input me-2"
                        v-model="aktif.tindakan"
                      />
                      <label class="col-form-label fw-semibold">Tindakan</label>
                    </div>
                    <div class="col-sm-8 d-flex">
                      <input
                        type="text"
                        id="selectDiagnosa"
                        class="form-control"
                        readonly
                        :disabled="!aktif.tindakan"
                        v-model="selectedTindakan"
                        @click="isModalOpen = true"
                      />
                      <Modal
                        :show="isModalOpen"
                        title="Pilih Tindakan"
                        :items="dataTindakan"
                        @close="isModalOpen = false"
                        @select="handleSelect"
                      />
                    </div>
                  </div>
                  <hr />
                </div>
                <hr />
              </div>
              <div class="d-flex justify-content-start gap-2 mt-2">
                <button
                  type="button"
                  class="btn btn-data fw-semibold text-white"
                  @click="aktif.showData = !aktif.showData"
                >
                  <i class="bi bi-eye"></i>
                  {{ aktif.showData ? 'Sembunyikan Data' : 'Tampilkan Data' }}
                </button>
                <button type="button" class="btn btn-html fw-semibold text-white">
                  <i class="bi bi-filetype-html"></i>
                  Tampilkan Data HTML
                </button>
                <button type="button" class="btn btn-excel fw-semibold text-white">
                  <i class="bi bi-download"></i> Download Excel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div v-show="aktif.showData" class="card shadow-sm mt-4" @click.stop>
        <div class="card-body">
          <div class="container">
            <!-- Tambahkan table-responsive -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="text-center fw-semibold">
                  <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Tgl Kunjungan</th>
                    <th scope="col">NIK</th>
                    <th scope="col">NO RM</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Kecamatan</th>
                    <th scope="col">Desa</th>
                    <th scope="col">Sex</th>
                    <th scope="col">Tgl Lahir</th>
                    <th scope="col">Umur</th>
                    <th scope="col">Kelompok Umur</th>
                    <th scope="col">Anamnesa</th>
                    <th scope="col">Diagnosa</th>
                    <th scope="col">Obat</th>
                    <th scope="col">Tindakan/LAB</th>
                    <th scope="col">BPJS</th>
                    <th scope="col">Nama Faskes</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tujuan</th>
                    <th scope="col">Rujuk Internal</th>
                    <th scope="col">Rujuk Lanjut</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(filter, index) in rekamMedis" :key="filter.idLoket">
                    <td>{{ index + 1 }}</td>
                    <td>{{ filter.tglKunjungan }}</td>
                    <td>{{ filter.pasien?.NIK }}</td>
                    <td>{{ filter.pasien?.NO_MR }}</td>
                    <td>{{ filter.pasien?.NAMA_LGKP }}</td>
                    <td>{{ filter.pasien?.ALAMAT }}</td>
                    <td>{{ filter.pasien?.NO_KEC }}</td>
                    <td>{{ filter.pasien?.NO_KEL }}</td>
                    <td>{{ filter.pasien?.JENIS_KLMIN }}</td>
                    <td>{{ filter.pasien?.TGL_LHR }}</td>
                    <td>{{ filter.umur }}</td>
                    <td>{{ filter.kelUmur }}</td>
                    <td>
                      <div class="table-text" v-if="filter.anamnesa">
                        <p>sistole: {{ filter.anamnesa.sistole }}</p>
                        <p>diastole: {{ filter.anamnesa.diastole }}</p>
                        <p>Suhu: {{ filter.anamnesa.suhu }} °C</p>
                        <p>BB: {{ filter.anamnesa.beratBadan }} kg</p>
                        <p>TB: {{ filter.anamnesa.tinggiBadan }} cm</p>
                        <p>Lingkar Perut: {{ filter.anamnesa.lingkarPerut }} cm</p>
                        <p>IMT: {{ filter.anamnesa.imtKet }}</p>
                        <p>RespRate: {{ filter.anamnesa.respRate }} HeartRate: {{ filter.anamnesa.heartRate }} </p>
                        <p>Catatan: {{ filter.anamnesa.catatan }}</p>
                      </div>
                      <em v-else>Belum ada anamnesa</em>
                    </td>
                    <td>
                      <div class="fw-semibold table-text" v-if="filter.kunjungan">
                        <p>{{ filter.kunjungan.nmdiagnosa1 }}</p>
                        <p>{{ filter.kunjungan.nmdiagnosa2 }}</p>
                        <p>{{ filter.kunjungan.nmdiagnosa3 }}</p>
                      </div>
                      <em v-else>Belum Ada Diagnosa</em>
                    </td>
                    <td>
                      <div class="fw-semibold" v-if="filter.obat">
                        <p>{{ filter.obat.nama_obat }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="fw-semibold table-text" v-if="filter.kunjungan">
                        <p>{{ filter.kunjungan.nmtindakan1 }}</p>
                        <p>{{ filter.kunjungan.nmtindakan2 }}</p>
                        <p>{{ filter.kunjungan.nmtindakan3 }}</p>
                        <p>{{ filter.kunjungan.nmtindakan4 }}</p>
                        <p>{{ filter.kunjungan.nmtindakan5 }}</p>
                      </div>
                      <em v-else>
                        Belum ada tindakan
                      </em>
                    </td>
                  </tr>
                  <!-- data di sini -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script setup>
  import { ref, computed, watch, reactive } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import Modal from '@/Components/Layouts/Modal/Modal.vue';

  // Variabel Reactive Untuk CheckBox
  const aktif = reactive({
    tempat: false,
    kasus: false,
    kunjungan: false,
    tmptKunjungan: false,
    nama: false,
    umur: false,
    jk: false,
    asal: false,
    kecamatan: false,
    desa: false,
    kepesertaan: false,
    kategori: false,
    isiDiagnosa: false,
    diagnosa: false,
    tindakan: false,
    rujukLanjut: false,
    unit: false,
    showData: false,
  });

  // Setup Modal untuk Diagnosa dan Tindakan
  const isModalOpen = ref(false);
  const selectedTindakan = ref('');
  const dataTindakan = ref([
    {
      kode: '00.01',
      nama: 'Therapeutic ultrasound of vessels of head and neck',
      translate: 'USG Terapi pembuluh kepala dan leher',
    },
    {
      kode: '00.02',
      nama: 'Therapeutic ultrasound of heart',
      translate: 'USG Terapi hati',
    },
  ]);
  const handleSelect = (item) => {
    selectedTindakan.value = `${item.kode} - ${item.nama}`;
    openModal.value = false;
  };

  // Array Untuk Mengambil Data Pada Database
  const props = defineProps({
    providers: Array,
    tempat_kunjungan: Array,
    detail_tempat_kunjungan: Array,
    kunj_kasus: Array,
    asal: Array,
    kecamatan: Array,
    desa: Array,
    unit: Array,
    kunjungan: Array,
    Filter: Array,
    rekamMedis: Array,
  });

  // Fungsi Untuk Filter Tempat Kunjungan
  const selectedKategori = ref('');
  const selectedUnit = ref('');
  const filteredUnits = computed(() => {
    if (!selectedKategori.value) return [];

    return props.detail_tempat_kunjungan.filter(
      (unit) => unit.id_kategori == selectedKategori.value
    );
  });

  watch(selectedKategori, () => {
    selectedUnit.value = '';
  });

  // Fungsi Untuk Filter Desa & Kecamatan
  const selectedKecamatan = ref('');
  const selectedDesa = ref('');
  const filteredDesa = computed(() => {
    if (!selectedKecamatan.value) return [];

    return props.desa.filter((desa) => desa.NO_KEC == selectedKecamatan.value);
  });

  watch(selectedKecamatan, () => {
    selectedDesa.value = '';
  });
  // Opsi 2: Jika menggunakan detail_tempat_kunjungan sebagai array terpisah
  // return props.detail_tempat_kunjungan.filter(
  //   (item) => item.id_kategori === selectedKategori.value

  // Data Untuk Form
</script>
<style scoped>
  .card-header {
    background: linear-gradient(135deg, #3b82f6, #10b981);
  }
  .btn {
    background: linear-gradient(135deg, #3b82f6, #10b981);
  }
  th {
    width: inherit;
  }
  td {
    white-space: nowrap;
  }
  .table-text p {
  margin: 4px 2;       /* jarak antar baris lebih rapat */
  line-height: 1;    /* tinggi baris rapat */
  font-size: 0.9rem;   /* optional, biar lebih compact */
}
</style>
