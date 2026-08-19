<template>
  <div class="bg-white p-3 rounded-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="fw-bold text-success mb-1">Assessment MTBMe</h5>
      </div>

      <button
        class="btn btn-success btn-sm"
        :disabled="!pasien || loading"
        @click="generateAssessment"
      >
        {{ loading ? 'Menghitung...' : 'Generate Assessment' }}
      </button>
    </div>

    <div class="row g-3">
      <!-- KIRI -->
      <div class="col-md-6">
        <!-- FORM DIAGNOSA -->
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Input Diagnosa Medis</h6>

          <div class="mb-2">
            <label class="form-label small fw-semibold">Diagnosa</label>
            <div class="input-group input-group-sm">
              <input
                v-model="displayDiagnosa"
                type="text"
                class="form-control"
                placeholder="Belum memilih diagnosa"
                readonly
              />
              <button
                class="btn btn-outline-success"
                type="button"
                @click="bukaModalDiagnosa"
              >
                Cari
              </button>
            </div>
          </div>

          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Kode</label>
              <input
                v-model="formDiagnosa.kodeDiagnosa"
                type="text"
                class="form-control form-control-sm"
                readonly
              />
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-semibold">Kasus</label>
              <select v-model="formDiagnosa.kasus" class="form-select form-select-sm">
                <option value="">- Pilih -</option>
                <option value="baru">Baru</option>
                <option value="lama">Lama</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-semibold">Keterangan</label>
              <input
                v-model="formDiagnosa.keterangan"
                type="text"
                class="form-control form-control-sm"
                placeholder="Opsional"
              />
            </div>
          </div>

          <div class="text-end mt-3">
            <button
              class="btn btn-success btn-sm"
              :disabled="!pasien || loadingDiagnosa"
              @click="simpanDiagnosaMedis"
            >
              {{ loadingDiagnosa ? 'Menyimpan...' : 'Simpan Diagnosa' }}
            </button>
          </div>
        </div>

        <!-- DIAGNOSA SAAT INI -->
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Diagnosa Medis Saat Ini</h6>

          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 45px;">No</th>
                  <th style="width: 90px;">Kode</th>
                  <th>Nama Diagnosa</th>
                  <th>Keterangan</th>
                  <th style="width: 80px;">Kasus</th>
                  <th style="width: 80px;">Poli</th>
                  <th style="width: 80px;">Action</th>
                </tr>
              </thead>

              <tbody>
                <tr v-if="listDiagnosa.length === 0">
                  <td colspan="7" class="text-center text-muted">
                    Belum ada diagnosa medis
                  </td>
                </tr>

                <tr v-for="(item, index) in listDiagnosa" :key="item.id || index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.kodeDiagnosa || item.kdDiag || '-' }}</td>
                  <td>{{ item.namaDiagnosa || item.nmDiag || '-' }}</td>
                  <td>{{ item.keterangan || '-' }}</td>
                  <td>{{ labelKasus(item.kasus) }}</td>
                  <td>{{ item.poli || pasien?.nmPoli || '-' }}</td>
                  <td>
                    <button
                      class="btn btn-danger btn-sm"
                      :disabled="!item.id || loadingDiagnosa"
                      @click="hapusDiagnosaMedis(item.id)"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- CATATAN ASSESSMENT -->
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Catatan Assessment</h6>

          <textarea
            v-model="catatanAssessment"
            class="form-control form-control-sm"
            rows="5"
            placeholder="Catatan tambahan assessment MTBM..."
          ></textarea>

          <div class="alert alert-info small mt-3 mb-0">
            Hasil hanya menampilkan kelompok pemeriksaan yang benar-benar diisi atau terpicu
            dari data Subjektif dan Objektif MTBM.
          </div>

          <div class="text-end mt-3">
            <button
              class="btn btn-outline-success btn-sm"
              :disabled="!pasien || !hasil || loadingSimpan"
              @click="simpanManual"
            >
              {{ loadingSimpan ? 'Menyimpan...' : 'Simpan Catatan / Hasil' }}
            </button>
          </div>
        </div>

        <!-- RULE -->
        <div class="border rounded-3 p-3">
          <h6 class="fw-bold mb-3">Keterangan Rule MTBM</h6>
          <ul class="small mb-0">
            <li>Infeksi tampil bila tanda atau pemeriksaan infeksi/penyakit berat diisi.</li>
            <li>Diare hanya tampil bila Subjektif mencatat bayi mengalami diare.</li>
            <li>Ikterus hanya tampil bila pemeriksaan kuning benar-benar diisi atau terpicu.</li>
            <li>Menyusu/BB hanya tampil bila BB/U atau pemeriksaan pemberian ASI/minum diisi.</li>
          </ul>
        </div>
      </div>

      <!-- KANAN -->
      <div class="col-md-6">
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Hasil Assessment (Otomatis berdasarkan subjektif dan objektif)</h6>

          <div v-if="hasil">
            <div v-if="kartuAssessment.length" class="row g-2 mb-3">
              <div
                v-for="item in kartuAssessment"
                :key="item.key"
                class="col-md-6"
              >
                <div class="p-2 border rounded-3 h-100">
                  <div class="small text-muted">{{ item.title }}</div>
                  <div class="fw-semibold">{{ item.label }}</div>
                </div>
              </div>
            </div>

            <div v-else class="text-muted fst-italic mb-3">
              Belum ada kelompok pemeriksaan yang diisi atau terpicu.
            </div>

            <div class="alert mb-3" :class="alertStatusClass">
              <div class="small text-muted">Status Kegawatan</div>
              <div class="fw-bold">
                {{ hasil.status_kegawatan || 'Belum dinilai' }}
              </div>
            </div>

            <div>
              <div class="fw-semibold mb-2">Klasifikasi Global</div>

              <ul v-if="hasil?.klasifikasi_global?.length" class="list-group">
                <li
                  v-for="(item, index) in hasil.klasifikasi_global"
                  :key="index"
                  class="list-group-item py-2"
                >
                  {{ item }}
                </li>
              </ul>

              <div v-else class="text-muted fst-italic">
                Belum ada klasifikasi global
              </div>
            </div>
          </div>

          <div v-else class="text-muted fst-italic">
            Belum ada hasil. Klik Generate Assessment.
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL CARI DIAGNOSA -->
    <div
      v-if="showModalDiagnosa"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.45);"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3">
          <div class="modal-header">
            <h6 class="modal-title fw-bold">Cari Diagnosa Medis</h6>
            <button type="button" class="btn-close" @click="tutupModalDiagnosa"></button>
          </div>

          <div class="modal-body">
            <input
              ref="inputCariDiagnosa"
              v-model="keywordDiagnosa"
              type="text"
              class="form-control form-control-sm mb-3"
              placeholder="Ketik kode / nama diagnosa..."
              @input="cariDiagnosa"
            />

            <div v-if="loadingCariDiagnosa" class="text-muted small">
              Mencari diagnosa...
            </div>

            <div v-else class="table-responsive">
              <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 120px;">Kode</th>
                    <th>Nama Diagnosa</th>
                    <th style="width: 120px;">Kategori</th>
                    <th style="width: 80px;">Pilih</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-if="listMasterDiagnosa.length === 0">
                    <td colspan="4" class="text-center text-muted">
                      Tidak ada diagnosa ditemukan
                    </td>
                  </tr>

                  <tr v-for="item in listMasterDiagnosa" :key="item.id">
                    <td>{{ item.kdDiag || '-' }}</td>
                    <td>
                      <div class="fw-semibold">{{ item.nmDiag || '-' }}</div>
                      <div v-if="item.klb == 1" class="small text-warning fw-semibold">
                        KLB
                      </div>
                    </td>
                    <td>{{ item.kategori_penyakit || '-' }}</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-success btn-sm"
                        @click="pilihDiagnosa(item)"
                      >
                        Pilih
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="text-muted small mt-2">
              Maksimal hasil ditampilkan 10 diagnosa.
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" @click="tutupModalDiagnosa">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  DataPasien: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const idPelayanan = computed(() => {
  const pasienAktif = props.DataPasien?.[0] || {}
  const candidates = [
    page.props?.idPelayanan,
    page.props?.idpelayanan,
    pasienAktif.idPelayanan,
    pasienAktif.idpelayanan,
    pasienAktif.kunjungan_id,
  ]

  const found = candidates.find((value) => (
    value !== undefined
    && value !== null
    && String(value).trim() !== ''
  ))

  return found ? String(found) : ''
})

