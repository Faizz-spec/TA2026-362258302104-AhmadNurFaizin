<template>
  <div class="card">
    <div class="card-header bg-purple text-white">
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
        Grafik Tinggi Badan Anak {{ jenisKelamin === 'laki' ? 'Laki-laki' : 'Perempuan' }} (Usia 24-60 Bulan)
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
            min="24" 
            max="60" 
            class="form-control" 
            placeholder="24-60"
            @input="hitungZScore"
          >
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Tinggi Badan (cm)</label>
          <input 
            v-model="formData.tinggiBadan" 
            type="number" 
            step="0.1" 
            class="form-control" 
            placeholder="Contoh: 95.5"
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
        <button @click="tambahData" class="btn btn-purple btn-lg px-5">
          💾 Simpan
        </button>
        <button 
          v-if="dataAnak.length > 0" 
          @click="resetData" 
          class="btn btn-danger btn-lg px-5 ms-2"
        >
          🗑️ Reset Data
        </button>
      </div>
      
      <div v-if="statusGizi" class="alert mt-4 mb-0" :class="alertClass">
        <h5 class="alert-heading mb-2">📊 Hasil Analisis</h5>
        <p class="mb-1"><strong>Z-Score:</strong> {{ zScore }}</p>
        <p class="mb-0"><strong>Status Gizi:</strong> {{ statusGizi }}</p>
      </div>

      <!-- Tabel Data Anak -->
      <div v-if="dataAnak.length > 0" class="mt-4">
        <h5 class="mb-3">📋 Riwayat Data Anak</h5>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Umur (bulan)</th>
                <th>Tinggi Badan (cm)</th>
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
                <td>{{ item.tinggiBadan }}</td>
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
  tinggiBadan: ''
});

const dataAnak = ref([]);

// Data standar WHO untuk tinggi badan anak laki-laki 24-60 bulan (2-5 tahun)
const dataStandarLaki = {
  umur: [24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60],
  median: [87.8, 88.7, 89.6, 90.4, 91.2, 92.0, 92.8, 93.5, 94.3, 95.0, 95.7, 96.4, 97.1, 97.7, 98.4, 99.0, 99.7, 100.3, 100.9, 101.5, 102.1, 102.7, 103.3, 103.9, 104.4, 105.0, 105.6, 106.1, 106.7, 107.2, 107.8, 108.3, 108.9, 109.4, 109.9, 110.5, 111.0],
  plus3sd: [93.9, 94.9, 95.8, 96.7, 97.6, 98.5, 99.3, 100.1, 100.9, 101.7, 102.5, 103.3, 104.0, 104.8, 105.5, 106.2, 106.9, 107.6, 108.3, 109.0, 109.7, 110.4, 111.0, 111.7, 112.3, 113.0, 113.6, 114.3, 114.9, 115.5, 116.1, 116.8, 117.4, 118.0, 118.6, 119.2, 119.8],
  plus2sd: [90.6, 91.6, 92.5, 93.4, 94.3, 95.1, 95.9, 96.7, 97.5, 98.3, 99.1, 99.8, 100.6, 101.3, 102.0, 102.7, 103.4, 104.1, 104.8, 105.5, 106.1, 106.8, 107.4, 108.1, 108.7, 109.4, 110.0, 110.6, 111.2, 111.9, 112.5, 113.1, 113.7, 114.3, 114.9, 115.5, 116.1],
  minus2sd: [81.7, 82.5, 83.4, 84.2, 85.0, 85.7, 86.5, 87.2, 87.9, 88.6, 89.3, 90.0, 90.7, 91.3, 92.0, 92.6, 93.2, 93.8, 94.4, 95.0, 95.6, 96.2, 96.8, 97.3, 97.9, 98.5, 99.0, 99.6, 100.1, 100.7, 101.2, 101.8, 102.3, 102.8, 103.4, 103.9, 104.4],
  minus3sd: [78.7, 79.5, 80.3, 81.1, 81.9, 82.6, 83.4, 84.1, 84.8, 85.5, 86.2, 86.8, 87.5, 88.1, 88.8, 89.4, 90.0, 90.6, 91.2, 91.8, 92.4, 93.0, 93.5, 94.1, 94.6, 95.2, 95.7, 96.3, 96.8, 97.4, 97.9, 98.4, 98.9, 99.5, 100.0, 100.5, 101.0]
};

const dataStandarAktif = computed(() => {
  return jenisKelamin.value === 'laki' ? dataStandarLaki : dataStandarPerempuan;
});

const zScore = ref('');
const statusGizi = ref('');

