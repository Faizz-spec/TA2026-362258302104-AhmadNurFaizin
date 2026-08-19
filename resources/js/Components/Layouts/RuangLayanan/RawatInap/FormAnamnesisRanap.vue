<template>
  <div class="anamnesis-container">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
        <div class="d-flex align-items-center">
          <i class="fas fa-clipboard-list me-2"></i>
          <h5 class="mb-0">Anamnesis Rawat Inap</h5>
        </div>
      </div>
      <div class="card-body">
        <div class="alert alert-info" role="alert">
          <i class="fas fa-info-circle me-2"></i>
          Lengkapi data anamnesis pasien dengan teliti untuk memastikan diagnosis yang akurat.
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <form @submit.prevent="saveAnamnesis">
      <!-- Basic Information Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-primary">
            <i class="fas fa-user-md me-2"></i>
            Informasi Dasar
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-calendar-alt me-1"></i>
                  Tanggal Anamnesis <span class="text-danger">*</span>
                </label>
                <input type="datetime-local" class="form-control" v-model="anamnesisForm.tglAnamnesis" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-brain me-1"></i>
                  Kesadaran <span class="text-danger">*</span>
                </label>
                <select class="form-select" v-model="anamnesisForm.kdSadar" required>
                  <option value="">- Pilih Kesadaran -</option>
                  <option v-for="s in refs.kesadaran" :key="s.kdSadar" :value="s.kdSadar">{{ s.nmSadar }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-user-nurse me-1"></i>
                  Tenaga Medis Askep <span class="text-danger">*</span>
                </label>
                <select class="form-select" v-model="anamnesisForm.tenagaMedisAskep" required>
                  <option value="">- Pilih Tenaga Medis -</option>
                  <option v-for="d in refs.dokter" :key="d.kdDokter" :value="d.kdDokter">{{ d.nmDokter }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vital Signs Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-success">
            <i class="fas fa-heartbeat me-2"></i>
            Tanda Vital & Antropometri
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-ruler-vertical me-1"></i>
                  Tinggi Badan (cm) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.tinggiBadan" @input="calculateIMT" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-weight me-1"></i>
                  Berat Badan (kg) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.beratBadan" @input="calculateIMT" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-tape me-1"></i>
                  Lingkar Perut (cm)
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.lingkarPerut" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-calculator me-1"></i>
                  IMT (BMI)
                </label>
                <input type="text" class="form-control bg-light" v-model="anamnesisForm.imt" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-info-circle me-1"></i>
                  Kategori IMT
                </label>
                <input type="text" class="form-control bg-light" v-model="anamnesisForm.imtKet" readonly />
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-thermometer-half me-1"></i>
                  Suhu (°C) <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.1" class="form-control" v-model="anamnesisForm.suhu" required />
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-stethoscope me-1"></i>
                  Sistole (mmHg) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.sistole" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-stethoscope me-1"></i>
                  Diastole (mmHg) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.diastole" required />
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-lungs me-1"></i>
                  Respiratory Rate (per minute) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.respRate" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-heart me-1"></i>
                  Heart Rate (bpm) <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" v-model="anamnesisForm.heartRate" required />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Physical Examination Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-warning">
            <i class="fas fa-search me-2"></i>
            Pemeriksaan Fisik
          </h6>
        </div>
        <div class="card-body">
          <!-- Thorax Section -->
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="text-primary border-bottom pb-2">
                <i class="fas fa-lungs me-2"></i>
                THORAX
              </h6>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Jantung</label>
                <select class="form-select" v-model="anamnesisForm.thoraxJantung" @change="toggleKet(1)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.thoraxJantung === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Jantung</label>
                <input type="text" class="form-control" v-model="anamnesisForm.thoraxJantungKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Pulmo</label>
                <select class="form-select" v-model="anamnesisForm.thoraxPulmo" @change="toggleKet(2)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.thoraxPulmo === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Pulmo</label>
                <input type="text" class="form-control" v-model="anamnesisForm.thoraxPulmoKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
          </div>

          <!-- Abdomen Section -->
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="text-primary border-bottom pb-2">
                <i class="fas fa-user-md me-2"></i>
                ABDOMEN
              </h6>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Atas</label>
                <select class="form-select" v-model="anamnesisForm.abdomanAtas" @change="toggleKet(3)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.abdomanAtas === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Abdomen Atas</label>
                <input type="text" class="form-control" v-model="anamnesisForm.abdomanAtasKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Bawah</label>
                <select class="form-select" v-model="anamnesisForm.abdomanBawah" @change="toggleKet(4)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.abdomanBawah === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Abdomen Bawah</label>
                <input type="text" class="form-control" v-model="anamnesisForm.abdomanBawahKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
          </div>

          <!-- Extremities Section -->
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="text-primary border-bottom pb-2">
                <i class="fas fa-hand-paper me-2"></i>
                EXTREMITAS
              </h6>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Atas</label>
                <select class="form-select" v-model="anamnesisForm.extrimitasAtas" @change="toggleKet(5)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.extrimitasAtas === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Extremitas Atas</label>
                <input type="text" class="form-control" v-model="anamnesisForm.extrimitasAtasKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Bawah</label>
                <select class="form-select" v-model="anamnesisForm.extrimitasBawah" @change="toggleKet(6)">
                  <option value="">- Pilih Kondisi -</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="ABNORMAL">ABNORMAL</option>
                </select>
              </div>
              <div v-if="anamnesisForm.extrimitasBawah === 'ABNORMAL'" class="mb-3">
                <label class="form-label">Keterangan Extremitas Bawah</label>
                <input type="text" class="form-control" v-model="anamnesisForm.extrimitasBawahKet" placeholder="Jelaskan temuan abnormal..." />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Complaints and Allergies Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Keluhan & Alergi
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-comment-medical me-1"></i>
                  Keluhan Utama
                </label>
                <textarea class="form-control" v-model="anamnesisForm.keluhan" rows="4" placeholder="Jelaskan keluhan pasien secara detail..."></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-allergies me-1"></i>
                  Alergi Makanan
                </label>
                <input type="text" class="form-control bg-light" v-model="anamnesisForm.alergiMakanan" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-pills me-1"></i>
                  Alergi Obat
                </label>
                <input type="text" class="form-control bg-light" v-model="anamnesisForm.alergiObat" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-sticky-note me-1"></i>
                  Keterangan Alergi
                </label>
                <textarea class="form-control bg-light" v-model="anamnesisForm.keteranganAlergi" readonly rows="2"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" @click="resetForm">
              <i class="fas fa-undo me-1"></i>
              Reset
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isSaving">
              <i class="fas fa-save me-1"></i>
              <span v-if="isSaving">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Menyimpan...
              </span>
              <span v-else>Simpan Anamnesis</span>
            </button>
          </div>
        </div>
      </div>
    </form>

    <!-- History Table Section -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 text-info">
          <i class="fas fa-history me-2"></i>
          Riwayat Anamnesis
        </h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Kesadaran</th>
                <th>Tekanan Darah & Vital Signs</th>
                <th>Thorax Jantung/Pulmo</th>
                <th>Abdomen Atas/Bawah</th>
                <th>Extremitas Atas/Bawah</th>
                <th>Pemeriksa</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(anam, index) in anamnesisList" :key="anam.idAnamnesa">
                <td class="text-center">{{ index + 1 }}</td>
                <td>{{ anam.tglAnamnesa }}</td>
                <td>{{ anam.nmSadar }}</td>
                <td>
                  <small>
                    <strong>T.Badan:</strong> {{ anam.tinggiBadan }} cm | <strong>B.Badan:</strong> {{ anam.beratBadan }} kg<br />
                    <strong>TD:</strong> {{ anam.sistole }}/{{ anam.diastole }} mmHg<br />
                    <strong>RR:</strong> {{ anam.respRate }}/min | <strong>HR:</strong> {{ anam.heartRate }} bpm | <strong>Suhu:</strong> {{ anam.suhu }} °C<br />
                    <strong>Keluhan:</strong> {{ anam.keluhan || '-' }}
                  </small>
                </td>
                <td>{{ anam.thoraxJantung }}/{{ anam.thoraxPulmo }}</td>
                <td>{{ anam.abdomanAtas }}/{{ anam.abdomanBawah }}</td>
                <td>{{ anam.extrimitasAtas }}/{{ anam.extrimitasBawah }}</td>
                <td>{{ anam.nmDokter }}</td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-danger" @click="deleteAnamnesis(anam.idAnamnesa)" title="Hapus anamnesis">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="anamnesisList.length === 0">
                <td colspan="9" class="text-center text-muted py-4">
                  <i class="fas fa-inbox fa-2x mb-2"></i><br />
                  Belum ada data anamnesis
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

const props = defineProps({
  idPelayanan: [String, Number],
  refs: Object,
  DataAnamnesaRanap: Object,
  alergi: Object,
});

const anamnesisList = ref([]);
const isSaving = ref(false);

const anamnesisForm = ref({

  tglAnamnesis: new Date().toISOString().slice(0, 16),
  kdSadar: '',
  tinggiBadan: '',
  beratBadan: '',
  lingkarPerut: '',
  imt: '',
  imtKet: '',
  sistole: '',
  diastole: '',
  respRate: '',
  heartRate: '',
  suhu: '',
  keluhan: '',
  thoraxJantung: '',
  thoraxJantungKet: '',
  thoraxPulmo: '',
  thoraxPulmoKet: '',
  abdomanAtas: '',
  abdomanAtasKet: '',
  abdomanBawah: '',
  abdomanBawahKet: '',
  extrimitasAtas: '',
  extrimitasAtasKet: '',
  extrimitasBawah: '',
  extrimitasBawahKet: '',
  alergiMakanan: props.alergi?.namaAlergiMakanan || '',
  alergiObat: props.alergi?.namaAlergiObat || '',
  keteranganAlergi: props.alergi?.keterangan || '',
  tenagaMedisAskep: '',
});

const calculateIMT = () => {
  const bb = parseFloat(anamnesisForm.value.beratBadan);
  const tb = parseFloat(anamnesisForm.value.tinggiBadan);
  if (bb && tb) {
    const imt = (bb / ((tb / 100) ** 2)).toFixed(2);
    anamnesisForm.value.imt = imt;
    if (imt < 18.5) anamnesisForm.value.imtKet = 'Berat Badan Kurang';
    else if (imt >= 18.5 && imt <= 22.9) anamnesisForm.value.imtKet = 'Berat Badan Normal';
    else if (imt >= 23 && imt <= 24.9) anamnesisForm.value.imtKet = 'Kelebihan Berat Badan';
    else if (imt >= 25 && imt <= 29.9) anamnesisForm.value.imtKet = 'Obes I';
    else anamnesisForm.value.imtKet = 'Obes II';
  }
};

const toggleKet = (n) => {
  // Logic to show/hide keterangan based on selection, but since we use v-if, no need for manual toggle
};

const resetForm = () => {
  anamnesisForm.value = {
    tglAnamnesis: new Date().toISOString().slice(0, 16),
    kdSadar: '',
    tinggiBadan: '',
    beratBadan: '',
    lingkarPerut: '',
    imt: '',
    imtKet: '',
    sistole: '',
    diastole: '',
    respRate: '',
    heartRate: '',
    suhu: '',
    keluhan: '',
    thoraxJantung: '',
    thoraxJantungKet: '',
    thoraxPulmo: '',
    thoraxPulmoKet: '',
    abdomanAtas: '',
    abdomanAtasKet: '',
    abdomanBawah: '',
    abdomanBawahKet: '',
    extrimitasAtas: '',
    extrimitasAtasKet: '',
    extrimitasBawah: '',
    extrimitasBawahKet: '',
    alergiMakanan: props.alergi?.namaAlergiMakanan || '',
    alergiObat: props.alergi?.namaAlergiObat || '',
    keteranganAlergi: props.alergi?.keterangan || '',
    tenagaMedisAskep: '',
  };
};

const saveAnamnesis = async () => {
  isSaving.value = true;
  try {
    const payload = {
      idKunjungan: props.idPelayanan,
      ...anamnesisForm.value,
      tglAnamnesis: anamnesisForm.value.tglAnamnesis.replace('T', ' ') + ':00',
    };
    await axios.post(route('ruang-layanan.ranap.care.anamnesis'), payload);
    alert('Anamnesis berhasil disimpan!');
    resetForm();
    loadAnamnesisList();
  } catch (e) {
    console.error('Gagal simpan anamnesis', e);
    alert(e?.response?.data?.message || 'Gagal menyimpan anamnesis');
  } finally {
    isSaving.value = false;
  }
};


const loadAnamnesisList = async () => {
  try {
    const response = await axios.get(route('ruang-layanan.ranap.anamnesis.list') + '?idKunjungan=' + props.idPelayanan);
    anamnesisList.value = response.data;
  } catch (e) {
    console.error('Gagal load list anamnesis', e);
  }
};



const deleteAnamnesis = async (id) => {
  if (confirm('Apa anda yakin ingin menghapus anamnesis ini?')) {
    try {
      await axios.delete(route('ruang-layanan.ranap.anamnesis.delete', { idAnamnesis: id }));
      alert('Anamnesis dihapus');
      loadAnamnesisList();
    } catch (e) {
      console.error('Gagal hapus anamnesis', e);
      alert('Gagal hapus anamnesis');
    }
  }
};


onMounted(() => {
  loadAnamnesisList();
});
</script>

<style scoped>
</style>
