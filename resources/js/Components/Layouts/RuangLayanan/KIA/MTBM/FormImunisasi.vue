<template>
  <div class="container-fluid p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold text-success mb-0">Imunisasiw</h5>
    </div>

    <!-- ALERT aturan MTBS -->
    <div v-if="rujukSegera" class="alert alert-warning">
      <b>Imunisasi ditunda</b> karena anak <b>akan dirujuk segera</b> (sesuai MTBS).
      Catat dan nasihati ibu untuk kembali setelah kondisi stabil/selesai rujuk.
    </div>

    <div v-else-if="!sakitRingan" class="alert alert-warning">
      Anak tidak termasuk “sehat/sakit ringan”. Sesuai prinsip MTBS, tunda imunisasi sampai kondisi stabil
      (atau ikut kebijakan klinis dokter).
    </div>

    <div class="row">
      <!-- Form Imunisasi -->
      <div class="col-md-6">
        <form @submit.prevent="simpanImunisasi" class="border-0 rounded p-3 shadow-sm">
          <!-- Tanggal Imunisasi -->
          <div class="mb-3 row align-items-center">
            <label class="col-sm-4 col-form-label fw-semibold">Tgl Imunisasi</label>
            <div class="col-sm-8">
              <input
                type="datetime-local"
                class="form-control"
                v-model="form.tanggal"
                :disabled="isFormDisabled || loading"
              />
            </div>
          </div>

          <!-- Umur Pasien -->
          <div class="mb-3 row align-items-center">
            <label class="col-sm-4 col-form-label fw-semibold">Umur</label>
            <div class="col-sm-8">
              <input class="form-control" :value="umurDisplay" disabled />
              <small class="text-muted">
                (Dipakai untuk menyarankan imunisasi sesuai umur MTBS)
              </small>
            </div>
          </div>

          <!-- Jenis Imunisasi -->
          <div class="mb-3 row align-items-center">
            <label class="col-sm-4 col-form-label fw-semibold">
              Jenis Imunisasi<span class="text-danger">*</span>
            </label>
            <div class="col-sm-8">
              <select class="form-select" v-model="form.jenis" :disabled="isFormDisabled || loading">
                <option value="">-- Pilih Jenis Imunisasi --</option>

                <optgroup label="Disarankan sesuai umur (MTBS)">
                  <option v-for="v in imunisasiSesuaiUmur" :key="v.code" :value="v.code">
                    {{ v.label }}
                  </option>
                </optgroup>

                <optgroup
                  v-if="imunisasiLain.length"
                  label="Lainnya (tetap sesuai MTBS, tapi di luar umur rekomendasi)"
                >
                  <option v-for="v in imunisasiLain" :key="v.code" :value="v.code">
                    {{ v.label }}
                  </option>
                </optgroup>
              </select>

              <small class="text-muted d-block mt-1">
                *Catatan: JE & PCV dilakukan di daerah terpilih (sesuai MTBS).
              </small>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-auto">
              <button type="submit" class="btn btn-success fw-semibold px-3" :disabled="isFormDisabled || loading">
                {{ loading ? 'Menyimpan...' : 'Simpan Data' }}
              </button>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-primary fw-semibold px-3" @click="loadRiwayat" :disabled="loading">
                {{ loading ? 'Loading...' : 'Lihat Riwayat Imunisasi' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="mt-4">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Tanggal Imunisasi</th>
            <th>Umur</th>
            <th>Imunisasi</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(item, index) in dataImunisasi" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ formatTanggal(item.tanggal) }}</td>
            <td>{{ formatUmurDb(item.umur_tahun, item.umur_bulan) }}</td>
            <td>{{ labelByCode(item.jenis) }}</td>
            <td>{{ item.createdBy || '-' }}</td>
            <td>
              <button class="btn btn-danger btn-sm" @click="hapusData(item.id)" :disabled="loading">
                Hapus
              </button>
            </td>
          </tr>

          <tr v-if="dataImunisasi.length === 0">
            <td colspan="6" class="text-center text-muted">Belum ada data imunisasi</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue"
import axios from "axios"
import { usePage } from "@inertiajs/vue3"

/**
 * Props tambahan supaya sesuai pedoman MTBS:
 * - rujukSegera: jika true => imunisasi DITUNDA (disable form)
 * - sakitRingan: jika false => sebaiknya tunda (aku disable biar aman)
 */
const props = defineProps({
  DataPasien: Array,
  rujukSegera: { type: Boolean, default: false },
  sakitRingan: { type: Boolean, default: true },
})

const page = usePage()
const idPelayanan = page.props.idPelayanan // kunjungan_id

const loading = ref(false)

const pasien = computed(() => props.DataPasien?.[0] ?? null)

const form = ref({
  tanggal: new Date().toISOString().slice(0, 16),
  jenis: "",
  umurBulan: null, // umur bulan hasil parsing
  umurText: "",    // tampilannya
})

/** Parser umur: dukung number, "10 bulan", "1 tahun 3 bulan", "2 th", dll */
function parseUmurToMonths(umurRaw) {
  if (umurRaw === null || umurRaw === undefined) return null
  if (typeof umurRaw === "number" && !Number.isNaN(umurRaw)) return umurRaw

  const s = String(umurRaw).toLowerCase().trim()
  if (!s) return null

  const yearMatch = s.match(/(\d+)\s*(tahun|th|taun)/)
  const monthMatch = s.match(/(\d+)\s*(bulan|bln)/)

  let months = 0
  if (yearMatch) months += parseInt(yearMatch[1], 10) * 12
  if (monthMatch) months += parseInt(monthMatch[1], 10)

  if (!yearMatch && !monthMatch) {
    const n = parseInt(s.replace(/[^\d]/g, ""), 10)
    return Number.isNaN(n) ? null : n
  }

  return months
}

function formatUmur(months) {
  if (months === null || months === undefined) return "-"
  const y = Math.floor(months / 12)
  const m = months % 12
  if (y === 0) return `${m} bulan`
  if (m === 0) return `${y} tahun`
  return `${y} tahun ${m} bulan`
}

// Update umur ketika props.DataPasien tersedia
watch(
  () => props.DataPasien,
  (newVal) => {
    if (Array.isArray(newVal) && newVal.length > 0) {
      const umurRaw = newVal[0]?.umur // sesuaikan kalau fieldmu beda
      const umurBulan = parseUmurToMonths(umurRaw)
      form.value.umurBulan = umurBulan
      form.value.umurText =
        umurBulan !== null ? formatUmur(umurBulan) : (umurRaw ? String(umurRaw) : "-")
    }
  },
  { immediate: true }
)

const umurDisplay = computed(() => form.value.umurText || "-")

/**
 * Jadwal imunisasi MTBS (disederhanakan berdasarkan umur bulan)
 */
const daftarImunisasi = [
  { code: "HB0", label: "HB-0 (0–24 jam)", min: 0, max: 0 },
  { code: "BCG", label: "BCG (1 bulan)", min: 1, max: 11 },
  { code: "OPV0", label: "OPV-0 / Polio tetes 0 (1 bulan)", min: 1, max: 11 },

  { code: "DPT_HB_HIB_1", label: "DPT-HB-Hib 1 (2 bulan)", min: 2, max: 11 },
  { code: "OPV1", label: "OPV 1 (2 bulan)", min: 2, max: 11 },
  { code: "PCV1", label: "PCV 1 (2 bulan) *daerah terpilih", min: 2, max: 11 },

  { code: "DPT_HB_HIB_2", label: "DPT-HB-Hib 2 (3 bulan)", min: 3, max: 11 },
  { code: "OPV2", label: "OPV 2 (3 bulan)", min: 3, max: 11 },
  { code: "PCV2", label: "PCV 2 (3 bulan) *daerah terpilih", min: 3, max: 11 },

  { code: "DPT_HB_HIB_3", label: "DPT-HB-Hib 3 (4 bulan)", min: 4, max: 11 },
  { code: "OPV3", label: "OPV 3 (4 bulan)", min: 4, max: 11 },
  { code: "IPV", label: "IPV / Polio suntik (4 bulan)", min: 4, max: 11 },

  { code: "MR9", label: "Campak Rubella (9 bulan)", min: 9, max: 59 },

  { code: "JE10", label: "Japanese Encephalitis (10 bulan) *daerah terpilih", min: 10, max: 59 },
  { code: "PCV3_12", label: "PCV 3 (12 bulan) *daerah terpilih", min: 12, max: 59 },
  { code: "BOOSTER18", label: "DPT-HB-Hib + Campak Rubella (18 bulan)", min: 18, max: 59 },
]

const imunisasiSesuaiUmur = computed(() => {
  const age = form.value.umurBulan
  if (age === null) return []
  return daftarImunisasi.filter((v) => age >= v.min && age <= v.max)
})

const imunisasiLain = computed(() => {
  const age = form.value.umurBulan
  if (age === null) return daftarImunisasi
  return daftarImunisasi.filter((v) => !(age >= v.min && age <= v.max))
})

const isFormDisabled = computed(() => props.rujukSegera || !props.sakitRingan)

const dataImunisasi = ref([])

function labelByCode(code) {
  const found = daftarImunisasi.find((v) => v.code === code)
  return found ? found.label : code
}

function formatTanggal(val) {
  if (!val) return "-"
  // dari MySQL biasanya: "YYYY-MM-DD HH:mm:ss"
  return String(val).replace("T", " ").slice(0, 16)
}

function formatUmurDb(umurTahun, umurBulan) {
  const y = (umurTahun === null || umurTahun === undefined) ? null : Number(umurTahun)
  const m = (umurBulan === null || umurBulan === undefined) ? null : Number(umurBulan)
  if (y === null && m === null) return "-"
  if (y !== null && (m === null || Number.isNaN(m))) return `${y} tahun`
  if ((y === null || Number.isNaN(y)) && m !== null) return `${m} bulan`
  if (m === 0) return `${y} tahun`
  return `${y} tahun ${m} bulan`
}

async function loadRiwayat() {
  if (!idPelayanan || !pasien.value?.ID) return
  try {
    loading.value = true
    const res = await axios.get("/simpus/kia/mtbs/imunisasi", {
      params: {
        kunjungan_id: String(idPelayanan),
        pasien_id: pasien.value.ID,
      },
    })
    dataImunisasi.value = res.data?.data || []
  } catch (error) {
    console.error(error.response?.data || error)
    alert("Gagal mengambil riwayat imunisasi")
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadRiwayat()
})

async function simpanImunisasi() {
  // Guard pedoman MTBS:
  if (props.rujukSegera) {
    alert("Imunisasi ditunda karena anak akan dirujuk segera (sesuai MTBS).")
    return
  }
  if (!props.sakitRingan) {
    alert("Anak tidak termasuk sehat/sakit ringan. Pertimbangkan tunda imunisasi sampai stabil.")
    return
  }

  if (!form.value.jenis) {
    alert("Jenis imunisasi wajib diisi!")
    return
  }
  if (!pasien.value?.ID) {
    alert("Data pasien tidak ditemukan!")
    return
  }

  // konversi umur bulan -> tahun/bulan untuk backend
  let umur_tahun = null
  let umur_bulan = null
  if (typeof form.value.umurBulan === "number" && !Number.isNaN(form.value.umurBulan)) {
    umur_tahun = Math.floor(form.value.umurBulan / 12)
    umur_bulan = form.value.umurBulan % 12
  }

  const payload = {
    kunjungan_id: String(idPelayanan),
    pasien_id: pasien.value.ID,
    tanggal: form.value.tanggal,
    jenis: form.value.jenis,
    umur_tahun,
    umur_bulan,
  }

  try {
    loading.value = true
    await axios.post("/simpus/kia/mtbs/imunisasi/store", payload)

    alert("Data imunisasi berhasil disimpan!")
    form.value.jenis = ""

    await loadRiwayat()
  } catch (error) {
    console.error(error.response?.data || error)

    if (error.response?.status === 422) {
      alert("Validasi gagal:\n" + JSON.stringify(error.response?.data?.errors, null, 2))
      return
    }

    alert("Gagal menyimpan data imunisasi")
  } finally {
    loading.value = false
  }
}

async function hapusData(id) {
  if (!confirm("Yakin hapus data imunisasi ini?")) return

  try {
    loading.value = true
    await axios.delete(`/simpus/kia/mtbs/imunisasi/${id}`)
    await loadRiwayat()
  } catch (error) {
    console.error(error.response?.data || error)
    alert("Gagal menghapus data imunisasi")
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.table th,
.table td {
  vertical-align: middle;
}
form {
  max-width: 500px;
}
button.btn-primary {
  background-color: #428bca;
  border-color: #357ebd;
}
button.btn-primary:hover {
  background-color: #3071a9;
}
</style>
