<template>
  <div class="card border-0 shadow-sm rounded-4 p-3">
    <div
      class="d-flex justify-content-between align-items-center mb-3"
    >
      <h5 class="fw-semibold text-success mb-0">
        Rujukan / Status Pasien MTBM
      </h5>

      <span
        v-if="kunjunganId"
        class="badge bg-light text-dark border"
      >
        ID: {{ kunjunganId }}
      </span>
    </div>

    <form @submit.prevent="simpanData">
      <div class="row g-3">
        <!-- Kolom kiri -->
        <div class="col-md-6">
          <!-- Status pulang -->
          <div class="mb-3">
            <label class="fw-semibold form-label">
              Status Pulang
              <span class="text-danger">*</span>
            </label>

            <select
              v-model="statusPulang"
              class="form-select"
              :disabled="loadingOptions"
            >
              <option disabled value="">
                -- Pilih Status Pulang --
              </option>

              <option
                v-for="status in statusOptions"
                :key="status"
                :value="status"
              >
                {{ status }}
              </option>
            </select>
          </div>

          <!-- Tenaga medis -->
          <div class="mb-3">
            <label class="fw-semibold form-label">
              Tenaga Medis
            </label>

            <select
              v-model="tenagaMedis"
              class="form-select"
              :disabled="loadingOptions"
            >
              <option value="">
                -- Pilih Tenaga Medis --
              </option>

              <option
                v-for="dokter in dokterOptions"
                :key="dokter.id || dokter.kode || dokter.nama"
                :value="dokter.nama"
              >
                {{ dokter.nama }}
              </option>
            </select>

            <div
              v-if="!loadingOptions && dokterOptions.length === 0"
              class="form-text text-danger"
            >
              Tidak ada dokter aktif yang ditemukan untuk unit ini.
            </div>
          </div>

          <!-- Informasi rujuk internal -->
          <div
            v-if="statusPulang === 'Rujuk Internal'"
            class="alert alert-info py-2 small"
          >
            Rujuk internal mengambil tujuan dari data master
            ruang layanan aktif.
          </div>

          <!-- Informasi rujuk eksternal -->
          <div
            v-if="isRujukEksternal"
            class="alert alert-warning py-2 small"
          >
            Pasien dirujuk keluar fasilitas atau rumah sakit.
          </div>

          <!-- Tombol -->
          <div class="mt-3 d-flex gap-2">
            <button
              type="submit"
              class="btn btn-success"
              :disabled="
                loading ||
                loadingOptions ||
                !kunjunganId
              "
            >
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>

            <button
              type="button"
              class="btn btn-outline-secondary"
              :disabled="loading"
              @click="resetForm"
            >
              Reset
            </button>
          </div>

          <div
            v-if="!kunjunganId"
            class="alert alert-danger mt-3 py-2"
          >
            ID pelayanan / kunjungan tidak ditemukan.
          </div>
        </div>

        <!-- Kolom kanan -->
        <div class="col-md-6">
          <!-- Rujuk internal -->
          <div
            v-if="statusPulang === 'Rujuk Internal'"
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Poli / Ruang Tujuan Internal
              <span class="text-danger">*</span>
            </label>

            <select
              v-model="poliInternal"
              class="form-select"
            >
              <option disabled value="">
                -- Pilih Poli / Ruang Layanan --
              </option>

              <option
                v-for="poli in ruangLayananOptions"
                :key="poli.id"
                :value="poli.name"
              >
                {{ poli.name }}
              </option>
            </select>
          </div>

          <!-- Rujukan eksternal -->
          <div
            v-if="isRujukEksternal"
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              PPK Rujukan
              <span class="text-danger">*</span>
            </label>

            <input
              v-model="ppkRujukan"
              type="text"
              class="form-control"
              placeholder="Contoh: RSUD Blambangan"
            />
          </div>

          <!-- Rumah sakit bukan BPJS -->
          <div
            v-if="
              statusPulang ===
              'Rujuk Rumah Sakit Bukan BPJS'
            "
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Nama Poli Tujuan
            </label>

            <input
              v-model="namaPoli"
              type="text"
              class="form-control"
              placeholder="Contoh: Poli Anak"
            />
          </div>

          <div
            v-if="
              statusPulang ===
              'Rujuk Rumah Sakit Bukan BPJS'
            "
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Nama Dokter
            </label>

            <input
              v-model="namaDokter"
              type="text"
              class="form-control"
              placeholder="Contoh: dr. Nama Dokter"
            />
          </div>

          <!-- PCare -->
          <div
            v-if="statusPulang === 'Rujuk Vertikal PCare'"
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Spesialis / Subspesialis
            </label>

            <input
              v-model="spesialis"
              type="text"
              class="form-control"
              placeholder="Contoh: Anak"
            />
          </div>

          <!-- Tanggal rencana -->
          <div
            v-if="isRujukEksternal"
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Tanggal Rencana Berkunjung
            </label>

            <input
              v-model="tglRencanaBerkunjung"
              type="date"
              class="form-control"
            />
          </div>

          <!-- Catatan -->
          <div
            v-if="
              statusPulang === 'Rujuk Internal'
              || isRujukEksternal
            "
            class="mb-3"
          >
            <label class="fw-semibold form-label">
              Catatan
            </label>

            <textarea
              v-model="catatan"
              class="form-control"
              rows="3"
              placeholder="Tulis catatan rujukan bila perlu"
            ></textarea>
          </div>
        </div>
      </div>
    </form>

    <hr class="my-4" />

    <!-- Riwayat -->
    <div
      class="d-flex justify-content-between align-items-center mb-2"
    >
      <h6 class="fw-semibold mb-0">
        Riwayat Status Pasien MTBM
      </h6>

      <button
        type="button"
        class="btn btn-outline-success btn-sm"
        :disabled="loadingRiwayat || !kunjunganId"
        @click="loadRiwayat"
      >
        {{ loadingRiwayat ? 'Memuat...' : 'Refresh' }}
      </button>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 60px">No</th>
            <th>Asal Poli</th>
            <th>Status</th>
            <th>Keterangan</th>
            <th>Poli Tujuan</th>
            <th>Tenaga Medis</th>
            <th>Created By</th>
            <th>Mulai Melayani</th>
            <th>Selesai Melayani</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(item, index) in dataRujukan"
            :key="item.id"
          >
            <td>{{ index + 1 }}</td>

            <td>
              <span class="badge bg-success">
                {{ item.asalPoli || 'MTBM' }}
              </span>
            </td>

            <td>
              <span :class="badgeClass(item.statusPulang)">
                {{ item.statusPulang || '-' }}
              </span>
            </td>

            <td>
              {{ item.keterangan || '-' }}
            </td>

            <td>
              {{ item.poliTujuan || '-' }}
            </td>

            <td>
              {{ item.tenagaMedis || '-' }}
            </td>

            <td>
              {{ item.createdBy || '-' }}
            </td>

            <td>
              {{ item.mulai || '-' }}
            </td>

            <td>
              {{ item.selesai || '-' }}
            </td>
          </tr>

          <tr v-if="dataRujukan.length === 0">
            <td
              colspan="9"
              class="text-center text-muted py-4"
            >
              {{
                loadingRiwayat
                  ? 'Memuat data...'
                  : 'Belum ada data'
              }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  computed,
  onMounted,
  watch,
} from 'vue'

