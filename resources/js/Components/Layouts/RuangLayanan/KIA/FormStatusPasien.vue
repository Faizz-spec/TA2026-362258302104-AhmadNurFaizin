<template>
  <div class="card border-0 shadow-sm rounded-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold text-success mb-0">
        Rujukan / Status Pasien
      </h5>

      <span v-if="kunjunganId" class="badge bg-light text-dark border">
        ID: {{ kunjunganId }}
      </span>
    </div>

    <form @submit.prevent="simpanData">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="fw-semibold form-label">Status Pulang</label>

            <select v-model="statusPulang" class="form-select">
              <option disabled value="">-- Pilih Status Pulang --</option>

              <option
                v-for="status in statusOptions"
                :key="status"
                :value="status"
              >
                {{ status }}
              </option>
            </select>

            <div v-if="statusOptions.length === 0" class="form-text text-danger">
              Data status pulang kosong. Cek tabel simpus_statuspulang / endpoint options.
            </div>
          </div>

          <div class="mb-3 position-relative">
            <label class="fw-semibold form-label">Tenaga Medis</label>

            <input
              v-model="searchDokter"
              type="text"
              class="form-control"
              placeholder="Cari nama dokter..."
              autocomplete="off"
              @focus="showDokterList = true"
            />

            <div
              v-if="showDokterList"
              class="dokter-dropdown border rounded-3 bg-white shadow-sm mt-1"
            >
              <button
                v-for="dokter in filteredDokterOptions"
                :key="dokter.id"
                type="button"
                class="dropdown-item dokter-item text-start"
                @click="pilihDokter(dokter)"
              >
                {{ dokter.nama }}
              </button>

              <div
                v-if="filteredDokterOptions.length === 0"
                class="px-3 py-2 text-muted small"
              >
                Dokter tidak ditemukan.
              </div>
            </div>

            <div v-if="tenagaMedis" class="form-text text-success">
              Terpilih: {{ tenagaMedis }}
            </div>

            <div v-if="dokterOptions.length === 0" class="form-text text-danger">
              Data dokter kosong. Cek tabel master_dokter / pusk_id / aktif.
            </div>
          </div>

          <div v-if="statusPulang === 'Rujuk Internal'" class="alert alert-info py-2 small">
            Rujuk internal mengambil tujuan dari data master ruang layanan aktif.
          </div>

          <div v-if="isRujukEksternal" class="alert alert-warning py-2 small">
            Pasien dirujuk keluar fasilitas / rumah sakit.
          </div>

          <div class="mt-3 d-flex gap-2">
            <button
              type="submit"
              class="btn btn-success"
              :disabled="loading || !kunjunganId"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>

            <button
              type="button"
              class="btn btn-outline-secondary"
              @click="resetForm"
            >
              Reset
            </button>
          </div>

          <div v-if="!kunjunganId" class="alert alert-danger mt-3 py-2">
            ID pelayanan / kunjungan tidak ditemukan.
          </div>
        </div>

        <div class="col-md-6">
          <div v-if="statusPulang === 'Rujuk Internal'" class="mb-3">
            <label class="fw-semibold form-label">
              Poli / Ruang Tujuan Internal
              <span class="text-danger">*</span>
            </label>

            <select v-model="poliInternal" class="form-select">
              <option disabled value="">-- Pilih Poli / Ruang Layanan --</option>

              <option
                v-for="poli in ruangLayananOptions"
                :key="poli.id"
                :value="poli.name"
              >
                {{ poli.name }}
              </option>
            </select>

            <div v-if="ruangLayananOptions.length === 0" class="form-text text-danger">
              Data ruang layanan kosong. Cek master_ruang_layanan / endpoint options.
            </div>
          </div>

          <div v-if="isRujukEksternal" class="mb-3">
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

          <div v-if="statusPulang === 'Rujuk Rumah Sakit Bukan BPJS'" class="mb-3">
            <label class="fw-semibold form-label">Nama Poli Tujuan</label>

            <input
              v-model="namaPoli"
              type="text"
              class="form-control"
              placeholder="Contoh: Poli Anak"
            />
          </div>

          <div v-if="statusPulang === 'Rujuk Rumah Sakit Bukan BPJS'" class="mb-3">
            <label class="fw-semibold form-label">Nama Dokter Tujuan</label>

            <input
              v-model="namaDokter"
              type="text"
              class="form-control"
              placeholder="Contoh: dr. ..."
            />
          </div>

          <div v-if="statusPulang === 'Rujuk Vertikal PCare'" class="mb-3">
            <label class="fw-semibold form-label">Spesialis/SubSpesialis</label>

            <input
              v-model="spesialis"
              type="text"
              class="form-control"
              placeholder="Contoh: Anak"
            />
          </div>

          <div v-if="isRujukEksternal" class="mb-3">
            <label class="fw-semibold form-label">Tanggal Rencana Berkunjung</label>

            <input
              v-model="tglRencanaBerkunjung"
              type="date"
              class="form-control"
            />
          </div>

          <div v-if="statusPulang === 'Rujuk Internal' || isRujukEksternal" class="mb-3">
            <label class="fw-semibold form-label">Catatan</label>

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

    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="fw-semibold mb-0">Riwayat Status Pasien</h6>

      <button
        type="button"
        class="btn btn-outline-success btn-sm"
        @click="loadRiwayat"
        :disabled="loadingRiwayat || !kunjunganId"
      >
        {{ loadingRiwayat ? 'Memuat...' : 'Refresh' }}
      </button>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 60px;">No</th>
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
          <tr v-for="(item, index) in dataRujukan" :key="item.id">
            <td>{{ index + 1 }}</td>

            <td>
              <span class="badge bg-success">
                {{ item.asalPoli || 'MTBS' }}
              </span>
            </td>

            <td>
              <span :class="badgeClass(item.statusPulang)">
                {{ item.statusPulang || '-' }}
              </span>
            </td>

            <td>{{ item.keterangan || '-' }}</td>
            <td>{{ item.poliTujuan || '-' }}</td>
            <td>{{ item.tenagaMedis || '-' }}</td>
            <td>{{ item.createdBy || '-' }}</td>
            <td>{{ item.mulai || '-' }}</td>
            <td>{{ item.selesai || '-' }}</td>
          </tr>

          <tr v-if="dataRujukan.length === 0">
            <td colspan="9" class="text-center text-muted py-4">
              Belum ada data
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  idPelayanan: {
    type: [String, Number],
    default: null,
  },
})

