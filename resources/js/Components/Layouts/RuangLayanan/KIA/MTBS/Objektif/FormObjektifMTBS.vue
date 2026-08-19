<template>
  <div class="bg-white p-3 rounded-3 shadow-sm objective-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h6 class="fw-bold text-success mb-1">O – OBJEKTIF (MTBS)</h6>
        <div class="text-muted small">Hasil pemeriksaan fisik dan penunjang balita sakit umur 2 bulan sampai kurang dari 5 tahun.</div>
      </div>

      <div class="d-flex gap-2 flex-wrap justify-content-end">
        <button type="button" class="btn btn-outline-danger btn-sm" :disabled="loading || saving || deleting" @click="hapusDataTesting">
          {{ deleting ? 'Menghapus...' : 'Hapus Data Testing' }}
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="loading || saving || deleting" @click="loadFromDb">
          {{ loading ? 'Memuat...' : 'Muat Ulang' }}
        </button>
        <button type="button" class="btn btn-success btn-sm" :disabled="loading || saving || deleting" @click="simpan">
          {{ saving ? 'Menyimpan...' : 'Simpan Objektif' }}
        </button>
      </div>
    </div>

    <div v-if="message" class="alert alert-info py-2">{{ message }}</div>

    <div class="row g-4">
      <div class="col-xl-6">
        <section class="form-section mb-4">
          <h6 class="section-title">A. Segitiga Asesmen Gawat Anak (SAGA)</h6>

          <div class="subsection-box mb-3">
            <div class="fw-semibold mb-2">Penampilan</div>
            <div v-for="item in sagaPenampilanOptions" :key="item" class="form-check mb-2">
              <input :id="`penampilan-${slug(item)}`" v-model="form.saga.penampilan" class="form-check-input" type="checkbox" :value="item" />
              <label class="form-check-label" :for="`penampilan-${slug(item)}`">{{ item }}</label>
            </div>
          </div>

          <div class="subsection-box mb-3">
            <div class="fw-semibold mb-2">Usaha napas</div>
            <div v-for="item in sagaNapasOptions" :key="item" class="form-check mb-2">
              <input :id="`napas-${slug(item)}`" v-model="form.saga.napas" class="form-check-input" type="checkbox" :value="item" />
              <label class="form-check-label" :for="`napas-${slug(item)}`">{{ item }}</label>
            </div>
          </div>

          <div class="subsection-box">
            <div class="fw-semibold mb-2">Sirkulasi</div>
            <div v-for="item in sagaSirkulasiOptions" :key="item" class="form-check mb-2">
              <input :id="`sirkulasi-${slug(item)}`" v-model="form.saga.sirkulasi" class="form-check-input" type="checkbox" :value="item" />
              <label class="form-check-label" :for="`sirkulasi-${slug(item)}`">{{ item }}</label>
            </div>
          </div>
          <small class="text-muted d-block mt-2">Status kegawatan final juga memperhitungkan tanda bahaya umum yang diisi pada Subjektif.</small>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">B. Tanda Vital dan Antropometri Mentah</h6>
          <div class="row g-3">
            <div v-for="field in numericFields" :key="field.key" class="col-md-6">
              <label class="form-label fw-semibold">{{ field.label }}</label>
              <div class="input-group">
                <input
                  v-model.number="form[field.key]"
                  type="number"
                  class="form-control"
                  :min="field.min"
                  :max="field.max"
                  :step="field.step"
                  :placeholder="field.placeholder"
                />
                <span class="input-group-text">{{ field.unit }}</span>
              </div>
            </div>
          </div>
          <small class="text-muted d-block mt-2">BB, PB/TB, LiLA, dan LK dipakai oleh modul Gizi; klasifikasi gizi/pertumbuhan tidak diketik ulang di Objektif.</small>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">C. Batuk/Sukar Bernapas</h6>
          <div class="form-check mb-2">
            <input id="resp-tarikan" v-model="tarikanDada" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="resp-tarikan">Ada tarikan dinding dada ke dalam</label>
          </div>
          <div class="form-check">
            <input id="wheezing" v-model="form.wheezing" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="wheezing">Ada wheezing/mengi</label>
          </div>
          <small class="text-muted">Napas cepat dihitung otomatis dari RR dan umur; SpO₂ ≤ 92% atau tarikan dada menjadi Pneumonia Berat.</small>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">D. Pemeriksaan Diare</h6>
          <div class="mb-3">
            <label class="form-label fw-semibold">Keadaan umum anak</label>
            <select v-model="form.diare.keadaanUmum" class="form-select">
              <option value="">Belum diperiksa</option>
              <option value="normal">Sadar/tenang</option>
              <option value="letargi">Letargi atau tidak sadar</option>
              <option value="rewel">Rewel atau mudah marah</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Mata</label>
            <select v-model="form.diare.mata" class="form-select">
              <option value="">Belum diperiksa</option>
              <option value="normal">Tidak cekung</option>
              <option value="cekung">Mata cekung</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Beri anak minum</label>
            <select v-model="form.diare.minum" class="form-select">
              <option value="">Belum diperiksa</option>
              <option value="normal">Minum biasa</option>
              <option value="tidak_bisa_malas">Tidak bisa minum atau malas minum</option>
              <option value="haus_lahap">Haus, minum dengan lahap</option>
            </select>
          </div>
          <div>
            <label class="form-label fw-semibold">Cubitan kulit perut</label>
            <select v-model="form.diare.turgor" class="form-select">
              <option value="">Belum diperiksa</option>
              <option value="normal">Kembali segera</option>
              <option value="lambat">Kembali lambat</option>
              <option value="sangat_lambat">Kembali sangat lambat (&gt; 2 detik)</option>
            </select>
          </div>
        </section>
      </div>

      <div class="col-xl-6">
        <section class="form-section mb-4">
          <h6 class="section-title">E. Demam dan Malaria</h6>
          <div class="form-check mb-2">
            <input id="teraba-panas" v-model="form.malaria.terabaPanas" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="teraba-panas">Anak teraba panas</label>
          </div>
          <div class="form-check mb-3">
            <input id="kaku-kuduk" v-model="form.malaria.kakuKuduk" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="kaku-kuduk">Kaku kuduk</label>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Status wilayah malaria tempat pelayanan</label>
            <select v-model="form.malaria.wilayah" class="form-select">
              <option value="">Belum ditentukan</option>
              <option value="endemis_tinggi">Endemis tinggi</option>
              <option value="endemis_rendah">Endemis rendah</option>
              <option value="non_endemis">Non-endemis</option>
            </select>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Hasil RDT malaria</label>
              <select v-model="form.malaria.rdt" class="form-select">
                <option value="">Belum diperiksa</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Hasil mikroskopis malaria</label>
              <select v-model="form.malaria.mikroskopis" class="form-select">
                <option value="">Belum diperiksa</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
          </div>

          <div class="form-check mt-3 mb-2">
            <input id="tes-malaria-tidak-tersedia" v-model="form.malaria.tesTidakTersedia" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="tes-malaria-tidak-tersedia">Tes malaria tidak tersedia</label>
          </div>
          <div class="form-check">
            <input id="penyebab-demam-lain" v-model="form.malaria.penyebabLain" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="penyebab-demam-lain">Ditemukan penyebab lain demam</label>
          </div>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">F. Pemeriksaan Campak</h6>
          <div class="row g-2">
            <div v-for="item in campakOptions" :key="item.key" class="col-md-6">
              <div class="form-check">
                <input :id="`campak-${item.key}`" v-model="form.campak[item.key]" class="form-check-input" type="checkbox" />
                <label class="form-check-label" :for="`campak-${item.key}`">{{ item.label }}</label>
              </div>
            </div>
          </div>
          <small class="text-muted d-block mt-2">Campak saat ini/dalam 3 bulan terakhir ditanyakan di Subjektif.</small>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">G. Pemeriksaan Infeksi Dengue</h6>
          <div class="subsection-box mb-3">
            <div class="fw-semibold text-danger mb-2">Syok, perdarahan berat, distres napas, atau gangguan organ</div>
            <div v-for="item in dengueBeratOptions" :key="item.key" class="form-check mb-2">
              <input :id="`db-${item.key}`" v-model="form.dengue[item.key]" class="form-check-input" type="checkbox" />
              <label class="form-check-label" :for="`db-${item.key}`">{{ item.label }}</label>
            </div>
          </div>

          <div class="subsection-box mb-3">
            <div class="fw-semibold text-warning-emphasis mb-2">Warning signs</div>
            <div v-for="item in dengueWarningOptions" :key="item.key" class="form-check mb-2">
              <input :id="`dw-${item.key}`" v-model="form.dengue[item.key]" class="form-check-input" type="checkbox" />
              <label class="form-check-label" :for="`dw-${item.key}`">{{ item.label }}</label>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Uji tourniquet</label>
              <select v-model="form.dengue.tourniquet" class="form-select">
                <option value="">Belum dilakukan</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">NS1</label>
              <select v-model="form.dengue.ns1" class="form-select">
                <option value="">Belum diperiksa</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Hematokrit</label>
              <div class="input-group"><input v-model.number="form.dengue.hematokrit" type="number" min="0" max="100" step="0.1" class="form-control" /><span class="input-group-text">%</span></div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Leukosit</label>
              <input v-model.number="form.dengue.leukosit" type="number" min="0" step="1" class="form-control" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Trombosit</label>
              <input v-model.number="form.dengue.trombosit" type="number" min="0" step="1" class="form-control" />
            </div>
          </div>

          <div class="form-check mt-3">
            <input id="hct-trend" v-model="form.dengue.hctNaikTrombositTurun" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="hct-trend">Hematokrit meningkat disertai penurunan trombosit yang cepat</label>
          </div>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">H. Pemeriksaan Telinga</h6>
          <div class="form-check mb-2">
            <input id="otore" v-model="form.telinga.cairanNanahTerlihat" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="otore">Terlihat cairan atau nanah keluar dari telinga</label>
          </div>
          <div class="form-check">
            <input id="mastoid" v-model="form.telinga.bengkakNyeriBelakang" class="form-check-input" type="checkbox" />
            <label class="form-check-label" for="mastoid">Pembengkakan yang nyeri di belakang telinga</label>
          </div>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">I. Pemeriksaan Anemia</h6>
          <div class="mb-3">
            <label class="form-label fw-semibold">Derajat kepucatan</label>
            <select v-model="form.anemia.derajat" class="form-select">
              <option value="">Belum diperiksa</option>
              <option value="tidak">Tidak pucat</option>
              <option value="pucat">Pucat</option>
              <option value="sangat_pucat">Sangat pucat</option>
            </select>
          </div>
          <div v-if="form.anemia.derajat === 'pucat' || form.anemia.derajat === 'sangat_pucat'" class="row g-2 mb-3">
            <div v-for="item in lokasiPucatOptions" :key="item" class="col-md-6">
              <div class="form-check">
                <input :id="`pucat-${slug(item)}`" v-model="form.anemia.lokasi" class="form-check-input" type="checkbox" :value="item" />
                <label class="form-check-label" :for="`pucat-${slug(item)}`">{{ item }}</label>
              </div>
            </div>
          </div>
          <label class="form-label fw-semibold">Hemoglobin, bila tersedia</label>
          <div class="input-group"><input v-model.number="form.hb" type="number" min="0" max="30" step="0.1" class="form-control" /><span class="input-group-text">g/dL</span></div>
        </section>

        <section class="form-section mb-4">
          <h6 class="section-title">J. Hasil Pemeriksaan HIV</h6>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tes HIV ibu</label>
              <select v-model="form.hiv.statusIbu" class="form-select">
                <option value="">Belum diketahui</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tes virologi anak</label>
              <select v-model="form.hiv.virologi" class="form-select">
                <option value="">Belum dilakukan/tidak diketahui</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tes serologi anak</label>
              <select v-model="form.hiv.serologi" class="form-select">
                <option value="">Belum dilakukan/tidak diketahui</option>
                <option value="positif">Positif</option>
                <option value="negatif">Negatif</option>
              </select>
            </div>
          </div>
        </section>

        <section class="form-section">
          <h6 class="section-title">L. Temuan Objektif Lain</h6>
          <textarea v-model="form.temuanLainText" class="form-control" rows="4" placeholder="Satu temuan per baris"></textarea>
        </section>
      </div>
    </div>

    <div class="alert mt-4" :class="statusAlertClass"><strong>Status SAGA:</strong> {{ statusSAGA }}</div>

    <div class="text-end mt-3">
      <button type="button" class="btn btn-success btn-sm px-4" :disabled="loading || saving || deleting" @click="simpan">
        {{ saving ? 'Menyimpan...' : 'Simpan Objektif MTBS' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const idPelayanan = page.props.idPelayanan
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const message = ref('')
const tandaBahayaSubjektif = ref([])

const sagaPenampilanOptions = [
  'Kejang',
  'Tidak dapat berinteraksi dengan lingkungan atau tidak sadar',
  'Gelisah, rewel, dan tidak dapat ditenangkan',
  'Pandangan kosong atau mata tidak membuka',
  'Tidak bersuara atau menangis melengking',
]
const sagaNapasOptions = [
  'Tarikan dinding dada ke dalam',
  'Stridor',
  'Napas cuping hidung',
  'Mencari posisi paling nyaman dan menolak berbaring',
]
const sagaSirkulasiOptions = ['Pucat', 'Sianosis', 'Kutis marmorata atau kulit seperti marmer']
const lokasiPucatOptions = ['Telapak tangan', 'Konjungtiva', 'Bibir', 'Lidah', 'Bantalan kuku']
const campakOptions = [
  { key: 'ruamMenyeluruh', label: 'Ruam kemerahan menyeluruh' },
  { key: 'batuk', label: 'Batuk' },
  { key: 'pilek', label: 'Pilek' },
  { key: 'mataMerah', label: 'Mata merah' },
  { key: 'nanahMata', label: 'Nanah pada mata' },
  { key: 'korneaKeruh', label: 'Kekeruhan kornea' },
  { key: 'lukaMulut', label: 'Luka pada mulut' },
  { key: 'lukaMulutBerat', label: 'Luka mulut dalam atau luas' },
]
const dengueBeratOptions = [
  { key: 'kakiTanganPucat', label: 'Kaki/tangan tampak pucat' },
  { key: 'crtLebih2', label: 'Waktu pengisian kapiler > 2 detik' },
  { key: 'ekstremitasDingin', label: 'Kaki/tangan teraba dingin' },
  { key: 'nadiLemah', label: 'Nadi lemah' },
  { key: 'nadiTidakTeraba', label: 'Nadi tidak teraba' },
  { key: 'nadiCepat', label: 'Nadi cepat' },
  { key: 'distresNapas', label: 'Sesak/distres napas' },
  { key: 'penurunanKesadaran', label: 'Penurunan kesadaran' },
  { key: 'penurunanFrekuensiNadi', label: 'Penurunan frekuensi denyut nadi' },
  { key: 'ikterik', label: 'Ikterik' },
]
const dengueWarningOptions = [
  { key: 'nyeriTekanKananAtas', label: 'Nyeri tekan perut kanan atas' },
  { key: 'akumulasiCairan', label: 'Klinis akumulasi cairan' },
  { key: 'perdarahanMukosa', label: 'Perdarahan mukosa' },
  { key: 'petekie', label: 'Perdarahan kulit/petekie' },
  { key: 'letargiGelisah', label: 'Letargi atau gelisah' },
  { key: 'heparLebih2', label: 'Pembesaran hepar > 2 cm' },
]
const numericFields = [
  { key: 'rr', label: 'Frekuensi napas', unit: '/menit', min: 0, max: 200, step: 1, placeholder: '40' },
  { key: 'suhu', label: 'Suhu aksila', unit: '°C', min: 0, max: 50, step: 0.1, placeholder: '36.7' },
  { key: 'spo2', label: 'SpO₂', unit: '%', min: 0, max: 100, step: 1, placeholder: '98' },
  { key: 'nadi', label: 'Frekuensi nadi', unit: '/menit', min: 0, max: 300, step: 1, placeholder: '120' },
  { key: 'bb', label: 'Berat badan', unit: 'kg', min: 0, max: 300, step: 0.1, placeholder: '12.5' },
  { key: 'tb', label: 'PB/TB', unit: 'cm', min: 0, max: 250, step: 0.1, placeholder: '85' },
  { key: 'lila', label: 'LiLA', unit: 'cm', min: 0, max: 100, step: 0.1, placeholder: '13' },
  { key: 'lk', label: 'Lingkar kepala', unit: 'cm', min: 0, max: 100, step: 0.1, placeholder: '46' },
]

const form = reactive({
  saga: { penampilan: [], napas: [], sirkulasi: [] },
  rr: null, suhu: null, spo2: null, nadi: null, bb: null, tb: null, lila: null, lk: null,
  wheezing: false,
  diare: { keadaanUmum: '', mata: '', minum: '', turgor: '' },
  malaria: { terabaPanas: false, kakuKuduk: false, wilayah: '', rdt: '', mikroskopis: '', tesTidakTersedia: false, penyebabLain: false },
  campak: Object.fromEntries(campakOptions.map((item) => [item.key, false])),
  dengue: {
    ...Object.fromEntries([...dengueBeratOptions, ...dengueWarningOptions].map((item) => [item.key, false])),
    tourniquet: '', ns1: '', hematokrit: null, leukosit: null, trombosit: null, hctNaikTrombositTurun: false,
  },
  telinga: { cairanNanahTerlihat: false, bengkakNyeriBelakang: false },
  anemia: { derajat: '', lokasi: [] },
  hb: null,
  hiv: { statusIbu: '', virologi: '', serologi: '' },
  temuanLainText: '',
})

const normalizeText = (value) => String(value ?? '').toLowerCase().replace(/[\\/_\-()[\]:;,>]/g, ' ').replace(/\s+/g, ' ').trim()
const slug = (value) => normalizeText(value).replace(/\s+/g, '-')
const includesExact = (items, text) => items.some((item) => normalizeText(item) === normalizeText(text))
const includesAny = (items, needles) => needles.some((needle) => items.some((item) => normalizeText(item).includes(normalizeText(needle))))
const findNumber = (items, labels) => {
  for (const raw of items) {
    const value = String(raw ?? '').replace(',', '.')
    if (!labels.some((label) => normalizeText(value).includes(normalizeText(label)))) continue
    const match = value.match(/[-+]?\d+(?:\.\d+)?/)
    if (match) return Number(match[0])
  }
  return null
}

const tarikanDada = computed({
  get: () => form.saga.napas.includes('Tarikan dinding dada ke dalam'),
  set: (value) => {
    const label = 'Tarikan dinding dada ke dalam'
    const index = form.saga.napas.indexOf(label)
    if (value && index === -1) form.saga.napas.push(label)
    if (!value && index !== -1) form.saga.napas.splice(index, 1)
  },
})

const statusSAGA = computed(() => {
  const bahaya = tandaBahayaSubjektif.value.length > 0
  const penampilan = form.saga.penampilan.length > 0
  const napas = form.saga.napas.length > 0
  const sirkulasi = form.saga.sirkulasi.length > 0
  if (penampilan && napas && sirkulasi) return 'GAGAL JANTUNG PARU'
  if (bahaya || penampilan || napas || sirkulasi) return 'PENYAKIT SANGAT BERAT'
  return 'STABIL'
})
const statusAlertClass = computed(() => statusSAGA.value === 'GAGAL JANTUNG PARU' ? 'alert-danger' : statusSAGA.value === 'PENYAKIT SANGAT BERAT' ? 'alert-warning' : 'alert-success')

const splitLines = (value) => String(value ?? '').split(/\r?\n/).map((item) => item.trim()).filter(Boolean)

const pemeriksaanKhususPayload = computed(() => {
  const items = []
  if (form.nadi !== null && form.nadi !== '') items.push(`Nadi: ${Number(form.nadi)}`)
  if (form.wheezing) items.push('Wheezing')

  const diareMap = {
    normal: 'Keadaan umum diare: sadar/tenang',
    letargi: 'Keadaan umum diare: letargi atau tidak sadar',
    rewel: 'Keadaan umum diare: rewel atau mudah marah',
  }
  if (diareMap[form.diare.keadaanUmum]) items.push(diareMap[form.diare.keadaanUmum])
  if (form.diare.mata === 'cekung') items.push('Mata cekung')
  if (form.diare.mata === 'normal') items.push('Mata tidak cekung')
  if (form.diare.minum === 'normal') items.push('Kemampuan minum normal')
  if (form.diare.minum === 'tidak_bisa_malas') items.push('Tidak bisa minum atau malas minum')
  if (form.diare.minum === 'haus_lahap') items.push('Haus, minum dengan lahap')
  if (form.diare.turgor === 'normal') items.push('Cubitan kulit kembali segera')
  if (form.diare.turgor === 'lambat') items.push('Cubitan kulit perut kembali lambat')
  if (form.diare.turgor === 'sangat_lambat') items.push('Cubitan kulit perut kembali sangat lambat')

  if (form.malaria.terabaPanas) items.push('Anak teraba panas')
  if (form.malaria.kakuKuduk) items.push('Kaku kuduk')
  if (form.malaria.wilayah === 'endemis_tinggi') items.push('Endemis malaria tinggi')
  if (form.malaria.wilayah === 'endemis_rendah') items.push('Endemis malaria rendah')
  if (form.malaria.wilayah === 'non_endemis') items.push('Non endemis malaria')
  if (form.malaria.rdt === 'positif') items.push('RDT malaria positif')
  if (form.malaria.rdt === 'negatif') items.push('RDT malaria negatif')
  if (form.malaria.mikroskopis === 'positif') items.push('Mikroskopis malaria positif')
  if (form.malaria.mikroskopis === 'negatif') items.push('Mikroskopis malaria negatif')
  if (form.malaria.tesTidakTersedia) items.push('Tes malaria tidak tersedia')
  if (form.malaria.penyebabLain) items.push('Penyebab lain demam ditemukan')

  const campakMap = {
    ruamMenyeluruh: 'Ruam kemerahan menyeluruh', batuk: 'Batuk pada campak', pilek: 'Pilek pada campak', mataMerah: 'Mata merah pada campak',
    nanahMata: 'Nanah pada mata', korneaKeruh: 'Kekeruhan kornea', lukaMulut: 'Luka pada mulut', lukaMulutBerat: 'Luka mulut dalam atau luas',
  }
  Object.entries(campakMap).forEach(([key, label]) => { if (form.campak[key]) items.push(label) })

  const dengueMap = {
    kakiTanganPucat: 'Kaki/tangan tampak pucat', crtLebih2: 'Waktu pengisian kapiler lebih dari 2 detik', ekstremitasDingin: 'Kaki/tangan teraba dingin',
    nadiLemah: 'Nadi lemah', nadiTidakTeraba: 'Nadi tidak teraba', nadiCepat: 'Nadi cepat', distresNapas: 'Distres napas',
    penurunanKesadaran: 'Penurunan kesadaran', penurunanFrekuensiNadi: 'Penurunan frekuensi denyut nadi', ikterik: 'Ikterik',
    nyeriTekanKananAtas: 'Nyeri tekan perut kanan atas', akumulasiCairan: 'Klinis akumulasi cairan',
    perdarahanMukosa: 'Perdarahan mukosa', petekie: 'Perdarahan kulit/petekie', letargiGelisah: 'Letargi atau gelisah', heparLebih2: 'Pembesaran hepar lebih dari 2 cm',
  }
  Object.entries(dengueMap).forEach(([key, label]) => { if (form.dengue[key]) items.push(label) })
  if (form.dengue.tourniquet === 'positif') items.push('Uji tourniquet positif')
  if (form.dengue.tourniquet === 'negatif') items.push('Uji tourniquet negatif')
  if (form.dengue.ns1 === 'positif') items.push('NS1 positif')
  if (form.dengue.ns1 === 'negatif') items.push('NS1 negatif')
  if (form.dengue.hematokrit !== null && form.dengue.hematokrit !== '') items.push(`Hematokrit: ${Number(form.dengue.hematokrit)}`)
  if (form.dengue.leukosit !== null && form.dengue.leukosit !== '') items.push(`Leukosit: ${Number(form.dengue.leukosit)}`)
  if (form.dengue.trombosit !== null && form.dengue.trombosit !== '') items.push(`Trombosit: ${Number(form.dengue.trombosit)}`)
  if (form.dengue.hctNaikTrombositTurun) items.push('Hematokrit meningkat disertai penurunan trombosit yang cepat')

  if (form.telinga.cairanNanahTerlihat) items.push('Terlihat cairan atau nanah keluar dari telinga')
  if (form.telinga.bengkakNyeriBelakang) items.push('Pembengkakan yang nyeri di belakang telinga')

  if (form.anemia.derajat === 'tidak') items.push('Tidak pucat')
  if (form.anemia.derajat === 'pucat') items.push('Pucat')
  if (form.anemia.derajat === 'sangat_pucat') items.push('Sangat pucat')
  if (form.anemia.derajat === 'pucat' || form.anemia.derajat === 'sangat_pucat') {
    form.anemia.lokasi.forEach((lokasi) => items.push(`${lokasi} pucat`))
  }
  if (form.hb !== null && form.hb !== '') items.push(`Hb: ${Number(form.hb)}`)

  if (form.hiv.statusIbu) items.push(`Status HIV ibu: ${form.hiv.statusIbu}`)
  if (form.hiv.virologi) items.push(`Tes virologi anak: ${form.hiv.virologi}`)
  if (form.hiv.serologi) items.push(`Tes serologi anak: ${form.hiv.serologi}`)

  return Array.from(new Set([...items, ...splitLines(form.temuanLainText)]))
})

const knownFinding = (item) => {
  const text = normalizeText(item)
  const prefixes = ['nadi ', 'hb ', 'hematokrit ', 'leukosit ', 'trombosit ', 'keadaan umum diare ', 'status hiv ibu ', 'tes virologi anak ', 'tes serologi anak ']
  if (prefixes.some((prefix) => text.startsWith(prefix))) return true
  const labels = [
    'wheezing','mata cekung','mata tidak cekung','kemampuan minum normal','tidak bisa minum atau malas minum','haus minum dengan lahap',
    'cubitan kulit kembali segera','cubitan kulit perut kembali lambat','cubitan kulit perut kembali sangat lambat','anak teraba panas','kaku kuduk',
    'endemis malaria tinggi','endemis malaria rendah','non endemis malaria','rdt malaria positif','rdt malaria negatif','mikroskopis malaria positif','mikroskopis malaria negatif','tes malaria tidak tersedia','penyebab lain demam ditemukan',
    ...Object.values({ ruamMenyeluruh: 'Ruam kemerahan menyeluruh', batuk: 'Batuk pada campak', pilek: 'Pilek pada campak', mataMerah: 'Mata merah pada campak', nanahMata: 'Nanah pada mata', korneaKeruh: 'Kekeruhan kornea', lukaMulut: 'Luka pada mulut', lukaMulutBerat: 'Luka mulut dalam atau luas' }),
    ...dengueBeratOptions.map((x) => x.label), ...dengueWarningOptions.map((x) => x.label),
    'uji tourniquet positif','uji tourniquet negatif','ns1 positif','ns1 negatif','hematokrit meningkat disertai penurunan trombosit yang cepat',
    'terlihat cairan atau nanah keluar dari telinga','pembengkakan yang nyeri di belakang telinga','tidak pucat','pucat','sangat pucat',
    ...lokasiPucatOptions.map((x) => `${x} pucat`),
  ].map(normalizeText)
  return labels.some((label) => text === label || text.includes(label))
}

const resetStructured = () => {
  form.saga.penampilan = []
  form.saga.napas = []
  form.saga.sirkulasi = []
  form.rr = null; form.suhu = null; form.spo2 = null; form.nadi = null; form.bb = null; form.tb = null; form.lila = null; form.lk = null
  form.wheezing = false
  form.diare.keadaanUmum = ''; form.diare.mata = ''; form.diare.minum = ''; form.diare.turgor = ''
  form.malaria.terabaPanas = false; form.malaria.kakuKuduk = false; form.malaria.wilayah = ''; form.malaria.rdt = ''; form.malaria.mikroskopis = ''; form.malaria.tesTidakTersedia = false; form.malaria.penyebabLain = false
  Object.keys(form.campak).forEach((key) => { form.campak[key] = false })
  Object.keys(form.dengue).forEach((key) => {
    if (typeof form.dengue[key] === 'boolean') form.dengue[key] = false
    else if (['hematokrit','leukosit','trombosit'].includes(key)) form.dengue[key] = null
    else form.dengue[key] = ''
  })
  form.telinga.cairanNanahTerlihat = false; form.telinga.bengkakNyeriBelakang = false
  form.anemia.derajat = ''; form.anemia.lokasi = []; form.hb = null
  form.hiv.statusIbu = ''; form.hiv.virologi = ''; form.hiv.serologi = ''
  form.temuanLainText = ''
}

const applyData = (data) => {
  resetStructured()
  if (!data) return
  form.saga.penampilan = Array.isArray(data.saga?.penampilan) ? data.saga.penampilan : []
  form.saga.napas = Array.isArray(data.saga?.napas) ? data.saga.napas : []
  form.saga.sirkulasi = Array.isArray(data.saga?.sirkulasi) ? data.saga.sirkulasi : []
  form.rr = data.rr ?? null; form.suhu = data.suhu ?? null; form.spo2 = data.spo2 ?? null; form.bb = data.bb ?? null; form.tb = data.tb ?? null; form.lila = data.lila ?? null; form.lk = data.lk ?? null
  const items = Array.isArray(data.pemeriksaanKhusus) ? data.pemeriksaanKhusus : []
  form.nadi = findNumber(items, ['nadi'])
  form.wheezing = includesAny(items, ['wheezing','mengi'])
  if (includesAny(items, ['keadaan umum diare letargi'])) form.diare.keadaanUmum = 'letargi'
  else if (includesAny(items, ['keadaan umum diare rewel'])) form.diare.keadaanUmum = 'rewel'
  else if (includesAny(items, ['keadaan umum diare sadar','keadaan umum diare tenang'])) form.diare.keadaanUmum = 'normal'
  if (includesExact(items, 'Mata cekung')) form.diare.mata = 'cekung'; else if (includesExact(items, 'Mata tidak cekung')) form.diare.mata = 'normal'
  if (includesAny(items, ['tidak bisa minum','malas minum'])) form.diare.minum = 'tidak_bisa_malas'; else if (includesAny(items, ['minum dengan lahap'])) form.diare.minum = 'haus_lahap'; else if (includesExact(items, 'Kemampuan minum normal')) form.diare.minum = 'normal'
  if (includesAny(items, ['sangat lambat'])) form.diare.turgor = 'sangat_lambat'; else if (includesAny(items, ['kembali lambat'])) form.diare.turgor = 'lambat'; else if (includesAny(items, ['kembali segera'])) form.diare.turgor = 'normal'
  form.malaria.terabaPanas = includesExact(items, 'Anak teraba panas')
  form.malaria.kakuKuduk = includesExact(items, 'Kaku kuduk')
  if (includesExact(items, 'Endemis malaria tinggi')) form.malaria.wilayah = 'endemis_tinggi'; else if (includesExact(items, 'Endemis malaria rendah')) form.malaria.wilayah = 'endemis_rendah'; else if (includesExact(items, 'Non endemis malaria')) form.malaria.wilayah = 'non_endemis'
  if (includesExact(items, 'RDT malaria positif')) form.malaria.rdt = 'positif'; else if (includesExact(items, 'RDT malaria negatif')) form.malaria.rdt = 'negatif'
  if (includesExact(items, 'Mikroskopis malaria positif')) form.malaria.mikroskopis = 'positif'; else if (includesExact(items, 'Mikroskopis malaria negatif')) form.malaria.mikroskopis = 'negatif'
  form.malaria.tesTidakTersedia = includesExact(items, 'Tes malaria tidak tersedia'); form.malaria.penyebabLain = includesExact(items, 'Penyebab lain demam ditemukan')
  const campakNeedles = { ruamMenyeluruh:'Ruam kemerahan menyeluruh', batuk:'Batuk pada campak', pilek:'Pilek pada campak', mataMerah:'Mata merah pada campak', nanahMata:'Nanah pada mata', korneaKeruh:'Kekeruhan kornea', lukaMulut:'Luka pada mulut', lukaMulutBerat:'Luka mulut dalam atau luas' }
  Object.entries(campakNeedles).forEach(([key,label]) => { form.campak[key] = includesExact(items,label) })
  const dengueNeedles = {
    kakiTanganPucat: 'kaki tangan tampak pucat',
    crtLebih2: 'pengisian kapiler lebih dari 2 detik',
    ekstremitasDingin: 'kaki tangan teraba dingin',
    nadiLemah: 'nadi lemah',
    nadiTidakTeraba: 'nadi tidak teraba',
    nadiCepat: 'nadi cepat',
    distresNapas: 'distres napas',
    penurunanKesadaran: 'penurunan kesadaran',
    penurunanFrekuensiNadi: 'penurunan frekuensi denyut nadi',
    ikterik: 'ikterik',
    nyeriTekanKananAtas: 'nyeri tekan perut kanan atas',
    akumulasiCairan: 'akumulasi cairan',
    perdarahanMukosa: 'perdarahan mukosa',
    petekie: 'perdarahan kulit petekie',
    letargiGelisah: 'letargi atau gelisah',
    heparLebih2: 'pembesaran hepar lebih dari 2 cm',
  }
  Object.entries(dengueNeedles).forEach(([key,needle]) => { form.dengue[key] = includesAny(items,[needle]) })
  if (includesExact(items,'Uji tourniquet positif')) form.dengue.tourniquet='positif'; else if (includesExact(items,'Uji tourniquet negatif')) form.dengue.tourniquet='negatif'
  if (includesExact(items,'NS1 positif')) form.dengue.ns1='positif'; else if (includesExact(items,'NS1 negatif')) form.dengue.ns1='negatif'
  form.dengue.hematokrit=findNumber(items,['hematokrit']); form.dengue.leukosit=findNumber(items,['leukosit']); form.dengue.trombosit=findNumber(items,['trombosit']); form.dengue.hctNaikTrombositTurun=includesAny(items,['hematokrit meningkat disertai penurunan trombosit'])
  form.telinga.cairanNanahTerlihat=includesAny(items,['terlihat cairan atau nanah keluar dari telinga']); form.telinga.bengkakNyeriBelakang=includesAny(items,['pembengkakan yang nyeri di belakang telinga'])
  if (includesExact(items,'Sangat pucat')) form.anemia.derajat='sangat_pucat'; else if (includesExact(items,'Pucat')) form.anemia.derajat='pucat'; else if (includesExact(items,'Tidak pucat')) form.anemia.derajat='tidak'
  form.anemia.lokasi=lokasiPucatOptions.filter((lokasi)=>includesAny(items,[`${lokasi} pucat`])); form.hb=findNumber(items,['hb','hemoglobin'])
  if (includesExact(items,'Status HIV ibu: positif')) form.hiv.statusIbu='positif'; else if (includesExact(items,'Status HIV ibu: negatif')) form.hiv.statusIbu='negatif'
  if (includesExact(items,'Tes virologi anak: positif')) form.hiv.virologi='positif'; else if (includesExact(items,'Tes virologi anak: negatif')) form.hiv.virologi='negatif'
  if (includesExact(items,'Tes serologi anak: positif')) form.hiv.serologi='positif'; else if (includesExact(items,'Tes serologi anak: negatif')) form.hiv.serologi='negatif'
  form.temuanLainText=items.filter((item)=>!knownFinding(item)).join('\n')
}

const dangerSignsFromSubjective = (data) => {
  const t = data?.anamnesisKhusus?.tanda_bahaya || {}
  const signs = []
  if (t.bisa_minum_menyusu === false) signs.push('Tidak bisa minum / menyusu')
  if (t.memuntahkan_semua === true) signs.push('Memuntahkan semua makanan dan minuman')
  if (t.pernah_kejang === true) signs.push('Pernah kejang selama sakit ini')
  return signs
}

const loadFromDb = async () => {
  if (!idPelayanan || loading.value) return
  loading.value = true
  message.value = ''
  try {
    const [objektifRes, subjektifRes] = await Promise.all([
      axios.get(`/simpus/kia/mtbs/objektif/${idPelayanan}`),
      axios.get(`/simpus/kia/mtbs/subjektif/${idPelayanan}`),
    ])
    applyData(objektifRes.data?.data)
    tandaBahayaSubjektif.value = dangerSignsFromSubjective(subjektifRes.data?.data)
  } catch (error) {
    console.error(error.response?.data || error)
    message.value = error.response?.data?.message || 'Gagal memuat Objektif MTBS.'
  } finally {
    loading.value = false
  }
}

const hapusDataTesting = async () => {
  if (!idPelayanan || deleting.value) return
  const yakin = window.confirm('Hapus data Subjektif, Objektif, Assessment, dan Gizi untuk kunjungan ini?\nKunjungan pasien tetap ada.')
  if (!yakin) return
  deleting.value = true; message.value = ''
  try { const res = await axios.delete(`/simpus/kia/mtbs/testing/hapus/${idPelayanan}`); window.alert(res.data?.message || 'Data testing berhasil dihapus.'); window.location.reload() }
  catch (error) { console.error(error.response?.data || error); message.value = error.response?.data?.message || 'Gagal menghapus data testing.' }
  finally { deleting.value = false }
}

const simpan = async () => {
  if (!idPelayanan || saving.value || deleting.value) return
  saving.value = true; message.value = ''
  try {
    await axios.post('/simpus/kia/mtbs/objektif/store', {
      kunjungan_id: String(idPelayanan), tandaBahaya: tandaBahayaSubjektif.value,
      saga: { penampilan: form.saga.penampilan, napas: form.saga.napas, sirkulasi: form.saga.sirkulasi },
      rr: form.rr, suhu: form.suhu, spo2: form.spo2, bb: form.bb, tb: form.tb, lila: form.lila, lk: form.lk,
      pemeriksaanKhusus: pemeriksaanKhususPayload.value, statusSAGA: statusSAGA.value,
    })
    await loadFromDb()

    window.dispatchEvent(
      new CustomEvent('mtbs:objektif-saved', {
        detail: { kunjunganId: String(idPelayanan) },
      }),
    )

    message.value = 'Objektif MTBS berhasil disimpan.'
  } catch (error) {
    console.error(error.response?.data || error)
    if (error.response?.status === 422) { const errors=error.response?.data?.errors||{}; message.value=`Validasi gagal: ${Object.values(errors).flat().join(' | ')}` }
    else message.value=error.response?.data?.message||'Gagal menyimpan Objektif MTBS.'
  } finally { saving.value = false }
}

onMounted(loadFromDb)
</script>

<style scoped>
.objective-card { font-size: 0.88rem; }
.form-section { border: 1px solid #e6eee9; border-radius: 12px; padding: 14px; background: #fbfdfc; }
.section-title { font-size: 0.92rem; font-weight: 800; color: #185c38; margin-bottom: 12px; }
.subsection-box { background: #fff; border: 1px solid #edf2ef; border-radius: 10px; padding: 11px; }
</style>
