<template>
  <div class="form-section border-0 card shadow-sm p-3">
    <h6 class="mb-3 text-success">Apgar Menit {{ menit }}</h6>

    <div class="mb-2">
      <label class="form-label fw-bold">1.1 Appearance (Warna Kulit)</label>
      <select v-model="form.appearance" class="form-select">
        <option value="LA7622-8">[0] Biru, pucat</option>
        <option value="LA7623-6">[1] Tubuh merah muda, ekstremitas biru</option>
        <option value="LA7624-4">[2] Seluruh tubuh merah muda</option>
      </select>
    </div>

    <div class="mb-2">
      <label class="form-label fw-bold">1.2 Pulse (Denyut jantung)</label>
      <select v-model="form.pulse" class="form-select">
        <option value="LA6716-0">[0] Tidak Ada</option>
        <option value="LA6717-8">[1]&lt;100/menit</option>
        <option value="LA6718-6">[2]&ge;100/menit</option>
      </select>
    </div>

    <div class="mb-2">
      <label class="form-label fw-bold">1.3 Grimace (Refleks Taktil)</label>
      <select v-model="form.grimace" class="form-select">
        <option value="LA6719-4">[0] Tidak Ada Respon</option>
        <option value="LA6720-2">[1] Meringis</option>
        <option value="LA6721-0">[2] Terbatuk atau Bersin</option>
      </select>
    </div>

    <div class="mb-2">
      <label class="form-label fw-bold">1.4 Activity (Tonus Otot)</label>
      <select v-model="form.activity" class="form-select">
        <option value="LA6713-7">[0] Lemas</option>
        <option value="LA6714-5">[1] Sedikit Gerakan Ekstremitas</option>
        <option value="LA6715-2">[2] Bergerak Aktif</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">1.5 Respiration (Pernapasan)</label>
      <select v-model="form.respiration" class="form-select">
        <option value="LA6725-1">[0] Tidak ada</option>
        <option value="LA6726-9">[1] Perlahan / Tidak teratur</option>
        <option value="LA6727-7">[2] Menangis kuat</option>
      </select>
    </div>

    <hr />

    <div class="mb-2">
      <table class="table table-bordered">
        <th>Total Score</th>
        <th class="text-center">{{ totalScore }}</th>
      </table>
    </div>
    <div class="mb-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Klasifikasi</th>
            <th class="text-center">Score</th>
          </tr>
        </thead>
        <tbody>
          <tr :class="rowClass">
            <td>{{ klasifikasi }}</td>
            <td class="text-center">{{ scoreRange }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
  import { reactive, computed, watch } from 'vue';

  const emit = defineEmits(['update']); 

  // Props untuk membedakan antar form (Menit 1, 5, 10)
  const props = defineProps({
    menit: {
      type: Number,
      required: true,
    },
  });

  // State form (pakai kode LOINC)
  const form = reactive({
    appearance: 'LA7622-8',
    pulse: 'LA6716-0',
    grimace: 'LA6719-4',
    activity: 'LA6713-7',
    respiration: 'LA6725-1',
  });

  // Mapping kode → skor angka
  const scoreMap = {
    'LA7622-8': 0,
    'LA7623-6': 1,
    'LA7624-4': 2,
    'LA6716-0': 0,
    'LA6717-8': 1,
    'LA6718-6': 2,
    'LA6719-4': 0,
    'LA6720-2': 1,
    'LA6721-0': 2,
    'LA6713-7': 0,
    'LA6714-5': 1,
    'LA6715-2': 2,
    'LA6725-1': 0,
    'LA6726-9': 1,
    'LA6727-7': 2,
  };

  // Total otomatis
  const totalScore = computed(
    () =>
      scoreMap[form.appearance] +
      scoreMap[form.pulse] +
      scoreMap[form.grimace] +
      scoreMap[form.activity] +
      scoreMap[form.respiration]
  );

  const scoreRange = computed(() => {
    if (totalScore.value >= 7 && totalScore.value <= 10) return '7 - 10';
    if (totalScore.value >= 4 && totalScore.value <= 6) return '4 - 6';
    return '0 - 3';
  });

  // Klasifikasi berdasarkan total
  const klasifikasi = computed(() => {
    if (totalScore.value >= 7) return 'Bayi Normal';
    if (totalScore.value >= 4) return 'Asfiksia Sedang';
    return 'Asfiksia Berat';
  });
  const rowClass = computed(() => {
    if (totalScore.value >= 7) return 'table-success';
    if (totalScore.value >= 4) return 'table-warning';
    return 'table-danger';
  });

  watch([() => totalScore.value, () => klasifikasi.value], () => {
    emit('update', {
      menit: props.menit,
      total: totalScore.value,
      klasifikasi: klasifikasi.value,
    });
  });
</script>