const page = usePage()

const kunjunganId = computed(() => {
  return props.idPelayanan || page.props.idPelayanan || page.props.idpelayanan || ''
})

const loading = ref(false)
const loadingRiwayat = ref(false)

const unitInfo = ref(null)

const statusOptions = ref([])
const ruangLayananOptions = ref([])
const dokterOptions = ref([])

const statusPulang = ref('')
const tenagaMedis = ref('')
const searchDokter = ref('')
const showDokterList = ref(false)

const poliInternal = ref('')
const ppkRujukan = ref('')
const namaPoli = ref('')
const namaDokter = ref('')
const spesialis = ref('')
const catatan = ref('')
const tglRencanaBerkunjung = ref('')

const dataRujukan = ref([])

const isRujukEksternal = computed(() => {
  return (
    statusPulang.value === 'Rujuk Vertikal PCare' ||
    statusPulang.value === 'Rujuk Rumah Sakit Bukan BPJS' ||
    statusPulang.value === 'Rujuk Rumah Sakit'
  )
})

const filteredDokterOptions = computed(() => {
  const keyword = searchDokter.value.toLowerCase().trim()

  let data = dokterOptions.value

  if (keyword) {
    data = data.filter((dokter) => {
      return String(dokter.nama || '').toLowerCase().includes(keyword)
    })
  }

  return data.slice(0, 5)
})

const pilihDokter = (dokter) => {
  tenagaMedis.value = dokter.nama
  searchDokter.value = dokter.nama
  showDokterList.value = false
}

watch(searchDokter, (value) => {
  showDokterList.value = true

  if (value !== tenagaMedis.value) {
    tenagaMedis.value = ''
  }
})

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
  statusPulang.value = statusOptions.value[0] || ''
  tenagaMedis.value = ''
  searchDokter.value = ''
  showDokterList.value = false
  resetTambahan()
}

watch(statusPulang, () => {
  resetTambahan()
})

