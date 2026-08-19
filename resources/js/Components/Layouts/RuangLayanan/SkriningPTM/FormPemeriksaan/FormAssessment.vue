<template>
  <div class="assessment-form">
    <section class="assessment-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-clipboard2-check"></i> Masalah Hasil Skrining</h4>
          <p>
            Konfirmasi masalah atau faktor risiko PTM dari data subjektif, objektif, dan penunjang.
          </p>
        </div>
        <span class="status-pill" :class="{ complete: suggestedAssessmentKeys.length > 0 }">
          {{ suggestedAssessmentKeys.length }} saran
        </span>
      </div>

      <div class="panel-body">
        <div class="diagnosis-grid">
          <label
            v-for="item in daftarAssessment"
            :key="item.key"
            class="diagnosis-option"
            :class="{ checked: form[item.key] }"
            :for="item.key"
          >
            <input
              :id="item.key"
              v-model="form[item.key]"
              class="form-check-input"
              type="checkbox"
            />
            <span>
              <strong>{{ item.label }}</strong>
              <small>{{ item.system }} - {{ item.code }}</small>
              <small v-if="suggestedAssessmentKeys.includes(item.key)" class="suggestion-hint">
                Disarankan dari hasil skrining
              </small>
            </span>
          </label>
        </div>
      </div>
    </section>

    <section class="assessment-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-clipboard2-pulse"></i> Diagnosis Klinis</h4>
          <p>Keputusan diagnosis oleh petugas berdasarkan masalah yang sudah dikonfirmasi.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="diagnosis-form-grid">
          <div class="form-field diagnosis-main-field">
            <label class="form-label" for="diagnosis_utama">Diagnosis Utama</label>
            <select
              id="diagnosis_utama"
              v-model="form.diagnosis_utama"
              name="diagnosis_utama"
              class="form-select"
            >
              <option value="">Gunakan saran otomatis: {{ diagnosisUtamaSaran }}</option>
              <option v-for="item in daftarDiagnosisUtama" :key="item.value" :value="item.value">
                {{ item.value }}
              </option>
            </select>
            <span class="field-hint"
              >Saran sistem hanya membantu pengisian. Diagnosis final tetap ditentukan
              petugas.</span
            >
          </div>

          <div class="form-field">
            <label class="form-label" for="status_klinis">Status Klinis</label>
            <select
              id="status_klinis"
              v-model="form.status_klinis"
              name="status_klinis"
              class="form-select"
            >
              <option value="active">Aktif</option>
              <option value="recurrence">Berulang</option>
              <option value="remission">Remisi</option>
              <option value="resolved">Selesai / teratasi</option>
            </select>
          </div>

          <div class="form-field note-field">
            <label class="form-label" for="catatan_diagnosis">Catatan Diagnosis / Komorbid</label>
            <textarea
              id="catatan_diagnosis"
              v-model="form.catatan_diagnosis"
              name="catatan_diagnosis"
              class="form-control"
              rows="3"
              placeholder="Diagnosis tambahan, komorbid, atau pertimbangan singkat bila ada"
            ></textarea>
          </div>
        </div>
      </div>
    </section>

    <section class="assessment-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-journal-check"></i> Kesimpulan Assessment</h4>
          <p>Kesimpulan akhir dari masalah hasil skrining dan diagnosis klinis.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="summary-grid">
          <div class="summary-item">
            <span>Diagnosis Final / Saran</span>
            <strong>{{ form.diagnosis_utama || diagnosisUtamaSaran }}</strong>
          </div>

          <div class="summary-item">
            <span>Masalah Hasil Skrining</span>
            <strong>{{ assessmentTerpilih }}</strong>
          </div>

          <div class="summary-item">
            <span>Kategori Risiko</span>
            <strong>{{ kategoriRisikoAssessment }}</strong>
          </div>

          <div class="form-field note-field">
            <label class="form-label" for="ringkasan_klinis">Ringkasan Klinis</label>
            <textarea
              id="ringkasan_klinis"
              v-model="form.ringkasan_klinis"
              name="ringkasan_klinis"
              class="form-control"
              rows="3"
              placeholder="Kesimpulan hasil skrining, diagnosis kerja, dan pertimbangan klinis"
            ></textarea>
          </div>

          <div class="form-field note-field">
            <label class="form-label" for="catatan_assessment">Catatan Tambahan</label>
            <textarea
              id="catatan_assessment"
              v-model="form.catatan_assessment"
              name="catatan_assessment"
              class="form-control"
              rows="3"
              placeholder="Catatan tambahan bila ada"
            ></textarea>
          </div>
        </div>
      </div>
    </section>

    <div class="form-actions">
      <div class="save-status" :class="{ success: saveStatus === 'ready' }">
        {{ saveMessage }}
      </div>
      <button type="button" class="save-button" :disabled="isSaving" @click="saveAssessment">
        <i class="bi" :class="isSaving ? 'bi-arrow-repeat' : 'bi-save'"></i>
        <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Assessment' }}</span>
      </button>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-check-circle-fill success-icon"></i>
            Data Berhasil Disimpan
          </h5>
          <button type="button" class="btn-close" @click="closeModal">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>
            Assessment pasien <strong>{{ DataPasien?.nama }}</strong> telah berhasil disimpan.
          </p>
          <div class="save-summary">
            <div class="summary-row">
              <span>Diagnosis:</span>
              <strong>{{ form.diagnosis_utama || diagnosisUtamaSaran }}</strong>
            </div>
            <div class="summary-row">
              <span>Masalah Teridentifikasi:</span>
              <strong>{{ assessmentTerpilih }}</strong>
            </div>
            <div class="summary-row">
              <span>Kategori Risiko:</span>
              <strong>{{ kategoriRisikoAssessment }}</strong>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="closeModal">
            <i class="bi bi-check"></i>
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3';
  import { computed, ref, watchEffect } from 'vue';

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
  });

  const isSaving = ref(false);
  const saveStatus = ref('idle');
  const showSuccessModal = ref(false);
  const form = props.formData?.assessment || {};
  const subjektif = computed(() => props.formData?.subjektif || {});
  const objektif = computed(() => props.formData?.objektif || {});
  const statusPasien = computed(() => props.formData?.status_pasien || {});

  const daftarDiagnosisUtama = [
    { value: 'Z13.6 - Skrining penyakit kardiovaskular' },
    { value: 'E66 - Obesitas / berat badan lebih' },
    { value: 'I10 - Hipertensi esensial' },
    { value: 'R73.0 - Risiko prediabetes' },
    { value: 'E11 - Diabetes melitus tipe 2' },
    { value: 'E78.5 - Dislipidemia' },
  ];

  const daftarAssessment = [
    { key: 'obesitas', label: 'Obesitas / berat badan lebih', system: 'ICD-10', code: 'E66' },
    {
      key: 'hipertensi',
      label: 'Hipertensi / tekanan darah tinggi',
      system: 'ICD-10',
      code: 'I10',
    },
    { key: 'risiko_diabetes', label: 'Risiko prediabetes', system: 'ICD-10', code: 'R73.0' },
    { key: 'diabetes_melitus', label: 'Diabetes melitus', system: 'ICD-10', code: 'E11' },
    { key: 'dislipidemia', label: 'Dislipidemia', system: 'ICD-10', code: 'E78.5' },
    {
      key: 'risiko_kardiovaskular',
      label: 'Risiko penyakit kardiovaskular',
      system: 'SNOMED CT',
      code: '395112001',
    },
    {
      key: 'perilaku_berisiko',
      label: 'Perilaku berisiko PTM',
      system: 'SNOMED CT',
      code: '160573003',
    },
  ];

  daftarAssessment.forEach((item) => {
    if (form[item.key] === undefined) form[item.key] = false;
  });

  form.diagnosis_utama = form.diagnosis_utama || '';
  form.status_klinis = form.status_klinis || 'active';
  form.catatan_diagnosis = form.catatan_diagnosis || '';
  form.ringkasan_klinis = form.ringkasan_klinis || '';
  form.catatan_assessment = form.catatan_assessment || '';

  const lipidDescription = computed(() => {
    const values = [
      objektif.value.koltot_i ? `Kolesterol total ${objektif.value.koltot_i}` : '',
      objektif.value.ldl_i ? `LDL ${objektif.value.ldl_i}` : '',
      objektif.value.hdl_i ? `HDL ${objektif.value.hdl_i}` : '',
      objektif.value.tg_i ? `Trigliserida ${objektif.value.tg_i}` : '',
    ].filter(Boolean);

    return values.length ? values.join(', ') : 'Assessment dislipidemia terpilih.';
  });

  const assessmentRules = [
    {
      condition: () => objektif.value.td_interp?.includes('Hipertensi'),
      icon: 'bi-exclamation-triangle-fill warning',
      title: 'Tekanan darah perlu perhatian',
      description: () => objektif.value.td_interp,
      suggests: ['hipertensi'],
    },
    {
      condition: () =>
        ['Gemuk', 'Obesitas'].some((label) => objektif.value.imt_interp?.includes(label)),
      icon: 'bi-exclamation-circle-fill warning',
      title: 'Status IMT meningkat',
      description: () => `IMT ${objektif.value.imt || '-'} - ${objektif.value.imt_interp}`,
      suggests: ['obesitas'],
    },
    {
      condition: () =>
        objektif.value.dm_interp === 'Diabetes' ||
        objektif.value.gd_sewaktu >= 200 ||
        objektif.value.gd_puasa >= 126 ||
        objektif.value.hba1c >= 6.5,
      icon: 'bi-droplet-fill danger',
      title: 'Indikasi diabetes melitus',
      description: () => objektif.value.dm_interp || 'Nilai gula darah memenuhi kriteria diabetes.',
      suggests: ['diabetes_melitus'],
    },
    {
      condition: () =>
        objektif.value.dm_interp === 'Prediabetes' ||
        between(objektif.value.gd_puasa, 100, 125) ||
        between(objektif.value.hba1c, 5.7, 6.4),
      icon: 'bi-droplet-half warning',
      title: 'Risiko prediabetes',
      description: () => objektif.value.dm_interp || 'Nilai gula darah berada pada rentang risiko.',
      suggests: ['risiko_diabetes'],
    },
    {
      condition: () =>
        objektif.value.koltot_i === 'Tinggi' ||
        objektif.value.ldl_i === 'Tinggi' ||
        objektif.value.tg_i === 'Tinggi' ||
        objektif.value.hdl_i === 'Rendah',
      icon: 'bi-capsule warning',
      title: 'Profil lipid abnormal',
      description: () => lipidDescription.value,
      suggests: ['dislipidemia', 'risiko_kardiovaskular'],
    },
    {
      condition: () =>
        subjektif.value.kategori_faktor_risiko === 'Risiko Tinggi' ||
        subjektif.value.status_merokok === 'current',
      icon: 'bi-exclamation-diamond-fill warning',
      title: 'Perilaku berisiko PTM',
      description: () =>
        subjektif.value.kategori_faktor_risiko ||
        `${subjektif.value.btg_rokok || 0} batang/hari selama ${subjektif.value.lama_rokok || 0} tahun.`,
      suggests: ['perilaku_berisiko'],
    },
    {
      condition: () =>
        statusPasien.value.rencana_rujuk && statusPasien.value.rencana_rujuk !== 'tidak',
      icon: 'bi-hospital-fill info',
      title: 'Perlu tindak lanjut rujukan',
      description: () => labelize(statusPasien.value.rencana_rujuk),
      suggests: ['risiko_kardiovaskular'],
    },
  ];

  const findings = computed(() => {
    const items = assessmentRules
      .filter((rule) => rule.condition())
      .map((rule) => ({
        icon: rule.icon,
        title: rule.title,
        description: typeof rule.description === 'function' ? rule.description() : rule.description,
        suggests: rule.suggests,
      }));

    if (items.length === 0) {
      items.push({
        icon: 'bi-check-circle-fill success',
        title: 'Tidak ada temuan prioritas',
        description:
          'Belum ada temuan otomatis yang perlu dikonfirmasi sebagai masalah assessment.',
        suggests: [],
      });
    }

    return items;
  });

  const suggestedAssessmentKeys = computed(() => [
    ...new Set(findings.value.flatMap((finding) => finding.suggests || [])),
  ]);

  const selectedAssessments = computed(() => daftarAssessment.filter((item) => form[item.key]));

  const assessmentTerpilih = computed(() => {
    const selected = selectedAssessments.value.map((item) => item.label);
    return selected.length ? selected.join(', ') : 'Belum ada masalah hasil skrining';
  });

  const diagnosisUtamaSaran = computed(() => {
    const suggestedKeys = new Set(suggestedAssessmentKeys.value);
    if (suggestedKeys.has('diabetes_melitus')) return 'E11 - Diabetes melitus tipe 2';
    if (suggestedKeys.has('hipertensi')) return 'I10 - Hipertensi esensial';
    if (suggestedKeys.has('dislipidemia')) return 'E78.5 - Dislipidemia';
    if (suggestedKeys.has('risiko_diabetes')) return 'R73.0 - Risiko prediabetes';
    if (suggestedKeys.has('obesitas')) return 'E66 - Obesitas / berat badan lebih';
    return 'Z13.6 - Skrining penyakit kardiovaskular';
  });

  const kategoriRisikoAssessment = computed(() => {
    const kategoriRisiko =
      statusPasien.value.kategori_risiko_ptm ||
      objektif.value.kat_risiko ||
      subjektif.value.kategori_faktor_risiko;

    return hasValue(kategoriRisiko) ? labelize(kategoriRisiko) : 'Belum ada kategori risiko';
  });

  const saveMessage = computed(() => {
    if (saveStatus.value === 'ready') {
      return 'Data assessment siap disimpan.';
    }

    return 'Simpan setelah diagnosis dan kesimpulan assessment selesai diisi.';
  });

  watchEffect(() => {
    form.ringkasan_temuan = findings.value.map((finding) => ({
      icon: finding.icon,
      title: finding.title,
      description: finding.description,
      suggests: finding.suggests || [],
    }));
    form.assessment_terpilih = assessmentTerpilih.value;
    form.diagnosis_utama_saran = diagnosisUtamaSaran.value;
    form.saran_kategori = kategoriRisikoAssessment.value;
  });

  const saveAssessment = () => {
    isSaving.value = true;

    router.post(route('pelayanan.simpan-assessment-ptm'), buildAssessmentPayload(), {
      preserveScroll: true,
      onSuccess: () => {
        saveStatus.value = 'ready';
        showSuccessModal.value = true;
        setTimeout(() => {
          closeModal();
        }, 5000);
      },
      onFinish: () => {
        isSaving.value = false;
      },
    });
  };

  const closeModal = () => {
    showSuccessModal.value = false;
    saveStatus.value = 'idle';
  };

  function buildAssessmentPayload() {
    return {
      skrining_ptm_id:
        form.skrining_ptm_id ||
        props.formData?.skrining_ptm_id ||
        props.DataPasien?.skrining_ptm_id ||
        null,
      idpelayanan: props.DataPasien?.idpelayanan,
      masalah_hasil_skrining: selectedAssessments.value.map(({ key, label, system, code }) => ({
        key,
        label,
        system,
        code,
      })),
      ringkasan_temuan: form.ringkasan_temuan || [],
      diagnosis_utama: form.diagnosis_utama || diagnosisUtamaSaran.value,
      diagnosis_utama_saran: diagnosisUtamaSaran.value,
      status_klinis: form.status_klinis,
      catatan_diagnosis: form.catatan_diagnosis,
      kategori_risiko: kategoriRisikoAssessment.value,
      ringkasan_klinis: form.ringkasan_klinis,
      catatan_assessment: form.catatan_assessment,
    };
  }

  function between(value, min, max) {
    const numericValue = Number(value);
    return Number.isFinite(numericValue) && numericValue >= min && numericValue <= max;
  }

  function hasValue(value) {
    return value !== undefined && value !== null && value !== '';
  }

  function labelize(value) {
    if (!hasValue(value)) return '';
    return String(value)
      .replace(/_/g, ' ')
      .replace(/\b\w/g, (char) => char.toUpperCase());
  }
