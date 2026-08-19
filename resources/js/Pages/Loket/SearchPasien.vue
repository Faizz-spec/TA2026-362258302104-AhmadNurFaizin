<template>
  <AppLayouts>
    <div class="loket-page">
      <div class="loket-shell">
        <div class="page-toolbar">
          <div>
            <p class="page-kicker">Loket pendaftaran</p>
            <h1 class="page-title">Pencarian Pasien</h1>
          </div>
          <div class="page-actions">
            <Link :href="route('loket.pasien')" class="btn btn-success">
              <i class="bi bi-person-plus"></i>
              <span>Tambah Pasien</span>
            </Link>
          </div>
        </div>

        <div class="loket-card mb-4">
          <div class="loket-card-header">
            <div>
              <h2><i class="bi bi-search"></i> Cari Data Pasien</h2>
              <p>
                Gunakan fitur ini untuk menemukan pasien dengan cepat sebelum melanjutkan ke loket.
              </p>
            </div>
          </div>

          <div class="loket-card-body">
            <section class="form-section">
              <div class="section-heading">
                <i class="bi bi-search"></i>
                <div>
                  <h3>Pencarian Cepat</h3>
                  <p>Cari pasien berdasarkan MR, NIK, nama, alamat, atau nomor BPJS.</p>
                </div>
              </div>

              <div class="d-flex flex-column flex-md-row align-items-center gap-3 mb-3">
                <div class="search-control flex-grow-1">
                  <i class="bi bi-search"></i>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Cari pasien..."
                    v-model="searchQuery"
                  />
                </div>

                <div class="entries-control">
                  <span>Rows</span>
                  <select v-model="itemsPerPage" class="form-select form-select-sm">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                  </select>
                </div>
              </div>

              <div class="table-responsive loket-table">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>NO MR / NO KK</th>
                      <th>Nama / NIK</th>
                      <th>Alamat / Kecamatan-Desa</th>
                      <th>BPJS / Provider</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-start" v-for="(item, index) in paginatedPasien" :key="item.ID">
                      <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                      <td>
                        {{ item.NO_MR }}<br />
                        <small class="text-muted">{{ item.NO_KK }}</small>
                      </td>
                      <td class="fw-semibold">
                        {{ item.NAMA_LGKP }}<br />
                        <small class="text-muted">{{ item.NIK }}</small>
                      </td>
                      <td>
                        {{ item.ALAMAT }}<br />
                        <small class="fw-semibold"
                          >{{ item.nama_kecamatan }} - {{ item.nama_kelurahan }}</small
                        >
                      </td>
                      <td>
                        {{ item.noKartu }}<br />
                        <small class="text-muted">{{ item.kdProvider }}</small>
                      </td>
                      <td>
                        <div class="action-buttons justify-content-center">
                          <Link
                            :href="route('loket.index')"
                            class="btn btn-icon btn-outline-primary"
                            title="Ke Loket"
                          >
                            <i class="bi bi-eye"></i>
                          </Link>
                          <Link
                            :href="route('loket.edit', item.ID)"
                            class="btn btn-icon btn-outline-success"
                            title="Edit Data"
                          >
                            <i class="bi bi-pencil-square"></i>
                          </Link>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="paginatedPasien.length === 0">
                      <td colspan="6" class="text-muted py-3 text-center">
                        <i class="bi bi-search"></i> Tidak ada data ditemukan
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div
                class="pagination-wrap d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-3"
              >
                <small class="text-muted">
                  Menampilkan
                  {{ filteredPasien.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1 }} -
                  {{
                    filteredPasien.length === 0
                      ? 0
                      : Math.min(currentPage * itemsPerPage, filteredPasien.length)
                  }}
                  dari {{ filteredPasien.length }} data
                </small>

                <nav>
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                      <button class="page-link" @click="prevPage" :disabled="currentPage === 1">
                        «
                      </button>
                    </li>
                    <li
                      v-for="page in totalPages"
                      :key="page"
                      class="page-item"
                      :class="{ active: page === currentPage }"
                    >
                      <button class="page-link" @click="goToPage(page)">
                        {{ page }}
                      </button>
                    </li>
                    <li
                      class="page-item"
                      :class="{ disabled: currentPage === totalPages || totalPages === 0 }"
                    >
                      <button
                        class="page-link"
                        @click="nextPage"
                        :disabled="currentPage === totalPages || totalPages === 0"
                      >
                        »
                      </button>
                    </li>
                  </ul>
                </nav>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '@/Components/Layouts/AppLayouts.vue';
  import { Link } from '@inertiajs/vue3';
  import { ref, computed } from 'vue';
  import { route } from 'ziggy-js';

  const props = defineProps({
    pasien: Object,
  });

  const searchQuery = ref('');
  const currentPage = ref(1);
  const itemsPerPage = ref(5);

  const filteredPasien = computed(() => {
    if (!searchQuery.value) return props.pasien;
    const query = searchQuery.value.toLowerCase();

    return props.pasien.filter(
      (item) =>
        (item.NO_MR || '').toLowerCase().includes(query) ||
        (item.NO_KK || '').toLowerCase().includes(query) ||
        (item.NAMA_LGKP || '').toLowerCase().includes(query) ||
        (item.NIK || '').toLowerCase().includes(query) ||
        (item.ALAMAT || '').toLowerCase().includes(query) ||
        (item.nama_kecamatan || '').toLowerCase().includes(query) ||
        (item.nama_kelurahan || '').toLowerCase().includes(query) ||
        (item.noKartu || '').toLowerCase().includes(query) ||
        (item.kdProvider || '').toLowerCase().includes(query)
    );
  });

  const paginatedPasien = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredPasien.value.slice(start, start + itemsPerPage.value);
  });

  const totalPages = computed(() => Math.ceil(filteredPasien.value.length / itemsPerPage.value));

  const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page;
    }
  };

  const prevPage = () => goToPage(currentPage.value - 1);
  const nextPage = () => goToPage(currentPage.value + 1);
