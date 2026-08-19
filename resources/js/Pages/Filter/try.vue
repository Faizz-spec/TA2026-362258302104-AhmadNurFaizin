<template>
<AppLayouts>
<table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>KODE</th>
                  <th>NAMA</th>
                  <th>KETERANGAN</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item) in diagnosa" :key="item.id">
                  <td>{{ item.kdDiag }}</td>
                  <td>{{ item.nmDiag }}</td>
                  <td></td>
                  <td>
                    <button class="btn btn-info btn-sm" @click="pilihDiagnosa(item)">Pilih</button>
                  </td>
                </tr>
              </tbody>
            </table>

            </AppLayouts>
</template>
<script setup>
  import AppLayouts from '../../Components/Layouts/AppLayouts.vue';
  const props = defineProps ({
    diagnosa: Array,
  });
  
</script>