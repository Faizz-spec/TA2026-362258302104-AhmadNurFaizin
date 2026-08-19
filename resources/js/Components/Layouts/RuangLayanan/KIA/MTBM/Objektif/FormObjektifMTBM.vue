<template>
  <div class="mtbm-objective bg-white rounded-3 shadow-sm">
    <div class="page-header">
      <div>
        <h5 class="page-title mb-1">OBJEKTIF MTBM</h5>
        <div class="page-subtitle">
          Pemeriksaan bayi muda umur kurang dari 2 bulan sesuai Buku Bagan MTBS 2022
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
          Pastikan halaman pelayanan mengirim <code>idPelayanan</code> atau <code>DataPasien</code>.
        </div>

        <div
          v-if="message"
          class="alert mb-3"
          :class="messageType === 'success' ? 'alert-success' : 'alert-danger'"
        >
          {{ message }}
        </div>

        <div class="guide-box mb-3">
          <div class="fw-bold mb-1">Petunjuk pemeriksaan</div>
          <div>
            Isi hanya berdasarkan temuan pemeriksaan. Jika frekuensi napas pertama ≥60 x/menit,
            tenangkan bayi lalu hitung ulang dan masukkan hasil pada kolom frekuensi napas ulang.
          </div>
        </div>

        <div v-if="umurHari !== null && umurHari >= 60" class="alert alert-danger mb-3">
          Umur pasien {{ umurHari }} hari. Pasien umur 60 hari atau lebih harus menggunakan modul MTBS,
          bukan MTBM.
        </div>

        <div class="row g-3">
          <div class="col-xl-7">
            <section class="section-card danger-section">
              <div class="section-header">
                <span class="section-number danger-number">1</span>
                <div>
                  <h6 class="section-title mb-1">Penyakit Sangat Berat atau Infeksi Bakteri Berat</h6>
                  <div class="section-description">
                    Napas, suhu, oksigenasi, gerakan, kejang, mata, pusar, dan saluran cerna.
                  </div>
                </div>
              </div>

              <div class="vital-panel mb-3">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">Frekuensi napas pertama</label>
                    <div class="input-group">
                      <input
                        v-model.number="form.rr"
                        type="number"
                        min="0"
                        max="300"
                        step="1"
                        class="form-control"
                        placeholder="Contoh: 62"
                      />
                      <span class="input-group-text">x/menit</span>
                    </div>
                    <small class="form-hint">Ulangi bila hasil pertama ≥60 x/menit.</small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Frekuensi napas ulang</label>
                    <div class="input-group">
                      <input
                        v-model.number="form.rr_ulang"
                        type="number"
                        min="0"
                        max="300"
                        step="1"
                        class="form-control"
                        :class="{ 'is-warning': rrPerluDiulang && !rrUlangTerisi }"
                        placeholder="Isi setelah dihitung ulang"
                      />
                      <span class="input-group-text">x/menit</span>
                    </div>
                    <small class="form-hint">
                      Assessment memakai hasil ulang bila tersedia; bahaya bila ≥60 atau &lt;40.
                    </small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Suhu aksila</label>
                    <div class="input-group">
                      <input
                        v-model.number="form.suhu"
                        type="number"
                        min="0"
                        max="50"
                        step="0.1"
                        class="form-control"
                        placeholder="Contoh: 36.8"
                      />
                      <span class="input-group-text">°C</span>
                    </div>
                    <small class="form-hint">Bahaya bila &gt;37,5°C atau &lt;36,5°C.</small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Gerakan bayi</label>
                    <select
                      v-model="form.kesadaran"
                      class="form-select"
                      @change="syncGerakanFromKesadaran"
                    >
                      <option value="">Pilih kondisi</option>
                      <option value="sadar">Bergerak aktif</option>
                      <option value="letargi">Bergerak hanya jika dirangsang</option>
                      <option value="tidak_sadar">Tidak bergerak</option>
                    </select>
                    <small class="form-hint">
                      Pilihan ini otomatis mengisi field gerakan untuk rule dehidrasi bayi muda.
                    </small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">SpO₂ tangan kanan</label>
                    <div class="input-group">
                      <input
                        v-model.number="form.spo2_tangan_kanan"
                        type="number"
                        min="0"
                        max="100"
                        step="1"
                        class="form-control"
                        placeholder="Contoh: 97"
                      />
                      <span class="input-group-text">%</span>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">SpO₂ kaki kiri</label>
                    <div class="input-group">
                      <input
                        v-model.number="form.spo2_kaki_kiri"
                        type="number"
                        min="0"
                        max="100"
                        step="1"
                        class="form-control"
                        placeholder="Contoh: 96"
                      />
                      <span class="input-group-text">%</span>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Selisih SpO₂</label>
                    <div class="readonly-box" :class="{ danger: selisihSpo2Bahaya }">
                      {{ selisihSpo2 === null ? '-' : `${selisihSpo2}%` }}
                    </div>
                    <small class="form-hint">Bahaya bila selisih &gt;3%.</small>
                  </div>

                  <div class="col-md-8">
                    <label class="form-label">Frekuensi napas yang dipakai Assessment</label>
                    <div class="readonly-box" :class="{ danger: rrBahaya }">
                      {{ rrDinilai === null ? '-' : `${rrDinilai} x/menit` }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="check-grid">
                <label
                  v-for="item in tandaBeratList"
                  :key="item.key"
                  class="check-card danger-card"
                  :class="{ selected: form[item.key] }"
                >
                  <input v-model="form[item.key]" type="checkbox" class="form-check-input" />
                  <span>
                    <span class="check-title">{{ item.label }}</span>
                    <span class="check-description">{{ item.description }}</span>
                  </span>
                </label>
              </div>
            </section>

            <section class="section-card local-section mt-3">
              <div class="section-header">
                <span class="section-number warning-number">2</span>
                <div>
                  <h6 class="section-title mb-1">Infeksi Bakteri Lokal</h6>
                  <div class="section-description">
                    Temuan lokal pada mata, pusar, dan kulit tanpa tanda infeksi berat.
                  </div>
                </div>
              </div>

              <div class="check-grid compact-grid">
                <label
                  v-for="item in infeksiLokalList"
                  :key="item.key"
                  class="check-card warning-card"
                  :class="{ selected: form[item.key] }"
                >
                  <input v-model="form[item.key]" type="checkbox" class="form-check-input" />
                  <span>
                    <span class="check-title">{{ item.label }}</span>
                    <span class="check-description">{{ item.description }}</span>
                  </span>
                </label>
              </div>
            </section>

            <section class="section-card diarrhea-section mt-3">
              <div class="section-header">
                <span class="section-number info-number">3</span>
                <div>
                  <h6 class="section-title mb-1">Diare dan Derajat Dehidrasi</h6>
                  <div class="section-description">
                    Rule dijalankan bila Subjektif mencatat bayi mengalami diare.
                  </div>
                </div>
              </div>

              <div class="subjective-status mb-3" :class="{ active: subjektifAdaDiare }">
                Status Subjektif:
                <strong>
                  {{ subjektifAdaDiare === null ? 'belum diisi' : (subjektifAdaDiare ? 'ada diare' : 'tidak diare') }}
                </strong>
              </div>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Gelisah atau rewel</label>
                  <select v-model="form.gelisah_rewel" class="form-select">
                    <option :value="false">Tidak</option>
                    <option :value="true">Ya</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Mata cekung</label>
                  <select v-model="form.mata_cekung" class="form-select">
                    <option value="">Pilih jawaban</option>
                    <option value="ya">Ya</option>
                    <option value="tidak">Tidak</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Cubitan kulit perut</label>
                  <select v-model="form.turgor_kulit" class="form-select">
                    <option value="">Pilih kondisi</option>
                    <option value="normal">Kembali segera</option>
                    <option value="lambat">Kembali lambat</option>
                    <option value="sangat_lambat">Kembali sangat lambat</option>
                  </select>
                </div>
              </div>

              <div class="small-note mt-3">
                Dehidrasi berat: minimal 2 tanda dari bergerak hanya jika dirangsang/tidak bergerak,
                mata cekung, dan cubitan sangat lambat. Dehidrasi ringan/sedang: minimal 2 tanda dari
                gelisah/rewel, mata cekung, dan cubitan lambat.
              </div>
            </section>
          </div>

          <div class="col-xl-5">
            <section class="section-card jaundice-section">
              <div class="section-header">
                <span class="section-number warning-number">4</span>
                <div>
                  <h6 class="section-title mb-1">Ikterus</h6>
                  <div class="section-description">
                    Warna kuning, waktu mulai terlihat, dan penyebaran sampai telapak.
                  </div>
                </div>
              </div>

              <div class="check-grid one-column mb-3">
                <label class="check-card warning-card" :class="{ selected: form.ikterus }">
                  <input v-model="form.ikterus" type="checkbox" class="form-check-input" />
                  <span>
                    <span class="check-title">Bayi tampak kuning</span>
                    <span class="check-description">Kulit atau sklera tampak kuning.</span>
                  </span>
                </label>

                <label
                  class="check-card danger-card"
                  :class="{ selected: form.ikterus_telapak, disabled: !form.ikterus }"
                >
                  <input
                    v-model="form.ikterus_telapak"
                    type="checkbox"
                    class="form-check-input"
                    :disabled="!form.ikterus"
                  />
                  <span>
                    <span class="check-title">Kuning sampai telapak tangan atau kaki</span>
                    <span class="check-description">Termasuk tanda ikterus berat.</span>
                  </span>
                </label>
              </div>

              <label class="form-label">Umur bayi saat mulai kuning</label>
              <div class="input-group">
                <input
                  v-model.number="form.umur_mulai_kuning_jam"
                  type="number"
                  min="0"
                  max="2000"
                  step="1"
                  class="form-control"
                  placeholder="Contoh: 18"
                  :disabled="!form.ikterus"
                />
                <span class="input-group-text">Jam</span>
              </div>
              <small class="form-hint">
                Mulai &lt;24 jam, kuning sampai telapak, atau masih kuning pada umur &gt;14 hari
                termasuk ikterus berat.
              </small>
            </section>

            <section class="section-card feeding-section mt-3">
              <div class="section-header">
                <span class="section-number success-number">5</span>
                <div>
                  <h6 class="section-title mb-1">Berat Badan Menurut Umur</h6>
                  <div class="section-description">
                    Assessment menggunakan BB/U, bukan status BB/TB.
                  </div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Berat badan</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.bb"
                      type="number"
                      min="0"
                      max="30"
                      step="0.01"
                      class="form-control"
                      placeholder="Contoh: 2.80"
                    />
                    <span class="input-group-text">kg</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Status BB/U</label>
                  <select v-model="form.status_bb_u" class="form-select">
                    <option value="">Pilih status</option>
                    <option value="normal">Tidak rendah</option>
                    <option value="rendah">Rendah</option>
                    <option value="sangat_rendah">Sangat rendah</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Z-score BB/U</label>
                  <input
                    v-model.number="form.zscore_bb_u"
                    type="number"
                    min="-10"
                    max="10"
                    step="0.01"
                    class="form-control"
                    placeholder="Contoh: -2.15"
                  />
                </div>

                <div class="col-md-6">
                  <label class="form-label">Umur pasien</label>
                  <div class="readonly-box">
                    {{ umurHari === null ? '-' : `${umurHari} hari` }}
                  </div>
                </div>
              </div>

              <div v-if="bbSangatRendahPotensial" class="alert alert-danger mt-3 mb-0 py-2">
                Berat badan &lt;2 kg pada umur &lt;7 hari termasuk berat badan sangat rendah menurut umur.
              </div>
            </section>

            <section v-if="!jalurPemberianMinum" class="section-card feeding-section mt-3">
              <div class="section-header">
                <span class="section-number success-number">6</span>
                <div>
                  <h6 class="section-title mb-1">Pemeriksaan Pemberian ASI</h6>
                  <div class="section-description">
                    Jalur ini digunakan pada bayi yang mendapat ASI atau bukan anak dari ibu HIV positif.
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Frekuensi ASI selama 24 jam</label>
                <div class="input-group">
                  <input
                    v-model.number="form.frekuensi_asi_24_jam"
                    type="number"
                    min="0"
                    max="100"
                    step="1"
                    class="form-control"
                    placeholder="Contoh: 10"
                  />
                  <span class="input-group-text">Kali</span>
                </div>
                <small class="form-hint">Kurang dari 8 kali per hari termasuk masalah pemberian ASI.</small>
              </div>

              <div class="check-grid one-column">
                <label
                  v-for="item in masalahAsiList"
                  :key="item.key"
                  class="check-card warning-card"
                  :class="{ selected: form[item.key] }"
                >
                  <input v-model="form[item.key]" type="checkbox" class="form-check-input" />
                  <span>
                    <span class="check-title">{{ item.label }}</span>
                    <span class="check-description">{{ item.description }}</span>
                  </span>
                </label>
              </div>
            </section>

            <section v-else class="section-card replacement-section mt-3">
              <div class="section-header">
                <span class="section-number warning-number">6</span>
                <div>
                  <h6 class="section-title mb-1">Pemeriksaan Pemberian Minum</h6>
                  <div class="section-description">
                    Jalur khusus ibu HIV positif ketika bayi tidak mendapat ASI.
                  </div>
                </div>
              </div>

              <div class="subjective-summary mb-3">
                <div><strong>Jenis:</strong> {{ subjectiveData?.jenis_susu_pengganti || '-' }}</div>
                <div><strong>Frekuensi:</strong> {{ subjectiveData?.frekuensi_minum_24_jam ?? '-' }} kali/24 jam</div>
                <div><strong>Jumlah:</strong> {{ subjectiveData?.jumlah_minum_per_kali_ml ?? '-' }} mL/kali</div>
              </div>

              <div class="check-grid one-column">
                <label
                  v-for="item in masalahMinumList"
                  :key="item.key"
                  class="check-card warning-card"
                  :class="{ selected: form[item.key] }"
                >
                  <input v-model="form[item.key]" type="checkbox" class="form-check-input" />
                  <span>
                    <span class="check-title">{{ item.label }}</span>
                    <span class="check-description">{{ item.description }}</span>
                  </span>
                </label>
              </div>
            </section>
          </div>
        </div>

        <div class="status-card mt-3" :class="statusClass">
          <div class="status-icon">{{ statusIcon }}</div>
          <div class="flex-grow-1">
            <div class="status-label">Ringkasan pemeriksaan</div>
            <div class="status-value">{{ statusRingkas }}</div>

            <div v-if="temuanRingkas.length" class="status-findings mt-2">
              <span v-for="item in temuanRingkas" :key="item" class="finding-chip">
                {{ item }}
              </span>
            </div>

            <div class="status-description mt-2">
              Ringkasan ini hanya membantu melihat data yang telah diisi. Klasifikasi resmi dibuat
              oleh endpoint Assessment MTBM setelah Subjektif dan Objektif tersimpan.
            </div>
          </div>
        </div>
      </div>

      <div class="form-footer">
        <div class="footer-information">
          Simpan Objektif, kemudian jalankan Assessment otomatis menggunakan data terbaru.
        </div>

        <div class="footer-actions">
          <button
            type="button"
            class="btn btn-outline-danger reset-testing-button"
            :disabled="loading || deleting || !idPelayanan"
            @click="hapusDataTesting"
          >
            <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
            {{ deleting ? 'Mereset data...' : 'Reset Data Testing MTBM' }}
          </button>

          <button
            type="submit"
            class="btn btn-success save-button"
            :disabled="loading || deleting || !idPelayanan || (umurHari !== null && umurHari >= 60)"
          >
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            {{ loading ? 'Menyimpan...' : 'Simpan Objektif MTBM' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import {
  computed,
  onBeforeUnmount,
  onMounted,
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
    default: null,
  },
})

const loading = ref(false)
const deleting = ref(false)
const message = ref('')
const messageType = ref('success')
const subjectiveData = ref(null)
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

const dataPasienAktif = computed(() => (
  props.DataPasien?.[0]
  || page.props?.DataPasien?.[0]
  || page.props?.pelayanan
  || {}
))

const umurHari = computed(() => {
  const pasien = dataPasienAktif.value

  // Gunakan total umur hari bila controller sudah menyediakannya.
  const directTotal = [
    pasien.umur_hari_total,
    pasien.umurHariTotal,
  ].find((value) => value !== undefined && value !== null && value !== '')

  if (directTotal !== undefined) {
    const numberValue = Number(directTotal)
    return Number.isFinite(numberValue) ? numberValue : null
  }

  // Jika tanggal lahir tersedia, hitung total hari secara langsung.
  const tanggalLahir = pasien.TGL_LHR || pasien.tgl_lahir || pasien.tanggal_lahir
  const tanggalKunjungan = pasien.tglKunjungan || pasien.tgl_kunjungan || new Date()

  if (tanggalLahir) {
    const lahir = new Date(tanggalLahir)
    const kunjungan = new Date(tanggalKunjungan)

    if (!Number.isNaN(lahir.getTime()) && !Number.isNaN(kunjungan.getTime())) {
      const diff = Math.floor((kunjungan.getTime() - lahir.getTime()) / 86400000)
      return diff >= 0 ? diff : null
    }
  }

  // Fallback untuk payload lama yang hanya mengirim umur tahun/bulan/sisa hari.
  // Ini mencegah bayi 1 bulan ditampilkan sebagai 0 hari.
  const tahun = Number(pasien.umur ?? pasien.umur_tahun ?? 0)
  const bulan = Number(pasien.umur_bulan ?? pasien.umurBulan ?? 0)
  const hari = Number(pasien.umur_hari ?? pasien.umurHari ?? 0)

  if ([tahun, bulan, hari].some((value) => Number.isFinite(value) && value > 0)) {
    return (Number.isFinite(tahun) ? tahun * 365 : 0)
      + (Number.isFinite(bulan) ? bulan * 30 : 0)
      + (Number.isFinite(hari) ? hari : 0)
  }

  return null
})

const form = reactive({
  kesadaran: '',
  rr: null,
  rr_ulang: null,
  suhu: null,
  spo2_tangan_kanan: null,
  spo2_kaki_kiri: null,

  biru_sekitar_mulut: false,
  merintih: false,
  napas_cuping_hidung: false,
  tarikan_dinding_dada_sangat_kuat: false,
  lemah_tidak_mau_mengisap: false,
  kejang_saat_ini: false,
  tidak_bab_48_jam: false,
  muntah_susu_atau_hijau: false,
  perut_kembung_sulit_bernapas: false,
  tidak_ada_lubang_anus: false,
  feses_lubang_abnormal: false,
  mata_bernanah_banyak: false,
  pusar_bernanah: false,
  pusar_kemerahan_meluas: false,

  mata_bernanah_sedikit: false,
  pusar_kemerahan: false,
  pustul_kulit: false,

  bergerak_hanya_dirangsang: false,
  tidak_bergerak: false,
  gelisah_rewel: false,
  mata_cekung: '',
  turgor_kulit: '',

  ikterus: false,
  ikterus_telapak: false,
  umur_mulai_kuning_jam: null,

  bb: null,
  status_bb_u: '',
  zscore_bb_u: null,
  frekuensi_asi_24_jam: null,
  menggunakan_botol: false,
  makanan_minuman_lain: false,
  posisi_menyusu_salah: false,
  perlekatan_tidak_baik: false,
  mengisap_tidak_efektif: false,
  thrush: false,
  celah_bibir_langit: false,

  minuman_pengganti_tidak_sesuai: false,
  jumlah_minuman_tidak_adekuat: false,
  penyiapan_minuman_tidak_higienis: false,
})

const tandaBeratList = [
  {
    key: 'biru_sekitar_mulut',
    label: 'Biru sekitar mulut',
    description: 'Terlihat saat bayi menangis atau mengisap.',
  },
  {
    key: 'merintih',
    label: 'Merintih',
    description: 'Terdengar suara merintih saat bernapas.',
  },
  {
    key: 'napas_cuping_hidung',
    label: 'Napas cuping hidung',
    description: 'Cuping hidung tampak kembang-kempis.',
  },
  {
    key: 'tarikan_dinding_dada_sangat_kuat',
    label: 'Tarikan dinding dada sangat kuat',
    description: 'Hanya tarikan yang sangat kuat yang masuk rule merah MTBM.',
  },
  {
    key: 'lemah_tidak_mau_mengisap',
    label: 'Lemah atau tidak kuat mengisap',
    description: 'Bayi lemah, gerakan tidak kuat, atau tidak kuat mengisap.',
  },
  {
    key: 'kejang_saat_ini',
    label: 'Kejang saat diperiksa',
    description: 'Gerakan kejang yang ditemukan saat pemeriksaan.',
  },
  {
    key: 'tidak_bab_48_jam',
    label: 'Tidak BAB dalam 48 jam setelah lahir',
    description: 'Tanda kemungkinan sumbatan saluran cerna.',
  },
  {
    key: 'muntah_susu_atau_hijau',
    label: 'Muntah berisi susu atau cairan hijau',
    description: 'Tanda muntah khusus pada penilaian bayi muda.',
  },
  {
    key: 'perut_kembung_sulit_bernapas',
    label: 'Perut kembung dan sulit bernapas',
    description: 'Tanda kemungkinan sumbatan saluran cerna.',
  },
  {
    key: 'tidak_ada_lubang_anus',
    label: 'Tidak ditemukan lubang anus',
    description: 'Lubang anus tidak ditemukan saat pemeriksaan.',
  },
  {
    key: 'feses_lubang_abnormal',
    label: 'Feses keluar dari lubang abnormal',
    description: 'Feses keluar melalui lubang selain anus.',
  },
  {
    key: 'mata_bernanah_banyak',
    label: 'Mata bernanah banyak',
    description: 'Sekret mata banyak termasuk tanda infeksi berat.',
  },
  {
    key: 'pusar_bernanah',
    label: 'Pusar bernanah',
    description: 'Terdapat nanah pada umbilikus.',
  },
  {
    key: 'pusar_kemerahan_meluas',
    label: 'Kemerahan pusar meluas',
    description: 'Kemerahan meluas ke kulit perut lebih dari 1 cm.',
  },
]

const infeksiLokalList = [
  {
    key: 'mata_bernanah_sedikit',
    label: 'Mata bernanah sedikit',
    description: 'Sekret sedikit tanpa tanda infeksi berat.',
  },
  {
    key: 'pusar_kemerahan',
    label: 'Pusar kemerahan lokal',
    description: 'Kemerahan tidak meluas lebih dari 1 cm.',
  },
  {
    key: 'pustul_kulit',
    label: 'Pustul kulit',
    description: 'Terdapat pustul atau infeksi lokal pada kulit.',
  },
]

const masalahAsiList = [
  {
    key: 'menggunakan_botol',
    label: 'ASI atau minuman diberikan menggunakan botol',
    description: 'Botol meningkatkan risiko kontaminasi dan perlu diganti dengan cangkir.',
  },
  {
    key: 'makanan_minuman_lain',
    label: 'Mendapat makanan atau minuman selain ASI',
    description: 'Termasuk pemberian campuran pada bayi muda.',
  },
  {
    key: 'posisi_menyusu_salah',
    label: 'Posisi menyusu salah',
    description: 'Posisi tubuh bayi saat menyusu tidak benar.',
  },
  {
    key: 'perlekatan_tidak_baik',
    label: 'Perlekatan tidak baik',
    description: 'Bayi tidak melekat dengan baik atau tidak melekat sama sekali.',
  },
  {
    key: 'mengisap_tidak_efektif',
    label: 'Mengisap tidak efektif',
    description: 'Isapan cepat dan dangkal atau tidak terdengar menelan.',
  },
  {
    key: 'thrush',
    label: 'Bercak putih atau thrush di mulut',
    description: 'Bercak putih yang tidak mudah dibersihkan.',
  },
  {
    key: 'celah_bibir_langit',
    label: 'Celah bibir atau langit-langit',
    description: 'Kelainan yang dapat mengganggu proses minum.',
  },
]

const masalahMinumList = [
  {
    key: 'minuman_pengganti_tidak_sesuai',
    label: 'Jenis minuman pengganti tidak sesuai',
    description: 'Jenis susu atau minuman tidak sesuai untuk bayi muda.',
  },
  {
    key: 'jumlah_minuman_tidak_adekuat',
    label: 'Jumlah atau frekuensi minum tidak adekuat',
    description: 'Jumlah per kali atau frekuensi 24 jam tidak memenuhi kebutuhan bayi.',
  },
  {
    key: 'penyiapan_minuman_tidak_higienis',
    label: 'Penyiapan minuman tidak higienis',
    description: 'Air, alat, tangan, penyimpanan, atau cara penyiapan tidak aman.',
  },
  {
    key: 'menggunakan_botol',
    label: 'Minuman diberikan menggunakan botol',
    description: 'Anjurkan pemberian memakai cangkir yang bersih.',
  },
  {
    key: 'thrush',
    label: 'Bercak putih atau thrush di mulut',
    description: 'Dapat mengganggu kemampuan bayi minum.',
  },
  {
    key: 'celah_bibir_langit',
    label: 'Celah bibir atau langit-langit',
    description: 'Dapat membutuhkan cara pemberian minum khusus.',
  },
]

const boolFields = [
  'biru_sekitar_mulut',
  'merintih',
  'napas_cuping_hidung',
  'tarikan_dinding_dada_sangat_kuat',
  'lemah_tidak_mau_mengisap',
  'kejang_saat_ini',
  'tidak_bab_48_jam',
  'muntah_susu_atau_hijau',
  'perut_kembung_sulit_bernapas',
  'tidak_ada_lubang_anus',
  'feses_lubang_abnormal',
  'mata_bernanah_banyak',
  'pusar_bernanah',
  'pusar_kemerahan_meluas',
  'mata_bernanah_sedikit',
  'pusar_kemerahan',
  'pustul_kulit',
  'bergerak_hanya_dirangsang',
  'tidak_bergerak',
  'gelisah_rewel',
  'ikterus',
  'ikterus_telapak',
  'menggunakan_botol',
  'makanan_minuman_lain',
  'posisi_menyusu_salah',
  'perlekatan_tidak_baik',
  'mengisap_tidak_efektif',
  'thrush',
  'celah_bibir_langit',
  'minuman_pengganti_tidak_sesuai',
  'jumlah_minuman_tidak_adekuat',
  'penyiapan_minuman_tidak_higienis',
]

const numericFields = [
  'rr',
  'rr_ulang',
  'suhu',
  'spo2_tangan_kanan',
  'spo2_kaki_kiri',
  'umur_mulai_kuning_jam',
  'bb',
  'zscore_bb_u',
  'frekuensi_asi_24_jam',
]

const toBool = (value) => (
  value === true
  || value === 1
  || value === '1'
  || value === 'true'
  || value === 'ya'
)

const nullableBool = (value) => {
  if (value === null || value === undefined || value === '') return null
  return toBool(value)
}

const jalurPemberianMinum = computed(() => (
  subjectiveData.value?.status_hiv_ibu === 'positif'
  && nullableBool(subjectiveData.value?.bayi_mendapat_asi) === false
))

const subjektifAdaDiare = computed(() => {
  const data = subjectiveData.value
  if (!data) return null

  // Jawaban eksplisit selalu diprioritaskan, termasuk nilai false/0.
  const jawabanEksplisit = nullableBool(data.ada_diare)
  if (jawabanEksplisit !== null) return jawabanEksplisit

  // Kompatibilitas instalasi lama: bila kolom ada_diare belum tersedia,
  // lama diare > 0 tetap menandakan bahwa diare sudah dicatat.
  if (
    data.diare_lama_hari !== null
    && data.diare_lama_hari !== undefined
    && data.diare_lama_hari !== ''
  ) {
    const lamaDiare = Number(data.diare_lama_hari)
    if (Number.isFinite(lamaDiare)) return lamaDiare > 0
  }

  return null
})

const selisihSpo2 = computed(() => {
  const kanan = Number(form.spo2_tangan_kanan)
  const kaki = Number(form.spo2_kaki_kiri)

  if (!Number.isFinite(kanan) || !Number.isFinite(kaki) || kanan <= 0 || kaki <= 0) {
    return null
  }

  return Math.abs(kanan - kaki)
})

const selisihSpo2Bahaya = computed(() => (
  selisihSpo2.value !== null && selisihSpo2.value > 3
))

const rrDinilai = computed(() => {
  const ulang = Number(form.rr_ulang)
  if (Number.isFinite(ulang) && ulang > 0) return ulang

  const pertama = Number(form.rr)
  return Number.isFinite(pertama) && pertama > 0 ? pertama : null
})

const rrPerluDiulang = computed(() => {
  const pertama = Number(form.rr)
  return Number.isFinite(pertama) && pertama >= 60
})

const rrUlangTerisi = computed(() => {
  const ulang = Number(form.rr_ulang)
  return Number.isFinite(ulang) && ulang > 0
})

const rrBahaya = computed(() => (
  rrDinilai.value !== null
  && (rrDinilai.value >= 60 || rrDinilai.value < 40)
))

const suhuBahaya = computed(() => {
  const suhu = Number(form.suhu)
  return Number.isFinite(suhu) && suhu > 0 && (suhu > 37.5 || suhu < 36.5)
})

const spo2Bahaya = computed(() => (
  [form.spo2_tangan_kanan, form.spo2_kaki_kiri].some((value) => {
    const numberValue = Number(value)
    return Number.isFinite(numberValue) && numberValue > 0 && numberValue < 95
  })
))

const dehidrasiBeratPotensial = computed(() => {
  let total = 0
  if (form.bergerak_hanya_dirangsang || form.tidak_bergerak) total += 1
  if (form.mata_cekung === 'ya') total += 1
  if (form.turgor_kulit === 'sangat_lambat') total += 1
  return total >= 2
})

const dehidrasiRinganPotensial = computed(() => {
  let total = 0
  if (form.gelisah_rewel) total += 1
  if (form.mata_cekung === 'ya') total += 1
  if (form.turgor_kulit === 'lambat') total += 1
  return total >= 2
})

const ikterusBeratPotensial = computed(() => {
  if (!form.ikterus) return false

  const mulaiJam = Number(form.umur_mulai_kuning_jam)
  const mulaiHariPertama = Number.isFinite(mulaiJam) && mulaiJam >= 0 && mulaiJam < 24
  const lebih14Hari = umurHari.value !== null && umurHari.value > 14

  return form.ikterus_telapak || mulaiHariPertama || lebih14Hari
})

const bbSangatRendahPotensial = computed(() => {
  const bb = Number(form.bb)
  return (
    Number.isFinite(bb)
    && bb > 0
    && bb < 2
    && umurHari.value !== null
    && umurHari.value < 7
  )
})

const bbRendahPotensial = computed(() => (
  ['rendah', 'sangat_rendah'].includes(form.status_bb_u)
  || (
    Number.isFinite(Number(form.zscore_bb_u))
    && form.zscore_bb_u !== null
    && Number(form.zscore_bb_u) < -2
  )
))

const masalahAsi = computed(() => {
  const frekuensi = Number(form.frekuensi_asi_24_jam)
  const kurang8 = form.frekuensi_asi_24_jam !== null
    && Number.isFinite(frekuensi)
    && frekuensi >= 0
    && frekuensi < 8

  return kurang8 || masalahAsiList.some((item) => form[item.key])
})

const masalahMinum = computed(() => (
  masalahMinumList.some((item) => form[item.key])
))

const temuanMerah = computed(() => {
  const findings = []

  if (form.biru_sekitar_mulut) findings.push('Biru sekitar mulut')
  if (spo2Bahaya.value) findings.push('SpO₂ <95%')
  if (selisihSpo2Bahaya.value) findings.push('Selisih SpO₂ >3%')
  if (rrBahaya.value) findings.push('Frekuensi napas bahaya')
  if (form.merintih) findings.push('Merintih')
  if (form.napas_cuping_hidung) findings.push('Napas cuping hidung')
  if (form.tarikan_dinding_dada_sangat_kuat) findings.push('Tarikan dada sangat kuat')
  if (form.lemah_tidak_mau_mengisap) findings.push('Lemah/tidak kuat mengisap')
  if (form.kejang_saat_ini) findings.push('Kejang')
  if (suhuBahaya.value) findings.push('Suhu bahaya')
  if (form.tidak_bab_48_jam) findings.push('Tidak BAB 48 jam')
  if (form.muntah_susu_atau_hijau) findings.push('Muntah susu/cairan hijau')
  if (form.perut_kembung_sulit_bernapas) findings.push('Perut kembung dan sulit bernapas')
  if (form.tidak_ada_lubang_anus) findings.push('Tidak ada lubang anus')
  if (form.feses_lubang_abnormal) findings.push('Feses dari lubang abnormal')
  if (form.mata_bernanah_banyak) findings.push('Mata bernanah banyak')
  if (form.pusar_bernanah) findings.push('Pusar bernanah')
  if (form.pusar_kemerahan_meluas) findings.push('Kemerahan pusar meluas')
  if (subjektifAdaDiare.value && dehidrasiBeratPotensial.value) findings.push('Tanda dehidrasi berat')
  if (ikterusBeratPotensial.value) findings.push('Tanda ikterus berat')
  if (bbSangatRendahPotensial.value) findings.push('BB <2 kg pada umur <7 hari')

  return findings
})

const temuanKuning = computed(() => {
  const findings = []

  if (form.mata_bernanah_sedikit) findings.push('Mata bernanah sedikit')
  if (form.pusar_kemerahan) findings.push('Pusar kemerahan')
  if (form.pustul_kulit) findings.push('Pustul kulit')
  if (subjektifAdaDiare.value && dehidrasiRinganPotensial.value) {
    findings.push('Tanda dehidrasi ringan/sedang')
  }
  if (form.ikterus && !ikterusBeratPotensial.value) findings.push('Ikterus')
  if (bbRendahPotensial.value) findings.push('BB/U rendah')
  if (!jalurPemberianMinum.value && masalahAsi.value) findings.push('Masalah pemberian ASI')
  if (jalurPemberianMinum.value && masalahMinum.value) findings.push('Masalah pemberian minum')

  return findings
})

const statusRingkas = computed(() => {
  if (temuanMerah.value.length > 0) {
    return `${temuanMerah.value.length} temuan perlu perhatian segera`
  }

  if (temuanKuning.value.length > 0) {
    return `${temuanKuning.value.length} temuan perlu ditindaklanjuti`
  }

  return 'Belum ada temuan objektif menonjol'
})

const statusClass = computed(() => {
  if (temuanMerah.value.length > 0) return 'status-danger'
  if (temuanKuning.value.length > 0) return 'status-warning'
  return 'status-stable'
})

const statusIcon = computed(() => {
  if (temuanMerah.value.length > 0) return '!'
  if (temuanKuning.value.length > 0) return '•'
  return '✓'
})

const temuanRingkas = computed(() => (
  temuanMerah.value.length > 0 ? temuanMerah.value : temuanKuning.value
))

const syncGerakanFromKesadaran = () => {
  form.bergerak_hanya_dirangsang = form.kesadaran === 'letargi'
  form.tidak_bergerak = form.kesadaran === 'tidak_sadar'
}

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
    return 'Data Objektif MTBM belum valid.'
  }

  return Object.values(errors).flat().find(Boolean)
    || 'Data Objektif MTBM belum valid.'
}

