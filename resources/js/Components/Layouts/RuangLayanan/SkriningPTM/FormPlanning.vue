<template>
  <div class="planning-form">
    <div class="planning-toolbar">
      <div>
        <p class="planning-kicker">Planning</p>
        <h3>Rencana Tindakan dan Pengobatan</h3>
      </div>

      <div class="segmented-control" role="tablist" aria-label="Planning">
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormPlanning === 'tindakan' }"
          @click="toggleForm('tindakan')"
        >
          <i class="bi bi-person-check"></i>
          <span>Tindakan</span>
        </button>
        <button
          type="button"
          class="segment-button"
          :class="{ active: activeFormPlanning === 'pengobatan' }"
          @click="toggleForm('pengobatan')"
        >
          <i class="bi bi-capsule"></i>
          <span>Pengobatan</span>
        </button>
      </div>
    </div>

    <div v-if="activeFormPlanning === 'tindakan'" class="fade-in">
      <section class="planning-panel">
        <div class="panel-header">
          <div>
            <h4><i class="bi bi-plus-circle"></i> Rencana Tindakan Skrining PTM</h4>
            <p>Pilih intervensi dan edukasi yang akan dilakukan pada pasien.</p>
          </div>
        </div>

        <div class="panel-body">
          <div class="form-grid action-form-grid">
            <div class="form-field span-2">
              <label class="form-label">Intervensi / Edukasi yang Diberikan</label>
              <div class="action-check-grid">
                <label
                  v-for="item in daftarTindakanPtm"
                  :key="item.kode"
                  class="action-check-item"
                  :class="{ checked: selectedTindakan.includes(item.kode) }"
                >
                  <input
                    v-model="selectedTindakan"
                    type="checkbox"
                    :value="item.kode"
                  />
                  <span>
                    <strong>{{ item.nama }}</strong>
                    <small>{{ item.keterangan }}</small>
                  </span>
                </label>
              </div>
            </div>

            <div class="form-field span-2">
              <label class="form-label" for="keterangan_tindakan">Keterangan</label>
              <textarea
                id="keterangan_tindakan"
                v-model="form.keterangan"
                class="form-control"
                rows="3"
                placeholder="Resep, anjuran, instruksi khusus, atau catatan tindak lanjut"
              ></textarea>
            </div>
          </div>
        </div>

        <div class="panel-footer">
          <button
            type="button"
            class="btn btn-outline-danger"
            @click="hapusForm"
            :disabled="form.processing"
          >
            <i class="bi bi-x-lg"></i>
            <span>Bersihkan</span>
          </button>
          <button
            type="button"
            class="btn btn-success"
            @click.prevent.stop="simpanData"
            :disabled="form.processing || selectedTindakan.length === 0"
          >
            <i class="bi" :class="form.processing ? 'bi-arrow-repeat spinner' : 'bi-check-circle'"></i>
            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Tindakan Terpilih' }}</span>
          </button>
        </div>
      </section>

      <section class="planning-panel">
        <div class="panel-header compact">
          <div>
            <h4><i class="bi bi-list-check"></i> Daftar Tindakan</h4>
            <p>{{ dataTindakan.length }} tindakan tercatat untuk pasien ini.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table planning-table mb-0">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Tindakan</th>
                <th>Poli</th>
                <th>Keterangan</th>
                <th>Created By</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="dataTindakan.length === 0">
                <td colspan="7" class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <span>Data tindakan belum tersedia.</span>
                </td>
              </tr>
              <tr v-for="(item, i) in dataTindakan" :key="item.idTindakan || i">
                <td>{{ i + 1 }}</td>
                <td><span class="code-pill">{{ item.kdTindakan || '-' }}</span></td>
                <td class="fw-semibold">{{ item.nmTindakan || '-' }}</td>
                <td><span class="service-pill">{{ item.nmPoli || '-' }}</span></td>
                <td class="table-muted">{{ item.keterangan || '-' }}</td>
                <td>{{ item.createdBy || '-' }}</td>
                <td class="text-center">
                  <button
                    class="btn btn-outline-danger btn-sm btn-icon"
                    @click="hapusTindakan(item.idTindakan)"
                    title="Hapus tindakan"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <ModalHapus
        :show="showDeleteModal"
        title="Hapus Tindakan?"
        message="Data tindakan yang dihapus tidak dapat dikembalikan."
        @close="showDeleteModal = false"
        @confirm="konfirmasiHapus"
      />

      <div v-if="showSuccessModal" class="success-overlay">
        <div class="success-dialog">
          <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <h5>Tindakan Berhasil Disimpan</h5>
          <p>Data tindakan telah berhasil disimpan ke sistem.</p>
          <button type="button" class="btn btn-success" @click="showSuccessModal = false">
            OK
          </button>
        </div>
      </div>
    </div>

    <div v-if="activeFormPlanning === 'pengobatan'" class="fade-in">
      <section class="planning-panel">
        <div class="panel-header">
          <div>
            <h4><i class="bi bi-capsule-pill"></i> Pengobatan dan Resep</h4>
            <p>Atur tipe obat dan instruksi pakai sebelum menyimpan resep.</p>
          </div>
        </div>

        <div class="panel-body">
          <div class="form-grid">
            <div class="form-field">
              <label class="form-label">Tipe Obat</label>
              <select class="form-select" v-model="jenisPuyer">
                <option value="Puyer">Puyer</option>
                <option value="Bukan Puyer">Bukan Puyer</option>
              </select>
            </div>

            <div class="form-note">
              <i class="bi bi-info-circle"></i>
              <span>
                Untuk tipe bukan puyer, resep akan disimpan sebagai catatan obat non racikan.
              </span>
            </div>
          </div>

          <div v-if="jenisPuyer === 'Puyer'" class="puyer-section">
            <div class="section-subtitle">
              <i class="bi bi-prescription2"></i>
              <span>Aturan Pakai Puyer</span>
            </div>

            <div class="form-grid">
              <div class="form-field">
                <label class="form-label">Jumlah Puyer</label>
                <div class="input-unit">
                  <input
                    type="number"
                    class="form-control"
                    v-model="jumlahPuyer"
                    placeholder="0"
                    min="0"
                  />
                  <span>bungkus</span>
                </div>
              </div>

              <div class="form-field span-2">
                <label class="form-label">Dosis Pakai</label>
                <div class="dose-grid">
                  <input
                    type="number"
                    class="form-control"
                    v-model="dosisPerHari"
                    placeholder="0"
                    min="0"
                  />
                  <span>x sehari, setiap</span>
                  <input
                    type="number"
                    class="form-control"
                    v-model="intervalJam"
                    placeholder="0"
                    min="0"
                  />
                  <span>jam sekali</span>
                </div>
              </div>

              <div class="form-field">
                <label class="form-label">Waktu</label>
                <div class="check-list">
                  <label class="check-item" v-for="w in ['Pagi', 'Siang', 'Malam']" :key="w">
                    <input
                      type="checkbox"
                      :value="w.toLowerCase()"
                      v-model="waktu"
                      :id="'waktu-' + w"
                    />
                    <span>{{ w }}</span>
                  </label>
                </div>
              </div>

              <div class="form-field span-2">
                <label class="form-label">Kondisi</label>
                <div class="check-list">
                  <label
                    class="check-item"
                    v-for="k in ['Sebelum Makan', 'Saat Makan', 'Setelah Makan']"
                    :key="k"
                  >
                    <input
                      type="checkbox"
                      :value="k.toLowerCase()"
                      v-model="kondisi"
                      :id="'kondisi-' + k"
                    />
                    <span>{{ k }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel-footer">
          <button class="btn btn-success" @click="simpanResep">
            <i class="bi bi-check-circle"></i>
            <span>Simpan Resep</span>
          </button>
        </div>
      </section>

      <section class="planning-panel">
        <div class="panel-header compact">
          <div>
            <h4><i class="bi bi-journal-medical"></i> Daftar Resep</h4>
            <p>{{ dataResep.length }} resep sementara tercatat.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table planning-table mb-0">
            <thead>
              <tr>
                <th>Poli</th>
                <th>Jenis / Nama Obat</th>
                <th>Jumlah</th>
                <th>Dosis Pakai</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="dataResep.length === 0">
                <td colspan="5" class="empty-state">
                  <i class="bi bi-capsule"></i>
                  <span>Belum ada resep.</span>
                </td>
              </tr>
              <tr v-for="(item, index) in dataResep" :key="index">
                <td><span class="service-pill">{{ item.poli }}</span></td>
                <td class="fw-semibold">{{ item.jenis }}</td>
                <td>{{ item.jumlah }}</td>
                <td class="table-muted">{{ item.dosis }}</td>
                <td class="text-center">
                  <button
                    class="btn btn-outline-danger btn-sm btn-icon"
                    @click="hapusResep(index)"
                    title="Hapus resep"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import ModalHapus from '../../Modal/ModalHapus.vue';

const activeFormPlanning = ref('tindakan');
const toggleForm = (form) => { activeFormPlanning.value = form; };

const props = defineProps({
  DataPasien: Object,
  tindakan: Array,
  DataTindakan: Array,
});

const dataTindakan = computed(() => props.DataTindakan || []);
const selectedTindakan = ref([]);
const daftarTindakanPtm = [
  {
    kode: 'eduk_gizi',
    nama: 'Edukasi gizi seimbang',
    keterangan: 'Anjuran pola makan sehat, pembatasan gula, garam, dan lemak.',
  },
  {
    kode: 'eduk_aktfis',
    nama: 'Edukasi aktivitas fisik',
    keterangan: 'Anjuran aktivitas fisik rutin sesuai kondisi pasien.',
  },
  {
    kode: 'eduk_rokok',
    nama: 'Konseling berhenti merokok (UBM)',
    keterangan: 'Upaya berhenti merokok dan pencegahan paparan asap rokok.',
  },
  {
    kode: 'eduk_htn',
    nama: 'Edukasi tatalaksana hipertensi',
    keterangan: 'Pemantauan tekanan darah, gaya hidup, dan kepatuhan kontrol.',
  },
  {
    kode: 'eduk_dm',
    nama: 'Edukasi tatalaksana DM',
    keterangan: 'Pemantauan gula darah, diet, aktivitas fisik, dan kontrol berkala.',
  },
];

// Form Tindakan
const form = useForm({
  idpelayanan: props.DataPasien.idpelayanan,
  loketId: props.DataPasien.idLoket,
  kdPoli: props.DataPasien.kdPoli,
  kode_tindakan: '',
  nama_tindakan: '',
  nama_tindakan_ind: '',
  keterangan: '',
});

const showSuccessModal = ref(false);
const showDeleteModal = ref(false);
const selectedDeleteId = ref(null);

const hapusTindakan = (id) => {
  selectedDeleteId.value = id;
  showDeleteModal.value = true;
};

const konfirmasiHapus = () => {
  router.delete(route('ptm.tindakan-hapus', { id: selectedDeleteId.value }), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedDeleteId.value = null;
    },
    onError: (errors) => console.error(errors),
  });
};

