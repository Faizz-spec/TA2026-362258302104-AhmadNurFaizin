<template>
  <div class="card rounded-4 border-0 shadow-sm m-4">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between align-items-center rounded-4 rounded-bottom-0 py-3"
    style="background: linear-gradient(135deg, #3b82f6, #10b981);">
      <h2 class="fs-6 mb-0 text-white fw-bold">Daftar Surat Keterangan</h2>

      <div class="d-flex align-items-center gap-2">
        <Link :href="route(createRoute, {
          idPoli: props.idPoli,
          idPelayanan: props.idPelayanan
        })"  class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
        <i class="bi bi-plus-lg "></i> TAMBAH DATA
        </Link>

        <Link v-if="backRoute" :href="backRoute" class="btn btn-info btn-sm fw-semibold d-flex align-items-center gap-2"
          style="background: rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.35);">
        <i class="bi bi-arrow-left"></i> KEMBALI KE DIAGNOSA
        </Link>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <!-- Optional: info pasien di atas tabel -->
      <div v-if="pasienName || noMr"
        class="alert alert-light border rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-3">
        <i class="bi bi-person-circle fs-4 text-success"></i>
        <div class="small">
          <div class="fw-semibold">{{ pasienName || '-' }}</div>
          <div class="text-muted">No. RM: {{ noMr || '-' }} <span v-if="nik">• NIK: {{ nik }}</span></div>
        </div>
      </div>

      <div class="table-responsive rounded-3 border">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr class="text-uppercase small text-muted">
              <th class="px-3" style="width: 56px;">No</th>
              <th>Tanggal</th>
              <th>Surat Keterangan</th>
              <th>No Surat</th>
              <th>Keperluan</th>
              <th class="text-center" style="width: 160px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.suketList">
              <td class="px-3">{{ index + 1 }}</td>
              <td class="fw-semibold">{{ item.simpus_pelayanan.simpus_loket.tglKunjungan }}</td>
              <td>{{ item.jenis_surat.SURAT }}</td>
              <td>{{ item.no_surat }}</td>
              <td>{{ item.keperluan }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <Link :href="route('ruang-layanan-umum.edit-suket', {idPoli: props.idPoli, idPelayanan:props.idPelayanan, idSurat: item.id_surat})" class="btn btn-outline-primary" title="Lihat / Edit">
                    <i class="bi bi-pencil-square"></i>
                  </Link>
                  <Link :href="route('ruang-layanan-umum.cetak-suket', { idSurat: item.id_surat })" type="button"
                    class="btn btn-outline-success" title="Cetak">
                  <i class="bi bi-printer"></i>
                  </Link>
                  <button @click="onDelete(item.id_surat)" type="button" class="btn btn-outline-danger" title="Hapus">
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
        <div>Menampilkan {{ items.length }} data</div>
        <!-- tempatkan pagination kalau perlu -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  items: { type: Array, default: () => [] },
  backRoute: { type: String, default: '' },
  createRoute: { type: String, default: '' },
  dataPasien: { type: [Object, Array], default: null },
  idPoli: String,
  idPelayanan: String,
  suketList: Object
})
const emit = defineEmits(['suketList-update']);

console.log('suket lisnya', props.suketList)
function onDelete(id_surat) {
  if (confirm('Yakin ingin menghapus surat ini?')) {
    router.post(route('ruang-layanan-umum.hapus-suket', {
      idSurat: id_surat
    }),{
      onSuccess: () => {
        emit('suketList-update');
      }
    })
  }
}
</script>
