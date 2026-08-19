<template>
  <div class="container">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between align-items-center p-3"
      style="background: linear-gradient(135deg, #4682B4, #5A9BD5);">
      <h5 class="text-white fw-bold m-0">
        <i class="fas fa-stethoscope me-2"></i>
      </h5>
    </div>
    <div class="row">
      <!-- Diagnosa Medis -->
      <div class="col-6">
        <div class="card shadow-sm border-0 mb-4 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger text-decoration-underline mb-0">
              Diagnosa Medis
            </h6>
          </div>
          <div class="card-body">
            <!-- Input Diagnosa -->
            <div class="mb-3 row align-items-center">
              <div class="row g-2"> <!-- kolom pertama: KODE -->
                <div class="col-sm-3"> <input type="text" class="form-control bg-light" placeholder=""
                    v-model="selectedKode" disabled> </div> <!-- kolom kedua: NAMA + tombol -->
                <div class="col-sm-5">
                  <div class="input-group"> <input type="text" class="form-control bg-light" placeholder=""
                      v-model="selectedNama" disabled> <button type="button" class="btn btn-primary"
                      @click="overlay = true"> Cari </button> <button type="button" class="btn btn-outline-danger"
                      @click="hapusPilihan"> Del </button> </div>
                </div>
              </div> <!-- Overlay -->
              <div v-if="overlay" class="overlay">
                <div class="overlay-content">
                  <h4 class="mb-3">Pilih Data</h4> <!-- Search + Table -->
                  <div class="d-flex justify-content-between mb-2">
                    <div> Show <select v-model="showEntries" class="form-select d-inline w-auto mx-1">
                        <option v-for="n in [10, 25, 50]" :key="n" :value="n">{{ n }}</option>
                      </select> entries </div>
                    <div> Search: <input v-model="search" type="text" class="form-control d-inline w-auto ms-1"> </div>
                  </div>
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>KODE</th>
                        <th>NAMA</th>
                        <th>KETERANGAN</th>
                        <th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, i) in paginatedData" :key="i">
                        <td>{{ item.kode }}</td>
                        <td>{{ item.nama }}</td>
                        <td>{{ item.keterangan }}</td>
                        <td>
                          <button class="btn btn-sm btn-primary" @click="pilihItem(item)">Pilih</button>
                        </td>
                      </tr>
                      <tr v-if="filteredData.length === 0">
                        <td colspan="4" class="text-center">Tidak ada data</td>
                      </tr>
                    </tbody>
                  </table> <button class="btn btn-secondary mt-3" @click="overlay = false">Tutup</button>

                  <!-- Pagination -->
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === 1" @click="prevPage">
                      Previous
                    </button>
                    <span>Halaman {{ currentPage }} / {{ totalPages }}</span>
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === totalPages"
                      @click="nextPage">
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Alergi -->
            <div class="row mb-3">
              <div class="col-3 d-flex flex-column">
                <label class="fw-bold">Alergi Makan</label>
                <input class="form-control bg-primary bg-opacity-25" type="text">
              </div>
              <div class="col-3 d-flex flex-column">
                <label class="fw-bold">Alergi Obat</label>
                <input class="form-control bg-primary bg-opacity-25" type="text">
              </div>
              <div class="col-6 d-flex flex-column">
                <label class="fw-bold">Keterangan Alergi</label>
                <input class="form-control bg-primary bg-opacity-25" type="text">
              </div>
            </div>

            <!-- Kunjungan Khusus -->
            <div class="row mb-3">
              <div class="col-3 d-flex flex-column">
                <label class="fw-bold">Kunjungan Kasus</label>
                <select class="form-control">
                  <option value="">Pilih</option>
                  <option v-for="k in kasus" :key="k.id" :value="k.id">
                    {{ k.kasus }}
                  </option>
                </select>
              </div>
              <div class="col-9 d-flex flex-column">
                <label class="fw-bold">Keterangan</label>
                <input class="form-control" type="text">
              </div>
            </div>

            <!-- Simpan -->
            <Link href="/surat-keterangan" class="btn btn-success mt-2">
            <i class="far fa-save me-2"></i>Simpan Diagnosa Medis
            </Link>
          </div>
        </div>
      </div>

      <!-- Pemeriksaan Penunjang & Form Lanjutan -->
      <div class="col-6">
        <div class="card shadow-sm border-0 mb-4 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger text-decoration-underline mb-0">
              Pemeriksaan Penunjang
            </h6>
          </div>
          <div class="card-body">
            <Link href="/mal-sehat/promkes/laboratorium" class="btn btn-info">
            <i class="fas fa-vials me-2"></i> Laboratorium
            </Link>
          </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger text-decoration-underline mb-0">
              Form Lanjutan
            </h6>
          </div>
          <div class="card-body d-flex gap-2 flex-wrap">
            <Link href="/surat-keterangan" class="btn btn-secondary">
            Diare
            </Link>
            <Link href="/surat-keterangan" class="btn btn-secondary">
            Katarak
            </Link>
            <Link href="/surat-keterangan" class="btn btn-secondary">
            PTM
            </Link>
            <Link href="/surat-keterangan" class="btn btn-secondary">
            Hipertensi
            </Link>
          </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger text-decoration-underline mb-0">
              Diagnosa Keperawatan
            </h6>
          </div>
          <div class="card-body">
            <select name="nmDiagnosaKeperawatan" class="form-control mb-3 select2 input-sm" style="width:100%">
              <option value="">- Pilih -</option>
              <option value="ansietas">Ansietas</option>
              <option value="Berat badan lebih">Berat badan lebih</option>
              <option value="Berduka">Berduka</option>
              <option value="Bersihan jalan nafas tidak efektif">Bersihan jalan nafas tidak efektif</option>
              <option value="Defisit nutrisi">Defisit nutrisi</option>
              <option value="Defisit pengetahuan">Defisit pengetahuan</option>
              <option value="defisit perawatan diri">Defisit perawatan diri</option>
              <option value="diare">Diare</option>
              <option value="disrefleksia otonom">Disrefleksia otonom</option>
              <option value="Distress spiritual">Distress spiritual</option>
              <option value="Ganggguan pertukaran gas">Ganggguan pertukaran gas</option>
              <option value="gangguan citra tubuh">Gangguan citra tubuh</option>
              <option value="Gangguan eliminasi urin">Gangguan eliminasi urin</option>
              <option value="gangguan identitas diri">Gangguan identitas diri</option>
              <option value="gangguan integritas kulit/jaringan">Gangguan integritas kulit/jaringan</option>
              <option value="gangguan interaksi social">Gangguan interaksi sosial</option>
              <option value="gangguan komunikasi verbal">Gangguan komunikasi verbal</option>
              <option value="Gangguan memori">Gangguan memori</option>
              <option value="gangguan mobilitas fisik">Gangguan mobilitas fisik</option>
              <option value="gangguan persepsi sensori">Gangguan persepsi sensori</option>
              <option value="gangguan pola tidur">Gangguan pola tidur</option>
              <option value="Gangguan rasa nyaman">Gangguan rasa nyaman</option>
              <option value="Gangguan tumbuh kembang">Gangguan tumbuh kembang</option>
              <option value="harga diri rendah">Harga diri rendah</option>
              <option value="harga diri rendah kronis">Harga diri rendah kronis</option>
              <option value="hipertermia">Hipertermia</option>
              <option value="Hipotermia">Hipotermia</option>
              <option value="ikterus neonates">Ikterus neonates</option>
              <option value="inkontinensia urin berlanjut">Inkontinensia urin berlanjut</option>
              <option value="inkontinesia fekal">Inkontinesia fekal</option>
              <option value="Intoleransi aktifitas">Intoleransi aktifitas</option>
              <option value="isolasi social">Isolasi sosial</option>
              <option value="Keletihan">Keletihan</option>
              <option value="keletihan">Keletihan</option>
              <option value="keputusasaan">Keputusasaan</option>
              <option value="Kesiapan peningkatan keseimbangan cairan">Kesiapan peningkatan keseimbangan cairan</option>
              <option value="Kesiapan peningkatan nutrisi">Kesiapan peningkatan nutrisi</option>
              <option value="ketidaknyamanan pasca partum">Ketidaknyamanan pasca partum</option>
              <option value="Ketidakpatuhan">Ketidakpatuhan</option>
              <option value="Ketidakstabilan kadar glukosa darah">Ketidakstabilan kadar glukosa darah</option>
              <option value="Konstipasi">Konstipasi</option>
              <option value="Menyusui efektif">Menyusui efektif</option>
              <option value="menyusui tidak efektif">Menyusui tidak efektif</option>
              <option value="nausea">Nausea</option>
              <option value="nyeri akut">Nyeri akut</option>
              <option value="Nyeri kronis">Nyeri kronis</option>
              <option value="Nyeri melahirkan">Nyeri melahirkan</option>
              <option value="Obesitas">Obesitas</option>
              <option value="penyangkalan tidak efektif">Penyangkalan tidak efektif</option>
              <option value="Perfusi perifer tidak efektif">Perfusi perifer tidak efektif</option>
              <option value="Perilaku kekerasan">Perilaku kekerasan</option>
              <option value="Pola nafas tidak efektif">Pola nafas tidak efektif</option>
              <option value="Resiko aspirasi">Resiko aspirasi</option>
              <option value="Retensi urin">Retensi urin</option>
              <option value="risiko alergi">Risiko alergi</option>
              <option value="Risiko berat badan lebih">Risiko berat badan lebih</option>
              <option value="risiko bunuh diri">Risiko bunuh diri</option>
              <option value="risiko cedera">Risiko cedera</option>
              <option value="risiko defisit nutrisi">Risiko defisit nutrisi</option>
              <option value="risiko gangguan perkembangan">Risiko gangguan perkembangan</option>
              <option value="risiko gangguan pertumbuhan">Risiko gangguan pertumbuhan</option>
              <option value="risiko gangguan sirkulasi spontan">Risiko gangguan sirkulasi spontan</option>
              <option value="Risiko ikterik neonates">Risiko ikterik neonates</option>
              <option value="risiko infeksi">Risiko infeksi</option>
              <option value="risiko intoleransi aktifitas">Risiko intoleransi aktifitas</option>
              <option value="risiko jatuh">Risiko jatuh</option>
              <option value="Risiko kehamilan tidak dikehendaki">Risiko kehamilan tidak dikehendaki</option>
              <option value="Risiko ketidakseimbangan cairan">Risiko ketidakseimbangan cairan</option>
              <option value="risiko perdarahan">Risiko perdarahan</option>
              <option value="Risiko perfusi gastrointestinal tidak efektif">Risiko perfusi gastrointestinal tidak
                efektif</option>
              <option value="Risiko perfusi perifer tidak efektif">Risiko perfusi perifer tidak efektif</option>
              <option value="Risiko perfusi renal tidak efektif">Risiko perfusi renal tidak efektif</option>
              <option value="Risiko perfusi serebral tidak efektif">Risiko perfusi serebral tidak efektif</option>
              <option value="Risiko syok">Risiko syok</option>
              <option value="sindrom pasca trauma">Sindrom pasca trauma</option>
              <option value="Tidak terpenuhinya kebutuhan akan bebas dari ketakutan dan stress.">Tidak terpenuhinya
                kebutuhan akan bebas dari ketakutan dan stress</option>
              <option value="Tidak terpenuhinya kebutuhan akan kesan wajah yang sehat.">Tidak terpenuhinya kebutuhan
                akan kesan wajah yang sehat</option>
              <option value="Tidak terpenuhinya kebutuhan akan kondisi biologis gigi geligi yang baik.">Tidak
                terpenuhinya kebutuhan akan kondisi biologis gigi geligi yang baik</option>
              <option value="Tidak terpenuhinya kebutuhan akan perlindungan dari resiko kesehatan.">Tidak terpenuhinya
                kebutuhan akan perlindungan dari resiko kesehatan</option>
              <option value="Tidak terpenuhinya kebutuhan terbebas dari nyeri.">Tidak terpenuhinya kebutuhan terbebas
                dari nyeri</option>
              <option value="Tidak terpenuhinya keutuhan kulit dan membran mukosa pada kepala dan leher.">Tidak
                terpenuhinya keutuhan kulit dan membran mukosa pada kepala dan leher</option>
              <option value="Tidak terpenuhinya konseptualisasi dan pemecahan masalah.">Tidak terpenuhinya
                konseptualisasi dan pemecahan masalah</option>
              <option value="Tidak terpenuhinya tanggung jawab untuk kesehatan gigi dan mulut.">Tidak terpenuhinya
                tanggung jawab untuk kesehatan gigi dan mulut</option>
              <option value="waham">Waham</option>
            </select>
            <Link href="/surat-keterangan" class="btn btn-success">
            <i class="far fa-save me-2"></i>Simpan Diagnosa Keperawatan
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Diagnosa -->
    <div class="row mt-4">
      <div class="col-6">
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger mb-0">Tabel Diagnosa Medis</h6>
          </div>
          <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Nama Diagnosa Medis</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-header bg-light">
            <h6 class="fw-bold text-danger mb-0">Tabel Diagnosa Keperawatan</h6>
          </div>
          <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Nama Diagnosa Asuhan Keperawatan</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Link } from '@inertiajs/vue3'