</script>

<style scoped>
  .loket-page {
    min-height: 100%;
    background: #f4f7fb;
    padding: 24px;
  }

  .loket-shell {
    max-width: 1480px;
    margin: 0 auto;
  }

  .page-toolbar,
  .loket-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .page-toolbar {
    margin-bottom: 18px;
  }

  .page-kicker {
    margin: 0 0 4px;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .page-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.65rem;
    font-weight: 750;
  }

  .page-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .loket-card {
    overflow: hidden;
    border: 1px solid #d9e5df;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
  }

  .loket-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e5edf0;
    background: #ffffff;
  }

  .loket-card-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: #0f3d2e;
    font-size: 1.05rem;
    font-weight: 750;
  }

  .loket-card-header p {
    margin: 5px 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }

  .loket-card-body {
    display: grid;
    gap: 18px;
    padding: 22px;
  }

  .form-section {
    padding: 18px;
    border: 1px solid #e3ebef;
    border-radius: 8px;
    background: #fbfdff;
  }

  .form-section:first-child,
  .form-section:last-child {
    grid-column: 1 / -1;
  }

  .section-heading {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
  }

  .section-heading > i {
    display: grid;
    flex: 0 0 36px;
    width: 36px;
    height: 36px;
    place-items: center;
    border-radius: 8px;
    background: #e7f5ef;
    color: #08734f;
    font-size: 1.05rem;
  }

  .section-heading h3 {
    margin: 0;
    color: #1e293b;
    font-size: 0.98rem;
    font-weight: 750;
  }

  .section-heading p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 0.84rem;
  }

  .form-control,
  .form-select {
    border-radius: 8px;
    border: 1px solid #cfd9e3;
    color: #0f172a;
    min-height: 42px;
    transition:
      border-color 0.15s ease-in-out,
      box-shadow 0.15s ease-in-out;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #16a36f;
    box-shadow: 0 0 0 0.2rem rgba(22, 163, 111, 0.14);
  }

  .search-control {
    position: relative;
    width: 100%;
  }

  .search-control i {
    position: absolute;
    left: 14px;
    color: #64748b;
  }

  .search-control .form-control {
    padding-left: 40px;
  }

  .btn {
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 650;
    transition: all 0.15s ease-in-out;
  }

  .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  }

  .entries-control {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #475569;
    font-size: 0.87rem;
    font-weight: 650;
  }

  .entries-control .form-select {
    width: 82px;
    min-height: 36px;
  }

  .table th {
    padding: 14px 12px;
    border-bottom: 1px solid #c9ded6;
    background: #e7f5ef;
    color: #174236;
    font-weight: 600;
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0;
    border-top: none;
    white-space: nowrap;
  }

  .table td {
    padding: 13px 12px;
    color: #1e293b;
    vertical-align: middle;
    font-size: 0.84rem;
    line-height: 1.55;
  }

  .loket-table tbody tr:hover {
    background: #f6fbf8;
  }

  .action-buttons {
    display: inline-flex;
    gap: 6px;
  }

  .btn-icon {
    width: 34px;
    height: 34px;
    padding: 0;
  }

  .pagination-wrap {
    padding: 16px;
    border-top: 1px solid #e5edf0;
    background: #ffffff;
  }

  .pagination .page-link {
    margin: 0 2px;
    border-radius: 7px;
    color: #0f6b4c;
  }

  :deep(.pagination .page-item.active .page-link) {
    border-color: #0f6b4c;
    background: #0f6b4c;
    color: #ffffff;
  }

  @media (max-width: 768px) {
    .loket-page {
      padding: 16px;
    }
  }
</style>
