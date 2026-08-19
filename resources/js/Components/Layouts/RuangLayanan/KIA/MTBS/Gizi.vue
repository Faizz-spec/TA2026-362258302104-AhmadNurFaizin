<template>
  <div class="gizi-page">
    <!-- HEADER -->
    <div class="card border-0 shadow-sm mb-3">
      <div
        class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3"
      >
        <div>
          <h4 class="fw-bold text-success mb-1">
            Gizi MTBS
          </h4>

          <p class="text-muted mb-0">
            Antropometri diambil otomatis dari Objektif. Klasifikasi mengikuti bagan MTBS 2022.
          </p>
        </div>

        <div class="d-flex gap-2">
          <button
            type="button"
            class="btn btn-outline-secondary"
            :disabled="loading"
            @click="loadData"
          >
            {{ loading ? 'Memuat...' : 'Refresh Data' }}
          </button>

          <button
            type="button"
            class="btn btn-success"
            :disabled="saving || loading || !kunjunganId || !data.data_objektif_ada"
            @click="saveData"
          >
            {{ saving ? 'Menyimpan...' : 'Simpan Pemeriksaan Gizi' }}
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="!kunjunganId"
      class="alert alert-danger"
    >
      ID pelayanan tidak ditemukan.
    </div>

    <div
      v-if="message"
      class="alert"
      :class="messageType === 'success' ? 'alert-success' : 'alert-danger'"
    >
      {{ message }}
    </div>

    <div
      v-if="!loading && !data.data_objektif_ada"
      class="alert alert-warning"
    >
      Data Objektif belum tersedia. Isi BB, TB/PB, dan LiLA di menu Objektif terlebih dahulu.
    </div>

    <!-- DATA OTOMATIS -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="section-heading">
          <div>
            <h5 class="fw-bold mb-1">
              Data Antropometri
            </h5>

            <div class="text-muted small">
              Hanya ditampilkan di sini. Petugas tidak perlu mengetik ulang.
            </div>
          </div>

          <span class="source-badge">
            Dari Loket & Objektif
          </span>
        </div>

        <div
          v-if="loading"
          class="text-center text-muted py-4"
        >
          Memuat data antropometri...
        </div>

        <div
          v-else
          class="metric-grid"
        >
          <div class="metric-card">
            <span>Umur</span>
            <strong>{{ data.umur_label || '-' }}</strong>
          </div>

          <div class="metric-card">
            <span>Jenis kelamin</span>
            <strong>{{ data.jenis_kelamin_label || '-' }}</strong>
          </div>

          <div class="metric-card">
            <span>Berat badan</span>
            <strong>{{ formatMetric(data.bb, 'kg') }}</strong>
          </div>

          <div class="metric-card">
            <span>{{ data.indikator === 'wfl' ? 'Panjang badan' : 'Tinggi badan' }}</span>
            <strong>{{ formatMetric(data.tb, 'cm') }}</strong>
          </div>

          <div
            class="metric-card"
            :class="{ muted: umurDiBawahEnamBulan }"
          >
            <span>LiLA</span>
            <strong>
              {{
                umurDiBawahEnamBulan
                  ? 'Tidak digunakan (<6 bulan)'
                  : formatMetric(data.lila, 'cm')
              }}
            </strong>
          </div>

          <div class="metric-card zscore-card">
            <span>Z-score {{ data.indikator_label || 'BB/PB atau BB/TB' }}</span>

            <strong>
              {{ formatZScore(data.zscore) }}
            </strong>

            <small>
              Otomatis dari standar pertumbuhan WHO
            </small>
          </div>
        </div>

        <div
          v-if="data.zscore_error"
          class="alert alert-warning mt-3 mb-0"
        >
          {{ data.zscore_error }}
        </div>
      </div>
    </div>

    <!-- PEMERIKSAAN TAMBAHAN -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="section-heading">
          <div>
            <h5 class="fw-bold mb-1">
              Pemeriksaan Tambahan Gizi
            </h5>

            <div class="text-muted small">
              Isi hanya temuan yang belum dapat diambil otomatis dari Subjektif atau Objektif.
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-lg-6">
            <label class="form-label fw-semibold">
              Edema bilateral yang bersifat pitting
            </label>

            <select
              v-model="form.edema"
              class="form-select"
            >
              <option value="0">
                Tidak ada edema
              </option>

              <option value="+1">
                +1 — edema minimal pada kedua punggung kaki/tangan
              </option>

              <option value="+2">
                +2 — edema sedang
              </option>

              <option value="+3">
                +3 — edema pada seluruh tubuh
              </option>
            </select>
          </div>

          <div class="col-lg-6">
            <label
              class="check-card"
              :class="{
                selected:
                  form.komplikasi_medis
                  || adaKomplikasiOtomatis,
              }"
            >
              <input
                v-model="form.komplikasi_medis"
                type="checkbox"
                class="form-check-input"
                :disabled="adaKomplikasiOtomatis"
              >

              <span>
                <strong>
                  Disertai komplikasi medis
                </strong>

                <small v-if="adaKomplikasiOtomatis">
                  Sudah terdeteksi otomatis dari Subjektif, Objektif, atau Assessment.
                </small>

                <small v-else>
                  Centang bila terdapat komplikasi medis yang belum terdeteksi otomatis.
                </small>
              </span>
            </label>
          </div>

          <div class="col-lg-6">
            <label class="form-label fw-semibold">
              Syok
            </label>

            <select
              v-model="form.syok"
              class="form-select"
            >
              <option :value="false">
                Tidak
              </option>

              <option :value="true">
                Ya
              </option>
            </select>

            <small class="text-muted">
              Digunakan untuk menentukan tindakan cairan pra-rujukan.
            </small>
          </div>

          <template v-if="umurDiBawahEnamBulan">
            <div class="col-lg-6">
              <label
                class="check-card"
                :class="{
                  selected:
                    form.lemah_menyusu
                    || data.otomatis?.lemah_menyusu,
                }"
              >
                <input
                  v-model="form.lemah_menyusu"
                  type="checkbox"
                  class="form-check-input"
                  :disabled="data.otomatis?.lemah_menyusu"
                >

                <span>
                  <strong>
                    Terlalu lemah untuk menyusu
                  </strong>

                  <small v-if="data.otomatis?.lemah_menyusu">
                    Sudah terdeteksi dari pemeriksaan sebelumnya.
                  </small>

                  <small v-else>
                    Khusus anak umur kurang dari 6 bulan.
                  </small>
                </span>
              </label>
            </div>

            <div class="col-lg-6">
              <label
                class="check-card"
                :class="{
                  selected:
                    form.bb_tidak_naik
                    || data.otomatis?.bb_tidak_naik,
                }"
              >
                <input
                  v-model="form.bb_tidak_naik"
                  type="checkbox"
                  class="form-check-input"
                  :disabled="data.otomatis?.bb_tidak_naik"
                >

                <span>
                  <strong>
                    Berat badan tidak naik atau turun
                  </strong>

                  <small v-if="data.otomatis?.bb_tidak_naik">
                    Terdeteksi dari perbandingan berat kunjungan sebelumnya.
                  </small>

                  <small v-else-if="data.otomatis?.bb_sebelumnya !== null">
                    BB sebelumnya:
                    {{ formatMetric(data.otomatis.bb_sebelumnya, 'kg') }}
                  </small>

                  <small v-else>
                    Centang bila riwayat pertumbuhan menunjukkan tidak naik/turun.
                  </small>
                </span>
              </label>
            </div>
          </template>

          <div class="col-12">
            <label class="form-label fw-semibold">
              Catatan tambahan
            </label>

            <textarea
              v-model="form.catatan"
              rows="3"
              class="form-control"
              placeholder="Catatan pemeriksaan gizi bila diperlukan..."
            ></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- TEMUAN OTOMATIS -->
    <div
      v-if="adaTemuanOtomatis"
      class="card border-0 shadow-sm mb-3"
    >
      <div class="card-body">
        <h5 class="fw-bold mb-3">
          Temuan yang Diambil Otomatis
        </h5>

        <div class="auto-findings">
          <span
            v-for="item in data.otomatis?.komplikasi_medis || []"
            :key="item"
            class="finding-chip finding-danger"
          >
            {{ item }}
          </span>

          <span
            v-if="data.otomatis?.diare"
            class="finding-chip finding-warning"
          >
            Diare
          </span>

          <span
            v-if="data.otomatis?.lemah_menyusu"
            class="finding-chip finding-danger"
          >
            Terlalu lemah untuk menyusu
          </span>

          <span
            v-if="data.otomatis?.bb_tidak_naik"
            class="finding-chip finding-warning"
          >
            BB tidak naik/turun
          </span>
        </div>
      </div>
    </div>

    <!-- HASIL -->
    <div class="row g-3">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="fw-bold mb-3">
              Klasifikasi Otomatis
            </h5>

            <div
              class="result-box"
              :class="resultClass"
            >
              {{ hasilKlasifikasi.klasifikasi }}
            </div>

            <div class="mt-3">
              <div class="small fw-bold text-uppercase text-muted mb-2">
                Dasar klasifikasi
              </div>

              <ul
                v-if="hasilKlasifikasi.dasar.length"
                class="classification-reasons"
              >
                <li
                  v-for="item in hasilKlasifikasi.dasar"
                  :key="item"
                >
                  {{ item }}
                </li>
              </ul>

              <div
                v-else
                class="text-muted small"
              >
                Belum ada dasar klasifikasi.
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="row g-3 h-100">
          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="fw-bold mb-3 text-primary">
                  Tindakan
                </h5>

                <ul
                  v-if="rekomendasi.tindakan.length"
                  class="recommendation-list"
                >
                  <li
                    v-for="item in rekomendasi.tindakan"
                    :key="item"
                  >
                    {{ item }}
                  </li>
                </ul>

                <div
                  v-else
                  class="empty-result"
                >
                  Belum ada rekomendasi tindakan.
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="fw-bold mb-3 text-success">
                  Pengobatan
                </h5>

                <ul
                  v-if="rekomendasi.pengobatan.length"
                  class="recommendation-list"
                >
                  <li
                    v-for="item in rekomendasi.pengobatan"
                    :key="item"
                  >
                    {{ item }}
                  </li>
                </ul>

                <div
                  v-else
                  class="empty-result"
                >
                  Tidak ada pengobatan rutin khusus untuk klasifikasi ini.
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="alert alert-light border mb-0 small">
              Hasil gizi ini akan dibaca oleh Assessment dan rekomendasi Planning MTBS setelah disimpan.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  computed,
  reactive,
  ref,
  watch,
  onActivated,
  onBeforeUnmount,
  onMounted,
} from 'vue'

