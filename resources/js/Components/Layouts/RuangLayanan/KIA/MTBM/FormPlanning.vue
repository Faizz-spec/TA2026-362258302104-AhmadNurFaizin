<template>
  <div class="bg-white p-3 rounded-3 shadow-sm planning-mtbm">
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h6 class="fw-bold text-success mb-1">
          P – PLANNING (RENCANA TINDAKAN MTBM)
        </h6>
        <small class="text-muted">
          Rencana tatalaksana bayi muda berdasarkan hasil Assessment MTBM.
        </small>
      </div>

      <div class="text-end">
        <small class="text-muted d-block">ID Kunjungan</small>
        <strong>{{ kid || 'Tidak terbaca' }}</strong>
      </div>
    </div>

    <div v-if="!kid" class="alert alert-danger py-2">
      ID pelayanan tidak terbaca. Pastikan parent mengirim
      <code>kunjunganId</code> atau data pasien memiliki
      <code>idpelayanan</code>.
    </div>

    <div
      v-if="message"
      class="alert py-2"
      :class="messageType === 'success' ? 'alert-success' : 'alert-danger'"
    >
      {{ message }}
    </div>

    <!-- REKOMENDASI SESUAI TAB AKTIF -->
    <div
      class="recommendation-simple mb-4"
      :class="activeBagian === 'tindakan'
        ? 'recommendation-simple-action'
        : 'recommendation-simple-medicine'"
    >
      <div class="recommendation-simple-header">
        <div>
          <h5
            class="recommendation-simple-title mb-1"
            :class="activeBagian === 'tindakan' ? 'text-primary' : 'text-success'"
          >
            {{
              activeBagian === 'tindakan'
                ? 'Rekomendasi Tindakan'
                : 'Rekomendasi Pengobatan'
            }}
          </h5>

          <div class="recommendation-simple-subtitle">
            Berdasarkan hasil Assessment MTBM
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <span
            class="recommendation-total"
            :class="activeBagian === 'tindakan'
              ? 'recommendation-total-action'
              : 'recommendation-total-medicine'"
          >
            {{
              activeBagian === 'tindakan'
                ? jumlahRekomendasiTindakan
                : jumlahRekomendasiPengobatan
            }}
          </span>

          <button
            type="button"
            class="btn"
            :class="activeBagian === 'tindakan'
              ? 'btn-outline-primary'
              : 'btn-outline-success'"
            :disabled="loadingRekomendasi || !kid"
            @click="loadRekomendasiPlanning"
          >
            <span
              v-if="loadingRekomendasi"
              class="spinner-border spinner-border-sm me-1"
            ></span>
            {{ loadingRekomendasi ? 'Memuat...' : 'Refresh' }}
          </button>
        </div>
      </div>

      <div v-if="loadingRekomendasi" class="recommendation-loading">
        Memuat rekomendasi dari Assessment MTBM...
      </div>

      <div
        v-else-if="rekomendasiPlanning.length === 0"
        class="recommendation-empty"
      >
        Belum ada rekomendasi. Pastikan Assessment MTBM sudah digenerate dan disimpan.
      </div>

      <!-- TAB TINDAKAN: TANPA TOMBOL PENCARIAN -->
      <div
        v-else-if="activeBagian === 'tindakan'"
        class="recommendation-list"
      >
        <div
          v-if="rekomendasiTindakan.length === 0"
          class="recommendation-empty"
        >
          Tidak ada rekomendasi tindakan khusus.
        </div>

        <template v-else>
          <div
            v-for="(rekomendasi, index) in rekomendasiTindakan"
            :key="`tindakan-${rekomendasi.klasifikasi}-${index}`"
            class="recommendation-group"
          >
            <div class="recommendation-group-title text-primary">
              {{ rekomendasi.klasifikasi }}
            </div>

            <div
              v-for="(item, itemIndex) in rekomendasi.items"
              :key="`tindakan-${item}-${itemIndex}`"
              class="recommendation-simple-item"
            >
              <span class="recommendation-bullet action-bullet"></span>
              <span>{{ item }}</span>
            </div>
          </div>
        </template>
      </div>

      <!-- TAB PENGOBATAN: TETAP ADA TOMBOL CARI OBAT -->
      <div v-else class="recommendation-list">
        <div
          v-if="rekomendasiPengobatan.length === 0"
          class="recommendation-empty"
        >
          Tidak ada rekomendasi obat khusus.
        </div>

        <template v-else>
          <div
            v-for="(rekomendasi, index) in rekomendasiPengobatan"
            :key="`obat-${rekomendasi.klasifikasi}-${index}`"
            class="recommendation-group"
          >
            <div class="recommendation-group-title text-success">
              {{ rekomendasi.klasifikasi }}
            </div>

            <div
              v-for="(item, itemIndex) in rekomendasi.items"
              :key="`obat-${item}-${itemIndex}`"
              class="recommendation-simple-item recommendation-simple-item-with-action"
            >
              <div class="recommendation-item-text">
                <span class="recommendation-bullet medicine-bullet"></span>
                <span>{{ item }}</span>
              </div>

              <button
                type="button"
                class="btn btn-outline-success"
                @click="pakaiRekomendasiObat(item)"
              >
                Cari obat ini
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- NAVIGASI DUA BAGIAN -->
    <div class="planning-tabs mb-4">
      <button
        type="button"
        class="planning-tab"
        :class="{ active: activeBagian === 'tindakan' }"
        @click="activeBagian = 'tindakan'"
      >
        <span class="planning-tab-icon">
          <i class="bi bi-clipboard2-pulse"></i>
        </span>

        <span class="text-start">
          <span class="d-block fw-bold">Tindakan</span>
          <small>Pilih tindakan, edukasi, dan kontrol</small>
        </span>

        <span class="planning-tab-count">
          {{ form.tindakanSegera.length }}
        </span>
      </button>

      <button
        type="button"
        class="planning-tab"
        :class="{ active: activeBagian === 'pengobatan' }"
        @click="activeBagian = 'pengobatan'"
      >
        <span class="planning-tab-icon">
          <i class="bi bi-capsule-pill"></i>
        </span>

        <span class="text-start">
          <span class="d-block fw-bold">Pengobatan</span>
          <small>Cari dan masukkan obat pasien</small>
        </span>

        <span class="planning-tab-count">
          {{ form.pengobatan.length }}
        </span>
      </button>
    </div>

    <!-- =========================================================
         BAGIAN 1: TINDAKAN
    ========================================================== -->
    <div
      v-show="activeBagian === 'tindakan'"
      ref="tindakanSection"
      class="main-section action-section mb-4"
    >
      <div class="main-section-header">
        <div>
          <h5 class="mb-1 fw-bold text-primary">
            Tindakan
          </h5>
          <small class="text-muted">
            Cari dan pilih tindakan berkode ICD-9-CM dari master tindakan SIMPUS.
          </small>
        </div>

        <span class="section-summary-badge action-summary">
          {{ form.tindakanSegera.length }} tindakan dipilih
        </span>
      </div>

      <!-- A. TINDAKAN MEDIS -->
      <div class="sub-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h6 class="sub-section-title mb-1">
              A. Tindakan Medis
            </h6>
            <small class="text-muted">
              Pilih tindakan berkode ICD-9-CM dari master tindakan SIMPUS.
            </small>
          </div>

          <button
            type="button"
            class="btn btn-primary px-4"
            @click="bukaModalTindakan"
          >
            <i class="bi bi-search me-1"></i>
            Cari Tindakan
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0 selected-action-table">
            <thead class="table-light">
              <tr>
                <th style="width: 55px">No</th>
                <th style="width: 110px">Kode</th>
                <th>Nama Tindakan</th>
                <th>Nama Indonesia</th>
                <th>Deskripsi</th>
                <th style="width: 90px">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(item, index) in form.tindakanSegera"
                :key="item.id || `${item.kode}-${index}`"
              >
                <td>{{ index + 1 }}</td>
                <td>
                  <span class="action-code-badge">
                    {{ item.kode || '-' }}
                  </span>
                </td>
                <td>{{ item.nama || '-' }}</td>
                <td>{{ item.nama_ind || '-' }}</td>
                <td>{{ item.keterangan || '-' }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    @click="hapusTindakan(index)"
                  >
                    Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="form.tindakanSegera.length === 0">
                <td colspan="6" class="text-center text-muted py-4">
                  Belum ada tindakan dipilih. Klik <b>Cari Tindakan</b> untuk menambahkan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- C. EDUKASI -->
      <div class="sub-section">
        <h6 class="sub-section-title">
          B. Edukasi Ibu
        </h6>

        <div class="row g-2">
          <div
            v-for="item in edukasiList"
            :key="item"
            class="col-md-6"
          >
            <label class="check-option w-100">
              <input
                v-model="form.edukasi"
                class="form-check-input"
                type="checkbox"
                :value="item"
              />
              <span>{{ item }}</span>
            </label>
          </div>
        </div>

        <div class="mt-3">
          <label class="form-label fw-semibold">
            Catatan Edukasi Tambahan
          </label>

          <textarea
            v-model.trim="form.catatanEdukasi"
            class="form-control"
            rows="3"
            placeholder="Tuliskan edukasi atau catatan tambahan..."
          ></textarea>
        </div>
      </div>

      <!-- D. KUNJUNGAN ULANG -->
      <div class="sub-section sub-section-last">
        <h6 class="sub-section-title">
          C. Rencana Kunjungan Ulang
        </h6>

        <div class="d-flex gap-2 flex-wrap">
          <label
            v-for="hari in kunjunganUlangOptions"
            :key="hari"
            class="radio-option"
            :class="{ selected: form.kunjunganUlang === hari }"
          >
            <input
              v-model.number="form.kunjunganUlang"
              class="form-check-input"
              type="radio"
              :value="hari"
            />
            <span>{{ hari }} hari</span>
          </label>

          <button
            v-if="form.kunjunganUlang !== null"
            type="button"
            class="btn btn-sm btn-outline-secondary"
            @click="form.kunjunganUlang = null"
          >
            Kosongkan
          </button>
        </div>
      </div>
    </div>

    <!-- =========================================================
         BAGIAN 2: PENGOBATAN
    ========================================================== -->
    <div
      v-show="activeBagian === 'pengobatan'"
      ref="pengobatanSection"
      class="main-section medicine-section mb-4"
    >
      <div class="main-section-header">
        <div>
          <h5 class="mb-1 fw-bold text-success">
            Pengobatan
          </h5>
          <small class="text-muted">
            Cari obat dari master SIMPUS, tentukan dosis, cara pemberian, dan durasi.
          </small>
        </div>

        <span class="section-summary-badge medicine-summary">
          {{ form.pengobatan.length }} obat ditambahkan
        </span>
      </div>

      <div class="medicine-form">
        <div class="row g-3 align-items-end">
          <div class="col-xl-4 col-md-6 position-relative">
            <label class="form-label fw-semibold">
              Nama Obat
            </label>

            <input
              ref="searchObatInput"
              v-model.trim="searchObat"
              class="form-control"
              placeholder="Cari dan pilih obat..."
              autocomplete="off"
              @input="loadObat(false)"
              @focus="bukaDropdownObat"
              @blur="tutupDropdownObat"
            />

            <div
              v-if="showDropdownObat && daftarObat.length > 0"
              class="list-group position-absolute w-100 shadow-sm obat-dropdown"
            >
              <button
                v-for="item in daftarObat"
                :key="item.obat_id"
                type="button"
                class="list-group-item list-group-item-action text-start"
                @mousedown.prevent="pilihObat(item)"
              >
                <div class="fw-semibold">
                  {{ item.nama }}
                </div>

                <small class="text-muted">
                  <span v-if="item.kode_obat">
                    {{ item.kode_obat }}
                  </span>
                  <span v-if="item.kode_obat && item.satuan">
                    ·
                  </span>
                  <span v-if="item.satuan">
                    {{ item.satuan }}
                  </span>
                </small>
              </button>
            </div>

            <small
              v-if="searchObat.length > 0 && searchObat.length < 2"
              class="text-muted"
            >
              Ketik minimal 2 huruf.
            </small>

            <small v-else-if="loadingObat" class="text-muted">
              Mencari obat...
            </small>

            <small
              v-else-if="showDropdownObat && searchObat.length >= 2 && daftarObat.length === 0"
              class="text-danger"
            >
              Obat tidak ditemukan.
            </small>

            <small v-else-if="obat.obat_id" class="text-success">
              Obat dipilih: {{ obat.nama }}
            </small>
          </div>

          <div class="col-xl-2 col-md-6">
            <label class="form-label fw-semibold">
              Dosis
            </label>

            <input
              v-model.trim="obat.dosis"
              class="form-control"
              placeholder="Otomatis / bisa edit"
            />
          </div>

          <div class="col-xl-2 col-md-6">
            <label class="form-label fw-semibold">
              Cara Pemberian
            </label>

            <select v-model="obat.cara" class="form-select">
              <option value="">
                -- Pilih --
              </option>
              <option value="oral">
                Oral
              </option>
              <option value="suntik">
                Suntik
              </option>
              <option value="infus">
                Infus
              </option>
              <option value="topikal">
                Topikal
              </option>
            </select>
          </div>

          <div class="col-xl-2 col-md-4">
            <label class="form-label fw-semibold">
              Lama Pemberian
            </label>

            <div class="input-group">
              <input
                v-model.number="obat.lama"
                type="number"
                class="form-control"
                min="0"
                max="365"
                step="1"
                placeholder="0"
              />
              <span class="input-group-text">hari</span>
            </div>
          </div>

          <div class="col-xl-2 col-md-8">
            <button
              type="button"
              class="btn btn-success w-100"
              :disabled="!obat.obat_id"
              @click="tambahObat"
            >
              Tambahkan Obat
            </button>
          </div>
        </div>
      </div>

      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle mb-0 medicine-table">
          <thead class="table-light">
            <tr>
              <th style="width: 55px">No.</th>
              <th>Obat</th>
              <th style="width: 180px">Dosis</th>
              <th style="width: 140px">Cara</th>
              <th style="width: 110px">Lama</th>
              <th style="width: 90px">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(item, index) in form.pengobatan"
              :key="`${item.obat_id}-${index}`"
            >
              <td>{{ index + 1 }}</td>
              <td>
                <div class="fw-semibold">
                  {{ item.nama }}
                </div>
                <small class="text-muted">
                  <span v-if="item.kode_obat">
                    {{ item.kode_obat }}
                  </span>
                  <span v-if="item.kode_obat && item.satuan">
                    ·
                  </span>
                  <span v-if="item.satuan">
                    {{ item.satuan }}
                  </span>
                </small>
              </td>
              <td>{{ item.dosis || '-' }}</td>
              <td class="text-capitalize">
                {{ item.cara || '-' }}
              </td>
              <td>
                {{ item.lama ?? 0 }} hari
              </td>
              <td>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-danger"
                  @click="hapusObat(index)"
                >
                  Hapus
                </button>
              </td>
            </tr>

            <tr v-if="form.pengobatan.length === 0">
              <td colspan="6" class="text-center text-muted py-4">
                Belum ada obat ditambahkan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SIMPAN -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 save-area">
      <div>
        <small class="text-muted d-block">
          Tindakan dan pengobatan disimpan dalam satu Planning MTBM.
        </small>
        <small class="text-muted">
          Terpilih: {{ form.tindakanSegera.length }} tindakan dan {{ form.pengobatan.length }} obat.
        </small>
      </div>

      <button
        type="button"
        class="btn btn-success px-4"
        :disabled="loading || loadingData || !kid"
        @click="simpanPlanning"
      >
        <span
          v-if="loading"
          class="spinner-border spinner-border-sm me-1"
        ></span>
        {{ loading ? 'Menyimpan...' : 'Simpan Planning MTBM' }}
      </button>
    </div>

    <!-- MODAL PENCARIAN TINDAKAN -->
    <Teleport to="body">
      <div
        v-if="showModalTindakan"
        class="mtbm-modal-backdrop"
        @click.self="tutupModalTindakan"
      >
        <div
          class="mtbm-modal-dialog"
          role="dialog"
          aria-modal="true"
          aria-labelledby="modal-tindakan-title"
        >
          <div class="mtbm-modal-header">
            <div>
              <h5 id="modal-tindakan-title" class="mb-1 fw-bold text-primary">
                Cari Tindakan ICD-9-CM
              </h5>
              <div class="text-muted">
                Cari berdasarkan kode, nama tindakan, nama Indonesia, atau deskripsi.
              </div>
            </div>

            <button
              type="button"
              class="btn-close"
              aria-label="Tutup"
              @click="tutupModalTindakan"
            ></button>
          </div>

          <div class="mtbm-modal-body">
            <div class="row g-2 align-items-end mb-3">
              <div class="col-lg-9">
                <label class="form-label fw-semibold">
                  Kode atau Nama Tindakan
                </label>

                <input
                  ref="searchTindakanInput"
                  v-model.trim="searchTindakan"
                  type="text"
                  class="form-control form-control-lg"
                  placeholder="Contoh: oxygen, infusion, ultrasound, 99.10..."
                  autocomplete="off"
                  @input="loadTindakan(false)"
                  @keyup.enter="loadTindakan(true)"
                />
              </div>

              <div class="col-lg-3">
                <div class="d-grid d-lg-flex gap-2">
                  <button
                    type="button"
                    class="btn btn-primary btn-lg"
                    :disabled="loadingTindakan"
                    @click="loadTindakan(true)"
                  >
                    <span
                      v-if="loadingTindakan"
                      class="spinner-border spinner-border-sm me-1"
                    ></span>
                    Cari
                  </button>

                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-lg"
                    :disabled="loadingTindakan"
                    @click="resetPencarianTindakan"
                  >
                    Reset
                  </button>
                </div>
              </div>
            </div>

            <div class="text-muted mb-3">
              Ditampilkan maksimal 10 data dari
              <code>simpus_master_tindakan</code>.
            </div>

            <div v-if="loadingTindakan" class="modal-loading-state">
              <span class="spinner-border spinner-border-sm me-2"></span>
              Memuat master tindakan...
            </div>

            <div v-else class="table-responsive modal-table-wrap">
              <table class="table table-bordered table-hover align-middle mb-0 action-master-table">
                <thead class="table-light sticky-top">
                  <tr>
                    <th style="width: 55px">No</th>
                    <th style="width: 110px">Kode</th>
                    <th>Nama Tindakan</th>
                    <th>Nama Indonesia</th>
                    <th style="width: 100px">Harga</th>
                    <th style="width: 100px">Sim Tarif</th>
                    <th>Deskripsi</th>
                    <th style="width: 90px">Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(item, index) in daftarTindakan"
                    :key="item.id || `${item.kode}-${index}`"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>
                      <span class="action-code-badge">
                        {{ item.kode || '-' }}
                      </span>
                    </td>
                    <td>{{ item.nama || '-' }}</td>
                    <td>{{ item.nama_ind || '-' }}</td>
                    <td>{{ formatTarif(item.harga) }}</td>
                    <td>{{ formatTarif(item.bayar) }}</td>
                    <td>{{ item.keterangan || '-' }}</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-sm"
                        :class="tindakanSudahAda(item)
                          ? 'btn-success'
                          : 'btn-outline-primary'"
                        :disabled="tindakanSudahAda(item)"
                        @click="pilihTindakan(item)"
                      >
                        {{ tindakanSudahAda(item) ? 'Dipilih' : 'Pilih' }}
                      </button>
                    </td>
                  </tr>

                  <tr v-if="daftarTindakan.length === 0">
                    <td colspan="8" class="text-center text-muted py-5">
                      Tindakan tidak ditemukan.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="mtbm-modal-footer">
            <div class="text-muted">
              {{ form.tindakanSegera.length }} tindakan sudah dipilih.
            </div>

            <button
              type="button"
              class="btn btn-secondary px-4"
              @click="tutupModalTindakan"
            >
              Selesai
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import {
  computed,
  nextTick,
  reactive,
  ref,
  watch,
} from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  DataPasien: {
    type: Array,
    default: () => [],
  },
  kunjunganId: {
    type: [String, Number],
    required: false,
    default: null,
  },
})

