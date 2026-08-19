<template>
  <div class="planning-wrapper bg-white rounded-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
      <div>
        <h6 class="fw-bold text-success mb-1">
          P – PLANNING MTBS
        </h6>
        <small class="text-muted">
          Planning menampilkan rekomendasi rule-based sistem, diagnosis medis dokter, serta ringkasan pemeriksaan pasien.
        </small>
      </div>

      <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        :disabled="loadingRekomendasi || !idPelayanan"
        @click="loadRekomendasiPlanning"
      >
        <span
          v-if="loadingRekomendasi"
          class="spinner-border spinner-border-sm me-1"
        ></span>
        {{ loadingRekomendasi ? 'Memuat...' : 'Refresh Assessment' }}
      </button>
    </div>

    <div class="row g-3 align-items-start planning-layout">
      <!-- AREA UTAMA PLANNING -->
      <div class="col-xl-8 col-lg-7 order-2 order-lg-1">
        <!-- REKOMENDASI RULE-BASED: TINDAKAN DAN OBAT DIGABUNG -->
        <div class="assessment-source-card system-assessment-card mb-3">
          <div class="assessment-source-header">
            <div>
              <div class="fw-bold text-primary">
                Rekomendasi Sistem Berdasarkan Assessment MTBS
              </div>
              <small class="text-muted">
                Rekomendasi tindakan dan pengobatan ditampilkan bersamaan agar hasil rule-based mudah diperiksa dan didokumentasikan.
              </small>
            </div>

            <span class="badge bg-primary">Sistem</span>
          </div>

          <div v-if="loadingRekomendasi" class="text-muted py-3">
            Memuat rekomendasi rule-based...
          </div>

          <div v-else-if="rekomendasiGabungan.length === 0" class="text-muted py-2">
            Tidak ada rekomendasi tindakan maupun obat untuk hasil Assessment MTBS ini.
          </div>

          <div v-else>
            <div
              v-for="r in rekomendasiGabungan"
              :key="`${r.sumber || 'sistem'}-${r.klasifikasi}`"
              class="recommendation-item"
            >
              <div class="recommendation-classification">
                {{ r.klasifikasi }}
              </div>

              <div class="row g-3">
                <!-- REKOMENDASI TINDAKAN -->
                <div class="col-md-6">
                  <div class="recommendation-group recommendation-action-group h-100">
                    <div class="recommendation-group-title text-primary">
                      Rekomendasi Tindakan
                    </div>

                    <div
                      v-if="r.tindakan.length === 0"
                      class="recommendation-empty"
                    >
                      Tidak ada rekomendasi tindakan.
                    </div>

                    <ul v-else class="mb-0 ps-3">
                      <li
                        v-for="item in r.tindakan"
                        :key="`tindakan-${r.klasifikasi}-${item}`"
                        class="mb-2"
                      >
                        <span>{{ item }}</span>

                        <button
                          v-if="bisaPakaiTindakan(item)"
                          type="button"
                          class="btn btn-sm btn-outline-primary ms-2 py-0"
                          @click="pakaiRekomendasiTindakan(item)"
                        >
                          Pakai tindakan
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>

                <!-- REKOMENDASI PENGOBATAN -->
                <div class="col-md-6">
                  <div class="recommendation-group recommendation-medicine-group h-100">
                    <div class="recommendation-group-title text-success">
                      Rekomendasi Obat
                    </div>

                    <div
                      v-if="r.pengobatan.length === 0"
                      class="recommendation-empty"
                    >
                      Tidak ada rekomendasi obat.
                    </div>

                    <ul v-else class="mb-0 ps-3">
                      <li
                        v-for="item in r.pengobatan"
                        :key="`pengobatan-${r.klasifikasi}-${item}`"
                        class="mb-2"
                      >
                        <span>{{ item }}</span>

                        <button
                          v-if="bisaCariObat(item)"
                          type="button"
                          class="btn btn-sm btn-outline-success ms-2 py-0"
                          @click="pakaiRekomendasiObat(item)"
                        >
                          Cari obat ini
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- HASIL DIAGNOSIS DOKTER -->
        <div class="assessment-source-card doctor-assessment-card mb-4">
          <div class="assessment-source-header">
            <div>
              <div class="fw-bold text-success">
                Hasil Diagnosis Dokter
              </div>
              <small class="text-muted">
                Diagnosis medis yang dipilih dan disimpan dokter pada menu Assessment.
              </small>
            </div>
            <span class="badge bg-success">Dokter</span>
          </div>

          <div v-if="loadingRekomendasi" class="text-muted py-3">
            Memuat hasil diagnosis dokter...
          </div>

          <div v-else-if="diagnosaDokter.length === 0" class="text-muted py-2">
            Belum ada diagnosis dokter. Tambahkan dan simpan diagnosis medis pada menu Assessment.
          </div>

          <div v-else class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 50px;">No</th>
                  <th style="width: 110px;">Kode</th>
                  <th>Diagnosis Medis</th>
                  <th style="width: 90px;">Kasus</th>
                  <th>Keterangan</th>
                  <th style="width: 150px;">Dokter/Petugas</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(d, index) in diagnosaDokter" :key="d.id || index">
                  <td class="text-center">{{ index + 1 }}</td>
                  <td>{{ d.kodeDiagnosa || '-' }}</td>
                  <td class="fw-semibold">{{ d.namaDiagnosa || '-' }}</td>
                  <td>
                    <span
                      class="badge"
                      :class="d.kasus === 'baru' ? 'bg-info text-dark' : 'bg-secondary'"
                    >
                      {{ d.kasus === 'baru' ? 'Baru' : d.kasus === 'lama' ? 'Lama' : '-' }}
                    </span>
                  </td>
                  <td>{{ d.keterangan || '-' }}</td>
                  <td>{{ d.dokterPetugas || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB INPUT PLANNING -->
        <ul class="nav nav-tabs simpus-tabs mb-3">
          <li class="nav-item">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'tindakan' }"
              @click="activeTab = 'tindakan'"
            >
              Tindakan
            </button>
          </li>

          <li class="nav-item">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'pengobatan' }"
              @click="activeTab = 'pengobatan'"
            >
              Pengobatan
            </button>
          </li>
        </ul>

        <!-- TAB TINDAKAN -->
        <div v-if="activeTab === 'tindakan'">
          <div class="form-area">
            <div class="row mb-2 align-items-start">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Kode Tindakan
              </label>

              <div class="col-md-7">
                <div class="input-group input-group-sm">
                  <input
                    class="form-control"
                    v-model="tindakan.kode"
                    placeholder="Kode tindakan"
                    autocomplete="off"
                    @keyup.enter="bukaModalTindakan"
                  />

                  <button
                    type="button"
                    class="btn btn-info text-white"
                    @click="bukaModalTindakan"
                  >
                    Cari
                  </button>

                  <button
                    type="button"
                    class="btn btn-danger"
                    @click="resetTindakan"
                  >
                    Del
                  </button>
                </div>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Nama Tindakan
              </label>
              <div class="col-md-7">
                <textarea
                  class="form-control form-control-sm"
                  rows="1"
                  v-model="tindakan.nama"
                  placeholder="Nama tindakan"
                ></textarea>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Nama Tindakan(Ind)
              </label>
              <div class="col-md-7">
                <textarea
                  class="form-control form-control-sm"
                  rows="1"
                  v-model="tindakan.nama_ind"
                  placeholder="Nama tindakan Indonesia"
                ></textarea>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Keterangan
              </label>
              <div class="col-md-7">
                <textarea
                  class="form-control form-control-sm"
                  rows="2"
                  v-model="tindakan.keterangan"
                  placeholder="Keterangan"
                ></textarea>
              </div>
            </div>

            <div class="mt-2">
              <button
                type="button"
                class="btn btn-sm btn-primary"
                @click="tambahTindakan"
              >
                Simpan
              </button>
            </div>
          </div>

          <hr class="my-3" />

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
              <thead>
                <tr>
                  <th style="width: 50px;">No</th>
                  <th style="width: 100px;">Kode</th>
                  <th>Nama Tindakan</th>
                  <th>Peraturan</th>
                  <th>Harga</th>
                  <th>Bayar</th>
                  <th>Poli</th>
                  <th>Keterangan</th>
                  <th>Ket gigi</th>
                  <th>Created by</th>
                  <th style="width: 90px;">Action</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(t, i) in form.tindakan" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ t.kode || '-' }}</td>
                  <td>{{ t.nama || '-' }}</td>
                  <td>{{ t.peraturan || '' }}</td>
                  <td>{{ t.harga || '' }}</td>
                  <td>{{ t.bayar || '' }}</td>
                  <td>{{ t.poli || '-' }}</td>
                  <td>{{ t.keterangan || '' }}</td>
                  <td>{{ t.ket_gigi || '' }}</td>
                  <td>{{ t.created_by || '-' }}</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-sm btn-danger"
                      @click="hapusTindakan(i)"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>

                <tr v-if="form.tindakan.length === 0">
                  <td colspan="11" class="text-center text-muted">
                    Belum ada tindakan
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB PENGOBATAN -->
        <div v-if="activeTab === 'pengobatan'">
          <div class="form-area">
            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Nama Obat
              </label>

              <div class="col-md-5 position-relative">
                <input
                  class="form-control form-control-sm"
                  v-model="searchObat"
                  placeholder="Cari dan pilih obat..."
                  autocomplete="off"
                  @input="loadObat"
                  @focus="bukaDropdownObat"
                />

                <div
                  v-if="showDropdownObat && daftarObat.length > 0"
                  class="list-group position-absolute w-100 shadow-sm mt-1"
                  style="z-index: 9999; max-height: 250px; overflow-y: auto;"
                >
                  <button
                    v-for="o in daftarObat"
                    :key="o.obat_id"
                    type="button"
                    class="list-group-item list-group-item-action text-start"
                    @click="pilihObat(o)"
                  >
                    <div class="fw-semibold">{{ o.nama }}</div>
                    <small class="text-muted">{{ o.satuan || '' }}</small>
                  </button>
                </div>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Dosis
              </label>
              <div class="col-md-5">
                <input
                  class="form-control form-control-sm"
                  v-model="obat.dosis"
                  placeholder="Dosis"
                />
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Cara Pemberian
              </label>
              <div class="col-md-5">
                <select class="form-select form-select-sm" v-model="obat.cara">
                  <option value="">-- Pilih --</option>
                  <option value="oral">Oral</option>
                  <option value="suntik">Suntik</option>
                  <option value="infus">Infus</option>
                </select>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-2 col-form-label text-md-end fw-semibold">
                Lama Hari
              </label>
              <div class="col-md-5">
                <input
                  type="number"
                  class="form-control form-control-sm"
                  v-model.number="obat.lama"
                  min="0"
                />
              </div>
            </div>

            <div class="mt-2">
              <button
                type="button"
                class="btn btn-sm btn-primary"
                @click="tambahObat"
              >
                Simpan
              </button>
            </div>
          </div>

          <hr class="my-3" />

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
              <thead>
                <tr>
                  <th style="width: 50px;">No</th>
                  <th>Nama Obat</th>
                  <th>Dosis</th>
                  <th>Cara</th>
                  <th>Lama</th>
                  <th style="width: 90px;">Action</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(o, i) in form.pengobatan" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>
                    {{ o.nama }}
                    <span v-if="o.satuan">({{ o.satuan }})</span>
                  </td>
                  <td>{{ o.dosis || '-' }}</td>
                  <td>{{ o.cara || '-' }}</td>
                  <td>{{ o.lama || 0 }} hari</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-sm btn-danger"
                      @click="hapusObat(i)"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>

                <tr v-if="form.pengobatan.length === 0">
                  <td colspan="6" class="text-center text-muted">
                    Belum ada pengobatan
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- EDUKASI -->
        <hr class="my-3" />

        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-semibold">Edukasi Ibu</h6>

            <div class="row">
              <div class="col-md-6" v-for="e in edukasiList" :key="e">
                <div class="form-check mb-1">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :value="e"
                    v-model="form.edukasi"
                  />
                  <label class="form-check-label">{{ e }}</label>
                </div>
              </div>
            </div>

            <textarea
              class="form-control form-control-sm mt-2"
              rows="2"
              placeholder="Catatan edukasi tambahan"
              v-model="form.catatanEdukasi"
            ></textarea>
          </div>

          <div class="col-md-6">
            <h6 class="fw-semibold">Rencana Kunjungan Ulang</h6>

            <div class="d-flex gap-4 flex-wrap">
              <div class="form-check" v-for="hari in [2, 5, 7, 14]" :key="hari">
                <input
                  class="form-check-input"
                  type="radio"
                  :value="hari"
                  v-model.number="form.kunjunganUlang"
                />
                <label class="form-check-label">{{ hari }} hari</label>
              </div>
            </div>
          </div>
        </div>

        <div class="text-end mt-3">
          <button
            class="btn btn-success btn-sm"
            :disabled="loading"
            @click="simpanPlanning"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan Planning MTBS' }}
          </button>
        </div>
      </div>

      <!-- RINGKASAN S/O DI KANAN ATAS -->
      <div class="col-xl-4 col-lg-5 order-1 order-lg-2">
        <div class="planning-summary-sticky">
          <!-- RINGKASAN SUBJEKTIF & OBJEKTIF -->
          <div class="clinical-summary-large clinical-summary-sidebar">
            <div class="clinical-summary-large-header">
              <div>
                <div class="clinical-summary-large-title">
                  Ringkasan Subjektif & Objektif
                </div>

                <div class="clinical-summary-large-subtitle">
                  Data penting dari pemeriksaan sebelumnya.
                </div>
              </div>

              <button
                type="button"
                class="btn btn-outline-success summary-refresh-button"
                :disabled="loadingRingkasan || !idPelayanan"
                @click="loadRingkasan"
              >
                <span
                  v-if="loadingRingkasan"
                  class="spinner-border spinner-border-sm me-2"
                ></span>

                {{ loadingRingkasan ? 'Memuat...' : 'Refresh Ringkasan' }}
              </button>
            </div>

            <div
              v-if="loadingRingkasan"
              class="clinical-summary-large-loading"
            >
              Memuat ringkasan Subjektif dan Objektif...
            </div>

            <div v-else class="clinical-summary-large-body">
              <div class="row g-3">
                <!-- SUBJEKTIF -->
                <div class="col-12">
                  <div class="clinical-summary-panel h-100">
                    <div class="clinical-summary-panel-header">
                      <span class="clinical-summary-icon subjective-icon">
                        S
                      </span>

                      <div>
                        <div class="clinical-summary-panel-title">
                          Subjektif
                        </div>

                        <div class="clinical-summary-panel-subtitle">
                          Keluhan dan riwayat penting pasien
                        </div>
                      </div>
                    </div>

                    <div
                      v-if="!ringkasan.subjektif_ada"
                      class="clinical-summary-empty"
                    >
                      Data Subjektif belum diisi.
                    </div>

                    <template v-else>
                      <div class="clinical-summary-section">
                        <div class="clinical-summary-label">
                          Keluhan utama
                        </div>

                        <div class="clinical-summary-text clinical-summary-main-text">
                          {{
                            formatKeluhan(
                              ringkasan.subjektif?.keluhanUtama,
                              ringkasan.subjektif?.keluhanLain,
                            )
                          }}
                        </div>
                      </div>

                      <div
                        v-if="hasDurasi(ringkasan.subjektif?.durasiKeluhan)"
                        class="clinical-summary-section"
                      >
                        <div class="clinical-summary-label">
                          Lama keluhan
                        </div>

                        <div class="clinical-summary-chip-list">
                          <span
                            v-if="isFilled(ringkasan.subjektif?.durasiKeluhan?.batuk)"
                            class="clinical-summary-chip"
                          >
                            Batuk {{ ringkasan.subjektif.durasiKeluhan.batuk }} hari
                          </span>

                          <span
                            v-if="isFilled(ringkasan.subjektif?.durasiKeluhan?.diare)"
                            class="clinical-summary-chip"
                          >
                            Diare {{ ringkasan.subjektif.durasiKeluhan.diare }} hari
                          </span>

                          <span
                            v-if="isFilled(ringkasan.subjektif?.durasiKeluhan?.demam)"
                            class="clinical-summary-chip"
                          >
                            Demam {{ ringkasan.subjektif.durasiKeluhan.demam }} hari
                          </span>

                          <span
                            v-if="isFilled(ringkasan.subjektif?.durasiKeluhan?.telinga)"
                            class="clinical-summary-chip"
                          >
                            Telinga {{ ringkasan.subjektif.durasiKeluhan.telinga }} hari
                          </span>
                        </div>
                      </div>

                      <div
                        v-if="ringkasan.subjektif?.temuanPenting?.length"
                        class="clinical-summary-section mb-0"
                      >
                        <div class="clinical-summary-label">
                          Temuan penting
                        </div>

                        <div class="clinical-summary-chip-list">
                          <span
                            v-for="item in ringkasan.subjektif.temuanPenting"
                            :key="item"
                            class="clinical-summary-chip warning-chip"
                          >
                            {{ item }}
                          </span>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>

                <!-- OBJEKTIF -->
                <div class="col-12">
                  <div class="clinical-summary-panel h-100">
                    <div class="clinical-summary-panel-header objective-header">
                      <span class="clinical-summary-icon objective-icon">
                        O
                      </span>

                      <div>
                        <div class="clinical-summary-panel-title">
                          Objektif
                        </div>

                        <div class="clinical-summary-panel-subtitle">
                          Tanda vital, antropometri, dan hasil pemeriksaan
                        </div>
                      </div>

                      <span
                        v-if="ringkasan.objektif_ada && ringkasan.objektif?.statusSAGA"
                        class="clinical-status-badge ms-auto"
                        :class="statusSagaClass(ringkasan.objektif.statusSAGA)"
                      >
                        {{ ringkasan.objektif.statusSAGA }}
                      </span>
                    </div>

                    <div
                      v-if="!ringkasan.objektif_ada"
                      class="clinical-summary-empty"
                    >
                      Data Objektif belum diisi.
                    </div>

                    <template v-else>
                      <div class="clinical-metric-grid">
                        <div
                          v-for="item in objektifUtama(ringkasan.objektif)"
                          :key="item.label"
                          class="clinical-metric-card"
                        >
                          <span>{{ item.label }}</span>
                          <strong>{{ item.value }}</strong>
                        </div>
                      </div>

                      <div
                        v-if="ringkasan.objektif?.tandaBahaya?.length"
                        class="clinical-summary-section"
                      >
                        <div class="clinical-summary-label">
                          Tanda bahaya
                        </div>

                        <div class="clinical-summary-chip-list">
                          <span
                            v-for="item in ringkasan.objektif.tandaBahaya"
                            :key="item"
                            class="clinical-summary-chip danger-chip"
                          >
                            {{ item }}
                          </span>
                        </div>
                      </div>

                      <div
                        v-if="ringkasan.objektif?.temuanSAGA?.length"
                        class="clinical-summary-section"
                      >
                        <div class="clinical-summary-label">
                          Temuan SAGA
                        </div>

                        <div class="clinical-summary-chip-list">
                          <span
                            v-for="item in ringkasan.objektif.temuanSAGA"
                            :key="item"
                            class="clinical-summary-chip warning-chip"
                          >
                            {{ item }}
                          </span>
                        </div>
                      </div>

                      <div
                        v-if="ringkasan.objektif?.pemeriksaanKhusus?.length"
                        class="clinical-summary-section mb-0"
                      >
                        <div class="clinical-summary-label">
                          Pemeriksaan khusus
                        </div>

                        <div class="clinical-summary-chip-list">
                          <span
                            v-for="item in ringkasan.objektif.pemeriksaanKhusus"
                            :key="item"
                            class="clinical-summary-chip"
                          >
                            {{ item }}
                          </span>
                        </div>
                      </div>

                      <div
                        v-if="
                          objektifUtama(ringkasan.objektif).length === 0
                          && !ringkasan.objektif?.tandaBahaya?.length
                          && !ringkasan.objektif?.temuanSAGA?.length
                          && !ringkasan.objektif?.pemeriksaanKhusus?.length
                        "
                        class="clinical-summary-empty"
                      >
                        Belum ada temuan objektif penting yang tercatat.
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL CARI TINDAKAN -->
    <div
      v-if="showModalTindakan"
      class="modal fade show d-block"
      tabindex="-1"
      role="dialog"
    >
      <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h6 class="modal-title fw-bold">
              Cari Tindakan
            </h6>

            <button
              type="button"
              class="btn-close"
              @click="tutupModalTindakan"
            ></button>
          </div>

          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-8">
                <input
                  ref="inputSearchTindakanRef"
                  type="text"
                  class="form-control form-control-sm"
                  v-model="searchTindakan"
                  placeholder="Ketik kode / nama tindakan..."
                  autocomplete="off"
                  @input="handleSearchTindakan"
                  @keyup.enter="loadTindakanModal"
                />
              </div>

              <div class="col-md-4">
                <button
                  type="button"
                  class="btn btn-sm btn-info text-white me-2"
                  @click="loadTindakanModal"
                >
                  Cari
                </button>

                <button
                  type="button"
                  class="btn btn-sm btn-secondary"
                  @click="resetSearchTindakan"
                >
                  Reset
                </button>
              </div>
            </div>

            <div v-if="loadingTindakan" class="text-muted py-3">
              Memuat data tindakan...
            </div>

            <div v-else class="table-responsive">
              <table class="table table-bordered table-sm align-middle">
                <thead>
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 110px;">Kode</th>
                    <th>Nama Tindakan</th>
                    <th>Nama Indonesia</th>
                    <th style="width: 120px;">Harga</th>
                    <th style="width: 140px;">Sim Tarif</th>
                    <th>Deskripsi</th>
                    <th style="width: 90px;">Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(t, i) in daftarTindakan" :key="t.id || `${t.kode}-${i}`">
                    <td>{{ i + 1 }}</td>
                    <td>{{ t.kode || '-' }}</td>
                    <td>{{ t.nama || '-' }}</td>
                    <td>{{ t.nama_ind || '-' }}</td>
                    <td>{{ t.harga || '' }}</td>
                    <td>{{ t.bayar || '' }}</td>
                    <td>{{ t.keterangan || '' }}</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        @click="pilihTindakan(t)"
                      >
                        Pilih
                      </button>
                    </td>
                  </tr>

                  <tr v-if="daftarTindakan.length === 0">
                    <td colspan="8" class="text-center text-muted">
                      Data tindakan tidak ditemukan
                    </td>
                  </tr>
                </tbody>
              </table>

              <small class="text-muted">
                Ditampilkan maksimal 10 data.
              </small>
            </div>
          </div>

          <div class="modal-footer py-2">
            <button
              type="button"
              class="btn btn-sm btn-secondary"
              @click="tutupModalTindakan"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModalTindakan" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, onMounted, onActivated, nextTick } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const idPelayanan = page.props.idPelayanan

