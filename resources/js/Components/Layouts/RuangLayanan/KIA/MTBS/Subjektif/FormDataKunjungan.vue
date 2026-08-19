<template>
  <div class="mtbs-card">
    <div class="header-box">
      <div>
        <h6 class="title">S – SUBJEKTIF (MTBS)</h6>
        <div class="subtitle">Anamnesis balita sakit umur 2 bulan sampai kurang dari 5 tahun</div>
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="loading || saving" @click="loadFromDb">
          {{ loading ? 'Memuat...' : 'Muat Ulang' }}
        </button>
        <button type="button" class="btn btn-success btn-sm px-3" :disabled="loading || saving" @click="simpan">
          {{ saving ? 'Menyimpan...' : 'Simpan Subjektif' }}
        </button>
      </div>
    </div>

    <div v-if="message" class="alert alert-info py-2">{{ message }}</div>

    <div class="row g-3">
      <div class="col-xl-4">
        <section class="section-box mb-3">
          <div class="section-title">A. Identitas Kunjungan</div>
          <div class="auto-visit-box">
            <span class="badge" :class="form.jenisKunjungan === 'ulang' ? 'bg-warning text-dark' : 'bg-success'">
              {{ form.jenisKunjungan === 'ulang' ? 'Kunjungan Ulang' : 'Kunjungan Pertama' }}
            </span>
            <small class="text-muted d-block mt-1">Otomatis dari riwayat loket pasien.</small>
          </div>
        </section>

        <section class="section-box mb-3">
          <div class="section-title">B. Tanda Bahaya Umum (Anamnesis Wajib)</div>
          <YesNo v-model="form.tandaBahaya.bisaMinumMenyusu" label="Apakah anak bisa minum atau menyusu?" />
          <div class="mt-2">
            <YesNo v-model="form.tandaBahaya.memuntahkanSemua" label="Apakah anak memuntahkan semua makanan dan minuman?" />
          </div>
          <div class="mt-2">
            <YesNo v-model="form.tandaBahaya.pernahKejang" label="Apakah anak pernah kejang selama sakit ini?" />
          </div>
          <small class="text-muted d-block mt-2">Ketiga pertanyaan wajib dijawab. Pemeriksaan SAGA tetap dilakukan di Objektif.</small>
        </section>

        <section class="section-box mb-3">
          <div class="section-title">C. Masalah Anak / Keluhan Utama</div>
          <div class="keluhan-grid">
            <label v-for="item in daftarKeluhan" :key="item" class="option-item">
              <input v-model="form.keluhanUtama" type="checkbox" :value="item" />
              <span>{{ item }}</span>
            </label>
          </div>

          <textarea
            v-model="form.keluhanLain"
            class="form-control form-control-sm mt-2"
            rows="3"
            placeholder="Masalah atau keluhan lain..."
          ></textarea>
        </section>

        <section class="section-box mb-3">
          <div class="section-title">D. Batuk dan/atau Sukar Bernapas</div>
          <YesNo v-model="form.batuk.ada" label="Apakah anak batuk dan/atau sukar bernapas?" />

          <div v-if="form.batuk.ada" class="mt-2">
            <label class="label-sm">Sudah berapa lama?</label>
            <div class="input-group input-group-sm">
              <input v-model.number="form.batukLama" type="number" min="0" step="1" class="form-control" />
              <span class="input-group-text">hari</span>
            </div>
            <small class="text-muted">Napas cepat, tarikan dada, wheezing, dan SpO₂ dinilai di Objektif.</small>
          </div>
        </section>

        <section class="section-box">
          <div class="section-title">E. Diare</div>
          <YesNo v-model="form.diare.ada" label="Apakah anak diare?" />

          <div v-if="form.diare.ada" class="mt-2">
            <label class="label-sm">Sudah berapa lama?</label>
            <div class="input-group input-group-sm mb-2">
              <input v-model.number="form.diareLama" type="number" min="0" step="1" class="form-control" />
              <span class="input-group-text">hari</span>
            </div>

            <label class="option-item">
              <input v-model="form.darahTinja" type="checkbox" />
              <span>Ada darah dalam tinja</span>
            </label>
          </div>
        </section>
      </div>

      <div class="col-xl-4">
        <section class="section-box mb-3">
          <div class="section-title">F. Demam, Malaria, dan Campak</div>
          <YesNo v-model="form.demam.ada" label="Apakah anak demam atau ada riwayat demam?" />

          <div v-if="form.demam.ada" class="mt-2">
            <label class="label-sm">Sudah berapa lama?</label>
            <div class="input-group input-group-sm mb-2">
              <input v-model.number="form.demamLama" type="number" min="0" step="1" class="form-control" />
              <span class="input-group-text">hari</span>
            </div>

            <label class="option-item mb-1">
              <input v-model="form.demamTiapHari" type="checkbox" />
              <span>Jika lebih dari 7 hari, demam terjadi setiap hari</span>
            </label>
            <label class="option-item mb-1">
              <input v-model="form.riwayatMalaria" type="checkbox" />
              <span>Pernah sakit malaria</span>
            </label>
            <label class="option-item mb-2">
              <input v-model="form.demam.minumObatAntimalaria" type="checkbox" />
              <span>Pernah/minum obat antimalaria selama sakit ini</span>
            </label>

            <label class="option-item mb-2">
              <input v-model="form.riwayatCampak" type="checkbox" />
              <span>Anak sakit campak saat ini atau dalam 3 bulan terakhir</span>
            </label>

            <label class="option-item mb-2">
              <input v-model="form.demam.perjalananEndemis" type="checkbox" />
              <span>Bepergian ke daerah endemis malaria dalam 2 minggu terakhir</span>
            </label>

            <div v-if="form.demam.perjalananEndemis">
              <label class="label-sm">Endemisitas tempat yang dikunjungi</label>
              <select v-model="form.demam.risikoDaerahTujuan" class="form-select form-select-sm">
                <option value="">Belum ditentukan</option>
                <option value="endemis_tinggi">Endemis tinggi</option>
                <option value="endemis_rendah">Endemis rendah</option>
                <option value="non_endemis">Non-endemis</option>
              </select>
            </div>
          </div>
        </section>

        <section v-if="form.demam.ada && isDemamDuaSampaiTujuhHari" class="section-box mb-3">
          <div class="section-title">G. Anamnesis Infeksi Dengue (Demam 2–7 Hari)</div>
          <div class="text-muted small mb-2">Pertanyaan kepada ibu/pengasuh; tanda pemeriksaan fisik dan laboratorium dicatat di Objektif.</div>

          <div class="keluhan-grid one-column">
            <label v-for="item in dengueAnamnesisOptions" :key="item.key" class="option-item">
              <input v-model="form.demam.dengue[item.key]" type="checkbox" />
              <span>{{ item.label }}</span>
            </label>
          </div>
        </section>

        <section class="section-box">
          <div class="section-title">H. Masalah Telinga</div>
          <YesNo v-model="form.telinga.ada" label="Apakah anak mempunyai masalah telinga?" />

          <div v-if="form.telinga.ada" class="mt-2">
            <label class="option-item mb-1">
              <input v-model="form.nyeriTelinga" type="checkbox" />
              <span>Nyeri telinga</span>
            </label>
            <label class="option-item mb-1">
              <input v-model="form.telinga.rasaPenuh" type="checkbox" />
              <span>Rasa penuh di telinga</span>
            </label>
            <label class="option-item mb-2">
              <input v-model="form.cairanTelinga" type="checkbox" />
              <span>Cairan atau nanah keluar dari telinga</span>
            </label>

            <div v-if="form.cairanTelinga">
              <label class="label-sm">Sudah berapa lama cairan/nanah keluar?</label>
              <div class="input-group input-group-sm">
                <input v-model.number="form.telingaLama" type="number" min="0" step="1" class="form-control" />
                <span class="input-group-text">hari</span>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div class="col-xl-4">
        <section class="section-box mb-3">
          <div class="section-title">I. Anamnesis Status HIV</div>
          <div class="row g-2">
            <div class="col-12">
              <label class="label-sm">Apakah ibu pernah dites HIV?</label>
              <select v-model="form.hiv.ibuPernahTes" class="form-select form-select-sm">
                <option value="">Belum ditanyakan</option>
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
                <option value="tidak_diketahui">Tidak diketahui</option>
              </select>
            </div>

            <div class="col-12">
              <label class="label-sm">Apakah anak pernah dites HIV?</label>
              <select v-model="form.hiv.anakPernahTes" class="form-select form-select-sm">
                <option value="">Belum ditanyakan</option>
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
                <option value="tidak_diketahui">Tidak diketahui</option>
              </select>
            </div>
          </div>

          <hr />

          <label class="option-item mb-1">
            <input v-model="form.hiv.asiSaatTesAtauEnamMinggu" type="checkbox" />
            <span>Anak mendapat ASI saat tes HIV atau dalam 6 minggu sebelum tes</span>
          </label>
          <label class="option-item mb-1">
            <input v-model="form.hiv.asiSekarang" type="checkbox" />
            <span>Anak saat ini masih mendapat ASI</span>
          </label>
          <label class="option-item mb-2">
            <input v-model="form.hiv.arvProfilaksis" type="checkbox" />
            <span>Ibu mendapat ARV dan anak mendapat profilaksis ARV</span>
          </label>

          <button type="button" class="collapse-head" @click="showIndikasiHiv = !showIndikasiHiv">
            <span>Indikasi melakukan tes HIV bila status belum diketahui</span>
            <span>{{ showIndikasiHiv ? '−' : '+' }}</span>
          </button>

          <div v-if="showIndikasiHiv" class="mt-2">
            <label v-for="item in indikasiTesHivOptions" :key="item.key" class="option-item mb-1">
              <input v-model="form.hiv.indikasiTes[item.key]" type="checkbox" />
              <span>{{ item.label }}</span>
            </label>
          </div>

          <small class="text-muted d-block mt-2">Hasil tes ibu/anak dicatat pada Objektif sebagai hasil pemeriksaan.</small>
        </section>

        <section class="section-box mb-3">
          <button type="button" class="collapse-head" @click="showRiwayat = !showRiwayat">
            <span>J. Riwayat Penting Lain</span>
            <span>{{ showRiwayat ? '−' : '+' }}</span>
          </button>

          <div v-if="showRiwayat" class="row g-2 mt-2">
            <div class="col-12">
              <label class="label-sm">Riwayat imunisasi</label>
              <textarea v-model="form.riwayatImunisasi" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="label-sm">Pemberian Vitamin A</label>
              <textarea v-model="form.vitaminA" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="label-sm">Riwayat ASI/pemberian makan</label>
              <textarea v-model="form.riwayatASI" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="label-sm">Riwayat penyakit sebelumnya</label>
              <textarea v-model="form.riwayatPenyakit" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="label-sm">Catatan HIV ibu</label>
              <textarea v-model="form.hivIbu" class="form-control form-control-sm" rows="2"></textarea>
            </div>
          </div>
        </section>

        <div class="alert alert-light border small mb-0">
          <strong>Pemisahan data:</strong> anamnesis/riwayat ada di Subjektif. RR, suhu, SpO₂, tarikan dada, keadaan dehidrasi, hasil tes, tanda campak/dengue, telinga, anemia, dan hasil HIV ada di Objektif.
        </div>
      </div>
    </div>

    <div class="text-end mt-3">
      <button type="button" class="btn btn-success btn-sm px-4" :disabled="loading || saving" @click="simpan">
        {{ saving ? 'Menyimpan...' : 'Simpan Subjektif MTBS' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, defineComponent, h, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const YesNo = defineComponent({
  name: 'YesNo',
  props: {
    modelValue: { default: null },
    label: { type: String, required: true },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    return () => h('div', { class: 'yes-no-row' }, [
      h('div', { class: 'fw-semibold small' }, props.label),
      h('div', { class: 'btn-group btn-group-sm' }, [
        h('button', {
          type: 'button',
          class: ['btn', props.modelValue === true ? 'btn-success' : 'btn-outline-secondary'],
          onClick: () => emit('update:modelValue', true),
        }, 'Ya'),
        h('button', {
          type: 'button',
          class: ['btn', props.modelValue === false ? 'btn-secondary' : 'btn-outline-secondary'],
          onClick: () => emit('update:modelValue', false),
        }, 'Tidak'),
      ]),
    ])
  },
})

const page = usePage()
const idPelayanan = page.props.idPelayanan

const loading = ref(false)
const saving = ref(false)
const message = ref('')
const showRiwayat = ref(false)
const showIndikasiHiv = ref(false)

const dengueAnamnesisOptions = [
  { key: 'demamMendadakTinggiTerus', label: 'Demam mendadak tinggi dan terus-menerus' },
  { key: 'badanDingin', label: 'Badan teraba dingin' },
  { key: 'lemasGelisah', label: 'Anak lemas atau gelisah' },
  { key: 'mual', label: 'Mual' },
  { key: 'muntah', label: 'Muntah' },
  { key: 'muntahTerus', label: 'Muntah terus-menerus' },
  { key: 'nyeriPerut', label: 'Nyeri perut' },
  { key: 'mimisan', label: 'Mimisan/perdarahan mukosa' },
  { key: 'muntahDarah', label: 'Muntah darah' },
  { key: 'muntahKopi', label: 'Muntah coklat seperti kopi' },
  { key: 'babBerdarahHitam', label: 'BAB berdarah atau berwarna hitam' },
  { key: 'ruam', label: 'Muncul ruam' },
  { key: 'nyeriPegal', label: 'Nyeri kepala/mata/otot/sendi atau rasa sakit badan' },
  { key: 'tidakBak6Jam', label: 'Tidak BAK selama 6 jam atau lebih' },
]

const indikasiTesHivOptions = [
  { key: 'pneumoniaBerulang', label: 'Pneumonia berulang' },
  { key: 'diarePersistenBerulang', label: 'Diare persisten berulang' },
  { key: 'thrushBerulang', label: 'Bercak putih/thrush di mulut berulang' },
  { key: 'infeksiBeratBerulang', label: 'Infeksi berat berulang' },
  { key: 'giziTidakMembaik', label: 'Gizi kurang/buruk tidak membaik dengan penanganan gizi' },
]

const createBooleanMap = (items) => Object.fromEntries(items.map((item) => [item.key, false]))

const form = reactive({
  jenisKunjungan: '',
  umurTahun: null,
  umurBulan: null,
  jenisKelamin: '',
  keluhanUtama: [],
  keluhanLain: '',

  tandaBahaya: {
    bisaMinumMenyusu: null,
    memuntahkanSemua: null,
    pernahKejang: null,
  },

  batuk: { ada: null },
  batukLama: null,

  diare: { ada: null },
  diareLama: null,
  darahTinja: false,

  demam: {
    ada: null,
    minumObatAntimalaria: false,
    perjalananEndemis: false,
    risikoDaerahTujuan: '',
    dengue: createBooleanMap(dengueAnamnesisOptions),
  },
  demamLama: null,
  demamTiapHari: false,
  riwayatMalaria: false,
  riwayatCampak: false,

  telinga: { ada: null, rasaPenuh: false },
  nyeriTelinga: false,
  cairanTelinga: false,
  telingaLama: null,

  hiv: {
    ibuPernahTes: '',
    anakPernahTes: '',
    asiSaatTesAtauEnamMinggu: false,
    asiSekarang: false,
    arvProfilaksis: false,
    indikasiTes: createBooleanMap(indikasiTesHivOptions),
  },

  riwayatImunisasi: '',
  vitaminA: '',
  riwayatASI: '',
  riwayatPenyakit: '',
  hivIbu: '',
})

const daftarKeluhan = [
  'Batuk',
  'Sukar bernapas',
  'Diare',
  'Demam',
  'Nyeri telinga',
  'Rasa penuh telinga',
  'Cairan telinga',
  'Tidak mau makan/minum',
  'Kejang',
  'Lemah/tidak aktif',
]

const isDemamDuaSampaiTujuhHari = computed(() => {
  const hari = Number(form.demamLama)
  return Number.isFinite(hari) && hari >= 2 && hari <= 7
})

const resetForm = () => {
  form.jenisKunjungan = ''
  form.umurTahun = null
  form.umurBulan = null
  form.jenisKelamin = ''
  form.keluhanUtama = []
  form.keluhanLain = ''

  form.tandaBahaya.bisaMinumMenyusu = null
  form.tandaBahaya.memuntahkanSemua = null
  form.tandaBahaya.pernahKejang = null

  form.batuk.ada = null
  form.batukLama = null
  form.diare.ada = null
  form.diareLama = null
  form.darahTinja = false

  form.demam.ada = null
  form.demamLama = null
  form.demamTiapHari = false
  form.riwayatMalaria = false
  form.riwayatCampak = false
  form.demam.minumObatAntimalaria = false
  form.demam.perjalananEndemis = false
  form.demam.risikoDaerahTujuan = ''
  Object.keys(form.demam.dengue).forEach((key) => { form.demam.dengue[key] = false })

  form.telinga.ada = null
  form.telinga.rasaPenuh = false
  form.nyeriTelinga = false
  form.cairanTelinga = false
  form.telingaLama = null

  form.hiv.ibuPernahTes = ''
  form.hiv.anakPernahTes = ''
  form.hiv.asiSaatTesAtauEnamMinggu = false
  form.hiv.asiSekarang = false
  form.hiv.arvProfilaksis = false
  Object.keys(form.hiv.indikasiTes).forEach((key) => { form.hiv.indikasiTes[key] = false })

  form.riwayatImunisasi = ''
  form.vitaminA = ''
  form.riwayatASI = ''
  form.riwayatPenyakit = ''
  form.hivIbu = ''
}

const applyData = (data) => {
  resetForm()
  if (!data) return

  form.jenisKunjungan = data.jenisKunjungan ?? ''
  form.umurTahun = data.umurTahun ?? null
  form.umurBulan = data.umurBulan ?? null
  form.jenisKelamin = data.jenisKelamin ?? ''
  form.keluhanUtama = Array.isArray(data.keluhanUtama) ? data.keluhanUtama : []
  form.keluhanLain = data.keluhanLain ?? ''

  form.batukLama = data.batukLama ?? null
  form.diareLama = data.diareLama ?? null
  form.darahTinja = !!data.darahTinja
  form.demamLama = data.demamLama ?? null
  form.demamTiapHari = !!data.demamTiapHari
  form.riwayatMalaria = !!data.riwayatMalaria
  form.riwayatCampak = !!data.riwayatCampak
  form.nyeriTelinga = !!data.nyeriTelinga
  form.cairanTelinga = !!data.cairanTelinga
  form.telingaLama = data.telingaLama ?? null

  const a = data.anamnesisKhusus || {}
  form.tandaBahaya.bisaMinumMenyusu = a.tanda_bahaya?.bisa_minum_menyusu ?? null
  form.tandaBahaya.memuntahkanSemua = a.tanda_bahaya?.memuntahkan_semua ?? null
  form.tandaBahaya.pernahKejang = a.tanda_bahaya?.pernah_kejang ?? null

  form.batuk.ada = a.batuk?.ada ?? (Number(form.batukLama) > 0 || form.keluhanUtama.includes('Batuk') || form.keluhanUtama.includes('Sukar bernapas'))
  form.diare.ada = a.diare?.ada ?? (Number(form.diareLama) > 0 || form.darahTinja || form.keluhanUtama.includes('Diare'))
  form.demam.ada = a.demam?.ada ?? (Number(form.demamLama) > 0 || form.keluhanUtama.includes('Demam'))
  form.demam.minumObatAntimalaria = !!a.demam?.minum_obat_antimalaria
  form.demam.perjalananEndemis = !!a.demam?.perjalanan_endemis
  form.demam.risikoDaerahTujuan = a.demam?.risiko_daerah_tujuan ?? ''
  Object.keys(form.demam.dengue).forEach((key) => {
    form.demam.dengue[key] = !!a.demam?.dengue?.[key]
  })

  form.telinga.ada = a.telinga?.ada ?? (form.nyeriTelinga || form.cairanTelinga || form.keluhanUtama.some((item) => String(item).toLowerCase().includes('telinga')))
  form.telinga.rasaPenuh = !!a.telinga?.rasa_penuh

  form.hiv.ibuPernahTes = a.hiv?.ibu_pernah_tes ?? ''
  form.hiv.anakPernahTes = a.hiv?.anak_pernah_tes ?? ''
  form.hiv.asiSaatTesAtauEnamMinggu = !!a.hiv?.asi_saat_tes_atau_6_minggu
  form.hiv.asiSekarang = !!a.hiv?.asi_sekarang
  form.hiv.arvProfilaksis = !!a.hiv?.arv_profilaksis
  Object.keys(form.hiv.indikasiTes).forEach((key) => {
    form.hiv.indikasiTes[key] = !!a.hiv?.indikasi_tes?.[key]
  })

  form.riwayatImunisasi = data.riwayatImunisasi ?? ''
  form.vitaminA = data.vitaminA ?? ''
  form.riwayatASI = data.riwayatASI ?? ''
  form.riwayatPenyakit = data.riwayatPenyakit ?? ''
  form.hivIbu = data.hivIbu ?? ''
}

const buildAnamnesisKhusus = () => ({
  tanda_bahaya: {
    bisa_minum_menyusu: form.tandaBahaya.bisaMinumMenyusu,
    memuntahkan_semua: form.tandaBahaya.memuntahkanSemua,
    pernah_kejang: form.tandaBahaya.pernahKejang,
  },
  batuk: { ada: form.batuk.ada },
  diare: { ada: form.diare.ada },
  demam: {
    ada: form.demam.ada,
    minum_obat_antimalaria: !!form.demam.minumObatAntimalaria,
    campak_3_bulan: !!form.riwayatCampak,
    perjalanan_endemis: !!form.demam.perjalananEndemis,
    risiko_daerah_tujuan: form.demam.risikoDaerahTujuan || null,
    dengue: { ...form.demam.dengue },
  },
  telinga: {
    ada: form.telinga.ada,
    rasa_penuh: !!form.telinga.rasaPenuh,
  },
  hiv: {
    ibu_pernah_tes: form.hiv.ibuPernahTes || null,
    anak_pernah_tes: form.hiv.anakPernahTes || null,
    asi_saat_tes_atau_6_minggu: !!form.hiv.asiSaatTesAtauEnamMinggu,
    asi_sekarang: !!form.hiv.asiSekarang,
    arv_profilaksis: !!form.hiv.arvProfilaksis,
    indikasi_tes: { ...form.hiv.indikasiTes },
  },
})

const loadFromDb = async () => {
  if (!idPelayanan || loading.value) return
  loading.value = true
  message.value = ''

  try {
    const response = await axios.get(`/simpus/kia/mtbs/subjektif/${idPelayanan}`)
    applyData(response.data?.data)
  } catch (error) {
    console.error('LOAD SUBJEKTIF MTBS:', error.response?.data || error)
    message.value = error.response?.data?.message || 'Gagal memuat Subjektif MTBS.'
  } finally {
    loading.value = false
  }
}

const simpan = async () => {
  if (!idPelayanan || saving.value) return

  const jawabanWajib = [
    form.tandaBahaya.bisaMinumMenyusu,
    form.tandaBahaya.memuntahkanSemua,
    form.tandaBahaya.pernahKejang,
    form.batuk.ada,
    form.diare.ada,
    form.demam.ada,
    form.telinga.ada,
  ]

  if (jawabanWajib.some((value) => value === null)) {
    message.value = 'Lengkapi semua pertanyaan Ya/Tidak wajib: tanda bahaya, batuk, diare, demam, dan masalah telinga.'
    return
  }

  saving.value = true
  message.value = ''

  try {
    await axios.post('/simpus/kia/mtbs/subjektif/store', {
      kunjungan_id: String(idPelayanan),
      jenisKunjungan: form.jenisKunjungan,
      umurTahun: form.umurTahun,
      umurBulan: form.umurBulan,
      jenisKelamin: form.jenisKelamin,
      keluhanUtama: form.keluhanUtama,
      keluhanLain: form.keluhanLain,
      batukLama: form.batuk.ada ? form.batukLama : null,
      napasCepat: false,
      mengi: false,
      diareLama: form.diare.ada ? form.diareLama : null,
      darahTinja: form.diare.ada && form.darahTinja,
      demamLama: form.demam.ada ? form.demamLama : null,
      demamTiapHari: form.demam.ada && form.demamTiapHari,
      riwayatMalaria: form.demam.ada && form.riwayatMalaria,
      riwayatCampak: form.demam.ada && form.riwayatCampak,
      nyeriTelinga: form.telinga.ada && form.nyeriTelinga,
      cairanTelinga: form.telinga.ada && form.cairanTelinga,
      telingaLama: form.telinga.ada && form.cairanTelinga ? form.telingaLama : null,
      anamnesisKhusus: buildAnamnesisKhusus(),
      riwayatImunisasi: form.riwayatImunisasi,
      vitaminA: form.vitaminA,
      riwayatASI: form.riwayatASI,
      riwayatPenyakit: form.riwayatPenyakit,
      hivIbu: form.hivIbu,
    })

    await loadFromDb()
    message.value = 'Subjektif MTBS berhasil disimpan.'
  } catch (error) {
    console.error('SAVE SUBJEKTIF MTBS:', error.response?.data || error)
    if (error.response?.status === 422) {
      const errors = error.response?.data?.errors || {}
      message.value = `Validasi gagal: ${Object.values(errors).flat().join(' | ')}`
    } else {
      message.value = error.response?.data?.message || 'Gagal menyimpan Subjektif MTBS.'
    }
  } finally {
    saving.value = false
  }
}

onMounted(loadFromDb)
</script>

<style scoped>
.mtbs-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
  font-size: 0.86rem;
}
.header-box {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 14px;
  padding-bottom: 12px;
  border-bottom: 1px solid #eef2f7;
}
.title { margin: 0; font-weight: 800; color: #198754; }
.subtitle { font-size: 0.76rem; color: #6c757d; margin-top: 2px; }
.section-box { background: #fbfdfc; border: 1px solid #e7f1eb; border-radius: 12px; padding: 12px; }
.section-title { font-size: 0.9rem; font-weight: 800; color: #185c38; margin-bottom: 10px; }
.label-sm { display: block; font-size: 0.78rem; font-weight: 700; margin-bottom: 4px; }
.auto-visit-box { background: #fff; border: 1px dashed #cfe3d6; border-radius: 10px; padding: 10px; }
.keluhan-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 7px; }
.keluhan-grid.one-column { grid-template-columns: 1fr; }
.option-item { display: flex; gap: 7px; align-items: flex-start; line-height: 1.25; cursor: pointer; }
.option-item input { margin-top: 2px; }
.yes-no-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 8px; background: #fff; border: 1px solid #edf2ef; border-radius: 9px; }
.collapse-head { width: 100%; display: flex; justify-content: space-between; align-items: center; border: 0; background: transparent; padding: 0; font-weight: 700; color: #185c38; text-align: left; }
@media (max-width: 575.98px) {
  .keluhan-grid { grid-template-columns: 1fr; }
}
</style>
