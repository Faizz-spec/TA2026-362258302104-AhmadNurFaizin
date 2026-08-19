<template>
  <div class="bg-white p-3 rounded-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="fw-bold text-success mb-1">Assessment MTBS</h5>
        <div v-if="pasien" class="text-muted small">
          {{ pasien.NAMA_LGKP }} | No. RM: {{ pasien.NO_MR || '-' }} | Poli:
          {{ pasien.nmPoli || '-' }}
        </div>
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
        <!-- FORM ALERGI -->
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Riwayat Alergi</h6>

          <div class="mb-2">
            <label class="form-label small fw-semibold">Alergi Makanan</label>
            <input
              v-model="formAlergi.alergiMakanan"
              type="text"
              class="form-control form-control-sm"
              placeholder="Contoh: Susu sapi"
            />
          </div>

          <div class="mb-2">
            <label class="form-label small fw-semibold">Alergi Obat</label>
            <input
              v-model="formAlergi.alergiObat"
              type="text"
              class="form-control form-control-sm"
              placeholder="Contoh: Tidak ada alergi"
            />
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Keterangan Alergi</label>
            <input
              v-model="formAlergi.keteranganAlergi"
              type="text"
              class="form-control form-control-sm"
              placeholder="Keterangan"
            />
          </div>

          <div class="text-end">
            <button
              class="btn btn-success btn-sm"
              :disabled="!pasien || loadingAlergi"
              @click="simpanAlergi"
            >
              {{ loadingAlergi ? 'Menyimpan...' : 'Simpan Alergi' }}
            </button>
          </div>
        </div>

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
        <div class="border rounded-3 p-3">
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
      </div>

      <!-- KANAN -->
      <div class="col-md-6">
        <!-- HASIL ASSESSMENT -->
        <div class="border rounded-3 p-3 mb-3">
          <h6 class="fw-bold mb-3">Hasil Assessment Otomatis</h6>

          <div v-if="hasil">
            <div class="alert mb-3" :class="alertStatusClass">
              <div class="small text-muted">Status Kegawatan</div>
              <div class="fw-bold">
                {{ hasil.status_kegawatan || '-' }}
              </div>
            </div>

            <div>
              <div class="fw-semibold mb-2">Klasifikasi </div>

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

        <!-- RIWAYAT ALERGI SAJA -->
        <div class="border rounded-3 p-3">
          <h6 class="fw-bold mb-3">Riwayat Alergi Sebelumnya</h6>

          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 45px;">No</th>
                  <th>Alergi Makanan</th>
                  <th>Alergi Obat</th>
                  <th>Keterangan</th>
                  <th style="width: 110px;">Tanggal</th>
                </tr>
              </thead>

              <tbody>
                <tr v-if="listAlergi.length === 0">
                  <td colspan="5" class="text-center text-muted">
                    Belum ada riwayat alergi
                  </td>
                </tr>

                <tr v-for="(item, index) in listAlergi" :key="item.id || index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.alergiMakanan || '-' }}</td>
                  <td>{{ item.alergiObat || '-' }}</td>
                  <td>{{ item.keteranganAlergi || '-' }}</td>
                  <td>{{ item.tanggal || '-' }}</td>
                </tr>
              </tbody>
            </table>
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
const idPelayanan = page.props.idPelayanan

const loading = ref(false)
const loadingAlergi = ref(false)
const loadingDiagnosa = ref(false)
const loadingCariDiagnosa = ref(false)

const hasil = ref(null)
const listAlergi = ref([])
const listDiagnosa = ref([])
const listMasterDiagnosa = ref([])
const keywordDiagnosa = ref('')
const showModalDiagnosa = ref(false)
const inputCariDiagnosa = ref(null)

let diagnosaTimer = null

const formAlergi = ref({
  alergiMakanan: '',
  alergiObat: '',
  keteranganAlergi: '',
})

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

const alertStatusClass = computed(() => {
  if (hasil.value?.status_kegawatan === 'Tidak gawat') return 'alert-success'
  if (hasil.value?.status_kegawatan === 'Perlu rujukan segera') return 'alert-warning'
  if (hasil.value?.status_kegawatan === 'Penyakit sangat berat') return 'alert-danger'
  return 'alert-light'
})

const labelKasus = (v) => {
  if (v === 'baru') return 'Baru'
  if (v === 'lama') return 'Lama'
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

    const res = await axios.get('/simpus/kia/mtbs/master-diagnosa', {
      params: {
        q: '',
        limit: 10,
      },
    })

    listMasterDiagnosa.value = res.data?.data || []
  } catch (error) {
    console.error('LOAD DIAGNOSA AWAL ERROR:', error.response?.data || error)
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

      const res = await axios.get('/simpus/kia/mtbs/master-diagnosa', {
        params: {
          q: keywordDiagnosa.value || '',
          limit: 10,
        },
      })

      listMasterDiagnosa.value = res.data?.data || []
    } catch (error) {
      console.error('CARI DIAGNOSA ERROR:', error.response?.data || error)
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
  if (!idPelayanan) return

  try {
    const res = await axios.get(`/simpus/kia/mtbs/assessment/${idPelayanan}`)
    hasil.value = res.data?.data || null
  } catch (error) {
    console.error('LOAD ASSESSMENT ERROR:', error.response?.data || error)
    hasil.value = null
  }
}