const activeTab = ref('tindakan')
const loading = ref(false)
const loadingRekomendasi = ref(false)
const loadingRingkasan = ref(false)

const ringkasan = ref({
  subjektif_ada: false,
  objektif_ada: false,
  subjektif: null,
  objektif: null,
})

const rekomendasiPlanning = ref([])
const diagnosaDokter = ref([])

const kataKunciObat = [
  'amoksisilin',
  'amoxicillin',
  'ampisilin',
  'gentamisin',
  'antibiotik',
  'oralit',
  'zinc',
  'parasetamol',
  'paracetamol',
  'artesunat',
  'antimalaria',
  'dihidroartemisinin',
  'piperakuin',
  'primakuin',
  'vitamin a',
  'zat besi',
  'kotrimoksazol',
  'diazepam',
  'salbutamol',
  'kloramfenikol',
  'tetrasiklin',
  'kuinolon',
  'nistatin',
  'albendazol',
  'pirantel',
  'obat cacing',
  'oat',
  'resomal',
  'aspirin',
  'ibuprofen',
  'diklofenak',
  'nsaid',
  'pelega tenggorokan',
  'pereda batuk',
]

const itemAdalahObat = (item) => {
  const text = String(item || '').toLowerCase()

  return kataKunciObat.some((kata) => text.includes(kata))
}

