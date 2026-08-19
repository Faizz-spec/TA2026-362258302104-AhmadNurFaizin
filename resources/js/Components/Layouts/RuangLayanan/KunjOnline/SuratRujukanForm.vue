<template>
  <div class="card m-4 rounded-4 rounded-bottom-0">
    <!-- Header -->
    <div
      class="card-header d-flex justify-content-between p-3 rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #3b82f6, #10b981);"
    >
      <h1 class="fs-5 text-white mb-0">Form Surat Rujukan</h1>

      <Link :href="backRoute" class="btn bg-white bg-opacity-25 border btn-sm text-white">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </Link>
    </div>

    <!-- Body -->
    <div class="card-body">
      <form @submit.prevent="submit" class="space-y-3">
        <!-- Data Pasien -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
          <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-person-vcard text-primary"></i> Data Pasien
          </h6>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small text-muted mb-1">No MR</label>
                <input type="text" class="form-control" :value="pasien?.NO_MR ?? '-'" disabled>
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Nama</label>
                <input type="text" class="form-control" :value="pasien?.NAMA_LGKP ?? '-'" disabled>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label small text-muted mb-1">Tempat Lahir</label>
                  <input type="text" class="form-control" v-model.trim="form.tempat_lahir" placeholder="Isi tempat lahir">
                </div>
                <div class="col-6">
                  <label class="form-label small text-muted mb-1">Tgl Lahir</label>
                  <input type="date" class="form-control" v-model="form.tgl_lahir">
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Agama</label>
                <input type="text" class="form-control" v-model.trim="form.agama">
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Pekerjaan</label>
                <input type="text" class="form-control" v-model.trim="form.pekerjaan">
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Alamat</label>
                <input type="text" class="form-control" :value="alamatFull" disabled>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label small text-muted mb-1">Kecamatan</label>
                  <input type="text" class="form-control" :value="pasien?.nama_kec ?? ''" disabled>
                </div>
                <div class="col-6">
                  <label class="form-label small text-muted mb-1">Kel/Desa</label>
                  <input type="text" class="form-control" :value="pasien?.nama_kel ?? ''" disabled>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Surat -->
        <div class="bg-white rounded-4 shadow-sm p-4">
          <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-envelope-paper text-primary"></i> Data Surat
          </h6>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Tanggal Rujuk</label>
                <input type="date" class="form-control" v-model="form.tgl_rujuk" required>
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">No Surat</label>
                <input type="text" class="form-control" v-model.trim="form.no_surat" placeholder="No surat rujukan">
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">No Telp/HP</label>
                <input type="text" class="form-control" v-model.trim="form.no_hp" placeholder="08xxxxxxxxxx">
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Rumah Sakit</label>
                <select class="form-select" v-model="form.kdppk">
                  <option value="" disabled>- pilih -</option>
                  <option v-for="rs in rumahSakitOptions" :key="rs.kdppk" :value="rs.kdppk">
                    {{ rs.nama_unit }}
                  </option>
                </select>
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Poli</label>
                <select class="form-select" v-model="form.kdPoliRujLan">
                  <option value="" disabled>- pilih -</option>
                  <option v-for="p in poliOptions" :key="p.kdPoli" :value="p.kdPoli">
                    {{ p.nmPoli }}
                  </option>
                </select>
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted mb-1">Dokter Jaga</label>
                <select class="form-select" v-model="form.tenagaMedis">
                  <option value="" disabled>- pilih -</option>
                  <option v-for="d in dokterOptions" :key="d.kode" :value="d.kode">
                    {{ d.nama }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="row mt-3">
            <div class="col-md-4">
              <Link :href="backRoute" class="btn w-100 btn-outline-secondary rounded-3">
                Kembali
              </Link>
            </div>
            <div class="col-md-8">
              <button type="submit" :disabled="formProcessing" class="btn btn-info text-white w-100 rounded-3">
                <span v-if="!formProcessing"><i class="bi bi-save me-1"></i> Simpan</span>
                <span v-else>Menyimpan…</span>
              </button>
            </div>
          </div>

          <!-- error bag -->
          <div v-if="hasErrors" class="alert alert-warning mt-3 rounded-3">
            <ul class="mb-0 small">
              <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
            </ul>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  dataPasien: { type: Object, required: true },     // { idLoket, NO_MR, NAMA_LGKP, ... }
  backRoute:  { type: String, required: true },     // url untuk tombol kembali
  saveRoute:  { type: String, required: true },     // route POST simpan rujukan
  rumahSakitOptions: { type: Array, default: () => [] }, // [{kdppk, nama_unit}]
  poliOptions:       { type: Array, default: () => [] }, // [{kdPoli, nmPoli}]
  dokterOptions:     { type: Array, default: () => [] }, // [{kode, nama}]
})

const pasien = computed(() => props.dataPasien ?? null)
const alamatFull = computed(() => {
  if (!pasien.value) return '-'
  const p = pasien.value
  return [
    p.alamat,
    p.no_rt ? `RT ${p.no_rt}` : '',
    p.no_rw ? `RW ${p.no_rw}` : '',
    p.nama_kel, p.nama_kec, p.nama_kab, p.nama_prop
  ].filter(Boolean).join(' ')
})

// Inertia useForm → biar otomatis kirim + handle error
const form = useForm({
  // relasi pelayanan
  id_pelayanan: pasien.value?.idLoket || '',

  // pasien (opsional, buat cetak surat)
  tempat_lahir: '',
  tgl_lahir: '',
  agama: '',
  pekerjaan: '',

  // surat
  tgl_rujuk: new Date().toISOString().slice(0,10),
  no_surat: '',
  no_hp: '',

  // tujuan
  kdppk: '',
  kdPoliRujLan: '',
  tenagaMedis: '',

  // tambahan opsional (jaga2 kebutuhan DB)
  nama_unit: '',
  alamat: alamatFull.value,
})

const formProcessing = ref(false)
const errors = computed(() => form.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)

function submit () {
  formProcessing.value = true

  form.post(props.saveRoute, {
    preserveScroll: true,
    onFinish: () => { formProcessing.value = false },
    onSuccess: () => {
      // reset ringan kecuali id_pelayanan & tgl_rujuk
      form.no_surat = ''
      form.no_hp = ''
    },
  })
}
</script>

<style scoped>
/* sentuhan kecil biar konsisten */
.form-label { font-weight: 600; }
.card-body { background: #f8fafc; border-radius: 0 0 1rem 1rem; }
</style>
