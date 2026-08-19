<template>
  <div class="card p-3 shadow-sm border-0 rounded-4">
    <div class="form">
      <form @submit.prevent="submitForm">
        <div class="row m-2">
          <!-- Pelayanan Luar Gedung -->
          <div class="col-md-6">
              <div class="fw-bold mb-4">
                <h5 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Pelayanan Luar Gedung</h5>
              </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Interfeksi</label>
              <div class="col-sm-8">
                <textarea v-model="form.interfeksi" class="form-control"></textarea>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Keluarga Binaan</label>
              <div class="col-sm-8">
                <select v-model="form.keluarga_binaan" class="form-select">
                  <option value="">-- Pilih --</option>
                  <option value="1">Ya</option>
                  <option value="0">Tidak</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Keluarga Risti</label>
              <div class="col-sm-8">
                <select v-model="form.keluarga_risti" class="form-select">
                  <option value="">-- Pilih --</option>
                  <option value="1">Ya</option>
                  <option value="0">Tidak</option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Alergi Makanan</label>
              <div class="col-sm-8">
                <input type="text" class="form-control bg-warning bg-opacity-75" :value="props.AlergiPasien.alergi_makanan.namaAlergiBpjs" />
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Alergi Obat</label>
              <div class="col-sm-8">
                <input  type="text" class="form-control bg-warning bg-opacity-75" :value="props.AlergiPasien.alergi_obat.namaAlergiBpjs"/>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Keterangan Alergi</label>
              <div class="col-sm-8">
                <textarea  class="form-control bg-warning bg-opacity-75"  :value="props.AlergiPasien.keterangan"></textarea>
              </div>
            </div>
          </div>

          <!-- Pelayanan Dalam Gedung -->
          <div class="col-md-6">
            <div class="fw-bold mb-4">
               <h5 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Pelayanan Dalam Gedung</h5>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Tindakan / Saran</label>
              <div class="col-sm-8">
                <textarea v-model="form.tindakan_saran" class="form-control"></textarea>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-4 col-form-label fw-bold">Hasil Wawancara</label>
              <div class="col-sm-8">
                <textarea v-model="form.hasil_wawancara" class="form-control"></textarea>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
              <button class="btn btn-success" type="submit" :disabled="form.processing">
                <i class="bi bi-save"></i> SIMPAN
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
const props = defineProps({
    idPelayanan : String,
    AlergiPasien : Object,
    sanitasi:Object
})

console.log('idpelayanan',props.AlergiPasien);

const form = useForm({
  interfeksi: props.sanitasi?.interfeksi?? '',
  keluarga_binaan: props.sanitasi?.keluargaBinaan??'',
  keluarga_risti: props.sanitasi?.keluargaRisti??'',
 
  tindakan_saran: props.sanitasi?.tindakanSaran??'',
  hasil_wawancara: props.sanitasi?.hasilWawancara??''
})

// fungsi kirim data ke backend (Laravel controller)
const submitForm = () => {
  form.post(route('ruang-layanan.simpan-sanitasi', [props.idPelayanan]), {
    onSuccess: () => {
      form.reset()
      alert('sukses simpan sanitasi')
    }
  })
}
</script>
