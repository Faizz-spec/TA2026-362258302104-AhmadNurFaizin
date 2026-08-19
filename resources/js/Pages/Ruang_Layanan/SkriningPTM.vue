<template>
  <div class="app-wrapper">
    <!-- HEADER -->
    <div class="app-header">
      <div class="logo">S</div>
      <div class="title">
        <h1>Form Skrining Penyakit Tidak Menular (PTM)</h1>
        <p>Integrasi SATUSEHAT Platform — QuestionnaireResponse &amp; Observation FHIR R4</p>
      </div>
      <div class="badge">SANDBOX</div>
      <div class="progress-top">
        <div class="progress-fill" :style="{ width: (currentStep / totalSteps) * 100 + '%' }"></div>
      </div>
    </div>

    <div class="container">
      <!-- SIDEBAR NAV -->
      <nav class="sidebar">
        <div class="nav-section">Data Umum</div>
        <div
          class="nav-item"
          :class="{ active: currentStep === 1, done: currentStep > 1 }"
          @click="goTo(1)"
        >
          <span class="nav-num">{{ currentStep > 1 ? '✓' : '1' }}</span>
          Data Kunjungan &amp; Pasien
        </div>
        <div
          class="nav-item"
          :class="{ active: currentStep === 2, done: currentStep > 2 }"
          @click="goTo(2)"
        >
          <span class="nav-num">{{ currentStep > 2 ? '✓' : '2' }}</span>
          Faktor Risiko PTM
        </div>

        <div class="nav-section" style="margin-top: 8px">Deteksi Dini</div>
        <div
          v-for="i in [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]"
          :key="'nav-' + i"
          class="nav-item"
          :class="{ active: currentStep === i, done: currentStep > i }"
          @click="goTo(i)"
        >
          <span class="nav-num">{{ currentStep > i ? '✓' : i }}</span>
          {{ getNavLabel(i) }}
        </div>

        <div class="nav-section" style="margin-top: 8px">Penutup</div>
        <div
          class="nav-item"
          :class="{ active: currentStep === 14, done: currentStep > 14 }"
          @click="goTo(14)"
        >
          <span class="nav-num">{{ currentStep > 14 ? '✓' : '14' }}</span>
          Diagnosis &amp; Tindak Lanjut
        </div>
        <div class="nav-item" :class="{ active: currentStep === 15 }" @click="goTo(15)">
          <span class="nav-num">✓</span>
          Ringkasan &amp; Kirim
        </div>
      </nav>

      <!-- MAIN CONTENT -->
      <main class="main">
        <!-- STEP 1: DATA KUNJUNGAN -->
        <div v-if="currentStep === 1" class="step-panel">
          <div class="breadcrumb">Skrining PTM <span>› Data Kunjungan &amp; Pasien</span></div>
          <div class="page-title">
            <h2>Data Kunjungan &amp; Pasien</h2>
            <p>Pendaftaran kunjungan dan identitas peserta skrining</p>
          </div>

          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #e3f9e5">🏥</div>
              <div>
                <h3>Data Kunjungan</h3>
                <div class="sub">Encounter FHIR — periode dan lokasi layanan</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-4 field">
                  <label class="label">Tanggal Skrining</label>
                  <input v-model="form.tgl_skrining" type="date" />
                  <span class="hint">Encounter.period.start</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Jenis Kunjungan</label>
                  <select v-model="form.jenis_kunjungan">
                    <option value="">— Pilih —</option>
                    <option>Baru</option>
                    <option>Ulang</option>
                  </select>
                  <span class="hint">Encounter.type</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Pemeriksa (Dokter/Perawat/Bidan)</label>
                  <input v-model="form.pemeriksa" type="text" placeholder="Nama pemeriksa" />
                  <span class="hint">Encounter.participant.individual</span>
                </div>
                <div class="col-6 field">
                  <label class="label">Fasilitas Pelayanan Kesehatan</label>
                  <input v-model="form.faskes" type="text" placeholder="Nama Puskesmas / Klinik" />
                  <span class="hint">Encounter.serviceProvider</span>
                </div>
                <div class="col-6 field">
                  <label class="label">Keluhan Utama / Anamnesis</label>
                  <textarea
                    v-model="form.keluhan"
                    placeholder="Keluhan, riwayat singkat, dan alasan skrining"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #deebff">👤</div>
              <div>
                <h3>Identitas Pasien</h3>
                <div class="sub">Patient FHIR — data demografi peserta skrining</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 field">
                  <label class="label">Nomor IHS Pasien</label>
                  <input v-model="form.ihs" type="text" placeholder="Patient IHS Number" />
                  <span class="hint">Patient.identifier (SATUSEHAT ID)</span>
                </div>
                <div class="col-6 field">
                  <label class="label">NIK</label>
                  <input v-model="form.nik" type="text" placeholder="16 digit NIK" />
                  <span class="hint">Patient.identifier (NIK)</span>
                </div>
                <div class="col-6 field">
                  <label class="label">Nama Lengkap</label>
                  <input v-model="form.nama" type="text" placeholder="Nama sesuai KTP" />
                  <span class="hint">Patient.name</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Tanggal Lahir</label>
                  <input v-model="form.tgl_lahir" type="date" />
                  <span class="hint">Patient.birthDate</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Jenis Kelamin</label>
                  <select v-model="form.jk" @change="updateInterpretasi">
                    <option value="">— Pilih —</option>
                    <option value="male">Laki-laki</option>
                    <option value="female">Perempuan</option>
                  </select>
                  <span class="hint">Patient.gender</span>
                </div>
                <div class="col-4 field">
                  <label class="label">No. HP / Telepon</label>
                  <input v-model="form.nohp" type="text" placeholder="08xxxxxxxx" />
                  <span class="hint">Patient.telecom</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Status Perkawinan</label>
                  <select v-model="form.status_kawin">
                    <option value="">— Pilih —</option>
                    <option>Belum kawin</option>
                    <option>Kawin</option>
                    <option>Cerai hidup</option>
                    <option>Cerai mati</option>
                  </select>
                  <span class="hint">Patient.maritalStatus</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Warga Negara</label>
                  <select v-model="form.wn">
                    <option value="ID">Indonesia (WNI)</option>
                    <option value="other">WNA</option>
                  </select>
                  <span class="hint">Patient.extension:citizenship</span>
                </div>
                <div class="col-12 field">
                  <label class="label">Alamat Lengkap</label>
                  <textarea
                    v-model="form.alamat"
                    placeholder="Jalan, RT/RW, Desa/Kelurahan, Kecamatan, Kab/Kota, Provinsi"
                  ></textarea>
                  <span class="hint">Patient.address.line</span>
                </div>
              </div>
            </div>
          </div>

          <div class="nav-btns">
            <div></div>
            <button class="btn btn-primary" @click="goTo(2)">Lanjut: Faktor Risiko PTM →</button>
          </div>
        </div>

        <!-- STEP 2: FAKTOR RISIKO -->
        <div v-if="currentStep === 2" class="step-panel">
          <div class="breadcrumb">Skrining PTM <span>› Faktor Risiko &amp; Riwayat PTM</span></div>
          <div class="page-title">
            <h2>Faktor Risiko PTM</h2>
            <p>QuestionnaireResponse Q0013 — Riwayat dan perilaku berisiko</p>
          </div>

          <div class="alert alert-info">
            ℹ️ Semua jawaban dikirim melalui
            <strong>QuestionnaireResponse</strong> dengan referensi ke
            <code>https://fhir.kemkes.go.id/Questionnaire/Q0013</code>
          </div>

          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #fff3e0">🚬</div>
              <div>
                <h3>Status Merokok</h3>
                <div class="sub">Observation LOINC 72166-2 + Q0013 linkId 1.1–1.5</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 field">
                  <label class="label"
                    >Apakah pernah merokok?
                    <span class="hint" style="display: inline">linkId 1.1</span></label
                  >
                  <div class="radio-group">
                    <label class="radio-btn" :class="{ checked: form.merokok === 'tidak' }">
                      <input v-model="form.merokok" type="radio" name="merokok" value="tidak" />
                      <span class="dot"></span> Tidak
                    </label>
                    <label class="radio-btn" :class="{ checked: form.merokok === 'ya' }">
                      <input v-model="form.merokok" type="radio" name="merokok" value="ya" />
                      <span class="dot"></span> Ya
                    </label>
                  </div>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Status Merokok Saat Ini
                    <span class="hint" style="display: inline"
                      >LOINC 72166-2, linkId 1.4</span
                    ></label
                  >
                  <select v-model="form.status_merokok">
                    <option value="">— Pilih —</option>
                    <option value="never">Tidak pernah merokok</option>
                    <option value="current">Merokok aktif</option>
                    <option value="ex">Mantan perokok</option>
                  </select>
                </div>
                <div class="col-4 field">
                  <label class="label"
                    >Rata-rata Batang Rokok/Hari
                    <span class="hint" style="display: inline">linkId 1.2</span></label
                  >
                  <input v-model.number="form.btg_rokok" type="number" min="0" placeholder="0" />
                </div>
                <div class="col-4 field">
                  <label class="label"
                    >Lama Merokok
                    <span class="hint" style="display: inline">linkId 1.3</span></label
                  >
                  <div class="input-group">
                    <input v-model.number="form.lama_rokok" type="number" min="0" placeholder="0" />
                    <span class="addon">tahun</span>
                  </div>
                </div>
                <div class="col-4 field">
                  <label class="label"
                    >Terpapar Asap Rokok Orang Lain (1 bln)
                    <span class="hint" style="display: inline">linkId 1.5</span></label
                  >
                  <select v-model="form.paparan_rokok">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #fde8e8">🍽️</div>
              <div>
                <h3>Pola Makan &amp; Aktivitas</h3>
                <div class="sub">Q0013 linkId 1.6–1.11</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 field">
                  <label class="label"
                    >Konsumsi gula &gt;4 sdm/hari?
                    <span class="hint" style="display: inline">1.6</span></label
                  >
                  <select v-model="form.gula">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Konsumsi garam &gt;1 sdt/hari?
                    <span class="hint" style="display: inline">1.7</span></label
                  >
                  <select v-model="form.garam">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Konsumsi minyak &gt;5 sdm/hari?
                    <span class="hint" style="display: inline">1.8</span></label
                  >
                  <select v-model="form.minyak">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Kurang sayur/buah (&lt;5 porsi/hari)?
                    <span class="hint" style="display: inline">1.9</span></label
                  >
                  <select v-model="form.sayur">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Aktivitas fisik &lt;30 mnt/hari atau &lt;150 mnt/minggu?
                    <span class="hint" style="display: inline">1.10</span></label
                  >
                  <select v-model="form.aktivitas">
                    <option value="tidak">Tidak</option>
                    <option value="kadang">Ya, tidak setiap hari</option>
                    <option value="setiap_hari">Ya, setiap hari</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label"
                    >Konsumsi alkohol dalam 1 bulan terakhir?
                    <span class="hint" style="display: inline">1.11</span></label
                  >
                  <select v-model="form.alkohol">
                    <option value="tidak">Tidak</option>
                    <option value="ya">Ya</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #ede7f6">📋</div>
              <div>
                <h3>Riwayat PTM</h3>
                <div class="sub">Condition FHIR — riwayat pribadi dan keluarga</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-4 field">
                  <label class="label">Riwayat Hipertensi</label>
                  <select v-model="form.r_htn">
                    <option value="tidak">Tidak</option>
                    <option value="aktif">Ya, sedang aktif</option>
                    <option value="dahulu">Ya, dahulu</option>
                    <option value="tidak_tahu">Tidak tahu</option>
                  </select>
                  <span class="hint">Condition.category — riwayat pribadi</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Riwayat Diabetes Melitus</label>
                  <select v-model="form.r_dm">
                    <option value="tidak">Tidak</option>
                    <option value="aktif">Ya, sedang aktif</option>
                    <option value="dahulu">Ya, dahulu</option>
                    <option value="tidak_tahu">Tidak tahu</option>
                  </select>
                  <span class="hint">Condition.category — riwayat pribadi</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Riwayat Stroke / Penyakit Jantung</label>
                  <select v-model="form.r_stroke">
                    <option value="tidak">Tidak</option>
                    <option value="aktif">Ya, sedang aktif</option>
                    <option value="dahulu">Ya, dahulu</option>
                    <option value="tidak_tahu">Tidak tahu</option>
                  </select>
                </div>
                <div class="col-6 field">
                  <label class="label">Riwayat PTM Pribadi Lain</label>
                  <textarea
                    v-model="form.r_pribadi_lain"
                    placeholder="Dislipidemia, kanker, ginjal kronis, dll."
                  ></textarea>
                </div>
                <div class="col-6 field">
                  <label class="label">Riwayat PTM Keluarga</label>
                  <textarea
                    v-model="form.r_keluarga"
                    placeholder="Hipertensi, DM, stroke, jantung, kanker, dll."
                  ></textarea>
                  <span class="hint">FamilyMemberHistory</span>
                </div>
                <div class="col-6 field">
                  <label class="label">Obat yang Sedang Dikonsumsi</label>
                  <textarea
                    v-model="form.obat"
                    placeholder="Antihipertensi, antidiabetes, statin, dll."
                  ></textarea>
                  <span class="hint">MedicationStatement</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Kesiapan Berubah</label>
                  <select v-model="form.kesiapan">
                    <option value="tidak_siap">Tidak siap</option>
                    <option value="ragu">Ragu</option>
                    <option value="siap">Siap</option>
                  </select>
                </div>
                <div class="col-3 field">
                  <label class="label">Dukungan Keluarga</label>
                  <select v-model="form.dukung">
                    <option value="kurang">Kurang</option>
                    <option value="cukup">Cukup</option>
                    <option value="baik">Baik</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="nav-btns">
            <button class="btn btn-secondary" @click="goTo(1)">← Kembali</button>
            <button class="btn btn-primary" @click="goTo(3)">
              Lanjut: Deteksi Dini Obesitas →
            </button>
          </div>
        </div>

        <!-- STEP 3: OBESITAS -->
        <div v-if="currentStep === 3" class="step-panel">
          <div class="breadcrumb">Skrining PTM <span>› Deteksi Dini Obesitas</span></div>
          <div class="page-title">
            <h2>Deteksi Dini Obesitas</h2>
            <p>Observation FHIR — Berat Badan, Tinggi Badan, IMT, Lingkar Pinggang</p>
          </div>
          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #e8f5e9">⚖️</div>
              <div>
                <h3>Antropometri</h3>
                <div class="sub">Observation LOINC — pengukuran fisik dasar</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-3 field">
                  <label class="label">Berat Badan</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.bb"
                      type="number"
                      min="0"
                      step="0.1"
                      placeholder="0.0"
                      @input="hitungIMT"
                    />
                    <span class="addon">kg</span>
                  </div>
                  <span class="hint">LOINC 29463-7</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Tinggi Badan</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.tb"
                      type="number"
                      min="0"
                      step="0.1"
                      placeholder="0.0"
                      @input="hitungIMT"
                    />
                    <span class="addon">cm</span>
                  </div>
                  <span class="hint">LOINC 8302-2</span>
                </div>
                <div class="col-3 field">
                  <label class="label">IMT (auto-hitung)</label>
                  <div class="input-group">
                    <input v-model="form.imt" type="text" readonly placeholder="—" />
                    <span class="addon">kg/m²</span>
                  </div>
                  <span class="hint">LOINC 39156-5</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Interpretasi IMT</label>
                  <input v-model="form.imt_interp" type="text" readonly placeholder="—" />
                  <span class="hint">Observation.interpretation</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Lingkar Pinggang</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.lp"
                      type="number"
                      min="0"
                      step="0.1"
                      placeholder="0.0"
                      @input="interpretLP"
                    />
                    <span class="addon">cm</span>
                  </div>
                  <span class="hint">LOINC 8280-0</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Interpretasi Lingkar Pinggang</label>
                  <input v-model="form.lp_interp" type="text" readonly placeholder="—" />
                  <span class="hint">Observation.interpretation</span>
                </div>
              </div>
            </div>
          </div>
          <div class="nav-btns">
            <button class="btn btn-secondary" @click="goTo(2)">← Kembali</button>
            <button class="btn btn-primary" @click="goTo(4)">Lanjut: Hipertensi →</button>
          </div>
        </div>

        <!-- STEP 4: HIPERTENSI -->
        <div v-if="currentStep === 4" class="step-panel">
          <div class="breadcrumb">Skrining PTM <span>› Deteksi Dini Hipertensi</span></div>
          <div class="page-title">
            <h2>Deteksi Dini Hipertensi</h2>
            <p>Observation FHIR — Tekanan Darah &amp; Interpretasi</p>
          </div>
          <div class="card">
            <div class="card-head">
              <div class="icon" style="background: #fde8e8">❤️</div>
              <div>
                <h3>Pengukuran Tekanan Darah</h3>
                <div class="sub">LOINC 8480-6 (sistolik), 8462-4 (diastolik)</div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-3 field">
                  <label class="label">Tekanan Darah Sistolik</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.td_s"
                      type="number"
                      min="0"
                      placeholder="120"
                      @input="interpretTD"
                    />
                    <span class="addon">mmHg</span>
                  </div>
                  <span class="hint">LOINC 8480-6</span>
                </div>
                <div class="col-3 field">
                  <label class="label">Tekanan Darah Diastolik</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.td_d"
                      type="number"
                      min="0"
                      placeholder="80"
                      @input="interpretTD"
                    />
                    <span class="addon">mmHg</span>
                  </div>
                  <span class="hint">LOINC 8462-4</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Interpretasi Hipertensi</label>
                  <input v-model="form.td_interp" type="text" readonly placeholder="—" />
                  <span class="hint">Observation.interpretation (KMK 4634/2021)</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Frekuensi Nadi</label>
                  <div class="input-group">
                    <input v-model.number="form.nadi" type="number" min="0" placeholder="80" />
                    <span class="addon">x/mnt</span>
                  </div>
                  <span class="hint">LOINC 8867-4</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Frekuensi Napas</label>
                  <div class="input-group">
                    <input v-model.number="form.napas" type="number" min="0" placeholder="18" />
                    <span class="addon">x/mnt</span>
                  </div>
                  <span class="hint">LOINC 9279-1</span>
                </div>
                <div class="col-4 field">
                  <label class="label">Suhu Tubuh</label>
                  <div class="input-group">
                    <input
                      v-model.number="form.suhu"
                      type="number"
                      min="0"
                      step="0.1"
                      placeholder="36.5"
                    />
                    <span class="addon">°C</span>
                  </div>
                  <span class="hint">LOINC 8310-5</span>
                </div>
              </div>
            </div>
          </div>
          <div class="nav-btns">
            <button class="btn btn-secondary" @click="goTo(3)">← Kembali</button>
            <button class="btn btn-primary" @click="goTo(5)">Lanjut: Gangguan Penglihatan →</button>
          </div>
        </div>

        <!-- STEPS 5-15: PLACEHOLDER -->
        <div v-if="currentStep >= 5" class="step-panel">
          <div class="breadcrumb">
            Skrining PTM <span>› {{ getPageTitle(currentStep) }}</span>
          </div>
          <div class="page-title">
            <h2>{{ getPageTitle(currentStep) }}</h2>
            <p>Silakan lengkapi form ini...</p>
          </div>
          <div class="card">
            <div class="card-body" style="text-align: center; padding: 40px">
              <p style="color: #5e6c84; margin-bottom: 20px">
                Step {{ currentStep }} belum diimplementasikan
              </p>
              <p style="color: #97a0af; font-size: 12px">
                Anda bisa menambahkan form fields untuk step ini
              </p>
            </div>
          </div>
          <div class="nav-btns">
            <button class="btn btn-secondary" @click="goTo(currentStep - 1)">← Kembali</button>
            <button
              v-if="currentStep < totalSteps"
              class="btn btn-primary"
              @click="goTo(currentStep + 1)"
            >
              Lanjut →
            </button>
            <button v-else class="btn btn-primary" @click="submitForm">
              🚀 Kirim ke SATUSEHAT
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'SkriningPTMSingleFile',
    data() {
      return {
        currentStep: 1,
        totalSteps: 15,
        form: {
          // Step 1
          tgl_skrining: new Date().toISOString().split('T')[0],
          jenis_kunjungan: '',
          pemeriksa: '',
          faskes: '',
          keluhan: '',
          ihs: '',
          nik: '',
          nama: '',
          tgl_lahir: '',
          jk: '',
          nohp: '',
          status_kawin: '',
          wn: 'ID',
          alamat: '',
          // Step 2
          merokok: '',
          status_merokok: '',
          btg_rokok: 0,
          lama_rokok: 0,
          paparan_rokok: 'tidak',
          gula: 'tidak',
          garam: 'tidak',
          minyak: 'tidak',
          sayur: 'tidak',
          aktivitas: 'tidak',
          alkohol: 'tidak',
          r_htn: 'tidak',
          r_dm: 'tidak',
          r_stroke: 'tidak',
          r_pribadi_lain: '',
          r_keluarga: '',
          obat: '',
          kesiapan: 'tidak_siap',
          dukung: 'kurang',
          // Step 3
          bb: 0,
          tb: 0,
          imt: '',
          imt_interp: '',
          lp: 0,
          lp_interp: '',
          // Step 4
          td_s: 0,
          td_d: 0,
          td_interp: '',
          nadi: 0,
          napas: 0,
          suhu: 0,
        },
      };
    },
    methods: {
      goTo(stepNumber) {
        this.currentStep = stepNumber;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
      getNavLabel(step) {
        const labels = {
          3: 'Obesitas',
          4: 'Hipertensi',
          5: 'Gangguan Penglihatan',
          6: 'Gangguan Pendengaran',
          7: 'PPOK',
          8: 'Kanker',
          9: 'Diabetes Melitus',
          10: 'Stroke &amp; Lipid',
          11: 'Asam Urat',
          12: 'Thalasemia',
          13: 'Penyakit Jantung',
        };
        return labels[step] || `Step ${step}`;
      },
      getPageTitle(step) {
        const titles = {
          5: 'Deteksi Dini Gangguan Penglihatan',
          6: 'Deteksi Dini Gangguan Pendengaran',
          7: 'Deteksi Dini PPOK',
          8: 'Deteksi Dini Kanker',
          9: 'Deteksi Dini Diabetes Melitus',
          10: 'Deteksi Dini Stroke',
          11: 'Deteksi Dini Asam Urat',
          12: 'Deteksi Dini Thalasemia',
          13: 'Deteksi Dini Penyakit Jantung',
          14: 'Diagnosis &amp; Tindak Lanjut',
          15: 'Ringkasan &amp; Kirim',
        };
        return titles[step] || `Step ${step}`;
      },
      hitungIMT() {
        const bb = parseFloat(this.form.bb);
        const tb = parseFloat(this.form.tb);
        if (bb > 0 && tb > 0) {
          const imt = bb / (tb / 100) ** 2;
          this.form.imt = imt.toFixed(1);
          let label = '';
          if (imt < 18.5) label = '🔵 Kurus';
          else if (imt < 23) label = '🟢 Normal';
          else if (imt < 27) label = '🟡 Gemuk';
          else label = '🔴 Obesitas';
          this.form.imt_interp = label;
        }
      },
      interpretLP() {
        const jk = this.form.jk;
        const lp = parseFloat(this.form.lp);
        if (!isNaN(lp)) {
          const batas = jk === 'female' ? 80 : 90;
          this.form.lp_interp =
            lp >= batas ? `🔴 Berisiko (≥${batas} cm)` : `🟢 Normal (<${batas} cm)`;
        }
      },
      interpretTD() {
        const s = parseInt(this.form.td_s);
        const d = parseInt(this.form.td_d);
        if (!isNaN(s) && !isNaN(d)) {
          let label = '';
          if (s < 120 && d < 80) label = '🟢 Normal';
          else if (s < 130 && d < 80) label = '🟡 Elevated';
          else if (s < 140 || d < 90) label = '🟠 Hipertensi Grade 1';
          else if (s < 180 || d < 110) label = '🔴 Hipertensi Grade 2';
          else label = '⛔ Krisis Hipertensi';
          this.form.td_interp = label;
        }
      },
      updateInterpretasi() {
        this.interpretLP();
      },
      submitForm() {
        alert('Form akan dikirim ke SATUSEHAT');
        console.log('Form data:', this.form);
      },
    },
  };