import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  DataPasien: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()

const loading = ref(false)
const saving = ref(false)
const message = ref('')
const messageType = ref('success')

const pasien = computed(() => {
  return (
    props.DataPasien?.[0]
    || page.props?.DataPasien?.[0]
    || {}
  )
})

const kunjunganId = computed(() => {
  return String(
    page.props?.idPelayanan
    || page.props?.idpelayanan
    || pasien.value?.idpelayanan
    || pasien.value?.idPelayanan
    || '',
  )
})

const emptyData = () => ({
  data_objektif_ada: false,
  umur_bulan: null,
  umur_label: null,
  jenis_kelamin: null,
  jenis_kelamin_label: null,
  bb: null,
  tb: null,
  lila: null,
  indikator: null,
  indikator_label: null,
  zscore: null,
  zscore_error: null,
  otomatis: {
    komplikasi_medis: [],
    lemah_menyusu: false,
    bb_tidak_naik: false,
    diare: false,
    bb_sebelumnya: null,
    tanggal_bb_sebelumnya: null,
  },
})

const data = reactive(emptyData())

const form = reactive({
  edema: '0',
  komplikasi_medis: false,
  lemah_menyusu: false,
  bb_tidak_naik: false,
  syok: false,
  catatan: '',
})

const normalizeNumber = (value) => {
  if (
    value === null
    || value === undefined
    || value === ''
  ) {
    return null
  }

  const numberValue = Number(value)

  return Number.isFinite(numberValue)
    ? numberValue
    : null
}

