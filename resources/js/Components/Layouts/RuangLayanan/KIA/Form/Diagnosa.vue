<template>
  <div class="row g-4 mb-2">
    <div class="col-md-6">
      <div class="card shadow-sm border-0 p-2">
        <div class="card-body">
          <form>
            <div class="row mb-3">
              <label class="form-label fw-semibold">Diagnosa</label>
              <div class="col-3">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Kode"
                  disabled
                  v-model="form.kode_diagnosa"
                />
              </div>
              <div class="col-9">
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control bg-light"
                    placeholder="Nama Diagnosa"
                    disabled
                    v-model="form.nama_diagnosa"
                  />
                  <button type="button" class="btn btn-info" @click="showModal = true">Cari</button>
                  <button type="button" class="btn btn-danger" @click="hapusForm">Del</button>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-6 d-flex flex-column">
                <label class="form-label fw-semibold">Alergi Makanan</label>
                <select class="form-select">
                  <option v-for="alrgm in AlergiMakanan" :key="alrgm.kodeBpjs">
                    {{ alrgm.namaAlergiBpjs }}
                  </option>
                </select>
              </div>
              <div class="col-6 d-flex flex-column">
                <label class="form-label fw-semibold">Alergi Obat</label>
                <select class="form-select">
                  <option
                    v-for="alrgo in AlergiObat"
                    :key="alrgo.kodeBpjs"
                    :value="alrgo.namaAlergiBpjs"
                  >
                    {{ alrgo.namaAlergiBpjs }}
                  </option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Keterangan Alergi</label>
              <textarea class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Kunjungan Kasus</label>
              <select class="form-control" v-model="form.kunjungan_khusus">
                <option value="">- Pilih -</option>
                <option value="1">Kasus Baru</option>
                <option value="2">Kasus Lama</option>
                <option value="3">Kunjungan Kasus Lama</option>
                <option value="4">Kunjungan Kasus Baru</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Keterangan</label>
              <textarea class="form-control" rows="2" v-model="form.keterangan"></textarea>
            </div>

            <div class="mb-4">
              <button type="button" @click="saveForm" class="btn btn-success">
                <i class="far fa-save me-2"></i> SIMPAN DIAGNOSA MEDIS
              </button>
            </div>

            <hr />

            <div class="mb-1">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Diagnosa Medis</th>
                    <th>Keterangan</th>
                    <th>Kasus</th>
                    <th>Poli</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(diag, index) in daftarDiagnosa" :key="diag.idDiagnosa">
                    <td>{{ index + 1 }}</td>
                    <td>{{ diag.nmDiagnosa }}</td>
                    <td>{{ diag.keterangan }}</td>
                    <td>{{ diag.diagnosaKasus }}</td>
                    <td>KIA</td>
                    <td>
                      <button
                        type="button"
                        @click="hapusData(diag.idDiagnosa)"
                        class="btn btn-danger btn-sm"
                      >
                        Hapus
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm border-0 p-3">
        <div class="card-body">
          <!-- Pemeriksaan Penunjang -->
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Pemeriksaan Penunjang</label>
<Link
  v-if="loketId"
  :href="route('ruang-layanan.laborat.pemeriksaan', { loketId })"
  class="btn btn-info"
>
  <i class="fas fa-bars me-2"></i> Laboratorium
</Link>

<span v-else class="text-muted small">
  Data pasien belum siap
