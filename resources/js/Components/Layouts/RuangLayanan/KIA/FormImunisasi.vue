<template>
  <div class="imunisasi-wrapper bg-white rounded-3 shadow-sm">
    <!-- HEADER -->
    <div class="page-header">
      <div>
        <h5 class="page-title mb-1">
          Skrining Status Imunisasi MTBS
        </h5>

        <div class="page-subtitle">
          Verifikasi imunisasi sesuai umur dan tentukan tindak lanjut.
          Pemberian vaksin tetap dicatat pada pelayanan/modul imunisasi.
        </div>
      </div>

      <button
        type="button"
        class="btn btn-sm btn-outline-success"
        :disabled="loading || !idPelayanan"
        @click="loadData"
      >
        <span
          v-if="loading"
          class="spinner-border spinner-border-sm me-1"
        ></span>
        {{ loading ? 'Memuat...' : 'Refresh' }}
      </button>
    </div>

    <div class="form-body">
      <div
        v-if="message"
        class="alert mb-3"
        :class="messageType === 'success' ? 'alert-success' : 'alert-danger'"
      >
        {{ message }}
      </div>

      <div
        v-if="!idPelayanan || !pasienId"
        class="alert alert-danger"
      >
        ID pelayanan atau data pasien tidak terbaca.
      </div>

      <!-- ATURAN MTBS -->
      <div class="rule-card mb-3">
        <div class="rule-icon">i</div>

        <div>
          <div class="rule-title">
            Aturan pemeriksaan imunisasi pada MTBS
          </div>

          <div class="rule-text">
            Anak sehat atau sakit ringan yang imunisasi dasarnya belum lengkap
            diarahkan untuk segera melengkapi imunisasi. Bila anak akan dirujuk
            segera, imunisasi ditunda agar tidak memperlambat rujukan.
          </div>
        </div>
      </div>

      <!-- IDENTITAS RINGKAS -->
      <div class="patient-strip mb-3">
        <div class="patient-item">
          <span class="patient-label">Pasien</span>
          <strong>{{ pasienNama }}</strong>
        </div>

        <div class="patient-item">
          <span class="patient-label">Umur</span>
          <strong>{{ umurDisplay }}</strong>
        </div>

        <div class="patient-item">
          <span class="patient-label">Assessment</span>
          <strong :class="rujukSegera ? 'text-danger' : 'text-success'">
            {{ assessmentLabel }}
          </strong>
        </div>
      </div>

      <form @submit.prevent="simpanSkrining">
        <div class="row g-3">
          <!-- KOLOM KIRI -->
          <div class="col-xl-4">
            <div class="section-card h-100">
              <div class="section-title">
                Verifikasi Data
              </div>

              <div class="section-description">
                Pilih sumber yang digunakan untuk memastikan riwayat imunisasi.
              </div>

              <div class="mb-3">
                <label class="form-label">
                  Sumber verifikasi
                </label>

                <select
                  v-model="form.sumber_verifikasi"
                  class="form-select"
                  :disabled="loading"
                >
                  <option value="buku_kia">
                    Buku KIA / kartu imunisasi
                  </option>
                  <option value="data_posyandu">
                    Data Posyandu
                  </option>
                  <option value="pengakuan_orang_tua">
                    Pengakuan orang tua
                  </option>
                  <option value="tidak_ada_bukti">
                    Tidak ada bukti / tidak diketahui
                  </option>
                  <option value="lainnya">
                    Sumber lainnya
                  </option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">
                  Kondisi anak untuk tindak lanjut imunisasi
                </label>

                <select
                  v-model="form.kondisi_anak"
                  class="form-select"
                  :disabled="loading || rujukSegera"
                >
                  <option value="sehat_sakit_ringan">
                    Sehat / sakit ringan
                  </option>
                  <option value="belum_stabil">
                    Belum stabil, tunda sementara
                  </option>
                  <option
                    value="rujuk_segera"
                    :disabled="!rujukSegera"
                  >
                    Akan dirujuk segera
                  </option>
                </select>

                <small
                  v-if="rujukSegera"
                  class="form-hint text-danger"
                >
                  Dikunci sebagai rujuk segera berdasarkan Assessment MTBS.
                </small>
              </div>

              <div class="program-box mb-3">
                <div class="program-title">
                  Program wilayah pada buku MTBS
                </div>

                <label class="form-check mb-2">
                  <input
                    v-model="form.program_pcv"
                    class="form-check-input"
                    type="checkbox"
                    :disabled="loading"
                  />
                  <span class="form-check-label">
                    PCV berlaku di wilayah pelayanan
                  </span>
                </label>

                <label class="form-check mb-0">
                  <input
                    v-model="form.program_je"
                    class="form-check-input"
                    type="checkbox"
                    :disabled="loading"
                  />
                  <span class="form-check-label">
                    Japanese Encephalitis berlaku di wilayah pelayanan
                  </span>
                </label>
              </div>

              <div>
                <label class="form-label">
                  Catatan verifikasi
                </label>

                <textarea
                  v-model="form.catatan"
                  class="form-control"
                  rows="4"
                  maxlength="2000"
                  placeholder="Contoh: Buku KIA dibawa, data sesuai dengan catatan Posyandu."
                  :disabled="loading"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- KOLOM KANAN -->
          <div class="col-xl-8">
            <div class="section-card h-100">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                  <div class="section-title mb-1">
                    Imunisasi yang Seharusnya Sudah Tercatat
                  </div>

                  <div class="section-description mb-0">
                    Centang berdasarkan Buku KIA, data Posyandu, atau sumber verifikasi yang dipilih.
                  </div>
                </div>

                <span class="age-badge">
                  Sampai umur {{ umurDisplay }}
                </span>
              </div>

              <div
                v-if="umurBulanTotal === null"
                class="empty-state"
              >
                Umur pasien belum terbaca sehingga jadwal belum dapat dihitung.
              </div>

              <div
                v-else-if="jadwalSampaiUmur.length === 0"
                class="empty-state"
              >
                Belum ada jadwal imunisasi yang harus dinilai pada umur ini.
              </div>

              <div v-else class="vaccine-list">
                <label
                  v-for="vaksin in jadwalSampaiUmur"
                  :key="vaksin.code"
                  class="vaccine-card"
                  :class="{
                    checked: form.vaksin_tercatat.includes(vaksin.code),
                    missing: !form.vaksin_tercatat.includes(vaksin.code),
                  }"
                >
                  <input
                    v-model="form.vaksin_tercatat"
                    class="form-check-input"
                    type="checkbox"
                    :value="vaksin.code"
                    :disabled="loading || form.sumber_verifikasi === 'tidak_ada_bukti'"
                  />

                  <div class="vaccine-info">
                    <div class="vaccine-name">
                      {{ vaksin.label }}
                    </div>

                    <div class="vaccine-schedule">
                      Jadwal: {{ vaksin.jadwal }}
                    </div>
                  </div>

                  <span
                    v-if="vaksin.program_wilayah"
                    class="regional-badge"
                  >
                    Program wilayah
                  </span>

                  <span
                    class="record-status"
                    :class="form.vaksin_tercatat.includes(vaksin.code)
                      ? 'recorded'
                      : 'not-recorded'"
                  >
                    {{
                      form.vaksin_tercatat.includes(vaksin.code)
                        ? 'Sudah tercatat'
                        : 'Belum tercatat'
                    }}
                  </span>
                </label>
              </div>

              <small class="form-hint d-block mt-3">
                Status “belum tercatat” tidak otomatis berarti vaksin pasti belum pernah diberikan.
                Verifikasi kembali bila bukti tidak tersedia.
              </small>
            </div>
          </div>
        </div>

        <!-- HASIL OTOMATIS -->
        <div class="result-card mt-3">
          <div class="result-header">
            <div>
              <div class="result-title">
                Hasil Skrining Imunisasi
              </div>
              <div class="result-subtitle">
                Status dan tindak lanjut dihitung otomatis dari umur, riwayat yang terverifikasi, dan kondisi anak.
              </div>
            </div>

            <span
              class="status-badge"
              :class="statusClass"
            >
              {{ statusLabel }}
            </span>
          </div>

          <div class="row g-3">
            <div class="col-lg-7">
              <div class="result-block">
                <div class="result-label">
                  Imunisasi belum tercatat sesuai umur
                </div>

                <div
                  v-if="statusImunisasi === 'tidak_diketahui'"
                  class="result-value text-muted"
                >
                  Belum dapat dinilai karena bukti imunisasi tidak tersedia.
                </div>

                <div
                  v-else-if="vaksinBelum.length === 0"
                  class="result-value text-success fw-semibold"
                >
                  Tidak ada kekurangan yang teridentifikasi.
                </div>

                <div v-else class="missing-list">
                  <span
                    v-for="code in vaksinBelum"
                    :key="code"
                    class="missing-chip"
                  >
                    {{ labelVaksin(code) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="col-lg-5">
              <div class="follow-up-box" :class="followUpClass">
                <div class="result-label">
                  Tindak lanjut MTBS
                </div>

                <div class="follow-up-value">
                  {{ tindakLanjutLabel }}
                </div>

                <div class="follow-up-description">
                  {{ tindakLanjutDescription }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-footer mt-3">
          <div class="footer-note">
            SIMPUS MTBS hanya mencatat hasil skrining dan keputusan tindak lanjut,
            bukan menggantikan pencatatan pemberian vaksin.
          </div>

          <button
            type="submit"
            class="btn btn-success save-button"
            :disabled="loading || !idPelayanan || !pasienId"
          >
            <span
              v-if="saving"
              class="spinner-border spinner-border-sm me-2"
            ></span>
            {{ saving ? 'Menyimpan...' : 'Simpan Skrining Imunisasi' }}
          </button>
        </div>
      </form>

      <!-- RIWAYAT SKRINING -->
      <div class="history-card mt-4">
        <div class="history-header">
          <div>
            <div class="section-title mb-1">
              Riwayat Skrining Imunisasi MTBS
            </div>
            <div class="section-description mb-0">
              Riwayat konfirmasi pada kunjungan MTBS pasien ini.
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th style="width: 55px">No</th>
                <th>Tanggal</th>
                <th>Umur</th>
                <th>Sumber</th>
                <th>Status</th>
                <th>Belum tercatat</th>
                <th>Tindak lanjut</th>
                <th>Petugas</th>
                <th style="width: 85px">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(item, index) in dataRiwayat"
                :key="item.id"
              >
                <td>{{ index + 1 }}</td>
                <td>{{ formatTanggal(item.updated_at || item.created_at) }}</td>
                <td>{{ formatUmur(item.umur_bulan_total) }}</td>
                <td>{{ sumberLabel(item.sumber_verifikasi) }}</td>
                <td>
                  <span
                    class="table-status"
                    :class="statusClassByValue(item.status_imunisasi)"
                  >
                    {{ statusLabelByValue(item.status_imunisasi) }}
                  </span>
                </td>
                <td>
                  {{ formatVaksinList(item.vaksin_belum) }}
                </td>
                <td>
                  {{ tindakLanjutLabelByValue(item.tindak_lanjut) }}
                </td>
                <td>
                  {{ item.updated_by || item.created_by || '-' }}
                </td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="loading"
                    @click="hapusData(item.id)"
                  >
                    Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="dataRiwayat.length === 0">
                <td
                  colspan="9"
                  class="text-center text-muted py-4"
                >
                  Belum ada riwayat skrining imunisasi MTBS.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  computed,
  onMounted,
  reactive,
  ref,
  watch,
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
const dataRiwayat = ref([])

const context = ref({
  assessment_ada: false,
  status_kegawatan: null,
  rujuk_segera: false,
})

const jadwal = ref([
  {
    code: 'HB0',
    label: 'HB-0',
    jadwal: '0–24 jam',
    umur_bulan: 0,
    program_wilayah: null,
  },
  {
    code: 'BCG',
    label: 'BCG',
    jadwal: '1 bulan',
    umur_bulan: 1,
    program_wilayah: null,
  },
  {
    code: 'OPV0',
    label: 'OPV 0',
    jadwal: '1 bulan',
    umur_bulan: 1,
    program_wilayah: null,
  },
  {
    code: 'DPT_HB_HIB_1',
    label: 'DPT-HB-Hib 1',
    jadwal: '2 bulan',
    umur_bulan: 2,
    program_wilayah: null,
  },
  {
    code: 'OPV1',
    label: 'OPV 1',
    jadwal: '2 bulan',
    umur_bulan: 2,
    program_wilayah: null,
  },
  {
    code: 'PCV1',
    label: 'PCV 1',
    jadwal: '2 bulan',
    umur_bulan: 2,
    program_wilayah: 'pcv',
  },
  {
    code: 'DPT_HB_HIB_2',
    label: 'DPT-HB-Hib 2',
    jadwal: '3 bulan',
    umur_bulan: 3,
    program_wilayah: null,
  },
  {
    code: 'OPV2',
    label: 'OPV 2',
    jadwal: '3 bulan',
    umur_bulan: 3,
    program_wilayah: null,
  },
  {
    code: 'PCV2',
    label: 'PCV 2',
    jadwal: '3 bulan',
    umur_bulan: 3,
    program_wilayah: 'pcv',
  },
  {
    code: 'DPT_HB_HIB_3',
    label: 'DPT-HB-Hib 3',
    jadwal: '4 bulan',
    umur_bulan: 4,
    program_wilayah: null,
  },
  {
    code: 'OPV3',
    label: 'OPV 3',
    jadwal: '4 bulan',
    umur_bulan: 4,
    program_wilayah: null,
  },
  {
    code: 'IPV',
    label: 'IPV / Polio suntik',
    jadwal: '4 bulan',
    umur_bulan: 4,
    program_wilayah: null,
  },
  {
    code: 'MR9',
    label: 'Campak Rubella',
    jadwal: '9 bulan',
    umur_bulan: 9,
    program_wilayah: null,
  },
  {
    code: 'JE10',
    label: 'Japanese Encephalitis',
    jadwal: '10 bulan',
    umur_bulan: 10,
    program_wilayah: 'je',
  },
  {
    code: 'PCV3_12',
    label: 'PCV 3',
    jadwal: '12 bulan',
    umur_bulan: 12,
    program_wilayah: 'pcv',
  },
  {
    code: 'DPT_HB_HIB_BOOSTER_18',
    label: 'DPT-HB-Hib lanjutan',
    jadwal: '18 bulan',
    umur_bulan: 18,
    program_wilayah: null,
  },
  {
    code: 'MR18',
    label: 'Campak Rubella lanjutan',
    jadwal: '18 bulan',
    umur_bulan: 18,
    program_wilayah: null,
  },
])

const form = reactive({
  sumber_verifikasi: 'tidak_ada_bukti',
  vaksin_tercatat: [],
  kondisi_anak: 'sehat_sakit_ringan',
  program_pcv: false,
  program_je: false,
  catatan: '',
})

const idPelayanan = computed(() => {
  return (
    page.props?.idPelayanan
    || page.props?.idpelayanan
    || page.props?.pelayanan?.idPelayanan
    || page.props?.pelayanan?.idpelayanan
    || page.props?.DataPasien?.[0]?.idPelayanan
    || page.props?.DataPasien?.[0]?.idpelayanan
    || null
  )
})

const pasien = computed(() => {
  return (
    props.DataPasien?.[0]
    || page.props?.DataPasien?.[0]
    || page.props?.pasien
    || null
  )
})

const pasienId = computed(() => {
  return (
    pasien.value?.ID
    || pasien.value?.id
    || pasien.value?.pasien_id
    || pasien.value?.pasienId
    || null
  )
})

const pasienNama = computed(() => {
  return (
    pasien.value?.NAMA_LGKP
    || pasien.value?.nama
    || pasien.value?.nama_pasien
    || '-'
  )
})

const parseUmurText = (value) => {
  if (value === null || value === undefined) {
    return null
  }

  if (
    typeof value === 'number'
    && Number.isFinite(value)
  ) {
    return Math.max(0, Math.floor(value))
  }

  const text = String(value).toLowerCase().trim()

  if (!text) {
    return null
  }

  const yearMatch = text.match(/(\d+)\s*(tahun|th|taun)/)
  const monthMatch = text.match(/(\d+)\s*(bulan|bln)/)

  if (yearMatch || monthMatch) {
    const years = yearMatch
      ? Number(yearMatch[1])
      : 0

    const months = monthMatch
      ? Number(monthMatch[1])
      : 0

    return (years * 12) + months
  }

  const numberValue = Number(text.replace(/[^0-9]/g, ''))

  return Number.isFinite(numberValue)
    ? numberValue
    : null
}

const calculateAgeFromDates = () => {
  const birthRaw =
    pasien.value?.TGL_LHR
    || pasien.value?.tgl_lahir
    || pasien.value?.tanggal_lahir

  if (!birthRaw) {
    return null
  }

  const visitRaw =
    pasien.value?.tglKunjungan
    || pasien.value?.tglPelayanan
    || new Date().toISOString().slice(0, 10)

  const birth = new Date(birthRaw)
  const visit = new Date(visitRaw)

  if (
    Number.isNaN(birth.getTime())
    || Number.isNaN(visit.getTime())
  ) {
    return null
  }

  let months =
    (visit.getFullYear() - birth.getFullYear()) * 12
    + (visit.getMonth() - birth.getMonth())

  if (visit.getDate() < birth.getDate()) {
    months -= 1
  }

  return Math.max(0, months)
}

const umurBulanTotal = computed(() => {
  const directCandidates = [
    pasien.value?.umur_bulan,
    pasien.value?.umurBulanTotal,
    pasien.value?.umur_bulan_total,
  ]

  for (const candidate of directCandidates) {
    if (
      candidate !== null
      && candidate !== undefined
      && candidate !== ''
      && Number.isFinite(Number(candidate))
    ) {
      return Math.max(0, Math.floor(Number(candidate)))
    }
  }

  const tahun = Number(
    pasien.value?.umur
    ?? pasien.value?.umur_tahun
    ?? pasien.value?.umurTahun,
  )

  const bulanSisa = Number(
    pasien.value?.umur_bulan_sisa
    ?? pasien.value?.umurBulan
    ?? 0,
  )

  if (
    Number.isFinite(tahun)
    && Number.isFinite(bulanSisa)
  ) {
    return Math.max(
      0,
      Math.floor((tahun * 12) + bulanSisa),
    )
  }

  const fromDate = calculateAgeFromDates()
  if (fromDate !== null) {
    return fromDate
  }

  return parseUmurText(pasien.value?.umur)
})

const formatUmur = (months) => {
  if (
    months === null
    || months === undefined
    || !Number.isFinite(Number(months))
  ) {
    return '-'
  }

  const total = Math.max(0, Math.floor(Number(months)))
  const years = Math.floor(total / 12)
  const remainingMonths = total % 12

  if (years === 0) {
    return `${remainingMonths} bulan`
  }

  if (remainingMonths === 0) {
    return `${years} tahun`
  }

  return `${years} tahun ${remainingMonths} bulan`
}

const umurDisplay = computed(() => {
  return formatUmur(umurBulanTotal.value)
})

const rujukSegera = computed(() => {
  return Boolean(context.value?.rujuk_segera)
})

const assessmentLabel = computed(() => {
  if (!context.value?.assessment_ada) {
    return 'Assessment belum digenerate'
  }

  return context.value?.status_kegawatan || 'Tidak gawat'
})

const jadwalSampaiUmur = computed(() => {
  const age = umurBulanTotal.value

  if (age === null) {
    return []
  }

  return jadwal.value.filter((item) => {
    if (Number(item.umur_bulan) > age) {
      return false
    }

    if (
      item.program_wilayah === 'pcv'
      && !form.program_pcv
    ) {
      return false
    }

    if (
      item.program_wilayah === 'je'
      && !form.program_je
    ) {
      return false
    }

    return true
  })
})

const vaksinBelum = computed(() => {
  const recorded = new Set(form.vaksin_tercatat)

  return jadwalSampaiUmur.value
    .map((item) => item.code)
    .filter((code) => !recorded.has(code))
})

const statusImunisasi = computed(() => {
  if (
    umurBulanTotal.value === null
    || (
      form.sumber_verifikasi === 'tidak_ada_bukti'
      && form.vaksin_tercatat.length === 0
    )
  ) {
    return 'tidak_diketahui'
  }

  return vaksinBelum.value.length === 0
    ? 'lengkap_sesuai_umur'
    : 'belum_lengkap'
})

const tindakLanjut = computed(() => {
  if (rujukSegera.value || form.kondisi_anak === 'rujuk_segera') {
    return 'tunda_rujuk_segera'
  }

  if (statusImunisasi.value === 'tidak_diketahui') {
    return 'verifikasi_ulang'
  }

  if (statusImunisasi.value === 'lengkap_sesuai_umur') {
    return 'tidak_perlu'
  }

  if (form.kondisi_anak === 'sehat_sakit_ringan') {
    return 'arahkan_ruang_imunisasi_hari_ini'
  }

  return 'jadwalkan_kembali'
})

const statusLabel = computed(() => {
  return statusLabelByValue(statusImunisasi.value)
})

const statusClass = computed(() => {
  return statusClassByValue(statusImunisasi.value)
})

const tindakLanjutLabel = computed(() => {
  return tindakLanjutLabelByValue(tindakLanjut.value)
})

const tindakLanjutDescription = computed(() => {
  const descriptions = {
    tidak_perlu:
      'Tidak ada imunisasi yang perlu dilengkapi berdasarkan data yang terverifikasi.',
    arahkan_ruang_imunisasi_hari_ini:
      'Arahkan ke ruang imunisasi pada kunjungan yang sama. Pemberian vaksin dicatat di modul imunisasi.',
    jadwalkan_kembali:
      'Kondisi anak belum stabil. Tentukan jadwal kembali setelah kondisi memungkinkan.',
    tunda_rujuk_segera:
      'Jangan menunda rujukan untuk imunisasi. Nasihati ibu agar kembali setelah penanganan rujukan.',
    verifikasi_ulang:
      'Minta Buku KIA atau konfirmasi data Posyandu sebelum menyimpulkan status imunisasi.',
  }

  return descriptions[tindakLanjut.value] || '-'
})

const followUpClass = computed(() => {
  if (tindakLanjut.value === 'tidak_perlu') {
    return 'follow-up-success'
  }

  if (tindakLanjut.value === 'tunda_rujuk_segera') {
    return 'follow-up-danger'
  }

  return 'follow-up-warning'
})

watch(
  rujukSegera,
  (value) => {
    if (value) {
      form.kondisi_anak = 'rujuk_segera'
    } else if (form.kondisi_anak === 'rujuk_segera') {
      form.kondisi_anak = 'sehat_sakit_ringan'
    }
  },
  { immediate: true },
)

watch(
  () => form.sumber_verifikasi,
  (value, oldValue) => {
    if (
      value === 'tidak_ada_bukti'
      && oldValue !== undefined
    ) {
      form.vaksin_tercatat = []
    }
  },
)

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type

  window.setTimeout(() => {
    message.value = ''
  }, 5000)
}

const statusLabelByValue = (value) => {
  const labels = {
    lengkap_sesuai_umur: 'Lengkap sesuai umur',
    belum_lengkap: 'Belum lengkap',
    tidak_diketahui: 'Belum diketahui',
  }

  return labels[value] || '-'
}

const statusClassByValue = (value) => {
  if (value === 'lengkap_sesuai_umur') {
    return 'status-complete'
  }

  if (value === 'belum_lengkap') {
    return 'status-incomplete'
  }

  return 'status-unknown'
}

const sumberLabel = (value) => {
  const labels = {
    buku_kia: 'Buku KIA',
    data_posyandu: 'Data Posyandu',
    pengakuan_orang_tua: 'Pengakuan orang tua',
    tidak_ada_bukti: 'Tidak ada bukti',
    lainnya: 'Lainnya',
  }

  return labels[value] || '-'
}

const tindakLanjutLabelByValue = (value) => {
  const labels = {
    tidak_perlu: 'Tidak perlu tindakan',
    arahkan_ruang_imunisasi_hari_ini: 'Arahkan ke ruang imunisasi hari ini',
    jadwalkan_kembali: 'Jadwalkan kembali',
    tunda_rujuk_segera: 'Tunda imunisasi, rujuk segera',
    verifikasi_ulang: 'Verifikasi ulang riwayat imunisasi',
  }

  return labels[value] || '-'
}

const labelVaksin = (code) => {
  const found = jadwal.value.find((item) => item.code === code)
  return found?.label || code
}

const formatVaksinList = (items) => {
  if (!Array.isArray(items) || items.length === 0) {
    return '-'
  }

  return items.map(labelVaksin).join(', ')
}

const formatTanggal = (value) => {
  if (!value) {
    return '-'
  }

  return String(value)
    .replace('T', ' ')
    .slice(0, 16)
}

const applySkrining = (data, carryForward = false) => {
  if (!data) {
    return
  }

  form.vaksin_tercatat = Array.isArray(data.vaksin_tercatat)
    ? [...data.vaksin_tercatat]
    : []

  form.program_pcv = Boolean(data.program_pcv)
  form.program_je = Boolean(data.program_je)

  if (carryForward) {
    form.sumber_verifikasi = data.sumber_verifikasi || 'tidak_ada_bukti'
    form.catatan = ''
    return
  }

  form.sumber_verifikasi = data.sumber_verifikasi || 'tidak_ada_bukti'
  form.kondisi_anak = data.kondisi_anak || 'sehat_sakit_ringan'
  form.catatan = data.catatan || ''
}

const loadData = async () => {
  if (!idPelayanan.value || !pasienId.value) {
    return
  }

  try {
    loading.value = true

    const response = await axios.get(
      '/simpus/kia/mtbs/imunisasi',
      {
        params: {
          kunjungan_id: String(idPelayanan.value),
          pasien_id: Number(pasienId.value),
        },
      },
    )

    if (Array.isArray(response.data?.jadwal)) {
      jadwal.value = response.data.jadwal
    }

    context.value = response.data?.context ?? {
      assessment_ada: false,
      status_kegawatan: null,
      rujuk_segera: false,
    }

    dataRiwayat.value = Array.isArray(response.data?.data)
      ? response.data.data
      : []

    if (response.data?.current) {
      applySkrining(response.data.current)
    } else if (response.data?.latest_patient) {
      applySkrining(response.data.latest_patient, true)
    }

    if (rujukSegera.value) {
      form.kondisi_anak = 'rujuk_segera'
    }
  } catch (error) {
    console.error(
      'LOAD SKRINING IMUNISASI MTBS ERROR:',
      error.response?.data || error,
    )

    showMessage(
      error.response?.data?.message
        || 'Data skrining imunisasi gagal dimuat.',
      'error',
    )
  } finally {
    loading.value = false
  }
}

const getValidationMessage = (errors) => {
  if (!errors || typeof errors !== 'object') {
    return 'Data yang dikirim belum valid.'
  }

  const firstError = Object.values(errors)
    .flat()
    .find(Boolean)

  return firstError || 'Data yang dikirim belum valid.'
}

const simpanSkrining = async () => {
  if (!idPelayanan.value || !pasienId.value) {
    showMessage(
      'ID pelayanan atau pasien tidak terbaca.',
      'error',
    )
    return
  }

  if (umurBulanTotal.value === null) {
    showMessage(
      'Umur pasien belum terbaca.',
      'error',
    )
    return
  }

  try {
    saving.value = true

    const payload = {
      kunjungan_id: String(idPelayanan.value),
      pasien_id: Number(pasienId.value),
      umur_bulan_total: Number(umurBulanTotal.value),
      sumber_verifikasi: form.sumber_verifikasi,
      vaksin_tercatat: [...form.vaksin_tercatat],
      kondisi_anak: rujukSegera.value
        ? 'rujuk_segera'
        : form.kondisi_anak,
      program_pcv: form.program_pcv ? 1 : 0,
      program_je: form.program_je ? 1 : 0,
      catatan: form.catatan || null,
    }

    const response = await axios.post(
      '/simpus/kia/mtbs/imunisasi/store',
      payload,
    )

    showMessage(
      response.data?.message
        || 'Skrining imunisasi berhasil disimpan.',
      'success',
    )

    await loadData()
  } catch (error) {
    console.error(
      'SAVE SKRINING IMUNISASI MTBS ERROR:',
      error.response?.data || error,
    )

    if (error.response?.status === 422) {
      showMessage(
        getValidationMessage(error.response?.data?.errors),
        'error',
      )
      return
    }

    showMessage(
      error.response?.data?.message
        || 'Skrining imunisasi gagal disimpan.',
      'error',
    )
  } finally {
    saving.value = false
  }
}

const hapusData = async (id) => {
  if (!window.confirm('Hapus data skrining imunisasi ini?')) {
    return
  }

  try {
    loading.value = true

    await axios.delete(
      `/simpus/kia/mtbs/imunisasi/${id}`,
    )

    showMessage(
      'Data skrining imunisasi berhasil dihapus.',
      'success',
    )

    await loadData()
  } catch (error) {
    console.error(
      'DELETE SKRINING IMUNISASI MTBS ERROR:',
      error.response?.data || error,
    )

    showMessage(
      error.response?.data?.message
        || 'Data skrining imunisasi gagal dihapus.',
      'error',
    )
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.imunisasi-wrapper {
  overflow: hidden;
  border: 1px solid #e0e7e3;
  color: #34433b;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 20px 24px;
  border-bottom: 1px solid #dfe8e2;
  background: linear-gradient(135deg, #f1fbf5, #ffffff);
}

.page-title {
  color: #198754;
  font-size: 20px;
  font-weight: 750;
}

.page-subtitle {
  max-width: 760px;
  color: #6f7b75;
  font-size: 13px;
  line-height: 1.5;
}

.form-body {
  padding: 20px;
  background: #f6f8f7;
}

.rule-card {
  display: flex;
  align-items: flex-start;
  gap: 13px;
  padding: 15px 17px;
  border: 1px solid #b8d8c6;
  border-radius: 10px;
  background: #edf8f1;
}

.rule-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 30px;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  color: #ffffff;
  background: #198754;
  font-weight: 800;
}

.rule-title {
  margin-bottom: 3px;
  color: #155d39;
  font-size: 14px;
  font-weight: 700;
}

.rule-text {
  color: #496558;
  font-size: 13px;
  line-height: 1.5;
}

.patient-strip {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  overflow: hidden;
  border: 1px solid #dfe6e2;
  border-radius: 10px;
  background: #ffffff;
}

.patient-item {
  padding: 13px 16px;
  border-right: 1px solid #e7ece9;
}

.patient-item:last-child {
  border-right: 0;
}

.patient-label {
  display: block;
  margin-bottom: 3px;
  color: #84908a;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.patient-item strong {
  font-size: 14px;
}

.section-card {
  padding: 18px;
  border: 1px solid #dfe6e2;
  border-radius: 11px;
  background: #ffffff;
  box-shadow: 0 2px 7px rgba(0, 0, 0, 0.025);
}

.section-title {
  color: #215c40;
  font-size: 16px;
  font-weight: 750;
}

.section-description {
  margin-bottom: 17px;
  color: #7b8680;
  font-size: 12px;
  line-height: 1.45;
}

.form-label {
  margin-bottom: 7px;
  color: #3f4d45;
  font-size: 13px;
  font-weight: 650;
}

.form-control,
.form-select {
  min-height: 43px;
  border-color: #d5ded9;
  font-size: 13px;
}

.form-control:focus,
.form-select:focus {
  border-color: #75b798;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1);
}

.form-hint {
  display: block;
  margin-top: 6px;
  color: #7d8782;
  font-size: 11px;
  line-height: 1.45;
}

.program-box {
  padding: 13px 14px;
  border: 1px solid #e0e7e3;
  border-radius: 9px;
  background: #f9fbfa;
}

.program-title {
  margin-bottom: 9px;
  color: #56635c;
  font-size: 12px;
  font-weight: 700;
}

.form-check-label {
  color: #4f5d55;
  font-size: 12px;
}

.age-badge {
  display: inline-flex;
  align-items: center;
  min-height: 30px;
  padding: 5px 10px;
  border: 1px solid #badbcc;
  border-radius: 20px;
  color: #146c43;
  background: #d1e7dd;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}

.vaccine-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 9px;
}

.vaccine-card {
  display: grid;
  grid-template-columns: 22px minmax(0, 1fr) auto;
  align-items: center;
  gap: 10px;
  min-height: 76px;
  margin: 0;
  padding: 12px 13px;
  border: 1px solid #dfe6e2;
  border-radius: 9px;
  background: #ffffff;
  cursor: pointer;
  transition: 0.15s ease;
}

.vaccine-card:hover {
  border-color: #75b798;
  background: #f7fcf9;
}

.vaccine-card.checked {
  border-color: #75b798;
  background: #edf8f2;
}

.vaccine-card.missing {
  border-color: #ead7a2;
}

.vaccine-card .form-check-input {
  width: 18px;
  height: 18px;
  margin: 0;
}

.vaccine-info {
  min-width: 0;
}

.vaccine-name {
  margin-bottom: 3px;
  color: #324239;
  font-size: 13px;
  font-weight: 700;
}

.vaccine-schedule {
  color: #7c8781;
  font-size: 11px;
}

.regional-badge {
  grid-column: 2;
  justify-self: start;
  padding: 3px 7px;
  border: 1px solid #b6d4fe;
  border-radius: 12px;
  color: #084298;
  background: #cfe2ff;
  font-size: 9px;
  font-weight: 700;
}

.record-status {
  grid-column: 3;
  grid-row: 1 / span 2;
  padding: 4px 7px;
  border-radius: 14px;
  font-size: 9px;
  font-weight: 700;
  white-space: nowrap;
}

.recorded {
  color: #0f5132;
  background: #d1e7dd;
}

.not-recorded {
  color: #664d03;
  background: #fff3cd;
}

.empty-state {
  padding: 28px 20px;
  border: 1px dashed #d7dfda;
  border-radius: 9px;
  color: #7b8580;
  background: #fafcfb;
  font-size: 13px;
  text-align: center;
}

.result-card {
  padding: 18px;
  border: 1px solid #dfe6e2;
  border-radius: 11px;
  background: #ffffff;
}

.result-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.result-title {
  margin-bottom: 2px;
  color: #263c30;
  font-size: 16px;
  font-weight: 750;
}

.result-subtitle {
  color: #7b8580;
  font-size: 12px;
}

.status-badge,
.table-status {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 11px;
  border: 1px solid;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 750;
}

.table-status {
  padding: 4px 8px;
  font-size: 10px;
}

.status-complete {
  color: #0f5132;
  border-color: #a3cfbb;
  background: #d1e7dd;
}

.status-incomplete {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.status-unknown {
  color: #41464b;
  border-color: #d3d6d8;
  background: #e2e3e5;
}

.result-block {
  height: 100%;
  padding: 14px;
  border: 1px solid #e4e9e6;
  border-radius: 9px;
  background: #fafcfb;
}

.result-label {
  margin-bottom: 7px;
  color: #77827c;
  font-size: 10px;
  font-weight: 750;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.result-value {
  color: #35433b;
  font-size: 13px;
}

.missing-list {
  display: flex;
  flex-wrap: wrap;
  gap: 7px;
}

.missing-chip {
  padding: 5px 9px;
  border: 1px solid #f1aeb5;
  border-radius: 18px;
  color: #842029;
  background: #f8d7da;
  font-size: 11px;
  font-weight: 650;
}

.follow-up-box {
  height: 100%;
  padding: 14px;
  border: 1px solid;
  border-radius: 9px;
}

.follow-up-success {
  color: #0f5132;
  border-color: #a3cfbb;
  background: #eef8f2;
}

.follow-up-warning {
  color: #664d03;
  border-color: #ffecb5;
  background: #fff9e9;
}

.follow-up-danger {
  color: #842029;
  border-color: #f1aeb5;
  background: #fff0f1;
}

.follow-up-value {
  margin-bottom: 4px;
  font-size: 14px;
  font-weight: 750;
}

.follow-up-description {
  font-size: 12px;
  line-height: 1.5;
}

.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 15px 17px;
  border: 1px solid #dfe6e2;
  border-radius: 10px;
  background: #ffffff;
}

.footer-note {
  max-width: 720px;
  color: #737e78;
  font-size: 12px;
  line-height: 1.5;
}

.save-button {
  min-width: 235px;
  min-height: 43px;
  font-size: 13px;
  font-weight: 650;
}

.history-card {
  overflow: hidden;
  border: 1px solid #dfe6e2;
  border-radius: 11px;
  background: #ffffff;
}

.history-header {
  padding: 16px 18px;
  border-bottom: 1px solid #e5ebe7;
  background: #fafcfb;
}

.table {
  font-size: 11px;
}

.table thead th {
  color: #45534b;
  background: #f5f8f6;
  font-weight: 700;
  white-space: nowrap;
}

.table td {
  color: #536058;
}

@media (max-width: 1199.98px) {
  .vaccine-list {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 767.98px) {
  .page-header,
  .result-header,
  .form-footer {
    align-items: stretch;
    flex-direction: column;
  }

  .form-body {
    padding: 12px;
  }

  .patient-strip {
    grid-template-columns: 1fr;
  }

  .patient-item {
    border-right: 0;
    border-bottom: 1px solid #e7ece9;
  }

  .patient-item:last-child {
    border-bottom: 0;
  }

  .save-button {
    width: 100%;
  }

  .vaccine-card {
    grid-template-columns: 22px minmax(0, 1fr);
  }

  .record-status {
    grid-column: 2;
    grid-row: auto;
    justify-self: start;
  }

  .regional-badge {
    grid-column: 2;
  }
}
</style>