const loadOptions = async () => {
  try {
    const res = await axios.get('/simpus/kia/mtbs/statuspasien/options')

    unitInfo.value = res.data?.unit || null
    ruangLayananOptions.value = res.data?.ruang_layanan || []
    dokterOptions.value = res.data?.dokter || []

    if (Array.isArray(res.data?.status_pulang)) {
      statusOptions.value = res.data.status_pulang
        .map((item) => {
          if (typeof item === 'string') return item
          return item.nama || item.nmStatusPulang || item.name || ''
        })
        .filter(Boolean)
    }

    if (!statusPulang.value && statusOptions.value.length > 0) {
      statusPulang.value = statusOptions.value[0]
    }
  } catch (e) {
    console.error('LOAD OPTIONS STATUS PASIEN ERROR:', e.response?.data || e)

    statusOptions.value = []
    ruangLayananOptions.value = []
    dokterOptions.value = []
    unitInfo.value = null

    alert('Gagal memuat opsi status pulang / ruang layanan / dokter. Cek route dan controller options.')
  }
}

const loadRiwayat = async () => {
  if (!kunjunganId.value) {
    console.error('ID pelayanan kosong, riwayat status pasien tidak bisa dimuat')
    return
  }

  try {
    loadingRiwayat.value = true

    const res = await axios.get('/simpus/kia/mtbs/statuspasien', {
      params: {
        kunjungan_id: String(kunjunganId.value),
      },
    })

    dataRujukan.value = res.data?.data || []
  } catch (e) {
    console.error('LOAD STATUSPASIEN ERROR:', e.response?.data || e)
  } finally {
    loadingRiwayat.value = false
  }
}

onMounted(async () => {
  await loadOptions()
  await loadRiwayat()
})

const validasiSebelumSimpan = () => {
  if (!kunjunganId.value) {
    alert('ID pelayanan / kunjungan tidak ditemukan.')
    return false
  }

  if (!statusPulang.value) {
    alert('Status pulang wajib dipilih.')
    return false
  }

  if (statusPulang.value === 'Rujuk Internal' && !poliInternal.value) {
    alert('Poli / ruang tujuan internal wajib dipilih.')
    return false
  }

  if (isRujukEksternal.value && !ppkRujukan.value) {
    alert('PPK rujukan wajib diisi.')
    return false
  }

  return true
}

const simpanData = async () => {
  if (!validasiSebelumSimpan()) {
    return
  }

  try {
    loading.value = true

    const payload = {
      kunjungan_id: String(kunjunganId.value),
      statusPulang: statusPulang.value,
      tenagaMedis: tenagaMedis.value || null,

      poliInternal: statusPulang.value === 'Rujuk Internal'
        ? poliInternal.value
        : null,

      ppkRujukan: ppkRujukan.value || null,
      namaPoli: namaPoli.value || null,
      namaDokter: namaDokter.value || null,
      spesialis: spesialis.value || null,
      catatan: catatan.value || null,
      tglRencanaBerkunjung: tglRencanaBerkunjung.value || null,
    }

    console.log('PAYLOAD STATUS PASIEN:', payload)

    await axios.post('/simpus/kia/mtbs/statuspasien/store', payload)

    alert('✅ Data berhasil disimpan!')
    resetForm()
    await loadRiwayat()
  } catch (e) {
    console.error('SAVE STATUSPASIEN ERROR:', e.response?.data || e)

    if (e.response?.status === 422) {
      alert('Validasi gagal:\n' + JSON.stringify(e.response?.data?.errors, null, 2))
      return
    }

    alert(
      '❌ Gagal simpan data!\n' +
        (e.response?.data?.error ||
          e.response?.data?.message ||
          'Cek console / laravel.log')
    )
  } finally {
    loading.value = false
  }
}

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

  return 'badge bg-secondary'
}
</script>

<style scoped>
.card {
  background: #fff;
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

.dokter-dropdown {
  position: absolute;
  z-index: 50;
  width: 100%;
  max-height: 220px;
  overflow-y: auto;
}

.dokter-item {
  padding: 8px 12px;
  font-size: 0.9rem;
}

.dokter-item:hover {
  background: #f1f8f4;
}
</style>