const umurDiBawahEnamBulan = computed(() => {
  const umur = normalizeNumber(data.umur_bulan)

  return umur !== null && umur < 6
})

const adaTemuanOtomatis = computed(() => {
  return (
    (data.otomatis?.komplikasi_medis?.length || 0) > 0
    || !!data.otomatis?.diare
    || !!data.otomatis?.lemah_menyusu
    || !!data.otomatis?.bb_tidak_naik
  )
})

const adaKomplikasiOtomatis = computed(() => {
  return (data.otomatis?.komplikasi_medis?.length || 0) > 0
})

const adaKomplikasiEfektif = computed(() => {
  return (
    form.komplikasi_medis
    || adaKomplikasiOtomatis.value
  )
})

const lemahMenyusuEfektif = computed(() => {
  return (
    form.lemah_menyusu
    || !!data.otomatis?.lemah_menyusu
  )
})

const bbTidakNaikEfektif = computed(() => {
  return (
    form.bb_tidak_naik
    || !!data.otomatis?.bb_tidak_naik
  )
})

const hitungKlasifikasiLokal = () => {
  const umur = normalizeNumber(data.umur_bulan)
  const bb = normalizeNumber(data.bb)
  const lila = normalizeNumber(data.lila)
  const z = normalizeNumber(data.zscore)
  const edema = form.edema

  if (
    umur === null
    || umur < 2
    || umur > 59
  ) {
    return {
      klasifikasi: 'Belum dapat diklasifikasikan',
      dasar: ['Umur harus berada pada rentang MTBS 2–59 bulan.'],
    }
  }

  if (bb === null || bb <= 0) {
    return {
      klasifikasi: 'Belum dapat diklasifikasikan',
      dasar: ['Berat badan pada Objektif belum tersedia.'],
    }
  }

  if (z === null) {
    return {
      klasifikasi: 'Belum dapat diklasifikasikan',
      dasar: ['Z-score BB/PB atau BB/TB belum dapat dihitung.'],
    }
  }

  if (umur < 6) {
    const dasar = []

    if (z < -3) {
      dasar.push('Skor Z BB/PB < -3 SD')
    }

    if (edema !== '0') {
      dasar.push('Ada edema bilateral pitting')
    }

    if (lemahMenyusuEfektif.value) {
      dasar.push('Terlalu lemah untuk menyusu')
    }

    if (bbTidakNaikEfektif.value) {
      dasar.push('Berat badan tidak naik atau turun')
    }

    if (adaKomplikasiEfektif.value) {
      dasar.push(
        adaKomplikasiOtomatis.value
          ? 'Terdapat komplikasi medis dari Assessment/Objektif'
          : 'Disertai komplikasi medis',
      )
    }

    if (dasar.length > 0) {
      return {
        klasifikasi: 'GIZI BURUK DENGAN KOMPLIKASI',
        dasar,
      }
    }

    if (z >= -3 && z < -2) {
      return {
        klasifikasi: 'GIZI KURANG',
        dasar: ['Skor Z BB/PB -3 SD sampai < -2 SD'],
      }
    }

    if (z >= -2 && z <= 1) {
      return {
        klasifikasi: 'GIZI BAIK',
        dasar: ['Skor Z BB/PB -2 SD sampai +1 SD'],
      }
    }

    if (z > 3) {
      return {
        klasifikasi: 'OBESITAS',
        dasar: ['Skor Z BB/PB > +3 SD'],
      }
    }

    if (z > 2) {
      return {
        klasifikasi: 'GIZI LEBIH',
        dasar: ['Skor Z BB/PB > +2 SD sampai +3 SD'],
      }
    }

    if (z > 1) {
      return {
        klasifikasi: 'BERISIKO GIZI LEBIH',
        dasar: ['Skor Z BB/PB > +1 SD sampai +2 SD'],
      }
    }
  }

  if (lila === null || lila <= 0) {
    return {
      klasifikasi: 'Belum dapat diklasifikasikan',
      dasar: ['LiLA pada Objektif belum tersedia untuk anak umur 6–59 bulan.'],
    }
  }

  const indikatorGiziBuruk = (
    z < -3
    || lila < 11.5
    || ['+1', '+2', '+3'].includes(edema)
  )

  if (edema === '+3') {
    return {
      klasifikasi: 'GIZI BURUK DENGAN KOMPLIKASI',
      dasar: ['Edema pada seluruh tubuh (derajat +3)'],
    }
  }

  if (bb < 4) {
    return {
      klasifikasi: 'GIZI BURUK DENGAN KOMPLIKASI',
      dasar: ['Berat badan < 4 kg pada umur 6–59 bulan'],
    }
  }

  if (
    indikatorGiziBuruk
    && adaKomplikasiEfektif.value
  ) {
    const dasar = []

    if (z < -3) {
      dasar.push('Skor Z BB/PB atau BB/TB < -3 SD')
    }

    if (lila < 11.5) {
      dasar.push('LiLA < 11,5 cm')
    }

    if (['+1', '+2'].includes(edema)) {
      dasar.push(`Edema bilateral pitting derajat ${edema}`)
    }

    dasar.push('Disertai komplikasi medis')

    return {
      klasifikasi: 'GIZI BURUK DENGAN KOMPLIKASI',
      dasar,
    }
  }

  if (indikatorGiziBuruk) {
    const dasar = []

    if (z < -3) {
      dasar.push('Skor Z BB/PB atau BB/TB < -3 SD')
    }

    if (lila < 11.5) {
      dasar.push('LiLA < 11,5 cm')
    }

    if (['+1', '+2'].includes(edema)) {
      dasar.push(`Edema minimal derajat ${edema}`)
    }

    return {
      klasifikasi: 'GIZI BURUK TANPA KOMPLIKASI',
      dasar,
    }
  }

  if (
    (z >= -3 && z < -2)
    || (lila >= 11.5 && lila < 12.5)
  ) {
    const dasar = []

    if (z >= -3 && z < -2) {
      dasar.push('Skor Z BB/PB atau BB/TB -3 SD sampai < -2 SD')
    }

    if (lila >= 11.5 && lila < 12.5) {
      dasar.push('LiLA 11,5 cm sampai < 12,5 cm')
    }

    return {
      klasifikasi: 'GIZI KURANG',
      dasar,
    }
  }

  if (
    z >= -2
    && z <= 1
    && lila >= 12.5
  ) {
    return {
      klasifikasi: 'GIZI BAIK',
      dasar: [
        'Skor Z BB/PB atau BB/TB -2 SD sampai +1 SD',
        'LiLA ≥ 12,5 cm',
      ],
    }
  }

  if (z > 3) {
    return {
      klasifikasi: 'OBESITAS',
      dasar: ['Skor Z BB/PB atau BB/TB > +3 SD'],
    }
  }

  if (z > 2) {
    return {
      klasifikasi: 'GIZI LEBIH',
      dasar: ['Skor Z BB/PB atau BB/TB > +2 SD sampai +3 SD'],
    }
  }

  if (z > 1) {
    return {
      klasifikasi: 'BERISIKO GIZI LEBIH',
      dasar: ['Skor Z BB/PB atau BB/TB > +1 SD sampai +2 SD'],
    }
  }

  return {
    klasifikasi: 'Belum dapat diklasifikasikan',
    dasar: ['Kombinasi indikator belum memenuhi klasifikasi pada bagan MTBS.'],
  }
}

