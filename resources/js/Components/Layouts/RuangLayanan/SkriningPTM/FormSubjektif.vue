<template>
  <div class="subjektif-form">
    <div class="subjektif-toolbar">
      <div>
        <p class="subjektif-kicker">Subjektif</p>
        <h3>Pendaftaran Kunjungan Peserta Skrining</h3>
      </div>
      <div class="segmented-control" role="tablist" aria-label="Planning">
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormPlanning === 'dataKunjungan' }"
          @click="toggleForm('dataKunjungan')"
        >
          <i class="bi bi-person-check"></i>
          <span>Data Kunjungan</span>
        </button>
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormPlanning === 'faktorRisiko' }"
          @click="toggleForm('faktorRisiko')"
        >
          <i class="bi bi-exclamation-triangle"></i>
          <span>Faktor Risiko</span>
        </button>
      </div>
    </div>

    <div v-if="activeFormPlanning === 'dataKunjungan'" class="fade-in">
      <dataKunjungan :DataPasien="props.DataPasien" :formData="props.formData" :TenagaMedis="props.TenagaMedis" />
    </div>
    <div v-if="activeFormPlanning === 'faktorRisiko'" class="fade-in">
      <faktorRisiko :DataPasien="props.DataPasien" :formData="props.formData" :TenagaMedis="props.TenagaMedis" />
    </div>

    <div class="form-actions">
      <div class="save-status" :class="{ success: saveStatus === 'ready' }">
        {{ saveMessage }}
      </div>
      <button type="button" class="save-button" :disabled="isSaving" @click="saveSubjektif">
        <i class="bi" :class="isSaving ? 'bi-arrow-repeat' : 'bi-save'"></i>
        <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Subjektif' }}</span>
      </button>
    </div>

    <div
      v-if="showDuplicateModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(15, 23, 42, 0.48)"
      @click.self="closeDuplicateModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold text-warning">Data sudah tersimpan</h5>
              <p class="text-muted mb-0 small">
                Kunjungan PTM untuk pasien ini sudah pernah dibuat sebelumnya.
              </p>
            </div>
            <button type="button" class="btn-close" @click="closeDuplicateModal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-warning mb-0">
              {{ duplicateMessage }}
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-warning text-white" @click="closeDuplicateModal">
              Mengerti
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed, ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import { route } from 'ziggy-js';
  import dataKunjungan from './FormPemeriksaan/DataKunjungan.vue';
  import faktorRisiko from './FormPemeriksaan/Faktor Risiko.vue';

  const activeFormPlanning = ref('dataKunjungan');
  const isSaving = ref(false);
  const saveStatus = ref('idle');
  const saveErrors = ref({});
  const showDuplicateModal = ref(false);
  const duplicateMessage = ref('Silakan lanjutkan pengisian data pada kunjungan yang sudah ada.');

  const toggleForm = (form) => {
    activeFormPlanning.value = form;
  };

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
    TenagaMedis: Array,
  });

  const pasien = computed(() => props.DataPasien || {});

  const form = useForm({
    idpelayanan: '',
    idLoket: '',
    nikPasien: '',
    tanggal_skrining: '',
    dokter: '',
    fasyankes: '',
    jenis_kunjungan: '',
    keluhan_utama: '',
    merokok: '',
    status_merokok: '',
    btg_rokok: 0,
    lama_rokok: 0,
    paparan_rokok: '',
    gula: '',
    garam: '',
    minyak: '',
    sayur: '',
    aktivitas: '',
    alkohol: '',
    r_pribadi_htn: false,
    r_pribadi_dm: false,
    r_pribadi_stroke: false,
    r_pribadi_jantung: false,
    r_keluarga_htn: false,
    r_keluarga_dm: false,
    r_keluarga_stroke: false,
    r_keluarga_jantung: false,
    obat: '',
    kesiapan: '',
    dukung: '',
    skor_faktor_risiko: null,
    kategori_faktor_risiko: '',
    detail_faktor_risiko: [],
  });

  const saveMessage = computed(() => {
    if (saveStatus.value === 'ready') {
      return 'Data subjektif berhasil disimpan.';
    }

    if (Object.keys(saveErrors.value).length > 0) {
      return getErrorMessage(saveErrors.value);
    }

    return 'Simpan setelah data kunjungan dan faktor risiko selesai diisi.';
  });

  const saveSubjektif = () => {
    const subjektif = props.formData?.subjektif || {};

    isSaving.value = true;
    saveErrors.value = {};
    saveStatus.value = 'idle';
    showDuplicateModal.value = false;

    form.idpelayanan = pasien.value?.idpelayanan || '';
    form.idLoket = pasien.value?.idLoket || '';
    form.nikPasien = pasien.value?.NIK || '';
    form.tanggal_skrining = subjektif.tanggal_skrining || '';
    form.dokter = subjektif.dokter || '';
    form.fasyankes = subjektif.fasyankes || '';
    form.jenis_kunjungan = subjektif.jenis_kunjungan || '';
    form.keluhan_utama = subjektif.keluhan_utama || 'Pasien Melakukan Skrining PTM untuk cek kesehatan';
    form.merokok = subjektif.merokok || '';
    form.status_merokok = subjektif.status_merokok || '';
    form.btg_rokok = subjektif.btg_rokok || 0;
    form.lama_rokok = subjektif.lama_rokok || 0;
    form.paparan_rokok = subjektif.paparan_rokok || 'tidak';
    form.gula = subjektif.gula || 'tidak';
    form.garam = subjektif.garam || 'tidak';
    form.minyak = subjektif.minyak || 'tidak';
    form.sayur = subjektif.sayur || 'tidak';
    form.aktivitas = subjektif.aktivitas || 'tidak';
    form.alkohol = subjektif.alkohol || 'tidak';
    form.r_pribadi_htn = Boolean(subjektif.r_pribadi_htn);
    form.r_pribadi_dm = Boolean(subjektif.r_pribadi_dm);
    form.r_pribadi_stroke = Boolean(subjektif.r_pribadi_stroke);
    form.r_pribadi_jantung = Boolean(subjektif.r_pribadi_jantung);
    form.r_keluarga_htn = Boolean(subjektif.r_keluarga_htn);
    form.r_keluarga_dm = Boolean(subjektif.r_keluarga_dm);
    form.r_keluarga_stroke = Boolean(subjektif.r_keluarga_stroke);
    form.r_keluarga_jantung = Boolean(subjektif.r_keluarga_jantung);
    form.obat = subjektif.obat || '';
    form.kesiapan = subjektif.kesiapan || 'tidak_siap';
    form.dukung = subjektif.dukung || 'kurang';
    form.skor_faktor_risiko = subjektif.skor_faktor_risiko ?? null;
    form.kategori_faktor_risiko = subjektif.kategori_faktor_risiko || '';
    form.detail_faktor_risiko = subjektif.detail_faktor_risiko || [];

    if (!isFaktorRisikoFilled(subjektif)) {
      isSaving.value = false;
      activeFormPlanning.value = 'faktorRisiko';
      saveErrors.value = {
        faktor_risiko: 'Lengkapi bagian faktor risiko terlebih dahulu sebelum menyimpan subjektif.',
      };
      return;
    }

    form.post(route('pelayanan.tambah-kunjungan-ptm'), {
      preserveScroll: true,
      onSuccess: () => {
        saveStatus.value = 'ready';
      },
      onError: (errors) => {
        saveErrors.value = errors;
        if (isDuplicateError(errors)) {
          duplicateMessage.value = getErrorMessage(errors);
          showDuplicateModal.value = true;
        }
      },
      onFinish: () => {
        isSaving.value = false;
      },
    });
  };

  const closeDuplicateModal = () => {
    showDuplicateModal.value = false;
  };

  const getErrorMessage = (errors) => {
    const message = Object.values(errors || {})
      .flat()
      .find(Boolean);

    return message || 'Data kunjungan PTM pasien ini sudah pernah tersimpan sebelumnya.';
  };

  const isDuplicateError = (errors) => {
    const message = getErrorMessage(errors).toLowerCase();

    return ['sudah', 'tersimpan', 'duplikat', 'duplicate', 'already', 'exists'].some((keyword) =>
      message.includes(keyword)
    );
  };

  const isFaktorRisikoFilled = (subjektif) => {
    const hasSmokingAnswer = subjektif.merokok === 'ya' || subjektif.merokok === 'tidak';
    const hasSmokingStatus = Boolean(subjektif.status_merokok);

    return hasSmokingAnswer && hasSmokingStatus;
  };
</script>

<style scoped src="./FormPemeriksaan/FormPemeriksaan.css"></style>