const loading = ref(false)
const loadingSimpan = ref(false)
const loadingDiagnosa = ref(false)
const loadingCariDiagnosa = ref(false)

const hasil = ref(null)
const catatanAssessment = ref('')

const listDiagnosa = ref([])
const listMasterDiagnosa = ref([])
const keywordDiagnosa = ref('')
const showModalDiagnosa = ref(false)
const inputCariDiagnosa = ref(null)

let diagnosaTimer = null

const formDiagnosa = ref({
  diagnosaId: null,
  kodeDiagnosa: '',
  namaDiagnosa: '',
  keterangan: '',
  kasus: '',
})

const pasien = computed(() => props.DataPasien?.[0] ?? null)

const displayDiagnosa = computed(() => {
  if (!formDiagnosa.value.kodeDiagnosa && !formDiagnosa.value.namaDiagnosa) return ''
  return `${formDiagnosa.value.kodeDiagnosa} - ${formDiagnosa.value.namaDiagnosa}`
})

const punyaNilaiAssessment = (value) => (
  value !== null
  && value !== undefined
  && String(value).trim() !== ''
)

const kartuAssessment = computed(() => {
  const data = hasil.value || {}

  return [
    {
      key: 'infeksi',
      title: 'Infeksi / Penyakit Berat',
      value: data.infeksi,
      label: labelInfeksi(data.infeksi),
    },
    {
      key: 'ikterus',
      title: 'Ikterus',
      value: data.ikterus,
      label: labelIkterus(data.ikterus),
    },
    {
      key: 'diare',
      title: 'Diare',
      value: data.diare,
      label: labelDiare(data.diare),
    },
    {
      key: 'hiv',
      title: 'HIV Bayi Muda',
      value: data.hiv,
      label: labelHiv(data.hiv),
    },
    {
      key: 'menyusu_bb',
      title: 'Menyusu / Berat Badan',
      value: data.menyusu_bb,
      label: labelMenyusuBb(data.menyusu_bb),
    },
  ].filter((item) => punyaNilaiAssessment(item.value))
})

