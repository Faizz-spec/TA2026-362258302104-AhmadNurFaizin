<template>
  <div class="container">
   <form @submit.prevent="pesan">
      <div class="card m-2 rounded-4 shadow-sm overflow-hidden">
        <!-- Header Tabs -->
        <div class="card-header d-flex align-items-center gap-3 p-3"
          style="background: linear-gradient(135deg, #4682B4, #5A9BD5);">
          <a href="" class="text-white text-decoration-none fw-semibold"
            :class="{ 'fw-bold border-bottom border-2 pb-1': currrentSubTabObjective === 'pemeriksaan_fisik' }"
            @click.prevent="currrentSubTabObjective = 'pemeriksaan_fisik'">
            Pemeriksaan Fisik
          </a>
          <a href="" class="text-white text-decoration-none fw-semibold"
            :class="{ 'fw-bold border-bottom border-2 pb-1': currrentSubTabObjective === 'status_generalis' }"
            @click.prevent="currrentSubTabObjective = 'status_generalis'">
            Status Generalis
          </a>
          <a v-if="halaman === 'gigi' && currrentSub === true" href=""
            class="text-white text-decoration-none fw-semibold"
            :class="{ 'fw-bold border-bottom border-2 pb-1': currrentSubTabObjective === 'pemeriksaan_intra_oral_gigi' }"
            @click.prevent="currrentSubTabObjective = 'pemeriksaan_intra_oral_gigi'">
            Pemeriksaan Intra Oral Gigi
          </a>
          <a href="" class="text-white text-decoration-none fw-semibold"
            :class="{ 'fw-bold border-bottom border-2 pb-1': currrentSubTabObjective === 'tenaga_medis' }"
            @click.prevent="currrentSubTabObjective = 'tenaga_medis'">
            Tenaga Medis
          </a>
        </div>

        <!-- Tab: Pemeriksaan Fisik -->
        <div class="card-body row g-4" v-if="currrentSubTabObjective === 'pemeriksaan_fisik'">
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal Anamnesa</label>
              <input type="date" class="form-control shadow-sm rounded-3" />
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keadaan Umum</label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="keadaan" id="baik" />
                  <label class="form-check-label" for="baik">Baik</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="keadaan" id="sedang" checked />
                  <label class="form-check-label" for="sedang">Sedang</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="keadaan" id="lemah" />
                  <label class="form-check-label" for="lemah">Lemah</label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Kesadaran</label>
              <select class="form-select shadow-sm rounded-3">
                <option value="">-- Pilih Kesadaran --</option>
                <option value="compos mentis">Compos Mentis</option>
                <option value="somnolence">Somnolence</option>
                <option value="sopor">Sopor</option>
                <option value="coma">Coma</option>
              </select>
            </div>

            <!-- Hasil IMT -->
            <div class="mb-3">
              <label class="form-label fw-bold">IMT</label>
              <input type="text" class="form-control shadow-sm rounded-3 bg-primary bg-opacity-25" v-model="imt"
                disabled />
            </div>

            <!-- Keterangan IMT -->
            <div class="mb-3">
              <label class="form-label fw-bold">Keterangan IMT</label>
              <input type="text" class="form-control shadow-sm rounded-3 bg-primary bg-opacity-25"
                v-model="imtKeterangan" disabled />
            </div>
          </div>

          <div class="col-md-4">
            <!-- Input Tinggi Badan -->
            <div class="mb-3">
              <label class="form-label fw-bold">Tinggi Badan (cm)</label>
              <input type="number" class="form-control shadow-sm rounded-3" v-model="tinggiBadan"
                placeholder="Masukkan tinggi badan dalam cm" />
            </div>
            <!-- Input Berat Badan -->
            <div class="mb-3">
              <label class="form-label fw-bold">Berat Badan (kg)</label>
              <input type="number" class="form-control shadow-sm rounded-3" v-model="beratBadan"
                placeholder="Masukkan berat badan dalam kg" />
            </div>
            <div v-for="(label, i) in ['Lingkar Perut (cm)', 'Lingkar Lengan (cm)']" :key="i" class="mb-3">
              <label class="form-label fw-bold">{{ label }}</label>
              <input type="text" class="form-control shadow-sm rounded-3" />
            </div>
          </div>

          <div class="col-md-4">
            <div
              v-for="(label, i) in ['Sistole (mmHg)', 'Diastole (mmHg)', 'Resp. Rate (bpm)', 'Heart Rate (bpm)', 'Suhu (°C)']"
              :key="i" class="mb-3">
              <label class="form-label fw-bold">{{ label }}</label>
              <input type="text" class="form-control shadow-sm rounded-3" />
            </div>
          </div>
        </div>

        <!-- Tab: Status Generalis -->
        <div class="card-body" v-if="currrentSubTabObjective === 'status_generalis'">
          <div class="row">
            <!-- Thorax & Abdomen -->
            <div class="col-md-6">
              <h6 class="fw-bold text-decoration-underline">THORAX</h6>

              <!-- Jantung -->
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Jantung</label>
                  <select v-model="statusGeneralis.jantung.status" class="form-select shadow-sm rounded-3">
                    <option>NORMAL</option>
                    <option>ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control shadow-sm rounded-3"
                    v-model="statusGeneralis.jantung.keterangan" :readonly="statusGeneralis.jantung.status === 'NORMAL'"
                    :class="statusGeneralis.jantung.status === 'NORMAL' ? 'bg-light' : ''"
                    placeholder="" />
                </div>
              </div>

              <!-- Pulmo -->
              <div class="row mb-3">
                <div class="col-4">
                  <label class="form-label">Pulmo</label>
                  <select v-model="statusGeneralis.pulmo.status" class="form-select shadow-sm rounded-3">
                    <option>NORMAL</option>
                    <option>ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control shadow-sm rounded-3" v-model="statusGeneralis.pulmo.keterangan"
                    :readonly="statusGeneralis.pulmo.status === 'NORMAL'"
                    :class="statusGeneralis.pulmo.status === 'NORMAL' ? 'bg-light' : ''"
                    placeholder="" />
                </div>
              </div>

              <!-- Abdomen -->
              <h6 class="fw-bold mt-4">ABDOMEN</h6>
              <div v-for="(bagian, i) in ['atas', 'bawah']" :key="i" class="row mb-3">
                <div class="col-4">
                  <label class="form-label">{{ bagian }}</label>
                  <select v-model="statusGeneralis.abdomen[bagian].status" class="form-select shadow-sm rounded-3">
                    <option>NORMAL</option>
                    <option>ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control shadow-sm rounded-3"
                    v-model="statusGeneralis.abdomen[bagian].keterangan"
                    :readonly="statusGeneralis.abdomen[bagian].status === 'NORMAL'"
                    :class="statusGeneralis.abdomen[bagian].status === 'NORMAL' ? 'bg-light' : ''"
                    placeholder="" />
                </div>
              </div>

              <!-- Extrimitas -->
              <h6 class="fw-bold mt-4">EXTRIMITAS</h6>
              <div v-for="(bagian, i) in ['atas', 'bawah']" :key="i" class="row mb-3">
                <div class="col-4">
                  <label class="form-label">{{ bagian }}</label>
                  <select v-model="statusGeneralis.extrimitas[bagian].status" class="form-select shadow-sm rounded-3">
                    <option>NORMAL</option>
                    <option>ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control shadow-sm rounded-3"
                    v-model="statusGeneralis.extrimitas[bagian].keterangan"
                    :readonly="statusGeneralis.extrimitas[bagian].status === 'NORMAL'"
                    :class="statusGeneralis.extrimitas[bagian].status === 'NORMAL' ? 'bg-light' : ''"
                    placeholder="" />
                </div>
              </div>
            </div>

            <!-- Kepala -->
            <div class="col-md-6">
              <h6 class="fw-bold text-uppercase text-decoration-underline">Kepala</h6>
              <div v-for="organ in ['kepala', 'mata', 'telinga', 'leher']" :key="organ" class="row mb-3">
                <div class="col-4">
                  <label class="form-label">{{ organ }}</label>
                  <select v-model="statusGeneralis.kepala[organ].status" class="form-select shadow-sm rounded-3">
                    <option>NORMAL</option>
                    <option>ABNORMAL</option>
                  </select>
                </div>
                <div class="col-8">
                  <label class="form-label">Keterangan</label>
                  <input type="text" class="form-control shadow-sm rounded-3"
                    v-model="statusGeneralis.kepala[organ].keterangan"
                    :readonly="statusGeneralis.kepala[organ].status === 'NORMAL'"
                    :class="statusGeneralis.kepala[organ].status === 'NORMAL' ? 'bg-light' : ''"
                    placeholder="" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Pemeriksaan Intra Oral Gigi -->
        <div v-if="currrentSubTabObjective === 'pemeriksaan_intra_oral_gigi'">
          <FormPemeriksaanIntraOralGigi />
        </div>

        <!-- Tab: Tenaga Medis -->
        <div class="card-body" v-if="currrentSubTabObjective === 'tenaga_medis'">
          <div class="row g-3">
            <slot name="keterangan-gigi"></slot>
            <div class="col-md-6">
              <label class="form-label fw-bold">Tenaga Medis Askep</label>
              <select class="form-select shadow-sm rounded-3 my-2">
                <option value="">- Pilih -</option>
                <option v-for="d in dokter" :key="d.idDokter" :value="d.idDokter">
                  {{ d.kdDokter }} - {{ d.nmDokter }}
                </option>
              </select>
              <button type="submit"
                class="btn d-flex align-items-center gap-2 px-4 fw-semibold text-white shadow-sm rounded-pill"
                style="background: linear-gradient(135deg, #4682B4, #5A9BD5); border: none;">
                <i class="fas fa-save"></i> SIMPAN
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
    
    <!-- Info -->
    <div class="alert bg-primary bg-opacity-25 fw-bold rounded-3 shadow-sm px-3 py-2 mb-4">
      <i class="fas fa-info-circle me-2"></i> Tombol <strong>"Simpan"</strong> ada di tab <strong>"Tenaga
        Medis"</strong>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, reactive } from "vue";
