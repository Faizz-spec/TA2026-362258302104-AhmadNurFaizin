<template>
  <div class="card rounded-4 border-0 shadow-sm m-4">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0 py-3"
      style="background: linear-gradient(135deg, #3b82f6, #10b981);">
      <h2 class="fs-6 mb-0 text-white">Daftar Surat Rujukan</h2>

      <div class="d-flex align-items-center gap-2">
        <Link :href="route('ruang-layanan.surat-rujuk-form', {
          idPoli: props.idPoli,
          idPelayanan: props.idPelayanan
        })" class="btn btn-light btn-sm border-0 fw-semibold d-flex align-items-center gap-2"
          style="background: rgba(255,255,255,.2); color: #fff;">
        <i class="bi bi-plus-lg"></i> TAMBAH DATA
        </Link>

        <Link v-if="backRoute" :href="backRoute" class="btn btn-info btn-sm fw-semibold d-flex align-items-center gap-2"
          style="background: rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.35);">
        <i class="bi bi-arrow-left"></i> KEMBALI KE DIAGNOSA
        </Link>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <div class="table-responsive rounded-3 border my-4">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead class="table-primary">
            <tr class="text-uppercase small text-muted">
              <th class="text-center" style="width: 56px;">No</th>
              <th class="text-center">Tanggal</th>
              <th class="text-center">Surat Keterangan</th>
              <th class="text-center">No Surat</th>
              <th class="text-center">Rumah Sakit</th>
              <th class="text-center">Poli</th>
              <th class="text-center">Tenaga medis</th>
              <th class="text-center" style="width: 160px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.suratRujuks">
              <td class="text-center">{{ index + 1 }}</td>
              <td class="fw-semibold">{{ item.tgl_rujuk }}</td>
              <td class="text-center">SURAT RUJUKAN</td>
              <td class="text-center">{{ item.no_surat }}</td>
              <td class="text-center">{{ item.provider.nmProvider }}</td>
              <td class="text-center">{{ item.poli_rujukan.nmPoli }}</td>
              <td class="text-center">{{ item.tenaga_medis.nmDokter }}</td>
              <td class="text-center">
                <div class="d-flex justify-content-evenly">
                  <Link :href="route('ruang-layanan.surat-rujuk-form-edit', {
                    idPoli: props.idPoli,
                    idPelayanan: props.idPelayanan,
                    idSurat: item.id_surat_rujukan
                  })" class="btn btn-primary" title="Lihat / Edit">
                  <i class="bi bi-pencil-square"></i>
                  </Link>
                  <Link :href="route('ruang-layanan.cetak-rujukan', {
                    idSurat: item.id_surat_rujukan
                  })" type="button" class="btn btn-success" title="Cetak">
                  <i class="bi bi-printer"></i>
                  </Link>
                  <button @click="onDelete(item.id_surat_rujukan)" type="button" class="btn btn-danger"
                    title="Hapus">
                    <i class="bi bi-trash3"></i>
                  </button>

                </div>
              </td>

            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer kecil / keterangan -->
      <div class="d-flex justify-content-between align-items-center mt-3 small text-muted">
        <div>Menampilkan data</div>
        <!-- tempatkan pagination kalau perlu -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  idPoli: String,
  idPelayanan: String,
  suratRujuks: Array
})
const emit = defineEmits(['suketList-update']);

console.log('suket lisnya', props.suratRujuks)

function onDelete(id_surat_rujukan) {
  alert('Yakin menghapus data ?')
  router.post(route('ruang-layanan.hapus-surat-rujukan', {
    idSurat: id_surat_rujukan
  }), {},
    {
      onSuccess: () => {
        router.visit(route('ruang-layanan.surat-rujuk', {
          idPoli: props.idPoli,
          idPelayanan: props.idPelayanan
        }));
      }
    }
  )

}
</script>
