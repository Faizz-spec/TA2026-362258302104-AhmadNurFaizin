<template>
  <div class="bg-white p-3 rounded-3 shadow-sm">
    <h6 class="fw-bold text-success mb-4">
      P – PLANNINGeee (RENCANA TINDAKAN MTBS)eeegg
    </h6>

    <!-- REKOMENDASI MTBS -->
    <div class="mb-4">
      <div class="alert alert-info py-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <div class="fw-bold">Rekomendasi Berdasarkan Klasifikasi MTBS</div>
            <small class="text-muted">
              Rekomendasi hanya bantuan. Input final tetap oleh dokter/petugas.
            </small>
          </div>

          <button
            type="button"
            class="btn btn-sm btn-outline-primary"
            @click="loadRekomendasiPlanning"
          >
            Refresh
          </button>
        </div>

        <div v-if="loadingRekomendasi" class="text-muted">
          Memuat rekomendasi...
        </div>

        <div v-else-if="rekomendasiPlanning.length === 0" class="text-muted">
          Belum ada rekomendasi. Pastikan assessment MTBS sudah digenerate.
        </div>

        <div v-else>
          <div
            v-for="r in rekomendasiPlanning"
            :key="r.klasifikasi"
            class="bg-white rounded-3 border p-2 mb-2"
          >
            <div class="fw-semibold text-success mb-1">
              {{ r.klasifikasi }}
            </div>

            <ul class="mb-0 ps-3">
              <li v-for="item in r.items" :key="item" class="mb-1">
                <span>{{ item }}</span>

                <button
                  v-if="bisaCariObat(item)"
                  type="button"
                  class="btn btn-sm btn-outline-success ms-2 py-0"
                  @click="pakaiRekomendasiObat(item)"
                >
                  Cari obat ini
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- A. TINDAKAN SEGERA -->
    <div class="mb-4">
      <h6 class="fw-semibold">A. Tindakan Segera</h6>

      <div class="row">
        <div v-for="item in tindakanSegeraList" :key="item" class="col-md-6 mb-2">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              :value="item"
              v-model="form.tindakanSegera"
            />
            <label class="form-check-label">{{ item }}</label>
          </div>
        </div>
      </div>
    </div>

    <!-- B. PENGOBATAN -->
    <div class="mb-4">
      <h6 class="fw-semibold">B. Pengobatan</h6>

      <div class="row mb-2">
        <div class="col-md-4 position-relative">
          <label class="form-label">Nama Obat</label>

          <input
            class="form-control"
            v-model="searchObat"
            placeholder="Cari dan pilih obat..."
            autocomplete="off"
            @input="loadObat"
            @focus="bukaDropdownObat"
          />

          <div
            v-if="showDropdownObat && daftarObat.length > 0"
            class="list-group position-absolute w-100 shadow-sm"
            style="z-index: 9999; max-height: 250px; overflow-y: auto;"
          >
            <button
              v-for="o in daftarObat"
              :key="o.obat_id"
              type="button"
              class="list-group-item list-group-item-action text-start"
              @click="pilihObat(o)"
            >
              <div class="fw-semibold">
                {{ o.nama }}
              </div>
              <small class="text-muted">
                {{ o.satuan ? o.satuan : '' }}
              </small>
            </button>
          </div>

          <small v-if="searchObat.length > 0 && searchObat.length < 2" class="text-muted">
            Ketik minimal 2 huruf
          </small>

          <small
            v-else-if="showDropdownObat && searchObat.length >= 2 && daftarObat.length === 0"
            class="text-danger"
          >
            Obat tidak ditemukan
          </small>

          <small v-else-if="obat.obat_id" class="text-success">
            Obat dipilih: {{ obat.nama }}
          </small>
        </div>

        <div class="col-md-2">
          <label class="form-label">Dosis</label>
          <input
            class="form-control"
            v-model="obat.dosis"
            placeholder="Otomatis / bisa edit"
          />
        </div>

        <div class="col-md-3">
          <label class="form-label">Cara Pemberian</label>
          <select class="form-select" v-model="obat.cara">
            <option value="">-- Pilih --</option>
            <option value="oral">Oral</option>
            <option value="suntik">Suntik</option>
            <option value="infus">Infus</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Lama (hari)</label>
          <input
            type="number"
            class="form-control"
            v-model.number="obat.lama"
            min="0"
            step="1"
          />
        </div>
      </div>

      <button class="btn btn-sm btn-outline-success mb-3" type="button" @click="tambahObat">
        Tambah Obat
      </button>

      <ul class="list-group">
        <li
          v-for="(o, i) in form.pengobatan"
          :key="i"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <div>
            <b>{{ o.nama }}</b>
            <span v-if="o.satuan">({{ o.satuan }})</span>
            – {{ o.dosis || '-' }} – {{ o.cara || '-' }} – {{ o.lama }} hari
          </div>

          <button class="btn btn-sm btn-danger" type="button" @click="hapusObat(i)">
            Hapus
          </button>
        </li>

        <li v-if="form.pengobatan.length === 0" class="list-group-item text-muted">
          Belum ada obat ditambahkan
        </li>
      </ul>
    </div>

    <!-- C. EDUKASI IBU -->
    <div class="mb-4">
      <h6 class="fw-semibold">C. Edukasi Ibu</h6>

      <div class="form-check mb-2" v-for="e in edukasiList" :key="e">
        <input class="form-check-input" type="checkbox" :value="e" v-model="form.edukasi" />
        <label class="form-check-label">{{ e }}</label>
      </div>

      <textarea
        class="form-control mt-2"
        rows="2"
        placeholder="Catatan edukasi tambahan"
        v-model="form.catatanEdukasi"
      ></textarea>
    </div>

    <!-- D. RENCANA KUNJUNGAN ULANG -->
    <div class="mb-4">
      <h6 class="fw-semibold">D. Rencana Kunjungan Ulang</h6>

      <div class="d-flex gap-4 flex-wrap">
        <div class="form-check" v-for="hari in [2, 3, 5, 7, 14]" :key="hari">
          <input
            class="form-check-input"
            type="radio"
            :value="hari"
            v-model.number="form.kunjunganUlang"
          />
          <label class="form-check-label">{{ hari }} hari</label>
        </div>
      </div>
    </div>

    <!-- SIMPAN -->
    <div class="text-end">
      <button class="btn btn-success btn-sm" :disabled="loading" @click="simpanPlanning">
        {{ loading ? 'Menyimpan...' : 'Simpan Planning MTBS' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const idPelayanan = page.props.idPelayanan // kunjungan_id

const loading = ref(false)
const loadingRekomendasi = ref(false)

const rekomendasiPlanning = ref([])
const daftarObat = ref([])
const searchObat = ref('')
const showDropdownObat = ref(false)

const tindakanSegeraList = [
  'Rujuk segera',
  'Oksigen',
  'Cairan infus',
  'Antibiotik suntik',
  'Diazepam (jika kejang)',
  'Cegah gula darah tidak turun',
  'Jaga tubuh anak tetap hangat',
]

const edukasiList = [
  'Cara minum obat',
  'Pemberian makan / ASI',
  'Tanda bahaya',
  'Kapan harus kembali segera',
  'Pemberian cairan lebih banyak',
  'Lanjutkan makan selama sakit',
]

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

const loadRekomendasiPlanning = async () => {
  if (!idPelayanan) return

  try {
    loadingRekomendasi.value = true

    const res = await axios.get(
      `/simpus/kia/mtbs/planning/rekomendasi/${idPelayanan}`
    )

    rekomendasiPlanning.value = res.data?.data ?? []
  } catch (error) {
    console.error('LOAD REKOMENDASI PLANNING ERROR:', error.response?.data || error)
    rekomendasiPlanning.value = []
  } finally {
    loadingRekomendasi.value = false
  }
}

const bisaCariObat = (item) => {
  const text = String(item).toLowerCase()

  const keywords = [
    'amoksisilin',
    'amoxicillin',
    'oralit',
    'zinc',
    'parasetamol',
    'paracetamol',
    'antibiotik',
    'vitamin a',
    'zat besi',
    'fe',
    'antimalaria',
    'kotrimoksazol',
    'diazepam',
    'salbutamol',
  ]

  return keywords.some((k) => text.includes(k))
}

const ambilKeywordObat = (item) => {
  const text = String(item).toLowerCase()

  if (text.includes('amoksisilin') || text.includes('amoxicillin')) return 'amoksisilin'
  if (text.includes('oralit')) return 'oralit'
  if (text.includes('zinc')) return 'zinc'
  if (text.includes('parasetamol') || text.includes('paracetamol')) return 'parasetamol'
  if (text.includes('vitamin a')) return 'vitamin a'
  if (text.includes('zat besi') || text.includes('fe')) return 'zat besi'
  if (text.includes('kotrimoksazol')) return 'kotrimoksazol'
  if (text.includes('diazepam')) return 'diazepam'
  if (text.includes('salbutamol')) return 'salbutamol'
  if (text.includes('antimalaria')) return 'antimalaria'
  if (text.includes('antibiotik')) return 'antibiotik'

  return item
}

const pakaiRekomendasiObat = async (item) => {
  searchObat.value = ambilKeywordObat(item)
  await loadObat()
}

const bukaDropdownObat = () => {
  if (searchObat.value.length >= 2 && daftarObat.value.length > 0) {
    showDropdownObat.value = true
  }
}

const generateDosisDefault = (selected) => {
  const nama = String(selected.nama ?? '').toLowerCase()
  const satuan = String(selected.satuan ?? '').toUpperCase()

  const matchMgPerMl = nama.match(/(\d+(?:[.,]\d+)?)\s*mg\s*\/\s*(\d+(?:[.,]\d+)?)\s*m?l/i)
  const matchMg = nama.match(/(\d+(?:[.,]\d+)?)\s*mg/i)
  const matchGram = nama.match(/(\d+(?:[.,]\d+)?)\s*g(?:ram)?/i)
  const matchMcg = nama.match(/(\d+(?:[.,]\d+)?)\s*(mcg|µg)/i)
  const matchMl = nama.match(/(\d+(?:[.,]\d+)?)\s*m?l/i)
  const matchPersen = nama.match(/(\d+(?:[.,]\d+)?)\s*%/i)

  if (matchMgPerMl) {
    return `${matchMgPerMl[1].replace(',', '.')} mg/${matchMgPerMl[2].replace(',', '.')} ml`
  }

  if (matchMg) {
    return `${matchMg[1].replace(',', '.')} mg`
  }

  if (matchGram) {
    return `${matchGram[1].replace(',', '.')} g`
  }

  if (matchMcg) {
    return `${matchMcg[1].replace(',', '.')} mcg`
  }

  if (matchMl) {
    return `${matchMl[1].replace(',', '.')} ml`
  }

  if (matchPersen) {
    return `${matchPersen[1].replace(',', '.')}%`
  }

  if (satuan) {
    return `1 ${satuan}`
  }

  return ''
}

const loadObat = async () => {
  obat.obat_id = ''
  obat.kode_obat = ''
  obat.nama = ''
  obat.satuan = ''
  obat.dosis = ''

  if (searchObat.value.length < 2) {
    daftarObat.value = []
    showDropdownObat.value = false
    return
  }

  try {
    const res = await axios.get('/simpus/kia/mtbs/obat', {
      params: {
        q: searchObat.value,
      },
    })

    daftarObat.value = res.data?.data ?? []
    showDropdownObat.value = true
  } catch (error) {
    console.error('LOAD OBAT ERROR:', error.response?.data || error)
  }
}

const pilihObat = (selected) => {
  obat.obat_id = selected.obat_id
  obat.kode_obat = selected.kode_obat ?? ''
  obat.nama = selected.nama ?? ''
  obat.satuan = selected.satuan ?? ''

  obat.dosis = generateDosisDefault(selected)

  searchObat.value = selected.nama ?? ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const tambahObat = () => {
  if (!obat.obat_id || !obat.nama) {
    alert('Pilih obat dulu dari daftar pencarian')
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

  obat.obat_id = ''
  obat.kode_obat = ''
  obat.nama = ''
  obat.satuan = ''
  obat.dosis = ''
  obat.cara = ''
  obat.lama = null

  searchObat.value = ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const hapusObat = (i) => {
  form.pengobatan.splice(i, 1)
}

const applyData = (data) => {
  if (!data) return

  form.tindakanSegera = Array.isArray(data.tindakanSegera) ? data.tindakanSegera : []
  form.pengobatan = Array.isArray(data.pengobatan) ? data.pengobatan : []
  form.edukasi = Array.isArray(data.edukasi) ? data.edukasi : []
  form.catatanEdukasi = data.catatanEdukasi ?? ''
  form.kunjunganUlang = data.kunjunganUlang ?? null
}

const loadFromDb = async () => {
  if (!idPelayanan) return

  try {
    const res = await axios.get(`/simpus/kia/mtbs/planning/${idPelayanan}`)
    applyData(res.data?.data)
  } catch (error) {
    console.error('LOAD PLANNING ERROR:', error.response?.data || error)
  }
}

onMounted(async () => {
  await loadRekomendasiPlanning()
  await loadFromDb()
})

const simpanPlanning = async () => {
  try {
    loading.value = true

    const payload = {
      kunjungan_id: String(idPelayanan),
      tindakanSegera: form.tindakanSegera,
      pengobatan: form.pengobatan,
      edukasi: form.edukasi,
      catatanEdukasi: form.catatanEdukasi,
      kunjunganUlang: form.kunjunganUlang,
    }

    await axios.post('/simpus/kia/mtbs/planning/store', payload)

    await loadFromDb()

    alert('Planning MTBS berhasil disimpan')
  } catch (error) {
    console.error('SAVE PLANNING ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      alert('Validasi gagal:\n' + JSON.stringify(error.response?.data?.errors, null, 2))
      return
    }

    alert('Gagal menyimpan Planning MTBS')
  } finally {
    loading.value = false
  }
}
</script>