<script setup>
import AppLayout from '@/Components/Layouts/AppLayouts.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

// props dari server / daftar pasien
const props = defineProps({
  pasien: {
    type: Object,
    required: true,
  },
})

function kembali() {
  router.get('/mal-sehat') // balik ke halaman daftar pasien
}
</script>

<template>
  <div class="p-6">
    <!-- Tombol kembali -->
    <button @click="kembali" class="px-4 py-2 bg-blue-600 text-white rounded-lg mb-4">
      ← Kembali
    </button>

    <!-- Detail pasien -->
    <div class="bg-white shadow-md rounded-xl p-6 space-y-4">
      <h2 class="text-xl font-bold text-gray-700">Detail Pasien</h2>

      <div class="grid grid-cols-2 gap-4 text-gray-700">
        <div>
          <p class="font-semibold">Tanggal Kunjungan</p>
          <p>{{ pasien.tanggal }}</p>
        </div>

        <div>
          <p class="font-semibold">No. Kunjungan</p>
          <p>{{ pasien.no_kunjungan }}</p>
        </div>

        <div>
          <p class="font-semibold">Nama</p>
          <p>{{ pasien.nama }}</p>
        </div>

        <div>
          <p class="font-semibold">NIK</p>
          <p>{{ pasien.nik }}</p>
        </div>

        <div>
          <p class="font-semibold">Alamat</p>
          <p>{{ pasien.alamat }}</p>
        </div>

        <div>
          <p class="font-semibold">Umur</p>
          <p>{{ pasien.umur }} tahun</p>
        </div>

        <div>
          <p class="font-semibold">Jenis Kelamin</p>
          <p>{{ pasien.jk }}</p>
        </div>

        <div>
          <p class="font-semibold">Status</p>
          <p>{{ pasien.status }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