const kid = computed(() => {
  const pasien = props.DataPasien?.[0] || {}

  const candidates = [
    props.kunjunganId,
    page.props?.idPelayanan,
    page.props?.idpelayanan,
    page.props?.pelayanan?.idPelayanan,
    page.props?.pelayanan?.idpelayanan,
    page.props?.DataPasien?.[0]?.idpelayanan,
    page.props?.DataPasien?.[0]?.idPelayanan,
    pasien.idpelayanan,
    pasien.idPelayanan,
    pasien.kunjungan_id,
    pasien.kunjunganId,
  ]

  const found = candidates.find((value) => {
    return (
      value !== undefined
      && value !== null
      && String(value).trim() !== ''
    )
  })

  return found ? String(found) : ''
})

const loading = ref(false)
const loadingData = ref(false)
const loadingRekomendasi = ref(false)
const loadingTindakan = ref(false)
const loadingObat = ref(false)

const message = ref('')
const messageType = ref('success')

const rekomendasiPlanning = ref([])

const daftarTindakan = ref([])
const searchTindakan = ref('')
const searchTindakanInput = ref(null)
const showModalTindakan = ref(false)

const daftarObat = ref([])
const searchObat = ref('')
const showDropdownObat = ref(false)

const searchObatInput = ref(null)
const pengobatanSection = ref(null)

