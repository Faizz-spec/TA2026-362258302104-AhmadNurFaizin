<template>
  <div
    v-if="show"
    class="modal fade show d-block"
    tabindex="-1"
    style="background-color: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button class="btn-close" @click="$emit('close')"></button>
        </div>

        <div class="modal-body">
          <input
            type="text"
            class="form-control mb-3"
            placeholder="Search..."
            v-model="searchTerm"
          />

          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>KODE</th>
                <th>NAMA TINDAKAN</th>
                <th>IND (TRANSLATE)</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredItems" :key="index">
                <td>{{ item.kode }}</td>
                <td>{{ item.nama }}</td>
                <td>{{ item.translate }}</td>
                <td>
                  <button class="btn btn-sm btn-info text-white" @click="selectItem(item)">
                    Pilih
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <div>
            <small> Menampilkan {{ filteredItems.length }} dari {{ items.length }} data </small>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="$emit('close')">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed, defineProps, defineEmits, ref } from 'vue';

  const props = defineProps({
    show: Boolean,
    title: String,
    items: Array,
  });

  const emits = defineEmits(['close', 'select']);

  const searchTerm = ref('');

  const filteredItems = computed(() => {
    if (!searchTerm.value) return props.items;
    return props.items.filter((item) =>
      Object.values(item).some((val) =>
        String(val).toLowerCase().includes(searchTerm.value.toLowerCase())
      )
    );
  });

  const selectItem = (item) => {
    emits('select', item);
  };
</script>
