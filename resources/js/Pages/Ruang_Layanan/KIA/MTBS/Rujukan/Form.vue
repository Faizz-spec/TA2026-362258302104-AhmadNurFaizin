<template>
  <div class="p-3">
    <div class="card border-success shadow-sm">
      <div class="card-header bg-success text-white fw-bold">
        Form Surat Rujukan MTBS
      </div>

      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Data Pasien</h6>

            <div class="mb-2">
              <label class="form-label">No RM</label>
              <input class="form-control" :value="pasien.NO_MR || '-'" disabled>
            </div>

            <div class="mb-2">
              <label class="form-label">Nama Pasien</label>
              <input class="form-control" :value="pasien.NAMA_LGKP || '-'" disabled>
            </div>

            <div class="mb-2">
              <label class="form-label">NIK</label>
              <input class="form-control" :value="pasien.NIK || '-'" disabled>
            </div>

            <div class="mb-2">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" rows="3" :value="alamatLengkap" disabled />
            </div>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Data Rujukan</h6>

            <div class="mb-2">
              <label class="form-label">Tanggal Rujuk</label>
              <input v-model="form.tanggal_rujuk" type="date" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">No Surat</label>
              <input v-model="form.no_surat" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">Rumah Sakit / Faskes Tujuan</label>
              <input v-model="form.rumah_sakit" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">Poli Tujuan</label>
              <input v-model="form.poli" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">Dokter</label>
              <input v-model="form.dokter_jaga" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">No Telp/HP</label>
              <input v-model="form.no_telp_hp" class="form-control">
            </div>
          </div>

          <div class="col-md-12">
            <label class="form-label">Anamnesa</label>
            <textarea v-model="form.anamnesa" class="form-control" rows="2" />
          </div>

          <div class="col-md-12">
            <label class="form-label">Pemeriksaan Fisik</label>
            <textarea v-model="form.pemeriksaan_fisik" class="form-control" rows="2" />
          </div>

          <div class="col-md-12">
            <label class="form-label">Diagnosa Sementara</label>
            <textarea v-model="form.diagnosa_sementara" class="form-control" rows="2" />
          </div>

          <div class="col-md-12">
            <label class="form-label">Catatan</label>
            <textarea v-model="form.catatan" class="form-control" rows="2" />
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <Link
            :href="route('mtbs.rujukan.index', { kunjungan_id: form.kunjungan_id })"
            class="btn btn-light border w-50"
          >
            Kembali
          </Link>

          <button class="btn btn-success w-50" :disabled="loading" @click="simpan">
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AppLayouts from '../../../../../Components/Layouts/AppLayouts.vue'
import { Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'
import axios from 'axios'

defineOptions({ layout: AppLayouts })

const props = defineProps({
  pasien: Object,
  existing: Object,
  defaultForm: Object,
})

const loading = ref(false)
const pasien = computed(() => props.pasien || {})

const form = reactive({
  kunjungan_id: props.defaultForm?.kunjungan_id || '',
  tanggal_rujuk: props.defaultForm?.tanggal_rujuk || '',
  no_surat: props.defaultForm?.no_surat || '',
  rumah_sakit: props.defaultForm?.rumah_sakit || '',
  poli: props.defaultForm?.poli || '',
  dokter_jaga: props.defaultForm?.dokter_jaga || '',
  no_telp_hp: props.defaultForm?.no_telp_hp || '',
  anamnesa: props.defaultForm?.anamnesa || '',
  pemeriksaan_fisik: props.defaultForm?.pemeriksaan_fisik || '',
  diagnosa_sementara: props.defaultForm?.diagnosa_sementara || '',
  catatan: props.defaultForm?.catatan || '',
})

const alamatLengkap = computed(() => {
  const p = pasien.value

  return [
    p.alamat,
    p.no_rt ? `RT ${p.no_rt}` : '',
    p.no_rw ? `RW ${p.no_rw}` : '',
    p.nama_kel ? `Kel. ${p.nama_kel}` : '',
    p.nama_kec ? `Kec. ${p.nama_kec}` : '',
    p.nama_kab,
    p.nama_prop,
  ].filter(Boolean).join(' ') || '-'
})

const simpan = async () => {
  loading.value = true

  try {
    const res = await axios.post(route('mtbs.rujukan.store'), form)
    alert(res.data?.message || 'Surat berhasil disimpan')

    router.visit(route('mtbs.rujukan.index', {
      kunjungan_id: form.kunjungan_id,
    }))
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menyimpan surat rujukan')
  } finally {
    loading.value = false
  }
}
</script>