const alertStatusClass = computed(() => {
  const status = hasil.value?.status_kegawatan

  if (status === 'Perlu rujukan segera' || status === 'Penyakit sangat berat') {
    return 'alert-danger'
  }

  if (status === 'Perlu tatalaksana / observasi') return 'alert-warning'
  if (status === 'Tidak gawat') return 'alert-success'
  return 'alert-light'
})

const labelKasus = (v) => {
  if (v === 'baru') return 'Baru'
  if (v === 'lama') return 'Lama'
  return '-'
}

const labelInfeksi = (v) => {
  if (v === 'penyakit_sangat_berat_infeksi_berat') return 'Penyakit sangat berat / Infeksi bakteri berat (Merah)'
  if (v === 'infeksi_bakteri_lokal') return 'Infeksi bakteri lokal (Kuning)'
  if (v === 'mungkin_bukan_infeksi' || v === 'tidak_ada_infeksi') return 'Mungkin bukan infeksi (Hijau)'

  if (v === 'merah_sangat_berat') return 'Penyakit sangat berat / Infeksi bakteri berat (Merah)'
  if (v === 'kuning_infeksi') return 'Infeksi bakteri lokal (Kuning)'
  if (v === 'hijau_tidak_infeksi') return 'Mungkin bukan infeksi (Hijau)'
  return '-'
}

const labelIkterus = (v) => {
  if (v === 'ikterus_berat' || v === 'merah_ikterus_berat') return 'Ikterus berat (Merah)'
  if (v === 'ikterus' || v === 'kuning_ikterus') return 'Ikterus (Kuning)'
  if (v === 'tidak_ikterus' || v === 'hijau_tidak_ikterus') return 'Tidak ikterus (Hijau)'
  return '-'
}

const labelDiare = (v) => {
  if (v === 'diare_dehidrasi_berat' || v === 'merah_dehidrasi_berat') {
    return 'Diare dengan dehidrasi berat (Merah)'
  }

  if (v === 'diare_dehidrasi_ringan_sedang' || v === 'kuning_dehidrasi_ringan') {
    return 'Diare dengan dehidrasi ringan/sedang (Kuning)'
  }

  if (v === 'diare_tanpa_dehidrasi' || v === 'hijau_tanpa_dehidrasi') {
    return 'Diare tanpa dehidrasi (Hijau)'
  }

  if (v === 'tidak_diare') return 'Tidak diare (Hijau)'
  return '-'
}

const labelHiv = (v) => {
  if (v === 'infeksi_hiv_terkonfirmasi') return 'Infeksi HIV terkonfirmasi (Merah)'
  if (v === 'terpajan_hiv_mungkin_infeksi') return 'Terpajan HIV / mungkin infeksi HIV (Kuning)'
  if (v === 'infeksi_hiv_tidak_diketahui') return 'Infeksi HIV tidak diketahui (Kuning)'
  if (v === 'bukan_infeksi_hiv') return 'Bukan infeksi HIV (Hijau)'
  return '-'
}