const hasilKlasifikasi = computed(() => {
  return hitungKlasifikasiLokal()
})

const dosisVitaminA = computed(() => {
  const umur = normalizeNumber(data.umur_bulan)

  if (umur === null) {
    return 'sesuai umur'
  }

  if (umur < 6) {
    return '50.000 IU'
  }

  if (umur < 12) {
    return '100.000 IU'
  }

  return '200.000 IU'
})

const rekomendasi = computed(() => {
  const klasifikasi = hasilKlasifikasi.value.klasifikasi
  const tindakan = []
  const pengobatan = []

  if (klasifikasi === 'GIZI BURUK DENGAN KOMPLIKASI') {
    tindakan.push(
      'Cegah agar gula darah tidak turun',
      'Nasihati cara menjaga anak tetap hangat selama perjalanan',
    )

    if (form.syok) {
      tindakan.push(
        'Jika disertai syok, berikan glukosa 10% dan cairan infus pra-rujukan sesuai pedoman',
      )
    }

    if (data.otomatis?.diare) {
      tindakan.push(
        'Jika disertai diare, berikan cairan ReSoMal atau modifikasinya 5 ml/kgBB sebelum dirujuk',
      )
    }

    tindakan.push('RUJUK SEGERA')

    pengobatan.push(
      'Ampisilin dosis pertama 50 mg/kgBB secara IM/IV',
      'Gentamisin dosis pertama 7,5 mg/kgBB secara IM/IV',
      `Vitamin A dosis pertama ${dosisVitaminA.value}`,
    )
  }

  if (klasifikasi === 'GIZI BURUK TANPA KOMPLIKASI') {
    tindakan.push(
      'Cegah agar gula darah tidak turun',
      'Nasihati cara menjaga anak tetap hangat selama perjalanan',
      'Lakukan skrining perkembangan sesuai SDIDTK',
    )

    if (data.otomatis?.diare) {
      tindakan.push(
        'Jika disertai diare, berikan cairan ReSoMal atau modifikasinya',
      )
    }

    tindakan.push(
      'Kunjungan ulang 7 hari',
      'Nasihati kapan harus kembali segera',
      'RUJUK ke dokter untuk penanganan gizi buruk dan kemungkinan penyakit penyerta seperti TB atau HIV',
    )

    pengobatan.push(
      'Amoksisilin 15 mg/kgBB setiap 8 jam selama 5 hari',
      `Vitamin A dosis pertama ${dosisVitaminA.value}`,
    )
  }

  if (klasifikasi === 'GIZI KURANG') {
    tindakan.push(
      'Nilai pemberian makan anak; bila ada masalah, lakukan konseling dan kunjungan ulang 7 hari',
      'Lakukan skrining perkembangan sesuai SDIDTK',
      'Kunjungan ulang 14 hari',
      'Nasihati kapan harus kembali segera',
      'RUJUK ke dokter untuk melacak kemungkinan penyakit penyerta seperti TB atau HIV',
    )
  }

  if (klasifikasi === 'GIZI BAIK') {
    tindakan.push(
      'Jika anak berumur < 2 tahun, nilai pemberian makan; bila ada masalah, kunjungan ulang 7 hari',
      'Timbang berat badan setiap bulan',
      'Pujilah ibu dan anjurkan melanjutkan pemberian makan sesuai umur',
    )
  }

  if (klasifikasi === 'BERISIKO GIZI LEBIH') {
    tindakan.push(
      'Plot IMT/U untuk menegakkan diagnosis',
      'Lakukan konseling gizi untuk menentukan penyebab',
      'Kunjungan ulang 14 hari; bila tidak membaik, RUJUK',
      'Nasihati kapan harus kembali segera',
    )
  }

  if (klasifikasi === 'GIZI LEBIH') {
    tindakan.push(
      'Lakukan konseling gizi dan aktivitas anak bersama petugas gizi',
      'Kunjungan ulang 14 hari; bila tidak membaik, RUJUK',
      'Nasihati kapan harus kembali segera',
    )
  }

  if (klasifikasi === 'OBESITAS') {
    tindakan.push(
      'RUJUK ke rumah sakit untuk penanganan lebih lanjut',
    )
  }

  return {
    tindakan,
    pengobatan,
  }
})