const normalisasiRekomendasi = (items) => {
  if (!Array.isArray(items)) {
    return []
  }

  return Array.from(
    new Set(
      items
        .map((item) => String(item || '').trim())
        .filter(Boolean),
    ),
  )
}

/**
 * Backend sudah memisahkan rekomendasi menjadi tindakan dan pengobatan.
 * Keduanya ditampilkan bersamaan dalam satu panel di atas tab input.
 */
const rekomendasiGabungan = computed(() => {
  return rekomendasiPlanning.value
    .map((rekomendasi) => ({
      ...rekomendasi,
      tindakan: normalisasiRekomendasi(rekomendasi?.tindakan),
      pengobatan: normalisasiRekomendasi(rekomendasi?.pengobatan),
    }))
    .filter((rekomendasi) => {
      return rekomendasi.tindakan.length > 0
        || rekomendasi.pengobatan.length > 0
    })
})

const daftarTindakan = ref([])
const searchTindakan = ref('')
const showModalTindakan = ref(false)
const loadingTindakan = ref(false)
const inputSearchTindakanRef = ref(null)
let tindakanSearchTimer = null

const daftarObat = ref([])
const searchObat = ref('')
const showDropdownObat = ref(false)

const form = reactive({
  tindakan: [],
  pengobatan: [],
  edukasi: [],
  catatanEdukasi: '',
  kunjunganUlang: null,
})

