<script setup>
defineProps({
  idPoli: String,
  kluster: [String, null],
  now: String
})
</script>

<template>
  <div style="padding:16px">
    <h2>Kunjungan Online (Test)</h2>
    <p><b>idPoli:</b> {{ idPoli }}</p>
    <p><b>kluster:</b> {{ kluster ?? '-' }}</p>
    <p><b>server time:</b> {{ now }}</p>

    <div style="margin-top:12px">
      <a :href="route('kunj-online.pelayanan', { id: 1, idPoli: idPoli ?? '999', idPelayanan: 1 })">
        Contoh link ke halaman pelayanan
      </a>
    </div>
  </div>
</template>
