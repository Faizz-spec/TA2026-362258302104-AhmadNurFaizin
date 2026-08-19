<template>
  <div class="p-2 rounded-4 mt-2">
    <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
      <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle px-2 me-3">
          <i class="bi bi-person-fill text-primary fs-1"></i>
        </div>

        <div>
          <h3 v-if="pasien" class="mb-1 fw-bold">
            {{ pasien.NAMA_LGKP }} <span class="text-muted">(-)</span>
          </h3>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar3 text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Jk / Umur</h6>
            </div>

            <p v-if="pasien" class="mb-0 fw-semibold">
              {{ pasien.jenis_kelamin_label }}
              {{ pasien.umur }} Tahun -
              {{ pasien.umur_bulan }} Bulan -
              {{ pasien.umur_hari }} Hari
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-geo-alt-fill text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Alamat</h6>
            </div>

            <p v-if="pasien" class="mb-0 fw-semibold">
              {{ pasien.alamat }} RT {{ pasien.no_rt }} RW {{ pasien.no_rw }} Kel.
              {{ pasien.nama_kel }} Kec. {{ pasien.nama_kec }}
              <br>
              {{ pasien.nama_kab }} Prov. {{ pasien.nama_prop }}
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-heart-pulse-fill text-success me-2"></i>
              <h6 class="mb-0 text-muted">Jenis/Poli</h6>
            </div>

            <p v-if="pasien" class="mb-0 fw-semibold">
              Kunjungan Sakit ({{ pasien.nmPoli }})
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar-check-fill text-info me-2"></i>
              <h6 class="mb-0 text-muted">Tanggal Kunjungan</h6>
            </div>

            <p v-if="pasien" class="mb-0 fw-semibold">
              {{ pasien.tglKunjungan }}
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-credit-card-fill text-warning me-2"></i>
              <h6 class="mb-0 text-muted">No. RM / NIK</h6>
            </div>

            <p v-if="pasien" class="mb-0 fw-semibold">
              {{ pasien.NO_MR }} / {{ pasien.NIK }}
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-credit-card-fill text-warning me-2"></i>
              <h6 class="mb-0 text-muted">No. BPJS/Provider</h6>
            </div>
            <p class="mb-0 fw-semibold">BPJS-9876543210 / Klinik Sehat</p>
          </div>
        </div>
      </div>
    </div>

    <div class="quick-actions mb-2">
      <div class="action-grid">

<Link
  v-if="pasien"
  class="action-card doc-action"
  :href="route('mtbs.rujukan.index', { kunjungan_id: pasien.idpelayanan })"
>
  <div class="action-icon">
    <i class="bi bi-send"></i>
  </div>
  <div class="action-label">Surat Rujukan</div>
