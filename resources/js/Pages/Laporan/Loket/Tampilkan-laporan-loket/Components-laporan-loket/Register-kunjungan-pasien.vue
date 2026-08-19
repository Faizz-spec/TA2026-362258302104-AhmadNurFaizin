<template>
  <div class="mt-4" style="font-family: 'Segoe UI', sans-serif;">
<div class="p-4 rounded-3 bg-light border shadow-sm mb-4">
  <h5 class="fw-bold text-success mb-3 text-center">LAPORAN REGISTER KUNJUNGAN PASIEN</h5>
  <div class="d-flex flex-column gap-1 small">
    <div><span class="fw-semibold">Unit:</span> SEMUA UNIT</div>
    <div><span class="fw-semibold">Nama Unit:</span> REKAP GABUNGAN</div>
    <div><span class="fw-semibold">Tanggal:</span> 14-07-2010 - 22-08-2025</div>
  </div>
</div>


    <div class="table-responsive rounded shadow-sm border border-light">
      <table class="table table-bordered table-sm align-middle text-center mb-0 table-hover">
<thead class="align-middle fw-semibold text-dark">
  <tr>
    <th rowspan="2" style="background-color:#cfe2ff !important;">NO</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">TGL KUNJ</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">NAMA</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">ALAMAT</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">NO BPJS</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">NO PASIEN</th>
    <th rowspan="2" style="background-color:#cfe2ff !important;">L/P</th>
    <th colspan="10" style="background-color:#cfe2ff !important;">GOLONGAN UMUR (TAHUN)</th>
    <th colspan="3" style="background-color:#cfe2ff !important;">TIPE PASIEN</th>
    <th colspan="7" style="background-color:#cfe2ff !important;">LAYANAN</th>
    <th colspan="2" style="background-color:#cfe2ff !important;">STATUS</th>
  </tr>
  <tr>
    <th style="background-color:#cfe2ff !important;">&lt;1</th>
    <th style="background-color:#cfe2ff !important;">1-4</th>
    <th style="background-color:#cfe2ff !important;">5-9</th>
    <th style="background-color:#cfe2ff !important;">10-14</th>
    <th style="background-color:#cfe2ff !important;">15-19</th>
    <th style="background-color:#cfe2ff !important;">20-44</th>
    <th style="background-color:#cfe2ff !important;">45-54</th>
    <th style="background-color:#cfe2ff !important;">55-59</th>
    <th style="background-color:#cfe2ff !important;">60-69</th>
    <th style="background-color:#cfe2ff !important;">&gt;70</th>
    <th style="background-color:#cfe2ff !important;">BPJS</th>
    <th style="background-color:#cfe2ff !important;">NON BPJS</th>
    <th style="background-color:#cfe2ff !important;">BAYAR</th>
    <th style="background-color:#cfe2ff !important;">BP</th>
    <th style="background-color:#cfe2ff !important;">GIGI</th>
    <th style="background-color:#cfe2ff !important;">KIA</th>
    <th style="background-color:#cfe2ff !important;">LAB</th>
    <th style="background-color:#cfe2ff !important;">UGD</th>
    <th style="background-color:#cfe2ff !important;">KB</th>
    <th style="background-color:#cfe2ff !important;">Kunj. Sehat</th>
    <th style="background-color:#cfe2ff !important;">BARU</th>
    <th style="background-color:#cfe2ff !important;">LAMA</th>
  </tr>
</thead>

        <tbody>
          <tr v-for="(item, index) in dataKunjungan" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ item.tanggal_kunjungan }}</td>
            <td>{{ item.nama }}</td>
            <td>{{ item.ALAMAT }}</td>
            <td>{{ item.no_bpjs }}</td>
            <td>{{ item.no_pasien }}</td>
            <td>{{ item.jenis_kelamin }}</td>

            <!-- Golongan umur -->
            <template v-for="range in umurRanges">
              <td>{{ item.golongan_umur === range ? 1 : 0 }}</td>
            </template>

            <!-- Tipe Pasien -->
            <td>{{ item.no_bpjs ? 1 : 0 }}</td>
            <td>{{ item.no_bpjs ? 0 : 1 }}</td>
            <td>0</td>

            <!-- Layanan dummy -->
            <td v-for="i in 7">0</td>

            <!-- Status -->
            <td>{{ item.status === 'BARU' ? 1 : 0 }}</td>
            <td>{{ item.status === 'LAMA' ? 1 : 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      <p class="mb-1">JUMLAH LAKI-LAKI (L): <strong>0</strong></p>
      <p class="mb-1">JUMLAH PEREMPUAN (P): <strong>0</strong></p>
      <p class="mb-0">TOTAL: <strong>{{ dataKunjungan.length }}</strong></p>
    </div>
<div class="mt-5 d-flex justify-content-end">
  <div style="line-height: 1.6; text-align: center;">
    <p class="mb-1">Banyuwangi, 04-08-2025</p>
    <p class="mb-3">Mengetahui</p>
    <p class="fw-bold mb-5">Kepala PUSKESMAS WONGSOREJO</p>

    <p class="fw-bold mb-0">NS.H.M.Shadiq, S.Kep.M.MKes</p>
    <p class="mb-0">NIP. 19641110 198502 1 002</p>
  </div>
</div>



  </div>
</template>


<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  dataKunjungan: Array
})

const umurRanges = ['<1', '1-4', '5-9', '10-14', '15-19', '20-44', '45-54', '55-59', '60-69', '>70']
</script>
