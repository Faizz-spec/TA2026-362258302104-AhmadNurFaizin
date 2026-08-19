<template>
  <div class="card">
    <div class="card-header bg-success text-white">
      <div class="text-center mb-3">
        <label class="me-2 fw-bold">Pilih Jenis Kelamin:</label>
        <div class="btn-group" role="group">
          <button 
            type="button" 
            class="btn" 
            :class="jenisKelamin === 'laki' ? 'btn-info' : 'btn-outline-light'"
            @click="ubahJenisKelamin('laki')"
          >
            Laki-laki
          </button>
          <button 
            type="button" 
            class="btn" 
            :class="jenisKelamin === 'perempuan' ? 'btn-warning' : 'btn-outline-light'"
            @click="ubahJenisKelamin('perempuan')"
          >
            Perempuan
          </button>
        </div>
      </div>
      <h5 class="card-title mb-0 text-center">
        Grafik Berat Badan Anak {{ jenisKelamin === 'laki' ? 'Laki-laki' : 'Perempuan' }} (Usia 0-24 Bulan)
      </h5>
    </div>
    <div class="card-body">
      <div class="chart-container mb-4">
        <canvas ref="chartCanvas"></canvas>
      </div>
      
      <hr>
      
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-bold">Tanggal</label>
          <input 
            v-model="formData.tanggal" 
            type="date" 
            class="form-control"
          >
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Umur (bulan)</label>
          <input 
            v-model="formData.umur" 
            type="number" 
            min="0" 
            max="24" 
            class="form-control" 
            placeholder="0-24"
            @input="hitungZScore"
          >
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Berat Badan (kg)</label>
          <input 
            v-model="formData.beratBadan" 
            type="number" 
            step="0.1" 
            class="form-control" 
            placeholder="Contoh: 8.5"
            @input="hitungZScore"
          >
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Z-Score</label>
          <input 
            v-model="zScore" 
            type="text" 
            class="form-control bg-light" 
            readonly 
            placeholder="Otomatis"
          >
        </div>
      </div>
      
      <div class="text-center mt-4">
        <button @click="tambahData" class="btn btn-success btn-lg px-5">
          Simpan
        </button>
        <button 
          v-if="dataAnak.length > 0" 
          @click="resetData" 
          class="btn btn-danger btn-lg px-5 ms-2"
        >
          Reset Data
        </button>
      </div>
      
      <div v-if="statusGizi" class="alert mt-4 mb-0" :class="alertClass">
        <h5 class="alert-heading mb-2">Hasil Analisis</h5>
        <p class="mb-1"><strong>Z-Score:</strong> {{ zScore }}</p>
        <p class="mb-0"><strong>Status Gizi:</strong> {{ statusGizi }}</p>
      </div>

      <!-- Tabel Data Anak -->
      <div v-if="dataAnak.length > 0" class="mt-4">
        <h5 class="mb-3">Riwayat Data Anak</h5>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Umur (bulan)</th>
                <th>Berat Badan (kg)</th>
                <th>Z-Score</th>
                <th>Status Gizi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in dataAnak" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ formatTanggal(item.tanggal) }}</td>
                <td>{{ item.umur }}</td>
                <td>{{ item.beratBadan }}</td>
                <td>{{ item.zScore }}</td>
                <td>
                  <span class="badge" :class="getBadgeClass(item.zScore)">
                    {{ item.statusGizi }}
                  </span>
                </td>
                <td>
                  <button 
                    @click="hapusData(index)" 
                    class="btn btn-sm btn-danger"
                  >
                    Hapus
                  </button>
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
import { ref, onMounted, watch, computed } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const chartCanvas = ref(null);
const chartInstance = ref(null);
const jenisKelamin = ref('perempuan');
const formData = ref({
  tanggal: '',
  umur: '',
  beratBadan: ''
});

const dataAnak = ref([]);

// Data standar WHO untuk berat badan anak laki-laki 0-24 bulan
const dataStandarLaki = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [3.3, 4.5, 5.6, 6.4, 7.0, 7.5, 7.9, 8.3, 8.6, 8.9, 9.2, 9.4, 9.6, 9.9, 10.1, 10.3, 10.5, 10.7, 10.9, 11.1, 11.3, 11.5, 11.8, 12.0, 12.2],
  plus3sd: [5.0, 6.6, 8.0, 9.0, 9.7, 10.4, 10.9, 11.4, 11.9, 12.3, 12.7, 13.0, 13.3, 13.7, 14.0, 14.3, 14.6, 14.9, 15.3, 15.6, 15.9, 16.2, 16.5, 16.9, 17.2],
  plus2sd: [4.4, 5.8, 7.1, 8.0, 8.7, 9.3, 9.8, 10.3, 10.7, 11.0, 11.4, 11.7, 12.0, 12.3, 12.6, 12.8, 13.1, 13.4, 13.7, 14.0, 14.2, 14.5, 14.8, 15.1, 15.4],
  minus2sd: [2.5, 3.4, 4.3, 5.0, 5.6, 6.0, 6.4, 6.7, 6.9, 7.1, 7.4, 7.6, 7.7, 7.9, 8.1, 8.3, 8.4, 8.6, 8.8, 8.9, 9.1, 9.2, 9.4, 9.5, 9.7],
  minus3sd: [2.1, 2.9, 3.8, 4.4, 4.9, 5.3, 5.7, 5.9, 6.2, 6.4, 6.6, 6.8, 6.9, 7.1, 7.2, 7.4, 7.5, 7.7, 7.8, 8.0, 8.1, 8.2, 8.4, 8.5, 8.6]
};

