<template>
    <div class="mb-5 shadow p-2 rounded-4 mt-2">
        <table class="table table-borderless mb-4">
          <tbody>
            <tr class="border-bottom">
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">Nama</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">AJENG NINA RISKI (-)</span>
              </td>
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">Alamat</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">
                  DUSUN KRAJAN II, Genteng - Setail
                </span>
              </td>
            </tr>

            <tr class="border-bottom">
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">Jk / Umur</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">P / 20 Tahun - 7 Bulan - 22 Hari</span>
              </td>
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">Jenis/Poli</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">Kunjungan Sakit ( Umum / )</span>
              </td>
            </tr>

            <tr class="border-bottom">
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">Tanggal Kunjungan</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">24-07-2025</span>
              </td>
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">No. BPJS/Provider</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">/</span>
              </td>
            </tr>

            <tr>
              <td class="col-md-3 py-2 ps-3 pe-0 text-start">
                <span class="text-secondary">No. RM / NIK</span>
              </td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start">
                <span class="fw-bold text-dark">0139184 / 3510094312040003</span>
              </td>
              <td class="col-md-3 py-2 ps-3 pe-0 text-start"></td>
              <td class="col-md-3 py-2 ps-0 pe-3 text-start"></td>
            </tr>
          </tbody>
        </table>

       <div class="d-flex flex-wrap gap-3 m-3">
        <Link href="/surat-keterangan" class="btn btn-primary text-white fw-bold">
          <i class="bi bi-file-earmark-text me-2"></i>SURAT KETERANGAN
        </Link>

        <Link href="/surat-rujukan" class="btn btn-primary text-white fw-bold">
          <i class="bi bi-file-earmark-text me-2"></i>SURAT RUJUKAN
        </Link>

        <Link href="/riwayat-pasien" class="btn btn-success text-white fw-bold">
          <i class="bi bi-clock-history me-2"></i>RIWAYAT PASIEN
        </Link>

        <Link href="/cppt" class="btn btn-success text-white fw-bold">
          <i class="bi bi-clock-history me-2"></i>CPPT
        </Link>

        <Link href="/ukk" class="btn btn-success text-white fw-bold">
          <i class="bi bi-clipboard2-heart me-2"></i>UKK
        </Link>

        <div v-if="!isMelayani">
          <button class="btn btn-info text-white fw-bold" @click.prevent="isMelayani = true">
            <i class="bi bi-person-check me-2"></i>MULAI MELAYANI PEMERIKSAAN PASIEN
          </button>
        </div>
      </div>
    </div>
     <div v-if="isMelayani" class="mt-4">
      <slot></slot>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isMelayani: Boolean
})

// 👉 bikin local state
const isMelayani = ref(props.isMelayani)

// emit event balik ke induk
const emit = defineEmits(['ubah-melayani'])

// emit setiap kali isMelayani berubah
watch(isMelayani, (val) => {
  emit('ubah-melayani', val)
})

// Optional: update local ref kalau prop berubah (biar sinkron terus)
watch(() => props.isMelayani, (val) => {
  isMelayani.value = val
})
</script>