const activeBagian = ref(
  localStorage.getItem('mtbmPlanningBagian') || 'tindakan',
)

watch(activeBagian, (value) => {
  localStorage.setItem('mtbmPlanningBagian', value)
})

const edukasiList = [
  'Cara pemberian obat',
  'Pemberian ASI lebih sering',
  'Posisi dan perlekatan menyusu',
  'Tanda bahaya bayi muda',
  'Kapan harus kembali segera',
  'Menjaga tubuh bayi tetap hangat',
  'Pencegahan dehidrasi',
  'Pemantauan berat badan dan pertumbuhan',
]

const kunjunganUlangOptions = [2, 3, 5, 7, 14]

const form = reactive({
  tindakanSegera: [],
  pengobatan: [],
  edukasi: [],
  catatanEdukasi: '',
  kunjunganUlang: null,
})

const obat = reactive({
  obat_id: '',
  kode_obat: '',
  nama: '',
  satuan: '',
  dosis: '',
  cara: '',
  lama: null,
})

let messageTimer = null
let searchTindakanTimer = null
let activeTindakanRequest = 0
let searchTimer = null
let activeSearchRequest = 0

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type

  if (messageTimer) {
    window.clearTimeout(messageTimer)
  }

  messageTimer = window.setTimeout(() => {
    message.value = ''
  }, 5000)
}