const hapusForm = () => {
  selectedTindakan.value = [];
  form.kode_tindakan = '';
  form.nama_tindakan = '';
  form.nama_tindakan_ind = '';
  form.keterangan = '';
};

const simpanData = () => {
  const tindakanTerpilih = daftarTindakanPtm.filter((item) =>
    selectedTindakan.value.includes(item.kode)
  );

  if (tindakanTerpilih.length === 0) return;

  simpanTindakanBerikutnya(tindakanTerpilih);
};

const simpanTindakanBerikutnya = (items, index = 0) => {
  const item = items[index];

  form.kode_tindakan = item.kode;
  form.nama_tindakan = item.nama;
  form.nama_tindakan_ind = item.nama;

  form.post(route('ptm.tindakan-simpan'), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      if (index + 1 < items.length) {
        simpanTindakanBerikutnya(items, index + 1);
        return;
      }

      showSuccessModal.value = true;
      form.reset();
      hapusForm();
    },
    onError: (errors) => console.log(errors),
  });
};

// Pengobatan
const jenisPuyer = ref('Bukan Puyer');
const jumlahPuyer = ref('');
const dosisPerHari = ref('');
const intervalJam = ref('');
const waktu = ref([]);
const kondisi = ref([]);
const dataResep = ref([]);

