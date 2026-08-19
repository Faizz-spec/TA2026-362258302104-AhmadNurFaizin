<template>
  <div class="m-4">

    <!-- Header -->
    <div class="text-center mb-3">
      <h5>Data Riwayat Kesehatan (Medical Record) Pasien</h5>
    </div>

    <!-- Data Pasien -->
    <table class="table-no-border">
      <tbody>
        <tr>
          <td>Tgl Reg.</td>
          <td>: {{ props.riwayatPasien.created || '-' }}</td>
          <td>NIK</td>
          <td>: {{ props.riwayatPasien.NIK || '-' }}</td>
        </tr>
        <tr>
          <td>No MR/No BPJS</td>
          <td>: {{ props.riwayatPasien.NO_MR }} / {{ props.riwayatPasien.noKartu }}</td>
          <td>Alamat</td>
          <td>: {{ props.riwayatPasien.ALAMAT || '-' }}</td>
        </tr>
        <tr>
          <td>Nama</td>
          <td>: {{ props.riwayatPasien.NAMA_LGKP?.toUpperCase() || '-' }}</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Tgl Lahir</td>
          <td>: {{ props.riwayatPasien.TGL_LHR || '-' }}</td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>

    <!-- Tabel Riwayat -->
    <table class="table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Tgl Kunjung</th>
          <th>Lokasi</th>
          <th>Status Pasien</th>
          <th>Anamnesa</th>
          <th>Lab</th>
          <th>Diagnosa</th>
          <th>Tindakan</th>
          <th>Obat</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(data, index) in props.riwayatPasien.simpus_loket">
          <td>{{ index + 1 }}</td>
          <td>{{ data.tglKunjungan || '-' }}</td>
          <td>{{ data.unitprofile.nama_unit || '-' }}</td>
          <td>Poli : {{ data.simpus_poli.nmPoli || '-' }}</td>
          <td v-if="data.anamnesa?.length">
            <div v-for="(data, i) in data.anamnesa" :key="i">
              TB: {{ data.tinggiBadan || '-' }} cm<br>
              BB: {{ data.beratBadan || '-' }} kg<br>
              Lingkar Perut: {{ data.lingkarPerut || '-' }} cm<br>
              Sistol: {{ data.sistole || '-' }}<br>
              Diastole: {{ data.diastole || '-' }}<br>
              Heart Rate: {{ data.heartRate || '-' }}<br>
              Resp. Rate: {{ data.respRate || '-' }}<br>
              Keluhan: {{ data.keluhan || '-' }}<br>
              Tambahan: {{ data.keluhanTambahan || '-' }}<br><br>
            </div>
          </td>
          <td v-else></td>

          <td>{{ data.hasil_lab || '-' }}</td>
          <td>
            <template v-if="data.diagnosa && data.diagnosa.length">
              <div v-for="(diag, i) in data.diagnosa" :key="i" class="mb-2">
                <strong>Diag {{ i + 1 }}: [{{ diag.kdDiagnosa }}]</strong><br>
                {{ diag.nmDiagnosa }}<br>
                Ket: {{ diag.keterangan || '-' }}
              </div>
            </template>
            <template v-else>-</template>
          </td>
          <td style="vertical-align: text-top;" class="isi">
            <template v-if="data.tindakan && data.tindakan.length">
              <div v-for="(t, i) in data.tindakan" :key="i" class="mb-2">
                <strong>Tindakan {{ i + 1 }} :</strong>
                [{{ t.kdTindakan }}] {{ t.nmTindakan }} <br>
                <strong>Ket :</strong> {{ t.keterangan && t.keterangan.trim() !== '' ? t.keterangan : '-' }}<br>
                <strong>Ket Gigi :</strong> {{ t.ketGigi && t.ketGigi.trim() !== '' ? t.ketGigi : '-' }}<br>
              </div>
            </template>
            <template v-else>
              -
            </template>
          </td>
          <td style="vertical-align: top;">
            <template v-if="data.resep_obat && data.resep_obat.length">
              <div v-for="(r, i) in data.resep_obat" :key="i" class="mb-2">
                <!-- Baris Resep -->
                <div>
                  <strong>{{ r.nama_puyer || 'Bukan Puyer' }}</strong>
                  <template v-if="r.kategori == '1'">
                    — ({{ r.jumlah_puyer }})
                    <br />
                    <small>
                      Dosis: {{ r.dosis_pakai_puyer }} |
                      Setiap {{ r.tiapJam }} jam sekali |
                      Waktu: {{ r.waktu }} 
                    </small>
                  </template>
                </div>

                <!-- Detail Obat di dalam resep -->
                <div v-if="r.detail_resep_obat && r.detail_resep_obat.length" class="ms-3 mt-1">
                  <div v-for="(d, j) in r.detail_resep_obat" :key="j" class="text-bold" style="font-size: 13px;">
                  <span class="fw-bold"> • {{ d.master_obat.NAMA }} ({{ d.jumlah }})</span>
                    <br />
                    <small>
                      Dosis: {{ d.dosis_pakai }}
                      <template v-if="d.tiapJam">, Setiap {{ d.tiapJam }} jam sekali</template>
                      <template v-if="d.waktu">, Waktu: {{ d.waktu }}</template>
                      <template v-if="d.kondisi">, Kondisi: {{ d.kondisi }}</template>
                    </small>
                  </div>
                </div>

                <hr v-if="i < data.resep_obat.length - 1" />
              </div>
            </template>

            <template v-else>-</template>
          </td>

        </tr>

        <tr v-if="!riwayat.length">
          <td colspan="9" class="text-center">
            Tidak ada data riwayat kesehatan
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const { props } = usePage()

const riwayatPasien = props.riwayatPasien
console.log(riwayatPasien)
const pasien = {
  tgl_reg: '2025-10-09',
  nik: '1234567890',
  no_bpjs: '9876543210',
  alamat: 'Jl. Contoh No. 10, Bandung',
  nama: 'Eunike Rambu',
  tgl_lahir: '2000-05-20'
}

const riwayat = [
  {
    tgl_kunjungan: '2025-10-05',
    lokasi: 'Poli Umum',
    status_pasien: 'Rawat Jalan',
    tb: 160,
    bb: 55,
    lingkar_perut: 70,
    sistol: 120,
    diastole: 80,
    heart_rate: 72,
    respirate_rate: 18,
    keluhan: 'Sakit kepala',
    keluhan_tambahan: 'Pusing saat malam',
    hasil_lab: 'Normal',
    diagnosa: [
      { kode: 'R51', nama: 'Sakit kepala', keterangan: 'Ringan' }
    ],
    tindakan: [{ nama_tindakan: 'Pemeriksaan fisik' }],
    obat: [{ nama_obat: 'Paracetamol', jumlah: 10, satuan: 'tablet' }]
  }
]
</script>

<style scoped>
body {
  font-family: DejaVu Sans, sans-serif;
  font-size: 12px;
}

.table-bordered {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid #000;
  padding: 5px;
  vertical-align: top;
}

.table-no-border {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}

.table-no-border td {
  border: none;
  padding: 3px 5px;
}
</style>
