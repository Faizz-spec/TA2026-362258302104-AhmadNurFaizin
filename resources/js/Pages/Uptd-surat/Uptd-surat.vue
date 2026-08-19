<template>
  <AppLayout title="Template Components">
    <div class="container py-4">
      <div class="row mb-4">
        <!-- Kosong -->
      </div>

      <div
        class="lab-report p-4 mb-5 bg-white border rounded shadow"
        style="
          max-width: 820px;
          margin: auto;
          font-size: 15px;
          position: relative;
          min-height: 1150px;
          padding-bottom: 150px;
        "
      >
        <div class="d-flex justify-content-between align-items-center mb-2">
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/f/f5/Lambang_Kabupaten_Banyuwangi.png"
            alt="Banyuwangi"
            height="60"
            style="margin-right: 8px"
          />
          <div class="w-100 text-center">
            <div class="fw-bold text-uppercase" style="font-size: 17px">
              PEMERINTAH KABUPATEN BANYUWANGI
            </div>
            <div class="fw-bold" style="font-size: 16px">DINAS KESEHATAN</div>
            <div class="fw-bold" style="font-size: 15px">UPTD LABORATORIUM KESEHATAN DAERAH</div>
            <div style="font-size: 13px">
              Jl. Letkol Istiqlah No.40 Banyuwangi Telepon (0333) 429444
            </div>
            <div style="font-size: 13px">Email: labkesdabanyuwangi@gmail.com</div>
          </div>
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/d/de/Logo_of_the_Ministry_of_Health_of_the_Republic_of_Indonesia.png"
            alt="Kemenkes"
            height="60"
            style="margin-left: 8px"
          />
        </div>
        <hr class="my-2" />
        <div class="d-flex justify-content-between" style="font-size: 14px">
          <div>
            <div>Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 440/1244/429.112.46/2025</div>
            <div>Lampiran : -</div>
            <div>Perihal&nbsp;&nbsp;&nbsp;&nbsp;: <b>LAPORAN HASIL ANALISA LABORATORIUM</b></div>
          </div>
          <div class="text-end">
            <div>Banyuwangi, 17 Juli 2025</div>
            <div>Kepada</div>
            <div>Yth. Managemen Hotel Santika</div>
            <div>Jl. Letjen S Parman No. 15 Sobo</div>
            <div>di <b>BANYUWANGI</b></div>
          </div>
        </div>
        <div class="mt-3 mb-2" style="font-size: 14px">
          Disampaikan dengan hormat hasil pemeriksaan laboratorium kami, sebagai berikut :
        </div>
        <table class="table table-bordered mb-2" style="font-size: 13px">
          <tbody>
            <tr>
              <td>Sampel diterima Tanggal</td>
              <td>09 Juli 2025</td>
            </tr>
            <tr>
              <td>Nama Pengambil</td>
              <td>UPTD Labkesda</td>
            </tr>
            <tr>
              <td>Instansi Pengambil Sampel</td>
              <td>Doni Bagus Pambudi, AM.Kes<br />Galon Cleo</td>
            </tr>
            <tr>
              <td>Alamat Sampel</td>
              <td>Jl. Letjen S Parman No. 15 Sobo</td>
            </tr>
            <tr>
              <td>Tempat Pengambilan Sampel</td>
              <td>Air Minum / botol 500 ml</td>
            </tr>
            <tr>
              <td>Jenis dan Kemasan sampel</td>
              <td>Air Minum / botol 500 ml</td>
            </tr>
            <tr>
              <td>No Register Laboratorium</td>
              <td>46,4462.16.134</td>
            </tr>
          </tbody>
        </table>
        <div class="mb-2" style="font-size: 14px"></div>
        <table class="table table-bordered mb-2" style="font-size: 13px">
          <thead class="table-light">
            <tr>
              <th class="text-center align-middle">DIPERIKSA TERHADAP</th>
              <th class="text-center align-middle">BATAS MAKSIMUM YANG DIPERBOLEHKAN</th>
              <th class="text-center align-middle">HASIL</th>
              <th class="text-center align-middle">SATUAN</th>
              <th class="text-center align-middle">METODE PENGUJIAN</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>JUMLAH BAKTERI</td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Total Bakteri E. Coli</td>
              <td class="text-center">0</td>
              <td class="text-center">0</td>
              <td class="text-center">CFU/100 ml</td>
              <td class="text-center">SNI/APHA</td>
            </tr>
            <tr>
              <td>Total Bakteri Coliform</td>
              <td class="text-center">0</td>
              <td class="text-center">0</td>
              <td class="text-center">CFU/100 ml</td>
              <td class="text-center">SNI/APHA</td>
            </tr>
          </tbody>
        </table>
        <div class="mb-2" style="font-size: 14px">
          <b>REKOMENDASI</b>
          <ul class="mb-1">
            <li>
              Terimakasih atas peran aktifnya dalam melakukan pengawasan kualitas Air Minum Saudara.
            </li>
            <li>Hasil analisa sampel terkirim tidak melebihi nilai ambang batas atas.</li>
            <li>
              Tetap melakukan inspeksi sanitasi kesehatan secara berkala untuk menjaga produk
              kualitas air.
            </li>
            <li>
              Pemeriksaan dan Pengambilan sampel Air Minum dilakukan setiap 1 (satu) bulan sekali
              atau sesuai dengan peraturan yang berlaku.
            </li>
          </ul>
          <span style="font-size: 12px">
            Batas syarat berdasarkan Peraturan Menteri Kesehatan Republik Indonesia Nomor 2 Tahun
            2023.
          </span>
        </div>
        <div class="mb-2" style="font-size: 14px">
          Demikian hasil analisa laboratorium kami, untuk dapat dipergunakan seperlunya.
        </div>

        <!-- Kotak PERHATIAN -->
        <div
          style="
            position: absolute;
            left: 24px;
            bottom: 90px;
            width: 340px;
            border: 1px solid #333;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 5px;
            background: #fff;
          "
        >
          <b>PERHATIAN :</b><br />
          Hasil pengujian ini hanya berlaku pada contoh di atas<br />
          Lembar ini <b>TIDAK UNTUK DIPUBLIKASIKAN</b><br />
          Lembar ini <b>BUKAN SERTIFIKASI</b>
        </div>
        <!-- Kotak Tembusan -->
        <div
          style="
            position: absolute;
            left: 24px;
            bottom: 15px;
            width: 340px;
            font-size: 13px;
            background: transparent;
          "
        >
          <b>Tembusan Yth :</b>
          <ol style="margin: 0; padding-left: 20px">
            <li>
              Sub Koordinator Kesehatan Lingkungan Kesehatan Kerja &amp; Olahraga Dinas Kesehatan
              Kab.Banyuwangi
            </li>
            <li>Arsip</li>
          </ol>
        </div>

        <!-- Tanda tangan -->
        <div class="text-end" style="margin-top: 32px">
          <div>KEPALA UPTD LABKESDA<br />DINAS KESEHATAN KAB. BANYUWANGI</div>
          <div style="height: 56px"></div>
          <div><b>KUSRIONO, S.Si</b></div>
          <div>NIP. 19751222 199703 1 004</div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 shadow-sm"></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import { Link } from '@inertiajs/vue3';
</script>