const tindakan = reactive({
  id: '',
  kode: '',
  nama: '',
  nama_ind: '',
  keterangan: '',
  peraturan: '',
  harga: '',
  bayar: '',
  poli: 'Umum',
  ket_gigi: '',
  created_by: '',
})

const obat = reactive({
  obat_id: '',
  kode_obat: '',
  nama: '',
  satuan: '',
  dosis: '',
  cara: '',
  lama: null,
})

const edukasiList = [
  'Cara minum obat',
  'Pemberian makan / ASI',
  'Tanda bahaya',
  'Kapan harus kembali segera',
  'Pemberian cairan lebih banyak',
  'Lanjutkan makan selama sakit',
]


const isFilled = (value) => {
  return (
    value !== null
    && value !== undefined
    && String(value).trim() !== ''
  )
}

const formatMetric = (value, unit) => {
  if (!isFilled(value)) {
    return '-'
  }

  return `${value} ${unit}`
}

const formatKeluhan = (utama, lain) => {
  const items = Array.isArray(utama)
    ? utama.filter(Boolean)
    : []

  if (isFilled(lain)) {
    items.push(lain)
  }

  return items.length > 0
    ? items.join(', ')
    : 'Belum ada keluhan yang dicatat.'
}

const hasDurasi = (durasi) => {
  if (!durasi || typeof durasi !== 'object') {
    return false
  }

  return Object.values(durasi).some(isFilled)
}