const resetObatTerpilih = () => {
  obat.obat_id = ''
  obat.kode_obat = ''
  obat.nama = ''
  obat.satuan = ''
  obat.dosis = ''
  obat.cara = ''
  obat.lama = null
}

const resetFormPlanning = () => {
  form.tindakanSegera = []
  form.pengobatan = []
  form.edukasi = []
  form.catatanEdukasi = ''
  form.kunjunganUlang = null

  rekomendasiPlanning.value = []
  daftarTindakan.value = []
  searchTindakan.value = ''
  showModalTindakan.value = false
  daftarObat.value = []
  searchObat.value = ''
  showDropdownObat.value = false

  resetObatTerpilih()
}

const getFirstValidationError = (errors) => {
  if (!errors || typeof errors !== 'object') {
    return 'Data Planning belum valid.'
  }

  return Object.values(errors)
    .flat()
    .find(Boolean) || 'Data Planning belum valid.'
}

const normalisasiRekomendasi = (items) => {
  if (!Array.isArray(items)) {
    return []
  }

  return Array.from(
    new Set(
      items
        .map((item) => String(item || '').trim())
        .filter(Boolean),
    ),
  )
}

/**
 * Controller MTBM terbaru mengirim format:
 * {
 *   klasifikasi: '...',
 *   tindakan: [...],
 *   pengobatan: [...]
 * }
 *
 * Properti `items` tetap dibentuk di sini karena template di atas
 * menggunakan rekomendasi.items.
 */
