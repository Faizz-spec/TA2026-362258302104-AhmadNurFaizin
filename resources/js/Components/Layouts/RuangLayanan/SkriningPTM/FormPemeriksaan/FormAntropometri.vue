<template>
  <div class="metabolik-form">
    <section class="metabolik-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-speedometer2"></i> Obesitas</h4>
          <p>Antropometri, IMT, dan lingkar pinggang.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="metric-grid">
          <div class="form-field">
            <label class="form-label" for="bb">Berat Badan</label>
            <div class="input-with-addon">
              <input
                id="bb"
                class="form-control"
                type="number"
                min="0"
                step="0.1"
                v-model.number="form.bb"
                placeholder="0.0"
                @input="hitungIMT"
              />
              <span>kg</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="tb">Tinggi Badan</label>
            <div class="input-with-addon">
              <input
                id="tb"
                class="form-control"
                type="number"
                min="0"
                step="0.1"
                v-model.number="form.tb"
                placeholder="0.0"
                @input="hitungIMT"
              />
              <span>cm</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="imt">IMT</label>
            <div class="input-with-addon">
              <input
                id="imt"
                class="form-control"
                type="text"
                v-model="form.imt"
                readonly
                placeholder="-"
              />
              <span>kg/m2</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="imt_interp">Interpretasi IMT</label>
            <input
              id="imt_interp"
              class="form-control"
              type="text"
              v-model="form.imt_interp"
              readonly
              placeholder="-"
            />
          </div>

          <div class="form-field">
            <label class="form-label" for="lp">Lingkar Pinggang</label>
            <div class="input-with-addon">
              <input
                id="lp"
                class="form-control"
                type="number"
                min="0"
                step="0.1"
                v-model.number="form.lp"
                placeholder="0.0"
                @input="interpretLP"
              />
              <span>cm</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="lp_interp">Interpretasi Lingkar Pinggang</label>
            <input
              id="lp_interp"
              class="form-control"
              type="text"
              v-model="form.lp_interp"
              readonly
              placeholder="-"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="metabolik-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-heart-pulse"></i> Hipertensi</h4>
          <p>Tekanan darah, nadi, frekuensi napas, dan suhu tubuh.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="metric-grid">
          <div class="form-field">
            <label class="form-label" for="td_s">Tekanan Darah Sistolik</label>
            <div class="input-with-addon">
              <input
                id="td_s"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.td_s"
                placeholder="120"
                @input="interpretTD"
              />
              <span>mmHg</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="td_d">Tekanan Darah Diastolik</label>
            <div class="input-with-addon">
              <input
                id="td_d"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.td_d"
                placeholder="80"
                @input="interpretTD"
              />
              <span>mmHg</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="td_interp">Interpretasi Hipertensi</label>
            <input
              id="td_interp"
              class="form-control"
              type="text"
              v-model="form.td_interp"
              readonly
              placeholder="-"
            />
          </div>

          <div class="form-field">
            <label class="form-label" for="nadi">Frekuensi Nadi</label>
            <div class="input-with-addon">
              <input
                id="nadi"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.nadi"
                placeholder="80"
              />
              <span>x/mnt</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="napas">Frekuensi Napas</label>
            <div class="input-with-addon">
              <input
                id="napas"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.napas"
                placeholder="18"
              />
              <span>x/mnt</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="suhu">Suhu Tubuh</label>
            <div class="input-with-addon">
              <input
                id="suhu"
                class="form-control"
                type="number"
                min="0"
                step="0.1"
                v-model.number="form.suhu"
                placeholder="36.5"
              />
              <span>C</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="metabolik-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-droplet-half"></i> Diabetes Melitus</h4>
          <p>Gula darah puasa, gula darah sewaktu, HbA1c, dan interpretasi diabetes.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="metric-grid">
          <div class="form-field">
            <label class="form-label" for="gd_puasa">Gula Darah Puasa</label>
            <div class="input-with-addon">
              <input
                id="gd_puasa"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.gd_puasa"
                placeholder="0"
                @input="interpretDM"
              />
              <span>mg/dL</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="gd_sewaktu">Gula Darah Sewaktu</label>
            <div class="input-with-addon">
              <input
                id="gd_sewaktu"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.gd_sewaktu"
                placeholder="0"
                @input="interpretDM"
              />
              <span>mg/dL</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="hba1c">HbA1c</label>
            <div class="input-with-addon">
              <input
                id="hba1c"
                class="form-control"
                type="number"
                min="0"
                step="0.1"
                v-model.number="form.hba1c"
                placeholder="0.0"
                @input="interpretDM"
              />
              <span>%</span>
            </div>
          </div>
          <div class="form-field">
            <label class="form-label" for="gd2pp">Gula Darah 2 Jam Pasca Puasa</label>
            <div class="input-with-addon">
              <input
                id="gd2pp"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.gd2pp"
                placeholder="0.0"
                @input="interpretDM"
              />
              <span>mg/dL</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="dm_interp">Interpretasi Diabetes</label>
            <input
              id="dm_interp"
              class="form-control"
              type="text"
              v-model="form.dm_interp"
              readonly
              placeholder="-"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="metabolik-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-droplet"></i> Asam Urat</h4>
          <p>Asam urat serum dan interpretasi risiko hiperurisemia.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="metric-grid">
          <div class="form-field">
            <label class="form-label" for="asam_urat">Asam Urat</label>
            <div class="input-with-addon">
              <input
                id="asam_urat"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.asam_urat"
                placeholder="0"
                @input="interpretUrat"
              />
              <span>mg/dL</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="urat_interp">Interpretasi Asam Urat</label>
            <input
              id="urat_interp"
              class="form-control"
              type="text"
              v-model="form.urat_interp"
              readonly
              placeholder="-"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="metabolik-panel">
      <div class="panel-header">
        <div>
          <h4><i class="bi bi-capsule"></i> Profil Lipid</h4>
          <p>Kolesterol total, HDL, LDL, trigliserida, dan prediksi risiko PTM.</p>
        </div>
      </div>

      <div class="panel-body">
        <div class="lab-grid">
          <div class="form-field lab-field">
            <label class="form-label" for="koltot">Kolesterol Total</label>
            <div class="input-with-addon">
              <input
                id="koltot"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.koltot"
                placeholder="0"
                @input="interpretLipid"
              />
              <span>mg/dL</span>
            </div>
            <input
              class="form-control interpretation-control"
              type="text"
              v-model="form.koltot_i"
              readonly
              placeholder="Interpretasi"
            />
            <small>LOINC 2093-3</small>
          </div>

          <div class="form-field lab-field">
            <label class="form-label" for="hdl">HDL</label>
            <div class="input-with-addon">
              <input
                id="hdl"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.hdl"
                placeholder="0"
                @input="interpretLipid"
              />
              <span>mg/dL</span>
            </div>
            <input
              class="form-control interpretation-control"
              type="text"
              v-model="form.hdl_i"
              readonly
              placeholder="Interpretasi"
            />
            <small>LOINC 2085-9</small>
          </div>

          <div class="form-field lab-field">
            <label class="form-label" for="ldl">LDL</label>
            <div class="input-with-addon">
              <input
                id="ldl"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.ldl"
                placeholder="0"
                @input="interpretLipid"
              />
              <span>mg/dL</span>
            </div>
            <input
              class="form-control interpretation-control"
              type="text"
              v-model="form.ldl_i"
              readonly
              placeholder="Interpretasi"
            />
            <small>LOINC 2089-1</small>
          </div>

          <div class="form-field lab-field">
            <label class="form-label" for="tg">Trigliserida</label>
            <div class="input-with-addon">
              <input
                id="tg"
                class="form-control"
                type="number"
                min="0"
                v-model.number="form.tg"
                placeholder="0"
                @input="interpretLipid"
              />
              <span>mg/dL</span>
            </div>
            <input
              class="form-control interpretation-control"
              type="text"
              v-model="form.tg_i"
              readonly
              placeholder="Interpretasi"
            />
            <small>LOINC 2571-8</small>
          </div>
        </div>

        <div class="section-title with-gap">
          <h5>Prediksi Risiko PTM</h5>
          <span>WHO/ISH risk chart</span>
        </div>

        <div class="risk-grid">
          <div class="form-field">
            <label class="form-label" for="risiko_lab">Risiko dengan Lab</label>
            <div class="input-with-addon">
              <input
                id="risiko_lab"
                class="form-control"
                type="number"
                min="0"
                max="100"
                v-model.number="form.risiko_lab"
                placeholder="0"
              />
              <span>%</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="risiko_nolab">Risiko tanpa Lab</label>
            <div class="input-with-addon">
              <input
                id="risiko_nolab"
                class="form-control"
                type="number"
                min="0"
                max="100"
                v-model.number="form.risiko_nolab"
                placeholder="0"
              />
              <span>%</span>
            </div>
          </div>

          <div class="form-field">
            <label class="form-label" for="kat_risiko">Interpretasi Risiko</label>
            <select id="kat_risiko" class="form-select" v-model="form.kat_risiko">
              <option value="">Pilih kategori</option>
              <option value="rendah">&lt;10% (Rendah)</option>
              <option value="sedang">10-20% (Sedang)</option>
              <option value="tinggi">20-30% (Tinggi)</option>
              <option value="sangat_tinggi">&gt;30% (Sangat Tinggi)</option>
            </select>
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

  const numericFields = [
    'bb',
    'tb',
    'lp',
    'td_s',
    'td_d',
    'nadi',
    'napas',
    'suhu',
    'gd_puasa',
    'gd_sewaktu',
    'gd2pp',
    'hba1c',
    'asam_urat',
    'koltot',
    'hdl',
    'ldl',
    'tg',
    'risiko_lab',
    'risiko_nolab',
  ];

  const textFields = [
    'imt',
    'imt_interp',
    'lp_interp',
    'td_interp',
    'dm_interp',
    'urat_interp',
    'koltot_i',
    'hdl_i',
    'ldl_i',
    'tg_i',
    'kat_risiko',
  ];

  numericFields.forEach((field) => {
    form[field] = form[field] ?? '';
  });

  textFields.forEach((field) => {
    form[field] = form[field] || '';
  });

  function toNumber(value) {
    const number = parseFloat(value);
    return Number.isFinite(number) ? number : 0;
  }

  function hitungIMT() {
    const bb = toNumber(form.bb);
    const tb = toNumber(form.tb);

    if (!bb || !tb) {
      form.imt = '';
      form.imt_interp = '';
      return;
    }

    const imt = bb / (tb / 100) ** 2;
    form.imt = imt.toFixed(1);

    if (imt < 18.5) form.imt_interp = 'Kurus';
    else if (imt < 23) form.imt_interp = 'Normal';
    else if (imt < 27) form.imt_interp = 'Gemuk';
    else form.imt_interp = 'Obesitas';
  }

  function interpretLP() {
    const lp = toNumber(form.lp);

    if (!lp) {
      form.lp_interp = '';
      return;
    }

    form.lp_interp = lp >= 90 ? 'Risiko meningkat' : 'Normal';
  }

  function interpretTD() {
    const s = toNumber(form.td_s);
    const d = toNumber(form.td_d);

    if (!s || !d) {
      form.td_interp = '';
      return;
    }

    if (s < 120 && d < 80) form.td_interp = 'Normal';
    else if (s < 130 && d < 80) form.td_interp = 'Elevated';
    else if (s < 140 || d < 90) form.td_interp = 'Hipertensi Grade 1';
    else if (s < 180 || d < 110) form.td_interp = 'Hipertensi Grade 2';
    else form.td_interp = 'Krisis Hipertensi';
  }

  function interpretDM() {
    const puasa = toNumber(form.gd_puasa);
    const sewaktu = toNumber(form.gd_sewaktu);
    const hba = toNumber(form.hba1c);

    if (!puasa && !sewaktu && !hba) {
      form.dm_interp = '';
      return;
    }

    if (hba >= 6.5 || puasa >= 126 || sewaktu >= 200) {
      form.dm_interp = 'Diabetes';
    } else if (hba >= 5.7 || puasa >= 100 || sewaktu >= 140) {
      form.dm_interp = 'Prediabetes';
    } else {
      form.dm_interp = 'Normal';
    }
  }

  function interpretUrat() {
    const urat = toNumber(form.asam_urat);

    if (!urat) {
      form.urat_interp = '';
      return;
    }

    form.urat_interp = urat > 7 ? 'Hiperurisemia' : 'Normal';
  }

  function interpretLipid() {
    form.koltot_i = interpretRange(form.koltot, 200, 'Normal', 'Tinggi');
    form.hdl_i = form.hdl ? (toNumber(form.hdl) >= 40 ? 'Baik' : 'Rendah') : '';
    form.ldl_i = interpretRange(form.ldl, 130, 'Optimal', 'Tinggi');
    form.tg_i = interpretRange(form.tg, 150, 'Normal', 'Tinggi');
  }

  function interpretRange(value, threshold, normalLabel, highLabel) {
    if (value === '' || value === null || value === undefined) return '';
    return toNumber(value) >= threshold ? highLabel : normalLabel;
  }
</script>

<style scoped src="./FormPemeriksaan.css"></style>