const currrentSubTabObjective = ref("pemeriksaan_fisik");

import FormPemeriksaanIntraOralGigi from "./FormPemeriksaanIntraOralGigi.vue";

// Data input
const tinggiBadan = ref("");
const beratBadan = ref("");
const imt = ref("");
const imtKeterangan = ref("");

// Hitung IMT setiap kali tinggi badan / berat badan berubah
watch([tinggiBadan, beratBadan], ([tb, bb]) => {
  if (tb && bb) {
    const tinggiMeter = tb / 100; // konversi cm ke m
    const imtHasil = bb / (tinggiMeter * tinggiMeter);
    imt.value = imtHasil.toFixed(2);

    if (imtHasil < 18.5) {
      imtKeterangan.value = "Kurus (Kekurangan Berat Badan)";
    } else if (imtHasil < 25) {
      imtKeterangan.value = "Normal (Berat Badan Ideal)";
    } else if (imtHasil < 27) {
      imtKeterangan.value = "Berlebih (Overweight)";
    } else {
      imtKeterangan.value = "Obesitas";
    }
  } else {
    imt.value = "";
    imtKeterangan.value = "";
  }
});

const statusGeneralis = reactive({
  jantung: { status: "NORMAL", keterangan: "" },
  pulmo: { status: "NORMAL", keterangan: "" },
  abdomen: {
    atas: { status: "NORMAL", keterangan: "" },
    bawah: { status: "NORMAL", keterangan: "" },
  },
  extrimitas: {
    atas: { status: "NORMAL", keterangan: "" },
    bawah: { status: "NORMAL", keterangan: "" },
  },
  kepala: {
    kepala: { status: "NORMAL", keterangan: "" },
    mata: { status: "NORMAL", keterangan: "" },
    telinga: { status: "NORMAL", keterangan: "" },
    leher: { status: "NORMAL", keterangan: "" },
  },
});

function pesan() {
  alert("tes");
}

const props = defineProps({
  dokter: Array,
  currrentSub: Boolean,
  halaman: String
})
</script>
