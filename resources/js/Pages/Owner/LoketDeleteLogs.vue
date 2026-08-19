<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

// JSON endpoint (web route)
const LOG_URL = '/owner/loket-delete-logs'

const rows = ref([])
const total = ref(0)
const loading = ref(false)
const page = ref(1)
const pageSize = ref(25)

const debugState = ref({
  lastUrl: null,
  lastParams: null,
  lastStatus: null,
  lastResponseMeta: null,
  lastError: null,
})

async function fetchLogs() {
  loading.value = true
  const params = {
    page: page.value,
    page_size: pageSize.value,
  }
  debugState.value = {
    lastUrl: LOG_URL,
    lastParams: params,
    lastStatus: null,
    lastResponseMeta: null,
    lastError: null,
  }
  try {
    const resp = await axios.get(LOG_URL, { params })
    debugState.value.lastStatus = resp.status
    rows.value = resp?.data?.data || []
    total.value = resp?.data?.meta?.total ?? rows.value.length
    debugState.value.lastResponseMeta = resp?.data?.meta ?? null
  } catch (e) {
    debugState.value.lastError = {
      message: e?.message,
      status: e?.response?.status,
      data: e?.response?.data,
    }
  } finally {
    loading.value = false
  }
}

onMounted(fetchLogs)
watch(pageSize, () => { page.value = 1; fetchLogs() })
</script>

<template>
  <Head title="Owner — Log Hapus Pasien (Loket)" />
  <div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="m-0">Log Hapus Pasien (Loket)</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" @click="fetchLogs"><i class="bi bi-arrow-clockwise"></i></button>
        <Link href="/owner" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali ke Owner</Link>
      </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
      <div class="card-header p-3 rounded-4 rounded-bottom-0" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="text-white m-0">Menampilkan tanpa filter</h5>
          <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small me-2">Per halaman:</span>
            <select v-model.number="pageSize" class="form-select form-select-sm" style="width: 90px;">
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive" style="max-height: 60vh; overflow:auto;">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
              <tr>
                <th>Waktu Hapus</th>
                <th>Petugas</th>
                <th>PasienID</th>
                <th>LoketID</th>
                <th>Puskesmas</th>
                <th>Poli</th>
                <th>IP / Agent</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7">Memuat log...</td>
              </tr>
              <tr v-else-if="!rows.length">
                <td colspan="7">Belum ada data log.</td>
              </tr>
              <tr v-for="r in rows" :key="r.id">
                <td>
                  <div class="fw-semibold">{{ new Date(r.deleteDate).toLocaleString() }}</div>
                  <div class="small text-muted">Kunj: {{ r.tglKunjungan || '-' }}</div>
                </td>
                <td>
                  <div class="fw-semibold">{{ (r.deleteBy_name || r.deleteBy || '').toString().trim() || '-' }}</div>
                  <div class="small text-muted">@{{ r.deleteBy || '-' }}</div>
                </td>
                <td>{{ r.pasienId || '-' }}</td>
                <td>{{ r.loketId || '-' }}</td>
                <td>{{ r.puskesmas_nama || '-' }}</td>
                <td>{{ r.kdPoli || '-' }}</td>
                <td>
                  <div class="small">{{ r.ip_address || '-' }}</div>
                  <div class="small text-muted" :title="r.agent">{{ r.platform || r.agent || '-' }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Debug -->
        <details class="mt-3">
          <summary class="small text-muted">Debug request/response</summary>
          <pre class="small bg-light p-2 rounded border" style="max-height:240px; overflow:auto;">
{{ JSON.stringify(debugState, null, 2) }}
          </pre>
        </details>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-2">
          <div class="text-muted small">Total: {{ total }}</div>
          <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" :disabled="page<=1" @click="page--; fetchLogs()">Prev</button>
            <button class="btn btn-outline-secondary btn-sm" :disabled="(page*pageSize)>=total" @click="page++; fetchLogs()">Next</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
