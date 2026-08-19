<template>
  <div class="indera-form">
    <section class="indera-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-eye"></i> Gangguan Penglihatan</h4>
          <p>Observation untuk mata kanan dan mata kiri.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="section-title">
          <h5>Tajam Penglihatan</h5>
          <span>Snellen chart - uncorrected visual acuity</span>
        </div>

        <div class="side-grid">
          <div class="side-card">
            <div class="side-head kanan">Mata Kanan (OD)</div>
            <div class="side-body">
              <div class="form-field">
                <label class="form-label" for="visus_od">Visus</label>
                <input id="visus_od" class="form-control" type="text" v-model="form.visus_od" placeholder="6/6" />
              </div>
              <div class="form-field">
                <label class="form-label" for="pinhole_od">Pinhole</label>
                <input id="pinhole_od" class="form-control" type="text" v-model="form.pinhole_od" placeholder="6/6" />
              </div>
            </div>
          </div>

          <div class="side-card">
            <div class="side-head kiri">Mata Kiri (OS)</div>
            <div class="side-body">
              <div class="form-field">
                <label class="form-label" for="visus_os">Visus</label>
                <input id="visus_os" class="form-control" type="text" v-model="form.visus_os" placeholder="6/6" />
              </div>
              <div class="form-field">
                <label class="form-label" for="pinhole_os">Pinhole</label>
                <input id="pinhole_os" class="form-control" type="text" v-model="form.pinhole_os" placeholder="6/6" />
              </div>
            </div>
          </div>
        </div>

        <div class="section-title with-gap">
          <h5>Segmen Anterior &amp; Katarak</h5>
          <span>Pupil, refleks fundus, shadow test, TIO</span>
        </div>

        <div class="side-grid">
          <div class="side-card">
            <div class="side-head kanan">Mata Kanan</div>
            <div class="side-body compact-grid">
              <div class="form-field">
                <label class="form-label" for="sa_od">Segmen Anterior</label>
                <select id="sa_od" class="form-select" v-model="form.sa_od">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal</option>
                  <option value="tidak_normal">Tidak Normal</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="rf_od">Refleks Fundus</label>
                <select id="rf_od" class="form-select" v-model="form.rf_od">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal</option>
                  <option value="tidak_normal">Tidak Normal</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="st_od">Shadow Test</label>
                <select id="st_od" class="form-select" v-model="form.st_od">
                  <option value="">Pilih hasil</option>
                  <option value="negatif">Negatif</option>
                  <option value="positif">Positif</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="gio_od">Curiga Glaukoma</label>
                <div class="input-with-addon">
                  <input id="gio_od" class="form-control" type="number" v-model.number="form.gio_od" placeholder="0" />
                  <span>mmHg</span>
                </div>
              </div>
            </div>
          </div>

          <div class="side-card">
            <div class="side-head kiri">Mata Kiri</div>
            <div class="side-body compact-grid">
              <div class="form-field">
                <label class="form-label" for="sa_os">Segmen Anterior</label>
                <select id="sa_os" class="form-select" v-model="form.sa_os">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal</option>
                  <option value="tidak_normal">Tidak Normal</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="rf_os">Refleks Fundus</label>
                <select id="rf_os" class="form-select" v-model="form.rf_os">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal</option>
                  <option value="tidak_normal">Tidak Normal</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="st_os">Shadow Test</label>
                <select id="st_os" class="form-select" v-model="form.st_os">
                  <option value="">Pilih hasil</option>
                  <option value="negatif">Negatif</option>
                  <option value="positif">Positif</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label" for="gio_os">Curiga Glaukoma</label>
                <div class="input-with-addon">
                  <input id="gio_os" class="form-control" type="number" v-model.number="form.gio_os" placeholder="0" />
                  <span>mmHg</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="result-grid">
          <div class="form-field">
            <label class="form-label" for="retino_od">Curiga Retinopati Mata Kanan</label>
            <select id="retino_od" class="form-select" v-model="form.retino_od">
              <option value="false">Tidak</option>
              <option value="true">Ya</option>
            </select>
          </div>
          <div class="form-field">
            <label class="form-label" for="retino_os">Curiga Retinopati Mata Kiri</label>
            <select id="retino_os" class="form-select" v-model="form.retino_os">
              <option value="false">Tidak</option>
              <option value="true">Ya</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <section class="indera-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-ear"></i> Gangguan Pendengaran</h4>
          <p>Observation untuk telinga kanan dan telinga kiri.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="side-grid">
          <div class="side-card">
            <div class="side-head kanan">Telinga Kanan</div>
            <div class="side-body compact-grid">
              <div class="form-field" v-for="field in rightEarFields" :key="field.model">
                <label class="form-label" :for="field.model">{{ field.label }}</label>
                <select :id="field.model" class="form-select" v-model="form[field.model]">
                  <option value="false">Tidak</option>
                  <option value="true">Ya</option>
                </select>
              </div>
              <div class="form-field span-full">
                <label class="form-label" for="bisik_kanan">Tes Berbisik</label>
                <select id="bisik_kanan" class="form-select" v-model="form.bisik_kanan">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal (&gt;=6 meter)</option>
                  <option value="ringan">Ringan (4-5 m)</option>
                  <option value="sedang">Sedang (1-3 m)</option>
                  <option value="berat">Berat (&lt;1 m)</option>
                </select>
              </div>
            </div>
          </div>

          <div class="side-card">
            <div class="side-head kiri">Telinga Kiri</div>
            <div class="side-body compact-grid">
              <div class="form-field" v-for="field in leftEarFields" :key="field.model">
                <label class="form-label" :for="field.model">{{ field.label }}</label>
                <select :id="field.model" class="form-select" v-model="form[field.model]">
                  <option value="false">Tidak</option>
                  <option value="true">Ya</option>
                </select>
              </div>
              <div class="form-field span-full">
                <label class="form-label" for="bisik_kiri">Tes Berbisik</label>
                <select id="bisik_kiri" class="form-select" v-model="form.bisik_kiri">
                  <option value="">Pilih hasil</option>
                  <option value="normal">Normal (&gt;=6 meter)</option>
                  <option value="ringan">Ringan (4-5 m)</option>
                  <option value="sedang">Sedang (1-3 m)</option>
                  <option value="berat">Berat (&lt;1 m)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="indera-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-lungs"></i> PPOK</h4>
          <p>QuestionnaireResponse Q0021, pemeriksaan fisik paru, dan spirometri.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="warning-note">
          Pemeriksaan ini khusus untuk peserta usia &gt;40 tahun dengan riwayat merokok.
        </div>

        <div class="section-title">
          <h5>Kuesioner PUMA</h5>
          <span>Skor >= 6 dilanjutkan spirometri</span>
        </div>

        <div class="puma-grid">
          <div class="form-field question-field" v-for="question in pumaQuestions" :key="question.model">
            <label class="form-label">{{ question.label }}</label>
            <div class="radio-group">
              <label class="radio-option" :class="{ checked: form[question.model] === 'ya' }">
                <input v-model="form[question.model]" type="radio" :name="question.model" value="ya" />
                <span>Ya</span>
              </label>
              <label class="radio-option" :class="{ checked: form[question.model] === 'tidak' }">
                <input v-model="form[question.model]" type="radio" :name="question.model" value="tidak" />
                <span>Tidak</span>
              </label>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="skor_puma">Total Skor PUMA</label>
            <input id="skor_puma" class="form-control" type="number" min="0" max="10" v-model.number="form.skor_puma" placeholder="0" />
            <small>Jika skor >= 6, wajib spirometri</small>
          </div>

          <div class="form-field">
            <label class="form-label" for="hasil_puma">Hasil Kuesioner PUMA</label>
            <select id="hasil_puma" class="form-select" v-model="form.hasil_puma">
              <option value="">Pilih hasil</option>
              <option value="rendah">Risiko Rendah (&lt;6)</option>
              <option value="tinggi">Risiko Tinggi (&gt;=6)</option>
            </select>
          </div>
        </div>

        <div class="section-title with-gap">
          <h5>Pemeriksaan Fisik Paru</h5>
          <span>Temuan klinis PPOK</span>
        </div>

        <div class="check-grid">
          <label class="check-option" v-for="item in ppokPhysicalFields" :key="item.model">
            <input v-model="form[item.model]" type="checkbox" />
            <span>{{ item.label }}</span>
          </label>
        </div>

        <div class="section-title with-gap">
          <h5>Spirometri</h5>
          <span>Dilakukan jika risiko tinggi</span>
        </div>

        <div class="spirometry-grid">
          <div class="form-field">
            <label class="form-label" for="kv">KV / KV Prediksi</label>
            <div class="input-with-addon">
              <input id="kv" class="form-control" type="number" step="0.1" v-model.number="form.kv" placeholder="0.0" />
              <span>%</span>
            </div>
          </div>
          <div class="form-field">
            <label class="form-label" for="kvp">KVP / KVP Prediksi</label>
            <div class="input-with-addon">
              <input id="kvp" class="form-control" type="number" step="0.1" v-model.number="form.kvp" placeholder="0.0" />
              <span>%</span>
            </div>
          </div>
          <div class="form-field">
            <label class="form-label" for="vep1">VEP1 / VEP1 Prediksi</label>
            <div class="input-with-addon">
              <input id="vep1" class="form-control" type="number" step="0.1" v-model.number="form.vep1" placeholder="0.0" />
              <span>%</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
  const props = defineProps({
    DataPasien: Object,
    formData: Object,
    tindakan: Array,
  });

  const form = props.formData?.objektif || {};

  const rightEarFields = [
    { model: 'tuli_kanan', label: 'Curiga Tuli Kongenital' },
    { model: 'omsk_kanan', label: 'OMSK / Congek' },
    { model: 'serumen_kanan', label: 'Serumen Impaksi' },
    { model: 'presbi_kanan', label: 'Curiga Presbikusis' },
  ];

  const leftEarFields = [
    { model: 'tuli_kiri', label: 'Curiga Tuli Kongenital' },
    { model: 'omsk_kiri', label: 'OMSK / Congek' },
    { model: 'serumen_kiri', label: 'Serumen Impaksi' },
    { model: 'presbi_kiri', label: 'Curiga Presbikusis' },
  ];

  const pumaQuestions = [
    {
      model: 'ppok1',
      label: '1. Pernah merasa napas pendek saat berjalan lebih cepat di jalan datar atau menanjak?',
    },
    {
      model: 'ppok2',
      label: '2. Biasanya mempunyai dahak dari paru atau sulit mengeluarkan dahak saat tidak flu?',
    },
    {
      model: 'ppok3',
      label: '3. Biasanya batuk saat tidak menderita selesma/flu?',
    },
    {
      model: 'ppok4',
      label: '4. Dokter/nakes pernah meminta melakukan spirometri/peak flow meter?',
    },
  ];

  const ppokPhysicalFields = [
    { model: 'pink_puffer', label: 'Pink puffer (kulit kemerahan)' },
    { model: 'pursed_lips', label: 'Pursed lips breathing' },
    { model: 'otot_bantu', label: 'Penggunaan otot bantu napas' },
    { model: 'pelebaran_sela', label: 'Pelebaran sela iga' },
    { model: 'barrel_chest', label: 'Barrel chest' },
    { model: 'hipersonor', label: 'Perkusi hipersonor' },
    { model: 'vesikuler', label: 'Suara nafas vesikuler melemah' },
    { model: 'ronki', label: 'Ronki / mengi' },
    { model: 'ekspirasi_pjg', label: 'Ekspirasi memanjang' },
  ];

  [
    'visus_od',
    'pinhole_od',
    'visus_os',
    'pinhole_os',
    'sa_od',
    'rf_od',
    'st_od',
    'sa_os',
    'rf_os',
    'st_os',
    'bisik_kanan',
    'bisik_kiri',
    'ppok1',
    'ppok2',
    'ppok3',
    'ppok4',
    'hasil_puma',
  ].forEach((field) => {
    form[field] = form[field] || '';
  });

  ['gio_od', 'gio_os', 'skor_puma', 'kv', 'kvp', 'vep1'].forEach((field) => {
    form[field] = form[field] ?? '';
  });

  ['retino_od', 'retino_os', ...rightEarFields.map((field) => field.model), ...leftEarFields.map((field) => field.model)].forEach((field) => {
    form[field] = form[field] || 'false';
  });

  ppokPhysicalFields.forEach((field) => {
    form[field.model] = form[field.model] || false;
  });
</script>

<style scoped src="./FormPemeriksaan.css"></style>
