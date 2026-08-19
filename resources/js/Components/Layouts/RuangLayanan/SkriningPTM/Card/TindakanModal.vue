<template>
  <div
    v-if="show"
    class="modal fade show"
    tabindex="-1"
    style="display: block; background: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <h5>Pilih Tindakan</h5>
          <button class="btn-close" @click="$emit('close')"></button>
        </div>

        <div class="modal-body">
          <!-- Input Pencarian -->
          <input
            type="text"
            v-model="keyword"
            class="form-control mb-3"
            placeholder="Cari tindakan..."
          />

          <!-- Tabel -->
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>KODE</th>
                <th>NAMA</th>
                <th>NAMA (IND)</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in paginatedTindakan" :key="item.idTindakan">
                <td>{{ item.kdTindakan }}</td>
                <td>{{ item.nmTindakan }}</td>
                <td>{{ item.nmTindakanInd }}</td>
                <td>
                  <button
                    class="btn btn-info btn-sm"
                    @click="$emit('select', item)"
                  >
                    Pilih
                  </button>
                </td>
              </tr>
              <tr v-if="paginatedTindakan.length === 0">
                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <nav class="d-flex justify-content-between align-items-center mt-2">
            <p class="mb-0 text-muted small">
              Menampilkan {{ startItem }} - {{ endItem }} dari {{ filteredTindakan.length }} data
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
import { ref, computed, toRaw, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  tindakan: Array,
});

const emit = defineEmits(['close', 'select']);

const keyword = ref('');
const currentPage = ref(1);
const perPage = 10;

// Filter
const filteredTindakan = computed(() => {
  if (!keyword.value) return props.tindakan || [];
  const lower = keyword.value.toLowerCase();
  return props.tindakan
    .map((item) => toRaw(item))
    .filter(
      (d) =>
        (d.kdTindakan ?? '').toLowerCase().includes(lower) ||
        (d.nmTindakan ?? '').toLowerCase().includes(lower) ||
        (d.nmTindakanInd ?? '').toLowerCase().includes(lower)
    );
});

const totalPages = computed(() => Math.ceil(filteredTindakan.value.length / perPage));

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

const paginatedTindakan = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  const end = start + perPage;
  return filteredTindakan.value.slice(start, end);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) currentPage.value = page;
};
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const startItem = computed(() =>
  filteredTindakan.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1
);
const endItem = computed(() =>
  Math.min(currentPage.value * perPage, filteredTindakan.value.length)
);

// Reset page tiap kali keyword berubah
watch(keyword, () => (currentPage.value = 1));
</script>

<style scoped>
.modal-dialog {
  margin-top: 10vh;
}
</style>