const labelMenyusuBb = (v) => {
  if ([
    'masalah_menyusu_berat_bb_sangat_rendah',
    'bb_sangat_rendah_menurut_umur',
    'merah_tidak_bisa_menyusu',
  ].includes(v)) {
    return 'Berat badan sangat rendah menurut umur (Merah)'
  }

  if ([
    'masalah_menyusu_bb_rendah',
    'bb_rendah_masalah_pemberian_asi',
    'kuning_masalah_menyusu',
  ].includes(v)) {
    return 'BB rendah dan/atau masalah pemberian ASI (Kuning)'
  }

  if (v === 'bb_rendah_masalah_pemberian_minum') {
    return 'BB rendah dan/atau masalah pemberian minum (Kuning)'
  }

  if ([
    'menyusu_baik',
    'bb_tidak_rendah_tidak_ada_masalah_asi',
    'hijau_menyusu_baik',
  ].includes(v)) {
    return 'BB tidak rendah dan tidak ada masalah pemberian ASI (Hijau)'
  }

  if (v === 'bb_tidak_rendah_tidak_ada_masalah_minum') {
    return 'BB tidak rendah dan tidak ada masalah pemberian minum (Hijau)'
  }

  return '-'
}

const bukaModalDiagnosa = async () => {
  showModalDiagnosa.value = true
  keywordDiagnosa.value = ''
  listMasterDiagnosa.value = []

  await nextTick()
  inputCariDiagnosa.value?.focus()

  await loadDiagnosaAwal()
}

const tutupModalDiagnosa = () => {
  showModalDiagnosa.value = false
  keywordDiagnosa.value = ''
  listMasterDiagnosa.value = []
}

const loadDiagnosaAwal = async () => {
  try {
    loadingCariDiagnosa.value = true

    const res = await axios.get('/simpus/kia/mtbm/master-diagnosa', {
      params: {
        q: '',
        limit: 10,
      },
    })

    listMasterDiagnosa.value = res.data?.data || []
  } catch (error) {
    console.error('LOAD DIAGNOSA AWAL MTBM ERROR:', error.response?.data || error)
    listMasterDiagnosa.value = []
  } finally {
    loadingCariDiagnosa.value = false
  }
}

const cariDiagnosa = () => {
  clearTimeout(diagnosaTimer)

  diagnosaTimer = setTimeout(async () => {
    try {
      loadingCariDiagnosa.value = true

      const res = await axios.get('/simpus/kia/mtbm/master-diagnosa', {
        params: {
          q: keywordDiagnosa.value || '',
          limit: 10,
        },
      })

      listMasterDiagnosa.value = res.data?.data || []
    } catch (error) {
      console.error('CARI DIAGNOSA MTBM ERROR:', error.response?.data || error)
      listMasterDiagnosa.value = []
    } finally {
      loadingCariDiagnosa.value = false
    }
  }, 300)
}

const pilihDiagnosa = (item) => {
  formDiagnosa.value = {
    diagnosaId: item.id ?? null,
    kodeDiagnosa: item.kdDiag ?? '',
    namaDiagnosa: item.nmDiag ?? '',
    keterangan: formDiagnosa.value.keterangan ?? '',
    kasus: formDiagnosa.value.kasus ?? '',
  }

  tutupModalDiagnosa()
}

const resetFormDiagnosa = () => {
  formDiagnosa.value = {
    diagnosaId: null,
    kodeDiagnosa: '',
    namaDiagnosa: '',
    keterangan: '',
    kasus: '',
  }

  keywordDiagnosa.value = ''
  listMasterDiagnosa.value = []
}

const loadAssessmentFromDb = async () => {
  if (!idPelayanan.value) return

  try {
    const res = await axios.get(`/simpus/kia/mtbm/assessment/${idPelayanan.value}`)
    hasil.value = res.data?.data || null
    catatanAssessment.value = hasil.value?.catatan_assessment || ''
  } catch (error) {
    console.error('LOAD ASSESSMENT MTBM ERROR:', error.response?.data || error)
    hasil.value = null
  }
}

const loadDiagnosaMedisFromDb = async () => {
  if (!idPelayanan.value) return

  try {
    const res = await axios.get(`/simpus/kia/mtbm/diagnosa-medis/${idPelayanan.value}`)
    listDiagnosa.value = res.data?.data || []
  } catch (error) {
    console.error('LOAD DIAGNOSA MEDIS MTBM ERROR:', error.response?.data || error)
    listDiagnosa.value = []
  }
}