const resultClass = computed(() => {
  const klasifikasi = hasilKlasifikasi.value.klasifikasi

  if (klasifikasi === 'GIZI BURUK DENGAN KOMPLIKASI') {
    return 'result-danger'
  }

  if (klasifikasi === 'GIZI BURUK TANPA KOMPLIKASI') {
    return 'result-warning'
  }

  if (klasifikasi === 'GIZI KURANG') {
    return 'result-mild'
  }

  if (klasifikasi === 'GIZI BAIK') {
    return 'result-good'
  }

  if (klasifikasi === 'OBESITAS') {
    return 'result-obesity'
  }

  if (klasifikasi === 'GIZI LEBIH') {
    return 'result-excess'
  }

  if (klasifikasi === 'BERISIKO GIZI LEBIH') {
    return 'result-risk'
  }

  return 'result-default'
})

const resetData = () => {
  Object.assign(data, emptyData())

  form.edema = '0'
  form.komplikasi_medis = false
  form.lemah_menyusu = false
  form.bb_tidak_naik = false
  form.syok = false
  form.catatan = ''
}

const applyData = (payload) => {
  resetData()

  if (!payload) {
    return
  }

  Object.assign(data, {
    ...emptyData(),
    ...payload,
    otomatis: {
      ...emptyData().otomatis,
      ...(payload.otomatis || {}),
    },
  })

  form.edema = payload.edema || '0'
  form.komplikasi_medis = !!payload.komplikasi_medis
  form.lemah_menyusu = !!payload.lemah_menyusu
  form.bb_tidak_naik = !!payload.bb_tidak_naik
  form.syok = !!payload.syok
  form.catatan = payload.catatan || ''
}

