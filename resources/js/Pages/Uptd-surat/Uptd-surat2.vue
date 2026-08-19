<template>
  <AppLayout title="Template Components">
    <div class="container py-4">
      <div
        class="lab-report"
        style="
          position: relative;
          max-width: 820px;
          min-height: 1100px;
          margin: auto;
          background: #fff;
          font-size: 15px;
          box-shadow: 0 0 10px #eee;
          border-radius: 12px;
          padding: 36px 28px 80px 28px;
        "
      >
        <!-- Header Surat -->
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

        <!-- Info Surat -->
        <div class="d-flex justify-content-between" style="font-size: 14px">
          <div>
            <div>Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 440/655/429.112.46/2025</div>
            <div>Lampiran : -</div>
            <div>Perihal&nbsp;&nbsp;&nbsp;&nbsp;: <u>LAPORAN HASIL ANALISA LABORATORIUM</u></div>
          </div>
          <div class="text-end">
            <div>Banyuwangi, 24 Mei 2025</div>
            <div>Kepada</div>
            <div>Yth. Management CV. Permata Boganita Nusantara</div>
            <div>Perum Mendent Regency Block 1-5</div>
            <div>Kel. Mojopanggung Kec. Giri</div>
            <div>di <b>BANYUWANGI</b></div>
          </div>
        </div>

        <!-- Pembuka -->
        <div class="mt-3 mb-2" style="font-size: 14px">
          Disampaikan dengan hormat hasil pemeriksaan Laboratorium kami, sebagai berikut:
        </div>

        <!-- Detail Sampel -->
        <table class="table table-bordered mb-2" style="font-size: 13px">
          <tbody>
            <tr>
              <td>Sampel diterima Tanggal</td>
              <td>17 Mei 2025</td>
            </tr>
            <tr>
              <td>Nama Pengambil</td>
              <td>Vania Sina S</td>
            </tr>
            <tr>
              <td>Instansi Pengambil Sampel</td>
              <td>Dinas Kesehatan Banyuwangi</td>
            </tr>
            <tr>
              <td>Jenis Pengambilan Sampel</td>
              <td>Kualitas Penjamanan Makanan (Rectal Swab)</td>
            </tr>
            <tr>
              <td>Asal Sampel</td>
              <td>CV. Permata Boganita Nusantara</td>
            </tr>
            <tr>
              <td>No Register Laboratorium</td>
              <td>46.2474.13.655</td>
            </tr>
          </tbody>
        </table>

        <!-- Hasil Kultur -->
        <table class="table table-bordered mb-2" style="font-size: 13px">
          <thead class="table-light">
            <tr>
              <th class="text-center align-middle" rowspan="2">NO</th>
              <th class="text-center align-middle" rowspan="2">NO ID SAMPEL<br />NAMA/UMUR</th>
              <th class="text-center align-middle" colspan="5">HASIL KULTUR TERHADAP BAKTERI</th>
              <th class="text-center align-middle" rowspan="2">KET</th>
            </tr>
            <tr>
              <th class="text-center align-middle">Salmonella SP</th>
              <th class="text-center align-middle">Vibrio cholerae</th>
              <th class="text-center align-middle">Shigella dysenterii</th>
              <th class="text-center align-middle">E. Coli Patogen</th>
              <th class="text-center align-middle">...</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Suryani Kartika Dewi / P / 48th</td>
              <td>Neg</td>
              <td>Neg</td>
              <td>Neg</td>
              <td>Neg</td>
              <td>Neg</td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <!-- Rekomendasi -->
        <div class="mb-2" style="font-size: 14px">
          <b>REKOMENDASI</b>
          <ul class="mb-1" style="padding-left: 17px">
            <li>
              Terimakasih atas peran aktifnya dalam melakukan pengawasan kualitas penjamanan makanan
              di Restoran saudari.
            </li>
            <li>
              Hasil kultur dan identifikasi dari sampel rectal swab atas nama tersebut diatas
              dinyatakan
              <b
                >TIDAK MENGANDUNG bakteri Salmonella spp; Vibrio cholerae; Shigella dysenterii; E.
                coli patogen</b
              >
              sehingga perlu untuk dipertahankan dan dijaga hygiene sanitasi penjamanan makanan.
            </li>
            <li>
              Tetap dilakukan inspeksi sanitasi kesehatan lingkungan Restoran secara berkala dalam
              rangka mencegah terjadinya pencemaran / kontaminasi dan peningkatan kualitas kesehatan
              Restoran.
            </li>
            <li>
              Pemeriksaan dan Pengambilan sampel kualitas penolah dan penjamah makanan beserta
              produk hasil olahannya dilakukan setiap 6(enam) bulan sekali atau sesuai dengan
              peraturan yang berlaku.
            </li>
          </ul>
          <span style="font-size: 12px">
            Batas syarat berdasarkan Permenkes RI Nomor 27 Tahun 2017 dan Keputusan Menteri
            Kesehatan RI Nomor. 1204/Menkes/SK/X/2004, PERMENKES RI Nomor 7 Tahun 2019.
          </span>
        </div>
        <div class="mb-2" style="font-size: 14px">
          Demikian hasil analisa laboratorium kami, untuk dapat dipergunakan seperlunya.
        </div>

        <!-- Tanda tangan kanan bawah -->
        <div class="text-end" style="margin-top: 32px">
          <div>KEPALA UPTD LABKESDA<br />DINAS KESEHATAN KAB. BANYUWANGI</div>
          <div style="height: 56px"></div>
          <div><b>KUSRIONO, S.Si</b></div>
          <div>NIP. 19751222 199703 1 004</div>
        </div>

        <!-- ...di dalam <div class="lab-report" style="position:relative; min-height:1100px;"> -->

        <!-- Kotak PERHATIAN Kiri Bawah -->
        <div
          style="
            position: absolute;
            left: 24px;
            bottom: 100px;
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

        <!-- Tembusan Yth DI LUAR KOTAK, BAWAH KIRI -->
        <div
          style="
            position: absolute;
            left: 24px;
            bottom: 10px;
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
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
</script>
