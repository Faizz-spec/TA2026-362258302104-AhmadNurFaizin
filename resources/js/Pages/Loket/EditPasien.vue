<template>
  <AppLayouts>
    <div class="loket-page">
      <div class="loket-shell">
        <div class="page-toolbar">
          <div>
            <p class="page-kicker">Loket pendaftaran</p>
            <h1 class="page-title">Edit Data Pasien</h1>
          </div>
          <div class="page-actions">
            <Link :href="route('loket.search')" class="btn btn-outline-success">
              <i class="bi bi-arrow-left"></i>
              <span>Kembali</span>
            </Link>
          </div>
        </div>

        <form class="loket-card" @submit.prevent="submit">
          <div class="loket-card-header">
            <div>
              <h2><i class="bi bi-person-vcard"></i> Identitas Pasien</h2>
              <p>Perbarui data kependudukan, kontak, dan alamat pasien.</p>
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
                      v-model="form.nik"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.nik }"
                      maxlength="16"
                    />
                    <button type="button" class="btn btn-soft-primary">
                      <i class="bi bi-search"></i>
                      <span>Cek</span>
                    </button>
                    <div v-if="form.errors.nik" class="invalid-feedback">
                      {{ form.errors.nik }}
                    </div>
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">No. BPJS</label>
                  <div class="input-group">
                    <input
                      v-model="form.no_kartu"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.no_kartu }"
                    />
                    <button type="button" class="btn btn-soft-primary">
                      <i class="bi bi-shield-check"></i>
                      <span>Cek</span>
                    </button>
                    <div v-if="form.errors.no_kartu" class="invalid-feedback">
                      {{ form.errors.no_kartu }}
                    </div>
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                  <input
                    v-model="form.nama"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.nama }"
                  />
                  <div v-if="form.errors.nama" class="invalid-feedback">
                    {{ form.errors.nama }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">kdProvider</label>
                  <input
                    v-model="form.provider"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.provider }"
                  />
                  <div v-if="form.errors.provider" class="invalid-feedback">
                    {{ form.errors.provider }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Tempat Lahir</label>
                  <input
                    v-model="form.tempat_lahir"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.tempat_lahir }"
                  />
                  <div v-if="form.errors.tempat_lahir" class="invalid-feedback">
                    {{ form.errors.tempat_lahir }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                  <input
                    v-model="form.tanggal_lahir"
                    type="date"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.tanggal_lahir }"
                  />
                  <div v-if="form.errors.tanggal_lahir" class="invalid-feedback">
                    {{ form.errors.tanggal_lahir }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Jenis Kelamin</label>
                  <select
                    v-model="form.jenis_kelamin"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.jenis_kelamin }"
                  >
                    <option value="1">Laki-laki</option>
                    <option value="2">Perempuan</option>
                  </select>
                  <div v-if="form.errors.jenis_kelamin" class="invalid-feedback">
                    {{ form.errors.jenis_kelamin }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Jenis Pekerjaan</label>
                  <select
                    v-model="form.jenis_pkrjn"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.jenis_pkrjn }"
                  >
                    <option value="">- Pilih Pekerjaan -</option>
                    <option v-for="pkrjn in pekerjaan" :key="pkrjn.NO" :value="pkrjn.NO">
                      {{ pkrjn.DESCRIP }}
                    </option>
                  </select>
                  <div v-if="form.errors.jenis_pkrjn" class="invalid-feedback">
                    {{ form.errors.jenis_pkrjn }}
                  </div>
                </div>

                <div class="col-lg-4">
                  <label class="form-label">Agama</label>
                  <select
                    v-model="form.agama"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.agama }"
                  >
                    <option value="">- Pilih Agama -</option>
                    <option v-for="agm in agama" :key="agm.NO" :value="agm.DESCRIP">
                      {{ agm.DESCRIP }}
                    </option>
                  </select>
                  <div v-if="form.errors.agama" class="invalid-feedback">
                    {{ form.errors.agama }}
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
                    v-model="form.ihs_pasien"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.ihs_pasien }"
                  />
                  <div v-if="form.errors.ihs_pasien" class="invalid-feedback">
                    {{ form.errors.ihs_pasien }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">No. KK</label>
                  <input
                    v-model="form.no_kk"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.no_kk }"
                  />
                  <div v-if="form.errors.no_kk" class="invalid-feedback">
                    {{ form.errors.no_kk }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Nama KK</label>
                  <input
                    v-model="form.nama_kk"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.nama_kk }"
                  />
                  <div v-if="form.errors.nama_kk" class="invalid-feedback">
                    {{ form.errors.nama_kk }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Hubungan Keluarga</label>
                  <select
                    v-model="form.hub_family"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.hub_family }"
                  >
                    <option value="">- Pilih Hubungan -</option>
                    <option v-for="fam in hubKeluarga" :key="fam.KODE" :value="fam.KODE">
                      {{ fam.KET }}
                    </option>
                  </select>
                  <div v-if="form.errors.hub_family" class="invalid-feedback">
                    {{ form.errors.hub_family }}
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
                    v-model="form.provinsi"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.provinsi }"
                  >
                    <option value="">- Pilih Provinsi -</option>
                    <option v-for="prop in provinsi" :key="prop.NO_PROP" :value="prop.NO_PROP">
                      {{ prop.NAMA_PROP }}
                    </option>
                  </select>
                  <div v-if="form.errors.provinsi" class="invalid-feedback">
                    {{ form.errors.provinsi }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Kabupaten <span class="required">*</span></label>
                  <select
                    v-model="form.kabupaten"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.kabupaten }"
                    :disabled="!form.provinsi || listKabupaten.length === 0"
                  >
                    <option value="">- Pilih Kabupaten -</option>
                    <option v-for="kab in listKabupaten" :key="kab.NO_KAB" :value="kab.NO_KAB">
                      {{ kab.NAMA_KAB }}
                    </option>
                  </select>
                  <div v-if="form.errors.kabupaten" class="invalid-feedback">
                    {{ form.errors.kabupaten }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Kecamatan <span class="required">*</span></label>
                  <select
                    v-model="form.kecamatan"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.kecamatan }"
                    :disabled="!form.kabupaten || listKecamatan.length === 0"
                  >
                    <option value="">- Pilih Kecamatan -</option>
                    <option v-for="kec in listKecamatan" :key="kec.NO_KEC" :value="kec.NO_KEC">
                      {{ kec.NAMA_KEC }}
                    </option>
                  </select>
                  <div v-if="form.errors.kecamatan" class="invalid-feedback">
                    {{ form.errors.kecamatan }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">Desa/Kelurahan <span class="required">*</span></label>
                  <select
                    v-model="form.kelurahan"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.kelurahan }"
                    :disabled="!form.kecamatan || listKelurahan.length === 0"
                  >
                    <option value="">- Pilih Desa/Kelurahan -</option>
                    <option v-for="kel in listKelurahan" :key="kel.NO_KEL" :value="kel.NO_KEL">
                      {{ kel.NAMA_KEL }}
                    </option>
                  </select>
                  <div v-if="form.errors.kelurahan" class="invalid-feedback">
                    {{ form.errors.kelurahan }}
                  </div>
                </div>

                <div class="col-lg-6">
                  <label class="form-label">Alamat <span class="required">*</span></label>
                  <textarea
                    v-model="form.alamat"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.alamat }"
                    rows="2"
                  ></textarea>
                  <div v-if="form.errors.alamat" class="invalid-feedback">
                    {{ form.errors.alamat }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">RT/RW</label>
                  <div class="input-pair">
                    <input
                      v-model="form.no_rt"
                      type="text"
                      class="form-control"
                      placeholder="RT"
                      :class="{ 'is-invalid': form.errors.no_rt }"
                    />
                    <input
                      v-model="form.no_rw"
                      type="text"
                      class="form-control"
                      placeholder="RW"
                      :class="{ 'is-invalid': form.errors.no_rw }"
                    />
                  </div>
                  <div v-if="form.errors.no_rt" class="invalid-feedback d-block">
                    {{ form.errors.no_rt }}
                  </div>
                  <div v-if="form.errors.no_rw" class="invalid-feedback d-block">
                    {{ form.errors.no_rw }}
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <label class="form-label">HP</label>
                  <input
                    v-model="form.phone"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.phone }"
                  />
                  <div v-if="form.errors.phone" class="invalid-feedback">
                    {{ form.errors.phone }}
                  </div>
                </div>
              </div>
            </section>
          </div>

          <div class="form-footer">
            <Link :href="route('loket.search')" class="btn btn-outline-secondary">
              <i class="bi bi-x-circle"></i>
              <span>Batal</span>
            </Link>
            <button type="submit" class="btn btn-success btn-save" :disabled="form.processing">
              <span v-if="form.processing" class="spinner-border spinner-border-sm"></span>
              <i v-else class="bi bi-check-circle"></i>
              <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '@/Components/Layouts/AppLayouts.vue';
  import { Link, router, useForm } from '@inertiajs/vue3';
  import axios from 'axios';
  import { ref, watch, onMounted, computed } from 'vue';

  const props = defineProps({
    pasien: Object,
    agama: Array,
    pekerjaan: Array,
    hubKeluarga: Array,
    kabupaten: Array,
    provinsi: Array,
    kecamatan: Array,
    kelurahan: Array,
  });

  const form = useForm({
    nama: props.pasien.NAMA_LGKP ?? '',
    nik: props.pasien.NIK ?? '',
    provinsi: props.pasien.NO_PROP ?? '',
    kabupaten: props.pasien.NO_KAB ?? '',
    kecamatan: props.pasien.NO_KEC ?? '',
    kelurahan: props.pasien.NO_KEL ?? '',
    alamat: props.pasien.ALAMAT ?? '',
    agama: props.pasien.AGAMA ?? '',
    provider: props.pasien.kdProvider ?? '',
    tanggal_lahir: props.pasien.TGL_LHR ?? '',
    tempat_lahir: props.pasien.TMPT_LHR ?? '',
    jenis_kelamin: props.pasien.JENIS_KLMIN ?? '',
    no_kk: props.pasien.NO_KK ?? '',
    nama_kk: props.pasien.NAMA_LGKP_AYAH ?? '',
    no_kartu: props.pasien.noKartu ?? '',
    no_rt: props.pasien.NO_RT ?? '',
    no_rw: props.pasien.NO_RW ?? '',
    phone: props.pasien.PHONE ?? '',
    jenis_pkrjn: props.pasien.JENIS_PKRJN ?? '',
    hub_family: props.pasien.STAT_HBKEL ?? '',
    ihs_pasien: props.pasien.IHS_NUMBER ?? '',
  });

  const listKabupaten = ref([]);
  const listKecamatan = ref([]);
  const listKelurahan = ref([]);

  const loadKabupaten = async () => {
    if (!form.provinsi) {
      listKabupaten.value = [];
      return;
    }

    const response = await axios.get(`/wilayah/kabupaten/${form.provinsi}`);

    listKabupaten.value = response.data;
  };

  const loadKecamatan = async () => {
    if (!form.kabupaten) {
      listKecamatan.value = [];
      return;
    }

    const response = await axios.get(`/wilayah/kecamatan/${form.provinsi}/${form.kabupaten}`);

    listKecamatan.value = response.data;
  };

  const loadKelurahan = async () => {
    if (!form.kecamatan) {
      listKelurahan.value = [];
      return;
    }

    const response = await axios.get(
      `/wilayah/kelurahan/${form.provinsi}/${form.kabupaten}/${form.kecamatan}`
    );

    listKelurahan.value = response.data;
  };

  watch(
    () => form.provinsi,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.kabupaten = '';
        form.kecamatan = '';
        form.kelurahan = '';
      }

      await loadKabupaten();
    }
  );

  watch(
    () => form.kabupaten,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.kecamatan = '';
        form.kelurahan = '';
      }

      await loadKecamatan();
    }
  );

  watch(
    () => form.kecamatan,
    async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        form.kelurahan = '';
      }

      await loadKelurahan();
    }
  );

  onMounted(async () => {
    await loadKabupaten();
    await loadKecamatan();
    await loadKelurahan();
  });

  const formValid = computed(
    () =>
      Boolean(form.nama) &&
      Boolean(form.nik) &&
      Boolean(form.alamat) &&
      Boolean(form.tanggal_lahir) &&
      Boolean(form.provinsi) &&
      Boolean(form.kabupaten) &&
      Boolean(form.kecamatan) &&
      Boolean(form.kelurahan)
  );

  const submit = () => {
    form.post(route('pasien.update', props.pasien.ID), {
      preserveScroll: true,
      onSuccess: () => {
        router.visit(route('loket.search'));
      },
    });
  };

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
