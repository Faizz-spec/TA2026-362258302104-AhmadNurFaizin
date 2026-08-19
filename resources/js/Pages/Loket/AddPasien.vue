<template>
  <AppLayouts>
    <div class="loket-page">
      <div class="loket-shell">
        <div class="page-toolbar">
          <div>
            <p class="page-kicker">Loket pendaftaran</p>
            <h1 class="page-title">Tambah Data Pasien</h1>
          </div>
          <div class="page-actions">
            <Link :href="route('loket.search')" class="btn btn-outline-success">
              <i class="bi bi-arrow-left"></i>
              <span>Kembali</span>
            </Link>
          </div>
        </div>

        <form class="loket-card" @submit.prevent="submitForm">
          <div class="loket-card-header">
            <div>
              <h2><i class="bi bi-person-plus"></i> Identitas Pasien Baru</h2>
              <p>Lengkapi data kependudukan, keluarga, alamat, dan kontak pasien.</p>
            </div>
            <span class="status-pill" :class="{ complete: formValid }">
              {{ formValid ? 'Siap disimpan' : 'Data wajib belum lengkap' }}
            </span>
          </div>

          <div class="loket-card-body">
            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-card-checklist"></i>
                <div>
                  <h3>Data Utama</h3>
                  <p>Identitas dasar pasien dan informasi jaminan kesehatan.</p>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">NIK <span class="required">*</span></label>
                  <div class="input-group">
                    <input
                      v-model="form.NIK"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.NIK }"
                      maxlength="16"
                      required
                    />
                    <button
                      type="button"
                      class="btn btn-soft-primary"
                      @click="cekNik"
                      :disabled="!form.NIK"
                    >
                      <i class="bi bi-search"></i>
                      <span>Cek</span>
                    </button>
                    <div v-if="form.errors.NIK" class="invalid-feedback">
                      {{ form.errors.NIK }}
                    </div>
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">No. BPJS</label>
                  <div class="input-group">
                    <input
                      v-model="form.noKartu"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.noKartu }"
                    />
                    <button type="button" class="btn btn-soft-primary" :disabled="!form.noKartu">
                      <i class="bi bi-shield-check"></i>
                      <span>Cek</span>
                    </button>
                    <div v-if="form.errors.noKartu" class="invalid-feedback">
                      {{ form.errors.noKartu }}
                    </div>
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                  <input
                    v-model="form.NAMA_LGKP"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.NAMA_LGKP }"
                    required
                  />
                  <div v-if="form.errors.NAMA_LGKP" class="invalid-feedback">
                    {{ form.errors.NAMA_LGKP }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">kdProvider</label>
                  <input
                    v-model="form.kdProvider"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.kdProvider }"
                  />
                  <div v-if="form.errors.kdProvider" class="invalid-feedback">
                    {{ form.errors.kdProvider }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                  <input
                    v-model="form.TMPT_LHR"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.TMPT_LHR }"
                    required
                  />
                  <div v-if="form.errors.TMPT_LHR" class="invalid-feedback">
                    {{ form.errors.TMPT_LHR }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                  <input
                    v-model="form.TGL_LHR"
                    type="date"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.TGL_LHR }"
                    required
                  />
                  <div v-if="form.errors.TGL_LHR" class="invalid-feedback">
                    {{ form.errors.TGL_LHR }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                  <select
                    v-model.number="form.JENIS_KLMIN"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.JENIS_KLMIN }"
                    required
                  >
                    <option value="1">Laki-laki</option>
                    <option value="2">Perempuan</option>
                  </select>
                  <div v-if="form.errors.JENIS_KLMIN" class="invalid-feedback">
                    {{ form.errors.JENIS_KLMIN }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Jenis Pekerjaan</label>
                  <select
                    v-model.number="form.JENIS_PKRJN"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.JENIS_PKRJN }"
                  >
                    <option value="">- Pilih Pekerjaan -</option>
                    <option
                      v-for="pekerjaan in pekerjaanList"
                      :key="pekerjaan.NO"
                      :value="pekerjaan.NO"
                    >
                      {{ pekerjaan.DESCRIP }}
                    </option>
                  </select>
                  <div v-if="form.errors.JENIS_PKRJN" class="invalid-feedback">
                    {{ form.errors.JENIS_PKRJN }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Agama <span class="required">*</span></label>
                  <select
                    v-model="form.AGAMA"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.AGAMA }"
                    required
                  >
                    <option value="">- Pilih Agama -</option>
                    <option v-for="agama in agamaList" :key="agama.NO" :value="agama.NO">
                      {{ agama.DESCRIP }}
                    </option>
                  </select>
                  <div v-if="form.errors.AGAMA" class="invalid-feedback">
                    {{ form.errors.AGAMA }}
                  </div>
                </div>
              </div>
            </section>

            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-people"></i>
                <div>
                  <h3>Data Keluarga</h3>
                  <p>Nomor keluarga, hubungan keluarga, dan identitas SATUSEHAT.</p>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">IHS Pasien</label>
                  <input
                    v-model="form.IHS_NUMBER"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.IHS_NUMBER }"
                  />
                  <div v-if="form.errors.IHS_NUMBER" class="invalid-feedback">
                    {{ form.errors.IHS_NUMBER }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">No. KK</label>
                  <input
                    v-model="form.NO_KK"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.NO_KK }"
                  />
                  <div v-if="form.errors.NO_KK" class="invalid-feedback">
                    {{ form.errors.NO_KK }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Nama KK</label>
                  <input
                    v-model="form.NAMA_LGKP_AYAH"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.NAMA_LGKP_AYAH }"
                  />
                  <div v-if="form.errors.NAMA_LGKP_AYAH" class="invalid-feedback">
                    {{ form.errors.NAMA_LGKP_AYAH }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label"
                    >Hubungan Keluarga <span class="required">*</span></label
                  >
                  <select
                    v-model.number="form.STAT_HBKEL"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.STAT_HBKEL }"
                    required
                  >
                    <option value="">- Pilih Hubungan -</option>
                    <option
                      v-for="hubungan in hubunganKeluargaList"
                      :key="hubungan.KODE"
                      :value="hubungan.KODE"
                    >
                      {{ hubungan.KET }}
                    </option>
                  </select>
                  <div v-if="form.errors.STAT_HBKEL" class="invalid-feedback">
                    {{ form.errors.STAT_HBKEL }}
                  </div>
                </div>
              </div>
            </section>

            <section class="form-section address-section">
              <div class="section-heading">
                <i class="bi bi-geo-alt"></i>
                <div>
                  <h3>Alamat & Kontak</h3>
                  <p>Wilayah domisili, alamat lengkap, RT/RW, dan nomor telepon.</p>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Provinsi <span class="required">*</span></label>
                  <select
                    v-model="form.NO_PROP"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.NO_PROP }"
                    required
                  >
                    <option value="">- Pilih Provinsi -</option>
                    <option v-for="prov in provinsiList" :key="prov.NO_PROP" :value="prov.NO_PROP">
                      {{ prov.NAMA_PROP }}
                    </option>
                  </select>
                  <div v-if="form.errors.NO_PROP" class="invalid-feedback">
                    {{ form.errors.NO_PROP }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Kabupaten <span class="required">*</span></label>
                  <select
                    v-model="form.NO_KAB"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.NO_KAB }"
                    :disabled="!form.NO_PROP || kabupatenList.length === 0"
                    required
                  >
                    <option value="">- Pilih Kabupaten -</option>
                    <option v-for="kab in kabupatenList" :key="kab.NO_KAB" :value="kab.NO_KAB">
                      {{ kab.NAMA_KAB }}
                    </option>
                  </select>
                  <div v-if="form.NO_PROP && kabupatenList.length === 0" class="field-hint danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Loading data kabupaten.
                  </div>
                  <div v-if="form.errors.NO_KAB" class="invalid-feedback">
                    {{ form.errors.NO_KAB }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Kecamatan <span class="required">*</span></label>
                  <select
                    v-model="form.NO_KEC"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.NO_KEC }"
                    :disabled="!form.NO_KAB || kecamatanList.length === 0"
                    required
                  >
                    <option value="">- Pilih Kecamatan -</option>
                    <option v-for="kec in kecamatanList" :key="kec.NO_KEC" :value="kec.NO_KEC">
                      {{ kec.NAMA_KEC }}
                    </option>
                  </select>
                  <div v-if="form.errors.NO_KEC" class="invalid-feedback">
                    {{ form.errors.NO_KEC }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Desa/Kelurahan <span class="required">*</span></label>
                  <select
                    v-model="form.NO_KEL"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.NO_KEL }"
                    :disabled="!form.NO_KEC || kelurahanList.length === 0"
                    required
                  >
                    <option value="">- Pilih Kelurahan -</option>
                    <option v-for="kel in kelurahanList" :key="kel.NO_KEL" :value="kel.NO_KEL">
                      {{ kel.NAMA_KEL }}
                    </option>
                  </select>
                  <div v-if="form.NO_KEC && kelurahanList.length === 0" class="field-hint danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Loading data kelurahan.
                  </div>
                  <div v-if="form.errors.NO_KEL" class="invalid-feedback">
                    {{ form.errors.NO_KEL }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Alamat <span class="required">*</span></label>
                  <textarea
                    v-model="form.ALAMAT"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.ALAMAT }"
                    rows="2"
                    required
                  ></textarea>
                  <div v-if="form.errors.ALAMAT" class="invalid-feedback">
                    {{ form.errors.ALAMAT }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">RT/RW <span class="required">*</span></label>
                  <div class="input-pair">
                    <input
                      v-model.number="form.NO_RT"
                      type="number"
                      class="form-control"
                      placeholder="RT"
                      :class="{ 'is-invalid': form.errors.NO_RT }"
                      required
                    />
                    <input
                      v-model.number="form.NO_RW"
                      type="number"
                      class="form-control"
                      placeholder="RW"
                      :class="{ 'is-invalid': form.errors.NO_RW }"
                      required
                    />
                  </div>
                  <div v-if="form.errors.NO_RT" class="invalid-feedback d-block">
                    {{ form.errors.NO_RT }}
                  </div>
                  <div v-if="form.errors.NO_RW" class="invalid-feedback d-block">
                    {{ form.errors.NO_RW }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">HP <span class="required">*</span></label>
                  <input
                    v-model="form.PHONE"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.PHONE }"
                    required
                  />
                  <div v-if="form.errors.PHONE" class="invalid-feedback">
                    {{ form.errors.PHONE }}
                  </div>
                </div>
              </div>
            </section>
          </div>

          <div class="form-footer">
            <button type="button" class="btn btn-outline-secondary" @click="resetForm">
              <i class="bi bi-arrow-counterclockwise"></i>
              <span>Reset</span>
            </button>
            <button
              type="submit"
              class="btn btn-success btn-save"
              :disabled="form.processing"
            >
              <span
                v-if="form.processing"
                class="spinner-border spinner-border-sm"
              ></span>
              <i v-else class="bi bi-check-circle"></i>
              <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Pasien' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '@/Components/Layouts/AppLayouts.vue';
  import { Link, useForm } from '@inertiajs/vue3';
  import { computed, onMounted, ref, watch } from 'vue';
  import axios from 'axios';

  const form = useForm({
    NIK: '',
    noKartu: '',
    kdProvider: '',
    NAMA_LGKP: '',
    JENIS_KLMIN: 1,
    TMPT_LHR: '',
    TGL_LHR: '',
    JENIS_PKRJN: null,
    AGAMA: '',
    STAT_HBKEL: null,
    NO_KK: '',
    NAMA_LGKP_AYAH: '',
    NO_PROP: '35',
    NO_KAB: '',
    NO_KEC: '',
    NO_KEL: '',
    ALAMAT: '',
    NO_RT: null,
    NO_RW: null,
    PHONE: '',
    IHS_NUMBER: '',
  });

  const provinsiList = ref([]);
  const kabupatenList = ref([]);
  const kecamatanList = ref([]);
  const kelurahanList = ref([]);

  const formValid = computed(
    () =>
      Boolean(form.NIK) &&
      Boolean(form.NAMA_LGKP) &&
      Boolean(form.JENIS_KLMIN) &&
      Boolean(form.TMPT_LHR) &&
      Boolean(form.TGL_LHR) &&
      Boolean(form.AGAMA) &&
      Boolean(form.STAT_HBKEL) &&
      Boolean(form.NO_PROP) &&
      Boolean(form.NO_KAB) &&
      Boolean(form.NO_KEC) &&
      Boolean(form.NO_KEL) &&
      Boolean(form.ALAMAT) &&
      form.NO_RT !== null &&
      form.NO_RT !== '' &&
      form.NO_RW !== null &&
      form.NO_RW !== '' &&
      Boolean(form.PHONE)
  );

  const loadProvinsi = async () => {
    try {
      const response = await axios.get(route('loket.api.provinsi'));

      provinsiList.value = response.data;
    } catch (error) {
      console.error('Error load provinsi', error);
    }
  };

  const loadKabupaten = async () => {
    if (!form.NO_PROP) {
      kabupatenList.value = [];
      return;
    }

    try {
      const response = await axios.get(`/wilayah/kabupaten/${form.NO_PROP}`);

      kabupatenList.value = response.data;
    } catch (error) {
      console.error('Error load kabupaten', error);

      kabupatenList.value = [];
    }
  };

  const loadKecamatan = async () => {
    if (!form.NO_PROP || !form.NO_KAB) {
      kecamatanList.value = [];
      return;
    }

    try {
      const response = await axios.get(`/wilayah/kecamatan/${form.NO_PROP}/${form.NO_KAB}`);

      kecamatanList.value = response.data;
    } catch (error) {
      console.error('Error load kecamatan', error);

      kecamatanList.value = [];
    }
  };

  const loadKelurahan = async () => {
    if (!form.NO_PROP || !form.NO_KAB || !form.NO_KEC) {
      kelurahanList.value = [];
      return;
    }

    try {
      const response = await axios.get(
        `/wilayah/kelurahan/${form.NO_PROP}/${form.NO_KAB}/${form.NO_KEC}`
      );

      kelurahanList.value = response.data;
    } catch (error) {
      console.error('Error load kelurahan', error);

      kelurahanList.value = [];
    }
  };

  watch(
    () => form.NO_PROP,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.NO_KAB = '';
        form.NO_KEC = '';
        form.NO_KEL = '';

        kecamatanList.value = [];
        kelurahanList.value = [];
      }

      await loadKabupaten();
    }
  );

  watch(
    () => form.NO_KAB,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.NO_KEC = '';
        form.NO_KEL = '';

        kelurahanList.value = [];
      }

      await loadKecamatan();
    }
  );

  watch(
    () => form.NO_KEC,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.NO_KEL = '';
      }

      await loadKelurahan();
    }
  );

  onMounted(async () => {
    await loadProvinsi();

    if (form.NO_PROP) {
      await loadKabupaten();
    }

    if (form.NO_KAB) {
      await loadKecamatan();
    }

    if (form.NO_KEC) {
      await loadKelurahan();
    }
  });

  defineProps({
    hubunganKeluargaList: {
      type: Array,
      required: true,
      default: () => [],
    },

    agamaList: {
      type: Array,
      required: true,
      default: () => [],
    },

    pekerjaanList: {
      type: Array,
      required: true,
      default: () => [],
    },
  });

  function cekNik() {
    if (!form.NIK) {
      alert('Silakan masukkan NIK terlebih dahulu');
      return;
    }
    alert('Fungsi cek NIK akan diimplementasikan');
  }

  async function resetForm() {
    form.reset();

    form.NO_PROP = '';
    form.NO_KAB = '';
    form.NO_KEC = '';
    form.NO_KEL = '';

    kabupatenList.value = [];
    kecamatanList.value = [];
    kelurahanList.value = [];
  }

  function submitForm() {
    form.post(route('loket.pasien.store'));
  }
