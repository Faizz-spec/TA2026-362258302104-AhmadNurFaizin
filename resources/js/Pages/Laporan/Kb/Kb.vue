<template>
  <AppLayout title="Laporan KB">
    <div class="container py-4" style="font-family: 'Segoe UI', sans-serif;">
      <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(135deg, #0d6efd, #20c997);">
          Filter Data Laporan KB
        </div>

        <div class="card-body">
          <form @submit.prevent="submit">
            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.pusk_id" @change="loadSub">
                  <option value="" disabled>Pilih Puskesmas</option>
                  <option v-for="p in dropdowns.puskesmas" :key="p.id" :value="p.id">{{ p.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Laporan (statis) -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Laporan</label>
              <div class="col-md-6">
                <select class="form-select" disabled>
                  <option>- Register KB -</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Awal</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.awal" />
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Akhir</label>
              <div class="col-md-6">
                <input type="date" class="form-control" v-model="form.akhir" />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.unit_id" @change="loadSub">
                  <option value="">- Pilih -</option>
                  <option v-for="u in dropdowns.units" :key="u.id" :value="u.id">{{ u.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Sub Unit -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Sub Unit</label>
              <div class="col-md-6">
                <select class="form-select" v-model="form.sub_id">
                  <option value="">- Pilih -</option>
                  <option v-for="s in subunits" :key="s.id" :value="s.id">{{ s.nama }}</option>
                </select>
              </div>
            </div>

            <!-- Tombol -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <button type="submit" class="btn btn-gradient text-white border-0 px-4 py-2 fw-semibold rounded">
                  <i class="bi bi-printer me-1"></i> Tampilkan Data
                </button>

                <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded"
                        @click="downloadExcel" :disabled="!hasResult">
                  <i class="bi bi-download me-1"></i> Download Excel
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- Tidak ada modal lagi. Hasil akan tampil di tab baru. -->
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'

const page = usePage()

// props dari server
const dropdowns = computed(() => page.props.dropdowns || {})
const initialFilters = computed(() => page.props.filters || {})
const results = computed(() => page.props.results || [])

// form
const form = useForm({
  pusk_id: initialFilters.value.pusk_id || '',
  awal:    initialFilters.value.awal    || '',
  akhir:   initialFilters.value.akhir   || '',
  unit_id: initialFilters.value.unit_id || '',
  sub_id:  initialFilters.value.sub_id  || '',
})

// subunits ikut dari props (server side refresh via GET ringan)
const subunits = computed(() => dropdowns.value.subunits || [])

// (legacy) modal state dihilangkan, tapi tetap ada agar kompatibel dengan kode lama
const modalOpen = ref(false)
watch(results, (val) => { modalOpen.value = false })

const d = (iso) => iso ? new Date(iso + 'T00:00:00').toLocaleDateString('id-ID') : ''

const puskName = computed(() => {
  const list = dropdowns.value.puskesmas || []
  const p = list.find(x => String(x.id) === String(form.pusk_id))
  return p ? p.nama : ''
})
const unitName = computed(() => {
  const list = dropdowns.value.units || []
  const u = list.find(x => String(x.id) === String(form.unit_id))
  return u ? u.nama : 'SEMUA UNIT'
})
const subUnitName = computed(() => {
  const s = subunits.value.find(x => String(x.id) === String(form.sub_id))
  return s ? s.nama : 'REKAP GABUNGAN'
})
const formatRange = computed(() => `${d(form.awal)} - ${d(form.akhir)}`)
const hasResult = computed(() => results.value.length > 0)

// === Tab baru: render HTML dari data results yang sudah ada di props ===
const openPreviewWindow = () => {
  const r = results.value || []
  const html = `
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>REGISTER RAWAT JALAN KB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * { box-sizing: border-box; font-family: Segoe UI,Arial,Helvetica,sans-serif; }
    body { margin: 0; padding: 24px; background: #f6f8fb; }
    .toolbar { display:flex; gap:8px; margin: -8px 0 12px; }
    .toolbar button { padding:8px 12px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer; }
    .card { background:#fff; padding:16px; border-radius:12px; box-shadow: 0 6px 24px rgba(0,0,0,.08); }
    h3 { margin: 0 0 8px; }
    .meta { font-size: 12px; color:#444; margin-bottom:10px; }
    .table-wrap { overflow:auto; max-height: calc(100vh - 190px); border:1px solid #e6eef6; border-radius:10px; background:#fff; }
    table { width:100%; border-collapse: collapse; }
    thead th { position: sticky; top: 0; background:#e8f4ff; border-bottom:1px solid #cfe6ff; }
    th, td { border: 1px solid #e8eef3; padding: 6px 8px; font-size: 12px; vertical-align: top; }
    tfoot td { border:none; padding-top:12px; }
    @media print {
      body { background:#fff; padding:0; }
      .toolbar { display:none; }
      .card { box-shadow:none; border:none; }
      .table-wrap { max-height: unset; border:none; }
      thead th { position: static; }
    }
  </style>
</head>
<body>
  <div class="toolbar">
    <button onclick="window.print()">🖨️ Cetak</button>
    <button onclick="window.close()">✕ Tutup</button>
  </div>

  <div class="card">
    <h3>REGISTER RAWAT JALAN KB</h3>
    <div class="meta">
      <div>Unit: <strong>${unitName.value}</strong></div>
      <div>Nama Unit: <strong>${subUnitName.value}</strong></div>
      <div>Tanggal: <strong>${formatRange.value}</strong></div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Pasien</th>
            <th>Nama</th>
            <th>Umur</th>
            <th>Sex</th>
            <th>Alamat</th>
            <th>Anamnesa</th>
            <th>Diagnosa</th>
            <th>Tindakan</th>
            <th>Obat</th>
            <th>Kategori</th>
            <th>Kasus</th>
            <th>Status Pulang</th>
            <th>Rujuk</th>
          </tr>
        </thead>
        <tbody>
          ${r.map((x, i) => `
            <tr>
              <td>${i+1}</td>
              <td>${x?.tanggal ? new Date(x.tanggal + 'T00:00:00').toLocaleDateString('id-ID') : ''}</td>
              <td>
                <div>No MR : ${x?.no_mr ?? ''}</div>
                <div>No NIK : ${x?.nik ?? ''}</div>
              </td>
              <td>${x?.nama ?? ''}</td>
              <td>${x?.umur ?? ''}</td>
              <td>${x?.sex ?? ''}</td>
              <td>${x?.alamat ?? ''}</td>
              <td class="small">
                <div>Kesadaran : </div>
                <div>Sistole : ${x?.sistole ?? ''}</div>
                <div>Diastole : ${x?.diastole ?? ''}</div>
                <div>RR : ${x?.rr ?? ''} · HR : ${x?.hr ?? ''}</div>
                <div>Keluhan : ${x?.keluhan ?? ''}</div>
                <div>Keluhan tambahan : ${x?.keluhanTambahan ?? ''}</div>
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td>${x?.kategori ?? ''}</td>
              <td>${x?.kasus ?? ''}</td>
              <td>${x?.statusPulang ?? ''}</td>
              <td>${x?.rujukKe ?? ''}</td>
            </tr>
          `).join('')}
        </tbody>
        <tfoot>
          <tr><td colspan="15" style="text-align:right">
            <em>Mengetahui<br/>Kepala PUSKESMAS ${puskName.value}</em>
          </td></tr>
        </tfoot>
      </table>
    </div>
  </div>
</body>
</html>
  `.trim()

  const w = window.open('', '_blank')
  if (!w) {
    alert('Popup terblokir. Izinkan pop-up untuk situs ini agar bisa membuka halaman preview.')
    return
  }
  w.document.open()
  w.document.write(html)
  w.document.close()
}

// === Submit: tetap POST ke route lama, lalu render tab baru ===
const submit = () => {
  router.post(route('laporan.kb'), form, {
    preserveScroll: true,
    preserveState: false, // ambil props results yang fresh
    onSuccess: () => {
      openPreviewWindow()
      modalOpen.value = false
    }
  })
}

// === Reload subunits via partial reload (tanpa ganti route) ===
const loadSub = () => {
  form.sub_id = ''
  router.get(route('laporan.kb'), {
    pusk_id: form.pusk_id || '',
    awal: form.awal || '',
    akhir: form.akhir || '',
    unit_id: form.unit_id || '',
    sub_id: ''
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    only: ['dropdowns','filters'],
    onSuccess: () => { modalOpen.value = false }
  })
}

const downloadExcel = () => {
  if (!hasResult.value) return
  const params = new URLSearchParams(form.data()).toString()
  window.open(route('laporan.kb') + '?' + params + '&export=1', '_blank')
}
</script>

<style scoped>
.btn-gradient{background:linear-gradient(135deg,#0d6efd,#20c997)}
</style>
