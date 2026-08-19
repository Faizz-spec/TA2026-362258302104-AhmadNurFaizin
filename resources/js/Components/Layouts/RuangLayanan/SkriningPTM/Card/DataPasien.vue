<template>
  <section class="patient-summary">
    <div class="summary-header">
      <div class="patient-identity">
        <div class="avatar">
          <i class="bi bi-person-vcard"></i>
        </div>
        <div>
          <p class="summary-kicker">Data pasien</p>
          <h1>{{ patientName }}</h1>
          <div class="identity-meta">
            <span>{{ ihsNumber }}</span>
            <span>{{ ageText }}</span>
            <span>MR {{ valueOrDash(patient.NO_MR) }}</span>
          </div>
        </div>
      </div>

      <Link :href="backHref" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
      </Link>
    </div>

    <div class="summary-grid">
      <article class="info-box highlight">
        <div class="info-icon">
          <i class="bi bi-heart-pulse"></i>
        </div>
        <div>
          <span>Jenis / Poli</span>
          <strong>{{ visitType }} ({{ valueOrDash(patient.nmPoli) }})</strong>
        </div>
      </article>

      <article class="info-box">
        <div class="info-icon">
          <i class="bi bi-calendar2-check"></i>
        </div>
        <div>
          <span>Tanggal Kunjungan</span>
          <strong>{{ formatDate(patient.tglKunjungan) }}</strong>
        </div>
      </article>

      <article class="info-box">
        <div class="info-icon">
          <i class="bi bi-credit-card-2-front"></i>
        </div>
        <div>
          <span>NIK</span>
          <strong>{{ valueOrDash(patient.NIK) }}</strong>
        </div>
      </article>

      <article class="info-box">
        <div class="info-icon">
          <i class="bi bi-shield-check"></i>
        </div>
        <div>
          <span>No. BPJS / Provider</span>
          <strong>{{ bpjsText }}</strong>
        </div>
      </article>

      <article class="info-box address-box">
        <div class="info-icon">
          <i class="bi bi-geo-alt"></i>
        </div>
        <div>
          <span>Alamat</span>
          <strong>{{ addressText }}</strong>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
  DataPasien: Object,
  backRoute: String,
});

const patient = computed(() => props.DataPasien || {});

const patientName = computed(() => valueOrDash(patient.value.NAMA_LGKP));
const ihsNumber = computed(() => valueOrDash(patient.value.IHS_NUMBER));

const genderLabel = computed(() => {
  const gender =
    patient.value.jenis_kelamin ||
    patient.value.JENIS_KELAMIN ||
    patient.value.JK ||
    patient.value.jk ||
    '';

  if (String(gender).toUpperCase().startsWith('P')) return 'Perempuan';
  if (String(gender).toUpperCase().startsWith('L')) return 'Laki-laki';
  return 'Jenis kelamin -';
});

const ageText = computed(() => {
  const years = patient.value.umur;
  const months = patient.value.umur_bulan;
  const days = patient.value.umur_hari;

  if ([years, months, days].every((item) => item === undefined || item === null || item === '')) {
    return 'Umur -';
  }

  return `${years || 0} tahun ${months || 0} bulan ${days || 0} hari`;
});

const visitType = computed(() => (patient.value.poliSakit === 'TRUE' ? 'Kunjungan Sehat' : 'Kunjungan Sakit'));

const bpjsText = computed(() => {
  const bpjs = patient.value.NO_BPJS || patient.value.noKartu;
  const provider = patient.value.PROVIDER || patient.value.providerKartu || patient.value.kdProvider;

  if (!bpjs && !provider) return '-';
  return [bpjs, provider].filter(Boolean).join(' / ');
});

const addressText = computed(() => {
  const parts = [
    patient.value.alamat,
    patient.value.no_rt ? `RT ${patient.value.no_rt}` : '',
    patient.value.no_rw ? `RW ${patient.value.no_rw}` : '',
    patient.value.nama_kel ? `Kel. ${patient.value.nama_kel}` : '',
    patient.value.nama_kec ? `Kec. ${patient.value.nama_kec}` : '',
    patient.value.nama_kab,
    patient.value.nama_prop ? `Prov. ${patient.value.nama_prop}` : '',
  ].filter(Boolean);

  return parts.length ? parts.join(', ') : '-';
});

const backHref = computed(() => {
  if (!props.backRoute) return '#';
  if (props.backRoute.startsWith('/')) return props.backRoute;
  return route(props.backRoute);
});

function valueOrDash(value) {
  return value === undefined || value === null || value === '' ? '-' : value;
}

function formatDate(dateString) {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
}
</script>

<style scoped>
.patient-summary {
  overflow: hidden;
  border: 1px solid #d9e5df;
  border-radius: 8px;
  background: #ffffff;
  box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
}

.summary-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  padding: 22px;
  border-bottom: 1px solid #e5edf0;
  background: #f8fafc;
}

.patient-identity {
  display: flex;
  min-width: 0;
  gap: 14px;
}

.avatar,
.info-icon {
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #e7f5ef;
  color: #0f6b4c;
}

.avatar {
  flex: 0 0 52px;
  width: 52px;
  height: 52px;
  font-size: 1.6rem;
}

.summary-kicker {
  margin: 0 0 4px;
  color: #64748b;
  font-size: 0.76rem;
  font-weight: 750;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

h1 {
  margin: 0;
  color: #0f172a;
  font-size: 1.35rem;
  font-weight: 750;
  line-height: 1.25;
}

.identity-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 8px;
}

.identity-meta span {
  min-height: 26px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #ffffff;
  color: #475569;
  font-size: 0.8rem;
  font-weight: 650;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 8px;
  font-weight: 650;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  padding: 18px;
}

.info-box {
  display: flex;
  gap: 12px;
  min-width: 0;
  min-height: 92px;
  padding: 14px;
  border: 1px solid #e3ebef;
  border-radius: 8px;
  background: #fbfdff;
}

.info-box.highlight {
  background: #f6fbf8;
}

.address-box {
  grid-column: span 2;
}

.info-icon {
  flex: 0 0 36px;
  width: 36px;
  height: 36px;
  font-size: 1rem;
}

.info-box span {
  display: block;
  margin-bottom: 4px;
  color: #64748b;
  font-size: 0.78rem;
  font-weight: 700;
}

.info-box strong {
  display: block;
  color: #1e293b;
  font-size: 0.9rem;
  font-weight: 700;
  line-height: 1.45;
  overflow-wrap: anywhere;
}

@media (max-width: 992px) {
  .summary-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 576px) {
  .summary-header {
    align-items: stretch;
    flex-direction: column;
    padding: 18px;
  }

  .summary-header .btn,
  .summary-grid,
  .address-box {
    width: 100%;
  }

  .summary-grid {
    grid-template-columns: 1fr;
    padding: 14px;
  }

  .address-box {
    grid-column: auto;
  }

  h1 {
    font-size: 1.15rem;
  }
}
</style>
