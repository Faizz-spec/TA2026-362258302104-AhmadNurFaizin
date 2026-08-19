<template>
  <AppLayout title="Template Components">
    <div class="container py-4" style="font-family: 'Segoe UI', sans-serif;">

      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold text-white px-3 py-2 rounded mb-3" style="background-color: #0d6efd;">
            Filter Data Laporan Laboratorium
          </h5>

          <form>
            <!-- ====== O D O N T O G R A M ====== -->
            <div class="mb-4">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="m-0 fw-semibold">Odontogram</h6>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearSelection">
                    Reset
                  </button>
                </div>
              </div>

              <div class="odontogram-frame">
                <!-- Row 1: 18..28 (atas permanen) -->
                <div class="row-odontogram top-row">
                  <div class="teeth-line">
                    <Tooth v-for="n in row1" :key="'t-'+n" :num="n" :active="selected.has(n)" @toggle="toggle(n)" />
                  </div>
                </div>

                <!-- Row 2: 55..65 (atas sulung) -->
                <div class="row-odontogram mid-row">
                  <div class="teeth-line short">
                    <Tooth v-for="n in row2" :key="'t-'+n" :num="n" :active="selected.has(n)" @toggle="toggle(n)" />
                  </div>
                </div>

                <!-- Row 3: 85..75 (bawah sulung) -->
                <div class="row-odontogram mid-row">
                  <div class="teeth-line short">
                    <Tooth v-for="n in row3" :key="'t-'+n" :num="n" :active="selected.has(n)" @toggle="toggle(n)" />
                  </div>
                </div>

                <!-- Row 4: 48..38 (bawah permanen) -->
                <div class="row-odontogram bottom-row">
                  <div class="teeth-line">
                    <Tooth v-for="n in row4" :key="'t-'+n" :num="n" :active="selected.has(n)" @toggle="toggle(n)" />
                  </div>
                </div>
              </div>

              <div class="small text-muted mt-2">
                Dipilih: <span v-if="selectedArray.length">{{ selectedArray.join(', ') }}</span><span v-else>-</span>
              </div>
            </div>
            <!-- ====== /ODONTOGRAM ====== -->

            <!-- Puskesmas -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Puskesmas</label>
              <div class="col-md-6">
                <select class="form-select">
                  <option>WONGSOREJO</option>
                </select>
              </div>
            </div>

            <!-- Laporan -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Laporan</label>
              <div class="col-md-6">
                <select class="form-select">
                  <option>- Pilih -</option>
                </select>
              </div>
            </div>

            <!-- Tgl Awal -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Awal</label>
              <div class="col-md-6">
                <input type="date" class="form-control" />
              </div>
            </div>

            <!-- Tgl Akhir -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Tgl Akhir</label>
              <div class="col-md-6">
                <input type="date" class="form-control" />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3">
              <label class="col-md-2 col-form-label fw-semibold">Unit</label>
              <div class="col-md-6">
                <select class="form-select">
                  <option>- Pilih -</option>
                </select>
              </div>
            </div>

            <!-- Sub Unit -->
            <div class="row mb-4">
              <label class="col-md-2 col-form-label fw-semibold">Sub Unit</label>
              <div class="col-md-6">
                <select class="form-select">
                  <option>- Pilih -</option>
                </select>
              </div>
            </div>

            <!-- Tombol -->
            <div class="row">
              <div class="col-md-8 offset-md-2 d-flex flex-wrap gap-3">
                <button type="button" class="btn text-white border-0 px-4 py-2 fw-semibold rounded"
                        style="background: linear-gradient(135deg, #28a745, #0dcaf0); transition: 0.3s ease;">
                  <i class="bi bi-printer me-1"></i> Tampilkan Data
                </button>

                <button type="button" class="btn text-white border-0 px-4 py-2 fw-semibold rounded"
                        style="background: linear-gradient(135deg, #0d6efd, #20c997); transition: 0.3s ease;">
                  <i class="bi bi-download me-1"></i> Download Excel
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="jsx">
import { ref, computed, defineComponent } from 'vue'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import '@/../css/laporan-css/form-styles.css'