watch(jenisPuyer, (val) => {
  if (val === 'Bukan Puyer') {
    jumlahPuyer.value = '';
    dosisPerHari.value = '';
    intervalJam.value = '';
    waktu.value = [];
    kondisi.value = [];
  }
});

const simpanResep = () => {
  dataResep.value.push({
    poli: props.DataPasien.kdPoli || 'KIA',
    jenis: jenisPuyer.value,
    jumlah: jenisPuyer.value === 'Puyer' ? `${jumlahPuyer.value} bungkus` : '-',
    dosis: jenisPuyer.value === 'Puyer'
      ? `${dosisPerHari.value}x / ${intervalJam.value} jam`
      : '-',
  });
};

const hapusResep = (index) => {
  dataResep.value.splice(index, 1);
};
</script>

<style scoped>
.planning-form {
  display: grid;
  gap: 18px;
}

.planning-toolbar,
.panel-header,
.panel-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.planning-toolbar {
  padding-bottom: 16px;
  border-bottom: 1px solid #e5edf0;
}

.planning-kicker {
  margin: 0 0 4px;
  color: #64748b;
  font-size: 0.76rem;
  font-weight: 750;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.planning-toolbar h3 {
  margin: 0;
  color: #0f172a;
  font-size: 1.2rem;
  font-weight: 750;
}

.segmented-control {
  display: inline-flex;
  gap: 4px;
  padding: 4px;
  border: 1px solid #cfd9e3;
  border-radius: 8px;
  background: #f8fafc;
}

.segment-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 36px;
  padding: 7px 12px;
  border: 0;
  border-radius: 6px;
  background: transparent;
  color: #475569;
  font-size: 0.86rem;
  font-weight: 700;
}

