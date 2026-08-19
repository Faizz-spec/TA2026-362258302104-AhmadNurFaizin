<template>
  <div class="card rounded-4 border-0 shadow-sm">
    <!-- Header -->
    <div
      class="card-header d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0 py-3"
      style="background: linear-gradient(135deg, #10b981, #059669);"
    >
      <h2 class="fs-6 mb-0 text-white">Daftar Surat Rujukan</h2>

      <div class="d-flex align-items-center gap-2">
        <Link
          v-if="createRoute"
          :href="createRoute"
          class="btn btn-light btn-sm border-0 fw-semibold d-flex align-items-center gap-2"
          style="background: rgba(255,255,255,.2); color: #fff;"
        >
          <i class="bi bi-plus-lg"></i> TAMBAH DATA
        </Link>

        <Link
          v-if="backRoute"
          :href="backRoute"
          class="btn btn-info btn-sm fw-semibold d-flex align-items-center gap-2"
          style="background: rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.35);"
        >
          <i class="bi bi-arrow-left"></i> KEMBALI KE DIAGNOSA
        </Link>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <!-- Optional: info pasien di atas tabel -->
      <div v-if="pasienName || noMr" class="alert alert-light border rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-3">
        <i class="bi bi-person-circle fs-4 text-success"></i>
        <div class="small">
          <div class="fw-semibold">{{ pasienName || '-' }}</div>
          <div class="text-muted">No. RM: {{ noMr || '-' }} <span v-if="nik">• NIK: {{ nik }}</span></div>
        </div>
      </div>

      <div class="table-responsive rounded-3 border">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr class="text-uppercase small text-muted">
              <th class="px-3" style="width: 56px;">No</th>
              <th>ID Rujukan</th>
              <th>Tanggal</th>
              <th>Surat Rujukan</th>
              <th>No Surat</th>
              <th>Rumah Sakit</th>
              <th>Poli</th>
              <th>Tenaga Medis</th>
              <th class="text-center" style="width: 160px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!items.length">
              <td colspan="9" class="text-center py-5 text-muted">
                Belum ada data rujukan.
                <Link v-if="createRoute" :href="createRoute" class="ms-2">
                  Tambah sekarang
                </Link>
              </td>
            </tr>

            <tr v-for="(row, idx) in items" :key="row.idRujukan || idx">
              <td class="px-3">{{ idx + 1 }}</td>
              <td class="fw-semibold">{{ row.idRujukan || '-' }}</td>
              <td>{{ formatDate(row.tanggal) }}</td>
              <td>{{ row.jenisSurat || '-' }}</td>
              <td>{{ row.noSurat || '-' }}</td>
              <td>{{ row.rumahSakit || '-' }}</td>
              <td>{{ row.poli || '-' }}</td>
              <td>{{ row.tenagaMedis || '-' }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <Link
                    v-if="row.viewRoute"
                    :href="row.viewRoute"
                    class="btn btn-outline-primary"
                    title="Lihat / Edit"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </Link>
                  <button
                    type="button"
                    class="btn btn-outline-success"
                    title="Cetak"
                    @click="onPrint(row)"
                  >
                    <i class="bi bi-printer"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-outline-danger"
                    title="Hapus"
                    @click="onDelete(row)"
                  >
                    <i class="bi bi-trash3"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer kecil / keterangan -->
      <div class="d-flex justify-content-between align-items-center mt-3 small text-muted">
        <div>Menampilkan {{ items.length }} data</div>
        <!-- tempatkan pagination kalau perlu -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

/**
 * Props yang bisa kamu kirim dari page:
 * - items: list rujukan (Array)
 *   tiap item idealnya: { idRujukan, tanggal, jenisSurat, noSurat, rumahSakit, poli, tenagaMedis, viewRoute }
 * - backRoute: string URL/route ke halaman diagnosa
 * - createRoute: string URL/route untuk tambah rujukan
 * - dataPasien: optional (Object) untuk info kecil di atas tabel (nama, RM, NIK)
 */
const props = defineProps({
  items: { type: Array, default: () => [] },
  backRoute: { type: String, default: '' },
  createRoute: { type: String, default: '' },
  dataPasien: { type: [Object, Array], default: null },
})

const pasienObj = computed(() => {
  if (Array.isArray(props.dataPasien)) return props.dataPasien[0] ?? null
  return props.dataPasien
})

const pasienName = computed(() => pasienObj.value?.NAMA_LGKP ?? pasienObj.value?.nama ?? '')
const noMr       = computed(() => pasienObj.value?.NO_MR ?? '')
const nik        = computed(() => pasienObj.value?.NIK ?? '')

function formatDate(d) {
  if (!d) return '-'
  try {
    // terima 'YYYY-MM-DD' atau ISO
    const date = new Date(d)
    if (Number.isNaN(date.getTime())) return d
    // tampilkan dd/mm/yyyy
    const pad = (n) => String(n).padStart(2, '0')
    return `${pad(date.getDate())}/${pad(date.getMonth()+1)}/${date.getFullYear()}`
  } catch {
    return d
  }
}

function onPrint(row) {
  // Sesuaikan: kalau kamu punya route cetak, bisa pakai router.get(...)
  // contoh:
  // router.get(route('rujukan.print', { id: row.idRujukan }))
  window.print()
}

function onDelete(row) {
  if (!row?.deleteRoute) {
    alert('Route hapus belum diset.')
    return
  }
  if (!confirm('Hapus data rujukan ini?')) return
  router.delete(row.deleteRoute, {
    preserveScroll: true,
    onSuccess: () => {},
  })
}
</script>

<style scoped>
.table > :not(caption) > * > * { vertical-align: middle; }
.card { background: #ffffff; }
.btn-outline-primary, .btn-outline-success, .btn-outline-danger {
  border-radius: 8px;
}
</style>