const showMessage = (
  text,
  type = 'success',
) => {
  message.value = text
  messageType.value = type

  window.setTimeout(() => {
    message.value = ''
  }, 5000)
}

let objectiveRetryTimer = null

const stopObjectiveRetry = () => {
  if (objectiveRetryTimer) {
    window.clearTimeout(objectiveRetryTimer)
    objectiveRetryTimer = null
  }
}

const scheduleObjectiveRetry = () => {
  stopObjectiveRetry()

  // Komponen Gizi dapat sudah ter-mount sebelum Objektif disimpan.
  // Coba lagi hanya selama data Objektif belum tersedia, lalu berhenti.
  objectiveRetryTimer = window.setTimeout(() => {
    loadData({ silent: true })
  }, 2000)
}

const loadData = async (options = {}) => {
  if (!kunjunganId.value) {
    return
  }

  const silent = options?.silent === true

  if (!silent) {
    loading.value = true
    message.value = ''
  }

  try {
    const response = await axios.get(
      `/mtbs/gizi/${kunjunganId.value}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    const payload = response.data?.data || null
    applyData(payload)

    if (payload?.data_objektif_ada) {
      stopObjectiveRetry()

      if (!silent) {
        showMessage(
          response.data?.message || 'Data gizi berhasil dimuat.',
          'success',
        )
      }
    } else {
      scheduleObjectiveRetry()

      if (!silent) {
        showMessage(
          'Data Objektif belum terbaca. Sistem akan memuat ulang otomatis setelah Objektif disimpan.',
          'error',
        )
      }
    }
  } catch (error) {
    console.error(
      'LOAD GIZI MTBS ERROR:',
      error.response?.data || error,
    )

    stopObjectiveRetry()
    resetData()

    if (!silent) {
      showMessage(
        error.response?.data?.message || 'Gagal memuat data gizi MTBS.',
        'error',
      )
    }
  } finally {
    if (!silent) {
      loading.value = false
    }
  }
}

const saveData = async () => {
  if (!kunjunganId.value) {
    showMessage(
      'ID pelayanan tidak ditemukan.',
      'error',
    )

    return
  }

  saving.value = true
  message.value = ''

  try {
    const response = await axios.post(
      '/mtbs/gizi/store',
      {
        kunjungan_id: kunjunganId.value,
        edema: form.edema,
        komplikasi_medis: form.komplikasi_medis,
        lemah_menyusu: form.lemah_menyusu,
        bb_tidak_naik: form.bb_tidak_naik,
        syok: form.syok,
        catatan: form.catatan,
      },
    )

    applyData(response.data?.data)

    showMessage(
      response.data?.message || 'Data gizi MTBS berhasil disimpan.',
      'success',
    )
  } catch (error) {
    console.error(
      'SAVE GIZI MTBS ERROR:',
      error.response?.data || error,
    )

    const validationError = error.response?.data?.errors
      ? Object.values(error.response.data.errors).flat()[0]
      : null

    showMessage(
      validationError
      || error.response?.data?.message
      || 'Gagal menyimpan data gizi MTBS.',
      'error',
    )
  } finally {
    saving.value = false
  }
}

const formatMetric = (
  value,
  unit,
) => {
  const numberValue = normalizeNumber(value)

  if (numberValue === null) {
    return '-'
  }

  return `${numberValue} ${unit}`
}

const formatZScore = (value) => {
  const numberValue = normalizeNumber(value)

  if (numberValue === null) {
    return '-'
  }

  return numberValue > 0
    ? `+${numberValue.toFixed(2)} SD`
    : `${numberValue.toFixed(2)} SD`
}

const handleObjektifSaved = (event) => {
  const savedId = String(event?.detail?.kunjunganId || '')

  if (!savedId || savedId === kunjunganId.value) {
    loadData({ silent: true })
  }
}

const handleWindowFocus = () => {
  loadData({ silent: true })
}

onMounted(() => {
  window.addEventListener('mtbs:objektif-saved', handleObjektifSaved)
  window.addEventListener('focus', handleWindowFocus)
})

onActivated(() => {
  loadData({ silent: true })
})

onBeforeUnmount(() => {
  stopObjectiveRetry()
  window.removeEventListener('mtbs:objektif-saved', handleObjektifSaved)
  window.removeEventListener('focus', handleWindowFocus)
})

watch(
  kunjunganId,
  (value) => {
    if (value) {
      loadData()
    } else {
      resetData()
    }
  },
  {
    immediate: true,
  },
)
</script>

<style scoped>
.gizi-page {
  padding: 4px;
  color: #34413a;
}

.section-heading {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.source-badge {
  display: inline-flex;
  align-items: center;
  min-height: 30px;
  padding: 5px 11px;
  border: 1px solid #a3cfbb;
  border-radius: 20px;
  color: #0f5132;
  background: #d1e7dd;
  font-size: 12px;
  font-weight: 700;
}

.metric-grid {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 10px;
}

.metric-card {
  min-height: 92px;
  padding: 14px;
  border: 1px solid #dfe7e2;
  border-radius: 11px;
  background: #fafcfb;
}

.metric-card span {
  display: block;
  margin-bottom: 8px;
  color: #758078;
  font-size: 12px;
  font-weight: 700;
}

.metric-card strong {
  display: block;
  color: #263b30;
  font-size: 17px;
  line-height: 1.3;
}

.metric-card small {
  display: block;
  margin-top: 6px;
  color: #748079;
  font-size: 11px;
}

.metric-card.muted {
  background: #f3f5f4;
}

.zscore-card {
  border-color: #9ec5fe;
  background: #eef5ff;
}

.check-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  min-height: 83px;
  margin: 0;
  padding: 15px;
  border: 1px solid #dfe5e1;
  border-radius: 10px;
  background: #fff;
  cursor: pointer;
}

.check-card.selected {
  border-color: #198754;
  background: #eef8f2;
}

.check-card .form-check-input {
  flex: 0 0 auto;
  width: 19px;
  height: 19px;
  margin-top: 2px;
}

.check-card span {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.check-card strong {
  color: #34413a;
  font-size: 14px;
}

.check-card small {
  color: #78827c;
  font-size: 12px;
  line-height: 1.4;
}

.auto-findings {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.finding-chip {
  display: inline-flex;
  align-items: center;
  min-height: 31px;
  padding: 6px 11px;
  border: 1px solid;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 650;
}

.finding-danger {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.finding-warning {
  color: #664d03;
  border-color: #ffecb5;
  background: #fff3cd;
}

.result-box {
  padding: 20px 16px;
  border: 1px solid transparent;
  border-radius: 13px;
  font-size: 19px;
  font-weight: 800;
  line-height: 1.35;
  text-align: center;
}

.classification-reasons,
.recommendation-list {
  margin: 0;
  padding-left: 20px;
}

.classification-reasons li,
.recommendation-list li {
  margin-bottom: 9px;
  line-height: 1.5;
}

.empty-result {
  min-height: 94px;
  padding: 18px;
  border: 1px dashed #d8e0dc;
  border-radius: 9px;
  color: #78827c;
  background: #fafcfb;
  font-size: 13px;
  text-align: center;
}

.result-danger {
  border-color: #f1aeb5;
  color: #842029;
  background: #f8d7da;
}

.result-warning {
  border-color: #ffe69c;
  color: #664d03;
  background: #fff3cd;
}

.result-mild {
  border-color: #f7e28a;
  color: #6b5b00;
  background: #fff7cc;
}

.result-good {
  border-color: #a3cfbb;
  color: #0f5132;
  background: #d1e7dd;
}

.result-obesity {
  border-color: #e8aaaa;
  color: #7a1f1f;
  background: #f5d0d0;
}

.result-excess {
  border-color: #ddd07b;
  color: #5a4b00;
  background: #efe8b0;
}

.result-risk {
  border-color: #e6dd95;
  color: #665a00;
  background: #f3efc7;
}

.result-default {
  border-color: #cbd5e1;
  color: #334155;
  background: #f1f5f9;
}

@media (max-width: 1199.98px) {
  .metric-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 767.98px) {
  .metric-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .section-heading {
    align-items: flex-start;
    flex-direction: column;
  }
}

@media (max-width: 479.98px) {
  .metric-grid {
    grid-template-columns: 1fr;
  }
}
</style>
