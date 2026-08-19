<template>
  <div class="resume-form">
    <section class="resume-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-clipboard2-check"></i> Ringkasan Skrining PTM</h4>
          <p>Status pengisian dan rangkuman data utama sebelum dikirim ke SATUSEHAT.</p>
        </div>
        <span class="status-pill" :class="{ complete: completedItems === summaryItems.length }">
          {{ completedItems }}/{{ summaryItems.length }} lengkap
        </span>
      </div>

      <div class="panel-body">
        <div class="summary-grid">
          <div class="summary-item" v-for="item in summaryItems" :key="item.label">
            <div class="summary-label">{{ item.label }}</div>
            <div class="summary-value">{{ valueOrDash(item.value) }}</div>
          </div>
        </div>
      </div>
    </section>

    <section class="resume-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-activity"></i> Ringkasan Temuan</h4>
          <p>Temuan otomatis dari subjektif, objektif, dan status pasien sebagai bahan resume.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="finding-list">
          <div class="finding-item" v-for="finding in assessmentFindings" :key="finding.title">
            <i class="bi" :class="finding.icon || 'bi-dot info'"></i>
            <div>
              <strong>{{ finding.title }}</strong>
              <span>{{ finding.description }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="resume-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-send-check"></i> Composition FHIR</h4>
          <p>Composition.type - LOINC 74213-0 untuk dokumen laporan skrining PTM.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="composition-grid">
          <div class="form-field">
            <label class="form-label" for="doc_num">Nomor Dokumen</label>
            <input id="doc_num" class="form-control bg-light" type="text" v-model="form.doc_num" readonly />
          </div>

          <div class="form-field">
            <label class="form-label" for="comp_date">Tanggal Komposisi</label>
            <input id="comp_date" class="form-control" type="date" v-model="form.comp_date" />
          </div>

          <div class="form-field">
            <label class="form-label" for="comp_status">Status Dokumen</label>
            <select id="comp_status" class="form-select" v-model="form.comp_status">
              <option value="preliminary">Preliminary</option>
              <option value="final">Final</option>
              <option value="amended">Amended</option>
            </select>
          </div>

          <div class="form-field span-full">
            <label class="form-label" for="comp_title">Judul Composition</label>
            <input
              id="comp_title"
              class="form-control"
              type="text"
              v-model="form.comp_title"
              placeholder="Ringkasan Skrining PTM"
            />
          </div>

          <div class="form-field span-full">
            <label class="form-label" for="catatan_penutup">Catatan Penutup</label>
            <textarea
              id="catatan_penutup"
              class="form-control"
              rows="4"
              v-model="form.catatan_penutup"
              placeholder="Catatan tambahan dokter/perawat sebelum pengiriman"
            ></textarea>
          </div>
        </div>

        <div class="fhir-preview">
          <div class="preview-header">
            <span>Resource yang dirangkum</span>
            <code>Encounter, QuestionnaireResponse, Observation, Condition, Procedure, CarePlan, Composition</code>
          </div>
          <pre>{{ compositionPreview }}</pre>
        </div>
      </div>
    </section>

    <div class="form-actions">
      <div class="send-status" :class="{ success: sendStatus === 'sent' }">
        {{ sendMessage }}
      </div>
      <div class="action-buttons">
        <button
          type="button"
          class="finish-button"
          :disabled="isFinishing"
          @click="finishPelayanan"
        >
          <i class="bi" :class="isFinishing ? 'bi-arrow-repeat' : 'bi-check2-circle'"></i>
          <span>{{ isFinishing ? 'Mengakhiri...' : 'Akhiri Pelayanan' }}</span>
        </button>
        <button
          type="button"
          class="send-button"
          :disabled="isSending"
          @click="sendSatuSehat"
        >
          <i class="bi" :class="isSending ? 'bi-arrow-repeat' : 'bi-send-check'"></i>
          <span>{{ isSending ? 'Mengirim...' : 'Kirim Satu Sehat' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed, ref } from 'vue';

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
    DataTindakan: Array,
  });


  const emit = defineEmits(['send-satu-sehat', 'finish-pelayanan']);
  const isSending = ref(false);
  const isFinishing = ref(false);
  const sendStatus = ref('idle');
  const subjektif = computed(() => props.formData?.subjektif || {});
  const objektif = computed(() => props.formData?.objektif || {});
  const assessment = computed(() => props.formData?.assessment || {});
  const statusPasien = computed(() => props.formData?.status_pasien || {});
  const planning = computed(() => props.formData?.planning || {});
  const pasien = computed(() => props.DataPasien || {});
  const form = props.formData?.resume || (props.formData.resume = {});

  form.doc_num = form.doc_num || generateDocumentNumber();
  form.comp_date = form.comp_date || today();
  form.comp_status = form.comp_status || 'preliminary';
  form.comp_title = form.comp_title || 'Ringkasan Skrining PTM';
  form.catatan_penutup = form.catatan_penutup || '';

  const summaryItems = computed(() => [
    { label: 'Nama Pasien', value: pasien.value.NAMA_LGKP || pasien.value.nama || pasien.value.nmPasien },
    { label: 'NIK', value: pasien.value.NIK || pasien.value.noKTP },
    { label: 'Tanggal Skrining', value: subjektif.value.tanggal_skrining || pasien.value.tglKunjungan },
    { label: 'Fasyankes', value: subjektif.value.fasyankes || pasien.value.nama_unit },
    { label: 'Pemeriksa', value: subjektif.value.dokter || pasien.value.dokter },
    { label: 'Status IMT', value: formatUnit(objektif.value.imt_interp, '') },
    { label: 'Tekanan Darah', value: formatBloodPressure(objektif.value.td_s, objektif.value.td_d) },
    { label: 'Gula Darah Sewaktu', value: formatUnit(objektif.value.gd_sewaktu || objektif.value.gds, 'mg/dL') },
    { label: 'Kolesterol Total', value: formatUnit(objektif.value.koltot, 'mg/dL') },
    { label: 'Kategori Risiko PTM', value: labelize(statusPasien.value.kategori_risiko_ptm || objektif.value.kat_risiko) },
    { label: 'Status Kasus', value: labelize(statusPasien.value.status_kasus) },
    { label: 'Rencana Rujuk', value: labelize(statusPasien.value.rencana_rujuk) },
  ]);

  const completedItems = computed(() => summaryItems.value.filter((item) => hasValue(item.value)).length);
  const assessmentFindings = computed(() => {
    if (assessment.value.ringkasan_temuan?.length) return assessment.value.ringkasan_temuan;

    return [
      {
        icon: 'bi-check-circle-fill success',
        title: 'Tidak ada temuan prioritas',
        description: 'Belum ada temuan otomatis yang perlu dikonfirmasi sebagai masalah assessment.',
      },
    ];
  });

  const sendMessage = computed(() => {
    if (sendStatus.value === 'sent') {
      return 'Data siap dikirim ke Satu Sehat.';
    }

    return `${completedItems.value}/${summaryItems.value.length} data utama lengkap sebelum pengiriman.`;
  });

  const compositionPreview = computed(() =>
    JSON.stringify(
      {
        resourceType: 'Composition',
        status: form.comp_status,
        type: {
          coding: [
            {
              system: 'http://loinc.org',
              code: '74213-0',
              display: 'Skrining PTM',
            },
          ],
        },
        title: form.comp_title,
        date: form.comp_date,
        subject: {
          display: valueOrDash(summaryItems.value[0]?.value),
          identifier: valueOrDash(summaryItems.value[1]?.value),
        },
        section: [
          {
            title: 'Ringkasan Skrining PTM',
            text: { status: 'generated', div: `${completedItems.value}/${summaryItems.value.length} data utama lengkap` },
          },
          {
            title: 'Ringkasan Temuan',
            text: { status: 'generated', div: assessmentFindings.value.map((item) => item.title).join('; ') },
          },
        ],
      },
      null,
      2,
    ),
  );

  function sendSatuSehat() {
    isSending.value = true;

    emit('send-satu-sehat', {
      DataPasien: props.DataPasien,
      formData: props.formData,
      composition: JSON.parse(compositionPreview.value),
    });

    window.setTimeout(() => {
      isSending.value = false;
      sendStatus.value = 'sent';
    }, 500);
  }

  function finishPelayanan() {
    isFinishing.value = true;

    emit('finish-pelayanan', {
      DataPasien: props.DataPasien,
      formData: props.formData,
    });

    window.setTimeout(() => {
      isFinishing.value = false;
    }, 500);
  }

  function formatBloodPressure(systolic, diastolic) {
    if (!hasValue(systolic) || !hasValue(diastolic)) return '';
    return `${systolic}/${diastolic} mmHg`;
  }

  function formatUnit(value, unit) {
    if (!hasValue(value)) return '';
    return `${value} ${unit}`;
  }

  function hasValue(value) {
    return value !== undefined && value !== null && value !== '';
  }

  function valueOrDash(value) {
    return hasValue(value) ? value : '-';
  }

  function labelize(value) {
    if (!hasValue(value)) return '';
    return String(value)
      .replace(/_/g, ' ')
      .replace(/\b\w/g, (char) => char.toUpperCase());
  }

  function today() {
    return new Date().toISOString().split('T')[0];
  }

  function generateDocumentNumber() {
    return `PTM-${Date.now().toString(36).toUpperCase()}`;
  }
</script>

<style scoped src="./FormPemeriksaan.css"></style>