const formatDurasiRingkas = (durasi) => {
  if (!durasi || typeof durasi !== 'object') {
    return '-'
  }

  const items = [
    ['Batuk', durasi.batuk],
    ['Diare', durasi.diare],
    ['Demam', durasi.demam],
    ['Telinga', durasi.telinga],
  ]
    .filter(([, value]) => isFilled(value))
    .map(([label, value]) => `${label} ${value} hari`)

  return items.length > 0
    ? items.join(' · ')
    : '-'
}

const objektifUtama = (data) => {
  if (!data || typeof data !== 'object') {
    return []
  }

  return [
    {
      label: 'BB',
      value: formatMetric(data.antropometri?.bb, 'kg'),
    },
    {
      label: 'TB/PB',
      value: formatMetric(data.antropometri?.tb, 'cm'),
    },
    {
      label: 'LiLA',
      value: formatMetric(data.antropometri?.lila, 'cm'),
    },
    {
      label: 'LK',
      value: formatMetric(data.antropometri?.lk, 'cm'),
    },
    {
      label: 'RR',
      value: formatMetric(data.vital?.rr, 'x/menit'),
    },
    {
      label: 'Suhu',
      value: formatMetric(data.vital?.suhu, '°C'),
    },
    {
      label: 'SpO₂',
      value: formatMetric(data.vital?.spo2, '%'),
    },
  ].filter((item) => item.value !== '-')
}

const statusSagaClass = (status) => {
  const value = String(status || '').toLowerCase()

  if (
    value.includes('gagal')
    || value.includes('berat')
    || value.includes('rujuk')
  ) {
    return 'saga-danger'
  }

  if (value.includes('stabil')) {
    return 'saga-success'
  }

  return 'saga-secondary'
}

