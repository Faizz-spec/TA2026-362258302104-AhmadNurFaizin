<template>
  <div class="container">
    <!-- Judul -->
    <div class=" mb-4">
      <h6 class="fw-bold mb-3 bg-primary bg-opacity-10 d-inline-block p-2 rounded-3">Anamnesa</h6>
    </div>

    <form @submit.prevent="submitForm">
      <div class="row g-4">
        <!-- PASANGAN INPUT KIRI - KANAN -->
        <div class="col-12 col-md-6">
          <div class="row mb-3 ">
            <label class="col-sm-4 col-form-label fw-bold">Tgl Anamnesa</label>
            <div class="col-sm-8">
              <input type="datetime-local" class="form-control" v-model="form.tgl_anamnesa" />
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold">Keluhan Utama/Keperluan</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.keluhan_utama" value></textarea>
              <div v-if="form.errors.keluhan_utama" class="invalid-feedback d-block">
                {{ form.errors.keluhan_utama }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold">Keluhan Tambahan</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.keluhan_tambahan"></textarea>
              <div v-if="form.errors.keluhan_tambahan" class="invalid-feedback d-block">
                {{ form.errors.keluhan_tambahan }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold">Riwayat Penyakit Sekarang</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.riwayat_penyakit_sekarang"></textarea>
              <div v-if="form.errors.riwayat_penyakit_sekarang" class="invalid-feedback d-block">
                {{ form.errors.riwayat_penyakit_sekarang }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold ">Riwayat Penyakit Dahulu</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.riwayat_penyakit_dahulu"></textarea>
              <div v-if="form.errors.riwayat_penyakit_dahulu" class="invalid-feedback d-block">
                {{ form.errors.riwayat_penyakit_dahulu }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold">Riwayat Penyakit Keluarga</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.riwayat_penyakit_keluarga"></textarea>
              <div v-if="form.errors.riwayat_penyakit_keluarga" class="invalid-feedback d-block">
                {{ form.errors.riwayat_penyakit_keluarga }}
              </div>
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN -->
        <div class="col-12 col-md-6">
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 col-form-label fw-bold ">Alergi Makanan</label>
            <div class="col-sm-8">
              <select class="form-control bg-warning bg-opacity-75" v-model="form.alergi_makanan">
                <option value="">-- Pilih Alergi Makanan --</option>
                <option v-for="item in alergiMakanan" :key="item.kodeSatuSehat" :value="item.kodeSatuSehat">
                  {{ item.namaAlergiBpjs }}
                </option>
              </select>
            </div>
          </div>

          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 col-form-label fw-bold">Alergi Obat</label>
            <div class="col-sm-8">
              <select class="form-control bg-warning bg-opacity-75" v-model="form.alergi_obat">
                <option value="">-- Pilih Alergi Obat --</option>
                <option v-for="item in alergiObat" :key="item.kodeSatuSehat" :value="item.kodeSatuSehat">
                  {{ item.namaAlergiBpjs }}
                </option>
              </select>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold ">Keterangan Alergi</label>
            <div class="col-sm-8">
              <textarea class="form-control bg-warning bg-opacity-75" rows="2"
                v-model="form.keterangan_alergi"></textarea>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold ">Tindakan/Terapi</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.tindakan"></textarea>
              <div v-if="form.errors.tindakan" class="invalid-feedback d-block">
                {{ form.errors.tindakan }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold ">Obat yang Sering Digunakan</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.obat_digunakan"></textarea>
              <div v-if="form.errors.obat_digunakan" class="invalid-feedback d-block">
                {{ form.errors.obat_digunakan }}
              </div>
            </div>
          </div>

          <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold ">Obat yang Sering Dikonsumsi</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="2" v-model="form.obat_dikonsumsi"></textarea>
              <div v-if="form.errors.obat_dikonsumsi" class="invalid-feedback d-block">
                {{ form.errors.obat_dikonsumsi }}
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-success btn">
              <i class="bi bi-save"></i> SIMPAN
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2'
const props = defineProps({
  dataAnamnesa: Object,
  idLoket: String,
  masterAlergi: Array,
  routeName: String,
  idPasien: String,
  AlergiPasien: Object
});
const emit = defineEmits(['dataAnamnesa-update'])
const alergiMakanan = props.masterAlergi.filter(item => item.category == 1)

const now = new Date();
const offset = now.getTimezoneOffset();
const localTime = new Date(now.getTime() - offset * 60 * 1000);
const datetimeLocal = localTime.toISOString().slice(0, 16);
console.log(now)

const alergiObat =
  props.masterAlergi.filter(item => item.category == 2)

console.log('data alergi pasein', props.AlergiPasien);
console.log('data alergi', props);
const form = useForm({
  idLoket: props.idLoket ?? '',
  tgl_anamnesa: props.dataAnamnesa?.tglAnamnesa ?? datetimeLocal,
  keluhan_utama: props.dataAnamnesa?.keluhan ?? '',
  keluhan_tambahan: props.dataAnamnesa?.keluhanTambahan ?? '',
  riwayat_penyakit_sekarang: props.dataAnamnesa?.riwayatPenyakitSekarang ?? '',
  riwayat_penyakit_dahulu: props.dataAnamnesa?.riwayatPenyakitDahulu ?? '',
  riwayat_penyakit_keluarga: props.dataAnamnesa?.riwayatPenyakitKeluarga ?? '',
  alergi_makanan: props.AlergiPasien?.alergi_makanan?.kodeSatuSehat ?? '',
  alergi_obat: props.AlergiPasien?.alergi_obat?.kodeSatuSehat ?? '',
  keterangan_alergi: props.AlergiPasien?.keterangan,
  tindakan: props.dataAnamnesa?.terapiYangPernahDijalani ?? '',
  obat_digunakan: props.dataAnamnesa?.obatSeringDigunakan ?? '',
  obat_dikonsumsi: props.dataAnamnesa?.obatSeringDikonsumsi ?? '',
  idPasien: props.idPasien
});

function submitForm() {
  form.post(route(props.routeName, {
    idLoket: props.idLoket
  }),
    {
      preserveScroll: true,
      onSuccess: () => {
        Swal?.fire({
          title: 'Sukses',
          text: 'Data Anamnesa Subjective Tersimpan!',
          icon: 'success',
          timer: 1600,
          showConfirmButton: false
        });
        emit('dataAnamnesa-update');
      },
    });
}
</script>