// Data standar WHO untuk berat badan anak perempuan 0-24 bulan
const dataStandarPerempuan = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [3.2, 4.2, 5.1, 5.8, 6.4, 6.9, 7.3, 7.6, 7.9, 8.2, 8.5, 8.7, 8.9, 9.2, 9.4, 9.6, 9.8, 10.0, 10.2, 10.4, 10.6, 10.9, 11.1, 11.3, 11.5],
  plus3sd: [4.8, 6.2, 7.5, 8.5, 9.3, 10.0, 10.6, 11.1, 11.6, 12.1, 12.5, 12.9, 13.3, 13.7, 14.1, 14.4, 14.8, 15.2, 15.6, 16.0, 16.4, 16.8, 17.2, 17.6, 18.0],
  plus2sd: [4.2, 5.5, 6.6, 7.5, 8.2, 8.8, 9.3, 9.8, 10.2, 10.5, 10.9, 11.2, 11.5, 11.8, 12.1, 12.4, 12.6, 12.9, 13.2, 13.5, 13.7, 14.0, 14.3, 14.6, 14.8],
  minus2sd: [2.4, 3.2, 3.9, 4.5, 5.0, 5.4, 5.7, 6.0, 6.3, 6.5, 6.7, 6.9, 7.0, 7.2, 7.4, 7.6, 7.7, 7.9, 8.1, 8.2, 8.4, 8.6, 8.7, 8.9, 9.0],
  minus3sd: [2.0, 2.7, 3.4, 3.9, 4.4, 4.8, 5.1, 5.3, 5.6, 5.8, 6.0, 6.1, 6.3, 6.4, 6.6, 6.7, 6.9, 7.0, 7.2, 7.3, 7.5, 7.6, 7.8, 7.9, 8.1]
};

const dataStandarAktif = computed(() => {
  return jenisKelamin.value === 'laki' ? dataStandarLaki : dataStandarPerempuan;
});

const zScore = ref('');
const statusGizi = ref('');

function hitungZScore() {
  if (!formData.value.umur || !formData.value.beratBadan) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  const berat = parseFloat(formData.value.beratBadan);
  const data = dataStandarAktif.value;
  
  if (umur < 0 || umur > 24) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const median = data.median[umur];
  const plus2sd = data.plus2sd[umur];
  const minus2sd = data.minus2sd[umur];
  
  // Hitung SD
  const sd = (plus2sd - median) / 2;
  
  // Hitung Z-Score
  const z = (berat - median) / sd;
  zScore.value = z.toFixed(2);
  
  // Tentukan status gizi berdasarkan BB/U
  if (z > 2) {
    statusGizi.value = 'Berat Badan Lebih (Overweight)';
  } else if (z >= -2) {
    statusGizi.value = 'Normal';
  } else if (z >= -3) {
    statusGizi.value = 'Berat Badan Kurang (Underweight)';
  } else {
    statusGizi.value = 'Berat Badan Sangat Kurang (Severely Underweight)';
  }
}

const alertClass = computed(() => {
  const z = parseFloat(zScore.value);
  if (isNaN(z)) return 'alert-secondary';
  
  if (z > 2 || z < -2) return 'alert-danger';
  if (z > 1 || z < -1) return 'alert-warning';
  return 'alert-success';
});

function getBadgeClass(z) {
  const zValue = parseFloat(z);
  if (zValue > 2 || zValue < -2) return 'bg-danger';
  if (zValue > 1 || zValue < -1) return 'bg-warning';
  return 'bg-success';
}

function formatTanggal(tanggal) {
  if (!tanggal) return '-';
  const date = new Date(tanggal);
  return date.toLocaleDateString('id-ID', { 
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric' 
  });
}