const loadRingkasan = async () => {
  if (!idPelayanan) {
    ringkasan.value = {
      subjektif_ada: false,
      objektif_ada: false,
      subjektif: null,
      objektif: null,
    }
    return
  }

  try {
    loadingRingkasan.value = true

    const response = await axios.get(
      `/simpus/kia/mtbs/ringkasan/${idPelayanan}`,
      {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    ringkasan.value = response.data?.data ?? {
      subjektif_ada: false,
      objektif_ada: false,
      subjektif: null,
      objektif: null,
    }
  } catch (error) {
    console.error(
      'LOAD RINGKASAN MTBS ERROR:',
      error.response?.data || error,
    )

    ringkasan.value = {
      subjektif_ada: false,
      objektif_ada: false,
      subjektif: null,
      objektif: null,
    }
  } finally {
    loadingRingkasan.value = false
  }
}

const loadRekomendasiPlanning = async () => {
  if (!idPelayanan) return

  try {
    loadingRekomendasi.value = true

    const res = await axios.get(`/simpus/kia/mtbs/planning/rekomendasi/${idPelayanan}`)

    rekomendasiPlanning.value = Array.isArray(res.data?.rekomendasi_sistem)
      ? res.data.rekomendasi_sistem
      : Array.isArray(res.data?.data)
        ? res.data.data
        : []

    diagnosaDokter.value = Array.isArray(res.data?.diagnosa_dokter)
      ? res.data.diagnosa_dokter
      : []
  } catch (error) {
    console.error('LOAD REKOMENDASI PLANNING ERROR:', error.response?.data || error)
    rekomendasiPlanning.value = []
    diagnosaDokter.value = []
  } finally {
    loadingRekomendasi.value = false
  }
}

const bisaPakaiTindakan = (item) => {
  const text = String(item).toLowerCase()

  const keywords = [
    'rujuk',
    'oksigen',
    'infus',
    'cairan',
    'antibiotik suntik',
    'diazepam',
    'hangat',
    'tindakan',
  ]

  return keywords.some((k) => text.includes(k))
}

const bisaCariObat = (item) => itemAdalahObat(item)

const pakaiRekomendasiTindakan = async (item) => {
  activeTab.value = 'tindakan'

  searchTindakan.value = item
  showModalTindakan.value = true

  await nextTick()
  await loadTindakanModal()

  if (inputSearchTindakanRef.value) {
    inputSearchTindakanRef.value.focus()
  }
}

const ambilKeywordObat = (item) => {
  const text = String(item || '').toLowerCase()

  const mappings = [
    [['amoksisilin', 'amoxicillin'], 'amoksisilin'],
    [['ampisilin'], 'ampisilin'],
    [['gentamisin'], 'gentamisin'],
    [['oralit'], 'oralit'],
    [['zinc'], 'zinc'],
    [['parasetamol', 'paracetamol'], 'parasetamol'],
    [['artesunat'], 'artesunat'],
    [['dihidroartemisinin', 'piperakuin', 'dhp'], 'dihidroartemisinin piperakuin'],
    [['primakuin'], 'primakuin'],
    [['antimalaria'], 'antimalaria'],
    [['vitamin a'], 'vitamin a'],
    [['zat besi', 'fe'], 'zat besi'],
    [['kotrimoksazol'], 'kotrimoksazol'],
    [['diazepam'], 'diazepam'],
    [['salbutamol'], 'salbutamol'],
    [['kloramfenikol'], 'kloramfenikol'],
    [['tetrasiklin'], 'tetrasiklin'],
    [['kuinolon'], 'kuinolon'],
    [['nistatin'], 'nistatin'],
    [['albendazol'], 'albendazol'],
    [['pirantel'], 'pirantel'],
    [['resomal'], 'resomal'],
    [['antibiotik'], 'antibiotik'],
    [['obat cacing'], 'obat cacing'],
  ]

  const found = mappings.find(([keywords]) => {
    return keywords.some((keyword) => text.includes(keyword))
  })

  return found ? found[1] : item
}

const pakaiRekomendasiObat = async (item) => {
  activeTab.value = 'pengobatan'
  searchObat.value = ambilKeywordObat(item)
  await loadObat()
}

const normalizeTindakan = (row) => {
  return {
    id: row.id ?? row.ID ?? '',
    kode: row.kode ?? row.KODE ?? row.KODE_TINDAKAN ?? row.kode_tindakan ?? '',
    nama: row.nama ?? row.NAMA ?? row.NAMA_TINDAKAN ?? row.nama_tindakan ?? '',
    nama_ind:
      row.nama_ind ??
      row.namaIndonesia ??
      row.NAMA_IND ??
      row.nama_tindakan_indonesia ??
      row.nama_tindakan_ind ??
      '',
    keterangan: row.keterangan ?? row.deskripsi ?? row.KETERANGAN ?? row.DESKRIPSI ?? '',
    peraturan: row.peraturan ?? row.nilai_normal ?? row.PERATURAN ?? row.NILAI_NORMAL ?? '',
    harga: row.harga ?? row.HARGA ?? '',
    bayar: row.bayar ?? row.simTarif ?? row.SIMTARIF ?? row.simtarif ?? '',
    poli: row.poli ?? row.POLI ?? 'Umum',
    ket_gigi: row.ket_gigi ?? row.KET_GIGI ?? '',
    created_by: row.created_by ?? row.CREATED_BY ?? '',
  }
}

const bukaModalTindakan = async () => {
  activeTab.value = 'tindakan'
  searchTindakan.value = tindakan.kode || tindakan.nama || ''
  showModalTindakan.value = true

  await nextTick()

  if (inputSearchTindakanRef.value) {
    inputSearchTindakanRef.value.focus()
  }

  await loadTindakanModal()
}

const tutupModalTindakan = () => {
  showModalTindakan.value = false
}

const resetSearchTindakan = async () => {
  searchTindakan.value = ''
  await loadTindakanModal()

  await nextTick()
  if (inputSearchTindakanRef.value) {
    inputSearchTindakanRef.value.focus()
  }
}

const handleSearchTindakan = () => {
  clearTimeout(tindakanSearchTimer)

  tindakanSearchTimer = setTimeout(() => {
    loadTindakanModal()
  }, 350)
}

const loadTindakanModal = async () => {
  try {
    loadingTindakan.value = true

    const res = await axios.get('/simpus/kia/mtbs/tindakan', {
      params: {
        q: searchTindakan.value || '',
      },
    })

    const rows = res.data?.data ?? res.data ?? []
    daftarTindakan.value = rows.map(normalizeTindakan).slice(0, 10)
  } catch (error) {
    console.error('LOAD TINDAKAN MODAL ERROR:', error.response?.data || error)
    daftarTindakan.value = []
    alert('Gagal mencari tindakan. Cek endpoint /simpus/kia/mtbs/tindakan')
  } finally {
    loadingTindakan.value = false
  }
}

const pilihTindakan = (selected) => {
  tindakan.id = selected.id
  tindakan.kode = selected.kode
  tindakan.nama = selected.nama
  tindakan.nama_ind = selected.nama_ind
  tindakan.keterangan = selected.keterangan
  tindakan.peraturan = selected.peraturan
  tindakan.harga = selected.harga
  tindakan.bayar = selected.bayar
  tindakan.poli = selected.poli || 'Umum'
  tindakan.ket_gigi = selected.ket_gigi
  tindakan.created_by = selected.created_by

  showModalTindakan.value = false
}

const tambahTindakan = () => {
  if (!tindakan.nama) {
    alert('Nama tindakan wajib diisi')
    return
  }

  form.tindakan.push({
    id: tindakan.id,
    kode: tindakan.kode,
    nama: tindakan.nama,
    nama_ind: tindakan.nama_ind,
    keterangan: tindakan.keterangan,
    peraturan: tindakan.peraturan,
    harga: tindakan.harga,
    bayar: tindakan.bayar,
    poli: tindakan.poli || 'Umum',
    ket_gigi: tindakan.ket_gigi,
    created_by: tindakan.created_by,
  })

  resetTindakan()
}

const resetTindakan = () => {
  tindakan.id = ''
  tindakan.kode = ''
  tindakan.nama = ''
  tindakan.nama_ind = ''
  tindakan.keterangan = ''
  tindakan.peraturan = ''
  tindakan.harga = ''
  tindakan.bayar = ''
  tindakan.poli = 'Umum'
  tindakan.ket_gigi = ''
  tindakan.created_by = ''

  daftarTindakan.value = []
  searchTindakan.value = ''
}

const hapusTindakan = (i) => {
  form.tindakan.splice(i, 1)
}

const bukaDropdownObat = () => {
  if (searchObat.value.length >= 2 && daftarObat.value.length > 0) {
    showDropdownObat.value = true
  }
}

const loadObat = async () => {
  obat.obat_id = ''
  obat.kode_obat = ''
  obat.nama = ''
  obat.satuan = ''
  obat.dosis = ''

  if (searchObat.value.length < 2) {
    daftarObat.value = []
    showDropdownObat.value = false
    return
  }

  try {
    const res = await axios.get('/simpus/kia/mtbs/obat', {
      params: { q: searchObat.value },
    })

    daftarObat.value = (res.data?.data ?? []).slice(0, 10)
    showDropdownObat.value = true
  } catch (error) {
    console.error('LOAD OBAT ERROR:', error.response?.data || error)
  }
}

const generateDosisDefault = (selected) => {
  const nama = String(selected.nama ?? '').toLowerCase()
  const satuan = String(selected.satuan ?? '').toUpperCase()

  const matchMgPerMl = nama.match(/(\d+(?:[.,]\d+)?)\s*mg\s*\/\s*(\d+(?:[.,]\d+)?)\s*m?l/i)
  const matchMg = nama.match(/(\d+(?:[.,]\d+)?)\s*mg/i)
  const matchGram = nama.match(/(\d+(?:[.,]\d+)?)\s*g(?:ram)?/i)
  const matchMcg = nama.match(/(\d+(?:[.,]\d+)?)\s*(mcg|µg)/i)
  const matchMl = nama.match(/(\d+(?:[.,]\d+)?)\s*m?l/i)
  const matchPersen = nama.match(/(\d+(?:[.,]\d+)?)\s*%/i)

  if (matchMgPerMl) return `${matchMgPerMl[1].replace(',', '.')} mg/${matchMgPerMl[2].replace(',', '.')} ml`
  if (matchMg) return `${matchMg[1].replace(',', '.')} mg`
  if (matchGram) return `${matchGram[1].replace(',', '.')} g`
  if (matchMcg) return `${matchMcg[1].replace(',', '.')} mcg`
  if (matchMl) return `${matchMl[1].replace(',', '.')} ml`
  if (matchPersen) return `${matchPersen[1].replace(',', '.')}%`
  if (satuan) return `1 ${satuan}`

  return ''
}

const pilihObat = (selected) => {
  obat.obat_id = selected.obat_id
  obat.kode_obat = selected.kode_obat ?? ''
  obat.nama = selected.nama ?? ''
  obat.satuan = selected.satuan ?? ''
  obat.dosis = generateDosisDefault(selected)

  searchObat.value = selected.nama ?? ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const tambahObat = () => {
  if (!obat.obat_id || !obat.nama) {
    alert('Pilih obat dulu dari daftar pencarian')
    return
  }

  form.pengobatan.push({
    obat_id: obat.obat_id,
    kode_obat: obat.kode_obat,
    nama: obat.nama,
    satuan: obat.satuan,
    dosis: obat.dosis,
    cara: obat.cara,
    lama: obat.lama ?? 0,
  })

  obat.obat_id = ''
  obat.kode_obat = ''
  obat.nama = ''
  obat.satuan = ''
  obat.dosis = ''
  obat.cara = ''
  obat.lama = null

  searchObat.value = ''
  daftarObat.value = []
  showDropdownObat.value = false
}

const hapusObat = (i) => {
  form.pengobatan.splice(i, 1)
}

const applyData = (data) => {
  if (!data) return

  form.tindakan = Array.isArray(data.tindakan)
    ? data.tindakan
    : Array.isArray(data.tindakanSegera)
      ? data.tindakanSegera.map((nama) => ({
          id: '',
          kode: '',
          nama,
          nama_ind: nama,
          keterangan: '',
          peraturan: '',
          harga: '',
          bayar: '',
          poli: 'Umum',
          ket_gigi: '',
          created_by: '',
        }))
      : []

  form.pengobatan = Array.isArray(data.pengobatan) ? data.pengobatan : []
  form.edukasi = Array.isArray(data.edukasi) ? data.edukasi : []
  form.catatanEdukasi = data.catatanEdukasi ?? ''
  form.kunjunganUlang = data.kunjunganUlang ?? null
}

const loadFromDb = async () => {
  if (!idPelayanan) return

  try {
    const res = await axios.get(`/simpus/kia/mtbs/planning/${idPelayanan}`)
    applyData(res.data?.data)
  } catch (error) {
    console.error('LOAD PLANNING ERROR:', error.response?.data || error)
  }
}

const refreshPlanning = async () => {
  await Promise.all([
    loadFromDb(),
    loadRekomendasiPlanning(),
    loadRingkasan(),
  ])
}

const simpanPlanning = async () => {
  try {
    loading.value = true

    const payload = {
      kunjungan_id: String(idPelayanan),
      tindakan: form.tindakan,
      tindakanSegera: form.tindakan.map((t) => t.nama),
      pengobatan: form.pengobatan,
      edukasi: form.edukasi,
      catatanEdukasi: form.catatanEdukasi,
      kunjunganUlang: form.kunjunganUlang,
    }

    await axios.post('/simpus/kia/mtbs/planning/store', payload)

    await refreshPlanning()

    alert('Planning MTBS berhasil disimpan')
  } catch (error) {
    console.error('SAVE PLANNING ERROR:', error.response?.data || error)

    if (error.response?.status === 422) {
      alert('Validasi gagal:\n' + JSON.stringify(error.response?.data?.errors, null, 2))
      return
    }

    alert('Gagal menyimpan Planning MTBS')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await refreshPlanning()
})

onActivated(async () => {
  await refreshPlanning()
})
</script>

<style scoped>
.planning-wrapper {
  padding: 16px;
}

.planning-layout {
  position: relative;
}

.planning-summary-sticky {
  position: sticky;
  top: 16px;
}

.simpus-tabs .nav-link {
  color: red;
  font-weight: 600;
  border-radius: 0;
  border-top: 0;
}

.simpus-tabs .nav-link.active {
  color: red;
  background: #fff;
  border-color: #dee2e6 #dee2e6 #fff;
}

.form-area {
  max-width: 980px;
  position: relative;
}

.table {
  font-size: 12px;
}

.table thead th {
  background: #fff;
  font-weight: 700;
}

.table tbody tr:nth-child(odd) {
  background: #fafafa;
}

.btn-sm {
  font-size: 12px;
}

.modal {
  background: rgba(0, 0, 0, 0.15);
}

.modal-backdrop {
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

/* DUA SUMBER ASSESSMENT */
.assessment-source-card {
  padding: 16px;
  border: 1px solid #dbe5df;
  border-radius: 12px;
  background: #ffffff;
}

.system-assessment-card {
  border-left: 4px solid #0d6efd;
  background: #f7fbff;
}

.doctor-assessment-card {
  border-left: 4px solid #198754;
  background: #f8fcfa;
}

.assessment-source-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 13px;
}

.recommendation-item {
  margin-bottom: 10px;
  padding: 11px 12px;
  border: 1px solid #dce6e0;
  border-radius: 9px;
  background: #ffffff;
}

.recommendation-item:last-child {
  margin-bottom: 0;
}

.recommendation-classification {
  margin-bottom: 12px;
  color: #198754;
  font-size: 14px;
  font-weight: 750;
}

.recommendation-group {
  padding: 12px;
  border: 1px solid #dfe7e2;
  border-radius: 9px;
  background: #ffffff;
}

.recommendation-action-group {
  border-top: 3px solid #0d6efd;
}

.recommendation-medicine-group {
  border-top: 3px solid #198754;
}

.recommendation-group-title {
  margin-bottom: 10px;
  font-size: 13px;
  font-weight: 750;
}

.recommendation-empty {
  color: #7a847f;
  font-size: 13px;
}

/* RINGKASAN SUBJEKTIF & OBJEKTIF */
.clinical-summary-large {
  overflow: hidden;
  border: 1px solid #cfe0d6;
  border-radius: 14px;
  background: #f7faf8;
  box-shadow: 0 3px 12px rgba(25, 135, 84, 0.06);
}

.clinical-summary-large-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 15px;
  border-bottom: 1px solid #dce8e1;
  background: #ffffff;
}

.clinical-summary-large-title {
  color: #176b42;
  font-size: 16px;
  font-weight: 750;
}

.clinical-summary-large-subtitle {
  margin-top: 3px;
  color: #78827d;
  font-size: 13px;
}

.summary-refresh-button {
  min-height: 34px;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}

.clinical-summary-large-body {
  padding: 12px;
}

.clinical-summary-large-loading {
  padding: 34px 20px;
  color: #75807a;
  font-size: 14px;
  text-align: center;
}

.clinical-summary-panel {
  padding: 14px;
  border: 1px solid #dce7e1;
  border-radius: 12px;
  background: #ffffff;
}

.clinical-summary-panel-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
  padding-bottom: 14px;
  border-bottom: 1px solid #edf2ef;
}

.clinical-summary-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 42px;
  width: 42px;
  height: 42px;
  border-radius: 11px;
  color: #ffffff;
  font-size: 16px;
  font-weight: 800;
}