const applyData = (data) => {
  if (!data) return

  Object.keys(form).forEach((key) => {
    if (data[key] === undefined) return

    if (boolFields.includes(key)) {
      form[key] = toBool(data[key])
      return
    }

    if (numericFields.includes(key)) {
      if (data[key] === null || data[key] === '') {
        form[key] = null
        return
      }

      const numberValue = Number(data[key])
      form[key] = Number.isFinite(numberValue) ? numberValue : null
      return
    }

    form[key] = data[key] ?? ''
  })

  if (form.tidak_bergerak) {
    form.kesadaran = 'tidak_sadar'
  } else if (form.bergerak_hanya_dirangsang) {
    form.kesadaran = 'letargi'
  }
}

const buildPayload = () => {
  const payload = {
    kunjungan_id: String(idPelayanan.value),
    statusRingkas: statusRingkas.value,
  }

  Object.keys(form).forEach((key) => {
    if (boolFields.includes(key)) {
      payload[key] = form[key] ? 1 : 0
      return
    }

    if (numericFields.includes(key)) {
      payload[key] = form[key] === '' || form[key] === undefined
        ? null
        : form[key]
      return
    }

    payload[key] = form[key] === '' ? null : form[key]
  })

  return payload
}

const requestHeaders = {
  Accept: 'application/json',
  'X-Requested-With': 'XMLHttpRequest',
}