</span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="form-label fw-semibold mb-2">Form Lanjutan</label>
            <div class="d-flex gap-2">
              <button
                type="button"
                class="btn btn-secondary bg-opacity-75 flex-fill"
                @click="alert('Buka form diare')"
              >
                <i class="fas fa-flask"></i> Diare
              </button>
              <button
                type="button"
                class="btn btn-secondary bg-opacity-75 flex-fill"
                @click="alert('Buka form katarak')"
              >
                <i class="fas fa-flask"></i> Katarak
              </button>
              <button
                type="button"
                class="btn btn-secondary bg-opacity-75 flex-fill"
                @click="alert('Buka form PTM')"
              >
                <i class="fas fa-flask"></i> PTM
              </button>
              <button
                type="button"
                class="btn btn-secondary bg-opacity-75 flex-fill"
                @click="alert('Buka form hipertensi')"
              >
                <i class="fas fa-flask"></i> Hipertensi
              </button>
            </div>
          </div>

          <div class="mb-1">
            <label class="form-label fw-semibold">Diagnosa Keperawatan</label>
            <select class="form-select">
              <option value="">-- Pilih --</option>
              <option v-for="item in diagnosaKeperawatan" :key="item.kdDiag">
                {{ item.nmDiag }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <button type="button" @click="formDiagnosaKep" class="btn btn-success mt-4">
              <i class="far fa-save me-2"></i> Simpan Diagnosa Keperawatan
            </button>
          </div>
          <hr />
          <div class="mb-3">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Diagnosa Asuhan Keperawatan</th>
                  <th>Keterangan</th>
                  <th>Kasus</th>
                  <th>Poli</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>Umum</td>
                  <td>
                    <button class="btn btn-danger btn-sm">Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <DiagnosaModal
    :show="showModal"
    :diagnosa="diagnosa"
    @close="showModal = false"
    @select="pilihDiagnosa"
  />
</template>
<script setup>
  import { ref, computed } from 'vue';
  import axios from 'axios';
  import { route } from 'ziggy-js';
  import { router, Link, usePage } from '@inertiajs/vue3';
  import DiagnosaModal from '../DiagnosaModal.vue';

  const props = defineProps({
    DataPasien: Array,
    DataDiagnosa: Array,
    diagnosa: Array,
    diagnosaKeperawatan: Array,
    AlergiMakanan: Array,
    AlergiObat: Array,
  });

  const form = ref({
    kode_diagnosa: '',
    nama_diagnosa: '',
    kunjungan_khusus: '',
    keterangan: '',
    kdPoli: props.DataPasien[0].kdPoli,
    loketId: props.DataPasien[0].idLoket,
    pelayananId: props.DataPasien[0].idPelayanan,
  });

  const daftarDiagnosa = ref([...props.DataDiagnosa]);

  const saveForm = async () => {
    try {
      const response = await axios.post(route('ruang-layanan-anc.dataDiagnosa'), form.value);

      // Ambil data dari server
      const dataBaru = response.data.data;

      // Tambahkan ke tabel tanpa reload
      daftarDiagnosa.value.push(dataBaru);

      // Reset form
      form.value = {
        kode_diagnosa: '',
        nama_diagnosa: '',
        kunjungan_khusus: '',
        keterangan: '',
        kdPoli: props.DataPasien[0].kdPoli,
        loketId: props.DataPasien[0].idLoket,
        pelayananId: props.DataPasien[0].idPelayanan,
      };

      alert('Data berhasil disimpan!');
    } catch (error) {
      console.error(error);
      alert('Gagal menyimpan data');
    }
  };

  const hapusData = async (id) => {
    if (!confirm('Yakin ingin menghapus data ini?')) return;

    try {
      await axios.delete(route('diagnosa.destroy', id));

      // Hapus dari reactive state
      const index = daftarDiagnosa.value.findIndex((item) => item.idDiagnosa === id);
      if (index !== -1) {
        daftarDiagnosa.value.splice(index, 1);
      }

      alert('Data berhasil dihapus!');
    } catch (error) {
      console.error(error);
      alert('Gagal menghapus data.');
    }
  };

  const formDiagnosaKep = () => {
    router.post(route('ruang-layanan-anc.diagnosaKep'), form.value, {
      onSuccess: () => {
        alert('Data Diagnosa berhasil disimpan!');
        form.value = {
          diagnosaKep: '',
        };
      },
      onError: (errors) => {
        console.error(errors);
        alert('Gagal menyimpan data.');
      },
    });
  };
  // Modal control
  const showModal = ref(false);

  // Fungsi
  const pilihDiagnosa = (item) => {
    form.value.kode_diagnosa = item.kdDiag;
    form.value.nama_diagnosa = item.nmDiag;
    showModal.value = false;
  };

  const hapusForm = () => {
    form.value.kode_diagnosa = '';
    form.value.nama_diagnosa = '';
  };

  //   const hapusData = (id) => {
  //     if (confirm('Yakin ingin menghapus data ini?')) {
  //       router.delete(route('diagnosa.destroy', id), {
  //         preserveState: true,
  //         preserveScroll: true,
  //         only: [],
  //         onSuccess: () => {
  //           alert('Data berhasil dihapus!');
  //         },
  //         onError: (errors) => {
  //           console.error(errors);
  //           alert('Gagal menghapus data.');
  //         },
  //       });
  //     }
  //   };
</script>