.segment-button.active {
  background: #0f6b4c;
  color: #ffffff;
  box-shadow: 0 8px 18px rgba(15, 107, 76, 0.18);
}

.planning-panel {
  overflow: hidden;
  margin-bottom: 18px;
  border: 1px solid #d9e5df;
  border-radius: 8px;
  background: #ffffff;
}

.panel-header {
  padding: 18px 20px;
  border-bottom: 1px solid #e5edf0;
  background: #f8fafc;
}

.panel-header.compact {
  padding-block: 15px;
}

.panel-header h4 {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0;
  color: #0f3d2e;
  font-size: 1rem;
  font-weight: 750;
}

.panel-header p {
  margin: 5px 0 0;
  color: #64748b;
  font-size: 0.86rem;
}

.panel-body {
  padding: 20px;
}

.panel-footer {
  justify-content: flex-end;
  padding: 16px 20px;
  border-top: 1px solid #e5edf0;
  background: #fbfdff;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.action-form-grid {
  grid-template-columns: 1fr;
}

.form-field {
  min-width: 0;
}

.span-2 {
  grid-column: span 2;
}

.form-label {
  margin-bottom: 6px;
  color: #334155;
  font-size: 0.86rem;
  font-weight: 700;
}

.form-control,
.form-select {
  min-height: 42px;
  border: 1px solid #cfd9e3;
  border-radius: 8px;
  color: #0f172a;
}

textarea.form-control {
  resize: vertical;
}

.form-control:focus,
.form-select:focus {
  border-color: #16a36f;
  box-shadow: 0 0 0 0.2rem rgba(22, 163, 111, 0.14);
}

.input-action {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto auto;
  gap: 8px;
}

.action-check-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  margin-top: 6px;
}

.action-check-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  min-height: 86px;
  padding: 13px;
  border: 1px solid #d9e5df;
  border-radius: 8px;
  background: #ffffff;
  color: #334155;
  cursor: pointer;
}