</Link>

        <a href="#" class="action-card history-action" @click.prevent="openRiwayatPasien">
          <div class="action-icon">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="action-label">Riwayat Pasien</div>
        </a>

        <Link
          v-if="pasien"
          class="action-card medical-action"
          :href="route('ruang-layanan.cppt', {
            idPoli: pasien.kdPoli,
            idPasien: pasien.ID,
          })"
        >
          <div class="action-icon">
            <i class="bi bi-file-text"></i>
          </div>
          <div class="action-label">CPPT</div>
        </Link>
      </div>
    </div>

    <div v-if="showRiwayatPasien" class="modal-backdrop-custom" @click.self="closeRiwayatPasien">
      <div class="modal-card-custom">
        <div class="modal-header-custom">
          <div>
            <h5 class="mb-1 fw-bold">Riwayat Pasien</h5>
            <div class="text-muted small">
              {{ pasien?.NAMA_LGKP || '-' }} / {{ pasien?.NO_MR || '-' }}
            </div>
          </div>

          <button class="btn btn-sm btn-outline-secondary" @click="closeRiwayatPasien">
            Tutup
          </button>
        </div>

        <div class="riwayat-filter-card mb-3">
          <div class="row align-items-end g-2">
            <div class="col-md-4">
              <label class="form-label fw-semibold mb-1">
                Filter Tahun
              </label>

              <select
                v-model.number="tahunRiwayat"
                class="form-select form-select-sm"
                :disabled="loadingRiwayat || daftarTahunRiwayat.length === 0"
                @change="gantiTahunRiwayat"
              >
                <option
                  v-for="tahun in daftarTahunRiwayat"
                  :key="tahun"
                  :value="tahun"
                >
                  {{ tahun }}
                </option>
              </select>
            </div>

            <div class="col-md-8">
              <div v-if="daftarTahunRiwayat.length" class="text-muted small">
                Menampilkan riwayat pasien tahun
                <strong>{{ tahunRiwayat || '-' }}</strong>.
              </div>

              <div v-else-if="!loadingRiwayat" class="text-muted small">
                Belum ada tahun kunjungan yang tersedia.
              </div>
            </div>
          </div>
        </div>

        <div v-if="loadingRiwayat" class="alert alert-info mb-0">
          Memuat riwayat pasien...
        </div>

        <div v-else>
          <h6 class="fw-bold text-danger mb-2">Riwayat Alergi</h6>

          <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered align-middle">
              <tbody>
                <tr>
                  <th width="180">Alergi Obat</th>
                  <td>
                    <span v-if="riwayatDetail.alergiObat.length">
                      {{ riwayatDetail.alergiObat.join(', ') }}
                    </span>
                    <span v-else>-</span>
                  </td>
                </tr>

                <tr>
                  <th>Alergi Makanan</th>
                  <td>
                    <span v-if="riwayatDetail.alergiMakanan.length">
                      {{ riwayatDetail.alergiMakanan.join(', ') }}
                    </span>
                    <span v-else>-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h6 class="fw-bold text-primary mb-2">Riwayat Keluhan / Klasifikasi MTBS</h6>

          <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 130px;">Tanggal</th>
                  <th style="width: 120px;">Poli</th>
                  <th>Keluhan</th>
                  <th>Klasifikasi MTBS</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="item in riwayatDetail.riwayatKeluhan" :key="item.id">
                  <td>{{ item.tanggal || '-' }}</td>
                  <td>{{ item.poli || '-' }}</td>
                  <td>{{ item.keluhan || '-' }}</td>
                  <td>{{ item.klasifikasi || '-' }}</td>
                </tr>

                <tr v-if="riwayatDetail.riwayatKeluhan.length === 0">
                  <td colspan="4" class="text-center text-muted">
                    Belum ada riwayat keluhan / klasifikasi MTBS.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h6 class="fw-bold text-danger mb-2">Riwayat Diagnosis Medis</h6>

          <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 130px;">Tanggal</th>
                  <th style="width: 120px;">Poli</th>
                  <th style="width: 100px;">Kode</th>
                  <th>Diagnosis</th>
                  <th style="width: 90px;">Kasus</th>
                  <th>Keterangan</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="item in riwayatDetail.riwayatDiagnosa" :key="item.id">
                  <td>{{ item.tanggal || '-' }}</td>
                  <td>{{ item.poli || '-' }}</td>
                  <td>{{ item.kode || '-' }}</td>
                  <td>{{ item.nama || '-' }}</td>
                  <td>
                    <span
                      class="badge"
                      :class="item.kasus === 'baru' ? 'bg-primary' : 'bg-secondary'"
                    >
                      {{ item.kasus || '-' }}
                    </span>
                  </td>
                  <td>{{ item.keterangan || '-' }}</td>
                </tr>

                <tr v-if="riwayatDetail.riwayatDiagnosa.length === 0">
                  <td colspan="6" class="text-center text-muted">
                    Belum ada riwayat diagnosis medis.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h6 class="fw-bold text-success mb-2">Riwayat Obat yang Pernah Diberikan</h6>

          <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 130px;">Tanggal</th>
                  <th style="width: 120px;">Poli</th>
                  <th>Nama Obat</th>
                  <th>Dosis</th>
                  <th style="width: 90px;">Cara</th>
                  <th style="width: 100px;">Lama</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="item in riwayatDetail.riwayatObat" :key="item.id">
                  <td>{{ item.tanggal || '-' }}</td>
                  <td>{{ item.poli || '-' }}</td>
                  <td>{{ item.nama || '-' }}</td>
                  <td>{{ item.dosis || '-' }}</td>
                  <td>{{ item.cara || '-' }}</td>
                  <td>{{ item.lama || '-' }}</td>
                </tr>

                <tr v-if="riwayatDetail.riwayatObat.length === 0">
                  <td colspan="6" class="text-center text-muted">
                    Belum ada riwayat obat yang diberikan.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h6 class="fw-bold text-success mb-2">Riwayat Kunjungan Puskesmas</h6>

          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 130px;">Tanggal</th>
                  <th>Puskesmas/Unit</th>
                  <th>Poli</th>
                  <th>Status</th>
                  <th style="width: 110px;">ID Loket</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="item in riwayatDetail.riwayatKunjungan" :key="item.idLoket">
                  <td>{{ item.tanggal_kunjungan || '-' }}</td>
                  <td>{{ item.puskesmas || '-' }}</td>
                  <td>{{ item.poli || '-' }}</td>
                  <td>
                    <span
                      class="badge"
                      :class="item.status === 'Sudah dilayani' ? 'bg-success' : 'bg-warning text-dark'"
                    >
                      {{ item.status || '-' }}
                    </span>
                  </td>
                  <td>{{ item.idLoket || '-' }}</td>
                </tr>

                <tr v-if="riwayatDetail.riwayatKunjungan.length === 0">
                  <td colspan="5" class="text-center text-muted">
                    Belum ada riwayat kunjungan.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <FormAnc
        :DataPasien="props.DataPasien"
        :KunjunganAnc="props.KunjunganAnc"
        :diagnosa="props.diagnosa"
        :AlergiMakanan="props.AlergiMakanan"
        :AlergiObat="props.AlergiObat"
        :diagnosa-keperawatan="props.diagnosaKeperawatan"
        :tindakan="props.tindakan"
        :riwayat="props.riwayat"
        :DataDiagnosa="props.DataDiagnosa"
      />
    </div>
  </div>
