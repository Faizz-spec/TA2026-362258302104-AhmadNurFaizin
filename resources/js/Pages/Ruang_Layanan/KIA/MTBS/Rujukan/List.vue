<template>
  <div class="p-2">
    <div class="card border-success shadow-sm">
      <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">Daftar Surat Rujukan</div>

        <Link
          v-if="idPelayanan"
          :href="route('mtbs.rujukan.create', idPelayanan)"
          class="btn btn-info btn-sm text-white fw-bold"
        >
          TAMBAH DATA
        </Link>
      </div>

      <div class="card-body">
        <div class="d-flex justify-content-end mb-2">
          <button class="btn btn-info btn-sm text-white fw-bold" @click="kembaliKePelayanan">
            KEMBALI KE DIAGNOSA
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 50px;">NO</th>
                <th style="width: 90px;">ID RUJUKAN</th>
                <th style="width: 130px;">TANGGAL</th>
                <th>SURAT RUJUKAN</th>
                <th style="width: 170px;">NO SURAT</th>
                <th>RUMAH SAKIT</th>
                <th style="width: 170px;">POLI</th>
                <th style="width: 170px;">TENAGA MEDIS</th>
                <th style="width: 230px;">AKSI</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, index) in rows" :key="item.id">
                <td>{{ index + 1 }}</td>
                <td>{{ item.id }}</td>
                <td>{{ item.tanggal_rujuk || '-' }}</td>
                <td>
                  Surat Rujukan {{ item.NAMA_LGKP || '-' }}
                  <br>
                  <small class="text-muted">No RM: {{ item.NO_MR || '-' }}</small>
                </td>
                <td>{{ item.no_surat || '-' }}</td>
                <td>{{ item.rumah_sakit || '-' }}</td>
                <td>{{ item.poli || '-' }}</td>
                <td>{{ item.dokter_jaga || '-' }}</td>
                <td>
                  <div class="d-flex gap-1 flex-wrap">
                    <Link
                      :href="route('mtbs.rujukan.cetak', item.id)"
                      class="btn btn-primary btn-sm"
                    >
                      Lihat Surat
                    </Link>

                    <Link
                      :href="route('mtbs.rujukan.create', item.kunjungan_id)"
                      class="btn btn-warning btn-sm text-white"
                    >
                      Edit
                    </Link>

                    <button class="btn btn-danger btn-sm" @click="hapus(item.id)">
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="rows.length === 0">
                <td colspan="9" class="text-center text-muted py-4">
                  Belum ada data surat rujukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AppLayouts from '../../../../../Components/Layouts/AppLayouts.vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { computed } from 'vue'

defineOptions({ layout: AppLayouts })

const props = defineProps({
  rows: {
    type: Array,
    default: () => [],
  },
  idPelayanan: {
    type: [String, Number],
    default: null,
  },
  idPoli: {
    type: [String, Number],
    default: null,
  },
  pasienId: {
    type: [String, Number],
    default: null,
  },
})

const rows = computed(() => props.rows || [])

const hapus = async (id) => {
  if (!confirm('Yakin hapus surat rujukan ini?')) return

  try {
    await axios.delete(route('mtbs.rujukan.destroy', id))

    router.visit(route('mtbs.rujukan.index', {
      kunjungan_id: props.idPelayanan,
    }))
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menghapus surat rujukan')
  }
}

const kembaliKePelayanan = () => {
  if (!props.idPelayanan || !props.idPoli || !props.pasienId) {
    window.history.back()
    return
  }

  router.visit(route('ruang-layanan.mtbs.pelayanan', {
    id: props.pasienId,
    idPoli: props.idPoli,
    idPelayanan: props.idPelayanan,
  }))
}
</script>