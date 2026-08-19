<template>
  <div v-if="show" class="modal-backdrop-custom" @click.self="emitClose">
    <div class="modal-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="m-0 fw-semibold">Pilih Tindakan</h6>
          <div class="text-muted small">Cari dan pilih tindakan untuk dimasukkan ke form.</div>
        </div>
        <button class="btn btn-sm btn-outline-secondary" @click="emitClose">
          Tutup
        </button>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-7">
          <input
            type="text"
            class="form-control"
            placeholder="Cari kode / nama tindakan..."
            v-model="q"
          />
        </div>
        <div class="col-md-5">
          <select class="form-select" v-model="filterPoli">
            <option value="">Semua Poli</option>
            <option v-for="p in daftarPoli" :key="p" :value="p">{{ p }}</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 160px;">Kode</th>
              <th>Nama</th>
              <th style="width: 140px;">Poli</th>
              <th style="width: 110px;">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(t, i) in filteredTindakan" :key="i">
              <td class="text-nowrap">{{ t.kdTindakan ?? t.kode ?? '-' }}</td>
              <td>
                <div class="fw-semibold">{{ t.nmTindakan ?? t.nama ?? '-' }}</div>
                <div class="text-muted small">
                  {{ t.nmTindakanInd ?? t.namaInd ?? '' }}
                </div>
              </td>
              <td>{{ t.poli ?? t.nmPoli ?? '-' }}</td>
              <td>
                <button class="btn btn-success btn-sm w-100" @click="selectItem(t)">
                  Pilih
                </button>
              </td>
            </tr>

            <tr v-if="!filteredTindakan.length">
              <td colspan="4" class="text-center text-muted py-4">
                Tidak ada data tindakan yang cocok.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-2">
        <div class="text-muted small">
          Total: <b>{{ filteredTindakan.length }}</b> dari <b>{{ tindakan.length }}</b>
        </div>
        <button class="btn btn-sm btn-outline-danger" @click="emitClose">Tutup</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  tindakan: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'select'])

const q = ref('')
const filterPoli = ref('')

watch(
  () => props.show,
  (val) => {
    if (val) {
      q.value = ''
      filterPoli.value = ''
    }
  }
)

const daftarPoli = computed(() => {
  const set = new Set()
  for (const t of props.tindakan || []) {
    const p = t?.poli ?? t?.nmPoli
    if (p) set.add(p)
  }
  return Array.from(set).sort()
})

const filteredTindakan = computed(() => {
  const list = props.tindakan || []
  const keyword = (q.value || '').trim().toLowerCase()

  return list.filter((t) => {
    const kode = String(t?.kdTindakan ?? t?.kode ?? '').toLowerCase()
    const nama = String(t?.nmTindakan ?? t?.nama ?? '').toLowerCase()
    const poli = String(t?.poli ?? t?.nmPoli ?? '').toLowerCase()

    const matchKeyword = !keyword || kode.includes(keyword) || nama.includes(keyword)
    const matchPoli = !filterPoli.value || (t?.poli ?? t?.nmPoli) === filterPoli.value

    return matchKeyword && matchPoli
  })
})

const emitClose = () => emit('close')
const selectItem = (item) => emit('select', item)
</script>

<style scoped>
.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal-card {
  width: min(1000px, 95vw);
  max-height: 85vh;
  overflow: auto;
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
</style>
