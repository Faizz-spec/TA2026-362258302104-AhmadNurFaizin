<template>
  <div class="container py-4">
    <div class="row g-4">
      <!-- Card atas: Filter -->
      <div class="col-12">
        <div class="card border-1 border-dark shadow-lg rounded-4">
          <div class="card-body p-4">
            <h4 class="text-primary fw-bold mb-3 d-flex align-items-center">
              <i class="bi bi-funnel-fill me-2"></i> Filter Laporan
            </h4>
            <hr />

            <div class="row g-4">
              <!-- Filter tanggal -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">Tanggal Kunjungan</label>
                <div class="d-flex align-items-center gap-2">
                  <input
                    type="date"
                    v-model="startDate"
                    class="form-control shadow-sm rounded-pill px-3"
                  />
                  <span class="fw-semibold">s/d</span>
                  <input
                    type="date"
                    v-model="endDate"
                    class="form-control shadow-sm rounded-pill px-3"
                  />
                </div>
              </div>

              <!-- Search -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">Cari Pasien</label>
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                  <input
                    type="text"
                    class="form-control border-0"
                    placeholder="Cari pasien..."
                    v-model="searchQuery"
                  />
                  <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card bawah: Tabel -->
      <div class="col-12">
        <div class="card border-1 border-dark shadow-lg rounded-4">
          <div class="card-body p-4">
            <!-- Header + Info -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
              <h4 class="text-primary fw-bold mb-0 d-flex align-items-center">
                <i class="bi bi-table me-2"></i> Data Laporan Pasien
                <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill ms-2">
                  Total: {{ filteredLaporan.length }}
                </span>
              </h4>
            </div>
            <hr />

            <!-- Table -->
            <div class="table-responsive" style="max-height: 65vh; overflow-y: auto">
              <table class="table table-hover table-striped align-middle text-center mb-0">
                <thead class="table-light sticky-top">
                  <tr>
                    <th>No.</th>
                    <th>Tanggal Kunjungan</th>
                    <th>NIK</th>
                    <th>NO RM</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kecamatan</th>
                    <th>Desa</th>
                    <th>Sex</th>
                    <th>Tanggal Lahir</th>
                    <th>Umur</th>
                    <th>Kelompok Umur</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(filter, index) in paginatedLaporan" :key="filter.idLoket">
                    <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                    <td>{{ filter.tglKunjungan }}</td>
                    <td>{{ filter.pasien?.NIK }}</td>
                    <td>{{ filter.pasien?.NO_MR }}</td>
                    <td>{{ filter.pasien?.NAMA_LGKP }}</td>
                    <td>{{ filter.pasien?.ALAMAT }}</td>
                    <td>{{ filter.pasien?.NO_KEC }}</td>
                    <td>{{ filter.pasien?.NO_KEL }}</td>
                    <td>{{ filter.pasien?.JENIS_KLMIN }}</td>
                    <td>{{ filter.pasien?.TGL_LHR }}</td>
                    <td>{{ filter.umur }}</td>
                    <td>{{ filter.pasien?.KEL_UMUR }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination + Action -->
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
              <nav>
                <ul class="pagination pagination-sm mb-0">
                  <li
                    v-for="page in totalPages"
                    :key="page"
                    class="page-item"
                    :class="{ active: currentPage === page }"
                  >
                    <button class="page-link" @click="goToPage(page)">
                      {{ page }}
                    </button>
                  </li>
                </ul>
              </nav>

              <div class="d-flex gap-2">
                <button class="btn btn-html shadow-sm rounded-pill px-3" @click="showHtml">
                  <i class="bi bi-eye me-1"></i> Tampilkan Data HTML
                </button>
                <button class="btn btn-excel shadow-sm rounded-pill px-3" @click="exportExcel">
                  <i class="bi bi-download me-1"></i> Download Excel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- row -->
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
  defineOptions({ layout: AppLayout });
  import { Link } from '@inertiajs/vue3';
  import ExcelJS from 'exceljs';
  import { saveAs } from 'file-saver';

  const exportExcel = async () => {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Laporan Pasien');

    worksheet.columns = [
      { header: 'No', key: 'no', width: 5 },
      { header: 'Tanggal Kunjungan', key: 'tglKunjungan', width: 20 },
      { header: 'NIK', key: 'nik', width: 20 },
      { header: 'NO RM', key: 'noRm', width: 15 },
      { header: 'Nama', key: 'nama', width: 25 },
      { header: 'Alamat', key: 'alamat', width: 30 },
      { header: 'Kecamatan', key: 'kec', width: 20 },
      { header: 'Desa', key: 'desa', width: 20 },
      { header: 'Sex', key: 'sex', width: 10 },
      { header: 'Tanggal Lahir', key: 'tglLahir', width: 15 },
      { header: 'Umur', key: 'umur', width: 10 },
      { header: 'Kelompok Umur', key: 'kelompokUmur', width: 15 },
    ];

    const headerRow = worksheet.getRow(1);
    headerRow.eachCell((cell) => {
      cell.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: '3B82F6' }, // biru (Tailwind blue-500)
      };
      cell.font = {
        color: { argb: 'FFFFFFFF' }, // teks putih
        bold: true,
      };
      cell.alignment = { vertical: 'middle', horizontal: 'center' };
      cell.border = {
        top: { style: 'thin' },
        left: { style: 'thin' },
        bottom: { style: 'thin' },
        right: { style: 'thin' },
      };
    });

    filteredLaporan.value.forEach((laporan, i) => {
      worksheet.addRow({
        no: i + 1,
        tglKunjungan: laporan.tglKunjungan || '-',
        nik: laporan.pasien?.NIK || '-',
        noRm: laporan.pasien?.NO_MR || '-',
        nama: laporan.pasien?.NAMA_LGKP || '-',
        alamat: laporan.pasien?.ALAMAT || '-',
        kec: laporan.pasien?.NO_KEC || '-',
        desa: laporan.pasien?.NO_KEL || '-',
        sex: laporan.pasien?.JENIS_KLMIN || '-',
        tglLahir: laporan.pasien?.TGL_LHR || '-',
        umur: laporan.umur || '-',
        kelompokUmur: laporan.pasien?.KEL_UMUR || '-',
      });
    });

    const buffer = await workbook.xlsx.writeBuffer();
    saveAs(new Blob([buffer]), 'laporan_pasien.xlsx');
  };

  const props = defineProps({
    rekamMedis: Array,
    dataPasien: Array,
  });

  const searchQuery = ref('');
  const startDate = ref('');
  const endDate = ref('');

  const currentPage = ref(1);
  const itemsPerPage = ref(10);

  const filteredLaporan = computed(() => {
    let results = props.rekamMedis;

    if (startDate.value && endDate.value) {
      results = results.filter((item) => {
        const tgl = (item.tglKunjungan || '').slice(0, 10);
        return tgl >= startDate.value && tgl <= endDate.value;
      });
    } else if (startDate.value) {
      results = results.filter((item) => (item.tglKunjungan || '').slice(0, 10) >= startDate.value);
    } else if (endDate.value) {
      results = results.filter((item) => (item.tglKunjungan || '').slice(0, 10) <= endDate.value);
    }

    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase();
      results = results.filter(
        (item) =>
          (item.pasien?.NO_MR || '').toLowerCase().includes(query) ||
          (item.pasien?.NO_KK || '').toLowerCase().includes(query) ||
          (item.pasien?.NAMA_LGKP || '').toLowerCase().includes(query) ||
          (item.pasien?.NIK || '').toLowerCase().includes(query) ||
          (item.pasien?.ALAMAT || '').toLowerCase().includes(query) ||
          (item.nama_kecamatan || '').toLowerCase().includes(query) ||
          (item.nama_kelurahan || '').toLowerCase().includes(query) ||
          (item.noKartu || '').toLowerCase().includes(query) ||
          (item.kdProvider || '').toLowerCase().includes(query)
      );
    }

    return results;
  });

  const paginatedLaporan = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredLaporan.value.slice(start, end);
  });

  const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page;
    }
  };

  const totalPages = computed(() => {
    return Math.ceil(filteredLaporan.value.length / itemsPerPage.value);
  });

  // fungsi tampilkan HTML
  function showHtml() {
  const htmlContent = `
    <html>
      <head>
        <title>Data Laporan Pasien</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; }
          h3 { text-align: center; margin-bottom: 16px; }
          table { border-collapse: collapse; width: 100%; }
          th, td { border: 1px solid black; padding: 8px; text-align: left; }
          th { background: #3b82f6; color: white; }
          tr:nth-child(even) { background: #f9f9f9; }
        </style>
      </head>
      <body>
        <h3>Data Laporan Pasien</h3>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Usia</th>
              <th>Diagnosa</th>
            </tr>
          </thead>
          <tbody>
            ${filteredLaporan.value
              .map(
                (laporan, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td>${laporan.pasien?.NAMA_LGKP || '-'}</td>
                    <td>${laporan.umur || '-'}</td>
                    <td>${laporan.diagnosa || '-'}</td>
                  </tr>
                `
              )
              .join('')}
          </tbody>
        </table>
      </body>
    </html>
  `;

  const newWindow = window.open('', '_blank');
  newWindow.document.write(htmlContent);
  newWindow.document.close();
}

</script>

<style scoped>
  .btn-html {
    background: #3b82f6;
    color: white;
  }
  .btn-excel {
    background: #10b981;
    color: white;
  }
  .table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
  }
</style>
