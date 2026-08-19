<template>
  <div class="ss-page container-fluid py-4">
    <div class="ss-shell">
      <!-- WARNING: ID kunjungan/poli tidak valid -->
      <div v-if="!hasValidIds" class="alert alert-danger d-flex align-items-start gap-2 mb-4">
        <i class="fa fa-triangle-exclamation mt-1"></i>
        <div>
          <div class="fw-bold">Halaman ini terbuka dengan ID kunjungan tidak valid</div>
          <div class="small">
            idPoli = <code>{{ String(idPoli) }}</code>, idPelayanan = <code>{{ String(idPelayanan) }}</code>.
            Tombol kirim dinonaktifkan. Cek link/route yang mengarahkan ke halaman ini -
            biasanya ID kunjungan hilang saat navigasi dari halaman Pelayanan MTBM.
          </div>
        </div>
      </div>

      <!-- HEADER -->
      <div class="ss-hero mb-4">
        <div class="row g-4 align-items-start">
          <div class="col-12 col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
              <div class="ss-icon-wrap">
                <i class="fa fa-baby"></i>
              </div>
              <div>
                <div class="ss-eyebrow">SATU SEHAT PREVIEW</div>
                <h1 class="ss-title mb-1">Integrasi MTBM</h1>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <span class="ss-badge ss-badge-warning">
                    {{ displayStatusLayanan || 'Draft SATUSEHAT' }}
                  </span>
                  <span class="ss-badge ss-badge-soft">
                    {{ header.poli || 'Poli MTBM' }}
                  </span>
                  <span class="ss-badge" :class="statusKegawatanClass(displayStatusKegawatan)">
                    {{ displayStatusKegawatan || 'Belum dinilai' }}
                  </span>
                  <span class="ss-badge" :class="episode.ada ? 'ss-badge-success' : 'ss-badge-warning'">
                    <i class="fa fa-notes-medical me-1"></i>
                    {{ episode.ada ? 'Episode neonatus aktif' : 'Episode belum terdaftar' }}
                  </span>
                </div>
              </div>
            </div>

            <p class="ss-subtitle mb-0">
              Halaman ini menampilkan preview data MTBM (bayi muda usia 0-&lt;2 bulan) sebelum
              dipetakan ke resource SATUSEHAT, termasuk EpisodeOfCare yang wajib dikirim satu kali
              per bayi selama masa neonatus.
            </p>
          </div>

          <div class="col-12 col-lg-4">
            <div class="ss-patient-card">
              <div class="ss-patient-name">{{ displayPatientName || '-' }}</div>
              <div class="ss-patient-meta"><strong>No. RM:</strong> {{ header.no_rm || '-' }}</div>
              <div class="ss-patient-meta"><strong>NIK:</strong> {{ header.nik || '-' }}</div>
              <div class="ss-patient-meta">
                <strong>Tanggal Kunjungan:</strong> {{ formatTanggal(header.tanggal_kunjungan) }}
              </div>
              <div class="ss-patient-meta">
                <strong>Episode of Care:</strong>
                <span v-if="displayEpisodeId">{{ displayEpisodeId }}</span>
                <span v-else class="text-warning">{{ episode.label || 'Belum terdaftar' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SUMMARY -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
          <div class="ss-card h-100">
            <div class="ss-card-head"><span class="ss-card-dot"></span><span>Keluhan Utama</span></div>
            <div class="ss-card-body">
              <div v-if="displayKeluhanUtama">{{ displayKeluhanUtama }}</div>
              <div v-else class="ss-empty">Belum ada keluhan utama</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="ss-card h-100">
            <div class="ss-card-head"><span class="ss-card-dot"></span><span>Klasifikasi Global</span></div>
            <div class="ss-card-body">
              <ul v-if="displayKlasifikasiGlobal.length" class="ss-list mb-0">
                <li v-for="(item, i) in displayKlasifikasiGlobal" :key="'kla-' + i">{{ item }}</li>
              </ul>
              <div v-else class="ss-empty">Belum ada klasifikasi global</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="ss-card h-100">
            <div class="ss-card-head"><span class="ss-card-dot"></span><span>Ringkasan MTBM</span></div>
            <div class="ss-card-body">
              <div class="ss-kv"><span>Infeksi</span><strong>{{ prettyText(displayInfeksi) }}</strong></div>
              <div class="ss-kv"><span>Ikterus</span><strong>{{ prettyText(displayIkterus) }}</strong></div>
              <div class="ss-kv"><span>Diare</span><strong>{{ prettyText(displayDiare) }}</strong></div>
              <div class="ss-kv"><span>HIV</span><strong>{{ prettyText(displayHiv) }}</strong></div>
              <div class="ss-kv mb-0"><span>BB / Menyusu</span><strong>{{ prettyText(displayMenyusuBb) }}</strong></div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB NAV -->
      <div class="ss-tabs-wrap mb-3">
        <div class="ss-tabs">
          <button type="button" class="ss-tab" :class="{ active: activeTab === 'kunjungan' }" @click="activeTab = 'kunjungan'">
            Kunjungan &amp; Klasifikasi
          </button>
          <button type="button" class="ss-tab" :class="{ active: activeTab === 'observasi' }" @click="activeTab = 'observasi'">
            Observasi MTBM
          </button>
          <button type="button" class="ss-tab" :class="{ active: activeTab === 'tatalaksana' }" @click="activeTab = 'tatalaksana'">
            Tatalaksana
          </button>
          <button type="button" class="ss-tab" :class="{ active: activeTab === 'edukasi' }" @click="activeTab = 'edukasi'">
            Edukasi &amp; Kontrol
          </button>
          <button type="button" class="ss-tab" :class="{ active: activeTab === 'integrasi' }" @click="activeTab = 'integrasi'">
            Integrasi SATUSEHAT
          </button>
        </div>
      </div>

      <!-- TAB: KUNJUNGAN -->
      <div v-if="activeTab === 'kunjungan'" class="ss-section">
        <div class="ss-section-head">
          <div>
            <div class="ss-section-title">Informasi Kunjungan</div>
            <div class="ss-section-subtitle">Ringkasan encounter, episode of care, dan klasifikasi MTBM.</div>
          </div>
        </div>

        <div class="table-responsive mb-4">
          <table class="table ss-table align-middle mb-0">
            <tbody>
              <tr>
                <td width="220">Encounter ID</td>
                <td><strong :class="displayEncounterId ? '' : 'text-danger'">{{ displayEncounterId || '[BELUM TERKIRIM]' }}</strong></td>
                <td width="220">Episode of Care ID</td>
                <td><strong :class="displayEpisodeId ? '' : 'text-warning'">{{ displayEpisodeId || '[BELUM ADA]' }}</strong></td>
              </tr>
              <tr>
                <td>Practitioner</td>
                <td><strong :class="displayPractitioner ? '' : 'text-danger'">{{ displayPractitioner || '[BELUM DIPILIH]' }}</strong></td>
                <td>Status Kegawatan</td>
                <td><strong>{{ prettyText(displayStatusKegawatan) }}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Klasifikasi Inti</div>
              <div class="ss-kv"><span>Infeksi</span><strong>{{ prettyText(displayInfeksi) }}</strong></div>
              <div class="ss-kv"><span>Ikterus</span><strong>{{ prettyText(displayIkterus) }}</strong></div>
              <div class="ss-kv"><span>Diare</span><strong>{{ prettyText(displayDiare) }}</strong></div>
              <div class="ss-kv"><span>HIV</span><strong>{{ prettyText(displayHiv) }}</strong></div>
              <div class="ss-kv mb-0"><span>BB / Menyusu</span><strong>{{ prettyText(displayMenyusuBb) }}</strong></div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Keluhan &amp; Klasifikasi Global</div>
              <div class="mb-3">
                <div class="ss-label">Keluhan Utama</div>
                <div v-if="displayKeluhanUtama">{{ displayKeluhanUtama }}</div>
                <div v-else class="ss-empty">Belum ada keluhan utama</div>
              </div>
              <div>
                <div class="ss-label">Klasifikasi Global</div>
                <ul v-if="displayKlasifikasiGlobal.length" class="ss-list mb-0">
                  <li v-for="(item, i) in displayKlasifikasiGlobal" :key="'glob-' + i">{{ item }}</li>
                </ul>
                <div v-else class="ss-empty">Belum ada klasifikasi global</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: OBSERVASI -->
      <div v-if="activeTab === 'observasi'" class="ss-section">
        <div class="ss-section-head">
          <div>
            <div class="ss-section-title">Observasi MTBM</div>
            <div class="ss-section-subtitle">Vital sign dan antropometri bayi muda.</div>
          </div>
        </div>

        <div class="ss-mini-card">
          <div class="ss-mini-title">Parameter Observasi</div>
          <div class="row g-3">
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.rr ?? '-' }}</div><div class="ss-stat-label">RR / menit</div></div>
            </div>
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.suhu ?? '-' }}</div><div class="ss-stat-label">Suhu °C</div></div>
            </div>
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.spo2 ?? '-' }}</div><div class="ss-stat-label">SpO2 %</div></div>
            </div>
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.bb ?? '-' }}</div><div class="ss-stat-label">BB kg</div></div>
            </div>
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.tb_pb ?? '-' }}</div><div class="ss-stat-label">PB cm</div></div>
            </div>
            <div class="col-6 col-md-4">
              <div class="ss-stat-box"><div class="ss-stat-value">{{ preview.observasi_mtbm?.lila ?? '-' }}</div><div class="ss-stat-label">LILA cm</div></div>
            </div>
          </div>
          <div class="ss-note-text mt-3">
            <i class="fa fa-info-circle me-1"></i>
            Lingkar kepala belum dikirim - form Objektif MTBM belum punya kolom ini.
          </div>
        </div>
      </div>

      <!-- TAB: TATALAKSANA -->
      <div v-if="activeTab === 'tatalaksana'" class="ss-section">
        <div class="ss-section-head">
          <div>
            <div class="ss-section-title">Tatalaksana MTBM</div>
            <div class="ss-section-subtitle">Tindakan dan resep obat.</div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12 col-lg-5">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Tindakan</div>
              <ul v-if="tindakanItems.length" class="ss-list mb-0">
                <li v-for="(item, i) in tindakanItems" :key="'tdk-' + i">
                  {{ item.nama_ind || item.nama || item }}
                  <span v-if="item.kode" class="text-muted small">({{ item.kode }})</span>
                </li>
              </ul>
              <div v-else class="ss-empty">Belum ada tindakan</div>
            </div>
          </div>

          <div class="col-12 col-lg-7">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Resep Obat</div>
              <div v-if="resepItems.length" class="row g-3">
                <div v-for="(obat, i) in resepItems" :key="'obat-' + i" class="col-12 col-md-6">
                  <div class="ss-medicine-card">
                    <div class="ss-medicine-name">{{ obat.nama || '-' }}</div>
                    <div class="ss-medicine-item"><span>Dosis</span><strong>{{ obat.dosis || '-' }}</strong></div>
                    <div class="ss-medicine-item"><span>Cara</span><strong>{{ obat.cara || '-' }}</strong></div>
                    <div class="ss-medicine-item"><span>Lama</span><strong>{{ obat.lama || '-' }} hari</strong></div>
                  </div>
                </div>
              </div>
              <div v-else class="ss-empty">Belum ada resep</div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: EDUKASI -->
      <div v-if="activeTab === 'edukasi'" class="ss-section">
        <div class="ss-section-head">
          <div>
            <div class="ss-section-title">Edukasi &amp; Kontrol</div>
            <div class="ss-section-subtitle">Konseling/edukasi dan jadwal kunjungan ulang.</div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Konseling / Edukasi</div>
              <ul v-if="konselingItems.length" class="ss-list mb-0">
                <li v-for="(item, i) in konselingItems" :key="'edu-' + i">{{ item }}</li>
              </ul>
              <div v-else class="ss-empty">Belum ada edukasi</div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Kontrol Ulang</div>
              <div class="ss-control-days">{{ preview.edukasi_mtbm?.kontrol_ulang || '-' }}</div>
              <div class="ss-control-text">hari lagi</div>
              <hr />
              <div class="ss-label">Catatan</div>
              <div class="ss-note-text">{{ preview.edukasi_mtbm?.catatan || '-' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: INTEGRASI -->
      <div v-if="activeTab === 'integrasi'" class="ss-section">
        <div class="ss-section-head">
          <div>
            <div class="ss-section-title">Integrasi SATUSEHAT</div>
            <div class="ss-section-subtitle">Kirim data MTBM ke SATUSEHAT dan lihat hasilnya di sini.</div>
          </div>
        </div>

        <div class="table-responsive mb-3">
          <table class="table ss-table align-middle mb-0">
            <tbody>
              <tr><td width="220">Status Draft</td><td><strong>{{ displayStatusLayanan || 'Draft SATUSEHAT' }}</strong></td></tr>
              <tr><td>Episode of Care</td><td><strong>{{ displayEpisodeId || episode.label }}</strong></td></tr>
              <tr><td>Encounter ID</td><td><strong>{{ displayEncounterId || 'Belum terkirim' }}</strong></td></tr>
              <tr><td>Practitioner</td><td><strong>{{ displayPractitioner || '-' }}</strong></td></tr>
              <tr><td>Endpoint Kirim</td><td><code>{{ sendUrl }}</code></td></tr>
            </tbody>
          </table>
        </div>

        <div class="ss-alert mb-3">
          Klik tombol kirim untuk menjalankan alur SATUSEHAT MTBM lengkap: EpisodeOfCare (kalau
          belum ada) &rarr; Encounter &rarr; Observation &rarr; QuestionnaireResponse &rarr;
          Condition &rarr; Procedure &rarr; MedicationRequest &rarr; ServiceRequest.
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <button type="button" class="btn btn-outline-secondary" @click="back" :disabled="sending">Kembali</button>
          <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2" @click="sendSatusehat" :disabled="sending || !hasValidIds">
            <span v-if="sending" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <i v-else class="fa fa-paper-plane"></i>
            <span>{{ sending ? 'Mengirim...' : 'Kirim ke SATUSEHAT' }}</span>
          </button>
          <button v-if="hasResponse" type="button" class="btn btn-outline-dark" @click="resetResponse" :disabled="sending">Reset Hasil</button>
        </div>

        <div v-if="hasResponse" class="mb-3">
          <div class="ss-result-banner" :class="responseSuccess ? 'ss-result-success' : 'ss-result-danger'">
            <div class="ss-result-banner-icon"><i :class="responseSuccess ? 'fa fa-check-circle' : 'fa fa-times-circle'"></i></div>
            <div class="ss-result-banner-content">
              <div class="ss-result-banner-title">{{ responseSuccess ? 'Request berhasil diproses' : 'Request gagal diproses' }}</div>
              <div class="ss-result-banner-text">{{ responseMessage }}</div>
            </div>
          </div>
        </div>

        <div v-if="responseError" class="alert alert-danger mb-3"><strong>Error:</strong> {{ responseError }}</div>

        <div v-if="hasResponse" class="row g-3 mb-3">
          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Hasil SATUSEHAT</div>
              <div class="ss-kv"><span>Token</span><strong>{{ prettyResult(responseResult?.token) }}</strong></div>
              <div class="ss-kv"><span>Patient ID</span><strong>{{ prettyResult(responseResult?.patient_id) }}</strong></div>
              <div class="ss-kv"><span>Practitioner ID</span><strong>{{ prettyResult(responseResult?.practitioner_id) }}</strong></div>
              <div class="ss-kv"><span>Episode of Care ID</span><strong>{{ prettyResult(responseResult?.episode_of_care_id) }}</strong></div>
              <div class="ss-kv"><span>Encounter ID</span><strong>{{ prettyResult(responseResult?.encounter_id) }}</strong></div>
              <div class="ss-kv"><span>Condition IDs</span><strong>{{ prettyArray(responseResult?.condition_ids) }}</strong></div>
              <div class="ss-kv"><span>Observation IDs</span><strong>{{ prettyArray(responseResult?.observation_ids) }}</strong></div>
              <div class="ss-kv"><span>Procedure IDs</span><strong>{{ prettyArray(responseResult?.procedure_ids) }}</strong></div>
              <div class="ss-kv"><span>MedicationRequest IDs</span><strong>{{ prettyArray(responseResult?.medication_request_ids) }}</strong></div>
              <div class="ss-kv"><span>QuestionnaireResponse ID</span><strong>{{ prettyResult(responseResult?.questionnaire_response_id) }}</strong></div>
              <div class="ss-kv mb-0"><span>ServiceRequest ID</span><strong>{{ prettyResult(responseResult?.service_request_id) }}</strong></div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="ss-mini-card h-100">
              <div class="ss-mini-title">Ringkasan Lokal yang Dikirim</div>
              <div class="ss-kv"><span>Nama Pasien</span><strong>{{ responseLocalPreview?.pasien?.nama || header.nama_pasien || '-' }}</strong></div>
              <div class="ss-kv"><span>NIK Pasien</span><strong>{{ responseLocalPreview?.pasien?.nik || header.nik || '-' }}</strong></div>
              <div class="ss-kv"><span>Poli</span><strong>{{ responseLocalPreview?.kunjungan?.poli || header.poli || '-' }}</strong></div>
              <div class="ss-kv"><span>Status Kegawatan</span><strong>{{ prettyText(responseLocalPreview?.assessment?.status_kegawatan || displayStatusKegawatan) }}</strong></div>
              <div class="ss-kv mb-0"><span>Keluhan Utama</span><strong>{{ responseLocalPreview?.subjektif?.keluhan_utama || displayKeluhanUtama || '-' }}</strong></div>
            </div>
          </div>
        </div>

        <div v-if="responseLogs.length" class="ss-mini-card">
          <div class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-wrap">
            <div class="ss-mini-title mb-0">Log Proses SATUSEHAT</div>
            <div class="text-muted small">Total step: <strong>{{ responseLogs.length }}</strong></div>
          </div>

          <div class="ss-log-list">
            <div v-for="(log, index) in responseLogs" :key="'log-' + index" class="ss-log-item" :class="log.ok ? 'is-ok' : 'is-fail'">
              <div class="ss-log-top">
                <div class="ss-log-badge" :class="log.ok ? 'ok' : 'fail'"><i :class="log.ok ? 'fa fa-check' : 'fa fa-times'"></i></div>
                <div class="ss-log-main">
                  <div class="ss-log-title">{{ index + 1 }}. {{ humanizeStep(log.step || log.phase || 'step') }}</div>
                  <div class="ss-log-message">{{ log.message || log.msg || '-' }}</div>
                </div>
              </div>
              <div v-if="log.data && Object.keys(log.data).length" class="ss-log-data mt-2">
                <pre>{{ stringifyPretty(log.data) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="ss-footer-note mt-4">
        <i class="fa fa-info-circle me-2"></i>
        Data yang ditampilkan berasal langsung dari database MTBM (mtbm_subjective, mtbm_objective,
        mtbm_assessment, mtbm_planning) - bukan data dummy.
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed } from 'vue'

const props = defineProps({
  idPelayanan: [String, Number],
  idPoli: [String, Number],
  preview: {
    type: Object,
    required: true,
  },
})

const activeTab = ref('kunjungan')

/**
 * FIX: sebelumnya kalau props.idPelayanan/idPoli kosong (Vue undefined),
 * sendUrl tetap membentuk URL dengan teks literal "undefined" di dalamnya
 * (mis. /mtbm/3/undefined/send-satusehat) - backend menerima itu sebagai
 * ID kunjungan yang sah dan balik dengan error "pasien tidak ditemukan"
 * yang membingungkan (padahal akar masalahnya ID hilang, bukan data hilang).
 * Guard ini mendeteksi kondisi itu SEBELUM request dikirim.
 */
const isBadId = (value) => {
  return value === undefined || value === null || value === '' || String(value) === 'undefined' || String(value) === 'null'
}
const hasValidIds = computed(() => !isBadId(props.idPoli) && !isBadId(props.idPelayanan))

const sending = ref(false)
const responseSuccess = ref(false)
const responseMessage = ref('')
const responseError = ref('')
const responseLogs = ref([])
const responseResult = ref(null)
const responseLocalPreview = ref(null)

const header = computed(() => props.preview?.header || {})
const episode = computed(() => props.preview?.episode_of_care || { ada: false, episode_of_care_id: null, label: '' })

const hasResponse = computed(() => {
  return (
    responseMessage.value ||
    responseError.value ||
    responseLogs.value.length > 0 ||
    !!responseResult.value ||
    !!responseLocalPreview.value
  )
})

const sendUrl = computed(() => {
  const currentPath = window.location.pathname
  if (currentPath.includes('/satusehat-preview')) {
    return currentPath.replace('/satusehat-preview', '/send-satusehat')
  }
  return `/mtbm/${props.idPoli}/${props.idPelayanan}/send-satusehat`
})

const tindakanItems = computed(() => props.preview?.tatalaksana_mtbm?.tindakan_items || [])
const resepItems = computed(() => props.preview?.tatalaksana_mtbm?.resep_items || [])
const konselingItems = computed(() => props.preview?.edukasi_mtbm?.konseling_edukasi || [])

const displayPatientName = computed(() => responseLocalPreview.value?.pasien?.nama || header.value?.nama_pasien || '-')
const displayStatusLayanan = computed(() => responseLocalPreview.value?.kunjungan?.status_layanan || header.value?.status_layanan || 'Draft SATUSEHAT')

const displayKeluhanUtama = computed(() => responseLocalPreview.value?.subjektif?.keluhan_utama || props.preview?.kunjungan_mtbm?.keluhan_utama || '')

const displayKlasifikasiGlobal = computed(() => {
  const fromResponse = responseLocalPreview.value?.assessment?.klasifikasi_global
  if (Array.isArray(fromResponse) && fromResponse.length) return fromResponse
  const fromPreview = props.preview?.kunjungan_mtbm?.klasifikasi_global
  if (Array.isArray(fromPreview) && fromPreview.length) return fromPreview
  return []
})

const displayInfeksi = computed(() => responseLocalPreview.value?.assessment?.klas_infeksi || props.preview?.kunjungan_mtbm?.infeksi || null)
const displayIkterus = computed(() => responseLocalPreview.value?.assessment?.klas_ikterus || props.preview?.kunjungan_mtbm?.ikterus || null)
const displayDiare = computed(() => responseLocalPreview.value?.assessment?.klas_diare || props.preview?.kunjungan_mtbm?.diare || null)
const displayHiv = computed(() => responseLocalPreview.value?.assessment?.klas_hiv || props.preview?.kunjungan_mtbm?.hiv || null)
const displayMenyusuBb = computed(() => responseLocalPreview.value?.assessment?.klas_menyusu_bb || props.preview?.kunjungan_mtbm?.menyusu_bb || null)
const displayStatusKegawatan = computed(() => responseLocalPreview.value?.assessment?.status_kegawatan || props.preview?.kunjungan_mtbm?.status_kegawatan || null)

const displayEncounterId = computed(() => responseResult.value?.encounter_id || props.preview?.kunjungan_mtbm?.encounter_id || null)
const displayEpisodeId = computed(() => responseResult.value?.episode_of_care_id || episode.value?.episode_of_care_id || null)
const displayPractitioner = computed(() => props.preview?.kunjungan_mtbm?.practitioner || '-')

const resetResponse = () => {
  responseSuccess.value = false
  responseMessage.value = ''
  responseError.value = ''
  responseLogs.value = []
  responseResult.value = null
  responseLocalPreview.value = null
}

const sendSatusehat = async () => {
  if (sending.value) return

  resetResponse()
  activeTab.value = 'integrasi'

  if (!hasValidIds.value) {
    responseError.value = `ID kunjungan/poli tidak valid (idPoli="${props.idPoli}", idPelayanan="${props.idPelayanan}"). Halaman ini kemungkinan dibuka dari link yang salah - cek link/route yang mengarahkan ke halaman Satusehat Preview MTBM.`
    responseMessage.value = 'Request tidak dikirim - ID kunjungan belum valid.'
    return
  }

  sending.value = true

  try {
    const { data } = await axios.post(sendUrl.value, {})

    responseSuccess.value = !!data?.success
    responseMessage.value = data?.message || 'Proses selesai'
    responseLogs.value = Array.isArray(data?.logs) ? data.logs : []
    responseResult.value = data?.result || null
    responseLocalPreview.value = data?.local_preview || null
    responseError.value = data?.error || ''

    if (!data?.success && !responseError.value) {
      responseError.value = data?.message || 'Proses gagal'
    }
  } catch (error) {
    const res = error?.response?.data

    responseSuccess.value = false
    responseMessage.value = res?.message || 'Gagal menghubungi endpoint SATUSEHAT'
    responseLogs.value = Array.isArray(res?.logs) ? res.logs : []
    responseResult.value = res?.result || null
    responseLocalPreview.value = res?.local_preview || null
    responseError.value = res?.error || error?.message || 'Terjadi kesalahan saat request ke server'
  } finally {
    sending.value = false
  }
}

const formatTanggal = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  const dd = String(date.getDate()).padStart(2, '0')
  const mm = String(date.getMonth() + 1).padStart(2, '0')
  const yyyy = date.getFullYear()
  return `${dd}-${mm}-${yyyy}`
}

const prettyText = (value) => {
  if (!value) return '-'
  return String(value).replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

const prettyResult = (value) => {
  if (value === null || value === undefined || value === '') return '-'
  return String(value)
}

const prettyArray = (value) => {
  if (!Array.isArray(value) || value.length === 0) return '-'
  return value.join(', ')
}

const humanizeStep = (value) => {
  if (!value) return '-'
  return String(value).replaceAll('_', ' ').replaceAll('-', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

const stringifyPretty = (value) => {
  try {
    return JSON.stringify(value, null, 2)
  } catch {
    return String(value)
  }
}

const statusKegawatanClass = (value) => {
  const v = String(value || '').toLowerCase()
  if (v.includes('rujukan segera')) return 'ss-badge-danger'
  if (v.includes('tatalaksana')) return 'ss-badge-warning'
  if (v.includes('tidak gawat')) return 'ss-badge-success'
  return 'ss-badge-soft'
}

const back = () => {
  window.history.back()
}
</script>

<style scoped>
.ss-page {
  background:
    radial-gradient(circle at top left, rgba(16, 185, 129, 0.10), transparent 28%),
    radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24%),
    #f6f8fb;
  min-height: 100vh;
}
.ss-shell { max-width: 1400px; margin: 0 auto; }
.ss-hero {
  background: linear-gradient(135deg, #0f766e 0%, #10b981 55%, #34d399 100%);
  color: #fff; border-radius: 24px; padding: 28px;
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.18);
}
.ss-icon-wrap {
  width: 58px; height: 58px; border-radius: 18px; display: flex; align-items: center;
  justify-content: center; background: rgba(255, 255, 255, 0.18); font-size: 24px; backdrop-filter: blur(8px);
}
.ss-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 0.12em; opacity: 0.9; text-transform: uppercase; }
.ss-title { font-size: 32px; font-weight: 800; line-height: 1.1; }
.ss-subtitle { max-width: 720px; color: rgba(255, 255, 255, 0.9); font-size: 14px; line-height: 1.7; }
.ss-patient-card {
  background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 20px; padding: 18px; backdrop-filter: blur(10px);
}
.ss-patient-name { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
.ss-patient-meta { font-size: 14px; margin-bottom: 6px; color: rgba(255, 255, 255, 0.95); }
.ss-badge {
  display: inline-flex; align-items: center; justify-content: center; border-radius: 999px;
  padding: 7px 12px; font-size: 12px; font-weight: 700; border: 1px solid transparent;
}
.ss-badge-warning { background: #fef3c7; color: #92400e; }
.ss-badge-soft { background: rgba(255, 255, 255, 0.15); color: #fff; border-color: rgba(255, 255, 255, 0.18); }
.ss-badge-danger { background: #fee2e2; color: #b91c1c; }
.ss-badge-success { background: #dcfce7; color: #166534; }
.ss-card, .ss-section, .ss-tabs-wrap { background: #fff; border: 1px solid #edf1f5; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05); }
.ss-card { border-radius: 20px; overflow: hidden; }
.ss-card-head { display: flex; align-items: center; gap: 10px; padding: 16px 18px 0; font-size: 15px; font-weight: 700; color: #0f172a; }
.ss-card-dot { width: 10px; height: 10px; border-radius: 999px; background: linear-gradient(135deg, #10b981 0%, #0ea5e9 100%); flex-shrink: 0; }
.ss-card-body { padding: 16px 18px 18px; }
.ss-tabs-wrap { border-radius: 18px; padding: 10px; }
.ss-tabs { display: flex; flex-wrap: wrap; gap: 10px; }
.ss-tab { border: 0; background: #f1f5f9; color: #334155; padding: 10px 16px; border-radius: 999px; font-weight: 700; transition: 0.2s ease; }
.ss-tab:hover { background: #e2e8f0; }
.ss-tab.active { background: linear-gradient(135deg, #0f766e 0%, #10b981 100%); color: #fff; }
.ss-section { border-radius: 22px; padding: 22px; }
.ss-section-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
.ss-section-title { font-size: 22px; font-weight: 800; color: #0f172a; }
.ss-section-subtitle { color: #64748b; font-size: 14px; margin-top: 4px; }
.ss-mini-card { background: #fff; border: 1px solid #e8eef5; border-radius: 18px; padding: 18px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04); }
.ss-mini-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 14px; }
.ss-list { padding-left: 18px; }
.ss-list li { margin-bottom: 6px; color: #334155; }
.ss-empty { color: #94a3b8; font-style: italic; }
.ss-kv { display: flex; justify-content: space-between; gap: 14px; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; }
.ss-kv:last-child { border-bottom: 0; }
.ss-kv span { color: #64748b; }
.ss-kv strong { color: #0f172a; text-align: right; }
.ss-label { font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; }
.ss-stat-box { border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); height: 100%; }
.ss-stat-value { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.1; }
.ss-stat-label { font-size: 12px; color: #64748b; margin-top: 6px; }
.ss-medicine-card { border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px; background: #f8fafc; height: 100%; }
.ss-medicine-name { font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
.ss-medicine-item { display: flex; justify-content: space-between; gap: 10px; padding: 6px 0; border-bottom: 1px dashed #dbe4ee; }
.ss-medicine-item:last-child { border-bottom: 0; }
.ss-medicine-item span { color: #64748b; }
.ss-medicine-item strong { color: #0f172a; }
.ss-control-days { font-size: 42px; font-weight: 900; line-height: 1; color: #10b981; }
.ss-control-text { color: #64748b; margin-top: 6px; }
.ss-note-text { white-space: pre-line; color: #334155; }
.ss-alert { border: 1px solid #bae6fd; background: #f0f9ff; color: #0c4a6e; border-radius: 16px; padding: 14px 16px; font-size: 14px; }
.ss-result-banner { display: flex; align-items: flex-start; gap: 14px; padding: 16px 18px; border-radius: 18px; border: 1px solid transparent; }
.ss-result-success { background: #ecfdf5; border-color: #a7f3d0; }
.ss-result-danger { background: #fef2f2; border-color: #fecaca; }
.ss-result-banner-icon { font-size: 22px; line-height: 1; margin-top: 2px; }
.ss-result-success .ss-result-banner-icon { color: #059669; }
.ss-result-danger .ss-result-banner-icon { color: #dc2626; }
.ss-result-banner-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.ss-result-banner-text { color: #475569; font-size: 14px; }
.ss-log-list { display: flex; flex-direction: column; gap: 12px; }
.ss-log-item { border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px; background: #fff; }
.ss-log-item.is-ok { border-color: #bbf7d0; background: #f0fdf4; }
.ss-log-item.is-fail { border-color: #fecaca; background: #fef2f2; }
.ss-log-top { display: flex; align-items: flex-start; gap: 12px; }
.ss-log-badge { width: 34px; height: 34px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: #fff; }
.ss-log-badge.ok { background: #10b981; }
.ss-log-badge.fail { background: #ef4444; }
.ss-log-main { min-width: 0; }
.ss-log-title { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.ss-log-message { font-size: 13px; color: #475569; white-space: pre-line; }
.ss-log-data { margin-left: 46px; }
.ss-log-data pre { margin: 0; white-space: pre-wrap; word-break: break-word; font-size: 12px; line-height: 1.6; color: #0f172a; background: rgba(255, 255, 255, 0.75); border: 1px dashed #cbd5e1; border-radius: 12px; padding: 12px; }
.ss-table { border-radius: 16px; overflow: hidden; }
.ss-table tbody tr td { padding: 14px 16px; vertical-align: top; border-color: #edf2f7; }
.ss-table tbody tr td:first-child, .ss-table tbody tr td:nth-child(3) { color: #64748b; font-weight: 700; background: #f8fafc; }
.ss-footer-note { background: #fff; border: 1px dashed #cbd5e1; color: #475569; border-radius: 16px; padding: 14px 16px; }
code { color: #0f172a; background: #f8fafc; padding: 3px 8px; border-radius: 8px; word-break: break-all; }

@media (max-width: 768px) {
  .ss-hero { padding: 20px; border-radius: 20px; }
  .ss-title { font-size: 26px; }
  .ss-section { padding: 16px; }
  .ss-kv { flex-direction: column; align-items: flex-start; }
  .ss-kv strong { text-align: left; }
  .ss-log-data { margin-left: 0; }
}
</style>