import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  idPelayanan: {
    type: [String, Number],
    default: null,
  },
})

const page = usePage()

/*
|--------------------------------------------------------------------------
| ID pelayanan / kunjungan
|--------------------------------------------------------------------------
*/
const kunjunganId = computed(() => {
  return (
    props.idPelayanan ||
    page.props?.idPelayanan ||
    page.props?.idpelayanan ||
    page.props?.DataPasien?.[0]?.idpelayanan ||
    ''
  )
})

/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/
const loading = ref(false)
const loadingOptions = ref(false)
const loadingRiwayat = ref(false)

/*
|--------------------------------------------------------------------------
| Data opsi
|--------------------------------------------------------------------------
*/
const unitInfo = ref(null)
const statusOptions = ref([])
const ruangLayananOptions = ref([])
const dokterOptions = ref([])

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/
const statusPulang = ref('')
const tenagaMedis = ref('')
const poliInternal = ref('')

const ppkRujukan = ref('')
const namaPoli = ref('')
const namaDokter = ref('')
const spesialis = ref('')
const catatan = ref('')
const tglRencanaBerkunjung = ref('')

/*
|--------------------------------------------------------------------------
| Riwayat
|--------------------------------------------------------------------------
*/
const dataRujukan = ref([])

/*
|--------------------------------------------------------------------------
| Status rujukan eksternal
|--------------------------------------------------------------------------
*/
const isRujukEksternal = computed(() => {
  return (
    statusPulang.value === 'Rujuk Vertikal PCare' ||
    statusPulang.value ===
      'Rujuk Rumah Sakit Bukan BPJS' ||
    statusPulang.value === 'Rujuk Rumah Sakit'
  )
})