const loadObjektifFromDb = async () => {
  if (!idPelayanan.value) return

  try {
    const response = await axios.get(
      `/simpus/kia/mtbm/objektif/${encodeURIComponent(idPelayanan.value)}`,
      { headers: requestHeaders },
    )

    const data = response.data?.data ?? response.data ?? null
    if (data) applyData(data)
  } catch (error) {
    if (error.response?.status === 404) return

    console.error('LOAD OBJEKTIF MTBM ERROR:', error.response?.data || error)
    showMessage(
      error.response?.data?.message || 'Data Objektif MTBM gagal dimuat.',
      'error',
    )
  }
}

const loadSubjektifFromDb = async () => {
  if (!idPelayanan.value) {
    subjectiveData.value = null
    return
  }

  try {
    const response = await axios.get(
      `/simpus/kia/mtbm/subjektif/${encodeURIComponent(idPelayanan.value)}`,
      { headers: requestHeaders },
    )

    subjectiveData.value = response.data?.data ?? response.data ?? null
  } catch (error) {
    if (error.response?.status === 404) {
      subjectiveData.value = null
      return
    }

    subjectiveData.value = null
    console.error('LOAD SUBJEKTIF UNTUK OBJEKTIF MTBM ERROR:', error.response?.data || error)
    showMessage(
      error.response?.data?.message || 'Data Subjektif MTBM gagal dimuat.',
      'error',
    )
  }
}

