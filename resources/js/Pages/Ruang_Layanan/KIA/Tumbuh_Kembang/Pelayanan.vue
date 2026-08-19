<template>
  <div class="p-2 rounded-4 mt-2">
    <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
      <!-- Header Section -->
      <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle px-2 me-3">
          <i class="bi bi-person-fill text-primary fs-1"></i>
        </div>
        <div>
          <h3
            v-for="pasien in DataPasien"
            :key="pasien.ID"
            :value="pasien.NAMA_LGKP"
            class="mb-1 fw-bold"
          >
            {{ pasien.NAMA_LGKP }} <span class="text-muted">(-)</span>
          </h3>
        </div>
      </div>

      <div class="row g-3">
        <!-- Column 1 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar3 text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Jk / Umur</h6>
            </div>
            <p v-for="pasien in DataPasien" :key="pasien.ID" class="mb-0 fw-semibold">
              L {{ pasien.umur }} Tahun - {{ pasien.umur_bulan }} Bulan -
              {{ pasien.umur_hari }} Hari
            </p>
          </div>
        </div>

        <!-- Column 2 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-geo-alt-fill text-primary me-2"></i>
              <h6 class="mb-0 text-muted">Alamat</h6>
            </div>
            <p v-for="address in DataPasien" :key="address.ID" class="mb-0 fw-semibold">
              {{ address.alamat }} RT {{ address.no_rt }} RW {{ address.no_rw }} Kel.
              {{ address.nama_kel }} Kec. {{ address.nama_kec }} <br />{{ address.nama_kab }} Prov.
              {{ address.nama_prop }}
            </p>
          </div>
        </div>

        <!-- Column 3 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-heart-pulse-fill text-success me-2"></i>
              <h6 class="mb-0 text-muted">Jenis/Poli</h6>
            </div>
            <p v-for="poli in DataPasien" :key="poli.ID" class="mb-0 fw-semibold">
              Kunjungan Sakit ({{ poli.nmPoli }})
            </p>
          </div>
        </div>

        <!-- Column 4 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar-check-fill text-info me-2"></i>
              <h6 class="mb-0 text-muted">Tanggal Kunjungan</h6>
            </div>
            <p v-for="tanggal in DataPasien" :key="tanggal.ID" class="mb-0 fw-semibold">
              {{ tanggal.tglKunjungan }}
            </p>
          </div>
        </div>

        <!-- Column 5 -->
        <div class="col-md-4">
          <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-credit-card-fill text-warning me-2"></i>
              <h6 class="mb-0 text-muted">No. RM / NIK</h6>
            </div>
            <p v-for="no in DataPasien" :key="no.ID" class="mb-0 fw-semibold">
              {{ no.NO_MR }} / {{ no.NIK }}
            </p>
          </div>
        </div>

        <!-- Column 6 -->
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
        <a href="#" class="action-card doc-action">
          <div class="action-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="action-label">Surat Keterangan</div>
        </a>

        <a href="#" class="action-card doc-action">
          <div class="action-icon">
            <i class="bi bi-send"></i>
          </div>
          <div class="action-label">Surat Rujukan</div>
        </a>

        <a href="#" class="action-card history-action">
          <div class="action-icon">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="action-label">Riwayat Pasien</div>
        </a>

        <a href="#" class="action-card medical-action">
          <div class="action-icon">
            <i class="bi bi-file-text"></i>
          </div>
          <div class="action-label">CPPT</div>
        </a>
      </div>
    </div>
    <div class="mt-4">
      <FormTumbuhKembang
        :DataPasien="props.DataPasien"
        :diagnosa="props.diagnosa"
        :AlergiMakanan="props.AlergiMakanan"
        :AlergiObat="props.AlergiObat"
        :diagnosa-keperawatan="props.diagnosaKeperawatan"
        :tindakan="props.tindakan"
        :DataDiagnosa="props.DataDiagnosa"
      />
    </div>
  </div>
</template>

<script setup>
  import AppLayouts from '../../../../Components/Layouts/AppLayouts.vue';
  import FormTumbuhKembang from '../../../../Components/Layouts/RuangLayanan/KIA/Tumbuh_Kembang/Index.vue';
  import { ref } from 'vue';
  defineOptions({ layout: AppLayouts });

  const activeForm = ref(null);

  const props = defineProps({
    DataPasien: Array,
    diagnosa: Array,
    tindakan: Array,
    diagnosaKeperawatan: Array,
    AlergiMakanan: Array,
    AlergiObat: Array,
    DataDiagnosa: Array,
  });
  const toggleForm = (form) => {
    activeForm.value = activeForm.value === form ? null : form;
  };

  // const props = defineProps({
  //   DataPasien: Object,
  // });

  // const form = useForm({
  //   nama: props.pasien.NAMA_LGKP ?? '',
  //   nik: props.pasien.NIK ?? '',
  //   provinsi: props.pasien.NO_PROP ?? '',
  //   kabupaten: props.pasien.NO_KAB ?? '',
  //   kecamatan: props.pasien.NO_KEC ?? '',
  //   kelurahan: props.pasien.NO_KEL ?? '',
  //   alamat: props.pasien.ALAMAT ?? '',
  //   agama: props.pasien.AGAMA ?? '',
  //   provider: props.pasien.kdProvider ?? '',
  //   tanggal_lahir: props.pasien.TGL_LHR ?? '',
  //   tempat_lahir: props.pasien.TMPT_LHR ?? '',
  //   jenis_kelamin: props.pasien.JENIS_KLMIN ?? '',
  //   no_kk: props.pasien.NO_KK ?? '',
  //   no_kartu: props.pasien.noKartu ?? '',
  //   no_rt: props.pasien.NO_RT ?? '',
  //   no_rw: props.pasien.NO_RW ?? '',
  //   phone: props.pasien.PHONE ?? '',
  //   jenis_pkrjn: props.pasien.JENIS_PKRJN ?? '',
  //   hub_family: props.pasien.STAT_HBKEL ?? '',
  //   ihs_pasien: props.pasien.IHS_NUMBER ?? '',
  // });

  // Template statis — tidak ada interaksi atau data binding
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

  .pulse-animation {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(248, 150, 30, 0.4);
    }
    70% {
      box-shadow: 0 0 0 10px rgba(248, 150, 30, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(248, 150, 30, 0);
    }
  }
</style>