/*
|--------------------------------------------------------------------------
| Reset field tambahan
|--------------------------------------------------------------------------
*/
const resetTambahan = () => {
  poliInternal.value = ''
  ppkRujukan.value = ''
  namaPoli.value = ''
  namaDokter.value = ''
  spesialis.value = ''
  catatan.value = ''
  tglRencanaBerkunjung.value = ''
}

const resetForm = () => {
  statusPulang.value =
    statusOptions.value.length > 0
      ? statusOptions.value[0]
      : ''

  tenagaMedis.value = ''

  resetTambahan()
}

/*
|--------------------------------------------------------------------------
| Bersihkan data rujukan saat status pulang berubah
|--------------------------------------------------------------------------
*/
watch(statusPulang, (statusBaru, statusLama) => {
  if (statusBaru !== statusLama) {
    resetTambahan()
  }
})

/*
|--------------------------------------------------------------------------
| Mengambil opsi dari controller
|--------------------------------------------------------------------------
*/
const loadOptions = async () => {
  try {
    loadingOptions.value = true

    const response = await axios.get(
      '/simpus/kia/mtbm/statuspasien/options'
    )

    const responseData = response.data || {}

    unitInfo.value = responseData.unit || null

    ruangLayananOptions.value = Array.isArray(
      responseData.ruang_layanan
    )
      ? responseData.ruang_layanan
      : []

    dokterOptions.value = Array.isArray(
      responseData.dokter
    )
      ? responseData.dokter
      : []

    if (Array.isArray(responseData.status_pulang)) {
      statusOptions.value =
        responseData.status_pulang
          .map((item) => {
            if (typeof item === 'string') {
              return item
            }

            return (
              item.nama ||
              item.nmStatusPulang ||
              item.name ||
              item.label ||
              item.value ||
              ''
            )
          })
          .filter(Boolean)
    } else {
      statusOptions.value = []
    }

    if (
      !statusPulang.value &&
      statusOptions.value.length > 0
    ) {
      statusPulang.value = statusOptions.value[0]
    }
  } catch (error) {
    console.error(
      'LOAD OPTIONS STATUS PASIEN MTBM ERROR:',
      error.response?.data || error
    )

    unitInfo.value = null
    statusOptions.value = []
    ruangLayananOptions.value = []
    dokterOptions.value = []

    alert(
      error.response?.data?.message ||
        'Gagal memuat opsi status pasien MTBM.'
    )
  } finally {
    loadingOptions.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Mengambil riwayat status pasien
|--------------------------------------------------------------------------
*/
const loadRiwayat = async () => {
  if (!kunjunganId.value) {
    console.error(
      'ID pelayanan kosong. Riwayat status pasien MTBM tidak dapat dimuat.'
    )

    dataRujukan.value = []
    return
  }

  try {
    loadingRiwayat.value = true

    const response = await axios.get(
      '/simpus/kia/mtbm/statuspasien',
      {
        params: {
          kunjungan_id: String(kunjunganId.value),
        },
      }
    )

    dataRujukan.value = Array.isArray(
      response.data?.data
    )
      ? response.data.data
      : []
  } catch (error) {
    console.error(
      'LOAD STATUS PASIEN MTBM ERROR:',
      error.response?.data || error
    )

    dataRujukan.value = []

    alert(
      error.response?.data?.message ||
        'Gagal memuat riwayat status pasien MTBM.'
    )
  } finally {
    loadingRiwayat.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Validasi sebelum simpan
|--------------------------------------------------------------------------
*/
const validasiSebelumSimpan = () => {
  if (!kunjunganId.value) {
    alert('ID pelayanan / kunjungan tidak ditemukan.')
    return false
  }

  if (!statusPulang.value) {
    alert('Status pulang wajib dipilih.')
    return false
  }

  if (
    statusPulang.value === 'Rujuk Internal' &&
    !poliInternal.value
  ) {
    alert(
      'Poli / ruang tujuan internal wajib dipilih.'
    )

    return false
  }

  if (
    isRujukEksternal.value &&
    !ppkRujukan.value
  ) {
    alert('PPK rujukan wajib diisi.')
    return false
  }

  return true
}

/*
|--------------------------------------------------------------------------
| Menyimpan status pasien
|--------------------------------------------------------------------------
*/
const simpanData = async () => {
  if (!validasiSebelumSimpan()) {
    return
  }

  try {
    loading.value = true

    const payload = {
      kunjungan_id: String(kunjunganId.value),

      statusPulang: statusPulang.value,

      /*
       * Berisi nama dokter dari master_dokter.nmDokter.
       */
      tenagaMedis: tenagaMedis.value || null,

      poliInternal:
        statusPulang.value === 'Rujuk Internal'
          ? poliInternal.value
          : null,

      ppkRujukan:
        isRujukEksternal.value
          ? ppkRujukan.value || null
          : null,

      namaPoli:
        statusPulang.value ===
        'Rujuk Rumah Sakit Bukan BPJS'
          ? namaPoli.value || null
          : null,

      namaDokter:
        statusPulang.value ===
        'Rujuk Rumah Sakit Bukan BPJS'
          ? namaDokter.value || null
          : null,

      spesialis:
        statusPulang.value ===
        'Rujuk Vertikal PCare'
          ? spesialis.value || null
          : null,

      catatan:
        statusPulang.value === 'Rujuk Internal' ||
        isRujukEksternal.value
          ? catatan.value || null
          : null,

      tglRencanaBerkunjung:
        isRujukEksternal.value
          ? tglRencanaBerkunjung.value || null
          : null,
    }

    console.log(
      'PAYLOAD STATUS PASIEN MTBM:',
      payload
    )

    const response = await axios.post(
      '/simpus/kia/mtbm/statuspasien/store',
      payload
    )

    alert(
      response.data?.message ||
        'Status pasien MTBM berhasil disimpan.'
    )

    resetForm()
    await loadRiwayat()
  } catch (error) {
    console.error(
      'SAVE STATUS PASIEN MTBM ERROR:',
      error.response?.data || error
    )

    if (error.response?.status === 422) {
      const errors =
        error.response?.data?.errors || {}

      const daftarError = Object.values(errors)
        .flat()
        .join('\n')

      alert(
        daftarError
          ? `Validasi gagal:\n${daftarError}`
          : 'Validasi data gagal.'
      )

      return
    }

    alert(
      'Gagal menyimpan data:\n' +
        (
          error.response?.data?.error ||
          error.response?.data?.message ||
          'Periksa console browser dan laravel.log.'
        )
    )
  } finally {
    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Warna badge status
|--------------------------------------------------------------------------
*/
const badgeClass = (status) => {
  if (status === 'Rujuk Internal') {
    return 'badge bg-info text-dark'
  }

  if (
    status === 'Rujuk Vertikal PCare' ||
    status === 'Rujuk Rumah Sakit Bukan BPJS' ||
    status === 'Rujuk Rumah Sakit'
  ) {
    return 'badge bg-warning text-dark'
  }

  if (status === 'Meninggal') {
    return 'badge bg-danger'
  }

  if (status === 'Berobat Jalan') {
    return 'badge bg-success'
  }

  return 'badge bg-secondary'
}

/*
|--------------------------------------------------------------------------
| Load awal
|--------------------------------------------------------------------------
*/
onMounted(async () => {
  await loadOptions()
  await loadRiwayat()
})
</script>

<style scoped>
.card {
  background: #ffffff;
}

.form-label {
  font-size: 0.9rem;
}

.form-control,
.form-select {
  font-size: 0.9rem;
}

.table th,
.table td {
  font-size: 0.86rem;
  vertical-align: middle;
}

.table th {
  white-space: nowrap;
}

.badge {
  font-weight: 500;
}

textarea {
  resize: vertical;
}
</style>