</script>

<style scoped>
  :root {
    --hijau: #00875a;
    --hijau-muda: #e3fcef;
    --hijau-dark: #005c3d;
    --biru: #0052cc;
    --biru-muda: #deebff;
    --merah: #c0392b;
    --merah-muda: #fff0ee;
    --kuning: #ff8b00;
    --kuning-muda: #fffae6;
    --abu: #f4f5f7;
    --abu-2: #dfe1e6;
    --abu-3: #97a0af;
    --teks: #172b4d;
    --teks-2: #344563;
    --teks-3: #5e6c84;
    --putih: #ffffff;
    --radius: 10px;
    --shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  .app-wrapper {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #eef0f3;
    color: var(--teks);
    font-size: 14px;
    line-height: 1.6;
    min-height: 100vh;
  }

  /* ===== HEADER ===== */
  .app-header {
    background: linear-gradient(135deg, var(--hijau-dark) 0%, var(--hijau) 100%);
    color: #fff;
    padding: 18px 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    position: sticky;
    top: 0;
    z-index: 200;
    box-shadow: 0 2px 16px rgba(0, 87, 58, 0.25);
    position: relative;
    overflow: hidden;
  }

  .app-header .logo {
    width: 38px;
    height: 38px;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -1px;
  }

  .app-header .title h1 {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: -0.3px;
  }

  .app-header .title p {
    font-size: 12px;
    opacity: 0.8;
    font-weight: 500;
  }

  .app-header .badge {
    margin-left: auto;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  /* ===== LAYOUT ===== */
  .container {
    display: flex;
    min-height: calc(100vh - 66px);
  }

  .sidebar {
    width: 260px;
    background: var(--putih);
    border-right: 1px solid var(--abu-2);
    position: sticky;
    top: 66px;
    height: calc(100vh - 66px);
    overflow-y: auto;
    padding: 16px 0;
    flex-shrink: 0;
  }

  .main {
    flex: 1;
    padding: 24px 28px;
    overflow-x: hidden;
    max-width: 900px;
  }

  /* ===== SIDEBAR NAV ===== */
  .nav-section {
    padding: 8px 16px 4px;
    font-size: 10px;
    font-weight: 700;
    color: var(--abu-3);
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all 0.18s;
    color: var(--teks-3);
    font-weight: 500;
    font-size: 13px;
  }

  .nav-item:hover {
    background: var(--abu);
    color: var(--teks);
  }

  .nav-item.active {
    border-left-color: var(--hijau);
    background: var(--hijau-muda);
    color: var(--hijau-dark);
    font-weight: 700;
  }

  .nav-item .nav-num {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--abu-2);
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .nav-item.active .nav-num {
    background: var(--hijau);
    color: #fff;
  }

  .nav-item.done .nav-num {
    background: var(--hijau-muda);
    color: var(--hijau);
  }

  /* ===== STEP PANELS ===== */
  .step-panel {
    animation: fadeIn 0.25s ease;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(8px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ===== CARDS ===== */
  .card {
    background: var(--putih);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .card-head {
    padding: 16px 20px;
    border-bottom: 1px solid var(--abu);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .card-head .icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .card-head h3 {
    font-size: 14px;
    font-weight: 700;
    color: var(--teks);
  }

  .card-head .sub {
    font-size: 11px;
    color: var(--teks-3);
    margin-top: 1px;
  }

  .card-body {
    padding: 20px;
  }

  /* ===== FORM ELEMENTS ===== */
  .row {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 14px;
  }

  .col-3 {
    grid-column: span 3;
  }
  .col-4 {
    grid-column: span 4;
  }
  .col-6 {
    grid-column: span 6;
  }
  .col-12 {
    grid-column: span 12;
  }

  @media (max-width: 720px) {
    .col-3,
    .col-4,
    .col-6,
    .col-12 {
      grid-column: span 12;
    }
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .label {
    font-size: 12px;
    font-weight: 700;
    color: var(--teks-2);
  }

  .hint {
    font-size: 10.5px;
    color: var(--teks-3);
    font-family: 'JetBrains Mono', monospace;
  }

  input[type='text'],
  input[type='date'],
  input[type='number'],
  select,
  textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid var(--abu-2);
    border-radius: 7px;
    font-family: inherit;
    font-size: 13px;
    color: var(--teks);
    background: var(--putih);
    transition:
      border 0.18s,
      box-shadow 0.18s;
    outline: none;
  }

  input:focus,
  select:focus,
  textarea:focus {
    border-color: var(--hijau);
    box-shadow: 0 0 0 3px rgba(0, 135, 90, 0.1);
  }

  input[readonly] {
    background: var(--abu);
    color: var(--teks-3);
  }

  textarea {
    resize: vertical;
    min-height: 72px;
  }

  .input-group {
    display: flex;
  }

  .input-group input {
    border-radius: 7px 0 0 7px;
    border-right: none;
  }

  .input-group .addon {
    padding: 9px 12px;
    background: var(--abu);
    border: 1.5px solid var(--abu-2);
    border-radius: 0 7px 7px 0;
    font-size: 12px;
    color: var(--teks-3);
    white-space: nowrap;
    font-weight: 600;
  }

  /* radio group */
  .radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .radio-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border: 1.5px solid var(--abu-2);
    border-radius: 7px;
    cursor: pointer;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--teks-2);
    transition: all 0.15s;
    user-select: none;
  }

  .radio-btn input {
    display: none;
  }

  .radio-btn:hover {
    border-color: var(--hijau);
    color: var(--hijau);
  }

  .radio-btn.checked {
    border-color: var(--hijau);
    background: var(--hijau-muda);
    color: var(--hijau-dark);
    font-weight: 700;
  }

  .radio-btn .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid var(--abu-2);
    transition: 0.15s;
    flex-shrink: 0;
  }

  .radio-btn.checked .dot {
    border-color: var(--hijau);
    background: var(--hijau);
  }

  /* alert */
  .alert {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .alert-info {
    background: var(--biru-muda);
    border: 1px solid #b3d4ff;
    color: #003893;
  }

  .alert-warn {
    background: var(--kuning-muda);
    border: 1px solid #ffe29f;
    color: #7a4f00;
  }

  /* step page title */
  .page-title {
    margin-bottom: 20px;
  }

  .page-title h2 {
    font-size: 20px;
    font-weight: 800;
    color: var(--teks);
    letter-spacing: -0.4px;
  }

  .page-title p {
    font-size: 13px;
    color: var(--teks-3);
    margin-top: 3px;
  }

  .breadcrumb {
    font-size: 11px;
    color: var(--teks-3);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .breadcrumb span {
    color: var(--hijau);
    font-weight: 600;
  }

  /* nav buttons */
  .nav-btns {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--abu);
  }

  .btn {
    padding: 10px 22px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.18s;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .btn-primary {
    background: var(--hijau);
    color: #fff;
  }

  .btn-primary:hover {
    background: var(--hijau-dark);
  }

  .btn-secondary {
    background: var(--abu);
    color: var(--teks-2);
    border: 1px solid var(--abu-2);
  }

  .btn-secondary:hover {
    background: var(--abu-2);
  }

  /* progress bar top */
  .progress-top {
    background: rgba(255, 255, 255, 0.2);
    height: 3px;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
  }

  .progress-fill {
    height: 100%;
    background: #fff;
    transition: width 0.4s ease;
    border-radius: 0 2px 2px 0;
  }

  /* scrollbar */
  .sidebar::-webkit-scrollbar {
    width: 4px;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: var(--abu-2);
    border-radius: 4px;
  }
</style>
