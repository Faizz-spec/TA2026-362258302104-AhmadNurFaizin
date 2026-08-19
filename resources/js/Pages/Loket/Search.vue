<template>
  <AppLayouts title="Pencarian Pasien">
    <div class="container py-4">
      <div class="card">
        <div class="card-header bg-info text-white fw-bold">Form Pencarian Pasien</div>
        <div class="card-body">
          <form @submit.prevent="submitSearch">
            <div class="row">
              <!-- Kolom Kiri -->
              <div class="col-md-6">
                <div class="mb-2" v-for="field in kolom1" :key="field.key">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          :id="field.key"
                          v-model="form[field.key].enabled"
                        />
                        <label class="form-check-label fw-bold" :for="field.key">
                          {{ field.label }}
                        </label>
                      </div>
                    </div>
                    <div class="col-8">
                      <input
                        v-if="field.type === 'text'"
                        type="text"
                        class="form-control form-control-sm"
                        v-model="form[field.key].value"
                        :disabled="!form[field.key].enabled"
                      />
                    </div>
                  </div>
                </div>

                <div class="mb-2" v-for="field in kolom2" :key="field.key">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          :id="field.key"
                          v-model="form[field.key].enabled"
                        />
                        <label class="form-check-label fw-bold" :for="field.key">
                          {{ field.label }}
                        </label>
                      </div>
                    </div>
                    <div class="col-8">
                      <template v-if="field.type === 'text'">
                        <input
                          type="text"
                          class="form-control form-control-sm"
                          v-model="form[field.key].value"
                          :disabled="!form[field.key].enabled"
                        />
                      </template>
                      <template v-else-if="field.type === 'select'">
                        <select
                          class="form-select form-select-sm"
                          v-model="form[field.key].value"
                          :disabled="!form[field.key].enabled"
                        >
                          <option value="">- Pilih -</option>
                          <option value="Laki-laki">Laki-laki</option>
                          <option value="Perempuan">Perempuan</option>
                        </select>
                      </template>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Kolom Kanan -->
              <div class="col-md-6">
                <div class="mb-2" v-for="field in kolom3" :key="field.key">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          :id="field.key"
                          v-model="form[field.key].enabled"
                        />
                        <label class="form-check-label fw-bold" :for="field.key">
                          {{ field.label }}
                        </label>
                      </div>
                    </div>
                    <div class="col-8">
                      <template v-if="field.type === 'text'">
                        <input
                          type="text"
                          class="form-control form-control-sm"
                          v-model="form[field.key].value"
                          :disabled="!form[field.key].enabled"
                        />
                      </template>
                      <template v-else-if="field.type === 'select'">
                        <select
                          class="form-select form-select-sm"
                          v-model="form[field.key].value"
                          :disabled="!form[field.key].enabled"
                        >
                          <option value="">- Pilih -</option>
                          <option v-for="kec in kecamatanList" :value="kec.NO_KEC">
                            {{ kec.NAMA_KEC }}
                          </option>
                        </select>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr />
            <div class="d-flex justify-content-between">
              <button class="btn btn-primary btn-sm" type="submit">
                <i class="bi bi-search me-1"></i> CARI PASIEN
              </button>
              <Link :href="route('loket.pasien')" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> TAMBAH PASIEN BARU
              </Link>
            </div>
          </form>

          <!-- Results Table -->
          <div class="mt-4" v-if="results.length > 0">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>NO. MR</th>
                    <th>NIK</th>
                    <th>NAMA</th>
                    <th>JENIS KELAMIN</th>
                    <th>ALAMAT</th>
                    <th>KECAMATAN</th>
                    <th>KELURAHAN</th>
                    <th>ACTION</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pasien in results" :key="pasien.ID">
                    <td>{{ pasien.NO_MR }}</td>
                    <td>{{ pasien.NIK }}</td>
                    <td>{{ pasien.NAMA_LGKP }}</td>
                    <td>{{ pasien.JENIS_KLMIN === 1 ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ pasien.ALAMAT }}</td>
                    <td>{{ pasien.kecamatan?.NAMA_KEC }}</td>
                    <td>{{ pasien.kelurahan?.NAMA_KEL }}</td>
                    <td>
                      <Link
                        :href="route('loket.index', { pasienId: pasien.ID })"
                        class="btn btn-sm btn-primary"
                      >
                        Daftarkan
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayouts>
</template>

<script setup>
  import AppLayouts from '@/Components/Layouts/AppLayouts.vue';
  import { Link, router } from '@inertiajs/vue3';
  import { reactive, defineProps } from 'vue';

  const props = defineProps({
    results: {
      type: Array,
      default: () => [],
    },
    searchParams: {
      type: Object,
      default: () => ({}),
    },
    kecamatanList: {
      type: Array,
      required: true,
    },
  });

  const form = reactive({
    nama: { enabled: false, value: props.searchParams.nama || '' },
    nik: { enabled: false, value: props.searchParams.nik || '' },
    no_mr: { enabled: false, value: props.searchParams.no_mr || '' },
    no_bpjs: { enabled: false, value: props.searchParams.no_bpjs || '' },
    jenis_kelamin: { enabled: false, value: props.searchParams.jenis_kelamin || '' },
    kecamatan: { enabled: false, value: props.searchParams.kecamatan || '' },
    alamat: { enabled: false, value: props.searchParams.alamat || '' },
  });

  const kolom1 = [
    { key: 'nama', label: 'Nama', type: 'text' },
    { key: 'nik', label: 'NIK', type: 'text' },
    { key: 'no_mr', label: 'NO MR', type: 'text' },
  ];
  const kolom2 = [
    { key: 'no_bpjs', label: 'NO BPJS', type: 'text' },
    { key: 'jenis_kelamin', label: 'Jenis Kelamin', type: 'select' },
  ];
  const kolom3 = [
    { key: 'kecamatan', label: 'Kecamatan', type: 'select' },
    { key: 'alamat', label: 'ALAMAT', type: 'text' },
  ];

  function submitSearch() {
    const searchData = {};

    Object.entries(form).forEach(([key, field]) => {
      if (field.enabled && field.value) {
        searchData[key] = field.value;
      }
    });

    router.get(route('loket.search'), searchData);
  }
</script>