</script>

<style scoped>
  .loket-page {
    min-height: 100%;
    background: #f4f7fb;
    padding: 24px;
  }

  .loket-shell {
    max-width: 1320px;
    margin: 0 auto;
  }

  .page-toolbar,
  .loket-card-header,
  .form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .page-toolbar {
    margin-bottom: 18px;
  }

  .page-kicker {
    margin: 0 0 4px;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .page-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.65rem;
    font-weight: 750;
  }

  .page-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .loket-card {
    overflow: hidden;
    border: 1px solid #d9e5df;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
  }

  .loket-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e5edf0;
    background: #ffffff;
  }

  .loket-card-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: #0f3d2e;
    font-size: 1.05rem;
    font-weight: 750;
  }

  .loket-card-header p {
    margin: 5px 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }

  .loket-card-body {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    padding: 22px;
  }

  .form-section {
    padding: 18px;
    border: 1px solid #e3ebef;
    border-radius: 8px;
    background: #fbfdff;
  }

  .address-section {
    grid-column: 1 / -1;
  }

  .section-heading {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
  }

  .section-heading > i {
    display: grid;
    flex: 0 0 36px;
    width: 36px;
    height: 36px;
    place-items: center;
    border-radius: 8px;
    background: #e7f5ef;
    color: #08734f;
    font-size: 1.05rem;
  }

  .section-heading h3 {
    margin: 0;
    color: #1e293b;
    font-size: 0.98rem;
    font-weight: 750;
  }

  .section-heading p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 0.84rem;
  }

  .form-label {
    margin-bottom: 6px;
    color: #334155;
    font-size: 0.86rem;
    font-weight: 600;
  }

  .form-control,
  .form-select {
    min-height: 42px;
    border: 1px solid #cfd9e3;
    border-radius: 8px;
    color: #0f172a;
    transition:
      border-color 0.15s ease-in-out,
      box-shadow 0.15s ease-in-out;
  }

  textarea.form-control {
    min-height: 86px;
    resize: vertical;
  }

  .form-control:disabled,
  .form-select:disabled {
    background-color: #f1f5f9;
    color: #64748b;
    opacity: 1;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #16a36f;
    box-shadow: 0 0 0 0.2rem rgba(22, 163, 111, 0.14);
  }

  .input-pair {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 8px;
    font-weight: 650;
    transition: all 0.15s ease-in-out;
  }

  .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  }

  .btn-soft-primary {
    border-color: #bfdbea;
    background: #edf8fc;
    color: #09637b;
  }

  .btn-save {
    min-width: 170px;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #fff7ed;
    color: #9a3412;
    font-size: 0.8rem;
    font-weight: 750;
  }

  .status-pill.complete {
    background: #dcfce7;
    color: #166534;
  }

  .required {
    color: #dc2626;
    font-weight: 800;
  }

  .invalid-feedback {
    font-size: 0.8rem;
    font-weight: 600;
  }

  .field-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    color: #64748b;
    font-size: 0.82rem;
    font-weight: 650;
  }

  .field-hint.danger {
    color: #b91c1c;
  }

  .form-footer {
    justify-content: flex-end;
    padding: 18px 22px;
    border-top: 1px solid #e5edf0;
    background: #f8fafc;
  }

  @media (max-width: 992px) {
    .loket-card-body {
      grid-template-columns: 1fr;
    }

    .form-section {
      grid-column: 1 / -1;
    }
  }

  @media (max-width: 768px) {
    .loket-page {
      padding: 16px;
    }

    .loket-card-body {
      padding: 16px;
    }

    .form-section {
      padding: 16px;
    }

    .page-actions,
    .page-actions .btn,
    .form-footer .btn {
      width: 100%;
    }

    .form-footer {
      justify-content: stretch;
      padding: 16px;
    }
  }
</style>
