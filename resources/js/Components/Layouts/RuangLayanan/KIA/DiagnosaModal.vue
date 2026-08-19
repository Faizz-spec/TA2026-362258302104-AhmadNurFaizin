<template>
  <div
    v-if="show"
    class="modal fade show d-block"
    style="background: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <h5>Pilih Diagnosa</h5>
          <button class="btn-close" @click="closeModal"></button>
        </div>

        <div class="modal-body">
          <!-- Search -->
          <input
            type="text"
            v-model="keyword"
            class="form-control mb-3"
            placeholder="Cari diagnosa..."
          />

          <!-- Tabel -->
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>KODE</th>
                <th>NAMA</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in paginatedDiagnosa" :key="item.id">
                <td>{{ item.kdDiag }}</td>
                <td>{{ item.nmDiag }}</td>
                <td>
                  <button class="btn btn-info btn-sm" @click="pilihDiagnosa(item)">Pilih</button>
                </td>
              </tr>
              <tr v-if="paginatedDiagnosa.length === 0">
                <td colspan="3" class="text-center text-muted">Tidak ada data</td>
              </tr>
            </tbody>
          </table>

          <nav class="d-flex justify-content-between align-items-center mt-2">
            <p class="mb-0 text-muted small">
              Menampilkan {{ startItem }} - {{ endItem }} dari {{ filteredDiagnosa.length }} data
            </p>

            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="prevPage">&laquo;</button>
              </li>
              <li
                v-for="(page, index) in visiblePages"
                :key="index"
                class="page-item"
                :class="{ active: page === currentPage, disabled: page === '...' }"
              >
                <button v-if="page !== '...'" class="page-link" @click="goToPage(page)">
                  {{ page }}
                </button>
                <span v-else class="page-link">...</span>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="nextPage">&raquo;</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  diagnosa: Array,
});

const emit = defineEmits(['close', 'select']);

const keyword = ref('');
const currentPage = ref(1);
const perPage = 10;

const filteredDiagnosa = computed(() => {
  if (!keyword.value) return props.diagnosa;
  const lower = keyword.value.toLowerCase();
  return props.diagnosa.filter(
    (d) => d.kdDiag.toLowerCase().includes(lower) || d.nmDiag.toLowerCase().includes(lower)
  );
});

const totalPages = computed(() => Math.ceil(filteredDiagnosa.value.length / perPage));
const paginatedDiagnosa = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  const end = start + perPage;
  return filteredDiagnosa.value.slice(start, end);
});

const visiblePages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  const maxVisible = 5;
  const pages = [];

  if (total <= maxVisible) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total);
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total);
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total);
    }
  }
  return pages;
});

const startItem = computed(() =>
  filteredDiagnosa.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1
);
const endItem = computed(() => Math.min(currentPage.value * perPage, filteredDiagnosa.value.length));

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) currentPage.value = page;
};
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const closeModal = () => emit('close');
const pilihDiagnosa = (item) => emit('select', item);
</script>