const rekomendasiTindakan = computed(() => {
  return rekomendasiPlanning.value
    .map((rekomendasi) => ({
      ...rekomendasi,
      items: normalisasiRekomendasi(
        rekomendasi?.tindakan,
      ),
    }))
    .filter((rekomendasi) => rekomendasi.items.length > 0)
})

const rekomendasiPengobatan = computed(() => {
  return rekomendasiPlanning.value
    .map((rekomendasi) => ({
      ...rekomendasi,
      items: normalisasiRekomendasi(
        rekomendasi?.pengobatan,
      ),
    }))
    .filter((rekomendasi) => rekomendasi.items.length > 0)
})

const jumlahRekomendasiTindakan = computed(() => {
  return rekomendasiTindakan.value.reduce(
    (total, rekomendasi) => total + rekomendasi.items.length,
    0,
  )
})

const jumlahRekomendasiPengobatan = computed(() => {
  return rekomendasiPengobatan.value.reduce(
    (total, rekomendasi) => total + rekomendasi.items.length,
    0,
  )
})

const formatTarif = (value) => {
  if (value === null || value === undefined || value === '') {
    return '-'
  }

  const numberValue = Number(value)

  if (!Number.isFinite(numberValue)) {
    return String(value)
  }

  return new Intl.NumberFormat('id-ID').format(numberValue)
}

const tindakanSudahAda = (selected) => {
  return form.tindakanSegera.some((item) => {
    if (selected?.id !== null && selected?.id !== undefined && item?.id !== null && item?.id !== undefined) {
      return String(item.id) === String(selected.id)
    }

    return String(item?.kode || '') === String(selected?.kode || '')
  })
}

const pilihTindakan = (selected) => {
  if (!selected?.kode || !selected?.nama) {
    showMessage('Data tindakan tidak lengkap.', 'error')
    return
  }

  if (tindakanSudahAda(selected)) {
    showMessage('Tindakan tersebut sudah dipilih.', 'error')
    return
  }

  form.tindakanSegera.push({
    id: selected.id ?? null,
    kode: selected.kode ?? '',
    nama: selected.nama ?? '',
    nama_ind: selected.nama_ind ?? '',
    harga: selected.harga ?? null,
    bayar: selected.bayar ?? null,
    keterangan: selected.keterangan ?? '',
    poli: selected.poli ?? 'MTBM',
  })

  showMessage('Tindakan berkode berhasil ditambahkan.', 'success')
}

const hapusTindakan = (index) => {
  form.tindakanSegera.splice(index, 1)
}

