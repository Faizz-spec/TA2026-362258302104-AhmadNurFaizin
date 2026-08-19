<template>
  <AppLayout title="Loket">
    <div class="loket-page">
      <div class="loket-shell">
        <div class="page-toolbar">
          <div>
            <p class="page-kicker">Loket pendaftaran</p>
            <h1 class="page-title">Form Loket</h1>
          </div>
          <div class="page-actions">
            <Link :href="route('loket.search')" class="btn btn-outline-success">
              <i class="bi bi-search"></i>
              <span>Cari Pasien</span>
            </Link>
            <Link :href="route('loket.pasien')" class="btn btn-success">
              <i class="bi bi-plus-lg"></i>
              <span>Tambah Pasien</span>
            </Link>
          </div>
        </div>

        <div class="loket-card mb-4">
          <div class="loket-card-header">
            <div>
              <h2><i class="bi bi-clipboard2-pulse"></i> Pendaftaran Kunjungan</h2>
              <p>Lengkapi data pasien, unit pelayanan, dan detail kunjungan.</p>
            </div>
            <span class="status-pill" :class="{ complete: formValid }">
              {{ formValid ? 'Siap disimpan' : 'Belum lengkap' }}
            </span>
          </div>

          <div class="loket-card-body">
            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-search"></i>
                <div>
                  <h3>Pencarian Pasien</h3>
                  <p>Cari cepat berdasarkan salah satu identitas pasien.</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">No. MR</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      v-model="searchParams.no_mr"
                      @keyup.enter="searchPasien('no_mr')"
                      placeholder="Masukkan No. MR"
                    />
                    <button class="btn btn-soft-primary" @click="searchPasien('no_mr')">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label">NIK</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      v-model="searchParams.nik"
                      @keyup.enter="searchPasien('nik')"
                      placeholder="Masukkan NIK"
                    />
                    <button class="btn btn-soft-primary" @click="searchPasien('nik')">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label">No. BPJS</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      v-model="searchParams.no_bpjs"
                      @keyup.enter="searchPasien('no_bpjs')"
                      placeholder="Masukkan No. BPJS"
                    />
                    <button class="btn btn-soft-primary" @click="searchPasien('no_bpjs')">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-person-vcard"></i>
                <div>
                  <h3>Data Pasien</h3>
                  <p>Data utama pasien terisi setelah pencarian berhasil.</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">Nama</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      v-model="selectedPasien.NAMA_LGKP"
                      disabled
                      readonly
                    />
                    <button class="btn btn-outline-danger" @click="openSearchModal">
                      <i class="bi bi-search"></i>
                      <span>Cari</span>
                    </button>
                  </div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Tanggal Kunjungan</label>
                  <input type="date" class="form-control" v-model="form.tglKunjungan" />
                </div>
                <div class="col-12">
                  <label class="form-label">Alamat <span class="required">*</span></label>
                  <textarea
                    class="form-control"
                    rows="2"
                    :value="formatKecamatanKelurahan"
                    disabled
                    readonly
                  ></textarea>
                </div>
              </div>
            </section>

            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-building"></i>
                <div>
                  <h3>Unit & Wilayah</h3>
                  <p>Tentukan layanan dan wilayah pendaftaran pasien.</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">Kategori Unit <span class="required">*</span></label>
                  <select
                    class="form-select"
                    v-model="form.kategoriUnitId"
                    @change="loadUnitList"
                    :disabled="loading.kategoriUnit"
                  >
                    <option value="">- Pilih Kategori Unit -</option>
                    <option v-for="(name, id) in kategoriUnits" :key="id" :value="id">
                      {{ name }}
                    </option>
                  </select>
                  <div v-if="loading.kategoriUnit" class="field-hint">
                    <i class="bi bi-arrow-repeat spinner"></i> Memuat kategori unit...
                  </div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Unit <span class="required">*</span></label>
                  <select
                    class="form-select"
                    v-model="form.unitId"
                    :disabled="!form.kategoriUnitId || loading.unitList"
                  >
                    <option value="">- Pilih Unit -</option>
                    <option v-for="(name, id) in unitList" :key="id" :value="id">
                      {{ name }}
                    </option>
                  </select>
                  <div v-if="loading.unitList" class="field-hint">
                    <i class="bi bi-arrow-repeat spinner"></i> Memuat daftar unit...
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Wilayah <span class="required">*</span></label>
                  <select class="form-select highlighted-input" v-model="form.wilayah">
                    <option value="">- Pilih Wilayah -</option>
                    <template v-for="(name, id) in wilayahList" :key="id">
                      <option v-if="id !== '0'" :value="id">
                        {{ name }}
                      </option>
                    </template>
                  </select>
                  <div
                    v-if="autoDetectedWilayah && form.wilayah === autoDetectedWilayah"
                    class="field-hint success"
                  >
                    <i class="bi bi-check-circle-fill"></i> Terdeteksi otomatis
                  </div>
                  <div v-else-if="form.wilayah" class="field-hint info">
                    <i class="bi bi-pencil-fill"></i> Dipilih manual
                  </div>
                </div>
              </div>
            </section>

            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-shield-check"></i>
                <div>
                  <h3>Status BPJS</h3>
                  <p>Informasi kartu dan peserta untuk pasien terpilih.</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-lg-4">
                  <label class="form-label">Status Kartu</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      v-model="selectedPasien.statusKartu"
                      disabled
                      readonly
                    />
                    <button class="btn btn-soft-primary" @click="checkCardStatus">
                      <i class="bi bi-check-circle"></i>
                    </button>
                  </div>
                </div>
                <div class="col-lg-4">
                  <label class="form-label">Provider Kartu</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="selectedPasien.kdProvider"
                    disabled
                    readonly
                  />
                </div>
                <div class="col-lg-4">
                  <label class="form-label">Jenis Peserta</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="selectedPasien.jenisPeserta"
                    disabled
                    readonly
                  />
                </div>
              </div>
            </section>

            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-calendar2-check"></i>
                <div>
                  <h3>Detail Kunjungan</h3>
                  <p>Atur kategori kunjungan, kegiatan, dan kontak pasien.</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">Jenis Pengunjung <span class="required">*</span></label>
                  <select class="form-select highlighted-input" v-model="jenisPengunjung">
                    <option value="Pengunjung Baru">Pengunjung Baru</option>
                    <option value="Pengunjung Lama">Pengunjung Lama</option>
                  </select>
                  <div v-if="jenisPengunjungAuto === 'Pengunjung Lama'" class="field-hint warning">
                    <i class="bi bi-info-circle-fill"></i>
                    Pasien sudah pernah daftar di {{ currentMonthYear }}
                  </div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Kategori <span class="required">*</span></label>
                  <select class="form-select highlighted-input" v-model="kategori">
                    <option value="NON_BPJS">NON BPJS</option>
                    <option value="JKN_PBI">JKN PBI</option>
                    <option value="JKN_NON_PBI">JKN NON PBI</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Jenis Kunjungan</label>
                  <select class="form-select" v-model="jenisKunjungan" @change="updatePoliOptions">
                    <option value="Kunjungan Sakit">Kunjungan Sakit</option>
                    <option value="Kunjungan Sehat">Kunjungan Sehat</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Kegiatan</label>
                  <select class="form-select" v-model="form.kdPoli">
                    <option value="">- Pilih Kegiatan -</option>
                    <option
                      v-for="poli in filteredPoliList"
                      :key="poli.kdPoli"
                      :value="poli.kdPoli"
                    >
                      {{ poli.nmPoli }}
                    </option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Sub Kegiatan</label>
                  <select class="form-select" disabled>
                    <option value="">- Pilih Sub Kegiatan -</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Kode TKP</label>
                  <select class="form-select" v-model="kodeTKP">
                    <option value="RJTP (RAWAT JALAN)">RJTP (RAWAT JALAN)</option>
                    <option value="RJTL (RAWAT JALAN LANJUTAN)">RJTL (RAWAT JALAN LANJUTAN)</option>
                    <option value="Promotif">Promotif</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">No HP</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.PHONE"
                    placeholder="Masukkan nomor HP"
                  />
                </div>
              </div>
            </section>
          </div>

          <div class="form-footer">
            <button
              class="btn btn-outline-secondary"
              @click="showRiwayatKunjungan"
              :disabled="!selectedPasien.ID"
            >
              <i class="bi bi-clock-history"></i>
              <span>Riwayat Kunjungan</span>
            </button>
            <button
              class="btn btn-success btn-save"
              @click="registerPasien"
              :disabled="!selectedPasien.ID || !formValid"
            >
              <i class="bi bi-check-circle"></i>
              <span>Simpan</span>
            </button>
          </div>
        </div>

        <div
          v-if="searchResultModal.show"
          class="custom-modal d-flex justify-content-center align-items-center"
          @click.self="closeSearchResultModal"
        >
          <div class="modal-content-custom text-center">
            <template v-if="searchResultModal.status === 'success'">
              <div class="success-icon mb-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h5 class="fw-bold mt-3">Berhasil</h5>
              <p class="text-muted mb-0">Pemeriksaan dimulai</p>
            </template>
            <template v-else>
              <div class="error-icon mb-3">
                <i class="bi bi-x-circle-fill"></i>
              </div>
              <h5 class="fw-bold mt-3">Gagal</h5>
              <p class="text-muted mb-0">{{ searchResultModal.message }}</p>
            </template>
          </div>
        </div>

        <div class="loket-card">
          <div class="loket-card-header table-header">
            <div>
              <h2><i class="bi bi-list-check"></i> Daftar Loket</h2>
              <p>Daftar pasien yang sudah terdaftar di loket.</p>
            </div>
            <button class="btn btn-light btn-sm">
              <i class="bi bi-cloud-check"></i>
              <span>Cek Pendaftaran Pcare</span>
            </button>
          </div>

          <div class="table-tools">
            <div class="entries-control">
              <span>Show</span>
              <select class="form-select form-select-sm" v-model="perPage">
                <option>10</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
              </select>
              <span>entries</span>
            </div>

            <div class="search-control">
              <i class="bi bi-search"></i>
              <input
                type="text"
                class="form-control form-control-sm"
                placeholder="Cari..."
                v-model="searchQuery"
              />
            </div>
          </div>

          <div class="table-responsive">
            <table class="table loket-table mb-0">
              <thead>
                <tr>
                  <th>Tanggal<br />No. Urut<br />(pcare)</th>
                  <th>No. Antrian<br />Poli</th>
                  <th>No. MR<br />Nama (Umur)<br />NIK</th>
                  <th>Alamat<br />Kecamatan-Desa</th>
                  <th>No. BPJS<br />Status</th>
                  <th>Poli</th>
                  <th>Unit<br />Sub Unit</th>
                  <th>Action<br />Pcare<br />Kategori</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <template v-if="loket.data.length > 0">
                  <tr v-for="item in loket.data" :key="item.idLoket">
                    <td>
                      <strong>{{ formatDate(item.tglKunjungan) }}</strong>
                      <span v-if="item.noPcare" class="table-muted">({{ item.noPcare }})</span>
                    </td>
                    <td>{{ item.noAntrian || `A${item.noUrut?.padStart(3, '0')}` }}</td>
                    <td>
                      <strong>{{ item.NO_MR }}</strong
                      ><br />
                      {{ item.NAMA_LGKP }} ({{ item.umur_tahun || '-' }} th)<br />
                      <span class="table-muted">{{ item.NIK }}</span>
                    </td>
                    <td>
                      {{ formatAlamatUntukTabel(item).rtRw }}<br />
                      <span class="table-muted">
                        {{ item.nama_kecamatan || '-' }} - {{ item.nama_kelurahan || '-' }}
                      </span>
                    </td>
                    <td>
                      {{ item.noKartu || '-' }}<br />
                      <span class="table-muted">{{ item.statusKartu || '-' }}</span>
                    </td>
                    <td>{{ item.nmPoli }}</td>
                    <td>
                      {{ item.kategori_unit || '-' }}<br />
                      <span class="table-muted">{{ item.nama_unit || '-' }}</span>
                    </td>
                    <td>
                      {{ item.action || '-' }}<br />
                      {{ item.noPcare || '-' }}<br />
                      <span class="table-muted">{{ item.kategori || '-' }}</span>
                    </td>
                    <td class="text-center">
                      <div class="action-buttons">
                        <button class="btn btn-icon btn-outline-info" title="Lihat Detail">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button
                          class="btn btn-icon btn-outline-success"
                          title="Cetak Kartu Pasien"
                          @click="cetakKartuPasien(item.pasien_id)"
                          :disabled="!item.pasien_id"
                        >
                          <i class="bi bi-printer"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="9" class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <span>Belum ada data loket.</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <nav aria-label="Page navigation" class="pagination-wrap">
            <ul class="pagination justify-content-center mb-0">
              <li
                v-for="link in loket.links"
                :key="link.label"
                class="page-item"
                :class="{ active: link.active, disabled: !link.url }"
              >
                <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import { Link, useForm, router } from '@inertiajs/vue3';
  import { ref, defineProps, watch, onMounted, computed } from 'vue';

  const props = defineProps({
    loket: {
      type: Object,
      default: () => ({ data: [], links: [] }),
    },
    poliList: {
      type: Array,
      default: () => [],
    },
    kategoriUnits: {
      type: Object,
      default: () => ({}),
    },
    wilayah: {
      type: Object,
      default: () => ({}),
    },
  });

  // Data untuk master data
  const kategoriUnits = ref({});
  const unitList = ref({});
  const wilayahList = ref({});
  const masterKecamatan = ref({});
  const masterKelurahan = ref({});

  // Data tambahan untuk fitur otomatis
  const autoDetectedStatus = ref('');
  const autoDetectedWilayah = ref('');

  // Loading states
  const loading = ref({
    kategoriUnit: false,
    unitList: false,
    wilayah: false,
  });

  // Data pencarian
  const searchParams = ref({
    no_mr: '',
    nik: '',
    no_bpjs: '',
  });

  const searchResultModal = ref({
    show: false,
    status: '',
    message: '',
  });

  const autoCloseTimer = ref(null);

  // Data pasien yang dipilih
  const selectedPasien = ref({
    ID: '',
    NO_MR: '',
    NIK: '',
    NAMA_LGKP: '',
    ALAMAT: '',
    noKartu: '',
    statusKartu: '',
    kdProvider: '',
    jenisPeserta: '',
    PHONE: '',
    TGL_LHR: '',
    kecamatan: null,
    kelurahan: null,
    NO_KEC: '',
    NO_KEL: '',
    NO_RT: '',
    NO_RW: '',
  });

  // Form pendaftaran
  const form = useForm({
    pasienId: '',
    tglKunjungan: new Date().toISOString().split('T')[0],
    kategoriUnitId: '',
    unitId: '',
    PHONE: '',
    wilayah: '',
    kunjBaru: 'true',
    jknPbi: 'NON_BPJS',
    kunjSakit: 'true',
    kdPoli: '',
    kdTkp: '10',
    noUrut: '',
    kelUmur: 1,
    umur: 30,
    statusPasien: 'umum',
    createdBy: 'test_user',
    kdProvider: '',
    statusKartu: '',
    noKartu: '',
    kdKegiatan: 1,
    puskId: 1,
    TGL_LHR: '',
  });

  // Computed properties untuk mapping UI ke database
  const jenisPengunjungAuto = computed(() => {
    if (!selectedPasien.value.ID || !form.tglKunjungan) {
      return 'Pengunjung Baru';
    }

    return autoDetectedStatus.value || 'Pengunjung Baru';
  });

  const currentMonthYear = computed(() => {
    if (!form.tglKunjungan) return '';
    const date = new Date(form.tglKunjungan);
    return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
  });

  // Computed untuk jenisPengunjung
  const jenisPengunjung = computed({
    get: () => {
      return form.kunjBaru === 'true' ? 'Pengunjung Baru' : 'Pengunjung Lama';
    },
    set: (value) => {
      form.kunjBaru = value === 'Pengunjung Baru' ? 'true' : 'false';
    },
  });

  const jenisKunjungan = computed({
    get: () => (form.kunjSakit === 'true' ? 'Kunjungan Sakit' : 'Kunjungan Sehat'),
    set: (value) => {
      form.kunjSakit = value === 'Kunjungan Sakit' ? 'true' : 'false';
    },
  });

  const kategori = computed({
    get: () => form.jknPbi,
    set: (value) => {
      form.jknPbi = value;
    },
  });

  // COMPUTED UNTUK KODE TKP DENGAN DEFAULT
  const kodeTKP = computed({
    get: () => {
      const mappings = {
        10: 'RJTP (RAWAT JALAN)',
        20: 'RJTL (RAWAT JALAN LANJUTAN)',
        50: 'Promotif',
      };
      return mappings[form.kdTkp] || 'RJTP (RAWAT JALAN)';
    },
    set: (value) => {
      const mappings = {
        'RJTP (RAWAT JALAN)': '10',
        'RJTL (RAWAT JALAN LANJUTAN)': '20',
        Promotif: '50',
      };
      form.kdTkp = mappings[value] || '10';
    },
  });

  // Data untuk menyimpan daftar poli
  const filteredPoliList = ref([]);

  // Pencarian tabel
  const searchQuery = ref('');
  const perPage = ref(10);

  // Computed property untuk validasi form
  const formValid = computed(() => {
    return selectedPasien.value.ID && form.kategoriUnitId && form.unitId && form.wilayah;
  });

  // ========== METHOD OTOMATIS ==========

  // Method untuk cek status otomatis dari backend
  const checkAutoStatus = async () => {
    if (!selectedPasien.value.ID || !form.tglKunjungan) {
      return;
    }

    try {
      const params = new URLSearchParams({
        pasienId: selectedPasien.value.ID,
        tglKunjungan: form.tglKunjungan,
      });

      const response = await fetch(
        route('loket.api.check-jenis-pengunjung') + '?' + params.toString()
      );

      if (response.ok) {
        const data = await response.json();
        autoDetectedStatus.value = data.status;

        // Set nilai otomatis berdasarkan deteksi sistem
        form.kunjBaru = data.status === 'Pengunjung Baru' ? 'true' : 'false';
      }
    } catch (error) {
      console.error('Error cek status pengunjung:', error);
      autoDetectedStatus.value = 'Pengunjung Baru';
      form.kunjBaru = 'true';
    }
  };

  // checkAutoWilayah dengan error handling yang proper
  const checkAutoWilayah = async () => {
    if (!selectedPasien.value.ID) {
      return;
    }

    console.log('Memeriksa wilayah otomatis untuk pasien:', selectedPasien.value.ID);

    try {
      const params = new URLSearchParams({
        pasienId: selectedPasien.value.ID,
      });

      const response = await fetch(route('loket.api.wilayah-otomatis') + '?' + params.toString());

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();

      // VALIDASI RESPONSE
      if (data.error) {
        throw new Error(data.message || `Error: ${data.error}`);
      }

      if (!data.wilayah) {
        throw new Error('Server tidak mengembalikan data wilayah');
      }

      // SET DATA JIKA SEMUA VALID
      autoDetectedWilayah.value = data.wilayah;

      if (!form.wilayah) {
        form.wilayah = data.wilayah;
        console.log('Wilayah otomatis berhasil:', data.wilayah);
      } else {
        console.log('Wilayah terdeteksi:', data.wilayah, '(tetap menggunakan pilihan manual)');
      }
    } catch (error) {
      console.error('Error deteksi wilayah otomatis:', error.message);

      // TAMPILKAN PESAN ERROR YANG JELAS
      const errorMessage =
        `Gagal mendeteksi wilayah otomatis ${error.message} Silakan pilih wilayah secara manual dari dropdown.`.trim();

      alert(errorMessage);
    }
  };

  // Fungsi untuk memuat daftar unit berdasarkan kategori
  const loadUnitList = async () => {
    if (!form.kategoriUnitId) {
      unitList.value = {};
      form.unitId = '';
      return;
    }

    loading.value.unitList = true;
    try {
      const response = await fetch(route('loket.api.unit-list', form.kategoriUnitId));
      if (response.ok) {
        const data = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(data, 'text/html');
        const options = doc.querySelectorAll('option');

        unitList.value = {};
        options.forEach((option) => {
          if (option.value) {
            unitList.value[option.value] = option.textContent;
          }
        });
      } else {
        console.error('Gagal memuat daftar unit');
      }
    } catch (error) {
      console.error('Error memuat unit:', error);
    } finally {
      loading.value.unitList = false;
    }
  };

  // Fungsi untuk memuat data master saat komponen dimuat
  const loadMasterData = async () => {
    kategoriUnits.value = props.kategoriUnits || {};
    wilayahList.value = props.wilayah || {};

    if (Object.keys(kategoriUnits.value).length === 0) {
      loading.value.kategoriUnit = true;
      try {
        const response = await fetch(route('loket.api.kategori-unit'));
        if (response.ok) {
          kategoriUnits.value = await response.json();
        }
      } catch (error) {
        console.error('Error memuat kategori unit:', error);
      } finally {
        loading.value.kategoriUnit = false;
      }
    }

    if (Object.keys(wilayahList.value).length === 0) {
      loading.value.wilayah = true;
      try {
        const response = await fetch(route('loket.api.wilayah'));
        if (response.ok) {
          wilayahList.value = await response.json();
        }
      } catch (error) {
        console.error('Error memuat wilayah:', error);
      } finally {
        loading.value.wilayah = false;
      }
    }
  };

  // Fungsi untuk memperbarui opsi poli berdasarkan jenis kunjungan
  const updatePoliOptions = async () => {
    try {
      const params = new URLSearchParams({
        jenisKunjungan: jenisKunjungan.value,
      });

      const response = await fetch(route('loket.api.poli-by-jenis') + '?' + params.toString(), {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (response.ok) {
        filteredPoliList.value = await response.json();
      } else {
        console.error('Gagal mengambil data poli');
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  // Fungsi untuk load master data wilayah
  const loadMasterWilayah = async () => {
    try {
      // Load kecamatan untuk Banyuwangi (NO_PROP=35, NO_KAB=10)
      const responseKecamatan = await fetch(
        route('loket.api.kecamatan') + '?propinsi=35&kabupaten=10'
      );
      if (responseKecamatan.ok) {
        const dataKecamatan = await responseKecamatan.json();
        // Convert array to object for easy lookup
        masterKecamatan.value = {};
        dataKecamatan.forEach((kec) => {
          masterKecamatan.value[kec.NO_KEC] = kec.NAMA_KEC;
        });
      }

      // Load kelurahan akan dilakukan on-demand saat ada data pasien
    } catch (error) {
      console.error('Error loading master wilayah:', error);
    }
  };

  // Mapping kecamatan & kelurahan kode ke nama
  const formatKecamatanKelurahan = computed(() => {
    const pasien = selectedPasien.value;

    if (pasien.nama_kecamatan && pasien.nama_kelurahan) {
      return `${pasien.nama_kecamatan} - ${pasien.nama_kelurahan}`;
    }

    return `${pasien.NO_KEC} ${pasien.NO_KEL}`;
  });

  const formatAlamatUntukTabel = (item) => {
    if (!item) return { rtRw: '', wilayah: '' };

    // PERBAIKI: Akses langsung dari item (bukan item.pasien)
    const rtRw = item.NO_RT || item.NO_RW ? `RT ${item.NO_RT || '?'}/RW ${item.NO_RW || '?'}` : '';

    const wilayah =
      item.nama_kecamatan && item.nama_kelurahan
        ? `${item.nama_kecamatan} - ${item.nama_kelurahan}`
        : `Kec.${item.NO_KEC || '?'} Kel.${item.NO_KEL || '?'}`;

    return { rtRw, wilayah };
  };

  const cetakKartuPasien = (pasienId) => {
    if (pasienId) {
      // Navigasi ke halaman cetak kartu di window yang sama
      router.visit(route('loket.cetak_kartu', pasienId));
    } else {
      alert('Data pasien tidak tersedia untuk dicetak');
    }
  };

  // const debugUnitDetailContent = () => {
  //   if (props.loket.data.length > 0) {
  //     const item = props.loket.data[0];
  //     console.log('=== DEBUG UNIT_DETAIL CONTENT ===');
  //     console.log('unit_detail object:', item.unit_detail);
  //     console.log('Keys in unit_detail:', Object.keys(item.unit_detail || {}));
  //     console.log('master_unit:', item.unit_detail?.master_unit);
  //     console.log('Keys in master_unit:', Object.keys(item.unit_detail?.master_unit || {}));
  //     console.log('nama_unit:', item.unit_detail?.nama_unit);
  //     console.log('id_kategori:', item.unit_detail?.id_kategori);
  //   }
  // };

  // Fungsi pencarian pasien
  const searchPasien = async (type) => {
    try {
      if (!searchParams.value[type]) {
        alert('Harap isi field yang ingin dicari');
        return;
      }

      const params = new URLSearchParams();
      params.append(type, searchParams.value[type]);

      const url = route('api.pasien.search') + '?' + params.toString();
      const response = await fetch(url);

      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('Pasien tidak ditemukan');
        }
        throw new Error('Terjadi kesalahan saat mencari pasien');
      }

      const data = await response.json();

      // Isi data pasien yang ditemukan ke field yang sesuai
      selectedPasien.value = {
        ...selectedPasien.value,
        ...data,
        ID: data.ID || '',
        NO_MR: data.NO_MR || '',
        NIK: data.NIK || '',
        NAMA_LGKP: data.NAMA_LGKP || '',
        ALAMAT: data.ALAMAT || '',
        noKartu: data.noKartu || '',
        PHONE: data.PHONE || '',
        TGL_LHR: data.TGL_LHR || '',
        kecamatan: data.kecamatan || null,
        kelurahan: data.kelurahan || null,
        NO_KEC: data.NO_KEC || '',
        NO_KEL: data.NO_KEL || '',
      };

      showSearchResultModal(
        'success',
        `Pasien ditemukan: ${data.NAMA_LGKP || data.NO_MR || data.NIK}`
      );

      if (data.NO_KEC && data.NO_KEL) {
        await loadKelurahanByKecamatan(data.NO_KEC);
      }

      // Mengisi field pencarian dengan data yang ditemukan
      searchParams.value.no_mr = data.NO_MR || '';
      searchParams.value.nik = data.NIK || '';
      searchParams.value.no_bpjs = data.noKartu || '';

      form.pasienId = data.ID;
      form.PHONE = data.PHONE || '';
      form.TGL_LHR = data.TGL_LHR || '';

      // Set nilai untuk field required
      form.kunjSakit = jenisKunjungan.value === 'Kunjungan Sakit';
      form.kunjBaru = jenisPengunjung.value === 'Pengunjung Baru';
      form.jknPbi = kategori.value;
      form.kdProvider = kategori.value === 'BPJS' ? 'BPJS' : '';
      form.statusKartu = kategori.value === 'BPJS' ? 'Aktif' : '';
      form.noKartu = kategori.value === 'BPJS' ? data.noKartu || '' : '';

      // Cek status otomatis setelah data pasien tersedia
      await checkAutoStatus();
      await checkAutoWilayah();
    } catch (error) {
      console.error('Error:', error);
      showSearchResultModal('error', error.message || 'Pasien tidak ditemukan');

      // Reset data pasien jika tidak ditemukan
      selectedPasien.value = {
        ID: '',
        NO_MR: '',
        NIK: '',
        NAMA_LGKP: '',
        ALAMAT: '',
        noKartu: '',
        statusKartu: '',
        kdProvider: '',
        jenisPeserta: '',
        PHONE: '',
        TGL_LHR: '',
        kecamatan: null,
        kelurahan: null,
        NO_KEC: '',
        NO_KEL: '',
        NO_RT: '',
        NO_RW: '',
      };

      form.pasienId = '';
      form.PHONE = '';
      form.TGL_LHR = '';
    }
  };

  const showSearchResultModal = (status, message) => {
    searchResultModal.value = {
      show: true,
      status,
      message,
    };

    if (autoCloseTimer.value) {
      clearTimeout(autoCloseTimer.value);
      autoCloseTimer.value = null;
    }

    if (status === 'success') {
      autoCloseTimer.value = setTimeout(() => {
        closeSearchResultModal();
      }, 1600);
    }
  };

  const closeSearchResultModal = () => {
    searchResultModal.value.show = false;
    if (autoCloseTimer.value) {
      clearTimeout(autoCloseTimer.value);
      autoCloseTimer.value = null;
    }
  };

  // Fungsi untuk load kelurahan berdasarkan kecamatan
  const loadKelurahanByKecamatan = async (kodeKecamatan) => {
    try {
      const response = await fetch(
        route('loket.api.kelurahan') +
          '?kecamatan=' +
          kodeKecamatan +
          '&propinsi=' +
          selectedPasien.value.NO_PROP +
          '&kabupaten=' +
          selectedPasien.value.NO_KAB
      );

      if (response.ok) {
        const dataKelurahan = await response.json();
        masterKelurahan.value[kodeKecamatan] = {};
        dataKelurahan.forEach((kel) => {
          masterKelurahan.value[kodeKecamatan][kel.NO_KEL] = kel.NAMA_KEL;
        });
      }
    } catch (error) {
      console.error('Error loading kelurahan:', error);
    }
  };

  // Fungsi pendaftaran
  const registerPasien = () => {
    if (!selectedPasien.value.ID) {
      alert('Silakan cari pasien terlebih dahulu');
      return;
    }

    if (!formValid.value) {
      alert('Harap lengkapi semua field yang wajib diisi (Kategori Unit, Unit, dan Wilayah)');
      return;
    }

    form.post(route('loket.simpan'), {
      preserveScroll: true,
      onSuccess: (page) => {
        // Handle success message dari Inertia
        if (page.props.success) {
          alert(page.props.success);
        }

        // Reset form setelah berhasil
        resetForm();
      },
      onError: (errors) => {
        console.error('Errors:', errors);
        if (errors.message) {
          alert('Terjadi kesalahan: ' + errors.message);
        } else {
          alert('Terjadi kesalahan. Silakan coba lagi.');
        }
      },
      onFinish: () => {},
    });
  };

  // Fungsi reset form yang komprehensif
  const resetForm = () => {
    // Reset selected pasien
    selectedPasien.value = {
      ID: '',
      NO_MR: '',
      NIK: '',
      NAMA_LGKP: '',
      ALAMAT: '',
      noKartu: '',
      statusKartu: '',
      kdProvider: '',
      jenisPeserta: '',
      PHONE: '',
      TGL_LHR: '',
      kecamatan: null,
      kelurahan: null,
      NO_KEC: '',
      NO_KEL: '',
      NO_RT: '',
      NO_RW: '',
    };

    // Reset search params
    searchParams.value = {
      no_mr: '',
      nik: '',
      no_bpjs: '',
    };

    // Reset form fields
    form.reset();

    // Set default values
    form.tglKunjungan = new Date().toISOString().split('T')[0];
    form.kunjSakit = 'true';
    form.kunjBaru = 'true';
    form.jknPbi = 'NON_BPJS';
    form.kategoriUnitId = '';
    form.unitId = '';
    form.wilayah = '';
    form.kdTkp = '10';
    form.kdPoli = '';
    form.PHONE = '';
    form.TGL_LHR = '';
    form.kelUmur = 1;
    form.umur = 30;

    // Reset computed values
    autoDetectedStatus.value = '';
    autoDetectedWilayah.value = '';
    jenisPengunjung.value = 'Pengunjung Baru';
    jenisKunjungan.value = 'Kunjungan Sakit';
    kategori.value = 'NON_BPJS';
    kodeTKP.value = 'RJTP (RAWAT JALAN)';

    // Reset unit list
    unitList.value = {};
  };

  // Fungsi untuk menampilkan riwayat kunjungan
  const showRiwayatKunjungan = () => {
    if (!selectedPasien.value.ID) {
      alert('Silakan pilih pasien terlebih dahulu');
      return;
    }
    alert('Fitur riwayat kunjungan akan segera tersedia');
  };

  // Format tanggal
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID');
  };

  // Fungsi cek status kartu
  const checkCardStatus = () => {
    if (!selectedPasien.value.noKartu) {
      alert('Pasien tidak memiliki nomor BPJS');
      return;
    }

    // Simulasi cek status kartu
    selectedPasien.value.statusKartu = 'Aktif';
    selectedPasien.value.kdProvider = 'BPJS Kesehatan';
    selectedPasien.value.jenisPeserta = 'PBI';
  };

  // Fungsi buka modal pencarian
  const openSearchModal = () => {
    router.visit(route('loket.search'));
  };

  // ========== WATCH FUNCTIONS ==========

  // Watch perubahan pada computed properties
  watch(jenisPengunjung, (newValue) => {
    form.kunjBaru = newValue === 'Pengunjung Baru' ? 'true' : 'false';
  });

  watch(jenisKunjungan, (newValue) => {
    form.kunjSakit = newValue === 'Kunjungan Sakit' ? 'true' : 'false';
    updatePoliOptions();
    form.kdPoli = '';
  });

  watch(kategori, (newValue) => {
    form.jknPbi = newValue;
    form.kdProvider = newValue === 'BPJS' ? 'BPJS' : '';
    form.statusKartu = newValue === 'BPJS' ? 'Aktif' : '';

    if (newValue === 'BPJS' && selectedPasien.value.noKartu) {
      form.noKartu = selectedPasien.value.noKartu;
    } else {
      form.noKartu = '';
    }
  });

  // Watch untuk perubahan pasien
  watch(
    () => selectedPasien.value.ID,
    (newId, oldId) => {
      if (newId && newId !== oldId) {
        checkAutoStatus();
        checkAutoWilayah();
      }
    }
  );

  // Watch untuk tanggal kunjungan (hanya pengunjung)
  watch(
    () => form.tglKunjungan,
    (newDate, oldDate) => {
      if (newDate && newDate !== oldDate && selectedPasien.value.ID) {
        checkAutoStatus();
      }
    }
  );

  // Inisialisasi data saat komponen dimuat
  onMounted(() => {
    filteredPoliList.value = props.poliList;
    loadMasterData();
    loadMasterWilayah();
    // debugUnitDetailContent();
    console.log('=== DEBUG LOKET DATA STRUCTURE ===');
    if (props.loket.data.length > 0) {
      console.log('Sample item:', props.loket.data[0]);
      console.log('Available keys:', Object.keys(props.loket.data[0]));
    }

    // Set initial values untuk computed properties
    jenisPengunjung.value = 'Pengunjung Baru';
    jenisKunjungan.value = 'Kunjungan Sakit';
    kategori.value = 'NON_BPJS';
    kodeTKP.value = 'RJTP (RAWAT JALAN)';
  });
</script>

<style scoped>
  .loket-page {
    min-height: 100%;
    background: #f4f7fb;
    padding: 24px;
  }

  .loket-shell {
    max-width: 1480px;
    margin: 0 auto;
  }

  .page-toolbar,
  .loket-card-header,
  .table-tools,
  .form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .page-toolbar {
    margin-bottom: 18px;
  }

  .page-kicker {
    margin: 0 0 4px;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .page-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.65rem;
    font-weight: 750;
  }

  .page-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .loket-card {
    overflow: hidden;
    border: 1px solid #d9e5df;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
  }

  .loket-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e5edf0;
    background: #ffffff;
  }

  .loket-card-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: #0f3d2e;
    font-size: 1.05rem;
    font-weight: 750;
  }

  .loket-card-header p {
    margin: 5px 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }

  .table-header {
    background: #0f6b4c;
  }

  .table-header h2,
  .table-header p {
    color: #ffffff;
  }

  .table-header p {
    opacity: 0.82;
  }

  .loket-card-body {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    padding: 22px;
  }

  .form-section {
    padding: 18px;
    border: 1px solid #e3ebef;
    border-radius: 8px;
    background: #fbfdff;
  }

  .form-section:first-child,
  .form-section:last-child {
    grid-column: 1 / -1;
  }

  .section-heading {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
  }

  .section-heading > i {
    display: grid;
    flex: 0 0 36px;
    width: 36px;
    height: 36px;
    place-items: center;
    border-radius: 8px;
    background: #e7f5ef;
    color: #08734f;
    font-size: 1.05rem;
  }

  .section-heading h3 {
    margin: 0;
    color: #1e293b;
    font-size: 0.98rem;
    font-weight: 750;
  }

  .section-heading p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 0.84rem;
  }

  .form-label {
    margin-bottom: 6px;
    font-weight: 600;
    color: #334155;
    font-size: 0.86rem;
  }

  .form-control,
  .form-select {
    border-radius: 8px;
    border: 1px solid #cfd9e3;
    color: #0f172a;
    min-height: 42px;
    transition:
      border-color 0.15s ease-in-out,
      box-shadow 0.15s ease-in-out;
  }

  textarea.form-control {
    min-height: 74px;
    resize: none;
  }

  .form-control:disabled,
  .form-select:disabled {
    background-color: #f1f5f9;
    color: #64748b;
    opacity: 1;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #16a36f;
    box-shadow: 0 0 0 0.2rem rgba(22, 163, 111, 0.14);
  }

  .highlighted-input {
    background-color: #f6fbf8;
  }

  .btn {
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 650;
    transition: all 0.15s ease-in-out;
  }

  .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  }

  .btn-soft-primary {
    border-color: #bfdbea;
    background: #edf8fc;
    color: #09637b;
  }

  .btn-save {
    min-width: 150px;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #fff7ed;
    color: #9a3412;
    font-size: 0.8rem;
    font-weight: 750;
  }

  .status-pill.complete {
    background: #dcfce7;
    color: #166534;
  }

  .required {
    color: #dc2626;
    font-weight: 800;
  }

  .field-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    color: #64748b;
    font-size: 0.82rem;
    font-weight: 650;
  }

  .field-hint.success {
    color: #15803d;
  }

  .field-hint.info {
    color: #0369a1;
  }

  .field-hint.warning {
    color: #a16207;
  }

  .form-footer {
    justify-content: flex-end;
    padding: 18px 22px;
    border-top: 1px solid #e5edf0;
    background: #f8fafc;
  }

  .table-tools {
    padding: 14px 18px;
    border-bottom: 1px solid #e5edf0;
    background: #f8fafc;
  }

  .entries-control,
  .search-control {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #475569;
    font-size: 0.87rem;
    font-weight: 650;
  }

  .entries-control .form-select {
    width: 82px;
    min-height: 36px;
  }

  .search-control {
    position: relative;
  }

  .search-control i {
    position: absolute;
    left: 12px;
    color: #64748b;
  }

  .search-control .form-control {
    width: 240px;
    min-height: 36px;
    padding-left: 34px;
  }

  .table th {
    padding: 14px 12px;
    border-bottom: 1px solid #c9ded6;
    background: #e7f5ef;
    color: #174236;
    font-weight: 600;
    font-size: 0.76rem;
    line-height: 1.4;
    text-transform: uppercase;
    letter-spacing: 0;
    border-top: none;
    white-space: nowrap;
  }

  .table td {
    padding: 13px 12px;
    color: #1e293b;
    vertical-align: middle;
    font-size: 0.84rem;
    line-height: 1.55;
  }

  .loket-table tbody tr:hover {
    background: #f6fbf8;
  }

  .table-muted {
    color: #64748b;
  }

  .table-responsive {
    width: 100%;
  }

  .action-buttons {
    display: inline-flex;
    gap: 6px;
  }

  .btn-icon {
    width: 34px;
    height: 34px;
    padding: 0;
  }

  .empty-state {
    height: 96px;
    color: #64748b;
    text-align: center;
  }

  .empty-state i {
    display: block;
    margin-bottom: 6px;
    font-size: 1.5rem;
  }

  .pagination-wrap {
    padding: 16px;
    border-top: 1px solid #e5edf0;
    background: #ffffff;
  }

  .pagination .page-link {
    margin: 0 2px;
    border-radius: 7px;
    color: #0f6b4c;
  }

  .spinner {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  :deep(.pagination .page-item.active .page-link) {
    border-color: #0f6b4c;
    background: #0f6b4c;
    color: #ffffff;
  }

  @media (max-width: 768px) {
    .loket-page {
      padding: 16px;
    }

    .loket-card-body {
      grid-template-columns: 1fr;
      padding: 16px;
    }

    .form-section {
      grid-column: 1 / -1;
      padding: 16px;
    }

    .page-actions,
    .page-actions .btn,
    .form-footer .btn,
    .search-control,
    .search-control .form-control {
      width: 100%;
    }

    .form-footer {
      justify-content: stretch;
      padding: 16px;
    }

    .table-tools {
      align-items: stretch;
      flex-direction: column;
    }
  }

  .custom-modal {
    position: fixed;
    inset: 0;
    z-index: 2000;
    background: rgba(0, 0, 0, 0.45);
    padding: 24px;
  }

  .modal-content-custom {
    width: min(95%, 360px);
    padding: 28px 24px;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
  }

  .success-icon,
  .error-icon {
    width: 78px;
    height: 78px;
    margin: 0 auto;
    display: grid;
    place-items: center;
    border-radius: 50%;
    font-size: 2.4rem;
  }

  .success-icon {
    background: #d1fae5;
    color: #16a34a;
  }

  .error-icon {
    background: #fee2e2;
    color: #dc2626;
  }
</style>
