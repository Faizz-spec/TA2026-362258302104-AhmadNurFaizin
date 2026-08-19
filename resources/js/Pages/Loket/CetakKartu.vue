<template>
  <AppLayout title="Cetak Kartu Pasien">
    <div class="container-fluid py-3">
      <!-- Tombol Aksi -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Kartu Pasien</h4>
        <div class="d-flex gap-2">
          <button @click="cetakKartu" class="btn btn-success">
            <i class="bi bi-printer me-1"></i> Cetak Kartu
          </button>
          <Link :href="route('loket.index')" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </Link>
        </div>
      </div>

      <!-- Area Cetak -->
      <div
        id="kartu-wrapper"
        class="mx-auto"
        style="width: 360px; height: 245px; border: 1px solid #000"
      >
        <!-- Header -->
        <div
          id="header"
          class="border-bottom border-2 d-flex align-items-center p-2"
          style="height: 50px"
        >
          <div class="logo me-2">
            <img src="../../../../public/images/logo.png" height="40" width="30" alt="Logo" />
          </div>
          <div id="title" class="text-center flex-grow-1">
            <div class="main-title fw-bold" style="font-size: 10px; line-height: 1.2">
              PEMERINTAH KABUPATEN BANYUWANGI<br />
              {{ alamat?.nama_unit || 'PUSKESMAS' }}<br />
            </div>
            <div class="small-title" style="font-size: 7px; line-height: 1.1">
              {{ alamat?.alamat || '' }} , Telp : {{ alamat?.telp || '' }}
            </div>
          </div>
        </div>

        <!-- Content -->
        <div id="content" class="p-2">
          <table class="table table-borderless" style="font-size: 11px; width: 100%">
            <tbody>
              <tr>
                <td width="75" valign="top" class="fw-bold">Nomor / NIK</td>
                <td width="10" valign="top">:</td>
                <td colspan="7">{{ pasien.NO_MR }}&nbsp;/&nbsp;{{ pasien.NIK }}</td>
              </tr>
              <tr>
                <td width="75" valign="top" class="fw-bold">Nomor BPJS</td>
                <td width="10" valign="top">:</td>
                <td colspan="7">{{ pasien.noKartu || '-' }}</td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Nama</td>
                <td valign="top">:</td>
                <td colspan="7">{{ pasien.NAMA_LGKP }}</td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Nama KK</td>
                <td valign="top">:</td>
                <td colspan="7">{{ pasien.NAMA_LGKP_AYAH || '-' }}</td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Tgl Lahir</td>
                <td valign="top">:</td>
                <td colspan="7">{{ formatTanggal(pasien.TGL_LHR) }}</td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Alamat</td>
                <td valign="top">:</td>
                <td colspan="7">
                  {{ pasien.ALAMAT }}
                  <span v-if="pasien.NO_RT || pasien.NO_RW">
                    RT {{ pasien.NO_RT || '?' }} RW {{ pasien.NO_RW || '?' }}
                  </span>
                </td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Desa</td>
                <td valign="top">:</td>
                <td colspan="7">
                  {{ getKelurahanName }}
                </td>
              </tr>
              <tr>
                <td valign="top" class="fw-bold">Kecamatan</td>
                <td valign="top">:</td>
                <td colspan="7">
                  {{ getKecamatanName }}
                </td>
              </tr>
              <tr>
                <td colspan="9" class="text-center py-1">
                  <div v-if="!pasien.NO_MR" class="text-danger small">No. MR tidak tersedia</div>
                  <template v-else>
                    <img
                      :src="barcodeUrl"
                      alt="Barcode"
                      style="max-width: 200px; height: 50px"
                      @error="handleBarcodeError"
                      v-if="!barcodeError"
                    />
                    <div v-if="barcodeError" class="text-muted small mt-1">
                      Barcode tidak dapat dimuat untuk: {{ pasien.NO_MR }}
                    </div>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Debug Info (Hanya untuk development) -->
      <div v-if="isDevelopment" class="mt-3 p-3 bg-warning rounded">
        <h6>Debug Information:</h6>
        <pre>{{ debugInfo }}</pre>
      </div>

      <!-- Info Pasien -->
      <div class="mt-3 p-3 bg-light rounded">
        <h6>Informasi Pasien:</h6>
        <div class="row">
          <div class="col-md-6">
            <strong>No. MR:</strong> {{ pasien.NO_MR }}<br />
            <strong>NIK:</strong> {{ pasien.NIK }}<br />
            <strong>Nama:</strong> {{ pasien.NAMA_LGKP }}
          </div>
          <div class="col-md-6">
            <strong>Tanggal Lahir:</strong> {{ formatTanggalLengkap(pasien.TGL_LHR) }}<br />
            <strong>Jenis Kelamin:</strong>
            {{ pasien.JENIS_KLMIN === 'L' ? 'Laki-laki' : 'Perempuan' }}<br />
            <strong>Alamat Lengkap:</strong> {{ getAlamatLengkap }}
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  import { Link } from '@inertiajs/vue3';
  import { computed, ref } from 'vue';

  const props = defineProps({
    pasien: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    alamat: {
      type: Object,
      default: () => ({}),
    },
    kecamatan: {
      type: Object,
      default: () => ({}),
    },
    kelurahan: {
      type: Object,
      default: () => ({}),
    },
  });

  const barcodeError = ref(false);

  // ✅ PERBAIKAN: Tambahkan computed properties yang missing
  const isDevelopment = ref(process.env.NODE_ENV === 'development');

  // Computed property untuk nama kelurahan
  const getKelurahanName = computed(() => {
    return props.pasien.nama_kelurahan || props.pasien.NO_KEL || '-';
  });

  // Computed property untuk nama kecamatan
  const getKecamatanName = computed(() => {
    return props.pasien.nama_kecamatan || props.pasien.NO_KEC || '-';
  });

  // Computed property untuk URL barcode
  const barcodeUrl = computed(() => {
    if (!props.pasien.NO_MR) {
      return '';
    }
    return route('loket.gen_barcode', { NO_MR: props.pasien.NO_MR });
  });

  // Computed property untuk alamat lengkap
  const getAlamatLengkap = computed(() => {
    const { pasien } = props;
    const parts = [];

    if (pasien.ALAMAT) parts.push(pasien.ALAMAT);
    if (pasien.NO_RT || pasien.NO_RW)
      parts.push(`RT ${pasien.NO_RT || '?'}/RW ${pasien.NO_RW || '?'}`);
    if (getKelurahanName.value !== '-') parts.push(getKelurahanName.value);
    if (getKecamatanName.value !== '-') parts.push(getKecamatanName.value);

    return parts.join(', ');
  });

  // Computed property untuk debug info
  const debugInfo = computed(() => {
    return {
      pasien: {
        ID: props.pasien.ID,
        NO_MR: props.pasien.NO_MR,
        NO_KEC: props.pasien.NO_KEC,
        NO_KEL: props.pasien.NO_KEL,
        NAMA_KEC: props.pasien.NAMA_KEC,
        NAMA_KEL: props.pasien.NAMA_KEL,
      },
      relasi: {
        kecamatan: props.pasien.kecamatan,
        kelurahan: props.pasien.kelurahan,
      },
      props: {
        kecamatan: props.kecamatan,
        kelurahan: props.kelurahan,
      },
      computed: {
        getKecamatanName: getKecamatanName.value,
        getKelurahanName: getKelurahanName.value,
      },
    };
  });

  // Method untuk handle error barcode
  const handleBarcodeError = () => {
    barcodeError.value = true;
    console.error('Gagal memuat barcode untuk NO_MR:', props.pasien.NO_MR);
  };

  // Method untuk format tanggal
  const formatTanggal = (tanggal) => {
    if (!tanggal) return '-';
    try {
      const date = new Date(tanggal);
      return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      });
    } catch (error) {
      console.error('Error formatting date:', error);
      return '-';
    }
  };

  // Method untuk format tanggal lengkap
  const formatTanggalLengkap = (tanggal) => {
    if (!tanggal) return '-';
    try {
      const date = new Date(tanggal);
      return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    } catch (error) {
      console.error('Error formatting date:', error);
      return '-';
    }
  };

  // Method untuk cetak kartu
  const cetakKartu = () => {
    const printContent = document.getElementById('kartu-wrapper').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = `
    <!DOCTYPE html>
    <html>
      <head>
        <title>Kartu Pasien - ${props.pasien.NAMA_LGKP}</title>
        <style>
          body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 10px;
            font-size: 10px;
          }
          #kartu-wrapper { 
            width: 360px; 
            height: 245px; 
            border: 1px solid #000;
            margin: 0 auto;
          }
          #header { 
            border-bottom: 2px solid #000;
            width: 100%;
            height: 50px;
            padding: 4px 3px 3px 3px;
            display: flex;
            align-items: center;
          }
          .logo { 
            float: left; 
            margin-right: 10px; 
          }
          #title { 
            text-align: center;
            flex-grow: 1;
          }
          .main-title { 
            margin-bottom: 5px; 
            font-size: 10px; 
            font-weight: bold;
            line-height: 1.2;
          }
          .small-title { 
            font-size: 7px; 
            line-height: 1.1;
          }
          #content { 
            padding: 3px; 
          }
          table { 
            font-size: 11px; 
            width: 100%;
          }
          @media print {
            body { margin: 0; padding: 0; }
            #kartu-wrapper { border: 1px solid #000; }
            .no-print { display: none !important; }
          }
        </style>
      </head>
      <body>
        <div id="kartu-wrapper">${printContent}</div>
      </body>
    </html>
  `;

    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
  };

  // Log debug info saat komponen dimuat
  console.log('CetakKartu Component Loaded:', debugInfo.value);
</script>

<style scoped>
  #kartu-wrapper {
    font-family: Arial, sans-serif;
  }

  .table td {
    padding: 1px 3px;
    border: none;
  }

  .main-title {
    font-weight: bold;
  }

  /* Style untuk print */
  @media print {
    .container-fluid {
      padding: 0 !important;
      margin: 0 !important;
    }

    #kartu-wrapper {
      border: 1px solid #000 !important;
      margin: 0 auto !important;
    }

    .btn,
    .bg-light,
    .bg-warning {
      display: none !important;
    }
  }

  /* Debug info hanya tampil di development */
  .bg-warning {
    display: none;
  }
</style>
