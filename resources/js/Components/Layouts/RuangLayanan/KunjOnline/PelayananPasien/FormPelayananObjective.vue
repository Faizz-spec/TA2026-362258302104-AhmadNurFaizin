<template>
  <div class="container">
    <div class="bg-warning d-inline-block fw-bold mb-4 ">
      <span>Tombol "Simpan" ada di tab "Tenaga medis"</span>
    </div>
    <form @submit.prevent="submitForm">
      <div class="card shadow-sm rounded-4 border-0">
        <div class="border rounded rounded-bottom-0 shadow-sm d-flex gap-4 p-3">
          <a href="" class="text-decoration-none text-primary"
            :class="{ 'text-primary fw-bold': currrentSubTabObjective === 'pemeriksaan_fisik' }"
            @click.prevent="currrentSubTabObjective = 'pemeriksaan_fisik'">Pemeriksaan Fisik ></a>
          <a href="" class="text-decoration-none text-primary"
            :class="{ 'text-primary fw-bold': currrentSubTabObjective === 'status_generalis' }"
            @click.prevent="currrentSubTabObjective = 'status_generalis'">Status Generalis ></a>
          <a href="" v-if="props.halaman === 'gigi'" class="text-decoration-none text-primary"
            :class="{ 'text-primary fw-bold': currrentSubTabObjective === 'pemeriksaan_intra_oral_gigi' }"
            @click.prevent="currrentSubTabObjective = 'pemeriksaan_intra_oral_gigi'">Pemeriksaan Intra Oral Gigi ></a>
          <a href="" class="text-decoration-none text-primary"
            :class="{ 'text-primary fw-bold': currrentSubTabObjective === 'tenaga_medis' }"
            @click.prevent="currrentSubTabObjective = 'tenaga_medis'">Tenaga Medis ></a>
        </div>
        <div class="card-body row g-4" v-if="currrentSubTabObjective === 'pemeriksaan_fisik'">
          <div class="col-6">
            <div class="mb-3">
              <label for="tanggal-anamnesa" class="form-label fw-bold">Tanggal Anamnesa</label>
              <input type="datetime-local" id="tanggal-anamnesa" class="form-control" v-model="form.tanggal_anamnesa">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keadaan Umum</label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" value="Baik" v-model="form.keadaan_umum">
                  <label class="form-check-label">Baik</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" value="Sedang" v-model="form.keadaan_umum">
                  <label class="form-check-label">Sedang</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" value="Lemah" v-model="form.keadaan_umum">
                  <label class="form-check-label">Lemah</label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Kesadaran</label>
              <select class="form-control" id="kesadaran" v-model="form.kesadaran">
                <option v-for="item in dataKesadaran" :key="item.kdSadar" :value="item.kdSadar">
                  {{ item.nmSadar }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">IMT</label>
              <input type="text" class="form-control bg-warning bg-opacity-75" v-model="form.imt" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keterangan IMT</label>
              <input type="text" class="form-control bg-warning bg-opacity-75" v-model="form.imtKet" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Tinggi Badan (cm)</label>
              <input type="text" class="form-control" v-model="form.tinggi_badan" @change="cekimt">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Berat Badan (kg)</label>
              <input type="text" class="form-control" v-model="form.berat_badan" @change="cekimt">
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label fw-bold">Lingkar Perut (cm)</label>
              <input type="text" class="form-control" v-model="form.lingkar_perut">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Lingkar Lengan (cm)</label>
              <input type="text" class="form-control" v-model="form.lingkar_lengan">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Sistole (mmHg)</label>
              <input type="text" class="form-control" v-model="form.sistole">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Diastole (mmHg)</label>
              <input type="text" class="form-control" v-model="form.diastole">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Resp. Rate (bpm)</label>
              <input type="text" class="form-control" v-model="form.resp_rate">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Heart Rate (bpm)</label>
              <input type="text" class="form-control" v-model="form.heart_rate">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Suhu (°C)</label>
              <input type="text" class="form-control" v-model="form.suhu">
            </div>
          </div>
        </div>

        <div class="card-body" v-if="currrentSubTabObjective === 'status_generalis'">
          <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
              <!-- THORAX -->
              <h6 class="fw-bold text-decoration-underline">THORAX</h6>
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Jantung</label>
                  <select class="form-select" id="jantung" v-model="form.jantung"
                    @change="changeInputKet('jantung', 'ket_jantung')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.jantung !== 'ABNORMAL' }"
                    :readonly="form.jantung !== 'ABNORMAL'" v-model="form.ket_jantung">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Pulmo</label>
                  <select class="form-select" v-model="form.pulmo" @change="changeInputKet('pulmo', 'ket_pulmo')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.pulmo !== 'ABNORMAL' }"
                    :readonly="form.pulmo !== 'ABNORMAL'" readonly v-model="form.ket_pulmo">
                </div>
              </div>

              <!-- ABDOMEN -->
              <h6 class="fw-bold mt-4">ABDOMEN</h6>
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Atas</label>
                  <select class="form-select" v-model="form.abdomen_atas"
                    @change="changeInputKet('abdomen_atas', 'ket_abdomen_atas')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.abdomen_atas !== 'ABNORMAL' }"
                    :readonly="form.abdomen_atas !== 'ABNORMAL'" readonly v-model="form.ket_abdomen_atas">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Bawah</label>
                  <select class="form-select" v-model="form.abdomen_bawah"
                    @change="changeInputKet('abdomen_bawah', 'ket_abdomen_bawah')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.abdomen_bawah !== 'ABNORMAL' }"
                    :readonly="form.abdomen_bawah !== 'ABNORMAL'" readonly v-model="form.ket_abdomen_bawah">
                </div>
              </div>

              <!-- EXTRIMITAS -->
              <h6 class="fw-bold mt-4">EXTRIMITAS</h6>
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Atas</label>
                  <select class="form-select" v-model="form.extrimitas_atas"
                    @change="changeInputKet('extrimitas_atas', 'ket_extrimitas_atas')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control bg-light"
                    :class="{ 'bg-light': form.extrimitas_atas !== 'ABNORMAL' }"
                    :readonly="form.extrimitas_atas !== 'ABNORMAL'" readonly v-model="form.ket_extrimitas_atas">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Bawah</label>
                  <select class="form-select" v-model="form.extrimitas_bawah"
                    @change="changeInputKet('extrimitas_bawah', 'ket_extrimitas_bawah')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.extrimitas_bawah !== 'ABNORMAL' }"
                    :readonly="form.extrimitas_bawah !== 'ABNORMAL'" readonly v-model="form.ket_extrimitas_bawah">
                </div>
              </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
              <h6 class="fw-bold text-uppercase text-decoration-underline">KEPALA</h6>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Kepala</label>
                  <select class="form-select" v-model="form.kepala" @change="changeInputKet('kepala', 'ket_kepala')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.kepala !== 'ABNORMAL' }"
                    :readonly="form.kepala !== 'ABNORMAL'" readonly v-model="form.ket_kepala">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Mata</label>
                  <select class="form-select" v-model="form.mata" @change="changeInputKet('mata', 'ket_mata')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.mata !== 'ABNORMAL' }"
                    :readonly="form.mata !== 'ABNORMAL'" readonly v-model="form.ket_mata">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Telinga</label>
                  <select class="form-select" v-model="form.telinga" @change="changeInputKet('telinga', 'ket_telinga')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.telinga !== 'ABNORMAL' }"
                    :readonly="form.telinga !== 'ABNORMAL'" readonly v-model="form.ket_telinga">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Leher</label>
                  <select class="form-select" v-model="form.leher" @change="changeInputKet('leher', 'ket_leher')">
                    <option value="NORMAL">NORMAL</option>
                    <option value="ABNORMAL">ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control" :class="{ 'bg-light': form.leher !== 'ABNORMAL' }"
                    :readonly="form.leher !== 'ABNORMAL'" readonly v-model="form.ket_leher">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div name="form-intra-oral-gigi" v-if="currrentSubTabObjective === 'pemeriksaan_intra_oral_gigi'">
          <FormPemeriksaanIntraOralGigi :form="form"></FormPemeriksaanIntraOralGigi>
        </div>
        <div class="card-body" v-if="currrentSubTabObjective === 'tenaga_medis'">
          <div class="row">
            <div class="col-6">
              <slot name="status_pasien">
              </slot>
              <label for="" class="fw-bold">Tenaga Media Askep</label>
              <select class="form-control my-3" name="" id="tenaga_medis_askep" v-model="form.tenaga_medis_askep">
                <option value="" selected>-- Pilih --</option>
                <option v-for="item in props.tenagaMedisAskep" :value=item.idDokter> {{ item.nmDokter }}</option>
              </select>
              <button type="submit" class="btn btn-success">Simpan</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
const currrentSubTabObjective = ref('pemeriksaan_fisik');
import FormPemeriksaanIntraOralGigi from './FormPemeriksaanIntraOralGigi.vue';
const emit = defineEmits(['dataAnamnesa-update']);
import { watch } from 'vue';

const props = defineProps({
  currrentSub: String,
  halaman: String,
  dataKesadaran: Object,
  dataAnamnesa: Object,
  routeName: String,
  statusPasien: String,
  tenagaMedisAskep: Object,
});
console.log(props.dataAnamnesa, 'data anamnesa dari tab objective');

const form = useForm({
  idAnamnesa: props.dataAnamnesa.idAnamnesa,
  tanggal_anamnesa: props.dataAnamnesa.tglAnamnesa,
  keadaan_umum: props.dataAnamnesa.keadaanUmum,
  kesadaran: props.dataAnamnesa.kdSadar,
  imt: props.dataAnamnesa.imt,
  imtKet: props.dataAnamnesa.imtKet,
  tinggi_badan: props.dataAnamnesa.tinggiBadan,
  berat_badan: props.dataAnamnesa.beratBadan,
  lingkar_perut: props.dataAnamnesa.lingkarPerut,
  lingkar_lengan: props.dataAnamnesa.lingkarTangan,
  sistole: props.dataAnamnesa.sistole,
  diastole: props.dataAnamnesa.diastole,
  resp_rate: props.dataAnamnesa.respRate,
  heart_rate: props.dataAnamnesa.heartRate,
  suhu: props.dataAnamnesa.suhu,

  jantung: props.dataAnamnesa.thoraxJantung ?? 'NORMAL',
  ket_jantung: props.dataAnamnesa.thoraxJantungKet,
  pulmo: props.dataAnamnesa.thoraxPulmo ?? 'NORMAL',
  ket_pulmo: props.dataAnamnesa.thoraxPulmoKet,
  abdomen_atas: props.dataAnamnesa.abdomanAtas ?? 'NORMAL',
  ket_abdomen_atas: props.dataAnamnesa.abdomanAtasKet,
  abdomen_bawah: props.dataAnamnesa.abdomanBawah ?? 'NORMAL',
  ket_abdomen_bawah: props.dataAnamnesa.abdomanBawahket,
  extrimitas_atas: props.dataAnamnesa.extrimitasAtas ?? 'NORMAL',
  ket_extrimitas_atas: props.dataAnamnesa.extrimitasAtasKet,
  extrimitas_bawah: props.dataAnamnesa.extrimitasBawah ?? 'NORMAL',
  ket_extrimitas_bawah: props.dataAnamnesa.extrimitasBawahKet,
  kepala: props.dataAnamnesa.kepala ?? 'NORMAL',
  ket_kepala: props.dataAnamnesa.kepalaKet,
  mata: props.dataAnamnesa.mata ?? 'NORMAL',
  ket_mata: props.dataAnamnesa.mataKet,
  telinga: props.dataAnamnesa.telinga ?? 'NORMAL',
  ket_telinga: props.dataAnamnesa.telingatKet,
  leher: props.dataAnamnesa.leher ?? 'NORMAL',
  ket_leher: props.dataAnamnesa.leherKet,
  tenaga_medis_askep: props.dataAnamnesa.tenagaMedisAskep,

  perkusi: props.dataAnamnesa.perkusi ?? '',
  druk: props.dataAnamnesa.druk ?? '',
  palpasi: props.dataAnamnesa.palpasi ?? '',
  luxasi: props.dataAnamnesa.luxasi ?? '',
  vitalitas: props.dataAnamnesa.vitalitas ?? '',
  statusPasien: props.statusPasien ?? ''
});

function changeInputKet(field, ketfield) {
  if (form[field] !== 'ABNORMAL') {
    form[ketfield] = '';
  }
}

function submitForm() {
  form.post(route(props.routeName), {
    preserveScroll: true,
    onSuccess: () => {
      alert("Anamnesa Objective tersimpan");
      emit('dataAnamnesa-update');
    },
  })
}
watch(() => props.statusPasien, (newVal) => {
  form.statusPasien = newVal;
});

function cekimt() {
  let berat = parseFloat(form.berat_badan);
  let tinggi = parseFloat(form.tinggi_badan);

  let imt1 = (berat / (tinggi * tinggi));
  let meter = 10000;
  let imt = parseFloat((imt1 * meter).toFixed(2));
  console.log('tipe imt', imt)

  let ket = "";
  if (imt < 18.5) {
    ket = 'Berat Badan Kurang';
  } else if (imt >= 18.5 && imt <= 22.9) {
    ket = 'Berat Badan Normal';
  } else if (imt == 23.0) {
    ket = 'Kelebihan Berat Badan';
  } else if (imt >= 23.1 && imt <= 24.9) {
    ket = 'Berisiko Menjadi Obes';
  } else if (imt >= 25.0 && imt <= 29.9) {
    ket = 'Obes I';
  } else if (imt >= 30.0) {
    ket = 'Obes II';
  }

  if (berat && tinggi) {
    form.imt = imt;
    form.imtKet = ket;
  }
}

</script>
