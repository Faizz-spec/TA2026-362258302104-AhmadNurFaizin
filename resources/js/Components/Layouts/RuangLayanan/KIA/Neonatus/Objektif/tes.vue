<template>
  <div class="card shadow-sm border-0 p-4 form-card">
    <div class="card-body">
      <form @submit.prevent="saveForm">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="form-section">
              <h6 class="mb-3">Apgar Menit 1</h6>
              <div class="mb-2">
                <label class="form-label fw-bold">1.1 Apperance (Warna Kulit)</label>
                <select class="form-select">
                  <option value="LA7622-8">[0] Biru, pucat</option>
                  <option value="LA7623-6">[1] Tubuh merah muda, ekstrimitas biru</option>
                  <option value="LA7624-4">[2] Seluruh Tubuh Merah Muda</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.2 Pulse (Denyut jantung)</label>
                <select class="form-select">
                  <option value="LA6716-0">[0] Tidak Ada</option>
                  <option value="LA6717-8">[1] &lt;100/menit</option>
                  <option value="LA6718-6">[2] &ge;=100/menit</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.3 Grimace(Refleks Taktil)</label>
                <select class="form-select">
                  <option value="LA6719-4">[0] Tidak Ada Respon</option>
                  <option value="LA6720-2">[1] Meringis</option>
                  <option value="LA6721-0">[2] Terbatuk atau Bersin</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.4 Activity(Tonus Otot)</label>
                <select class="form-select">
                  <option value="LA6713-7">[0] Lemas</option>
                  <option value="LA6714-5">[1] Sedikit Gerakan Ekstremitas</option>
                  <option value="LA6715-2">[2] Bergerak aktif</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.5 Respiration(Pernapasan)</label>
                <select class="form-select">
                  <option value="LA6725-1">[0] Tidak ada</option>
                  <option value="LA6726-9">[1] Perlahan / Tidak teratur</option>
                  <option value="LA6727-7">[2] Menangis kuat</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-section">
              <h6 class="mb-3">Apgar Menit 5</h6>
              <div class="mb-2">
                <label class="form-label fw-bold">1.1 Apperance (Warna Kulit)</label>
                <select class="form-select">
                  <option value="LA7622-8">[0] Biru, pucat</option>
                  <option value="LA7623-6">[1] Tubuh merah muda, ekstrimitas biru</option>
                  <option value="LA7624-4">[2] Seluruh Tubuh Merah Muda</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.2 Pulse (Denyut jantung)</label>
                <select class="form-select">
                  <option value="LA6716-0">[0] Tidak Ada</option>
                  <option value="LA6717-8">[1] &lt;100/menit</option>
                  <option value="LA6718-6">[2] &ge;=100/menit</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.3 Grimace(Refleks Taktil)</label>
                <select class="form-select">
                  <option value="LA6719-4">[0] Tidak Ada Respon</option>
                  <option value="LA6720-2">[1] Meringis</option>
                  <option value="LA6721-0">[2] Terbatuk atau Bersin</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.4 Activity(Tonus Otot)</label>
                <select class="form-select">
                  <option value="LA6713-7">[0] Lemas</option>
                  <option value="LA6714-5">[1] Sedikit Gerakan Ekstremitas</option>
                  <option value="LA6715-2">[2] Bergerak aktif</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.5 Respiration(Pernapasan)</label>
                <select class="form-select">
                  <option value="LA6725-1">[0] Tidak ada</option>
                  <option value="LA6726-9">[1] Perlahan / Tidak teratur</option>
                  <option value="LA6727-7">[2] Menangis kuat</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-section">
              <h6 class="mb-3">Apgar Menit 10</h6>
              <div class="mb-2">
                <label class="form-label fw-bold">1.1 Apperance (Warna Kulit)</label>
                <select class="form-select" v-model.number="apgar.appearance">
                  <option value="LA7622-8">[0] Biru, pucat</option>
                  <option value="LA7623-6">[1] Tubuh merah muda, ekstrimitas biru</option>
                  <option value="LA7624-4">[2] Seluruh Tubuh Merah Muda</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.2 Pulse (Denyut jantung)</label>
                <select class="form-select" v-model.number="apgar.pulse">
                  <option value="LA6716-0">[0] Tidak Ada</option>
                  <option value="LA6717-8">[1] &lt;100/menit</option>
                  <option value="LA6718-6">[2] &ge;=100/menit</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.3 Grimace(Refleks Taktil)</label>
                <select class="form-select" v-model.number="apgar.grimace">
                  <option value="LA6719-4">[0] Tidak Ada Respon</option>
                  <option value="LA6720-2">[1] Meringis</option>
                  <option value="LA6721-0">[2] Terbatuk atau Bersin</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label fw-bold">1.4 Activity(Tonus Otot)</label>
                <select class="form-select" v-model.number="apgar.activity">
                  <option value="LA6713-7">[0] Lemas</option>
                  <option value="LA6714-5">[1] Sedikit Gerakan Ekstremitas</option>
                  <option value="LA6715-2">[2] Bergerak aktif</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">1.5 Respiration(Pernapasan)</label>
                <select class="form-select" v-model.number="apgar.respiration">
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
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
  import { computed, reactive } from 'vue';

  const apgar = reactive({
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
  const totalScore = computed(() => {
    return (
      scoreMap[apgar.appearance] +
      scoreMap[apgar.pulse] +
      scoreMap[apgar.grimace] +
      scoreMap[apgar.activity] +
      scoreMap[apgar.respiration]
    );
  });

  const klasifikasi = computed(() => {
    if (totalScore.value >= 7) return 'Bayi Normal';
    if (totalScore.value >= 4) return 'Asfiksia Sedang';
    return 'Asfiksia Berat';
  });

  // Tentukan rentang score
  const scoreRange = computed(() => {
    if (totalScore.value >= 7 && totalScore.value <= 10) return '7 - 10';
    if (totalScore.value >= 4 && totalScore.value <= 6) return '4 - 6';
    return '0 - 3';
  });

  // Tambahkan warna background sesuai klasifikasi
  const rowClass = computed(() => {
    if (totalScore.value >= 7) return 'table-success';
    if (totalScore.value >= 4) return 'table-warning';
    return 'table-danger';
  });

  // Simpan form
  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
<style scoped>
  .form-card {
    width: 100%;
    margin: 0;
  }
</style>