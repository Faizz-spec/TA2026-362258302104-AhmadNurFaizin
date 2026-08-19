<template>
  <div class="subjektif-form">
      <section class="subjektif-panel visit-panel">
        <div class="panel-header">
          <div>
            <h4><i class="bi bi-clipboard2-pulse"></i> Data Kunjungan</h4>
            <p>Encounter FHIR - periode, fasyankes, dan alasan kunjungan.</p>
          </div>
        </div>

        <div class="panel-body">
          <div class="visit-grid">
            <div class="form-field">
              <label class="form-label" for="tanggal_skrining">Tanggal Skrining</label>
              <input
                type="date"
                name="tanggal_skrining"
                id="tanggal_skrining"
                class="form-control"
                v-model="form.tanggal_skrining"
              />
            </div>

            <div class="form-field">
              <label class="form-label" for="jenis_kunjungan">Jenis Kunjungan</label>
              <!-- <select
              name="jenis_kunjungan"
              id="jenis_kunjungan"
              class="form-select"
              v-model="form.jenis_kunjungan"
            >
              <option value="">Pilih jenis kunjungan</option>
              <option value="skrining">Skrining</option>
              <option value="konsultasi">Konsultasi</option>
              <option value="kontrol">Kontrol</option>
              <option value="rujukan">Rujukan</option>
            </select> -->
              <input
                type="text"
                name="jenis_kunjungan"
                id="jenis_kunjungan"
                class="form-control"
                v-model="form.jenis_kunjungan"
                placeholder="Masukkan jenis kunjungan (misal: Skrining, Konsultasi, Kontrol, Rujukan)"
              />
            </div>

            <div class="form-field">
              <label class="form-label" for="dokter">Dokter</label>
              <div class="field-with-icon">
                <i class="bi bi-person-badge"></i>
                <input
                  type="text"
                  name="dokter"
                  id="dokter"
                  class="form-control"
                  v-model="form.dokter"
                  placeholder="Masukkan nama dokter"
                />
              </div>
            </div>

            <div class="form-field">
              <label class="form-label" for="fasyankes">Fasyankes</label>
              <div class="field-with-icon">
                <i class="bi bi-hospital"></i>
                <input
                  type="text"
                  name="fasyankes"
                  id="fasyankes"
                  class="form-control"
                  v-model="form.fasyankes"
                  placeholder="Masukkan nama fasyankes"
                />
              </div>
            </div>

            <div class="form-field span-full">
              <label class="form-label" for="keluhan_utama">Keluhan Utama</label>
              <textarea
                name="keluhan_utama"
                id="keluhan_utama"
                class="form-control"
                rows="4"
                v-model="form.keluhan_utama"
                placeholder="Tuliskan keluhan utama, riwayat singkat, atau alasan skrining"
              ></textarea>
            </div>
          </div>
        </div>
      </section>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
    TenagaMedis: Array,
  });

  const pasien = computed(() => props.DataPasien || {});
  const fasyankes = computed(() => valueOrDash(pasien.value.nama_unit));
  const jenisKunjungan = computed(() => {
    if (pasien.value.kunjBaru === 'false') {
      return 'Kunjungan Lama';
    } else if (pasien.value.kunjBaru === 'true') {
      return 'Kunjungan Baru';
    }
    return '';
  });
  const form = props.formData?.subjektif || {};

  if (!form.tanggal_skrining) {
    form.tanggal_skrining =
      toDateInput(props.DataPasien?.tglKunjungan) || new Date().toISOString().split('T')[0];
  }

  form.jenis_kunjungan = form.jenis_kunjungan || jenisKunjungan.value;
  form.dokter = form.dokter || '';
  form.fasyankes = form.fasyankes || fasyankes.value;
  form.keluhan_utama = form.keluhan_utama || '';

  function toDateInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) return '';
    return date.toISOString().split('T')[0];
  }

  function valueOrDash(value) {
    return value === undefined || value === null || value === '' ? '-' : value;
  }
</script>

<style scoped src="./FormPemeriksaan.css"></style>
