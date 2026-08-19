<template>
  <div class="card m-4 rounded-4 rounded-bottom-0">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between p-3 rounded-4 rounded-bottom-0"
      style="background: linear-gradient(135deg, #3b82f6, #10b981);">
      <h1 class="fs-5 text-white mb-0">Form Surat Rujukan</h1>

      <a href="#" class="btn bg-white bg-opacity-25 border btn-sm text-white">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
    </div>

    <!-- Body -->
    <div class="card-body">
      <form class="space-y-3" @submit.prevent="submit">
        <!-- Data Pasien -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
          <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-person-vcard text-primary"></i> Data Pasien
          </h6>

          <div class="row g-5">
            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">No MR</label>
                <input type="text" class="form-control" :value="props.dataAnamnesa.loket.simpus_pasien.NO_MR ?? '-'"
                  disabled>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Nama</label>
                <input type="text" class="form-control" :value="props.dataAnamnesa.loket.simpus_pasien.NAMA_LGKP ?? '-'"
                  disabled>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label small mb-1 fw-semibold">Tempat Lahir</label>
                  <input type="text" class="form-control" v-model.trim="props.dataAnamnesa.loket.simpus_pasien.TMPT_LHR"
                    placeholder="Isi tempat lahir" disabled>
                </div>
                <div class="col-6">
                  <label class="form-label small mb-1 fw-semibold">Tgl Lahir</label>
                  <input class="form-control" :value="formatDate(props.dataAnamnesa.loket.simpus_pasien.TGL_LHR)"
                    disabled>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Agama</label>
                <input type="text" class="form-control" v-model.trim="props.dataAnamnesa.loket.simpus_pasien.AGAMA"
                  disabled>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Pekerjaan</label>
                <input type="text" class="form-control"
                  v-model.trim="props.dataAnamnesa.loket.simpus_pasien.JENIS_PKRJN" disabled>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Alamat</label>
                <input type="text" class="form-control" :value="props.dataAnamnesa.loket.simpus_pasien.ALAMAT" disabled>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label small mb-1 fw-semibold">Kecamatan</label>
                  <input type="text" class="form-control"
                    :value="props.dataAnamnesa.loket.simpus_pasien.setup_kab.NAMA_KAB ?? ''" disabled>
                </div>
                <div class="col-6">
                  <label class="form-label small mb-1 fw-semibold">Kel/Desa</label>
                  <input type="text" class="form-control"
                    :value="props.dataAnamnesa.loket.simpus_pasien.setup_kel.NAMA_KEL ?? ''" disabled>
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

          <div class="row g-5">
            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Tanggal Rujuk</label>
                <input type="text" class="form-control" v-model="form.tgl_rujuk" disabled>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">No Surat</label>
                <input type="text" class="form-control" v-model="form.no_surat">
                <div v-if="form.errors.no_surat" class="invalid-feedback d-block">
                  {{ form.errors.no_surat }}
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">No Telp/HP</label>
                <input type="tel" class="form-control" v-model="form.no_hp">
                  <div v-if="form.errors.no_hp" class="invalid-feedback d-block">
                  {{ form.errors.no_hp }}
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Rumah Sakit</label>
                <select class="form-select" v-model="form.provider">
                  <option value="" selected>--Pilih Rumah Sakit --</option>
                  <option v-for="item in props.provider" :value="item.kdProvider"> {{
                    item.nmProvider }}</option>
                </select>
                <div v-if="form.errors.provider" class="invalid-feedback d-block">
                  {{ form.errors.provider }}
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Poli</label>
                <select class="form-select" v-model="form.Poli">
                  <option value="" selected>--Pilih Poli --</option>
                  <option v-for="item in props.poliFktl" :value="item.kdPoli"> {{
                    item.nmPoli }}</option>
                </select>
                <div v-if="form.errors.Poli" class="invalid-feedback d-block">
                  {{ form.errors.Poli }}
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label small mb-1 fw-semibold">Dokter Jaga</label>
                <select class="form-select" v-model="form.tenagaMedis">
                  <option value="" selected>--Pilih Dokter --</option>
                  <option v-for="item in props.tenagaMedisAskep" :value="item.idDokter"> {{
                    item.nmDokter }}</option>
                </select>
                <div v-if="form.errors.tenagaMedis" class="invalid-feedback d-block">
                  {{ form.errors.tenagaMedis }}
                </div>
              </div>
              <div class=" mt-4">
                <div class=" d-flex justify-content-end gap-2">
                  <button type="submit" :disabled="formProcessing" class="btn btn-primary text-white rounded-3">
                    <span v-if="!formProcessing"><i class="bi bi-save me-1"></i> Simpan</span>
                    <span v-else>Menyimpan…</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <!-- <div class="row mt-3">
            <div class="col-md-8">
              <button type="submit" class="btn btn-info text-white w-100 rounded-3">
                <i class="bi bi-save me-1"></i> Simpan
              </button>
            </div>
          </div> -->
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Swal from 'sweetalert2'

const props = defineProps({
  idPoli: String,
  idPelayanan: String,
  dataAnamnesa: Object,
  tenagaMedisAskep: Array,
  poliFktl: Array,
  provider: Array,
  surat: Object
})
const form = useForm({
  id_pelayanan: props.idPelayanan ?? '',
  tgl_rujuk: props.surat?.tgl_rujuk ?? new Date().toISOString().split('T')[0],
  no_surat: props.surat?.no_surat ?? '',
  no_hp: props.surat?.no_hp ?? '',
  provider: props.surat?.kdppk ?? '',
  Poli: props.surat?.kdPoliRujLan ?? '',
  tenagaMedis: props.surat?.tenagaMedis ?? '',
})

function submit() {
  if (!props.surat) {
    form.post(route('ruang-layanan.simpan-rujukan', {
      idPoli: props.idPoli
    }), {
      onSuccess: () => {
        Swal.fire({
          title: 'Sukses',
          text: 'Suskes Menambah Data Surat Rujukan',
          icon: 'success',
          timer: 1600,
          showConfirmButton: false
        })
        router.visit(route('ruang-layanan.surat-rujuk', {
          idPoli: props.idPoli,
          idPelayanan: props.idPelayanan
        }))
      },
      onError: (errors) => {
        console.error('Terjadi kesalahan:', errors)
      }
    })
  } else {
    form.post(route('ruang-layanan.update-rujukan', {
      idPoli: props.idPoli,
      idSurat: props.surat.id_surat_rujukan
    }), {
      onSuccess: () => {
         Swal.fire({
          title: 'Sukses',
          text: 'Suskes Mengubah Data Surat Rujukan',
          icon: 'success',
          timer: 1600,
          showConfirmButton: false
        })
        router.visit(route('ruang-layanan.surat-rujuk', {
          idPoli: props.idPoli,
          idPelayanan: props.idPelayanan
        }))
      },
      onError: (errors) => {
        console.error('Terjadi kesalahan:', errors)
      }
    })
  }
}
function formatDate(tgl) {
  if (!tgl) return "-"
  const date = new Date(tgl);
  console.log(tgl)
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric"
  });
}

</script>

<style scoped>
/* sentuhan kecil biar konsisten */
.form-label {
  font-weight: 600;
}

.card-body {
  background: #f8fafc;
  border-radius: 0 0 1rem 1rem;
}
</style>