const loadFromDb = async () => {
  if (!idPelayanan.value) return

  // Request dipisahkan agar kegagalan salah satu endpoint tidak menghalangi
  // data endpoint lain untuk tetap dimuat.
  await Promise.all([
    loadObjektifFromDb(),
    loadSubjektifFromDb(),
  ])
}

const handleSubjektifSaved = async (event) => {
  const savedKunjunganId = String(event?.detail?.kunjungan_id ?? '')

  if (
    savedKunjunganId
    && savedKunjunganId !== String(idPelayanan.value)
  ) {
    return
  }

  const savedData = event?.detail?.data
  if (savedData && typeof savedData === 'object') {
    subjectiveData.value = savedData
    return
  }

  await loadSubjektifFromDb()
}

const hapusDataTesting = async () => {
  if (!idPelayanan.value) {
    showMessage('ID pelayanan kosong atau tidak terbaca.', 'error')
    return
  }

  if (deleting.value || loading.value) return

  const confirmed = window.confirm(
    'Reset data Subjektif, Objektif, Assessment, dan Gizi MTBM untuk kunjungan ini?\n\n'
      + 'Data pasien, loket, dan pelayanan tetap ada sehingga bisa langsung dipakai testing ulang.',
  )

  if (!confirmed) return

  deleting.value = true
  message.value = ''

  try {
    const response = await axios.delete(
      `/simpus/kia/mtbm/testing/hapus/${encodeURIComponent(idPelayanan.value)}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    const totalDeleted = Number(response.data?.data?.total_deleted || 0)

    window.alert(
      `${response.data?.message || 'Data testing MTBM berhasil direset.'}\n`
        + `Total baris terhapus: ${totalDeleted}`,
    )

    // Tetap di kunjungan pasien yang sama, tetapi semua tab membaca ulang DB yang kosong.
    window.location.reload()
  } catch (error) {
    console.error('RESET DATA TESTING MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 419) {
      showMessage('Sesi login telah habis. Login ulang lalu coba kembali.', 'error')
      return
    }

    showMessage(
      error.response?.data?.message || 'Gagal mereset data testing MTBM.',
      'error',
    )
  } finally {
    deleting.value = false
  }
}

const simpan = async () => {
  if (deleting.value) return

  if (!idPelayanan.value) {
    showMessage('ID pelayanan kosong atau tidak terbaca.', 'error')
    return
  }

  if (umurHari.value !== null && umurHari.value >= 60) {
    showMessage('Pasien umur 60 hari atau lebih harus menggunakan modul MTBS.', 'error')
    return
  }

  if (rrPerluDiulang.value && !rrUlangTerisi.value) {
    showMessage('Frekuensi napas pertama ≥60. Hitung ulang napas dan isi hasil ulang.', 'error')
    return
  }

  loading.value = true
  message.value = ''

  try {
    const response = await axios.post(
      '/simpus/kia/mtbm/objektif/store',
      buildPayload(),
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    applyData(response.data?.data)
    showMessage(
      response.data?.message || 'Objektif MTBM berhasil disimpan.',
      'success',
    )
  } catch (error) {
    console.error('SAVE OBJEKTIF MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 419) {
      showMessage('Sesi login telah habis. Login ulang lalu coba kembali.', 'error')
      return
    }

    if (error.response?.status === 422) {
      showMessage(getValidationMessage(error.response?.data?.errors), 'error')
      return
    }

    showMessage(
      error.response?.data?.message || 'Objektif MTBM gagal disimpan.',
      'error',
    )
  } finally {
    loading.value = false
  }
}

watch(
  () => form.ikterus,
  (value) => {
    if (!value) {
      form.ikterus_telapak = false
      form.umur_mulai_kuning_jam = null
    }
  },
)

watch(
  () => form.rr,
  (value) => {
    const numberValue = Number(value)
    if (!Number.isFinite(numberValue) || numberValue < 60) {
      form.rr_ulang = null
    }
  },
)

watch(
  idPelayanan,
  (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
      subjectiveData.value = null
      loadFromDb()
    }
  },
  { immediate: true },
)

onMounted(() => {
  window.addEventListener(
    MTBM_SUBJEKTIF_SAVED_EVENT,
    handleSubjektifSaved,
  )
})

onBeforeUnmount(() => {
  window.removeEventListener(
    MTBM_SUBJEKTIF_SAVED_EVENT,
    handleSubjektifSaved,
  )

  if (messageTimer) window.clearTimeout(messageTimer)
})
</script>

<style scoped>
.mtbm-objective {
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

.section-card {
  padding: 20px;
  border: 1px solid #dfe6e2;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: 0 2px 7px rgba(0, 0, 0, 0.03);
}

.danger-section { border-color: #f1aeb5; }
.local-section,
.jaundice-section { border-color: #ffe69c; }
.diarrhea-section { border-color: #9eeaf9; }
.feeding-section { border-color: #a3cfbb; }
.replacement-section { border-color: #ffda6a; background: #fffdf4; }

.section-header {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 18px;
}

.section-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 34px;
  width: 34px;
  height: 34px;
  border-radius: 8px;
  color: #ffffff;
  font-size: 14px;
  font-weight: 700;
}

.danger-number { background: #dc3545; }
.warning-number { color: #664d03; background: #ffc107; }
.info-number { color: #055160; background: #6edff6; }
.success-number { background: #198754; }

.section-title {
  color: #35423b;
  font-size: 16px;
  font-weight: 750;
}

.section-description {
  color: #7a8480;
  font-size: 12px;
  line-height: 1.45;
}

.vital-panel {
  padding: 16px;
  border: 1px solid #e2e8e5;
  border-radius: 10px;
  background: #fafcfb;
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
  min-height: 43px;
  border-color: #d5ded9;
  font-size: 14px;
}

.form-control:focus,
.form-select:focus {
  border-color: #75b798;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
}

.form-control.is-warning {
  border-color: #ffc107;
  background: #fffdf3;
}

.input-group-text {
  min-width: 64px;
  justify-content: center;
  color: #66716b;
  background: #f3f6f4;
  font-weight: 600;
}

.form-hint {
  display: block;
  margin-top: 5px;
  color: #7c8581;
  font-size: 11px;
  line-height: 1.35;
}

.readonly-box {
  display: flex;
  align-items: center;
  min-height: 43px;
  padding: 9px 12px;
  border: 1px solid #d5ded9;
  border-radius: 6px;
  color: #47534d;
  background: #f3f6f4;
  font-weight: 700;
}

.readonly-box.danger {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.check-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.compact-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.one-column {
  grid-template-columns: 1fr;
}

.check-card {
  display: flex;
  align-items: flex-start;
  gap: 11px;
  min-height: 76px;
  margin: 0;
  padding: 13px 14px;
  border: 1px solid #dce4df;
  border-radius: 9px;
  background: #ffffff;
  cursor: pointer;
  transition: 0.15s ease;
}

.check-card:hover {
  border-color: #8fbca4;
  background: #f8fcfa;
}

.check-card.selected.danger-card,
.danger-card.selected {
  border-color: #dc3545;
  background: #fff0f1;
  box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.05);
}

.check-card.selected.warning-card,
.warning-card.selected {
  border-color: #e0a800;
  background: #fff8db;
}

.check-card.disabled {
  opacity: 0.58;
  cursor: not-allowed;
}

.check-card .form-check-input {
  flex: 0 0 auto;
  width: 18px;
  height: 18px;
  margin: 2px 0 0;
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

.subjective-status,
.subjective-summary {
  padding: 10px 12px;
  border: 1px solid #d6ded9;
  border-radius: 8px;
  color: #59655f;
  background: #f7f9f8;
  font-size: 12px;
}

.subjective-status.active {
  color: #055160;
  border-color: #9eeaf9;
  background: #cff4fc;
}

.subjective-summary {
  display: grid;
  gap: 4px;
}

.status-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 17px 20px;
  border: 1px solid;
  border-radius: 11px;
}

.status-stable {
  color: #0f5132;
  border-color: #a3cfbb;
  background: #d1e7dd;
}

.status-warning {
  color: #664d03;
  border-color: #ffecb5;
  background: #fff3cd;
}

.status-danger {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.status-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 36px;
  width: 36px;
  height: 36px;
  border: 2px solid currentColor;
  border-radius: 50%;
  font-size: 20px;
  font-weight: 800;
}

.status-label {
  margin-bottom: 2px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-value {
  font-size: 16px;
  font-weight: 750;
}

.status-findings {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.finding-chip {
  padding: 4px 8px;
  border: 1px solid currentColor;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.45);
  font-size: 10px;
  font-weight: 650;
}

.status-description {
  font-size: 11px;
  line-height: 1.45;
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

.footer-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 10px;
}

.save-button,
.reset-testing-button {
  min-width: 220px;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 650;
}

@media (max-width: 1199.98px) {
  .compact-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 991.98px) {
  .page-header,
  .form-footer {
    align-items: flex-start;
    flex-direction: column;
  }

  .footer-actions,
  .save-button,
  .reset-testing-button {
    width: 100%;
  }
}

@media (max-width: 767.98px) {
  .form-body {
    padding: 12px;
  }

  .section-card {
    padding: 16px;
  }

  .check-grid {
    grid-template-columns: 1fr;
  }
}
</style>