const simpanDiagnosaMedis = async () => {
  if (!pasien.value || !idPelayanan.value) return

  if (!formDiagnosa.value.diagnosaId) {
    alert('Pilih diagnosa dari master dulu')
    return
  }

  if (!formDiagnosa.value.kasus) {
    alert('Kasus wajib dipilih')
    return
  }

  try {
    loadingDiagnosa.value = true

    await axios.post('/simpus/kia/mtbm/diagnosa-medis', {
      kunjungan_id: idPelayanan.value,
      pasien_id: pasien.value?.ID ?? null,
      diagnosa_id: formDiagnosa.value.diagnosaId,
      kodeDiagnosa: formDiagnosa.value.kodeDiagnosa,
      namaDiagnosa: formDiagnosa.value.namaDiagnosa,
      keterangan: formDiagnosa.value.keterangan,
      kasus: formDiagnosa.value.kasus,
      poli: pasien.value?.nmPoli ?? null,
    })

    resetFormDiagnosa()
    await loadDiagnosaMedisFromDb()

    alert('Diagnosa medis berhasil disimpan')
  } catch (error) {
    console.error('SIMPAN DIAGNOSA MEDIS MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      const msg = error.response?.data?.message || 'Validasi gagal'
      const errors = error.response?.data?.errors
      alert(msg + '\n' + (errors ? JSON.stringify(errors, null, 2) : ''))
      return
    }

    alert('Gagal menyimpan diagnosa medis')
  } finally {
    loadingDiagnosa.value = false
  }
}

const hapusDiagnosaMedis = async (id) => {
  if (!id) return
  if (!confirm('Hapus diagnosa medis ini?')) return

  try {
    loadingDiagnosa.value = true

    await axios.delete(`/simpus/kia/mtbm/diagnosa-medis/${id}`)
    await loadDiagnosaMedisFromDb()

    alert('Diagnosa medis berhasil dihapus')
  } catch (error) {
    console.error('HAPUS DIAGNOSA MEDIS MTBM ERROR:', error.response?.data || error)
    alert('Gagal menghapus diagnosa medis')
  } finally {
    loadingDiagnosa.value = false
  }
}

const generateAssessment = async () => {
  if (!pasien.value || !idPelayanan.value) return

  try {
    loading.value = true

    const res = await axios.post('/simpus/kia/mtbm/assessment/auto', {
      kunjungan_id: idPelayanan.value,
    })

    hasil.value = res.data?.data || null
    catatanAssessment.value = hasil.value?.catatan_assessment || ''
    alert('Assessment MTBM berhasil digenerate')
  } catch (error) {
    console.error('AUTO ASSESSMENT MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      const msg = error.response?.data?.message || 'Validasi gagal'
      const errors = error.response?.data?.errors
      alert(msg + '\n' + (errors ? JSON.stringify(errors, null, 2) : ''))
      return
    }

    alert('Gagal generate Assessment MTBM')
  } finally {
    loading.value = false
  }
}

const simpanManual = async () => {
  if (!pasien.value || !hasil.value || !idPelayanan.value) return

  try {
    loadingSimpan.value = true

    await axios.post('/simpus/kia/mtbm/assessment/store', {
      kunjungan_id: idPelayanan.value,
      pasien_id: pasien.value?.ID ?? null,
      assessment_mtbm: {
        infeksi: hasil.value?.infeksi ?? null,
        ikterus: hasil.value?.ikterus ?? null,
        diare: hasil.value?.diare ?? null,
        hiv: hasil.value?.hiv ?? null,
        menyusu_bb: hasil.value?.menyusu_bb ?? null,
      },
      klasifikasi: hasil.value?.klasifikasi_global ?? [],
      status_kegawatan: hasil.value?.status_kegawatan || 'Belum dinilai',
      catatan_assessment: catatanAssessment.value,
    })

    await loadAssessmentFromDb()
    alert('Assessment MTBM berhasil disimpan')
  } catch (error) {
    console.error('SIMPAN ASSESSMENT MTBM ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      const msg = error.response?.data?.message || 'Validasi gagal'
      const errors = error.response?.data?.errors
      alert(msg + '\n' + (errors ? JSON.stringify(errors, null, 2) : ''))
      return
    }

    alert('Gagal menyimpan assessment MTBM')
  } finally {
    loadingSimpan.value = false
  }
}

onMounted(() => {
  loadAssessmentFromDb()
  loadDiagnosaMedisFromDb()
})
</script>

<style scoped>
.table th {
  font-size: 13px;
  white-space: nowrap;
}

.table td {
  font-size: 13px;
}
</style>
