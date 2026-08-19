  <template>
      <div class="p-2 rounded-4 mt-2">
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
          <!-- Header Section -->
          <div class="d-flex align-items-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle px-2 me-3">
              <i class="bi bi-person-fill text-primary fs-1"></i>
            </div>
            <div>
              <h3 class="mb-1 fw-bold">{{ dataPasien.NAMA_LGKP }} <span class="text-muted">(-)</span></h3>
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
              <p class="mb-0 fw-semibold"> {{ dataPasien.jenis_klmin === 1? 'L': "P" }} {{ dataPasien.umur }} Tahun - {{ dataPasien.umur_bulan }} Bulan - {{ dataPasien.umur_hari }} Hari</p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                <h6 class="mb-0 text-muted">Alamat</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.alamat }}
                    {{ 'RT '+  dataPasien.no_rt }}
                    {{'RW '+ dataPasien.no_rw }}
                    {{ dataPasien.nama_kel }}
                    {{ dataPasien.nama_kec}}
                    {{ dataPasien.nama_kab}}
                    {{ dataPasien.nama_prop }}</p>
            </div>
          </div>
          
          <!-- Column 2 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-heart-pulse-fill text-success me-2"></i>
                <h6 class="mb-0 text-muted">Jenis/Poli</h6>
              </div>
              <p class="mb-0 fw-semibold">Kunjungan Sakit (Umum)</p>
            </div>
          </div>
          
          <!-- Column 3 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-check-fill text-info me-2"></i>
                <h6 class="mb-0 text-muted">Tanggal Kunjungan</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.tglKunjungan }}</p>
            </div>
          </div>
          
          <!-- Column 4 -->
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-credit-card-fill text-warning me-2"></i>
                <h6 class="mb-0 text-muted">No. RM / NIK</h6>
              </div>
              <p class="mb-0 fw-semibold">{{ dataPasien.NO_MR }} / {{ dataPasien.NIK }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item bg-light bg-opacity-50 p-3 rounded-3 h-100">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-credit-card-fill text-warning me-2"></i>
                <h6 class="mb-0 text-muted">No. BPJS/Provider</h6>
              </div>
              <p class="mb-0 fw-semibold">/</p>
            </div>
          </div>
        </div>
      </div>

        <div class="quick-actions mb-2">
        <div class="action-grid">
          <Link href="/surat-keterangan" class="action-card doc-action">
            <div class="action-icon">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="action-label">Surat Keterangan</div>
          </Link>
          <Link
            :href="route('ruang-layanan.kunjungan-online.surat-rujukan', { id: dataPasien[0]?.idLoket || dataPasien.idLoket })"
            class="action-card doc-action"
          >
            <div class="action-icon"><i class="bi bi-send"></i></div>
            <div class="action-label">Surat Rujukan</div>
          </Link>


          <Link :href="route('ruang-layanan.kunjungan-online.riwayat-kesehatan', { id: dataPasien[0]?.idLoket || dataPasien.idLoket })" class="action-card history-action">
            <div class="action-icon">
              <i class="bi bi-clock-history"></i>
            </div>
            <div class="action-label">Riwayat Pasien</div>
          </Link>

          <Link :href="route('ruang-layanan.kunjungan-online.cppt', { id: dataPasien[0]?.idLoket || dataPasien.idLoket })" class="action-card history-action">
            <div class="action-icon">
              <i class="bi bi-file-text"></i>
            </div>
            <div class="action-label">CPPT</div>
          </Link>

          <Link href="/ukk" class="action-card medical-action">
            <div class="action-icon">
              <i class="bi bi-clipboard2-heart"></i>
            </div>
            <div class="action-label">UKK</div>
          </Link>

          <button v-if="!isMelayani" 
                  @click.prevent="mulaiPemeriksaanPasien"
                  class="action-card start-action pulse-animation">
            <div class="action-icon">
              <i class="bi bi-person-check"></i>
            </div>
            <div class="action-label">Mulai Pemeriksaan</div>
          </button>
        </div>
      </div>
      </div>
      <div v-if="isMelayani" class="mt-4 p-2">
        <slot></slot>
      </div>
  </template>

  <script setup>
  import { ref, watch } from 'vue'
  import { router } from '@inertiajs/vue3'
  import { route } from 'ziggy-js'
    import { Link } from '@inertiajs/vue3'

  const props = defineProps({
    isMelayani: Boolean,
    dataPasien : Array,
    dataAnamnesa :Array
  })

  const isMelayani = ref(props.dataAnamnesa !== null)

  const emit = defineEmits(['ubah-melayani', 'ubah-dataAnamnesa']);


  watch(isMelayani, (val) => {
    emit('ubah-melayani', val)
  })

  watch(() => props.isMelayani, (val) => {
    isMelayani.value = val
  })

  function mulaiPemeriksaanPasien(){
    const now = new Date();
    const formattedDateTime = now.toISOString().slice(0,16);
    router.post(route('ruang-layanan-umum.mulai-pemeriksaan-pasien'), {
      idLoket: props.dataPasien.idLoket,
      tglKunjungan : formattedDateTime
    }, {
      preserveScroll: true,
      onSuccess: (page) => {
        isMelayani.value = true; 
        emit('ubah-melayani', false);
        alert('sukses memulai melayani pasien') 
      }
    });
  }
  </script>

  <style>

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
  }</style>