/* ===== DATA ===== */
const row1 = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28]
const row2 = [55,54,53,52,51,61,62,63,64,65]
const row3 = [85,84,83,82,81,71,72,73,74,75]
const row4 = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38]
const TOP = row1

const selected = ref(new Set())
const selectedArray = computed(() => Array.from(selected.value).sort((a,b)=>a-b))
function toggle(n){ selected.value.has(n) ? selected.value.delete(n) : selected.value.add(n); selected.value = new Set(selected.value) }
function clearSelection(){ selected.value.clear(); selected.value = new Set() }

/* ===== UTIL: polygon chamfer ===== */
function chamferRectPoints(x, y, w, h, c) {
  const x2 = x + w, y2 = y + h
  return [
    [x + c, y], [x2 - c, y], [x2, y + c],
    [x2, y2 - c], [x2 - c, y2], [x + c, y2],
    [x, y2 - c], [x, y + c]
  ].map(p => p.join(',')).join(' ')
}

/* ===== KOMPONEN GIGI ===== */
const Tooth = defineComponent({
  name: 'Tooth',
  props: { num: { type: Number, required: true }, active: { type: Boolean, default: false } },
  emits: ['toggle'],
  setup(props, { emit }) {
    const stroke = () => (props.active ? '#0d6efd' : '#555')
    const fillBg = () => (props.active ? 'rgba(13,110,253,.10)' : '#fff')

    return () => (
      <div class={['tooth', props.active ? 'active' : '']} onClick={() => emit('toggle')}>
        { TOP.includes(props.num) ? <div class="tooth-num top">{props.num}</div> : <div class="tooth-num bottom">{props.num}</div> }

        <svg viewBox="0 0 100 46" class="tooth-svg" aria-hidden="true">
          <polygon points={chamferRectPoints(1, 1, 98, 44, 6)} fill={fillBg()} stroke={stroke()} stroke-width="1.2" />
          <polygon points={chamferRectPoints(8, 9, 24, 28, 3.2)} fill="none" stroke={stroke()} stroke-width="1.2" />
          <polygon points={chamferRectPoints(11.5, 12.5, 17, 21, 2.2)} fill="none" stroke={stroke()} stroke-width="1.0" />
          <polygon points="50,12 63,25 50,38 37,25" fill="none" stroke={stroke()} stroke-width="1.2" />
          <polygon points="50,16 59,25 50,34 41,25" fill="none" stroke={stroke()} stroke-width="1.0" />
          <polygon points={chamferRectPoints(68, 9, 24, 28, 3.2)} fill="none" stroke={stroke()} stroke-width="1.2" />
          <polygon points={chamferRectPoints(71.5, 12.5, 17, 21, 2.2)} fill="none" stroke={stroke()} stroke-width="1.0" />
        </svg>
      </div>
    )
  }
})
</script>

<style>
/* ===== ODONTOGRAM – outline chamfer, mirip referensi ===== */
.odontogram-frame{
  border: 1.4px solid #6b7280;
  border-radius: 10px;
  padding: 14px 12px;
  background: #fff;
}
.row-odontogram{ margin: 10px 0; }
.teeth-line{
  display: grid;
  grid-template-columns: repeat(16, minmax(42px, 1fr));
  gap: 10px;
}
.teeth-line.short{
  grid-template-columns: repeat(10, minmax(42px, 1fr));
  margin-left: 7%;
}

.tooth{ position: relative; cursor: pointer; user-select: none; }
.tooth-svg{ display:block; width:100%; height:46px; }

.tooth-num{
  position: absolute; left: 50%; transform: translateX(-50%);
  font-size: .95rem; font-weight: 700; color: #111827;
}
.tooth-num.top{ top: -22px; }
.tooth-num.bottom{ bottom: -22px; }

/* aktif: outer polygon biru muda */
.tooth.active polygon:first-of-type{
  stroke:#0d6efd; fill:rgba(13,110,253,.10);
}
</style>
