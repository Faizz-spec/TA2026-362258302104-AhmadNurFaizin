<template>
  <AppLayouts>
    <div class="card m-4 rounded-4 rounded-bottom-0 shadow-sm">
      <!-- Header -->
      <div
        class="card-header d-flex justify-content-between align-items-center p-3 rounded-4 rounded-bottom-0"
        style="background: linear-gradient(135deg, #3b82f6, #10b981);"
      >
        <h1 class="m-0 fs-5 text-white">Pelayanan Laboratorium</h1>

        <Link :href="route('dashboard')" class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
          <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
        </Link>
      </div>

      <div class="card-body">
        <!-- Filter -->
        <div class="shadow-sm rounded-5 border mb-4">
          <div
            class="card-header p-3 d-flex justify-content-between align-items-center rounded-top-5"
            style="background: linear-gradient(135deg, #e0f2fe, #dcfce7);"
          >
            <h2 class="m-0 fs-6 text-dark">Filter Data</h2>
          </div>

          <div class="card-body">
            <form @submit.prevent="TampilkanData = true">
              <!-- Kategori -> Unit -->
              <div class="mb-3 row align-items-center">
                <label class="col-md-2 col-form-label fw-semibold">Unit</label>
                <div class="col-md-10">
                  <div class="row g-2">
                    <div class="col-md-6">
                      <!-- Kategori (PUSKESMAS, POLINDES, dll) -->
                      <select class="form-control" v-model="selectedKategori">
                        <option value="">Semua Kategori</option>
                        <option v-for="k in kategoriOptions" :key="k.value" :value="k.value">
                          {{ k.label }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <!-- Nama Unit (tergantung kategori) -->
                      <select class="form-control" v-model="selectedUnit" :disabled="unitOptions.length === 0">
                        <option value="">Semua Unit</option>
                        <option v-for="u in unitOptions" :key="u.value" :value="u.value">
                          {{ u.label }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Periode per HARI -->
              <div class="mb-3 row align-items-center">
                <label class="col-md-2 col-form-label fw-semibold">Periode (Harian)</label>
                <div class="col-md-10">
                  <div class="d-flex gap-2">
                    <input
                      type="date"
                      class="form-control"
                      v-model="selectedDate"
                      :max="today"
                    />
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="setToday">
                      Hari ini
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="clearDate">
                      Hapus tanggal
                    </button>
                  </div>
                  <small class="text-muted">
                    Menyaring berdasarkan tanggal <code>tglKunjungan</code> / <code>tglDibuat</code> (format YYYY-MM-DD).
                  </small>
                </div>
              </div>

              <button class="btn btn-primary gradient-btn" type="submit">
                <i class="bi bi-search me-2"></i>Tampilkan Data
              </button>
            </form>
          </div>
        </div>

        <!-- Tabel -->
        <div class="card mt-4 rounded-4 overflow-hidden" v-if="TampilkanData">
          <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <form class="w-100" style="max-width: 320px;">
              <input v-model="q" type="search" class="form-control" placeholder="Cari nama / MR / alamat..." />
            </form>
            <div class="small text-muted">
              Ditampilkan: <strong>{{ filteredRows.length }}</strong> dari {{ DataPasien.length }} data
              <span v-if="selectedDate"> • Tanggal: <strong>{{ selectedDate }}</strong></span>
            </div>
          </div>

          <div class="card-body p-0">
            <table class="table table-bordered mb-0 align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th style="width:56px;">NO</th>
                  <th>Nama Pasien</th>
                  <th>Alamat</th>
                  <th>Kategori</th>
                  <th>Nama Unit</th>
                  <th>Nama Poli</th>
                  <th>Alasan dirujuk</th>
                  <th>Status</th>
                  <th style="width:140px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in filteredRows"
                  :key="`${item.idLoket}-${index}`"
                  class="table-hover-row"
                >
                  <td class="text-center">{{ index + 1 }}</td>
                  <td>
                    <div class="fw-semibold">{{ item.NAMA_LGKP || '(tanpa nama)' }}</div>
                    <div class="small text-muted">
                      MR: {{ item.NO_MR || '-' }} • {{ toDateOnly(item.tglKunjungan) || '-' }}
                    </div>
                  </td>
                  <td>{{ formatAlamat(item) }}</td>
                  <td>{{ item.kategori || '-' }}</td>
                  <td>{{ item.nama_unit || '-' }}</td>
                  <td>{{ item.nmPoli || '-' }}</td>
                  <td>{{ item.alasan || '-' }}</td>
<td class="text-center">
  <span
    class="badge"
    :class="
      String(item.statusLayanan).trim() === '1'
        ? 'bg-success'
        : String(item.statusLayanan).trim() === '2'
          ? 'bg-warning text-dark'
          : 'bg-secondary'
    "
  >
    {{
      String(item.statusLayanan).trim() === '1'
        ? 'Selesai'
        : String(item.statusLayanan).trim() === '2'
          ? 'Proses'
          : 'Belum dilayani'
    }}
  </span>
</td>

                  <td class="text-center">
                    <Link
                      class="btn btn-success btn-sm"
                      :href="route('ruang-layanan.laborat.pemeriksaan', { loketId: item.idLoket })"
                    >
                      Detail
                    </Link>
                  </td>
                </tr>

                <tr v-if="filteredRows.length === 0">
                  <td colspan="9" class="text-center py-4 text-muted">Tidak ada data untuk ditampilkan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- DEBUG -->
        <!-- <div class="mt-4">
          <h6 class="fw-semibold text-primary mb-2">DEBUG OUTPUT</h6>
          <pre class="bg-light p-3 small rounded border">{{ pretty(Debug) }}</pre>
        </div> -->
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue'

const page = usePage()

// === Props dari controller ===
const DataPasien     = computed(() => page.props.DataPasien || [])
const FilterKategori = computed(() => page.props.FilterKategori || [])
const FilterUnits    = computed(() => page.props.FilterUnits || [])
const Debug          = computed(() => page.props.Debug || {})

// === UI state ===
const TampilkanData = ref(true)
const q = ref('')

// === Filter state ===
// === Filter state ===
const selectedKategori = ref('')
const selectedUnit     = ref('')
const selectedDate     = ref('') // YYYY-MM-DD

// === Today helper ===
const today = computed(() => {
  const d = new Date()
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  return `${d.getFullYear()}-${mm}-${dd}`
})

// 🔥 Tambahkan baris ini biar default langsung hari ini:
selectedDate.value = today.value

function setToday() { selectedDate.value = today.value }
function clearDate() { selectedDate.value = '' }

// === Dropdown options ===
const kategoriOptions = computed(() =>
  FilterKategori.value.map(k => ({
    value: String(k.id_kategori),
    label: k.kategori || `(Kategori ${k.id_kategori})`,
  }))
)

const unitOptions = computed(() => {
  const idKat = selectedKategori.value
  const list = idKat
    ? FilterUnits.value.filter(u => String(u.id_kategori) === String(idKat))
    : FilterUnits.value
  // Controller mengirim (id_kategori, kategori, id_unit, nama_unit)
  return list.map(u => ({
    value: String(u.id_unit),
    label: u.nama_unit || `(Unit ${u.id_unit})`,
  }))
})

// === Helpers tanggal & alamat ===
function toDateOnly(t) {
  if (!t) return ''
  // handle "YYYY-MM-DD hh:mm:ss" atau Date
  if (typeof t === 'string') return t.slice(0, 10)
  try {
    const d = new Date(t)
    if (isNaN(d)) return ''
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    return `${d.getFullYear()}-${mm}-${dd}`
  } catch { return '' }
}

function formatAlamat(it) {
  return [it.alamat, it.no_rt ? `RT ${it.no_rt}` : null, it.no_rw ? `RW ${it.no_rw}` : null]
    .filter(Boolean)
    .join(', ')
}

// === Tabel: filter client-side ===
const filteredRows = computed(() => {
  let rows = [...DataPasien.value]

  // --- FILTER KATEGORI ---
  if (selectedKategori.value) {
    const katId = String(selectedKategori.value)
    rows = rows.filter(r => String(r?.id_kategori ?? '') === katId)
  }

  // --- FILTER UNIT ---
  if (selectedUnit.value) {
    const uid = String(selectedUnit.value)
    rows = rows.filter(r => String(r?.unit_id ?? '') === uid)
  }

  // --- FILTER PER TANGGAL (HARIAN) ---
  if (selectedDate.value) {
    rows = rows.filter(r => toDateOnly(r.tglKunjungan) === selectedDate.value)
  }

  // --- SEARCH ---
  const s = q.value.toLowerCase().trim()
  if (!s) return rows

  return rows.filter(it =>
    [it.NAMA_LGKP, it.NO_MR, it.alamat, it.nmPoli, it.alasan, it.nama_unit, it.kategori]
      .map(v => (v ? String(v).toLowerCase() : ''))
      .some(v => v.includes(s))
  )
})

// pretty debug
function pretty(obj) {
  try { return JSON.stringify(obj, null, 2) } catch { return obj }
}
</script>

<style scoped>
.gradient-btn {
  background: linear-gradient(135deg, #3b82f6, #10b981) !important;
  border: none !important;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.gradient-btn:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(16,185,129,.15); }
.table-hover-row:hover { background-color: #f8fafc; }
.card-header { border: none; }
.table th, .table td { vertical-align: middle; }
.table { --bs-table-bg: transparent; }
</style>