</template>

<script setup>
import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue'
import FormAnc from '../../../../Components/Layouts/RuangLayanan/KIA/MTBS/Index.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import axios from 'axios'

defineOptions({ layout: AppLayouts })

const props = defineProps({
  KunjunganAnc: Array,
  DataPasien: Array,
  diagnosa: Array,
  tindakan: Array,
  riwayat: Array,
  diagnosaKeperawatan: Array,
  AlergiMakanan: Array,
  AlergiObat: Array,
  DataDiagnosa: Array,
  simpusResepObat: Array,
  routeResepObat: String,
  routeDetailResepObat: String,
})

const pasien = computed(() => props.DataPasien?.[0] || null)

const showRiwayatPasien = ref(false)
const loadingRiwayat = ref(false)
const tahunRiwayat = ref(null)
const daftarTahunRiwayat = ref([])

const riwayatDetail = ref({
  alergiObat: [],
  alergiMakanan: [],
  riwayatKeluhan: [],
  riwayatDiagnosa: [],
  riwayatObat: [],
  riwayatKunjungan: [],
})

const resetRiwayatDetail = () => {
  riwayatDetail.value = {
    alergiObat: [],
    alergiMakanan: [],
    riwayatKeluhan: [],
    riwayatDiagnosa: [],
    riwayatObat: [],
    riwayatKunjungan: [],
  }
}

const loadRiwayatPasien = async () => {
  if (!pasien.value?.idpelayanan) {
    alert('idpelayanan tidak ditemukan')
    return
  }

  loadingRiwayat.value = true
  resetRiwayatDetail()

  try {
    const params = {}

    if (tahunRiwayat.value) {
      params.tahun = tahunRiwayat.value
    }

    const res = await axios.get(
      `/simpus/kia/mtbs/riwayat-pasien/${pasien.value.idpelayanan}`,
      { params },
    )

    tahunRiwayat.value = res.data?.filter?.tahun ?? null

    daftarTahunRiwayat.value = Array.isArray(
      res.data?.filter?.tahunTersedia,
    )
      ? res.data.filter.tahunTersedia
      : []

    riwayatDetail.value = {
      alergiObat: res.data?.data?.alergiObat || [],
      alergiMakanan: res.data?.data?.alergiMakanan || [],
      riwayatKeluhan: res.data?.data?.riwayatKeluhan || [],
      riwayatDiagnosa: res.data?.data?.riwayatDiagnosa || [],
      riwayatObat: res.data?.data?.riwayatObat || [],
      riwayatKunjungan: res.data?.data?.riwayatKunjungan || [],
    }
  } catch (error) {
    console.error('Gagal load riwayat pasien:', error)
    alert(error.response?.data?.message || 'Gagal memuat riwayat pasien')
  } finally {
    loadingRiwayat.value = false
  }
}

const openRiwayatPasien = async () => {
  if (!pasien.value?.idpelayanan) {
    alert('idpelayanan tidak ditemukan')
    return
  }

  showRiwayatPasien.value = true
  tahunRiwayat.value = null
  daftarTahunRiwayat.value = []

  await loadRiwayatPasien()
}

const gantiTahunRiwayat = async () => {
  await loadRiwayatPasien()
}

const closeRiwayatPasien = () => {
  showRiwayatPasien.value = false
}
</script>

<style scoped>
.section-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}

.action-card {
  background: white;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(0, 0, 0, 0.05);
  cursor: pointer;
  text-decoration: none;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.action-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.action-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--dark-color);
}

.doc-action .action-icon {
  background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
}

.history-action .action-icon {
  background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
}

.medical-action .action-icon {
  background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
}

.start-action .action-icon {
  background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);
}

.start-action {
  border: 2px dashed rgba(248, 150, 30, 0.5);
  background: rgba(248, 150, 30, 0.1);
}

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.riwayat-filter-card {
  padding: 12px 14px;
  border: 1px solid #dbe7f3;
  border-radius: 10px;
  background: #f8fbff;
}

.riwayat-filter-card .form-select {
  min-width: 150px;
}

.modal-card-custom {
  width: min(1050px, 100%);
  max-height: 85vh;
  overflow: auto;
  background: #fff;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
}

.modal-header-custom {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: flex-start;
  padding-bottom: 12px;
  border-bottom: 1px solid #eef2f7;
  margin-bottom: 14px;
}

@media (max-width: 768px) {
  .modal-card-custom {
    max-height: 90vh;
  }
}
</style>