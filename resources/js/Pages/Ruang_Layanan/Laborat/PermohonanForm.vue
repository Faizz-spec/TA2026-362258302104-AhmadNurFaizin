<template>
  <div class="border rounded-4 mb-4 shadow-sm">
    <!-- Header -->
    <div
      class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center"
    >
      <h6 class="m-0 fw-bold">Form Permohonan Laboratorium Pasien</h6>
      <button
        type="button"
        class="btn btn-primary btn-sm"
        @click="$emit('back')"
      >
        Kembali
      </button>
    </div>

    <!-- Body -->
    <form
      @submit.prevent="handleSubmit"
      class="p-3 small bg-light rounded-4 shadow-sm"
    >
      <div class="row g-3">
        <!-- Tanggal dibuat -->
        <div class="col-md-4">
          <label for="tgl_dibuat" class="form-label fw-semibold">Tgl dibuat</label>
          <input
            type="datetime-local"
            id="tgl_dibuat"
            name="tgl_dibuat"
            class="form-control"
            v-model="form.tgl_dibuat"
          />
        </div>

        <!-- Tenaga medis -->
        <div class="col-md-4">
          <label for="tenaga_medis" class="form-label fw-semibold">
            Tenaga Medis Dokter
          </label>
          <select
            id="tenaga_medis"
            name="tenaga_medis"
            class="form-select"
            v-model="form.tenaga_medis"
          >
            <option value="">- pilih -</option>
            <option
              v-for="item in tenagaMedisAskep"
              :key="item.idDokter"
              :value="item.idDokter"
            >
              {{ item.nmDokter }}
            </option>
          </select>
        </div>

        <!-- Radio button hasil lab -->
        <div class="col-md-4">
          <label class="form-label fw-semibold mb-1">
            Pasien sudah memiliki hasil lab luar faskes?
          </label>
          <div class="d-flex flex-wrap gap-3">
            <div class="form-check">
              <input
                type="radio"
                name="hasil_lab"
                id="belum"
                value="belum"
                class="form-check-input"
                v-model="form.hasil_lab_luar_faskes"
              />
              <label for="belum" class="form-check-label">Belum</label>
            </div>
            <div class="form-check">
              <input
                type="radio"
                name="hasil_lab"
                id="sudah"
                value="sudah"
                class="form-check-input"
                v-model="form.hasil_lab_luar_faskes"
              />
              <label for="sudah" class="form-check-label">Sudah</label>
            </div>
          </div>
        </div>

        <!-- Alasan dirujuk -->
        <div class="col-12">
          <label for="alasan" class="form-label fw-semibold">Alasan dirujuk</label>
          <textarea
            id="alasan"
            name="alasan"
            class="form-control"
            rows="3"
            placeholder="Tuliskan alasan rujukan pasien..."
            v-model="form.alasan"
          ></textarea>
        </div>

        <!-- Tombol -->
        <div class="col-12 text-end mt-2">
          <button
            type="submit"
            class="btn btn-success btn-sm px-3"
            :disabled="form.processing"
          >
            <i class="bi bi-plus-circle me-1"></i> Buat Permohonan
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { watch } from 'vue'

const props = defineProps({
  // data pelayanan dari parent (punya loketId, kdPoli, idpelayanan, simpus_loket.pasienId)
  pelayanan: {
    type: Object,
    required: true,
  },
  // daftar dokter pengirim (tenagaMedisAskep dari controller)
  tenagaMedisAskep: {
    type: Array,
    default: () => [],
  },
  // opsional: untuk mode edit / preload nilai
  initial: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['saved', 'back'])

const form = useForm({
  tgl_dibuat: '',
  tenaga_medis: '',
  hasil_lab_luar_faskes: 'belum', // radio: 'belum' / 'sudah'
  alasan: '',
  idPelayanan: props.pelayanan?.idpelayanan ?? '',
  idPoli: props.pelayanan?.kdPoli ?? '',
  idPasien: props.pelayanan?.simpus_loket?.pasienId ?? '',
})

// isi default / initial kalau ada
watch(
  () => props.initial,
  (val) => {
    if (!val) return
    form.tgl_dibuat = val.tgl_dibuat || form.tgl_dibuat
    form.tenaga_medis = val.tenaga_medis || form.tenaga_medis
    form.hasil_lab_luar_faskes = val.hasil_lab_luar_faskes || form.hasil_lab_luar_faskes
    form.alasan = val.alasan || form.alasan
  },
  { immediate: true }
)

// helper ubah now → format datetime-local
function defaultNow() {
  const d = new Date()
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(
    d.getHours()
  )}:${pad(d.getMinutes())}`
}

// set default tanggal kalau kosong
if (!form.tgl_dibuat) {
  form.tgl_dibuat = defaultNow()
}

function handleSubmit() {
  const loketId = props.pelayanan?.loketId || ''

  if (!form.idPasien || !loketId) {
    alert('ID Pasien atau Loket belum terbaca. Silakan kembali ke daftar lalu buka ulang pemeriksaan.')
    return
  }

  form
    .transform((data) => ({
      ...data,
      // backend simpanPermohonanLab pakai: tenaga_medis, alasan, hasil_lab_luar_faskes, idPelayanan, idPoli, idPasien
      // kita convert radio 'belum'/'sudah' → '0'/'1' biar sinkron dengan tampilan list
      hasil_lab_luar_faskes: data.hasil_lab_luar_faskes === 'sudah' ? '1' : '0',
      // kirim juga tglDibuat versi SQL-friendly (kalau nanti controller mau dipakai)
      tglDibuat: data.tgl_dibuat
        ? data.tgl_dibuat.replace('T', ' ') + ':00'
        : null,
    }))
    .post(
      route('ruang-layanan.simpan-permohonan-lab', {
        idLoket: loketId,
      }),
      {
        preserveScroll: true,
        onSuccess: () => {
          // reset sebagian field, jangan sentuh idPasien/dll
          form.reset('alasan', 'tenaga_medis')
          form.hasil_lab_luar_faskes = 'belum'
          emit('saved')
        },
      }
    )
}
</script>
