<template>
  <div class="risk-form">
    <section class="risk-score-panel" :class="scoreClass">
      <div class="risk-score-main">
        <div>
          <p class="risk-score-label">Skor Faktor Risiko Perilaku</p>
          <h4>{{ riskScore.total }} / {{ riskScore.maxScore }}</h4>
          <span>{{ riskScore.category }}</span>
        </div>
        <div class="risk-score-meter" aria-hidden="true">
          <div :style="{ width: riskScore.percentage + '%' }"></div>
        </div>
      </div>

      <div class="risk-score-note">
        <strong>{{ riskScore.recommendation }}</strong>
        <span>{{ riskScore.summary }}</span>
      </div>

      <div v-if="riskScore.items.length" class="risk-score-factors">
        <span v-for="item in riskScore.items" :key="item.key">
          +{{ item.score }} {{ item.label }}
        </span>
      </div>
    </section>

    <section class="risk-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-exclamation-triangle"></i> Status Merokok</h4>
          <p>Observation LOINC 72166-2 dan kuesioner Q0013 linkId 1.1-1.5.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="smoking-grid">
          <div class="form-field">
            <label class="form-label">Apakah Pernah Merokok?</label>
            <div class="radio-group">
              <label class="radio-option" :class="{ checked: form.merokok === 'tidak' }">
                <input v-model="form.merokok" type="radio" name="merokok" value="tidak" />
                <span>Tidak</span>
              </label>
              <label class="radio-option" :class="{ checked: form.merokok === 'ya' }">
                <input v-model="form.merokok" type="radio" name="merokok" value="ya" />
                <span>Ya</span>
              </label>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="status_merokok">Status Merokok Saat Ini</label>
            <select id="status_merokok" name="status_merokok" class="form-select" v-model="form.status_merokok">
              <option value="">Pilih status merokok</option>
              <option value="never">Tidak pernah merokok</option>
              <option value="current">Merokok aktif</option>
              <option value="ex">Mantan perokok</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="btg_rokok">Rata-rata Batang Rokok/Hari</label>
            <input
              id="btg_rokok"
              name="btg_rokok"
              type="number"
              min="0"
              class="form-control"
              v-model.number="form.btg_rokok"
              placeholder="0"
            />
          </div>

          <div class="form-field">
            <label class="form-label" for="lama_rokok">Lama Merokok</label>
            <div class="input-with-addon">
              <input
                id="lama_rokok"
                name="lama_rokok"
                type="number"
                min="0"
                class="form-control"
                v-model.number="form.lama_rokok"
                placeholder="0"
              />
              <span>tahun</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="paparan_rokok">Terpapar Asap Rokok Orang Lain (1 Bulan)</label>
            <select id="paparan_rokok" name="paparan_rokok" class="form-select" v-model="form.paparan_rokok">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <section class="risk-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-activity"></i> Pola Makan &amp; Aktivitas</h4>
          <p>Faktor risiko perilaku Q0013 linkId 1.6-1.11.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="habit-grid">
          <div class="form-field">
            <label class="form-label" for="gula">Konsumsi Gula &gt;4 sdm/hari?</label>
            <select id="gula" name="gula" class="form-select" v-model="form.gula">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="garam">Konsumsi Garam &gt;1 sdt/hari?</label>
            <select id="garam" name="garam" class="form-select" v-model="form.garam">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="minyak">Konsumsi Minyak &gt;5 sdm/hari?</label>
            <select id="minyak" name="minyak" class="form-select" v-model="form.minyak">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="sayur">Kurang Sayur/Buah (&lt;5 porsi/hari)?</label>
            <select id="sayur" name="sayur" class="form-select" v-model="form.sayur">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="aktivitas">Aktivitas Fisik Kurang?</label>
            <select id="aktivitas" name="aktivitas" class="form-select" v-model="form.aktivitas">
              <option value="tidak">Tidak</option>
              <option value="kadang">Ya, tidak setiap hari</option>
              <option value="setiap_hari">Ya, setiap hari</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="alkohol">Konsumsi Alkohol 1 Bulan Terakhir?</label>
            <select id="alkohol" name="alkohol" class="form-select" v-model="form.alkohol">
              <option value="tidak">Tidak</option>
              <option value="ya">Ya</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <section class="risk-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-journal-medical"></i> Riwayat PTM</h4>
          <p>Condition FHIR, FamilyMemberHistory, dan MedicationStatement.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="history-grid">
          <div class="form-field history-check-field">
            <label class="form-label">Riwayat PTM Pribadi</label>
            <div class="check-grid history-check-grid">
              <label class="check-option" v-for="item in riwayatPribadiOptions" :key="item.model">
                <input v-model="form[item.model]" type="checkbox" />
                <span>{{ item.label }}</span>
              </label>
            </div>
          </div>

          <div class="form-field history-check-field">
            <label class="form-label">Riwayat PTM Keluarga</label>
            <div class="check-grid history-check-grid">
              <label class="check-option" v-for="item in riwayatKeluargaOptions" :key="item.model">
                <input v-model="form[item.model]" type="checkbox" />
                <span>{{ item.label }}</span>
              </label>
            </div>
          </div>

          <div class="form-field note-field">
            <label class="form-label" for="obat">Obat yang Sedang Dikonsumsi</label>
            <textarea
              id="obat"
              name="obat"
              class="form-control"
              rows="3"
              v-model="form.obat"
              placeholder="Antihipertensi, antidiabetes, statin, dll."
            ></textarea>
          </div>

          <div class="form-field">
            <label class="form-label" for="kesiapan">Kesiapan Berubah</label>
            <select id="kesiapan" name="kesiapan" class="form-select" v-model="form.kesiapan">
              <option value="tidak_siap">Tidak siap</option>
              <option value="ragu">Ragu</option>
              <option value="siap">Siap</option>
            </select>
          </div>

          <div class="form-field">
            <label class="form-label" for="dukung">Dukungan Keluarga</label>
            <select id="dukung" name="dukung" class="form-select" v-model="form.dukung">
              <option value="kurang">Kurang</option>
              <option value="cukup">Cukup</option>
              <option value="baik">Baik</option>
            </select>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
  import { computed, watchEffect } from 'vue';

  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
  });

  const form = props.formData?.subjektif || {};

  form.merokok = form.merokok || '';
  form.status_merokok = form.status_merokok || '';
  form.btg_rokok = form.btg_rokok ?? 0;
  form.lama_rokok = form.lama_rokok ?? 0;
  form.paparan_rokok = form.paparan_rokok || 'tidak';
  form.gula = form.gula || 'tidak';
  form.garam = form.garam || 'tidak';
  form.minyak = form.minyak || 'tidak';
  form.sayur = form.sayur || 'tidak';
  form.aktivitas = form.aktivitas || 'tidak';
  form.alkohol = form.alkohol || 'tidak';
  form.obat = form.obat || '';
  form.kesiapan = form.kesiapan || 'tidak_siap';
  form.dukung = form.dukung || 'kurang';

  const riwayatPribadiOptions = [
    { model: 'r_pribadi_htn', legacy: 'r_htn', label: 'Hipertensi' },
    { model: 'r_pribadi_dm', legacy: 'r_dm', label: 'Diabetes Melitus' },
    { model: 'r_pribadi_stroke', legacy: 'r_stroke', label: 'Stroke' },
    { model: 'r_pribadi_jantung', label: 'Penyakit Jantung' },
  ];

  const riwayatKeluargaOptions = [
    { model: 'r_keluarga_htn', label: 'Hipertensi' },
    { model: 'r_keluarga_dm', label: 'Diabetes Melitus' },
    { model: 'r_keluarga_stroke', label: 'Stroke' },
    { model: 'r_keluarga_jantung', label: 'Penyakit Jantung' },
  ];

  riwayatPribadiOptions.forEach((item) => {
    form[item.model] = form[item.model] || ['aktif', 'dahulu'].includes(form[item.legacy]);
  });

  riwayatKeluargaOptions.forEach((item) => {
    form[item.model] = form[item.model] || false;
  });

  const hasText = (value) => String(value || '').trim().length > 0;
  const toNumber = (value) => Number(value || 0);

  const addRisk = (items, condition, key, label, score) => {
    if (condition) {
      items.push({ key, label, score });
    }
  };

  const riskScore = computed(() => {
    const items = [];

    addRisk(items, form.merokok === 'ya', 'merokok', 'Pernah merokok', 2);
    addRisk(items, form.status_merokok === 'current', 'status_merokok', 'Merokok aktif', 2);
    addRisk(items, form.status_merokok === 'ex', 'mantan_perokok', 'Mantan perokok', 1);
    addRisk(items, toNumber(form.btg_rokok) >= 10, 'btg_rokok', 'Rokok >=10 batang/hari', 1);
    addRisk(items, toNumber(form.lama_rokok) >= 10, 'lama_rokok', 'Lama merokok >=10 tahun', 1);
    addRisk(items, form.paparan_rokok === 'kadang', 'paparan_kadang', 'Paparan asap rokok kadang', 1);
    addRisk(items, form.paparan_rokok === 'setiap_hari', 'paparan_harian', 'Paparan asap rokok harian', 2);

    addRisk(items, form.gula === 'kadang', 'gula_kadang', 'Gula berlebih kadang', 1);
    addRisk(items, form.gula === 'setiap_hari', 'gula_harian', 'Gula berlebih harian', 2);
    addRisk(items, form.garam === 'kadang', 'garam_kadang', 'Garam berlebih kadang', 1);
    addRisk(items, form.garam === 'setiap_hari', 'garam_harian', 'Garam berlebih harian', 2);
    addRisk(items, form.minyak === 'kadang', 'minyak_kadang', 'Minyak berlebih kadang', 1);
    addRisk(items, form.minyak === 'setiap_hari', 'minyak_harian', 'Minyak berlebih harian', 2);
    addRisk(items, form.sayur === 'kadang', 'sayur_kadang', 'Kurang sayur/buah kadang', 1);
    addRisk(items, form.sayur === 'setiap_hari', 'sayur_harian', 'Kurang sayur/buah harian', 2);
    addRisk(items, form.aktivitas === 'kadang', 'aktivitas_kadang', 'Aktivitas fisik kurang kadang', 1);
    addRisk(items, form.aktivitas === 'setiap_hari', 'aktivitas_harian', 'Aktivitas fisik kurang harian', 2);
    addRisk(items, form.alkohol === 'ya', 'alkohol', 'Konsumsi alkohol', 1);

    addRisk(items, form.r_pribadi_htn, 'pribadi_htn', 'Riwayat pribadi hipertensi', 2);
    addRisk(items, form.r_pribadi_dm, 'pribadi_dm', 'Riwayat pribadi diabetes', 2);
    addRisk(items, form.r_pribadi_stroke, 'pribadi_stroke', 'Riwayat pribadi stroke', 3);
    addRisk(items, form.r_pribadi_jantung, 'pribadi_jantung', 'Riwayat pribadi penyakit jantung', 3);
    addRisk(items, form.r_keluarga_htn, 'keluarga_htn', 'Riwayat keluarga hipertensi', 1);
    addRisk(items, form.r_keluarga_dm, 'keluarga_dm', 'Riwayat keluarga diabetes', 1);
    addRisk(items, form.r_keluarga_stroke, 'keluarga_stroke', 'Riwayat keluarga stroke', 1);
    addRisk(items, form.r_keluarga_jantung, 'keluarga_jantung', 'Riwayat keluarga penyakit jantung', 1);

    addRisk(items, form.kesiapan === 'tidak_siap', 'tidak_siap', 'Belum siap berubah', 1);
    addRisk(items, form.kesiapan === 'ragu', 'ragu', 'Masih ragu berubah', 1);
    addRisk(items, form.dukung === 'kurang', 'dukungan_kurang', 'Dukungan keluarga kurang', 1);

    const total = items.reduce((sum, item) => sum + item.score, 0);
    const maxScore = 30;

    if (total >= 12) {
      return {
        total,
        maxScore,
        items,
        percentage: Math.min(100, Math.round((total / maxScore) * 100)),
        level: 'high',
        category: 'Risiko Tinggi',
        recommendation: 'Prioritaskan konseling intensif dan evaluasi klinis.',
        summary: 'Ada kombinasi faktor perilaku, riwayat PTM, atau hambatan perubahan yang cukup kuat.',
      };
    }

    if (total >= 6) {
      return {
        total,
        maxScore,
        items,
        percentage: Math.min(100, Math.round((total / maxScore) * 100)),
        level: 'medium',
        category: 'Risiko Sedang',
        recommendation: 'Berikan edukasi terarah dan buat target perubahan kecil.',
        summary: 'Beberapa faktor risiko sudah muncul dan perlu dipantau saat kontrol berikutnya.',
      };
    }

    return {
      total,
      maxScore,
      items,
      percentage: Math.min(100, Math.round((total / maxScore) * 100)),
      level: 'low',
      category: 'Risiko Rendah',
      recommendation: 'Pertahankan perilaku sehat dan lakukan edukasi pencegahan.',
      summary: total === 0 ? 'Belum ada faktor risiko bermakna dari isian saat ini.' : 'Faktor risiko masih terbatas.',
    };
  });

  const scoreClass = computed(() => `risk-score-${riskScore.value.level}`);

  watchEffect(() => {
    form.skor_faktor_risiko = riskScore.value.total;
    form.kategori_faktor_risiko = riskScore.value.category;
    form.detail_faktor_risiko = riskScore.value.items;
  });
</script>

<style scoped src="./FormPemeriksaan.css"></style>
