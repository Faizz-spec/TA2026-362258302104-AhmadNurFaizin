<template>
  <div class="card">
    <div class="card-header bg-primary text-white">
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
        Grafik Panjang Badan Anak Perempuan (Usia 0-24 Bulan)
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
          <label class="form-label fw-bold">Panjang Badan (cm)</label>
          <input 
            v-model="formData.panjangBadan" 
            type="number" 
            step="0.1" 
            class="form-control" 
            placeholder="Contoh: 70.5"
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
                <th>Panjang Badan (cm)</th>
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
                <td>{{ item.panjangBadan }}</td>
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
  panjangBadan: ''
});

const dataAnak = ref([]);

// Data standar WHO untuk panjang badan anak 0-24 bulan
const dataStandarLaki = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [49.9, 54.7, 58.4, 61.4, 63.9, 65.9, 67.6, 69.2, 70.6, 72.0, 73.3, 74.5, 75.7, 76.9, 78.0, 79.1, 80.2, 81.2, 82.3, 83.2, 84.2, 85.1, 86.0, 86.9, 87.8],
  plus3sd: [53.7, 58.6, 62.4, 65.5, 68.0, 70.1, 71.9, 73.5, 75.0, 76.5, 77.9, 79.2, 80.5, 81.8, 83.0, 84.2, 85.4, 86.5, 87.7, 88.8, 89.8, 90.9, 91.9, 92.9, 93.9],
  plus2sd: [52.0, 56.7, 60.4, 63.3, 65.7, 67.8, 69.5, 71.1, 72.6, 74.0, 75.3, 76.6, 77.9, 79.1, 80.2, 81.4, 82.5, 83.6, 84.7, 85.7, 86.7, 87.7, 88.7, 89.6, 90.6],
  minus2sd: [46.1, 50.8, 54.4, 57.3, 59.7, 61.7, 63.3, 64.8, 66.2, 67.5, 68.7, 69.9, 71.0, 72.1, 73.1, 74.1, 75.0, 76.0, 76.9, 77.7, 78.6, 79.4, 80.2, 81.0, 81.7],
  minus3sd: [44.2, 48.9, 52.4, 55.3, 57.6, 59.6, 61.2, 62.7, 64.0, 65.2, 66.4, 67.6, 68.6, 69.6, 70.6, 71.6, 72.5, 73.3, 74.2, 75.0, 75.8, 76.5, 77.2, 78.0, 78.7]
};

const dataStandarPerempuan = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [49.1, 53.7, 57.1, 59.8, 62.1, 64.0, 65.7, 67.3, 68.7, 70.1, 71.5, 72.8, 74.0, 75.2, 76.4, 77.5, 78.6, 79.7, 80.7, 81.7, 82.7, 83.7, 84.6, 85.5, 86.4],
  plus3sd: [52.9, 57.6, 61.1, 64.0, 66.4, 68.5, 70.3, 71.9, 73.5, 75.0, 76.4, 77.8, 79.2, 80.5, 81.7, 83.0, 84.2, 85.4, 86.5, 87.6, 88.7, 89.8, 90.8, 91.9, 92.9],
  plus2sd: [51.0, 55.6, 59.1, 61.9, 64.3, 66.2, 68.0, 69.6, 71.1, 72.6, 74.0, 75.3, 76.6, 77.8, 79.1, 80.2, 81.4, 82.5, 83.6, 84.7, 85.7, 86.7, 87.7, 88.7, 89.6],
  minus2sd: [45.4, 49.8, 53.0, 55.6, 57.8, 59.6, 61.2, 62.7, 64.0, 65.3, 66.5, 67.7, 68.9, 70.0, 71.0, 72.0, 73.0, 74.0, 74.9, 75.8, 76.7, 77.5, 78.4, 79.2, 80.0],
  minus3sd: [43.6, 47.8, 51.0, 53.5, 55.6, 57.4, 58.9, 60.3, 61.7, 62.9, 64.1, 65.2, 66.3, 67.3, 68.3, 69.3, 70.2, 71.1, 72.0, 72.8, 73.7, 74.5, 75.2, 76.0, 76.7]
};

const dataStandarAktif = computed(() => {
  return jenisKelamin.value === 'laki' ? dataStandarLaki : dataStandarPerempuan;
});

const zScore = ref('');
const statusGizi = ref('');

function hitungZScore() {
  if (!formData.value.umur || !formData.value.panjangBadan) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  const panjang = parseFloat(formData.value.panjangBadan);
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
  const z = (panjang - median) / sd;
  zScore.value = z.toFixed(2);
  
  // Tentukan status gizi
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
          data: dataAnak.value.map(d => ({ x: d.umur, y: d.panjangBadan })),
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
          min: 0,
          max: 24,
          ticks: {
            stepSize: 1
          }
        },
        y: {
          title: {
            display: true,
            text: 'Panjang Badan (cm)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          min: 45,
          max: 95
        }
      }
    }
  });
}

function tambahData() {
  if (!formData.value.umur || !formData.value.panjangBadan) {
    alert('⚠️ Mohon isi umur dan panjang badan terlebih dahulu!');
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
    panjangBadan: parseFloat(formData.value.panjangBadan),
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
    panjangBadan: ''
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
  hitungZScore(); // Recalculate jika ada input
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