const loadAlergiFromDb = async () => {
  if (!idPelayanan) return

  try {
    const res = await axios.get(`/simpus/kia/mtbs/alergi/${idPelayanan}`)
    const data = res.data?.data

    formAlergi.value = {
      alergiMakanan: data?.current?.alergiMakanan ?? '',
      alergiObat: data?.current?.alergiObat ?? '',
      keteranganAlergi: data?.current?.keteranganAlergi ?? '',
    }

    listAlergi.value = data?.riwayat || []
  } catch (error) {
    console.error('LOAD ALERGI ERROR:', error.response?.data || error)
    listAlergi.value = []
  }
}

const simpanAlergi = async () => {
  if (!pasien.value) return

  try {
    loadingAlergi.value = true

    await axios.post('/simpus/kia/mtbs/alergi', {
      kunjungan_id: String(idPelayanan),
      pasien_id: pasien.value?.ID ?? null,
      alergiMakanan: formAlergi.value.alergiMakanan,
      alergiObat: formAlergi.value.alergiObat,
      keteranganAlergi: formAlergi.value.keteranganAlergi,
    })

    await loadAlergiFromDb()

    alert('Alergi berhasil disimpan')
  } catch (error) {
    console.error('SIMPAN ALERGI ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      const msg = error.response?.data?.message || 'Validasi gagal'
      const errors = error.response?.data?.errors
      alert(msg + '\n' + (errors ? JSON.stringify(errors, null, 2) : ''))
      return
    }

    alert('Gagal menyimpan alergi')
  } finally {
    loadingAlergi.value = false
  }
}

const loadDiagnosaMedisFromDb = async () => {
  if (!idPelayanan) return

  try {
    const res = await axios.get(`/simpus/kia/mtbs/diagnosa-medis/${idPelayanan}`)
    listDiagnosa.value = res.data?.data || []
  } catch (error) {
    console.error('LOAD DIAGNOSA MEDIS ERROR:', error.response?.data || error)
    listDiagnosa.value = []
  }
}

const simpanDiagnosaMedis = async () => {
  if (!pasien.value) return

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

    await axios.post('/simpus/kia/mtbs/diagnosa-medis', {
      kunjungan_id: String(idPelayanan),
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
    console.error('SIMPAN DIAGNOSA MEDIS ERROR:', error.response?.data || error)

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

    await axios.delete(`/simpus/kia/mtbs/diagnosa-medis/${id}`)
    await loadDiagnosaMedisFromDb()

    alert('Diagnosa medis berhasil dihapus')
  } catch (error) {
    console.error('HAPUS DIAGNOSA MEDIS ERROR:', error.response?.data || error)
    alert('Gagal menghapus diagnosa medis')
  } finally {
    loadingDiagnosa.value = false
  }
}

const generateAssessment = async () => {
  if (!pasien.value) return

  try {
    loading.value = true

    const res = await axios.post('/simpus/kia/mtbs/assessment/auto', {
      kunjungan_id: String(idPelayanan),
    })

    hasil.value = res.data?.data || null
    alert('Assessment MTBS berhasil digenerate')
  } catch (error) {
    console.error('AUTO ASSESSMENT ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      const msg = error.response?.data?.message || 'Validasi gagal'
      const errors = error.response?.data?.errors
      alert(msg + '\n' + (errors ? JSON.stringify(errors, null, 2) : ''))
      return
    }

    alert('Gagal generate Assessment MTBS')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAssessmentFromDb()
  loadAlergiFromDb()
  loadDiagnosaMedisFromDb()
})
</script>
<style scoped>
.patient-card {
  background: #f8faf9;
  border: 1px solid #e6f1ea;
  border-radius: 12px;
  padding: 12px;
}

.avatar-icon {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #e8f5ee;
  color: #198754;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.section-mini {
  background: #fcfcfc;
  border-radius: 10px;
}

.section-title {
  font-size: 13px;
  font-weight: 700;
  color: #198754;
  margin-bottom: 8px;
}

.empty-box {
  background: #f8f9fa;
  border: 1px dashed #d6d6d6;
  color: #6c757d;
  border-radius: 10px;
  padding: 14px;
  text-align: center;
  font-size: 13px;
}

.assessment-card {
  border: 1px solid #e3e3e3;
  background: #fff;
  border-radius: 10px;
  padding: 10px;
  height: 100%;
}

.assessment-card:hover {
  background: #f8faf9;
}

.table th {
  font-size: 13px;
  white-space: nowrap;
}

.table td {
  font-size: 13px;
}
</style>