function hitungZScore() {
  if (!formData.value.umur || !formData.value.tinggiBadan) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  const tinggi = parseFloat(formData.value.tinggiBadan);
  const data = dataStandarAktif.value;
  
  if (umur < 24 || umur > 60) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const index = umur - 24; // karena array dimulai dari bulan 24
  
  const median = data.median[index];
  const plus2sd = data.plus2sd[index];
  const minus2sd = data.minus2sd[index];
  
  // Hitung SD
  const sd = (plus2sd - median) / 2;
  
  // Hitung Z-Score
  const z = (tinggi - median) / sd;
  zScore.value = z.toFixed(2);
  
  // Tentukan status gizi berdasarkan tinggi badan
  if (z > 3) {
    statusGizi.value = 'Tinggi (Severely Tall)';
  } else if (z > 2) {
    statusGizi.value = 'Tinggi';
  } else if (z >= -2) {
    statusGizi.value = 'Normal';
  } else if (z >= -3) {
    statusGizi.value = 'Pendek (Stunted)';
  } else {
    statusGizi.value = 'Sangat Pendek (Severely Stunted)';
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
          data: dataAnak.value.map(d => ({ x: d.umur, y: d.tinggiBadan })),
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
              label += context.parsed.y.toFixed(1) + ' cm';
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
          min: 24,
          max: 60,
          ticks: {
            stepSize: 3
          }
        },
        y: {
          title: {
            display: true,
            text: 'Tinggi Badan (cm)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          min: 75,
          max: 120,
          ticks: {
            stepSize: 5
          }
        }
      }
    }
  });
}

function tambahData() {
  if (!formData.value.umur || !formData.value.tinggiBadan) {
    alert('⚠️ Mohon isi umur dan tinggi badan terlebih dahulu!');
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  if (umur < 24 || umur > 60) {
    alert('⚠️ Umur harus antara 24-60 bulan!');
    return;
  }
  
  const newData = {
    tanggal: formData.value.tanggal || new Date().toISOString().split('T')[0],
    umur: umur,
    tinggiBadan: parseFloat(formData.value.tinggiBadan),
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
    tinggiBadan: ''
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

.bg-purple {
  background-color: #6f42c1 !important;
}

.btn-purple {
  background-color: #6f42c1;
  color: white;
  border-color: #6f42c1;
}

.btn-purple:hover {
  background-color: #5a32a3;
  border-color: #5a32a3;
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

// Data standar WHO untuk tinggi badan anak perempuan 24-60 bulan (2-5 tahun)
const dataStandarPerempuan = {
  umur: [24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60],
  median: [86.4, 87.3, 88.1, 88.9, 89.7, 90.4, 91.2, 91.9, 92.6, 93.3, 94.0, 94.6, 95.3, 95.9, 96.6, 97.2, 97.8, 98.4, 99.0, 99.6, 100.2, 100.7, 101.3, 101.9, 102.4, 103.0, 103.5, 104.1, 104.6, 105.1, 105.7, 106.2, 106.7, 107.2, 107.7, 108.2, 108.7],
  plus3sd: [92.9, 93.9, 94.8, 95.7, 96.6, 97.4, 98.3, 99.1, 99.9, 100.7, 101.5, 102.2, 103.0, 103.7, 104.5, 105.2, 105.9, 106.6, 107.3, 108.0, 108.7, 109.3, 110.0, 110.6, 111.3, 111.9, 112.5, 113.2, 113.8, 114.4, 115.0, 115.7, 116.3, 116.9, 117.5, 118.1, 118.7],
  plus2sd: [89.6, 90.5, 91.4, 92.3, 93.1, 94.0, 94.8, 95.6, 96.4, 97.2, 97.9, 98.7, 99.4, 100.2, 100.9, 101.6, 102.3, 103.0, 103.7, 104.3, 105.0, 105.6, 106.3, 106.9, 107.5, 108.2, 108.8, 109.4, 110.0, 110.6, 111.2, 111.8, 112.4, 113.0, 113.6, 114.2, 114.8],
  minus2sd: [80.0, 80.8, 81.7, 82.5, 83.2, 84.0, 84.7, 85.4, 86.1, 86.8, 87.5, 88.1, 88.8, 89.4, 90.1, 90.7, 91.3, 91.9, 92.5, 93.1, 93.6, 94.2, 94.8, 95.3, 95.9, 96.4, 97.0, 97.5, 98.1, 98.6, 99.1, 99.7, 100.2, 100.7, 101.2, 101.7, 102.2],
  minus3sd: [76.7, 77.5, 78.4, 79.1, 79.9, 80.6, 81.3, 82.0, 82.7, 83.4, 84.0, 84.7, 85.3, 85.9, 86.6, 87.2, 87.8, 88.4, 89.0, 89.6, 90.1, 90.7, 91.3, 91.8, 92.4, 92.9, 93.5, 94.0, 94.5, 95.1, 95.6, 96.1, 96.6, 97.1, 97.6, 98.1, 98.6]
}; bisakah kamu lengkapi kode yang saya kirim ini tolong