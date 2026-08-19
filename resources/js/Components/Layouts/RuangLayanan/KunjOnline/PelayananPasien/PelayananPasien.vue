<template>
  <div class="p-2 rounded-4 mt-2">
    <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
      <!-- Header Section -->
      <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle px-2 me-3">
          <i class="bi bi-person-fill text-primary fs-1"></i>
        </div>
        <div>
          <h3 class="mb-1 fw-bold">
            {{ p.NAMA_LGKP || p.nama_lgkp || p.nama || '-' }}
            <span class="text-muted">(-)</span>
          </h3>
        </div>
      </div>

      <div class="row g-3">
        <!-- Column 1 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar3 text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Jk / Umur</h6>
            </div>
            <p class="mb-0 fw-semibold">
              {{ jkText }} {{ umurTahun }} Tahun - {{ p.umur_bulan ?? 0 }} Bulan - {{ p.umur_hari ?? 0 }} Hari
            </p>
          </div>
        </div>

        <!-- Column 2 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-geo-alt-fill text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Alamat</h6>
            </div>
            <p class="mb-0 fw-semibold">
              {{ (p.ALAMAT || p.alamat || '-') }}
              {{ ' RT ' + (p.NO_RT ?? p.no_rt ?? '-') }}
              {{ ' RW ' + (p.NO_RW ?? p.no_rw ?? '-') }}
              {{ (p.nama_kel ?? p.NAMA_KEL ?? '') }}
              {{ (p.nama_kec ?? p.NAMA_KEC ?? '') }}
              {{ (p.nama_kab ?? p.NAMA_KAB ?? '') }}
              {{ (p.nama_prop ?? p.NAMA_PROP ?? '') }}
            </p>
          </div>
        </div>

        <!-- Column 3 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-heart-pulse-fill text-success me-2"></i>
              <h6 class="mb-0 text-muted">Jenis/Poli</h6>
            </div>
            <p class="mb-0 fw-semibold">
              Kunjungan Sakit ({{ p.nmPoli || 'Kunjungan Online' }})
            </p>
          </div>
        </div>

        <!-- Column 4 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar-check-fill text-info me-2"></i>
              <h6 class="mb-0 text-muted">Tanggal Kunjungan</h6>
            </div>
            <p class="mb-0 fw-semibold">{{ p.tgl_kunjungan || p.tglKunjungan || '-' }}</p>
          </div>
        </div>

        <!-- Column 5 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-credit-card-fill text-warning me-2"></i>
              <h6 class="mb-0 text-muted">No. RM / NIK</h6>
            </div>
            <p class="mb-0 fw-semibold">
              {{ p.no_rm || p.NO_MR || p.no_mr || '-' }} / {{ p.NIK || p.nik || '-' }}
            </p>
          </div>
        </div>

        <!-- Column 6 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-credit-card-fill text-warning me-2"></i>
              <h6 class="mb-0 text-muted">No. BPJS/Provider</h6>
            </div>
            <p class="mb-0 fw-semibold">
              {{ (p.no_bpjs ?? p.noKartu ?? '-') }} / {{ (p.provider ?? p.kdProvider ?? '-') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="quick-actions mb-2">
      <div class="action-grid">
        <Link :href="routeOr('#','ruang-layanan.kunjungan-online.surat-rujukan', p.idLoket)" class="action-card doc-action">
          <div class="action-icon"><i class="bi bi-send"></i></div>
          <div class="action-label">Surat Rujukan</div>
        </Link>

        <Link :href="routeOr('#','ruang-layanan.kunjungan-online.riwayat', p.idLoket)" class="action-card history-action">
          <div class="action-icon"><i class="bi bi-clock-history"></i></div>
          <div class="action-label">Riwayat Pasien</div>
        </Link>

        <Link :href="routeOr('#','ruang-layanan.kunjungan-online.cppt', p.idLoket)" class="action-card medical-action">
          <div class="action-icon"><i class="bi bi-file-text"></i></div>
          <div class="action-label">CPPT</div>
        </Link>

        <button v-if="!isMelayani"
                @click.prevent="mulaiPemeriksaanPasien"
                class="action-card start-action pulse-animation">
          <div class="action-icon"><i class="bi bi-person-check"></i></div>
          <div class="action-label">Mulai Pemeriksaan</div>
        </button>
      </div>
    </div>

    <div v-if="isMelayani" class="mt-4 p-2">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  isMelayani: { type: Boolean, default: false },
  dataPasien: { type: Array, default: () => [] },    // <- array
  dataAnamnesa: { type: Array, default: () => [] }, // <- array
})

// Ambil item pasien pertama (controller kirim 1 row dalam array)
const p = computed(() => props.dataPasien?.[0] ?? {})

// JK & umur
const jkText = computed(() => {
  if (p.value?.jk) return p.value.jk
  const v = p.value?.jenis_klmin ?? p.value?.JENIS_KLMIN
  if (v === 1 || v === '1') return 'L'
  if (v === 2 || v === '2') return 'P'
  return '-'
})
const umurTahun = computed(() => p.value?.umur_tahun ?? p.value?.umur ?? 0)

// state melayani (true kalau sudah ada anamnesa)
const isMelayani = ref((props.dataAnamnesa?.length ?? 0) > 0)

const emit = defineEmits(['ubah-melayani', 'ubah-dataAnamnesa'])

watch(isMelayani, (val) => emit('ubah-melayani', val))
watch(() => props.isMelayani, (val) => { isMelayani.value = val })

// helper ziggy aman (kalau route name belum didaftarkan)
function routeOr(fallback, name, param) {
  try { return route(name, param) } catch { return fallback }
}

function mulaiPemeriksaanPasien () {
  const now = new Date()
  const formattedDateTime = now.toISOString().slice(0, 16)

  router.post(
    routeOr('#', 'ruang-layanan.kunjungan-online.mulai-pemeriksaan-pasien'),
    {
      idLoket: p.value?.idLoket || '',
      tglKunjungan: formattedDateTime,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isMelayani.value = true
        emit('ubah-melayani', true)
        alert('Sukses memulai melayani pasien')
      },
    }
  )
}
</script>

<style>
.action-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px}
.action-card{background:#fff;border-radius:12px;padding:15px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;text-align:center;transition:.3s;box-shadow:0 4px 6px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.05);cursor:pointer;text-decoration:none}
.action-card:hover{transform:translateY(-5px);box-shadow:0 10px 20px rgba(0,0,0,.1)}
.action-icon{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff}
.doc-action .action-icon{background:linear-gradient(135deg,#4361ee 0%,#3a0ca3 100%)}
.history-action .action-icon{background:linear-gradient(135deg,#4cc9f0 0%,#4895ef 100%)}
.medical-action .action-icon{background:linear-gradient(135deg,#f72585 0%,#b5179e 100%)}
.start-action .action-icon{background:linear-gradient(135deg,#f8961e 0%,#f3722c 100%)}
.start-action{border:2px dashed rgba(248,150,30,.5);background:rgba(248,150,30,.1)}
.pulse-animation{animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(248,150,30,.4)}70%{box-shadow:0 0 0 10px rgba(248,150,30,0)}100%{box-shadow:0 0 0 0 rgba(248,150,30,0)}}
</style>
