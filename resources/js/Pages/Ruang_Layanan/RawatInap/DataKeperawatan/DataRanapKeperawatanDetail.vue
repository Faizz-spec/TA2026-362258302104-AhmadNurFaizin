<template>
  <AppLayouts>
    <div class="card m-4  rounded-4 rounded-bottom-0">
      <div class="card-header bg-info d-flex justify-content-between p-3  rounded-4 rounded-bottom-0"
        style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="fs-5 text-white">BP RAWAT INAP</h1>
        <Link :href="route('ruang-layanan.rawat-inap.perawatan')" class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
          <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
        </Link>
      </div>
      <div class="card-body">
        <PelayananPasien
          @ubah-melayani="isMelayani = $event"
          :dataPasien="dataPasienForWrapper"
          :dataAnamnesa="DataAnamnesaRanap"
          :id-pelayanan="idPelayanan"
          :pelayanan="pelayanan"
        >
          <div class="shadow-sm rounded-5">
            <NavigasiFormPemeriksaan :currentTab="currentTab" @change-currentTab="currentTab = $event" />

            <div class="m-4 pb-4 row gx-5">
              <!-- anamnesis => Anamnesa ranap -->
              <div v-if="currentTab === 'anamnesis'" class="col-12">
                <FormAnamnesisRanap :idPelayanan="idPelayanan" :refs="refs" :DataAnamnesaRanap="DataAnamnesaRanap" :alergi="alergi" />
              </div>

              <!-- objective (placeholder) -->
              <div v-if="currentTab === 'objective'" class="col-12">
                <div class="alert alert-info">Form Objective belum tersedia untuk Rawat Inap.</div>
              </div>

              <!-- assesment => Diagnosa ranap -->
              <div v-if="currentTab === 'assesment'" class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <h6 class="mb-0">Diagnosa</h6>
                  </div>
                  <div class="card-body">
                    <form class="mb-3" @submit.prevent="saveDiagnosis">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Tanggal Diagnosa</label>
                            <input type="datetime-local" class="form-control" v-model="diagnosisForm.tglDiagnosa" />
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="mb-3">
                            <label class="form-label">Jenis Kasus Diagnosa</label>
                            <select class="form-select" v-model="diagnosisForm.diagnosaKasus" required>
                              <option value="">- Pilih -</option>
                              <option v-for="c in refs.diagnosaKasus" :key="c.id" :value="c.id">{{ c.kasus }}</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" v-model="diagnosisForm.keterangan" rows="2"></textarea>
                      </div>
                      <button type="submit" class="btn btn-primary">Simpan Diagnosa</button>
                    </form>

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Tanggal</th>
                          <th>Jenis Kasus</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="diagnosa in SimpusDataDiagnosa" :key="diagnosa.idDiagnosa">
                          <td>{{ diagnosa.tglDiagnosa || '-' }}</td>
                          <td>{{ diagnosa.kasus || diagnosa.nmDiagnosa || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- planning => Tindakan ranap -->
              <div v-if="currentTab === 'planning'" class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <h6 class="mb-0">Tindakan</h6>
                  </div>
                  <div class="card-body">
                    <form class="mb-3" @submit.prevent="saveProcedure">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Tanggal Tindakan</label>
                            <input type="datetime-local" class="form-control" v-model="procedureForm.tglTindakan" />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Kode Tindakan</label>
                            <input type="text" class="form-control" v-model="procedureForm.kodeTindakan" placeholder="Opsional" />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Uraian Tindakan</label>
                            <input type="text" class="form-control" v-model="procedureForm.uraianTindakan" />
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Simpan Tindakan</button>
                    </form>

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Tanggal</th>
                          <th>Kode</th>
                          <th>Uraian</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="tindakan in SimpusTindakan" :key="tindakan.idTindakan">
                          <td>{{ tindakan.tglTindakan || '-' }}</td>
                          <td>{{ tindakan.kodeTindakan || tindakan.kdTindakan || '-' }}</td>
                          <td>{{ tindakan.uraianTindakan || tindakan.nmTindakan || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- status_pasien (placeholder) -->
              <div v-if="currentTab === 'status_pasien'" class="col-12">
                <div class="alert alert-info">Status Pasien (discharge) tidak dibahas pada Tahap 2.</div>
              </div>

              <!-- visit => Visit ranap -->
              <div v-if="currentTab === 'visit'" class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <h6 class="mb-0">Visit Dokter</h6>
                  </div>
                  <div class="card-body">
                    <form class="mb-3" @submit.prevent="saveVisit">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Tanggal Visit</label>
                            <input type="datetime-local" class="form-control" v-model="visitForm.tglVisit" />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Dokter</label>
                            <select class="form-select" v-model="visitForm.kdDokter" required>
                              <option value="">- Pilih -</option>
                              <option v-for="d in refs.dokter" :key="d.kdDokter" :value="d.kdDokter">{{ d.nmDokter }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" class="form-control" v-model="visitForm.catatanVisit" />
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Simpan Visit</button>
                    </form>

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Tanggal</th>
                          <th>Dokter</th>
                          <th>Catatan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="visit in SimpusVisit" :key="visit.idVisit">
                          <td>{{ visit.tglVisit || visit.tanggalVisit || '-' }}</td>
                          <td>{{ visit.kdDokter || '-' }}</td>
                          <td>{{ visit.catatanVisit || visit.instruksi || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </PelayananPasien>
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
  import { Link, useForm } from '@inertiajs/vue3';
  import axios from 'axios';
  import { ref, onMounted, computed } from 'vue';
  import { route } from 'ziggy-js';
  import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue';
  import PelayananPasien from '../../../../Components/Layouts/RuangLayanan/PelayananPasien/PelayananPasien.vue';
  import NavigasiFormPemeriksaan from '../../../../Components/Layouts/RuangLayanan/RawatInap/NavigasiFormPemeriksaan.vue';
  import FormAnamnesisRanap from '../../../../Components/Layouts/RuangLayanan/RawatInap/FormAnamnesisRanap.vue';

  const props = defineProps({
    DataPasien: Object,
    DataRanap: Object,
    DataAnamnesaRanap: Object,
    SimpusDataDiagnosa: Array,
    SimpusTindakan: Array,
    SimpusObatPakai: Array,
    SimpusVisit: Array,
    idPelayanan: [String, Number],
    kdPoli: String,
  });

  const dataPasienForWrapper = computed(() => {
    const src = props.DataPasien || {};
    const ID = src.ID ?? src.id ?? src.pasienId ?? src.idPasien ?? null;
    return {
      ...src,
      ID,
      kdPoli: src.kdPoli || '098',
    };
  });

  const isMelayani = ref(true); // tampilkan slot langsung
  const currentTab = ref('anamnesis');
  const pelayanan = ref({ sudahDilayani: 1 });

  const refs = ref({ kesadaran: [], dokter: [], diagnosaKasus: [] });
  const alergi = ref({});

  const nowLocal = () => {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  };

  const diagnosisForm = ref({
    tglDiagnosa: nowLocal(),
    diagnosaKasus: '',
    keterangan: '',
  });

  const procedureForm = ref({
    tglTindakan: nowLocal(),
    kodeTindakan: '',
    uraianTindakan: '',
  });

  const visitForm = ref({
    tglVisit: nowLocal(),
    kdDokter: '',
    catatanVisit: '',
  });

  async function loadRefs() {
    try {
      const [r1, r2, r3] = await Promise.all([
        axios.get(route('ruang-layanan.ranap.ref.kesadaran')),
        axios.get(route('ruang-layanan.ranap.ref.dokter')),
        axios.get(route('ruang-layanan.ranap.ref.diagnosa-kasus')),
      ]);
      refs.value.kesadaran = r1.data || [];
      refs.value.dokter = r2.data || [];
      refs.value.diagnosaKasus = r3.data || [];
    } catch (e) {
      console.error('Gagal load referensi:', e);
      refs.value = { kesadaran: [], dokter: [], diagnosaKasus: [] };
    }
  }

  async function loadAlergi() {
    try {
      const response = await axios.get(route('ruang-layanan.ranap.ref.alergi') + '?idPasien=' + props.DataPasien.ID);
      alergi.value = response.data || {};
    } catch (e) {
      console.error('Gagal load alergi:', e);
      alergi.value = {};
    }
  }



  async function saveDiagnosis() {
    try {
      const payload = {
        idKunjungan: props.idPelayanan,
        tglDiagnosa: (diagnosisForm.value.tglDiagnosa + ':00').replace('T',' '),
        diagnosaKasus: diagnosisForm.value.diagnosaKasus,
        keterangan: diagnosisForm.value.keterangan || null,
      };
      await axios.post(route('ruang-layanan.ranap.care.diagnosis'), payload);
      alert('Diagnosa tersimpan');
    } catch (e) {
      console.error('Gagal simpan diagnosa', e);
      alert(e?.response?.data?.message || 'Gagal simpan diagnosa');
    }
  }

  async function saveProcedure() {
    try {
      const payload = {
        idKunjungan: props.idPelayanan,
        tglTindakan: (procedureForm.value.tglTindakan + ':00').replace('T',' '),
        kodeTindakan: procedureForm.value.kodeTindakan || null,
        uraianTindakan: procedureForm.value.uraianTindakan,
      };
      await axios.post(route('ruang-layanan.ranap.care.procedure'), payload);
      alert('Tindakan tersimpan');
    } catch (e) {
      console.error('Gagal simpan tindakan', e);
      alert(e?.response?.data?.message || 'Gagal simpan tindakan');
    }
  }

  async function saveVisit() {
    try {
      const payload = {
        idKunjungan: props.idPelayanan,
        tglVisit: (visitForm.value.tglVisit + ':00').replace('T',' '),
        kdDokter: visitForm.value.kdDokter,
        catatanVisit: visitForm.value.catatanVisit || null,
      };
      await axios.post(route('ruang-layanan.ranap.care.visit'), payload);
      alert('Visit tersimpan');
    } catch (e) {
      console.error('Gagal simpan visit', e);
      alert(e?.response?.data?.message || 'Gagal simpan visit');
    }
  }

  onMounted(() => {
    loadRefs();
    loadAlergi();
  });

  function deleteDiagnosa(id) {
    console.log('Deleting diagnosa:', id);
  }

  function deleteTindakan(id) {
    console.log('Deleting tindakan:', id);
  }

  function deleteObat(id) {
    console.log('Deleting obat:', id);
  }

  function deleteVisit(id) {
    console.log('Deleting visit:', id);
  }
</script>

<style scoped>
</style>