.subjective-icon {
  background: #198754;
}

.objective-icon {
  background: #0d6efd;
}

.clinical-summary-panel-title {
  color: #263b30;
  font-size: 17px;
  font-weight: 750;
}

.clinical-summary-panel-subtitle {
  margin-top: 2px;
  color: #7a847f;
  font-size: 12px;
}

.clinical-summary-section {
  margin-bottom: 16px;
}

.clinical-summary-label {
  margin-bottom: 7px;
  color: #66716b;
  font-size: 12px;
  font-weight: 750;
  letter-spacing: 0.2px;
  text-transform: uppercase;
}

.clinical-summary-text {
  color: #303d36;
  font-size: 14px;
  line-height: 1.6;
}

.clinical-summary-main-text {
  padding: 11px 13px;
  border: 1px solid #e0e8e3;
  border-radius: 9px;
  background: #fafcfb;
  font-weight: 600;
}

.clinical-summary-chip-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.clinical-summary-chip {
  display: inline-flex;
  align-items: center;
  min-height: 34px;
  padding: 7px 11px;
  border: 1px solid #d9e3dd;
  border-radius: 18px;
  color: #3f4d45;
  background: #f7faf8;
  font-size: 13px;
  line-height: 1.35;
}

.warning-chip {
  color: #664d03;
  border-color: #ffecb5;
  background: #fff3cd;
}

