<template>
  <AppLayouts>
    <FormAnamnesa
      title="Skrining Penyakit Tidak Menular"
      :backRoute="backRoute"
      :pelayananRoute="pelayananRoute"
      :unitList="unitList"
      :rows="rows"
      :loadingTable="loadingTable"
      v-model:tanggal_kunjungan="tanggal_kunjungan"
      v-model:nama_unit="nama_unit"
      v-model:search="search"
      v-model:perPage="perPage"
      :pagination="pagination"
      @change-page="currentPage = $event"
    />
  </AppLayouts>
</template>

<script setup>
  import { computed, ref, watch, onMounted } from 'vue';
  import { route } from 'ziggy-js';
  import { usePage, router } from '@inertiajs/vue3';

  import FormAnamnesa from '../../../Components/Layouts/RuangLayanan/SkriningPTM/UnitPelayanan.vue';
  import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';

  const page = usePage();

  const loadingTable = ref(false);
  const tanggal_kunjungan = ref('');
  const nama_unit = ref('');
  const search = ref('');
  const perPage = ref(1);
  const currentPage = ref(1);

  const backRoute = route('ruang-layanan.poli-kluster', {
    kluster: 3,
  });

  const pelayananRoute = 'ruang-layanan.skrining-ptm';

  const rows = computed(() => page.props.DataPasien.data);
  const pagination = computed(() => page.props.DataPasien);

  const unitList = computed(() => {
    return page.props.DataUnit.map((item) => {
      return {
        nama_unit: item.nama_unit,
        kategori: item.data_master_unit.kategori,
      };
    });
  });

  // default tanggal hari ini
  onMounted(() => {
    tanggal_kunjungan.value = new Date().toISOString().split('T')[0];
  });

  watch(
    [tanggal_kunjungan, nama_unit, search, perPage, currentPage],
    ([tanggal, unit, keyword, limit, page]) => {
      loadingTable.value = true;

      router.get(
        route('ruang-layanan.ptm'),
        {
          tanggal_kunjungan: tanggal,
          nama_unit: unit,
          search: keyword,
          per_page: limit,
          page: page,
        },
        {
          preserveState: true,
          preserveScroll: true,
          replace: true,
          async: true,

          showGlobalLoader: false,

          onStart: () => {
            loadingTable.value = true;
          },

          // hanya refresh data pasien
          only: ['DataPasien'],

          onFinish: () => {
            loadingTable.value = false;
          },
        }
      );
    }
  );
</script>
