<template>
  <div class="mtbm-container bg-white rounded-3 shadow-sm">
    <div class="page-header">
      <div>
        <h5 class="page-title mb-1">SUBJEKTIF MTBM</h5>
        <div class="page-subtitle">
          Anamnesis bayi muda umur kurang dari 2 bulan sesuai rule MTBM
        </div>
      </div>

      <div v-if="idPelayanan" class="pelayanan-badge">
        ID Pelayanan: {{ idPelayanan }}
      </div>
    </div>

    <form @submit.prevent="simpan">
      <div class="form-body">
        <div v-if="!idPelayanan" class="alert alert-danger mb-3">
          <strong>ID pelayanan tidak terbaca.</strong>
          Periksa props dari halaman pelayanan MTBM.
        </div>

        <div
          v-if="message"
          class="alert mb-3"
          :class="messageType === 'success' ? 'alert-success' : 'alert-danger'"
        >
          {{ message }}
        </div>

        <div class="guide-box mb-3">
          <div class="fw-bold mb-1">Petunjuk pengisian</div>
          <div>
            Isi berdasarkan hasil wawancara dengan ibu atau pengasuh. Tanda klinis seperti
            tarikan dada, gerakan bayi, mata, pusar, ikterus, dan hidrasi dicatat pada Objektif.
          </div>
        </div>

        <div class="row g-3">
          <div class="col-xl-6">
            <div class="column-card">
              <section class="form-section">
                <SectionHeader
                  number="1"
                  title="Keluhan Utama"
                  description="Keluhan utama dan lama bayi mengalami sakit"
                />

                <div class="mb-3">
                  <label class="form-label">Keluhan utama</label>
                  <input
                    v-model.trim="form.keluhan_utama"
                    type="text"
                    class="form-control form-control-lg"
                    placeholder="Contoh: demam, sulit menyusu, diare"
                  />
                </div>

                <div>
                  <label class="form-label">Lama sakit</label>
                  <div class="input-group input-group-lg">
                    <input
                      v-model.number="form.lama_sakit_hari"
                      type="number"
                      min="0"
                      max="365"
                      class="form-control"
                      placeholder="0"
                    />
                    <span class="input-group-text">Hari</span>
                  </div>
                </div>
              </section>

              <section class="form-section">
                <SectionHeader
                  number="2"
                  title="Riwayat Kondisi Berat"
                  description="Riwayat minum atau menyusu dan kejang selama sakit"
                />

                <div class="check-list">
                  <label
                    class="check-card"
                    :class="{ selected: form.bisa_minum_menyusu }"
                  >
                    <input
                      v-model="form.bisa_minum_menyusu"
                      class="form-check-input"
                      type="checkbox"
                    />
                    <span>
                      <span class="check-title">Bayi masih bisa minum atau menyusu</span>
                      <span class="check-description">
                        Untuk dokumentasi anamnesis. Kekuatan mengisap dinilai kembali pada Objektif.
                      </span>
                    </span>
                  </label>

                  <label
                    class="check-card danger-card"
                    :class="{ selected: form.kejang }"
                  >
                    <input
                      v-model="form.kejang"
                      class="form-check-input"
                      type="checkbox"
                    />
                    <span>
                      <span class="check-title">Pernah kejang selama sakit ini</span>
                      <span class="check-description">
                        Riwayat kejang termasuk tanda penyakit sangat berat.
                      </span>
                    </span>
                  </label>
                </div>

                <div class="small-note mt-3">
                  Muntah pada MTBM dinilai sebagai muntah berisi susu atau cairan hijau pada
                  pemeriksaan Objektif, bukan memakai tanda “memuntahkan semua” milik MTBS balita.
                </div>
              </section>

              <section class="form-section">
                <SectionHeader
                  number="3"
                  title="Batuk dan Diare"
                  description="Durasi batuk serta pertanyaan eksplisit apakah bayi mengalami diare"
                />

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Lama batuk atau sesak</label>
                    <div class="input-group input-group-lg">
                      <input
                        v-model.number="form.batuk_lama_hari"
                        type="number"
                        min="0"
                        max="365"
                        class="form-control"
                        placeholder="0"
                      />
                      <span class="input-group-text">Hari</span>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Apakah bayi mengalami diare?</label>
                    <select v-model="form.ada_diare" class="form-select form-select-lg">
                      <option :value="null">Pilih jawaban</option>
                      <option :value="true">Ya</option>
                      <option :value="false">Tidak</option>
                    </select>
                  </div>

                  <template v-if="form.ada_diare === true">
                    <div class="col-md-6">
                      <label class="form-label">Lama diare</label>
                      <div class="input-group input-group-lg">
                        <input
                          v-model.number="form.diare_lama_hari"
                          type="number"
                          min="0"
                          max="365"
                          class="form-control"
                          placeholder="0"
                        />
                        <span class="input-group-text">Hari</span>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Ada darah dalam tinja?</label>
                      <select v-model="form.darah_diare" class="form-select form-select-lg">
                        <option :value="null">Pilih jawaban</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                      </select>
                    </div>
                  </template>
                </div>
              </section>

              <section class="form-section section-last">
                <SectionHeader
                  number="4"
                  title="Demam dan Riwayat Tambahan"
                  description="Data pendukung yang tetap disimpan untuk dokumentasi pelayanan"
                />

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Lama demam</label>
                    <div class="input-group input-group-lg">
                      <input
                        v-model.number="form.demam_lama_hari"
                        type="number"
                        min="0"
                        max="365"
                        class="form-control"
                        placeholder="0"
                      />
                      <span class="input-group-text">Hari</span>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Demam setiap hari?</label>
                    <select v-model="form.demam_tiap_hari" class="form-select form-select-lg">
                      <option :value="null">Pilih jawaban</option>
                      <option value="ya">Ya</option>
                      <option value="tidak">Tidak</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Status minum obat malaria</label>
                    <select v-model="form.minum_obat_malaria" class="form-select">
                      <option :value="null">Tidak diketahui</option>
                      <option value="ya">Ya</option>
                      <option value="tidak">Tidak</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Campak 3 bulan terakhir</label>
                    <select v-model="form.campak_3_bulan" class="form-select">
                      <option :value="null">Tidak diketahui</option>
                      <option value="ya">Ya</option>
                      <option value="tidak">Tidak</option>
                    </select>
                  </div>
                </div>

                <div class="check-grid mt-3">
                  <label class="check-card compact" :class="{ selected: form.pernah_malaria }">
                    <input v-model="form.pernah_malaria" type="checkbox" class="form-check-input" />
                    <span class="check-title mb-0">Pernah malaria</span>
                  </label>

                  <label class="check-card compact" :class="{ selected: form.nyeri_telinga }">
                    <input v-model="form.nyeri_telinga" type="checkbox" class="form-check-input" />
                    <span class="check-title mb-0">Nyeri telinga</span>
                  </label>

                  <label class="check-card compact" :class="{ selected: form.cairan_telinga }">
                    <input v-model="form.cairan_telinga" type="checkbox" class="form-check-input" />
                    <span class="check-title mb-0">Cairan dari telinga</span>
                  </label>
                </div>
              </section>
            </div>
          </div>

          <div class="col-xl-6">
            <div class="column-card">
              <section class="form-section hiv-section">
                <SectionHeader
                  number="5"
                  title="Penilaian HIV Bayi Muda"
                  description="Status HIV ibu, hasil tes bayi, pajanan ASI, ART, dan profilaksis"
                />

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">Status HIV ibu</label>
                    <select v-model="form.status_hiv_ibu" class="form-select form-select-lg">
                      <option :value="null">Pilih status</option>
                      <option value="positif">Positif</option>
                      <option value="negatif">Negatif</option>
                      <option value="belum_tes">Belum tes / tidak diketahui</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Tes virologis bayi</label>
                    <select v-model="form.tes_virologis_bayi" class="form-select form-select-lg">
                      <option :value="null">Pilih hasil</option>
                      <option value="positif">Positif</option>
                      <option value="negatif">Negatif</option>
                      <option value="belum_tes">Belum tes</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Tes serologis bayi</label>
                    <select v-model="form.tes_serologis_bayi" class="form-select form-select-lg">
                      <option :value="null">Pilih hasil</option>
                      <option value="positif">Positif</option>
                      <option value="negatif">Negatif</option>
                      <option value="belum_tes">Belum tes</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Bayi saat ini mendapat ASI?</label>
                    <select v-model="form.bayi_mendapat_asi" class="form-select form-select-lg">
                      <option :value="null">Pilih jawaban</option>
                      <option :value="true">Ya</option>
                      <option :value="false">Tidak</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Bayi pernah mendapat ASI?</label>
                    <select v-model="form.bayi_pernah_mendapat_asi" class="form-select form-select-lg">
                      <option :value="null">Pilih jawaban</option>
                      <option :value="true">Ya</option>
                      <option :value="false">Tidak</option>
                    </select>
                  </div>

                  <div
                    v-if="form.bayi_mendapat_asi === false && form.bayi_pernah_mendapat_asi === true"
                    class="col-md-6"
                  >
                    <label class="form-label">ASI dihentikan sejak</label>
                    <div class="input-group input-group-lg">
                      <input
                        v-model.number="form.berhenti_asi_minggu"
                        type="number"
                        min="0"
                        max="52"
                        class="form-control"
                        placeholder="0"
                      />
                      <span class="input-group-text">Minggu</span>
                    </div>
                    <small class="form-hint">Kurang dari 6 minggu masih diperhitungkan sebagai pajanan.</small>
                  </div>
                </div>

                <div class="check-grid mt-3">
                  <label class="check-card" :class="{ selected: form.ibu_dalam_art }">
                    <input v-model="form.ibu_dalam_art" type="checkbox" class="form-check-input" />
                    <span>
                      <span class="check-title">Ibu sedang mendapat ART</span>
                      <span class="check-description">Catat terapi antiretroviral ibu.</span>
                    </span>
                  </label>

                  <label class="check-card" :class="{ selected: form.bayi_profilaksis_arv }">
                    <input v-model="form.bayi_profilaksis_arv" type="checkbox" class="form-check-input" />
                    <span>
                      <span class="check-title">Bayi mendapat profilaksis ARV</span>
                      <span class="check-description">Catat profilaksis yang sedang diberikan.</span>
                    </span>
                  </label>
                </div>
              </section>

              <section v-if="jalurPemberianMinum" class="form-section replacement-section">
                <SectionHeader
                  number="6"
                  title="Riwayat Pemberian Minum Pengganti"
                  description="Diisi pada ibu HIV positif ketika bayi tidak mendapat ASI"
                />

                <div class="alert alert-warning py-2">
                  Assessment akan memakai jalur <strong>masalah pemberian minum</strong>, bukan jalur
                  masalah pemberian ASI.
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Jenis susu atau minuman pengganti</label>
                    <input
                      v-model.trim="form.jenis_susu_pengganti"
                      type="text"
                      class="form-control form-control-lg"
                      placeholder="Contoh: susu formula bayi"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Frekuensi minum 24 jam</label>
                    <div class="input-group input-group-lg">
                      <input
                        v-model.number="form.frekuensi_minum_24_jam"
                        type="number"
                        min="0"
                        max="100"
                        class="form-control"
                        placeholder="0"
                      />
                      <span class="input-group-text">Kali</span>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Jumlah tiap kali minum</label>
                    <div class="input-group input-group-lg">
                      <input
                        v-model.number="form.jumlah_minum_per_kali_ml"
                        type="number"
                        min="0"
                        max="1000"
                        step="0.1"
                        class="form-control"
                        placeholder="0"
                      />
                      <span class="input-group-text">mL</span>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Tambahan minuman lain</label>
                    <input
                      v-model.trim="form.tambahan_minuman_pengganti"
                      type="text"
                      class="form-control form-control-lg"
                      placeholder="Contoh: air putih, teh, lainnya"
                    />
                  </div>
                </div>
              </section>

              <section class="form-section">
                <SectionHeader
                  :number="jalurPemberianMinum ? '7' : '6'"
                  title="Riwayat Imunisasi dan Pemberian Makan"
                  description="Catatan bebas untuk melengkapi anamnesis bayi"
                />

                <div class="mb-3">
                  <label class="form-label">Riwayat imunisasi</label>
                  <textarea
                    v-model.trim="form.riwayat_imunisasi"
                    class="form-control large-textarea"
                    rows="3"
                    placeholder="Contoh: HB-0, BCG, Polio 0"
                  ></textarea>
                </div>

                <div>
                  <label class="form-label">Riwayat ASI atau pemberian makan</label>
                  <textarea
                    v-model.trim="form.riwayat_asi_makan"
                    class="form-control large-textarea"
                    rows="3"
                    placeholder="Tuliskan pola ASI atau pemberian minum bayi"
                  ></textarea>
                </div>
              </section>

              <section class="form-section section-last">
                <SectionHeader
                  :number="jalurPemberianMinum ? '8' : '7'"
                  title="Keluhan Lain"
                  description="Keluhan atau informasi lain yang belum tercatat"
                />

                <textarea
                  v-model.trim="form.keluhan_lain"
                  class="form-control large-textarea"
                  rows="5"
                  placeholder="Tuliskan keluhan lain jika ada"
                ></textarea>
              </section>
            </div>
          </div>
        </div>
      </div>

      <div class="form-footer">
        <div class="footer-information">
          Setelah Subjektif disimpan, lengkapi Objektif sebelum menjalankan Assessment otomatis.
        </div>

        <button
          type="submit"
          class="btn btn-success save-button"
          :disabled="loading || !idPelayanan"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          {{ loading ? 'Menyimpan...' : 'Simpan Subjektif MTBM' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import {
  computed,
  defineComponent,
  h,
  onBeforeUnmount,
  reactive,
  ref,
  watch,
} from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const SectionHeader = defineComponent({
  name: 'SectionHeader',
  props: {
    number: {
      type: [String, Number],
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      default: '',
    },
  },
  setup(props) {
    return () => h('div', { class: 'section-header' }, [
      h('span', { class: 'section-number' }, String(props.number)),
      h('div', {}, [
        h('div', { class: 'section-title' }, props.title),
        h('div', { class: 'section-description' }, props.description),
      ]),
    ])
  },
})

const page = usePage()

const props = defineProps({
  DataPasien: {
    type: Array,
    default: () => [],
  },
  kunjunganId: {
    type: [String, Number],
    default: null,
  },
})

const loading = ref(false)
const message = ref('')
const messageType = ref('success')
let messageTimer = null
const MTBM_SUBJEKTIF_SAVED_EVENT = 'mtbm-subjektif-saved'

const idPelayanan = computed(() => {
  const pasien = props.DataPasien?.[0] || {}
  const candidates = [
    props.kunjunganId,
    page.props?.idPelayanan,
    page.props?.idpelayanan,
    page.props?.pelayanan?.idPelayanan,
    page.props?.pelayanan?.idpelayanan,
    page.props?.DataPasien?.[0]?.idPelayanan,
    page.props?.DataPasien?.[0]?.idpelayanan,
    pasien.idPelayanan,
    pasien.idpelayanan,
    pasien.kunjungan_id,
  ]

  const found = candidates.find((value) => (
    value !== undefined
    && value !== null
    && String(value).trim() !== ''
  ))

  return found ? String(found) : ''
})

const form = reactive({
  keluhan_utama: '',
  lama_sakit_hari: null,

  bisa_minum_menyusu: true,
  muntah_semua: false,
  kejang: false,

  batuk_lama_hari: null,
  ada_diare: null,
  diare_lama_hari: null,
  darah_diare: null,

  demam_lama_hari: null,
  demam_tiap_hari: null,
  pernah_malaria: false,
  minum_obat_malaria: null,
  campak_3_bulan: null,
  nyeri_telinga: false,
  cairan_telinga: false,

  status_hiv_ibu: null,
  tes_virologis_bayi: null,
  tes_serologis_bayi: null,
  bayi_mendapat_asi: null,
  bayi_pernah_mendapat_asi: null,
  berhenti_asi_minggu: null,
  ibu_dalam_art: false,
  bayi_profilaksis_arv: false,

  jenis_susu_pengganti: '',
  frekuensi_minum_24_jam: null,
  jumlah_minum_per_kali_ml: null,
  tambahan_minuman_pengganti: '',

  riwayat_imunisasi: '',
  riwayat_asi_makan: '',
  keluhan_lain: '',
})

const jalurPemberianMinum = computed(() => (
  form.status_hiv_ibu === 'positif'
  && form.bayi_mendapat_asi === false
))

const toBool = (value) => (
  value === true
  || value === 1
  || value === '1'
  || value === 'true'
  || value === 'ya'
)

const toNullableBool = (value) => {
  if (value === null || value === undefined || value === '') return null
  return toBool(value)
}

const emptyToNull = (value) => (
  value === '' || value === undefined ? null : value
)

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type

  if (messageTimer) window.clearTimeout(messageTimer)
  messageTimer = window.setTimeout(() => {
    message.value = ''
  }, 5000)
}

const getValidationMessage = (errors) => {
  if (!errors || typeof errors !== 'object') {
    return 'Data Subjektif MTBM belum valid.'
  }

  return Object.values(errors).flat().find(Boolean)
    || 'Data Subjektif MTBM belum valid.'
}

const mapDbToForm = (data) => {
  if (!data) return

  form.keluhan_utama = data.keluhan_utama ?? ''
  form.lama_sakit_hari = data.lama_sakit_hari ?? null

  form.bisa_minum_menyusu = data.bisa_minum_menyusu === null
    || data.bisa_minum_menyusu === undefined
    ? true
    : toBool(data.bisa_minum_menyusu)
  form.muntah_semua = toBool(data.muntah_semua)
  form.kejang = toBool(data.kejang)

  form.batuk_lama_hari = data.batuk_lama_hari ?? null
  form.ada_diare = toNullableBool(data.ada_diare)
  form.diare_lama_hari = data.diare_lama_hari ?? null
  form.darah_diare = data.darah_diare ?? null

  form.demam_lama_hari = data.demam_lama_hari ?? null
  form.demam_tiap_hari = data.demam_tiap_hari ?? null
  form.pernah_malaria = toBool(data.pernah_malaria)
  form.minum_obat_malaria = data.minum_obat_malaria ?? null
  form.campak_3_bulan = data.campak_3_bulan ?? null
  form.nyeri_telinga = toBool(data.nyeri_telinga)
  form.cairan_telinga = toBool(data.cairan_telinga)

  form.status_hiv_ibu = data.status_hiv_ibu ?? null
  form.tes_virologis_bayi = data.tes_virologis_bayi ?? null
  form.tes_serologis_bayi = data.tes_serologis_bayi ?? null
  form.bayi_mendapat_asi = toNullableBool(data.bayi_mendapat_asi)
  form.bayi_pernah_mendapat_asi = toNullableBool(data.bayi_pernah_mendapat_asi)
  form.berhenti_asi_minggu = data.berhenti_asi_minggu ?? null
  form.ibu_dalam_art = toBool(data.ibu_dalam_art)
  form.bayi_profilaksis_arv = toBool(data.bayi_profilaksis_arv)

  form.jenis_susu_pengganti = data.jenis_susu_pengganti ?? ''
  form.frekuensi_minum_24_jam = data.frekuensi_minum_24_jam ?? null
  form.jumlah_minum_per_kali_ml = data.jumlah_minum_per_kali_ml ?? null
  form.tambahan_minuman_pengganti = data.tambahan_minuman_pengganti ?? ''

  form.riwayat_imunisasi = data.riwayat_imunisasi ?? ''
  form.riwayat_asi_makan = data.riwayat_asi_makan ?? ''
  form.keluhan_lain = data.keluhan_lain ?? ''
}

const buildPayload = () => ({
  kunjungan_id: String(idPelayanan.value),
  keluhan_utama: emptyToNull(form.keluhan_utama),
  lama_sakit_hari: emptyToNull(form.lama_sakit_hari),

  bisa_minum_menyusu: form.bisa_minum_menyusu ? 1 : 0,
  muntah_semua: 0,
  kejang: form.kejang ? 1 : 0,

  batuk_lama_hari: emptyToNull(form.batuk_lama_hari),
  ada_diare: form.ada_diare === null ? null : (form.ada_diare ? 1 : 0),
  diare_lama_hari: form.ada_diare === true
    ? emptyToNull(form.diare_lama_hari)
    : null,
  darah_diare: form.ada_diare === true
    ? emptyToNull(form.darah_diare)
    : null,

  demam_lama_hari: emptyToNull(form.demam_lama_hari),
  demam_tiap_hari: emptyToNull(form.demam_tiap_hari),
  pernah_malaria: form.pernah_malaria ? 1 : 0,
  minum_obat_malaria: emptyToNull(form.minum_obat_malaria),
  campak_3_bulan: emptyToNull(form.campak_3_bulan),
  nyeri_telinga: form.nyeri_telinga ? 1 : 0,
  cairan_telinga: form.cairan_telinga ? 1 : 0,

  status_hiv_ibu: emptyToNull(form.status_hiv_ibu),
  tes_virologis_bayi: emptyToNull(form.tes_virologis_bayi),
  tes_serologis_bayi: emptyToNull(form.tes_serologis_bayi),
  bayi_mendapat_asi: form.bayi_mendapat_asi === null
    ? null
    : (form.bayi_mendapat_asi ? 1 : 0),
  bayi_pernah_mendapat_asi: form.bayi_pernah_mendapat_asi === null
    ? null
    : (form.bayi_pernah_mendapat_asi ? 1 : 0),
  berhenti_asi_minggu: (
    form.bayi_mendapat_asi === false
    && form.bayi_pernah_mendapat_asi === true
  )
    ? emptyToNull(form.berhenti_asi_minggu)
    : null,
  ibu_dalam_art: form.ibu_dalam_art ? 1 : 0,
  bayi_profilaksis_arv: form.bayi_profilaksis_arv ? 1 : 0,

  jenis_susu_pengganti: jalurPemberianMinum.value
    ? emptyToNull(form.jenis_susu_pengganti)
    : null,
  frekuensi_minum_24_jam: jalurPemberianMinum.value
    ? emptyToNull(form.frekuensi_minum_24_jam)
    : null,
  jumlah_minum_per_kali_ml: jalurPemberianMinum.value
    ? emptyToNull(form.jumlah_minum_per_kali_ml)
    : null,
  tambahan_minuman_pengganti: jalurPemberianMinum.value
    ? emptyToNull(form.tambahan_minuman_pengganti)
    : null,

  riwayat_imunisasi: emptyToNull(form.riwayat_imunisasi),
  riwayat_asi_makan: emptyToNull(form.riwayat_asi_makan),
  keluhan_lain: emptyToNull(form.keluhan_lain),
})

const loadFromDb = async () => {
  if (!idPelayanan.value) return

  try {
    const response = await axios.get(
      `/simpus/kia/mtbm/subjektif/${idPelayanan.value}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    mapDbToForm(response.data?.data ?? response.data)
  } catch (error) {
    if (error.response?.status === 404) return

    console.error('LOAD SUBJEKTIF MTBM ERROR:', error.response?.data || error)
    showMessage(
      error.response?.data?.message || 'Data Subjektif MTBM gagal dimuat.',
      'error',
    )
  }
}

const simpan = async () => {
  if (!idPelayanan.value) {
    showMessage('ID pelayanan kosong atau tidak terbaca.', 'error')
    return
  }

  loading.value = true
  message.value = ''

  try {
    const payload = buildPayload()
    const response = await axios.post(
      '/simpus/kia/mtbm/subjektif/store',
      payload,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    // Gabungkan payload dengan response agar field opsional seperti ada_diare
    // tetap dapat diteruskan ke tab Objektif meskipun instalasi lama belum
    // mengembalikan seluruh kolom pada response database.
    const savedData = {
      ...payload,
      ...(response.data?.data ?? {}),
    }

    mapDbToForm(savedData)

    window.dispatchEvent(
      new CustomEvent(MTBM_SUBJEKTIF_SAVED_EVENT, {
        detail: {
          kunjungan_id: String(idPelayanan.value),
          data: savedData,
        },
      }),
    )

    showMessage(
      response.data?.message || 'Subjektif MTBM berhasil disimpan.',
      'success',
    )
  } catch (error) {
    console.error('SAVE SUBJEKTIF MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 419) {
      showMessage('Sesi login telah habis. Login ulang lalu coba kembali.', 'error')
      return
    }

    if (error.response?.status === 422) {
      showMessage(getValidationMessage(error.response?.data?.errors), 'error')
      return
    }

    showMessage(
      error.response?.data?.message || 'Subjektif MTBM gagal disimpan.',
      'error',
    )
  } finally {
    loading.value = false
  }
}

watch(
  () => form.ada_diare,
  (value) => {
    if (value !== true) {
      form.diare_lama_hari = null
      form.darah_diare = null
    }
  },
)

watch(
  () => form.bayi_mendapat_asi,
  (value) => {
    if (value === true) {
      form.bayi_pernah_mendapat_asi = true
      form.berhenti_asi_minggu = null
    }
  },
)

watch(
  jalurPemberianMinum,
  (aktif) => {
    if (!aktif) {
      form.jenis_susu_pengganti = ''
      form.frekuensi_minum_24_jam = null
      form.jumlah_minum_per_kali_ml = null
      form.tambahan_minuman_pengganti = ''
    }
  },
)

watch(
  idPelayanan,
  (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) loadFromDb()
  },
  { immediate: true },
)

onBeforeUnmount(() => {
  if (messageTimer) window.clearTimeout(messageTimer)
})
</script>

<style scoped>
.mtbm-container {
  overflow: hidden;
  border: 1px solid #e1e7e4;
  color: #343a40;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 20px 24px;
  border-bottom: 1px solid #dde7e1;
  background: linear-gradient(135deg, #f4fbf7, #ffffff);
}

.page-title {
  color: #198754;
  font-size: 20px;
  font-weight: 750;
}

.page-subtitle {
  color: #6c757d;
  font-size: 14px;
}

.pelayanan-badge {
  padding: 9px 15px;
  border: 1px solid #badbcc;
  border-radius: 30px;
  color: #146c43;
  background: #d1e7dd;
  font-size: 13px;
  font-weight: 600;
}

.form-body {
  padding: 20px;
  background: #f5f7f6;
}

.guide-box {
  padding: 14px 16px;
  border: 1px solid #b6d4fe;
  border-radius: 10px;
  color: #084298;
  background: #e7f1ff;
  font-size: 13px;
}

.column-card {
  height: 100%;
  overflow: hidden;
  border: 1px solid #dfe6e2;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: 0 2px 7px rgba(0, 0, 0, 0.03);
}

.form-section {
  padding: 20px;
  border-bottom: 1px solid #e6ebe8;
}

.section-last {
  border-bottom: 0;
}

.hiv-section {
  background: linear-gradient(180deg, #fff, #fbf8ff);
}

.replacement-section {
  background: #fffdf5;
}

:deep(.section-header) {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 18px;
}

:deep(.section-number) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 32px;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: #ffffff;
  background: #198754;
  font-size: 14px;
  font-weight: 700;
}

:deep(.section-title) {
  margin-bottom: 2px;
  color: #215c40;
  font-size: 16px;
  font-weight: 750;
}

:deep(.section-description) {
  color: #7a8480;
  font-size: 12px;
  line-height: 1.45;
}

.form-label {
  margin-bottom: 7px;
  color: #404944;
  font-size: 13px;
  font-weight: 650;
}

.form-control,
.form-select,
.input-group-text {
  border-color: #d6ded9;
  font-size: 14px;
}

.form-control-lg,
.form-select-lg,
.input-group-lg > .form-control,
.input-group-lg > .input-group-text {
  min-height: 46px;
  padding: 10px 14px;
  font-size: 14px;
  border-radius: 8px;
}

.input-group-lg > .input-group-text {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.input-group-text {
  min-width: 68px;
  justify-content: center;
  color: #68726d;
  background: #f3f6f4;
  font-weight: 600;
}

.form-control:focus,
.form-select:focus {
  border-color: #75b798;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
}

.form-hint {
  display: block;
  margin-top: 5px;
  color: #7c8581;
  font-size: 11px;
}

.large-textarea {
  min-height: 100px;
  padding: 12px 14px;
  border-radius: 8px;
  resize: vertical;
  line-height: 1.5;
}

.check-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.check-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.check-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  width: 100%;
  min-height: 70px;
  margin: 0;
  padding: 14px 16px;
  border: 1px solid #dce4df;
  border-radius: 9px;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.15s ease;
}

.check-card.compact {
  align-items: center;
  min-height: 52px;
  padding: 11px 13px;
}

.check-card:hover {
  border-color: #75b798;
  background: #f6fcf8;
}

.check-card.selected {
  border-color: #198754;
  background: #eaf7f0;
  box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.06);
}

.danger-card.selected {
  border-color: #dc3545;
  background: #fff0f1;
  box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.05);
}

.check-card .form-check-input {
  flex: 0 0 auto;
  width: 19px;
  height: 19px;
  margin: 2px 0 0;
  cursor: pointer;
}

.check-title,
.check-description {
  display: block;
}

.check-title {
  margin-bottom: 3px;
  color: #3d4842;
  font-size: 13px;
  font-weight: 700;
}

.check-description {
  color: #7d8681;
  font-size: 11px;
  line-height: 1.4;
}

.small-note {
  padding: 10px 12px;
  border-left: 4px solid #0dcaf0;
  color: #53615a;
  background: #eefafd;
  font-size: 12px;
}

.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 16px 24px;
  border-top: 1px solid #dfe6e2;
  background: #ffffff;
}

.footer-information {
  color: #737c78;
  font-size: 12px;
}

.save-button {
  min-width: 220px;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 650;
}

@media (max-width: 991.98px) {
  .page-header,
  .form-footer {
    align-items: flex-start;
    flex-direction: column;
  }

  .save-button {
    width: 100%;
  }
}

@media (max-width: 767.98px) {
  .form-body {
    padding: 12px;
  }

  .form-section {
    padding: 16px;
  }

  .check-grid {
    grid-template-columns: 1fr;
  }
}
</style>
