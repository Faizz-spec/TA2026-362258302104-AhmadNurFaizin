<template>
  <div class="card m-2 rounded-4 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between align-items-center p-3"
      style="background: linear-gradient(135deg, #4682B4, #5A9BD5);">
      <h5 class="text-white fw-bold m-0">
        <i class="fas fa-stethoscope me-2"></i> Form Anamnesa
      </h5>
    </div>

    <!-- Body -->
    <div class="card-body p-4">
      <form>
        <div class="row g-4">
          <!-- Kolom Kiri -->
          <div class="col-12 col-md-6">
            <div class="mb-3">
              <label class="form-label fw-bold">Tgl Anamnesa</label>
              <input type="date" class="form-control shadow-sm rounded-3" />
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keluhan Utama/Keperluan</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keluhan Tambahan</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Riwayat Penyakit Sekarang</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Riwayat Penyakit Dahulu</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Riwayat Penyakit Keluarga</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-12 col-md-6">
            <div class="mb-3">
              <label class="form-label fw-bold">Alergi Makanan</label>
              <select class="form-select shadow-sm rounded-3 bg-primary bg-opacity-10" v-model="selectedMakanan">
                <option value="">Tidak Ada Alergi</option>
                <option v-for="a in alergiMakanan" :key="a.id" :value="a.namaAlergiBpjs">
                  {{ a.namaAlergiBpjs }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Alergi Obat</label>
              <select class="form-select shadow-sm rounded-3 bg-primary bg-opacity-10" v-model="selectedObat">
                <option value="">Tidak Ada Alergi</option>
                <option v-for="a in alergiObat" :key="a.id" :value="a.namaAlergiBpjs">
                  {{ a.namaAlergiBpjs }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Keterangan Alergi</label>
              <textarea class="form-control shadow-sm rounded-3 bg-primary bg-opacity-10" rows="2"
                v-model="keteranganAlergi"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Tindakan/Terapi</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Obat yang Sering Digunakan</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Obat yang Sering Dikonsumsi</label>
              <textarea class="form-control shadow-sm rounded-3" rows="2"></textarea>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit"
                class="btn btn-success d-flex align-items-center gap-2 px-4 fw-semibold text-white shadow-sm rounded-pill"
                style="background: linear-gradient(135deg, #4682B4, #5A9BD5); border: none;">
                <i class="fas fa-save"></i> SIMPAN
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// data dari backend (Laravel)
const alergiMakanan = ref(page.props.alergiMakanan);
const alergiObat = ref(page.props.alergiObat);
const pasien = ref(page.props.pasien);

const props = defineProps({
  alergiMakanan: Array,
  alergiObat: Array,
});

// isi default sesuai database
const selectedMakanan = ref('');
const selectedObat = ref('');
const keteranganAlergi = ref('');

// isi otomatis dari data pasien
onMounted(() => {
  if (pasien.value && pasien.value.alergi) {
    // misal kolom ALERGI berisi teks seperti "Makanan: Susu, Obat: Paracetamol"
    const alergiText = pasien.value.alergi.toLowerCase();

    if (alergiText.includes('makanan')) {
      const match = alergiText.match(/makanan[:\-]?\s*([a-z0-9\s]+)/i);
      if (match) selectedMakanan.value = match[1].trim();
    }

    if (alergiText.includes('obat')) {
      const match = alergiText.match(/obat[:\-]?\s*([a-z0-9\s]+)/i);
      if (match) selectedObat.value = match[1].trim();
    }

    keteranganAlergi.value = pasien.value.alergi;
  }
});

function goBack() {
  window.history.back();
}
</script>