export default {
  data() {
    return {
      overlay: false,
      search: "",
      showEntries: 10,      // jumlah per halaman
      currentPage: 1,       // halaman aktif
      dataDiagnosa: [],
      selectedKode: "",
      selectedNama: "",
    };
  },
  computed: {
    filteredData() {
      if (!this.search) return this.dataDiagnosa;
      return this.dataDiagnosa.filter(
        (item) =>
          item.nama.toLowerCase().includes(this.search.toLowerCase()) ||
          item.kode.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.showEntries;
      const end = start + this.showEntries;
      return this.filteredData.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.showEntries);
    },
  },
  methods: {
    async fetchDiagnosa() {
      try {
        const res = await axios.get("/mal-sehat/promkes/diagnosa/list", {
          params: { q: this.search },
        });
        this.dataDiagnosa = res.data;
      } catch (e) {
        console.error("Gagal ambil data diagnosa", e);
      }
    },
    pilihItem(item) {
      this.selectedKode = item.kode;
      this.selectedNama = item.nama;
      this.overlay = false; // otomatis tutup popup
    },
    hapusPilihan() {
      this.selectedKode = "";
      this.selectedNama = "";
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
  },
  watch: {
    overlay(val) {
      if (val) {
        this.fetchDiagnosa();
      }
    },
    search() {
      this.currentPage = 1; // reset ke page 1 setiap cari
      this.fetchDiagnosa();
    },
  },
};
</script>


<style scoped>
.overlay {
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
  padding: 1rem;
}

.overlay-content {
  background: #fff;
  padding: 1rem;
  border-radius: .5rem;
  width: 90%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
}
</style>