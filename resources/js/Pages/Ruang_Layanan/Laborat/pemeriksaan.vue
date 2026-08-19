<template>
  <AppLayouts>
    <!-- ===================== APP NORMAL (disembunyikan saat print) ===================== -->
    <div class="app-shell">
      <div class="card m-4 rounded-4 rounded-bottom-0 shadow-sm">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center p-3 rounded-4 rounded-bottom-0"
          style="background: linear-gradient(135deg, #3b82f6, #10b981);">
          <h1 class="m-0 fs-5 text-white">Laboratorium — Pemeriksaan Pasien</h1>
          <Link :href="route('ruang-layanan.laborat')"
            class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
          <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
          </Link>
        </div>

        <div class="card-body">
          <div class="row g-4">
            <!-- ============ KIRI: IDENTITAS + AKSI ============ -->
            <div class="col-lg-4">
              <div class="border rounded-4 h-100">
                <!-- Header -->
                <div class="p-3 border-bottom" style="background: linear-gradient(135deg,#e0f2fe,#dcfce7);">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fs-5 fw-semibold">Identitas Pasien</h6>

                    <div class="btn-group">
                      <div class="btn-group" role="group">
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketCatin')">Paket Catin</a>
                          </li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketAnck1')">Paket ANC K1</a>
                          </li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketAnck3')">Paket ANC K3</a>
                          </li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketPtm')">Paket PTM</a></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketDarahLengkap')">Paket
                              Darah Lengkap</a></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addPaket('paketWidal')">Paket Widal</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Identitas -->
                <div class="p-3" style="font-size: 1rem;"> <!-- 🔹 teks sedikit diperbesar -->
                  <div class="fw-semibold fs-5">{{ pasien.NAMA_LGKP || '-' }}</div>
                  <div class="text-muted">NO. MR: <span class="fw-medium">{{ pasien.NO_MR || '-' }}</span></div>
                  <hr />
                  <div><span class="text-muted">Tgl Kunjungan:</span> <span class="fw-medium">{{ pasien.tglKunjungan ||
                      '-' }}</span></div>
                  <div><span class="text-muted">NIK:</span> <span class="fw-medium">{{ pasien.NIK || '-' }}</span></div>
                  <div><span class="text-muted">Jenis Kelamin:</span> <span class="fw-medium">{{ pasien.jenis_klmin ||
                      '-' }}</span></div>
                  <div class="mt-2">
                    <span class="text-muted">Alamat:</span>
                    <div class="fw-medium">{{ alamatLengkap }}</div>
                  </div>
                </div>

                <!-- Objective -->
                <div class="p-3 border-top">
                  <h6 class="mb-2 text-dark fw-bold fs-5">Hasil Pemeriksaan Objective</h6>


                  <!-- Fisik -->
                  <div class="mb-2 text-danger fw-bold fs-6">Fisik</div> <!-- 🔴 Merah & tebal -->
                  <ul class="list-unstyled ps-3 mb-3 fs-6"> <!-- Sedikit lebih besar dari normal -->
                    <li class="d-flex justify-content-between gap-3">
                      <span>Keadaan umum</span>
                      <span>{{ objective?.keadaanUmum || '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Kesadaran</span>
                      <span>{{ objective?.kesadaran || 'Compos mentis' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Tinggi Badan</span>
                      <span>{{ objective?.tinggiBadan ? objective.tinggiBadan + ' cm' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Berat badan</span>
                      <span>{{ objective?.beratBadan ? objective.beratBadan + ' kg' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Lingkar perut</span>
                      <span>{{ objective?.lingkarPerut ? objective.lingkarPerut + ' cm' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Tinggi lutut</span>
                      <span>{{ objective?.tinggiLutut ? objective.tinggiLutut + ' cm' : '-' }}</span>
                    </li>
                  </ul>



                  <!-- Tekanan darah -->
                  <div class="mb-2 text-danger fw-bold fs-6">Tekanan darah</div> <!-- 🔴 Merah & tebal -->
                  <ul class="list-unstyled ps-3 mb-0 fs-6"> <!-- Agak besar tapi tidak lebih dari header -->
                    <li class="d-flex justify-content-between gap-3">
                      <span>Sistole</span>
                      <span>{{ objective?.sistole ? objective.sistole + ' mmHg' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Diastole</span>
                      <span>{{ objective?.diastole ? objective.diastole + ' mmHg' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Resp. rate</span>
                      <span>{{ objective?.respRate ? objective.respRate + ' bpm' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Heart rate</span>
                      <span>{{ objective?.heartRate ? objective.heartRate + ' bpm' : '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between gap-3">
                      <span>Suhu</span>
                      <span>{{ objective?.suhu ? objective.suhu + ' °C' : '-' }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <!-- ============ KANAN: PERMOHONAN + TABEL ============ -->
            <div class="col-lg-8">
              <!-- Data Permohonan -->
              <div class="border rounded-4 mb-4">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center"
                  style="background: linear-gradient(135deg,#e0f2fe,#dcfce7);">
                  <h6 class="m-0">
                    Data Permohonan
                    <span v-if="permId" class="text-muted small">(#{{ permId }})</span>
                  </h6>
                  <span class="badge"
                    :class="order && order.status === 'Selesai' ? 'bg-success' : 'bg-warning text-dark'">
                    {{ (order && order.status) || 'Belum dilayani' }}
                  </span>
                </div>
                <div class="p-3 table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead>
                      <tr>
                        <th style="width:48px;">No.</th>
                        <th>Tgl. dibuat</th>
                        <th>Tenaga Medis Perujuk</th>
                        <th>Tenaga Medis Pemeriksa</th>
                        <th>Poli</th>
                        <th>Alasan</th>
                        <th>Hasil lab luar faskes</th>
                        <th style="width:110px;" class="text-end">
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="order">
                        <td>1</td>
                        <td>{{ order.tgl_dibuat || '-' }}</td>
                        <td>{{ order.tenaga_medis_perujuk || '-' }}</td>
                        <td>{{ order.tenaga_medis_pemeriksa || '-' }}</td>
                        <td>{{ order.poli || pasien.nmPoli || '-' }}</td>
                        <td>{{ order.alasan || '-' }}</td>
                        <td>{{ order.hasil_lab_luar_faskes ? 'ya' : 'tidak' }}</td>
                        <td class="text-end">
                        <td class="text-end">
                          <div class="btn-group btn-group-sm" role="group" style="white-space:nowrap;">
                            <button
                              class="btn btn-lg btn-print gradient-btn shadow-sm rounded-pill d-inline-flex align-items-center gap-2 px-4"
                              @click="openMasterModal">
                              <i class="bi bi-plus-square me-1"></i>Tambah
                            </button>
                            <button
                              class="btn btn-lg btn-print gradient-btn shadow-sm rounded-pill d-inline-flex align-items-center gap-2 px-4"
                              @click="printHasil" :disabled="!order" title="Cetak / Print lembar hasil"
                              aria-label="Cetak lembar hasil">
                              <i class="bi bi-printer fs-5"></i>
                              <span class="fw-semibold">Cetak</span>
                            </button>

                          </div>
                        </td>

                        </td>
                      </tr>
                      <tr v-else>
                        <td colspan="8" class="text-center text-muted">Tidak ada permohonan.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Jenis Pemeriksaan -->
              <div class="border rounded-4">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                  <h6 class="m-0">Jenis Pemeriksaan Laborat</h6>
                  <div class="d-flex align-items-center gap-2">
                    <!-- ICON PRINT -->


                    <button class="btn btn-sm btn-outline-secondary" @click="reloadPage">
                      <i class="bi bi-arrow-clockwise me-1"></i> Reload
                    </button>

                    <!-- NEW: Hapus semua -->
                    <button class="btn btn-danger btn-sm shadow-sm rounded-3 px-3 fw-semibold" @click="hapusSemua"
                      :disabled="!order || rows.length === 0" title="Hapus semua item pemeriksaan dalam permohonan ini">
                      <i class="bi bi-trash3 me-2"></i> Hapus Semua
                    </button>



                  </div>
                </div>

                <div class="p-3">
                  <div class="row g-3 mb-3">

                  </div>

                  <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                      <thead class="table-light text-center">
                        <tr>
                          <th style="width:56px;">No</th>
                          <th style="width:120px;">Kode</th>
                          <th>Nama pemeriksaan</th>

                          <th style="width:220px;">Nilai Normal/kritis</th>
                          <th style="width:240px;">Nilai Lab (satuan)</th>
                          <th style="width:100px;">Aksi</th>
                        </tr>
                      </thead>
                      <tbody v-if="groupedRows.length">
                        <!-- loop per kategori -->
                        <template v-for="(g, gi) in groupedRows" :key="'grp-main-'+gi">
                          <!-- baris judul kategori -->
                          <tr class="table-secondary">
                            <!-- 6 kolom: No, Kode, Nama, Nilai normal, Nilai Lab, Aksi -->
                            <td colspan="6" class="fw-bold text-uppercase">
                              {{ g.kategori }}
                            </td>
                          </tr>

                          <!-- baris item dalam kategori -->
                          <tr v-for="(row, i) in g.items" :key="row.detail_id">
                            <td class="text-center">{{ i + 1 }}</td>
                            <td class="text-center">{{ row.kode }}</td>
                            <td>{{ row.nama_pemeriksaan }}</td>

                            <td class="small">
                              <div class="text-muted white-pre-line">
                                {{ row.nilai_normal_kritis || '-' }}
                              </div>
                            </td>

                            <td>
                              <div class="input-group">
                                <input type="text" class="form-control" v-model="hasil[row.detail_id]"
                                  :placeholder="row.satuan ? `(${row.satuan})` : ''" />
                                <span class="input-group-text">{{ row.satuan || '-' }}</span>
                              </div>
                            </td>

                            <td class="text-center">
                              <button class="btn btn-outline-danger" @click="hapusRow(row)" :disabled="!order"
                                title="Hapus item ini">
                                <i class="bi bi-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </template>
                      </tbody>

                      <tbody v-else>
                        <tr>
                          <!-- 6 kolom juga -->
                          <td colspan="6" class="text-center text-muted py-4">
                            Belum ada item pemeriksaan.
                          </td>
                        </tr>
                      </tbody>

                    </table>

                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Sample diambil jam</label>
                    <input type="datetime-local" class="form-control" v-model="formWaktu.sample_diambil_at" />
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Sample selesai jam</label>
                    <input type="datetime-local" class="form-control" v-model="formWaktu.sample_selesai_at" />
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Tenaga Medis Pemeriksa</label>
                    <select class="form-select" v-model="formWaktu.tenaga_medis_pemeriksa">
                      <option value="" disabled>Pilih...</option>
                      <option v-for="tm in tenagaMedis" :key="tm.id" :value="tm.nama">
                        {{ tm.nama }} <span v-if="tm.profesi">— {{ tm.profesi }}</span>
                      </option>
                    </select>
                  </div>
                  <div class="col-12">
                    <button class="btn btn-primary gradient-btn" @click="submitWaktu" :disabled="!order">
                      Simpan Waktu & Pemeriksa
                    </button>
                  </div>
                  <br />
                  <div class="text-end">
                    <button class="btn btn-sm btn-success" @click="submitNilai" :disabled="!order">
                      <i class="bi bi-check2-square me-1"></i> UPDATE NILAI LAB
                    </button>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- ===== MODAL: TAMBAH PERMOHONAN ===== -->
      <div v-if="showPermohonan" class="modal-mask" @click.self="showPermohonan = false">
        <div class="modal-card">
          <div class="modal-header">
            <h6 class="m-0">Tambah Permohonan Laborat</h6>
            <button class="btn-close" @click="showPermohonan = false"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-warning py-2" v-if="!permForm.idPasien">
              <i class="bi bi-info-circle me-1"></i> idPasien belum terdeteksi otomatis. Silakan isi manual.
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small">ID Loket</label>
                <input class="form-control" :value="permForm.idLoket" readonly />
              </div>
              <div class="col-md-6">
                <label class="form-label small">ID Pasien</label>
                <input class="form-control" v-model="permForm.idPasien" placeholder="cth: 1 / 2 / 3" />
              </div>
              <div class="col-md-6">
                <label class="form-label small">Tanggal dibuat</label>
                <input type="datetime-local" class="form-control" v-model="permForm.tglDibuat" />
              </div>
              <div class="col-md-6">
                <label class="form-label small">Hasil lab luar faskes?</label>
                <select class="form-select" v-model="permForm.hasilLabLuarFaskes">
                  <option value="">Tidak</option>
                  <option value="1">Ya</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Kode Poli (opsional)</label>
                <input class="form-control" v-model="permForm.kdPoli" placeholder="cth: 004" />
              </div>
              <div class="col-md-6">
                <label class="form-label small">Tenaga Medis Pengirim (kode)</label>
                <input class="form-control" v-model="permForm.tenagaMedisPengirim" placeholder="cth: DR001" />
              </div>
              <div class="col-12">
                <label class="form-label small">Alasan Dirujuk</label>
                <textarea class="form-control" rows="2" v-model="permForm.alasanDirujuk"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" @click="showPermohonan = false">Batal</button>
            <button class="btn btn-primary" @click="submitPermohonan">Simpan</button>
          </div>
        </div>
      </div>

      <div v-if="showMaster" class="modal-mask" @click.self="showMaster = false">
        <div class="modal-card modal-xl">
          <div class="modal-header">
            <h6 class="m-0">Daftar Jenis Pemeriksaan</h6>
            <button class="btn-close" @click="showMaster = false"></button>
          </div>


          <div class="col-lg-6 px-4 mb-3">
            <label class="form-label fw-semibold text-secondary mb-1">
              <i class="bi bi-funnel me-1 text-primary"></i>
              Cari Jenis Pemeriksaan
            </label>

            <div class="input-group shadow-sm rounded-3">
              <span class="input-group-text bg-primary text-white border-0 px-3">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control border-0 ps-1" v-model="master.search"
                placeholder="Ketik nama atau kode pemeriksaan..." @keyup.enter="doSearchAll">
              <button class="btn btn-primary px-3 fw-semibold" @click="doSearchAll"
                style="border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                Cari / Tampilkan Semua
              </button>
            </div>
          </div>

          <!-- ============== FILTER: Kategori ============== -->
          <!-- <div class="px-4 mb-3">
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="text-secondary small fw-semibold me-1">
      <i class="bi bi-collection me-1"></i> Kategori:
    </span>

    <button
      class="btn btn-sm"
      :class="selectedKategori === null ? 'btn-primary' : 'btn-outline-primary'"
      @click="selectedKategori = null; loadParams(1);"
    >
      Semua
    </button>

    <button
      v-for="kat in kategoriList"
      :key="'kat-'+kat.id_kategori"
      class="btn btn-sm"
      :class="selectedKategori === kat.id_kategori ? 'btn-primary' : 'btn-outline-primary'"
      @click="selectedKategori = kat.id_kategori; loadParams(1);"
      :title="`Jumlah parameter: ${kat.jumlah}`"
    >
      {{ kat.nama_kategori }}
      <span class="badge text-bg-light ms-1">{{ kat.jumlah }}</span>
    </button>
  </div>
</div> -->



          <div class="modal-body">

            <div class="row g-3 align-items-end mb-3 px-3">
              <div class="col-lg-6">
                <!-- <label class="form-label small text-danger">Paket dari parameter_uji (Header ➝ Subheader)</label> -->
                <!-- HEADER (paket) -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <button v-for="ph in paketHeaders" :key="'h-' + ph.header" class="btn btn-outline-success btn-sm"
                    :class="{ 'active': selectedHeader && selectedHeader.header === ph.header }" @click="pickHeader(ph)"
                    :disabled="!order">
                    {{ ph.header_name || ('Header ' + ph.header) }}
                    <span class="badge text-bg-light ms-1">{{ ph.jumlah }}</span>
                  </button>
                </div>

                <!-- SUBHEADER (opsional) -->
                <div v-if="selectedHeader" class="d-flex flex-wrap gap-2">
                  <!-- <button class="btn btn-outline-primary btn-sm" @click="addPaketHeader(selectedHeader)"
                    :disabled="!order" title="Tambahkan semua item di header ini">
                    + Tambah SEMUA ({{ selectedHeader.header_name || ('Header ' + selectedHeader.header) }})
                  </button> -->



                  <button v-for="sh in paketSubs" v-if="false" :key="'s-' + sh.sub_header"
                    class="btn btn-outline-secondary btn-sm" @click="addPaketSub(selectedHeader, sh)"
                    :disabled="!order">
                    Sub {{ sh.sub_header }} <span class="badge text-bg-light ms-1">{{ sh.jumlah }}</span>
                  </button>

                </div>
              </div>
            </div>

            <div class="table-responsive border rounded">
              <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                  <tr>
                    <th style="width:120px;">Kode</th>
                    <th>Jenis Pemeriksaan</th>
                    <th style="width:120px;">Satuan</th>
                    <th style="width:260px;">Nilai normal / kritis</th>
                    <th style="width:180px;">Hasil Lab</th>
                    <th style="width:60px;" class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody v-if="browse.loading">
                  <tr>
                    <td colspan="6" class="text-center py-4">Memuat...</td>
                  </tr>
                </tbody>

                <tbody v-else-if="!groupedItems.length">
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Data kosong.</td>
                  </tr>
                </tbody>

                <!-- Kelompok per kategori -->
                <tbody v-else v-for="(g, gi) in groupedItems" :key="'grp-' + gi">
                  <!-- Judul kategori -->
                  <tr class="table-secondary">
                    <td colspan="6" class="fw-bold text-uppercase">
                      {{ g.kategori }}
                    </td>
                  </tr>

                  <!-- Baris item dalam kategori -->
                  <tr v-for="(it, idx) in g.items" :key="'m-' + it.id">
                    <td class="text-muted" style="width:120px;">{{ it.kode || '-' }}</td>
                    <td class="fw-semibold">{{ it.nama || '-' }}</td>
                    <td style="width:120px;">{{ it.satuan || '-' }}</td>
                    <td class="small" style="width:260px;">
                      <div class="text-muted white-pre-line">{{ it.nilaiNormalKritis || '-' }}</div>
                    </td>
                    <td style="width:180px;">
                      <input class="form-control form-control-sm" v-model="valueMap[it.id]"
                        placeholder="isian opsional" />
                    </td>
                    <td class="text-center" style="width:60px;">
                      <input type="checkbox" class="form-check-input" v-model="selectedMap[it.id]" />
                    </td>
                  </tr>
                </tbody>

              </table>
            </div>

            <!-- pagination simple -->
            <div class="d-flex justify-content-between align-items-center mt-2">
              <div class="small text-muted">
                Total: {{ master.meta.total || 0 }}
              </div>
              <div class="btn-group">
                <button v-for="(ln, i) in master.links" :key="i" class="btn btn-sm"
                  :class="['btn-outline-secondary', ln.active ? 'active' : '']" :disabled="!ln.url"
                  @click="gotoLink(ln.url)">
                  {{ ln.label }}
                </button>
              </div>
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between align-items-center">

            <!-- Bagian kiri -->
            <div class="d-flex gap-2">
              <button class="btn btn-light" @click="showMaster = false">Close</button>
            </div>

            <!-- Bagian kanan -->
            <div class="d-flex gap-2 align-items-center">

              <!-- Tambah Semua Header (hanya muncul jika header dipilih) -->
              <button v-if="selectedHeader" class="btn btn-success gradient-btn btn-sm text-white fw-semibold"
                @click="addPaketHeader(selectedHeader)" :disabled="!order" title="Tambahkan semua item di header ini">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah SEMUA
                ({{ selectedHeader.header_name || ('Header ' + selectedHeader.header) }})
              </button>


              <!-- Tombol Simpan -->
              <button class="btn btn-success" :disabled="!order" @click="submitSelected">
                <i class="bi bi-check2-square me-1"></i> SIMPAN
              </button>

            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- ===================== /APP NORMAL ===================== -->

    <!-- ===================== PRINT SHEET (hanya muncul saat print) ===================== -->
    <div class="print-sheet" v-if="order">
      <!-- Header -->
      <div class="ps-head">
        <img src="../../../../../public/images/logo.png" alt="logo" class="ps-logo" />
        <div class="ps-title">
          <div>PEMERINTAH KABUPATEN BANYUWANGI</div>
          <div>DINAS KESEHATAN</div>
          <div>PUSKESMAS WONGSOREJO</div>
          <div class="ps-sub">JL. Raya Situbondo No.04, Dsn. Kebunrejo RT 01 / RW 01</div>
        </div>
      </div>

      <h3 class="ps-report-title">LEMBAR HASIL LABORATORIUM</h3>

      <!-- Identitas -->
      <table class="ps-info">
        <tr>
          <td class="label">Nama</td>
          <td class="value">: {{ pasien.NAMA_LGKP || '-' }}</td>

          <td class="label">No reg lab</td>
          <td class="value">: {{ order.no_reg_lab || order.order_id || '-' }}</td>
        </tr>

        <tr>
          <td class="label">Tgl Lahir</td>
          <td class="value">: {{ pasien.TGL_LHR || '-' }}</td>

          <td class="label">Tgl periksa</td>
          <td class="value">: {{ order.tgl_dibuat || '-' }}</td>
        </tr>

        <tr>
          <td class="label">Jenis kelamin</td>
          <td class="value">: {{ jenisKelaminLabel }}</td>

          <td class="label">Spec diambil jam</td>
          <td class="value">: {{ order.sample_diambil_at || '-' }}</td>
        </tr>

        <tr>
          <td class="label">Alamat</td>
          <td class="value">: {{ alamatLengkap }}</td>

          <td class="label">Spec selesai jam</td>
          <td class="value">: {{ order.sample_selesai_at || '-' }}</td>
        </tr>

        <tr>
          <td class="label">No RM</td>
          <td class="value">: {{ pasien.NO_MR || '-' }}</td>

          <td class="label">Unit pengirim</td>
          <td class="value">: {{ order.unit_pengirim || order.poli || pasien.nmPoli || 'Umum' }}</td>
        </tr>

        <tr>
          <td class="label">Pemeriksa</td>
          <td class="value" colspan="3">: {{ order.tenaga_medis_pemeriksa || '-' }}</td>
        </tr>
      </table>


      <!-- Section contoh -->
      <!-- ========== CETAK PER KATEGORI ========== -->
      <div v-if="groupedRows.length">
        <!-- loop per kategori -->
        <div v-for="(g, gi) in groupedRows" :key="'psgrp-' + gi" class="ps-group">
          <!-- judul kategori dari database -->
          <div class="ps-section">
            {{ g.kategori }}
          </div>

          <table class="ps-table">
            <thead>
              <tr>
                <th class="w-1">No.</th>
                <th class="w-2">Kode</th>
                <th>Nama pemeriksaan</th>
                <th>Nilai Normal/kritis</th>
                <th>Nilai Lab (satuan)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in g.items" :key="r.detail_id">
                <td class="t-center">{{ i + 1 }}</td>
                <td class="t-center">{{ r.kode }}</td>
                <td>{{ r.nama_pemeriksaan }}</td>
                <td class="t-small">{{ r.nilai_normal_kritis || '-' }}</td>
                <td>
                  {{ (hasil && hasil[r.detail_id]) || r.nilai_lab || '' }}
                  <span class="muted">{{ r.satuan }}</span>
                </td>
              </tr>
              <tr v-if="g.items.length === 0">
                <td colspan="5" class="t-center">Belum ada data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- kalau sama sekali tidak ada pemeriksaan -->
      <div v-else>
        <div class="ps-section">PEMERIKSAAN LABORATORIUM</div>
        <div class="t-center" style="margin-top:8px;">Belum ada data pemeriksaan.</div>
      </div>


      <!-- Tanda tangan -->
      <div class="ps-sign">
        <div class="col">Dokter<br><br><br>
          <u>{{ order.tenaga_medis_perujuk || '___________________' }}</u>
        </div>
        <div class="col">Pemeriksa<br><br><br>
          <u>{{ order.tenaga_medis_pemeriksa || '___________________' }}</u>
        </div>
      </div>

      <div class="ps-footnote">
        <div class="small muted">* Hasil pemeriksaan mohon dibawa saat konsultasi. Lembar ini dicetak otomatis dari
          sistem.</div>
      </div>
    </div>
    <!-- ===================== /PRINT SHEET ===================== -->
  </AppLayouts>
</template>

<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch, isRef, onMounted } from 'vue';
import Swal from 'sweetalert2'

const page = usePage();
const pageProps = computed(() => (isRef(page?.props) ? (page.props.value || {}) : (page?.props || {})));

// Props dari Inertia
const pasien = computed(() => pageProps.value.DataPasien || {});
const order = computed(() => pageProps.value.DataPermohonan || null);
const rows = computed(() => pageProps.value.DataPemeriksaan || []);
const objective = computed(() => pageProps.value.DataObjective || null);
const tenagaMedis = computed(() => pageProps.value.TenagaMedis || []);
const Source = computed(() => page.props.Source ?? 'laborat')
const paketHeaders = ref([]);
const paketSubs = ref([]);
const selectedHeader = ref(null);
const selectedSub = ref(null);
const loketId = computed(() => DataPasien.value?.idLoket ?? '')

// === STATE TAMBAHAN UNTUK PAKET PARAMETER_UJI
const groupedRows = computed(() => {
  const list = rows.value || [];
  const groups = new Map();

  for (const r of list) {
    const key = r.kategori || r.nama_kategori || '';
    if (!groups.has(key)) groups.set(key, []);
    groups.get(key).push(r);
  }

  // ubah Map jadi array [{kategori, items: [...]}, ...]
  return Array.from(groups, ([kategori, items]) => ({ kategori, items }));
});

async function loadPaketHeaders() {
  try {
    const url = route('ruang-layanan.laborat.param.headers');
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    paketHeaders.value = await res.json(); // [{header, header_name, jumlah}]
  } catch (e) {
    console.error(e);
    paketHeaders.value = [];
  }
}



function addPaketHeader(headerObj) {
  if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
  const payload = {
    loketId: pasien.value?.idLoket || '',
    permohonanId: permId.value,
    pelayananId: order.value?.idPelayanan || '',
  };
  router.post(route('ruang-layanan.laborat.param.simpan', { header: headerObj.header }), payload, {
    preserveScroll: true,
    onError: (errs) => {
      showErrors(errs);
      toast('error', 'Gagal menambahkan semua item');
    },
    onSuccess: () => {
      const nama = headerObj.header_name || ('Header ' + headerObj.header);
      toast('success', 'Tambah semua berhasil', nama);
      console.log('headaer', headerObj.header)
      closeMaster();       // <—— tutup modal di sini
      reloadPage();
    }
  });
}
const kategoriList = ref([]);       // [{id_kategori, nama_kategori, jumlah}]
const selectedKategori = ref(null); // id_kategori atau null

async function loadKategori() {
  try {
    const res = await fetch(route('ruang-layanan.laborat.param.categories'), {
      headers: { 'Accept': 'application/json' }
    });
    kategoriList.value = await res.json();
  } catch (e) {
    console.error(e);
    kategoriList.value = [];
  }
}

function hapusSemua() {
  if (!order.value || !permId.value) return;

  const ok = confirm(`Hapus SEMUA item pemeriksaan pada permohonan #${permId.value}?`);
  if (!ok) return;

  const payload = { order_id: permId.value };

  router.post(route('ruang-layanan.laborat.hapusSemuaTindakan'), payload, {
    preserveScroll: true,
    onError: (errs) => showErrors(errs),
    onSuccess: () => {
      reloadPage();
      try {
        Swal?.fire({
          title: 'Selesai',
          text: 'Semua item pemeriksaan berhasil dihapus.',
          icon: 'success',
          timer: 1600,
          showConfirmButton: false
        });
      } catch { }
    }
  });
}



async function loadParamItems(header, sub = null) {
  try {
    const base = route('ruang-layanan.laborat.param.items', { header });
    const url = new URL(base, window.location.origin);
    if (sub !== null && sub !== undefined) url.searchParams.set('sub_header', String(sub));

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    const items = await res.json();

    // mapping ke bentuk master.items biar tabelnya tetap jalan
    master.items = (items || []).map(x => ({
      id: x.id_parameter,
      idPemeriksaan: x.id_parameter,
      kode: x.kode,
      nama: x.nama,
      nmPemeriksaanInd: x.nama, // optional
      satuan: x.satuan,
      nilaiNormalKritis: x.nilai_normal_kritis,
    }));
    master.meta = { total: master.items.length };
    master.links = [];
  } catch (e) {
    console.error(e);
    master.items = [];
    master.meta = { total: 0 };
    master.links = [];
  }
}




async function showSub(sub) {
  selectedSub.value = sub;
  await loadParamItems(selectedHeader.value.header, sub.sub_header);
}


function addPaketSub(headerObj, sub) {
  if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
  const form = new FormData();
  form.append('loketId', pasien.value?.idLoket || '');
  form.append('permohonanId', permId.value);
  if (order.value?.idPelayanan) form.append('pelayananId', order.value.idPelayanan);
  form.append('sub_header', sub.sub_header);

  fetch(route('ruang-layanan.laborat.param.simpan', { header: headerObj.header }), {
    method: 'POST',
    body: form,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then((r) => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      toast('success', 'Tambah sub berhasil', 'Sub ' + sub.sub_header);
      reloadPage();
    })
    .catch((e) => {
      console.error(e);
      toast('error', 'Gagal menambahkan sub');
    });
}


// Saat modal master dibuka, sekalian load header paket

// ===== Helpers umum =====
function toInt(x) {
  if (x === null || x === undefined) return null;
  const n = typeof x === 'string' ? parseInt(x.trim(), 10) : Number(x);
  return Number.isFinite(n) && Number.isInteger(n) ? n : null;
}
function toStr(x) {
  if (x === null || x === undefined) return null;
  const s = String(x).trim();
  return s.length ? s : null;
}

// Ambil ID permohonan sebagai **STRING** (PMH001, dsb.)
const permId = computed(() => {
  const o = order.value || {};
  const cands = [
    o?.order_id,
    o?.orderId,
    o?.idPermohonan,
    o?.id,
    pageProps.value?.DataPermohonan?.order_id,
    pageProps.value?.DataPermohonan?.idPermohonan,
  ];
  for (const c of cands) {
    const s = toStr(c);
    if (s) return s;
  }
  console.warn('[Laborat] Tidak menemukan permohonan ID.', o, cands);
  return null;
});

function showErrors(errs) {
  try {
    const list = Object.values(errs || {}).flat().map(String);
    if (list.length) alert(list.join('\n'));
  } catch {
    alert('Terjadi kesalahan. Coba lagi.');
  }
}

function hapusRow(row) {
  if (!order.value) return;
  if (!row || !row.detail_id) return;

  const ok = confirm(`Hapus item "${row.nama_pemeriksaan}"?`);
  if (!ok) return;

  const payload = {
    order_id: permId.value,
    detail_id: row.detail_id
  };

  router.post(route('ruang-layanan.laborat.hapusTindakan'), payload, {
    preserveScroll: true,
    onError: (errs) => showErrors(errs),
    onSuccess: () => {
      // refresh daftar & nilai lokal
      reloadPage();
      // popup sederhana
      try {
        Swal.fire({
          title: 'Berhasil!',
          text: 'Item berhasil dihapus.',
          icon: 'success',
          showConfirmButton: false,
          timer: 1800,
          timerProgressBar: true,
          position: 'center',
          background: '#f0fdf4',
          color: '#14532d',
        });
      } catch (e) {
        console.error(e);
      }


    }
  });
}


// Alamat rapi
const alamatLengkap = computed(() => {
  const p = pasien.value || {};
  const parts = [
    p.alamat, `RT ${p.no_rt || '-'}`, `RW ${p.no_rw || '-'}`,
    p.nama_kel, p.nama_kec, p.nama_kab, p.nama_prop
  ].filter(Boolean);
  return parts.join(', ');
});

// ===== Waktu sample & pemeriksa =====
const formWaktu = reactive({
  order_id: null,
  sample_diambil_at: '',
  sample_selesai_at: '',
  tenaga_medis_pemeriksa: '',
});
watch(order, (o) => {
  if (!o) return;
  formWaktu.order_id = toStr(o.order_id ?? o.orderId ?? o.idPermohonan ?? o.id ?? null);
  formWaktu.sample_diambil_at = toInputDateTime(o.sample_diambil_at || o.sampleDiambil || '');
  formWaktu.sample_selesai_at = toInputDateTime(o.sample_selesai_at || o.sampleSelesai || '');
  formWaktu.tenaga_medis_pemeriksa = o.tenaga_medis_pemeriksa || o.tenagaMedisPemeriksa || '';
}, { immediate: true });

const hasil = ref({});
watch(rows, (list) => {
  const map = {};
  (list || []).forEach(r => {
    map[r.detail_id] = (r.nilai_lab !== undefined && r.nilai_lab !== null) ? r.nilai_lab : '';
  });
  hasil.value = map;
}, { immediate: true });

function toInputDateTime(val) {
  if (!val) return '';
  return String(val).replace(' ', 'T').slice(0, 16);
}
function copyNormal(row) {
  if (!row || !row.nilai_normal_kritis) return;
  hasil.value[row.detail_id] = row.nilai_normal ?? row.nilai_normal_kritis ?? '';
}
function submitWaktu() {
  if (permId.value === null) {
    alert('Permohonan ID tidak valid.\nSilakan muat ulang halaman.');
    return;
  }
  const payload = { ...formWaktu, order_id: permId.value };
  router.post(route('ruang-layanan.laborat.setWaktuSample'), payload, {
    preserveScroll: true,
    onError: (errs) => showErrors(errs),
    onSuccess: () => {
      alert('✅ Waktu & Tenaga Medis Pemeriksa berhasil disimpan');
      reloadPage();
    }
  });
}

function submitNilai() {
  if (permId.value === null) {
    alert('Permohonan ID tidak valid.\nSilakan muat ulang halaman.');
    return;
  }
  const payload = {
    order_id: permId.value,
    hasil: Object.entries(hasil.value).map(([detail_id, nilai]) => ({
      detail_id: toInt(detail_id),
      nilai
    })).filter(x => x.detail_id !== null)
  };
  router.post(route('ruang-layanan.laborat.updateNilaiLab'), payload, {
    preserveScroll: true,
    onError: (errs) => showErrors(errs),
    onSuccess: () => {
      alert('✅ Nilai lab berhasil disimpan');
      reloadPage();
    }
  });
}

function reloadPage() {
  router.reload({
    only: ['DataPermohonan', 'DataPemeriksaan', 'DataObjective'],
    preserveState: true,
    preserveScroll: true,
  });
}
function printHasil() {
  // print hanya konten print-sheet
  requestAnimationFrame(() => window.print());
}

// ===== Permohonan =====
const showPermohonan = ref(false);
const permForm = reactive({
  idLoket: '',
  idPasien: '',
  idPelayanan: '',
  hasilLabLuarFaskes: '',
  kdPoli: '',
  tenagaMedisPengirim: '',
  alasanDirujuk: '',
  tglDibuat: defaultNow(),
});

// Prefill otomatis
watch(pasien, (p) => {
  permForm.idLoket = p?.idLoket || '';
}, { immediate: true });
watch(order, (o) => {
  permForm.idPasien = o?.pasien_id ?? '';
}, { immediate: true });

function defaultNow() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
function openPermohonanModal() {
  permForm.idLoket = pasien.value?.idLoket || '';
  permForm.idPasien = order.value?.pasien_id ? String(order.value.pasien_id) : '';
  permForm.tglDibuat = defaultNow();
  showPermohonan.value = true;
}
function submitPermohonan() {
  const payload = {
    idLoket: permForm.idLoket,
    idPasien: permForm.idPasien,
    idPelayanan: permForm.idPelayanan || '',
    hasilLabLuarFaskes: permForm.hasilLabLuarFaskes || '',
    kdPoli: permForm.kdPoli || '',
    tenagaMedisPengirim: permForm.tenagaMedisPengirim || '',
    alasanDirujuk: permForm.alasanDirujuk || '',
    tglDibuat: permForm.tglDibuat ? permForm.tglDibuat.replace('T', ' ') + ':00' : '',
  };
  if (!payload.idLoket || !payload.idPasien) {
    alert('ID Loket dan ID Pasien wajib diisi.');
    return;
  }
  router.post(route('ruang-layanan.laborat.simpanPermohonan'), payload, {
    preserveScroll: true,
    onError: (errs) => showErrors(errs),
    onSuccess: () => { showPermohonan.value = false; reloadPage(); }
  });
}

// ===== MASTER PEMERIKSAAN (modal) =====
const showMaster = ref(false);
const master = reactive({
  search: '',
  items: [],
  meta: { total: 0 },
  links: [],
  loading: false,
});
const selectedMap = reactive({});
const valueMap = reactive({});

// ==== STATE UNTUK BROWSE SEMUA ====
master.search = '';                 // sudah ada di state mu
const browse = reactive({
  filters: { header: null, sub_header: null }, // untuk paket filter
  loading: false,
});

// ambil data ke tabel (parameter_uji) — bisa search + paket filter
async function loadParams(page = 1) {
  browse.loading = true;
  try {
    const url = new URL(route('ruang-layanan.laborat.param.browse'), window.location.origin);
    if (master.search) url.searchParams.set('search', master.search);
    if (browse.filters.header != null) url.searchParams.set('header', String(browse.filters.header));
    if (browse.filters.sub_header != null) url.searchParams.set('sub_header', String(browse.filters.sub_header));

    // <<< TAMBAHAN: filter kategori
    if (selectedKategori.value != null) {
      url.searchParams.set('kategori', String(selectedKategori.value));
    }

    url.searchParams.set('page', String(page));
    url.searchParams.set('per_page', '25');

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    const data = await res.json();

    master.items = (data?.data || []).map((x) => ({
      id: x.id_parameter,
      kode: x.kode,
      nama: x.nama,
      satuan: x.satuan,
      nilaiNormalKritis: x.nilai_normal_kritis,
      // ⬇️ ambil kategori dari apa pun nama field-nya
      kategori: x.kategori_nama ?? x.nama_kategori ?? x.kategori ?? '',
    }));

    master.meta = data?.meta || {};
    master.links = data?.links || [];
  } catch (e) {
    console.error(e);
    master.items = []; master.meta = { total: 0 }; master.links = [];
  } finally {
    browse.loading = false;
  }
}
const groupedItems = computed(() => {
  const groups = new Map();
  for (const it of master.items) {
    const key = it.kategori || '';
    if (!groups.has(key)) groups.set(key, []);
    groups.get(key).push(it);
  }
  // urutkan kategori (opsional)
  return Array.from(groups, ([kategori, items]) => ({ kategori, items }));
});


// klik paging
function gotoLink(url) {
  if (!url) return;
  const u = new URL(url, window.location.origin);
  const p = u.searchParams.get('page') || '1';
  loadParams(Number(p));
}
// tombol: CARI / TAMPILKAN SEMUA
function doSearchAll() {
  browse.filters.header = null;
  browse.filters.sub_header = null;
  selectedKategori.value = null; // <<< reset kategori
  loadParams(1);
}


// ----- Paket (header/sub) -----
async function pickHeader(hdr) {
  selectedHeader.value = hdr;
  // tampilkan isi paket di tabel
  browse.filters.header = hdr.header;  // filter ke isi paket
  browse.filters.sub_header = null;
  await loadParams(1);

  // (tetap load daftar sub kalau suatu saat kamu butuh tombol sub)
  try {
    const url = route('ruang-layanan.laborat.param.subheaders', { header: hdr.header });
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    paketSubs.value = await res.json();
  } catch { paketSubs.value = []; }
}
function pickSub(sub) {
  browse.filters.header = null;
  browse.filters.sub_header = sub.sub_header;
  loadParams(1);
}
// === Toast helper ===
function toast(icon, title, text = '') {
  try {
    Swal.fire({
      icon, title, text,
      timer: 1600,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
      timerProgressBar: true,
    });
  } catch { }
}


// SIMPAN pilihan manual (checkbox)
function submitSelected() {
  if (permId.value === null) { alert('Permohonan ID tidak valid.'); return; }
  const pilih = Object.entries(selectedMap)
    .filter(([, v]) => !!v)
    .map(([k]) => toInt(k))
    .filter((n) => n !== null);

  if (pilih.length === 0) { alert('Pilih minimal satu pemeriksaan.'); return; }

  const payload = {
    loketId: pasien.value?.idLoket || '',
    permohonanId: permId.value,
    pelayananId: order.value?.idPelayanan || '',
    ids: pilih,
    nilaiLab: valueMap,
  };
  router.post(route('ruang-layanan.laborat.param.simpanTerpilih'), payload, {
    preserveScroll: true,
    onError: (errs) => {
      showErrors(errs);
      toast('error', 'Gagal menambahkan item terpilih');
    },
    onSuccess: () => {
      showMaster.value = false;
      toast('success', 'Tambah pemeriksaan berhasil', `${pilih.length} item ditambahkan`);
      reloadPage();
    }
  });
}
function closeMaster() {
  showMaster.value = false;
  // opsional: bersihkan pilihan biar fresh saat buka lagi
  Object.keys(selectedMap).forEach(k => delete selectedMap[k]);
  Object.keys(valueMap).forEach(k => delete valueMap[k]);
  selectedHeader.value = null;
  paketSubs.value = [];
}




// PASTIKAN hanya SATU versi ini
function openMasterModal() {
  if (!order.value) return;
  Object.keys(selectedMap).forEach(k => delete selectedMap[k]);
  Object.keys(valueMap).forEach(k => delete valueMap[k]);
  selectedHeader.value = null;
  paketSubs.value = [];
  selectedKategori.value = null;      // reset pilihan kategori

  showMaster.value = true;
  if (!paketHeaders.value.length) loadPaketHeaders();
  loadKategori();                      // <<< panggil load kategori
  doSearchAll();
}


async function loadMaster(page = 1) {
  master.loading = true;
  try {
    const url = new URL(route('ruang-layanan.laborat.paginasiMasterPemeriksaan'), window.location.origin);
    if (master.search) url.searchParams.set('search', master.search);
    url.searchParams.set('page', String(page));
    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    master.items = (data?.data || []).map((x) => ({
      id: x.id || x.idPemeriksaan || x.id_pemeriksaan || x.pemeriksaanId,
      idPemeriksaan: x.idPemeriksaan ?? x.id ?? x.id_pemeriksaan ?? x.pemeriksaanId,
      kode: x.kode,
      nama: x.nama || x.nmPemeriksaan,
      nmPemeriksaanInd: x.nmPemeriksaanInd,
      satuan: x.satuan,
      nilaiNormalKritis: x.nilai_normal_kritis || x.nilaiNormalKritis,
    }));
    master.meta = data?.meta || {};
    master.links = data?.links || [];
  } catch (e) {
    console.error(e);
    master.items = [];
    master.meta = { total: 0 };
    master.links = [];
  } finally {
    master.loading = false;
  }
}


const jenisKelaminLabel = computed(() => {
  const jk = pasien.value?.jenis_klmin;
  if (jk == '1' || jk?.toString().toLowerCase() === 'l') return 'Laki-laki';
  if (jk == '2' || jk?.toString().toLowerCase() === 'p') return 'Perempuan';
  return '-';
});

function addPaket(paketKey) {
  if (permId.value === null) {
    alert('Permohonan ID tidak valid.\nSilakan muat ulang halaman.');
    return;
  }
  const payload = {
    loketId: pasien.value?.idLoket || '',
    permohonanId: permId.value,
    pelayananId: order.value?.idPelayanan || '',
  };
  router.post(route('ruang-layanan.laborat.paketPemeriksaanSimpan', { paket: paketKey }), payload, {
    preserveScroll: true,
    onError: (errs) => {
      showErrors(errs);
      toast('error', 'Gagal menambahkan paket');
    },
    onSuccess: () => {
      toast('success', 'Tambah paket berhasil');
      reloadPage();
    }
  });
}


// debug helper (opsional)
onMounted(() => {
  window.__order = order;
  window.__permId = permId;
});
</script>

<!-- ========== GLOBAL PRINT CSS (JANGAN scoped) ========== -->
<style>
@page {
  size: A4;
  margin: 14mm 12mm;
}

/* MODE PRINT */
@media print {
  /* Sembunyikan semua layout aplikasi */
  .app-shell,
  .modal-mask,
  header,
  .navbar,
  .sidebar,
  .card-header,
  footer {
    display: none !important;
  }

  .ps-info {
    width: 100%;
    border-collapse: collapse;
    font-size: 11pt;
  }

  .ps-info td {
    padding: 3px 6px;
    vertical-align: top;
  }

  .ps-info .label {
    width: 22%;
    /* stabil */
    font-weight: bold;
  }

  .ps-info .value {
    width: 28%;
    /* stabil */
  }

  /* Tampilkan lembar print */
  .print-sheet {
    display: block !important;
    position: static !important;
    /* ⬅️ penting: biar bisa multi-page */
    width: 100%;
    margin: 0;
    padding: 0;
  }

  /* Biar teks dan border tetap jelas */
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Jaga elemen-elemen penting tidak kepotong di tengah halaman */
  .ps-section,
  .ps-info,
  .ps-sign {
    page-break-inside: avoid;
  }

  .ps-table {
    page-break-inside: auto;
  }

  .ps-table tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }
}

/* MODE LAYAR: print-sheet disembunyikan */
@media screen {
  .print-sheet {
    display: none;
  }
}



/* Tombol print versi besar & cantik */
.btn-print {
  border: 0 !important;
  transition: transform .15s ease, box-shadow .15s ease, filter .2s ease;
}

.btn-print:hover {
  transform: translateY(-1px);
  box-shadow: 0 .75rem 1.5rem rgba(16, 185, 129, .18);
  filter: saturate(1.05);
}

.btn-print:active {
  transform: translateY(0);
  box-shadow: 0 .25rem .75rem rgba(16, 185, 129, .22);
}

.btn-print:focus-visible {
  outline: none;
  box-shadow:
    0 0 0 .2rem rgba(59, 130, 246, .25),
    /* biru */
    0 0 0 .45rem rgba(16, 185, 129, .18);
  /* hijau */
}

/* Gradient-nya selaras dengan tema kamu */
.gradient-btn {
  background: linear-gradient(135deg, #3b82f6, #10b981) !important;
  color: #fff !important;
}

.btn-print[disabled] {
  opacity: .6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}


/* ====== Gaya print-sheet ====== */
.print-sheet {
  font-family: "Times New Roman", serif;
  color: #000;
  width: 190mm;
  margin: 0 auto;
  font-size: 12pt;
}

.ps-head {
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 2px solid #000;
  padding-bottom: 8px;
}

.ps-logo {
  width: 58px;
  height: 58px;
  object-fit: contain;
}

.ps-title {
  flex: 1;
  text-align: center;
  font-weight: bold;
  font-size: 14pt;
  line-height: 1.25;
}

.ps-sub {
  font-size: 10pt;
  font-weight: normal;
}

.ps-report-title {
  text-align: center;
  margin: 12px 0 8px;
  text-decoration: underline;
}

.ps-info {
  width: 100%;
  border-collapse: collapse;
  margin-top: 6px;
}

.ps-info td {
  padding: 2px 4px;
  vertical-align: top;
}

.ps-section {
  margin-top: 12px;
  font-weight: bold;
  background: #f3f4f6;
  border: 1px solid #000;
  border-bottom: 0;
  padding: 4px 6px;
}

.ps-table {
  width: 100%;
  border-collapse: collapse;
}

.ps-table th,
.ps-table td {
  border: 1px solid #000;
  padding: 6px;
}

.ps-table th {
  text-align: center;
  font-weight: bold;
}

.t-center {
  text-align: center;
}

.t-small {
  font-size: 10pt;
}

.muted {
  color: #555;
}

.ps-sign {
  display: flex;
  justify-content: space-between;
  margin-top: 30px;
}

.ps-sign .col {
  width: 48%;
  text-align: center;
}

.ps-footnote {
  margin-top: 14px;
  font-size: 10pt;
  color: #444;
}

.w-1 {
  width: 36px;
}

.w-2 {
  width: 90px;
}
</style>

<!-- ========== STYLE BIASA (scoped) ========== -->
<style scoped>
.gradient-btn {
  background: linear-gradient(135deg, #3b82f6, #10b981) !important;
  border: none !important;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.gradient-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 .5rem 1rem rgba(16, 185, 129, .15);
}

.table th,
.table td {
  vertical-align: middle;
}

/* Tombol merah besar dan cantik */
.btn-danger {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  /* merah gradien */
  border: none;
  color: #fff !important;
  transition: all 0.2s ease;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  transform: translateY(-1px);
  box-shadow: 0 0.5rem 1rem rgba(239, 68, 68, 0.25);
}

.btn-danger:active {
  transform: translateY(0);
  box-shadow: 0 0.3rem 0.6rem rgba(185, 28, 28, 0.3);
}

.btn-danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* --- Simple Modal --- */
.modal-mask {
  position: fixed;
  z-index: 1050;
  inset: 0;
  background: rgba(0, 0, 0, .45);
  display: grid;
  place-items: center;
  padding: 1rem;
}

.modal-card {
  width: 100%;
  max-width: 720px;
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 1rem 2rem rgba(0, 0, 0, .2);
  overflow: hidden;
}

.modal-card.modal-xl {
  max-width: 1100px;
}

/* yang daftar master */
.modal-header,
.modal-footer {
  padding: .75rem 1rem;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-body {
  padding: 1rem;
}

.btn-close {
  border: 0;
  background: transparent;
}

/* --- Simple Modal (update) --- */
.modal-mask {
  position: fixed;
  z-index: 1050;
  inset: 0;
  background: rgba(0, 0, 0, .45);
  display: grid;
  place-items: center;
  padding: 1rem;
  /* biar aman kalau konten tinggi */
  overflow: auto;
}

.modal-card {
  /* lebar responsif */
  width: min(96vw, 720px);
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 1rem 2rem rgba(0, 0, 0, .2);
  overflow: hidden;

  /* KUNCI: jadikan flex column & batasi tinggi */
  display: flex;
  flex-direction: column;
  max-height: 90vh;
  /* cegah full-screen */
}

.modal-card.modal-xl {
  width: min(96vw, 1100px);
  /* modal master lebih lebar */
  max-height: 90vh;
  /* tetap dibatasi tinggi */
}

/* header/footer tetap ukuran kontennya saja */
.modal-header,
.modal-footer {
  padding: .75rem 1rem;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex: 0 0 auto;
}

/* KUNCI: body yang scroll */
.modal-body {
  padding: 1rem;
  flex: 1 1 auto;
  min-height: 0;
  /* penting supaya flex child boleh overflow */
  overflow: auto;
  /* scroll di dalam modal */
}

/* opsional: sedikit tweak mobile */
@media (max-width: 576px) {

  .modal-card,
  .modal-card.modal-xl {
    border-radius: .75rem;
  }
}
</style>