function buatGrafik() {
  if (!chartCanvas.value) return;
  
  if (chartInstance.value) {
    chartInstance.value.destroy();
  }
  
  const data = dataStandarAktif.value;
  
  chartInstance.value = new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels: data.umur,
      datasets: [
        {
          label: '+3 SD',
          data: data.plus3sd,
          borderColor: 'rgba(0, 0, 0, 0.5)',
          backgroundColor: 'transparent',
          borderWidth: 1,
          pointRadius: 2,
          tension: 0.4
        },
        {
          label: '+2 SD',
          data: data.plus2sd,
          borderColor: 'rgba(255, 0, 0, 0.5)',
          backgroundColor: 'transparent',
          borderWidth: 1,
          pointRadius: 2,
          tension: 0.4
        },
        {
          label: 'Median (0 SD)',
          data: data.median,
          borderColor: 'rgba(0, 200, 0, 0.8)',
          backgroundColor: 'transparent',
          borderWidth: 2,
          pointRadius: 3,
          tension: 0.4
        },
        {
          label: '-2 SD',
          data: data.minus2sd,
          borderColor: 'rgba(255, 0, 0, 0.5)',
          backgroundColor: 'transparent',
          borderWidth: 1,
          pointRadius: 2,
          tension: 0.4
        },
        {
          label: '-3 SD',
          data: data.minus3sd,
          borderColor: 'rgba(0, 0, 0, 0.5)',
          backgroundColor: 'transparent',
          borderWidth: 1,
          pointRadius: 2,
          tension: 0.4
        },
        {
          label: 'Data Anak',
          data: dataAnak.value.map(d => ({ x: d.umur, y: d.beratBadan })),
          borderColor: 'rgba(0, 0, 255, 1)',
          backgroundColor: 'rgba(0, 0, 255, 0.5)',
          borderWidth: 3,
          pointRadius: 6,
          pointHoverRadius: 8,
          showLine: true,
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      aspectRatio: 2.2,
      interaction: {
        intersect: false,
        mode: 'index'
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            boxWidth: 20,
            padding: 15
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              label += context.parsed.y.toFixed(1) + ' kg';
              return label;
            }
          }
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Umur (bulan)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          min: 0,
          max: 24,
          ticks: {
            stepSize: 1
          }
        },
        y: {
          title: {
            display: true,
            text: 'Berat Badan (kg)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          min: 2,
          max: 18,
          ticks: {
            stepSize: 2
          }
        }
      }
    }
  });
}

function tambahData() {
  if (!formData.value.umur || !formData.value.beratBadan) {
    alert('⚠️ Mohon isi umur dan berat badan terlebih dahulu!');
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  if (umur < 0 || umur > 24) {
    alert('⚠️ Umur harus antara 0-24 bulan!');
    return;
  }
  
  const newData = {
    tanggal: formData.value.tanggal || new Date().toISOString().split('T')[0],
    umur: umur,
    beratBadan: parseFloat(formData.value.beratBadan),
    zScore: parseFloat(zScore.value),
    statusGizi: statusGizi.value
  };
  
  dataAnak.value.push(newData);
  dataAnak.value.sort((a, b) => a.umur - b.umur);
  
  buatGrafik();
  
  // Reset form
  formData.value = {
    tanggal: '',
    umur: '',
    beratBadan: ''
  };
  zScore.value = '';
  statusGizi.value = '';
  
  alert('✅ Data berhasil ditambahkan!');
}

function hapusData(index) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    dataAnak.value.splice(index, 1);
    buatGrafik();
    alert('✅ Data berhasil dihapus!');
  }
}

function resetData() {
  if (confirm('Apakah Anda yakin ingin menghapus semua data?')) {
    dataAnak.value = [];
    buatGrafik();
    alert('✅ Semua data berhasil dihapus!');
  }
}

function ubahJenisKelamin(jk) {
  jenisKelamin.value = jk;
  hitungZScore();
}

watch(jenisKelamin, () => {
  buatGrafik();
});

onMounted(() => {
  buatGrafik();
});
</script>

<style scoped>
.card {
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  border: none;
  border-radius: 10px;
  overflow: hidden;
}

.card-header {
  padding: 1.5rem;
}

.btn-group .btn {
  min-width: 120px;
  font-weight: 600;
}

.chart-container {
  position: relative;
  min-height: 400px;
  padding: 1rem 0;
}

canvas {
  max-height: 450px;
}

.table {
  font-size: 0.9rem;
}

.table thead th {
  font-weight: 600;
  text-align: center;
  vertical-align: middle;
}

.table tbody td {
  vertical-align: middle;
  text-align: center;
}

.alert {
  border-radius: 8px;
  border-left: 5px solid;
}

.alert-success {
  border-left-color: #28a745;
}

.alert-warning {
  border-left-color: #ffc107;
}

.alert-danger {
  border-left-color: #dc3545;
}

@media (max-width: 768px) {
  .btn-group .btn {
    min-width: 100px;
    font-size: 0.9rem;
  }
  
  .chart-container {
    min-height: 300px;
  }
}
</style>