const requestTindakan = async () => {
  const query = searchTindakan.value.trim()
  const requestId = ++activeTindakanRequest

  loadingTindakan.value = true

  try {
    const response = await axios.get(
      '/simpus/kia/mtbm/tindakan',
      {
        params: {
          q: query,
        },
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    if (requestId !== activeTindakanRequest) {
      return
    }

    daftarTindakan.value = Array.isArray(response.data?.data)
      ? response.data.data
      : []
  } catch (error) {
    if (requestId !== activeTindakanRequest) {
      return
    }

    console.error(
      'LOAD TINDAKAN MTBM ERROR:',
      error.response?.data || error,
    )

    daftarTindakan.value = []

    if (error.response?.status === 419) {
      showMessage(
        'Sesi login telah habis. Login ulang lalu coba kembali.',
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Gagal mencari master tindakan.',
      'error',
    )
  } finally {
    if (requestId === activeTindakanRequest) {
      loadingTindakan.value = false
    }
  }
}

const loadTindakan = async (langsung = false) => {
  if (searchTindakanTimer) {
    window.clearTimeout(searchTindakanTimer)
  }

  if (langsung) {
    await requestTindakan()
    return
  }

  searchTindakanTimer = window.setTimeout(() => {
    requestTindakan()
  }, 300)
}

const resetPencarianTindakan = async () => {
  searchTindakan.value = ''
  await requestTindakan()
}

const bukaModalTindakan = async () => {
  showModalTindakan.value = true
  searchTindakan.value = ''

  await nextTick()
  searchTindakanInput.value?.focus()
  await requestTindakan()
}

const tutupModalTindakan = () => {
  showModalTindakan.value = false
}

const loadRekomendasiPlanning = async () => {
  if (!kid.value) {
    rekomendasiPlanning.value = []
    return
  }

  loadingRekomendasi.value = true

  try {
    const response = await axios.get(
      `/simpus/kia/mtbm/planning/rekomendasi/${kid.value}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    rekomendasiPlanning.value = Array.isArray(response.data?.data)
      ? response.data.data
      : []
  } catch (error) {
    console.error(
      'LOAD REKOMENDASI PLANNING MTBM ERROR:',
      error.response?.data || error,
    )

    rekomendasiPlanning.value = []

    if (error.response?.status === 419) {
      showMessage(
        'Sesi login telah habis. Login ulang lalu buka kembali halaman ini.',
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Gagal memuat rekomendasi Planning MTBM.',
      'error',
    )
  } finally {
    loadingRekomendasi.value = false
  }
}

const ambilKeywordObat = (item) => {
  const text = String(item || '').toLowerCase()

  if (text.includes('amoksisilin') || text.includes('amoxicillin')) {
    return 'amoksisilin'
  }

  if (text.includes('ampisilin') || text.includes('ampicillin')) {
    return 'ampisilin'
  }

  if (text.includes('gentamisin') || text.includes('gentamicin')) {
    return 'gentamisin'
  }

  if (text.includes('oralit')) return 'oralit'
  if (text.includes('zinc')) return 'zinc'

  if (text.includes('parasetamol') || text.includes('paracetamol')) {
    return 'parasetamol'
  }

  if (text.includes('vitamin a')) return 'vitamin a'
  if (text.includes('zat besi')) return 'zat besi'
  if (text.includes('kotrimoksazol')) return 'kotrimoksazol'
  if (text.includes('diazepam')) return 'diazepam'
  if (text.includes('salbutamol')) return 'salbutamol'
  if (text.includes('antimalaria')) return 'antimalaria'
  if (text.includes('antibiotik')) return 'antibiotik'

  return String(item || '')
}

const pakaiRekomendasiObat = async (item) => {
  activeBagian.value = 'pengobatan'
  searchObat.value = ambilKeywordObat(item)
  resetObatTerpilih()

  await nextTick()

  pengobatanSection.value?.scrollIntoView({
    behavior: 'smooth',
    block: 'start',
  })

  searchObatInput.value?.focus()
  await loadObat(true)
}

const bukaDropdownObat = () => {
  if (searchObat.value.length >= 2 && daftarObat.value.length > 0) {
    showDropdownObat.value = true
  }
}

const tutupDropdownObat = () => {
  window.setTimeout(() => {
    showDropdownObat.value = false
  }, 150)
}

const generateDosisDefault = (selected) => {
  const nama = String(selected?.nama ?? '').toLowerCase()
  const satuan = String(selected?.satuan ?? '').trim()

  const mgPerMl = nama.match(
    /(\d+(?:[.,]\d+)?)\s*mg\s*\/\s*(\d+(?:[.,]\d+)?)\s*ml/i,
  )
  const mg = nama.match(/(\d+(?:[.,]\d+)?)\s*mg/i)
  const gram = nama.match(/(\d+(?:[.,]\d+)?)\s*g(?:ram)?\b/i)
  const mcg = nama.match(/(\d+(?:[.,]\d+)?)\s*(?:mcg|µg)/i)
  const ml = nama.match(/(\d+(?:[.,]\d+)?)\s*ml/i)
  const persen = nama.match(/(\d+(?:[.,]\d+)?)\s*%/i)

  if (mgPerMl) {
    return `${mgPerMl[1].replace(',', '.')} mg/${mgPerMl[2].replace(',', '.')} ml`
  }

  if (mg) {
    return `${mg[1].replace(',', '.')} mg`
  }

  if (gram) {
    return `${gram[1].replace(',', '.')} g`
  }

  if (mcg) {
    return `${mcg[1].replace(',', '.')} mcg`
  }

  if (ml) {
    return `${ml[1].replace(',', '.')} ml`
  }

  if (persen) {
    return `${persen[1].replace(',', '.')}%`
  }

  return satuan ? `1 ${satuan}` : ''
}

const requestObat = async () => {
  const query = searchObat.value.trim()

  resetObatTerpilih()

  if (query.length < 2) {
    daftarObat.value = []
    showDropdownObat.value = false
    loadingObat.value = false
    return
  }

  const requestId = ++activeSearchRequest
  loadingObat.value = true

  try {
    const response = await axios.get(
      '/simpus/kia/mtbm/obat',
      {
        params: {
          q: query,
        },
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    if (requestId !== activeSearchRequest) {
      return
    }

    daftarObat.value = Array.isArray(response.data?.data)
      ? response.data.data
      : []

    showDropdownObat.value = true
  } catch (error) {
    if (requestId !== activeSearchRequest) {
      return
    }

    console.error(
      'LOAD OBAT MTBM ERROR:',
      error.response?.data || error,
    )

    daftarObat.value = []
    showDropdownObat.value = true

    if (error.response?.status === 419) {
      showMessage(
        'Sesi login telah habis. Login ulang lalu coba kembali.',
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Gagal mencari master obat.',
      'error',
    )
  } finally {
    if (requestId === activeSearchRequest) {
      loadingObat.value = false
    }
  }
}

const loadObat = async (langsung = false) => {
  if (searchTimer) {
    window.clearTimeout(searchTimer)
  }

  if (langsung) {
    await requestObat()
    return
  }

  searchTimer = window.setTimeout(() => {
    requestObat()
  }, 300)
}

const pilihObat = (selected) => {
  obat.obat_id = selected?.obat_id ?? ''
  obat.kode_obat = selected?.kode_obat ?? ''
  obat.nama = selected?.nama ?? ''
  obat.satuan = selected?.satuan ?? ''
  obat.dosis = generateDosisDefault(selected)
  obat.cara = ''
  obat.lama = null

  searchObat.value = selected?.nama ?? ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const tambahObat = () => {
  if (!obat.obat_id || !obat.nama) {
    showMessage(
      'Pilih obat terlebih dahulu dari daftar pencarian.',
      'error',
    )
    return
  }

  const sudahAda = form.pengobatan.some((item) => {
    return String(item.obat_id) === String(obat.obat_id)
  })

  if (sudahAda) {
    showMessage(
      'Obat tersebut sudah ada dalam daftar pengobatan.',
      'error',
    )
    return
  }

  form.pengobatan.push({
    obat_id: obat.obat_id,
    kode_obat: obat.kode_obat,
    nama: obat.nama,
    satuan: obat.satuan,
    dosis: obat.dosis,
    cara: obat.cara,
    lama: obat.lama ?? 0,
  })

  resetObatTerpilih()
  searchObat.value = ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const hapusObat = (index) => {
  form.pengobatan.splice(index, 1)
}

const applyData = (data) => {
  if (!data) {
    form.tindakanSegera = []
    form.pengobatan = []
    form.edukasi = []
    form.catatanEdukasi = ''
    form.kunjunganUlang = null
    return
  }

  form.tindakanSegera = Array.isArray(data.tindakanSegera)
    ? data.tindakanSegera.map((item) => {
      if (item && typeof item === 'object') {
        return {
          id: item.id ?? null,
          kode: item.kode ?? '-',
          nama: item.nama ?? '-',
          nama_ind: item.nama_ind ?? '',
          harga: item.harga ?? null,
          bayar: item.bayar ?? null,
          keterangan: item.keterangan ?? '',
          poli: item.poli ?? 'MTBM',
        }
      }

      // Kompatibilitas data lama yang masih berupa string.
      return {
        id: null,
        kode: '-',
        nama: String(item || '-'),
        nama_ind: '',
        harga: null,
        bayar: null,
        keterangan: 'Data Planning lama sebelum memakai master tindakan.',
        poli: 'MTBM',
      }
    })
    : []

  form.pengobatan = Array.isArray(data.pengobatan)
    ? data.pengobatan
    : []

  form.edukasi = Array.isArray(data.edukasi)
    ? data.edukasi
    : []

  form.catatanEdukasi = data.catatanEdukasi ?? ''

  form.kunjunganUlang = (
    data.kunjunganUlang !== null
    && data.kunjunganUlang !== undefined
    && data.kunjunganUlang !== ''
  )
    ? Number(data.kunjunganUlang)
    : null
}

const loadFromDb = async () => {
  if (!kid.value) {
    applyData(null)
    return
  }

  loadingData.value = true

  try {
    const response = await axios.get(
      `/simpus/kia/mtbm/planning/${kid.value}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    applyData(response.data?.data)
  } catch (error) {
    console.error(
      'LOAD PLANNING MTBM ERROR:',
      error.response?.data || error,
    )

    applyData(null)

    if (error.response?.status === 419) {
      showMessage(
        'Sesi login telah habis. Login ulang lalu buka kembali halaman ini.',
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Gagal memuat Planning MTBM.',
      'error',
    )
  } finally {
    loadingData.value = false
  }
}

const loadSemuaData = async () => {
  if (!kid.value) {
    resetFormPlanning()
    return
  }

  await Promise.all([
    loadRekomendasiPlanning(),
    loadFromDb(),
  ])
}

const simpanPlanning = async () => {
  if (!kid.value) {
    showMessage(
      'ID pelayanan tidak terbaca.',
      'error',
    )
    return
  }

  loading.value = true
  message.value = ''

  try {
    const payload = {
      kunjungan_id: String(kid.value),
      tindakanSegera: form.tindakanSegera,
      pengobatan: form.pengobatan,
      edukasi: form.edukasi,
      catatanEdukasi: form.catatanEdukasi,
      kunjunganUlang: form.kunjunganUlang,
    }

    const response = await axios.post(
      '/simpus/kia/mtbm/planning/store',
      payload,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    await loadFromDb()

    showMessage(
      response.data?.message
        || 'Planning MTBM berhasil disimpan.',
      'success',
    )
  } catch (error) {
    console.error(
      'SAVE PLANNING MTBM ERROR:',
      error.response?.data || error,
    )

    if (error.response?.status === 419) {
      showMessage(
        'Sesi login telah habis. Login ulang lalu coba kembali.',
        'error',
      )
      return
    }

    if (error.response?.status === 422) {
      showMessage(
        getFirstValidationError(error.response?.data?.errors),
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Gagal menyimpan Planning MTBM.',
      'error',
    )
  } finally {
    loading.value = false
  }
}

watch(
  kid,
  (newKid, oldKid) => {
    if (!newKid) {
      resetFormPlanning()
      return
    }

    if (newKid !== oldKid) {
      loadSemuaData()
    }
  },
  {
    immediate: true,
  },
)
</script>

<style scoped>
.planning-mtbm {
  border: 1px solid #e4ebe7;
}

.recommendation-simple {
  padding: 22px;
  border: 1px solid #dfe6e2;
  border-left-width: 5px;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
}

.recommendation-simple-action {
  border-left-color: #0d6efd;
}

.recommendation-simple-medicine {
  border-left-color: #198754;
}

.recommendation-simple-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 18px;
}

.recommendation-simple-title {
  font-size: 21px;
  font-weight: 750;
}

.recommendation-simple-subtitle {
  color: #6c757d;
  font-size: 15px;
}

.recommendation-total {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 38px;
  height: 38px;
  padding: 0 12px;
  border-radius: 20px;
  font-size: 15px;
  font-weight: 750;
}

.recommendation-total-action {
  color: #084298;
  background: #cfe2ff;
}

.recommendation-total-medicine {
  color: #0f5132;
  background: #d1e7dd;
}

.recommendation-loading,
.recommendation-empty {
  padding: 18px;
  border: 1px dashed #d6dfda;
  border-radius: 9px;
  color: #6c757d;
  background: #fafcfb;
  font-size: 15px;
  text-align: center;
}

.recommendation-list {
  display: grid;
  gap: 14px;
}

.recommendation-group {
  padding: 17px;
  border: 1px solid #e0e7e3;
  border-radius: 10px;
  background: #fbfcfb;
}

.recommendation-group-title {
  margin-bottom: 12px;
  font-size: 17px;
  font-weight: 750;
}

.recommendation-simple-item {
  display: flex;
  align-items: flex-start;
  gap: 11px;
  padding: 11px 0;
  color: #35413b;
  border-top: 1px solid #edf1ef;
  font-size: 16px;
  line-height: 1.5;
}

.recommendation-simple-item:first-of-type {
  border-top: 0;
}

.recommendation-simple-item-with-action {
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}

.recommendation-item-text {
  display: flex;
  align-items: flex-start;
  gap: 11px;
  min-width: 0;
}

.recommendation-bullet {
  flex: 0 0 auto;
  width: 10px;
  height: 10px;
  margin-top: 7px;
  border-radius: 50%;
}

.action-bullet {
  background: #0d6efd;
}

.medicine-bullet {
  background: #198754;
}

/* NAVIGASI */
.planning-tabs {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.planning-tab {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 72px;
  padding: 13px 16px;
  border: 1px solid #d9e2dd;
  border-radius: 11px;
  color: #3e4a43;
  background: #ffffff;
  transition: all 0.15s ease;
}

.planning-tab:hover {
  border-color: #75b798;
  background: #f4faf6;
}

.planning-tab.active {
  color: #ffffff;
  border-color: #198754;
  background: #198754;
  box-shadow: 0 4px 12px rgba(25, 135, 84, 0.18);
}

.planning-tab-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 42px;
  width: 42px;
  height: 42px;
  border-radius: 9px;
  background: rgba(128, 128, 128, 0.11);
  font-size: 21px;
}

.planning-tab.active .planning-tab-icon {
  background: rgba(255, 255, 255, 0.18);
}

.planning-tab small {
  color: #7a8580;
}

.planning-tab.active small {
  color: rgba(255, 255, 255, 0.82);
}

.planning-tab-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 28px;
  height: 28px;
  margin-left: auto;
  padding: 0 8px;
  border-radius: 20px;
  background: rgba(128, 128, 128, 0.14);
  font-size: 12px;
  font-weight: 700;
}

.planning-tab.active .planning-tab-count {
  background: rgba(255, 255, 255, 0.2);
}

/* BAGIAN UTAMA */
.main-section {
  overflow: visible;
  padding: 20px;
  border: 1px solid;
  border-radius: 12px;
  background: #ffffff;
}

.action-section {
  border-color: #b6d4fe;
}

.medicine-section {
  border-color: #a3cfbb;
}

.sub-section {
  padding: 18px 0;
  border-bottom: 1px solid #e6ece8;
}

.sub-section:first-of-type {
  padding-top: 0;
}

.sub-section-last {
  padding-bottom: 0;
  border-bottom: 0;
}

.sub-section-title {
  margin-bottom: 14px;
  color: #3e4b44;
  font-weight: 700;
}

.check-option,
.radio-option {
  display: flex;
  align-items: center;
  gap: 9px;
  min-height: 43px;
  margin: 0;
  padding: 9px 12px;
  border: 1px solid #dce4df;
  border-radius: 8px;
  background: #fafcfb;
  cursor: pointer;
  transition: all 0.15s ease;
}

.check-option:hover,
.radio-option:hover {
  border-color: #75b798;
  background: #f2faf5;
}

.check-option:has(.form-check-input:checked),
.radio-option.selected {
  border-color: #198754;
  color: #146c43;
  background: #eaf7f0;
}

.check-option .form-check-input,
.radio-option .form-check-input {
  flex: 0 0 auto;
  margin: 0;
}

.radio-option {
  min-width: 105px;
}

/* MASTER TINDAKAN + MODAL */
.action-master-table {
  min-width: 1150px;
}

.selected-action-table {
  min-width: 900px;
}

.mtbm-modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 10550;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(16, 24, 20, 0.58);
}

.mtbm-modal-dialog {
  display: flex;
  flex-direction: column;
  width: min(1400px, 96vw);
  max-height: 92vh;
  overflow: hidden;
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 24px 70px rgba(0, 0, 0, 0.24);
}

.mtbm-modal-header,
.mtbm-modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 22px;
  background: #ffffff;
}

.mtbm-modal-header {
  border-bottom: 1px solid #e1e7e4;
}

.mtbm-modal-footer {
  border-top: 1px solid #e1e7e4;
}

.mtbm-modal-body {
  min-height: 0;
  padding: 20px 22px;
  overflow-y: auto;
  background: #f8faf9;
}

.modal-table-wrap {
  max-height: 58vh;
  overflow: auto;
  border: 1px solid #dfe6e2;
  border-radius: 10px;
  background: #ffffff;
}

.modal-loading-state {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 220px;
  color: #6c757d;
}

.action-code-badge {
  display: inline-block;
  padding: 4px 8px;
  border: 1px solid #b6d4fe;
  border-radius: 6px;
  color: #084298;
  background: #e7f1ff;
  font-family: monospace;
  font-size: 12px;
  font-weight: 700;
}

.medicine-form {
  padding: 16px;
  border: 1px solid #dce6e0;
  border-radius: 10px;
  background: #f8fbf9;
}

.form-label {
  margin-bottom: 6px;
  color: #3f4944;
  font-size: 14px;
}

.form-control,
.form-select,
.input-group-text {
  min-height: 43px;
  border-color: #d5ded9;
  font-size: 14px;
}

.form-control:focus,
.form-select:focus {
  border-color: #75b798;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
}

.obat-dropdown {
  z-index: 9999;
  top: calc(100% - 18px);
  max-height: 280px;
  overflow-y: auto;
}

.table th {
  color: #44524a;
  font-size: 13px;
  white-space: nowrap;
}

.table td {
  font-size: 13px;
}

.medicine-table {
  overflow: hidden;
  border-radius: 9px;
}

.save-area {
  padding-top: 15px;
  border-top: 1px solid #e1e7e4;
}

@media (max-width: 991.98px) {
  .recommendation-line {
    align-items: flex-start;
    flex-direction: column;
  }

  .recommendation-line .btn {
    margin-left: 17px;
  }
}

@media (max-width: 767.98px) {
  .planning-tabs {
    grid-template-columns: 1fr;
  }

  .main-section {
    padding: 15px;
  }

  .main-section-header,
  .recommendation-column-header {
    align-items: flex-start;
    flex-direction: column;
  }

  .save-area {
    align-items: stretch !important;
    flex-direction: column;
  }

  .save-area .btn {
    width: 100%;
  }
  .mtbm-modal-backdrop {
    align-items: flex-end;
    padding: 0;
  }

  .mtbm-modal-dialog {
    width: 100%;
    max-height: 94vh;
    border-radius: 14px 14px 0 0;
  }

  .mtbm-modal-header,
  .mtbm-modal-body,
  .mtbm-modal-footer {
    padding: 15px;
  }

  .mtbm-modal-footer {
    align-items: stretch;
    flex-direction: column;
  }

}
</style>