.danger-chip {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.clinical-metric-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 17px;
}

.clinical-metric-card {
  min-height: 68px;
  padding: 10px;
  border: 1px solid #dce6e0;
  border-radius: 10px;
  background: #fafcfb;
}

.clinical-metric-card span {
  display: block;
  margin-bottom: 6px;
  color: #758079;
  font-size: 12px;
  font-weight: 650;
}

.clinical-metric-card strong {
  display: block;
  color: #263a30;
  font-size: 15px;
  font-weight: 750;
  overflow-wrap: anywhere;
}

.clinical-status-badge {
  display: inline-flex;
  align-items: center;
  min-height: 32px;
  padding: 6px 11px;
  border: 1px solid;
  border-radius: 18px;
  font-size: 12px;
  font-weight: 750;
  text-transform: uppercase;
}

.saga-success {
  color: #0f5132;
  border-color: #a3cfbb;
  background: #d1e7dd;
}

.saga-danger {
  color: #842029;
  border-color: #f1aeb5;
  background: #f8d7da;
}

.saga-secondary {
  color: #41464b;
  border-color: #d3d6d8;
  background: #e2e3e5;
}

.clinical-summary-empty {
  padding: 22px 15px;
  border: 1px dashed #d7e1db;
  border-radius: 9px;
  color: #758079;
  background: #fafcfb;
  font-size: 14px;
  text-align: center;
}

@media (max-width: 991.98px) {
  .planning-summary-sticky {
    position: static;
  }

  .clinical-metric-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 767.98px) {
  .clinical-summary-large-header {
    align-items: flex-start;
    flex-direction: column;
    padding: 16px;
  }

  .summary-refresh-button {
    width: 100%;
  }

  .clinical-summary-large-body {
    padding: 12px;
  }

  .clinical-summary-panel {
    padding: 15px;
  }

  .clinical-summary-panel-header {
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .clinical-status-badge {
    margin-left: 54px;
  }

  .clinical-metric-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

</style>