.action-check-item.checked {
  border-color: #16a36f;
  background: #effaf5;
  color: #0f6b4c;
}

.action-check-item input {
  flex: 0 0 auto;
  margin-top: 3px;
}

.action-check-item strong,
.action-check-item small {
  display: block;
}

.action-check-item strong {
  font-size: 0.9rem;
  line-height: 1.35;
}

.action-check-item small {
  margin-top: 5px;
  color: #64748b;
  font-size: 0.78rem;
  font-weight: 650;
  line-height: 1.4;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 8px;
  font-weight: 650;
}

.btn-icon {
  width: 36px;
  height: 36px;
  padding: 0;
}

.planning-table th {
  padding: 13px 12px;
  border-bottom: 1px solid #c9ded6;
  background: #e7f5ef;
  color: #174236;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0;
  text-transform: uppercase;
  white-space: nowrap;
}

.planning-table td {
  padding: 13px 12px;
  color: #1e293b;
  font-size: 0.84rem;
  line-height: 1.5;
  vertical-align: middle;
}

.planning-table tbody tr:hover {
  background: #f6fbf8;
}

.table-muted {
  color: #64748b;
}

.code-pill,
.service-pill {
  display: inline-flex;
  align-items: center;
  min-height: 26px;
  padding: 4px 9px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 750;
}

.code-pill {
  background: #e0f2fe;
  color: #075985;
}

.service-pill {
  background: #e7f5ef;
  color: #0f6b4c;
}

.empty-state {
  height: 108px;
  color: #64748b;
  text-align: center;
}

.empty-state i {
  display: block;
  margin-bottom: 6px;
  font-size: 1.5rem;
}

.form-note {
  display: flex;
  align-items: center;
  gap: 10px;
  align-self: end;
  min-height: 42px;
  padding: 10px 12px;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: #eff6ff;
  color: #1e40af;
  font-size: 0.84rem;
  font-weight: 650;
}

.puyer-section {
  margin-top: 18px;
  padding: 16px;
  border: 1px solid #cdeedd;
  border-radius: 8px;
  background: #f6fbf8;
}

.section-subtitle {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
  color: #0f6b4c;
  font-size: 0.9rem;
  font-weight: 750;
}

.input-unit {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: center;
  gap: 8px;
}

.input-unit span,
.dose-grid span {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 650;
}

.dose-grid {
  display: grid;
  grid-template-columns: 90px auto 90px auto;
  align-items: center;
  gap: 8px;
}

.check-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.check-item {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 36px;
  padding: 7px 10px;
  border: 1px solid #d9e5df;
  border-radius: 8px;
  background: #ffffff;
  color: #334155;
  font-size: 0.86rem;
  font-weight: 650;
}

.success-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: grid;
  place-items: center;
  padding: 24px;
  background: rgba(15, 23, 42, 0.42);
}

.success-dialog {
  width: min(95%, 380px);
  padding: 28px 24px;
  border-radius: 8px;
  background: #ffffff;
  text-align: center;
  box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
}

.success-icon {
  display: grid;
  place-items: center;
  width: 70px;
  height: 70px;
  margin: 0 auto 14px;
  border-radius: 50%;
  background: #dcfce7;
  color: #16a34a;
  font-size: 2.2rem;
}

.success-dialog h5 {
  margin: 0 0 6px;
  color: #166534;
  font-size: 1.05rem;
  font-weight: 750;
}

.success-dialog p {
  margin: 0 0 18px;
  color: #64748b;
  font-size: 0.9rem;
}

.fade-in {
  animation: fadeIn 0.2s ease-in-out;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .planning-toolbar {
    align-items: stretch;
    flex-direction: column;
  }

  .segmented-control,
  .segment-button,
  .panel-footer .btn {
    width: 100%;
  }

  .form-grid,
  .input-action,
  .dose-grid,
  .action-check-grid {
    grid-template-columns: 1fr;
  }

  .span-2 {
    grid-column: auto;
  }
}
</style>