</script>

<style scoped src="./FormPemeriksaan.css"></style>

<style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
  }

  .modal-content {
    background: white;
    border-radius: 8px;
    box-shadow:
      0 4px 6px rgba(0, 0, 0, 0.1),
      0 10px 20px rgba(0, 0, 0, 0.15);
    max-width: 450px;
    width: 90%;
    animation: modalSlideIn 0.3s ease-out;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
  }

  .modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .success-icon {
    color: #28a745;
    font-size: 1.5rem;
  }

  .btn-close {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    font-size: 1.5rem;
    padding: 0;
    line-height: 1;
  }

  .btn-close:hover {
    color: #2c3e50;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-body > p {
    margin: 0 0 1rem 0;
    color: #495057;
    font-size: 0.95rem;
  }

  .save-summary {
    background: #f8f9fa;
    border-left: 4px solid #28a745;
    padding: 1rem;
    border-radius: 4px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.9rem;
  }

  .summary-row span {
    color: #6c757d;
  }

  .summary-row strong {
    color: #2c3e50;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
  }

  .modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e9ecef;
    gap: 0.5rem;
  }

  .btn {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }

  .btn-primary {
    background: #0d6efd;
    color: white;
  }

  .btn-primary:hover {
    background: #0b5ed7;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
  }
</style>
