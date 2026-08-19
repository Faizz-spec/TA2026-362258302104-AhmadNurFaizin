<template>
  <div class="print-wrap">
    <!-- Header -->
    <div class="head">
      <h3 class="title">REGISTER RAWAT JALAN UGD</h3>
      <div class="meta">
        <div><b>Unit</b> : {{ unitLabel }}</div>
        <div><b>Nama Unit</b> : {{ unitName }}</div>
        <div><b>Puskesmas</b> : {{ puskName }}</div>
        <div><b>Tanggal</b> : {{ rangeTanggal }}</div>
      </div>
    </div>

    <!-- Tabel data -->
    <table class="table">
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
        <tr v-if="!rows.length">
          <td colspan="15" class="empty">Tidak ada data pada rentang/filter ini.</td>
        </tr>

        <tr v-for="(r, i) in rows" :key="i">
          <td class="center">{{ i + 1 }}</td>
          <td class="center">{{ fmtDate(r.tanggal) }}</td>
          <td class="center">{{ r.no_mr ?? '-' }}</td>
          <td>{{ r.nama_pasien || '-' }}</td>
          <td class="center">{{ r.umur ?? '-' }}</td>
          <td class="center">{{ r.sex || '-' }}</td>
          <td>{{ r.alamat || '-' }}</td>
          <td>{{ r.keluhan_utama || '-' }}</td>
          <td>{{ r.diagnosis || '-' }}</td>
          <td>-</td>
          <td>-</td>
          <td class="center">{{ r.kategori_bayar || '-' }}</td>
          <td class="center">{{ r.kasus || '-' }}</td>
          <td class="center">{{ r.status_pulang || '-' }}</td>
          <td>-</td>
        </tr>
      </tbody>
    </table>

    <!-- Footer tanda tangan -->
    <div class="ttd">
      <div>Banyuwangi, {{ todayId }}</div>
      <div>Mengetahui</div>
      <div><b>Kepala {{ puskName }}</b></div>
      <div style="height: 56px"></div>
      <div><b>NS.H.M.Shadiq, S.Kep.,M.MKes</b></div>
      <div>NIP. 19641110 198502 1 002</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// props dari controller
const p = usePage()
const rows   = computed(() => p.props.rows ?? [])
const filt   = computed(() => p.props.filters ?? {})
const units  = computed(() => p.props.unitOptions ?? [])
const puskes = computed(() => p.props.puskesmasOptions ?? [])

// header text
const puskName = computed(() => {
  const id = String(filt.value.pusk_id ?? '')
  const found = puskes.value.find(x => String(x.id) === id)
  return found ? found.nama : 'SEMUA PUSKESMAS'
})
const unitLabel = computed(() => (filt.value.unit_id ? 'UNIT TERPILIH' : 'SEMUA UNIT'))
const unitName = computed(() => {
  if (!filt.value.unit_id) return 'REKAP GABUNGAN'
  const found = units.value.find(x => String(x.id) === String(filt.value.unit_id))
  return found ? found.nama : 'REKAP GABUNGAN'
})

const rangeTanggal = computed(() => {
  const a = filt.value.awal  || new Date().toISOString().slice(0,10)
  const b = filt.value.akhir || a
  return `${fmtDate(a)} - ${fmtDate(b)}`
})

const todayId = computed(() => {
  const d = new Date()
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
})

// util format
function fmtDate(yyyyMmDd) {
  if (!yyyyMmDd) return '-'
  const [y,m,d] = String(yyyyMmDd).split('-')
  if (!y || !m || !d) return yyyyMmDd
  return `${d}/${m}/${y}`
}

// otomatis buka dialog print (opsional, boleh dihapus kalau tidak mau)
setTimeout(() => {
  try { window.print() } catch (e) {}
}, 300)
</script>

<style scoped>
.print-wrap { font-family: "Segoe UI", system-ui, sans-serif; color:#111; padding: 12px; }
.head { margin-bottom: 10px; }
.title { margin: 0 0 6px 0; text-align: center; text-transform: uppercase; }
.meta { display:grid; grid-template-columns: 1fr 1fr; gap: 2px 16px; font-size: 12px; margin-bottom: 8px; }
.table { width:100%; border-collapse: collapse; font-size: 12px; }
.table th, .table td { border:1px solid #000; padding: 4px 6px; vertical-align: top; }
.table thead th { text-align:center; font-weight:700; }
.center { text-align:center; }
.empty { text-align:center; color:#666; height: 48px; }
.ttd { width: 280px; margin-left:auto; margin-top: 18px; font-size: 12px; text-align:center; }
@media print {
  .print-wrap { padding:0 }
  .table th, .table td { padding: 3px 4px; }
}
</style>
