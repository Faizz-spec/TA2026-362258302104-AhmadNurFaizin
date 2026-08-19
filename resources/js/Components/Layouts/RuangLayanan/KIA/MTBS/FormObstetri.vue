<template>
  <div class="bg-white shadow-sm p-3 rounded-3 mb-3 d-flex align-items-center">
    <h5 class="fw-semibold text-success mb-1">Pemeriksaan Obstetri</h5>
  </div>
  <form @submit.prevent="saveForm">
    <div class="row mb-4 g-4">
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-4">
                <label class="form-label fw-semibold">Gravida</label>
                <input
                  v-model="form.gravida"
                  type="text"
                  class="form-control"
                  placeholder="Gravida 0 s/d 20"
                />
              </div>

              <div class="col-4">
                <label class="form-label fw-semibold">Partus</label>
                <input
                  v-model="form.partus"
                  type="text"
                  class="form-control"
                  placeholder="Partus 0 s/d 20"
                />
              </div>

              <div class="col-4">
                <label class="form-label fw-semibold">Abortus</label>
                <input
                  v-model="form.abortus"
                  type="text"
                  class="form-control"
                  placeholder="Abortus 0 s/d 20"
                />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Tanggal TPHT</label>
              <input v-model="form.tanggal_tpht" type="date" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Jarak Hamil</label>
              <input v-model="form.jarak_hamil" type="number" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Status Imunisasi Tetanus</label>
              <select v-model="form.statusImunisasi" class="form-select">
                <option value="0">Belum Pernah</option>
                <option value="1">Sudah Pernah</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Tinggi Badan</label>
                <input v-model="form.tbadan" type="number" class="form-control" />
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Berat Badan Sebelum Hamil</label>
                <input v-model="form.bb_sebelum" type="number" class="form-control" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">IMT</label>
                <input v-model="form.imt" disabled type="text" class="form-control" />
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Status</label>
                <input v-model="form.status_imt" disabled type="text" class="form-control" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Target Kenaikan Berat Badan</label>
              <div class="input-group">
                <input v-model="form.bbTarget" disabled type="text" class="form-control" />
                <span class="input-group-text">Kg</span>
              </div>
            </div>
            <div class="mb-2 text-end">
              <button type="submit" class="btn btn-success w-100 shadow-sm px-3">
                <i class="bi bi-save me-1"></i> Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="form.statusImunisasi == '1'" class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3">
          <div class="card-body">
            <div class="row mb-2" v-for="(item, index) in form.imunisasi" :key="index">
              <div class="col-4">
                <label class="form-label fw-semibold">Tanggal Imunisasi {{ index + 1 }}</label>
                <input
                  type="date"
                  v-model="item.tanggal"
                  class="form-control"
                  placeholder="dd/mm/yyyy"
                />
              </div>

              <div class="col-4">
                <label class="form-label fw-semibold">No Batch</label>
                <input
                  type="text"
                  v-model="item.noBatch"
                  class="form-control"
                  placeholder="No Batch"
                />
              </div>

              <div class="col-4">
                <label class="form-label fw-semibold">Nama Vaksin</label>
                <input
                  type="text"
                  v-model="item.namaVaksin"
                  class="form-control"
                  placeholder="Nama Vaksin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { ref, watch, reactive } from 'vue';
  import { route } from 'ziggy-js';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    DataPasien: Array,
  });

  const form = ref({
    pasienId: props.DataPasien[0].ID,
    gravida: '',
    partus: '',
    abortus: '',
    tbadan: '',
    tanggal_tpht: '',
    bb_sebelum: '',
    bbTarget: '',
    imt: '',
    status_imt: '',
    jarak_hamil: '',
    statusImunisasi: '0',
    imunisasi: [
      { tanggal: '', noBatch: '', namaVaksin: '' },
      { tanggal: '', noBatch: '', namaVaksin: '' },
      { tanggal: '', noBatch: '', namaVaksin: '' },
      { tanggal: '', noBatch: '', namaVaksin: '' },
      { tanggal: '', noBatch: '', namaVaksin: '' },
    ],
  });

  // Fungsi hitung IMT
  const hitungIMT = () => {
    const bb = parseFloat(form.value.bb_sebelum);
    const tb = parseFloat(form.value.tbadan) / 100; // ubah cm ke meter

    if (bb && tb) {
      const imt = bb / (tb * tb);
      form.value.imt = imt.toFixed(1);

      // Tentukan status IMT
      if (imt < 18.5) {
        form.value.status_imt = 'Kekurangan Berat Badan';
        form.value.bbTarget = '12.5 - 15';
      } else if (imt < 25) form.value.status_imt = 'Normal';
      else if (imt < 30) form.value.status_imt = 'Kelebihan Berat badan';
      else form.value.status_imt = 'Obesitas';
    } else {
      form.value.imt = '';
      form.value.status_imt = '';
    }
  };

  // Watcher — akan otomatis jalan saat berat atau tinggi berubah
  watch(() => [form.value.bb_sebelum, form.value.tbadan, form.value.bbTarget], hitungIMT);

  const saveForm = () => {
    router.post(route('ruang-layanan-anc.obstetri'), form.value, {
      onSuccess: () => {
        alert('Data Kunjungan berhasil disimpan!');

        // Reset form dengan benar
        Object.assign(form.value, {
          gravida: '',
          partus: '',
          abortus: '',
          tbadan: '',
          tanggal_tpht: '',
          bb_sebelum: '',
          bbTarget: '',
          imt: '',
          status_imt: '',
          jarak_hamil: '',
          statusImunisasi: '0',

          // Reset imunisasi juga
          imunisasi: [
            { tanggal: '', noBatch: '', namaVaksin: '' },
            { tanggal: '', noBatch: '', namaVaksin: '' },
            { tanggal: '', noBatch: '', namaVaksin: '' },
            { tanggal: '', noBatch: '', namaVaksin: '' },
            { tanggal: '', noBatch: '', namaVaksin: '' },
          ],
        });
      },

      onError: (errors) => {
        console.error(errors);
        alert('Gagal menyimpan data.');
      },
    });
  };
</script>
