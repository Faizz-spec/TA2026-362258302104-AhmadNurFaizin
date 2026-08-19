<template>
  <div class=" my-4 bg-white p-4 rounded shadow-sm border">
    <!-- Header Identitas Pasien -->
    <div class="border p-3 mb-3 small">
      <div class="row">
        <div class="col-md-6">
          <div><strong>No Rekam Medik</strong> : 0139183</div>
          <div><strong>Nama Pasien</strong> : {{ props.riwayatPasien.NAMA_LGKP?.toUpperCase() || '-' }}</div>
          <div><strong>Tanggal Lahir</strong> :{{ props.riwayatPasien.TGL_LHR || '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>NIK</strong> : {{ props.riwayatPasien.NIK || '-' }}</div>
          <div><strong>Alamat</strong> : {{ props.riwayatPasien.ALAMAT || '-' }}</div>
          <div><strong>Status</strong> : {{ props.riwayatPasien.noKartu ? 'BPJS' : 'NON BPJS' || '-' }}</div>
        </div>
      </div>
    </div>

    <!-- Judul -->
    <h6 class="text-center fw-bold mb-3 text-uppercase">
      CATATAN PERKEMBANGAN PASIEN TERINTEGRASI<br />
      <small class="fw-normal">(Ditulis dengan prinsip SOAP)</small>
    </h6>

    <!-- Tabel SOAP -->
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-top">
        <thead class="text-center align-middle" style="background-color: #d9f2f2;">
          <tr>
            <th style="width:120px;">Tanggal/Jam</th>
            <th style="width:200px;">Unit<br />Ruang layanan</th>
            <th style="width:300px;">ANAMNESA DAN PEMERIKSAAN<br /><small>(Subjective - Objective)</small></th>
            <th style="width:260px;">DIAGNOSA &amp; KODE ICD X<br /><small>(Assessment)</small></th>
            <th style="width:260px;">PERENCANAAN LAYANAN<br /><small>(Planning)</small></th>
            <th style="width:140px;">Nama &amp; Paraf Petugas</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(data, index) in props.riwayatPasien.simpus_loket" :key="index">
            <!-- Tanggal -->
            <td class="text-center">{{ data.tglKunjungan || '-' }}</td>

            <!-- Unit / Ruang layanan -->
            <td style="white-space: pre-wrap;">
              <strong>*Unit*</strong><br>
              {{ data.unitprofile?.nama_unit || '-' }}<br>
              <strong>*Ruang layanan*</strong><br>
              Poli: {{ data.simpus_poli?.nmPoli || '-' }}
            </td>

            <!-- Subjective - Objective -->
            <td style="white-space: pre-wrap;">
              <strong>*Subjective*</strong><br>
              <template v-if="data.anamnesa && data.anamnesa.length">
                <div v-for="(a, i) in data.anamnesa" :key="i">
                  Keluhan: {{ a.keluhan || '-' }}<br>
                  Tambahan: {{ a.keluhanTambahan || '-' }}<br><br>
                  <strong>*Objective*</strong><br>
                  TB: {{ a.tinggiBadan || '-' }} cm<br>
                  BB: {{ a.beratBadan || '-' }} kg<br>
                  Lingkar Perut: {{ a.lingkarPerut || '-' }} cm<br>
                  Sistol: {{ a.sistole || '-' }}<br>
                  Diastole: {{ a.diastole || '-' }}<br>
                  Heart Rate: {{ a.heartRate || '-' }}<br>
                  Resp Rate: {{ a.respRate || '-' }}<br>
                </div>
              </template>
              <template v-else>-</template>
            </td>

            <!-- Assessment (Diagnosa) -->
            <td style="white-space: pre-wrap;">
              <strong>*Assessment*</strong><br>
              <template v-if="data.diagnosa && data.diagnosa.length">
                <div v-for="(diag, i) in data.diagnosa" :key="i" class="mb-2">
                  Diag {{ i + 1 }}: [{{ diag.kdDiagnosa || '-' }}] {{ diag.nmDiagnosa || '-' }}<br>
                  Ket: {{ diag.keterangan || '-' }}
                </div>
              </template>
              <template v-else>-</template>
            </td>

            <!-- Planning (Tindakan dan Obat) -->
            <td style="white-space: pre-wrap;">
              <strong>Tindakan:</strong><br>
              <template v-if="data.tindakan && data.tindakan.length">
                <div v-for="(t, i) in data.tindakan" :key="i" class="mb-2">
                  <span class="fw-bold">Tindakan {{ i+ 1}} :</span>  [{{ t.idTindakan }}] {{ t.nmTindakan }}<br>
                 <span class="fw-bold">Ket : </span> {{ t.keterangan && t.keterangan.trim() !== '' ? t.keterangan : '-' }}<br>
                 <span class="fw-bold">Ket gigi:</span> {{ t.ketGigi && t.ketGigi.trim() !== '' ? t.ketGigi : '-' }}
                </div>
              </template>
              <template v-else>-</template>

              <br>
              <strong>Pengobatan :</strong><br>
              <template v-if="data.resep_obat && data.resep_obat.length">
                <div v-for="(r, i) in data.resep_obat" :key="i">
                  {{ r.nama_puyer || 'Bukan Puyer' }}
                  <template v-if="r.kategori == '1'">
                    ({{ r.jumlah_puyer }})
                    <br><small>Dosis: {{ r.dosis_pakai_puyer }} | Tiap {{ r.tiapJam }} jam | Waktu: {{ r.waktu
                    }}</small>
                  </template>

                  <div v-if="r.detail_resep_obat && r.detail_resep_obat.length" class="ms-3 mt-1">
                    <div v-for="(d, j) in r.detail_resep_obat" :key="j">
                      • {{ d.master_obat.NAMA }} ({{ d.jumlah }})
                      <small>
                        Dosis: {{ d.dosis_pakai }}
                        <template v-if="d.tiapJam">, Tiap {{ d.tiapJam }} jam</template>
                        <template v-if="d.waktu">, Waktu: {{ d.waktu }}</template>
                        <template v-if="d.kondisi">, Kondisi: {{ d.kondisi }}</template>
                      </small>
                    </div>
                  </div>
                  <hr v-if="i < data.resep_obat.length - 1">
                </div>
              </template>
              <template v-else>-</template>
            </td>

            <!-- Nama & Paraf Petugas -->
            <td style="white-space: pre-wrap;">{{ data.petugas?.nama || '—' }}</td>
          </tr>

        </tbody>
      </table>
    </div>

    <!-- Tombol Cetak -->
    <div class="d-flex justify-content-end mt-2 no-print">
      <button class="btn btn-outline-secondary btn-sm" @click="window.print()">Cetak</button>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';


const { props } = usePage();
const riwayatPasien = props.riwayatPasien;
console.log(riwayatPasien)
const items = [
  {
    tanggal: '19-05-2025',
    unit: '*Unit*\nPUSKESMAS WONGSOREJO\n*Ruang layanan*\nPoli 1 : Umum',
    subjective: 'keluhan :\nkeluhan tambahan :',
    objective: 'TB : cm   BB : kg\nLingkar Perut : cm\nSistole :\nDiastole :\nHeart rate :\nRespirate rate :',
    assessment: '*Laborat*',
    planning: '*Tindakan*\n*Pengobatan*',
    petugas: '',
  },
  {
    tanggal: '03-06-2025',
    unit: '*Unit*\nPUSKESMAS WONGSOREJO\n*Ruang layanan*\nPoli 1 : KIA',
    subjective: 'keluhan :\nkeluhan tambahan :',
    objective: 'TB : cm   BB : kg\nLingkar Perut : cm\nSistole :\nDiastole :\nHeart rate :\nRespirate rate :',
    assessment:
      'Diag 1 : [A01.2] Paratyphoid fever B\nKet : asdad\nDiag 2 : [A01.3] Paratyphoid fever C\nKet : dfdfg\nDiag 3 : [A01.0] Typhoid fever\nKet : sdfsdf',
    planning: '*Tindakan*\n*Pengobatan*',
    petugas: '',
  },
  {
    tanggal: '22-07-2025',
    unit: '*Unit*\nPUSKESMAS WONGSOREJO\n*Ruang layanan*\nPoli 1 : Umum - Rujuk Internal\nPoli 2 : UGD',
    subjective: 'test\nkeluhan tambahan : test',
    objective: 'TB : 165.4 cm   BB : 55 kg\nLingkar Perut : 60 cm',
    assessment: 'Diag 1 : [A01.4] Paratyphoid fever, unspecified\nDiag 2 : []',
    planning: '*Tindakan*\nTindakan 1 : [00.25] Intravascular imaging of renal vessels\nKet : -\nKet Gigi : -',
    petugas: '',
  },
];
</script>

<style scoped>
.table th,
.table td {
  vertical-align: top;
  font-size: 11.5px;
  padding: 6px;
}

small {
  font-size: 10px;
}

.no-print {
  display: block;
}

@media print {
  .no-print {
    display: none !important;
  }

  body {
    background: white !important;
  }

  .container {
    box-shadow: none !important;
    border: none !important;
    margin: 0;
    padding: 0;
  }
}
</style>
