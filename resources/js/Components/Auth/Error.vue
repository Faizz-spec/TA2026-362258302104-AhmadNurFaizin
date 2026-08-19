<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

function showPopup({ title = 'Error', text = 'Terjadi kesalahan', icon = 'error' } = {}) {
  Swal.fire({
    icon,
    title,
    text,
    confirmButtonColor: '#2d6a4f',
    background: 'rgba(255,255,255,0.95)',
    showClass: { popup: 'swal2-show' },
    hideClass: { popup: 'swal2-hide' },
  })
}

const onPopupError = (e) => showPopup({ title: e.detail?.title ?? 'Login gagal', text: e.detail?.text ?? 'Terjadi kesalahan', icon: 'error' })
const onPopupWarn  = (e) => showPopup({ title: e.detail?.title ?? 'Perhatian',  text: e.detail?.text ?? '',                 icon: 'warning' })
const onPopupOk    = (e) => showPopup({ title: e.detail?.title ?? 'Berhasil',   text: e.detail?.text ?? '',                 icon: 'success' })

onMounted(() => {
  window.addEventListener('popup:error', onPopupError)
  window.addEventListener('popup:warn',  onPopupWarn)
  window.addEventListener('popup:ok',    onPopupOk)
})
onBeforeUnmount(() => {
  window.removeEventListener('popup:error', onPopupError)
  window.removeEventListener('popup:warn',  onPopupWarn)
  window.removeEventListener('popup:ok',    onPopupOk)
})
</script>

<template>
  <!-- Komponen ini tidak merender apa-apa; hanya “mendengarkan” event -->
  <div style="display:none"></div>
</template>
