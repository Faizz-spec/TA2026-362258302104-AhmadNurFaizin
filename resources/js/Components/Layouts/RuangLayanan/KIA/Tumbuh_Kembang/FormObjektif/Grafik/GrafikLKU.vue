<template>
  <div class="card">
    <div class="card-header bg-warning text-dark">
      <div class="text-center mb-3">
        <label class="me-2 fw-bold">Pilih Jenis Kelamin:</label>
        <div class="btn-group" role="group">
          <button 
            type="button" 
            class="btn" 
            :class="jenisKelamin === 'laki' ? 'btn-info' : 'btn-outline-dark'"
            @click="ubahJenisKelamin('laki')"
          >
            Laki-laki
          </button>
          <button 
            type="button" 
            class="btn" 
            :class="jenisKelamin === 'perempuan' ? 'btn-danger' : 'btn-outline-dark'"
            @click="ubahJenisKelamin('perempuan')"
          >
            Perempuan
          </button>
        </div>
      </div>
      <h5 class="card-title mb-0 text-center">
        Grafik Lingkar Kepala Anak {{ jenisKelamin === 'laki' ? 'Laki-laki' : 'Perempuan' }} (Usia 0-24 Bulan)
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
          <label class="form-label fw-bold">Lingkar Kepala (cm)</label>
          <input 
            v-model="formData.lingkarKepala" 
            type="number" 
            step="0.1" 
            class="form-control" 
            placeholder="Contoh: 42.5"
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
        <button @click="tambahData" class="btn btn-warning btn-lg px-5">
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
        <p class="mb-0"><strong>Status:</strong> {{ statusGizi }}</p>
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
                <th>Lingkar Kepala (cm)</th>
                <th>Z-Score</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in dataAnak" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ formatTanggal(item.tanggal) }}</td>
                <td>{{ item.umur }}</td>
                <td>{{ item.lingkarKepala }}</td>
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
  lingkarKepala: ''
});

const dataAnak = ref([]);

// Data standar WHO untuk lingkar kepala anak laki-laki 0-24 bulan
const dataStandarLaki = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [34.5, 37.3, 39.1, 40.5, 41.6, 42.6, 43.3, 43.9, 44.5, 45.0, 45.4, 45.8, 46.1, 46.4, 46.7, 46.9, 47.1, 47.4, 47.6, 47.8, 48.0, 48.1, 48.3, 48.5, 48.7],
  plus3sd: [37.9, 40.9, 42.9, 44.4, 45.6, 46.6, 47.4, 48.1, 48.8, 49.3, 49.8, 50.3, 50.7, 51.0, 51.4, 51.7, 52.0, 52.3, 52.6, 52.8, 53.1, 53.3, 53.6, 53.8, 54.0],
  plus2sd: [36.9, 39.7, 41.5, 42.9, 44.0, 44.9, 45.7, 46.4, 47.0, 47.5, 48.0, 48.4, 48.8, 49.2, 49.5, 49.8, 50.0, 50.3, 50.6, 50.8, 51.0, 51.3, 51.5, 51.7, 51.9],
  minus2sd: [31.9, 34.7, 36.5, 37.9, 39.0, 39.9, 40.7, 41.3, 41.9, 42.4, 42.9, 43.3, 43.6, 44.0, 44.3, 44.6, 44.8, 45.1, 45.3, 45.5, 45.7, 45.9, 46.1, 46.3, 46.5],
  minus3sd: [30.7, 33.2, 35.0, 36.3, 37.3, 38.2, 39.0, 39.7, 40.2, 40.7, 41.2, 41.6, 41.9, 42.3, 42.6, 42.8, 43.1, 43.3, 43.5, 43.7, 43.9, 44.1, 44.3, 44.5, 44.7]
};

// Data standar WHO untuk lingkar kepala anak perempuan 0-24 bulan
const dataStandarPerempuan = {
  umur: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
  median: [33.9, 36.5, 38.3, 39.5, 40.6, 41.5, 42.2, 42.8, 43.4, 43.8, 44.3, 44.6, 45.0, 45.3, 45.6, 45.8, 46.0, 46.3, 46.5, 46.7, 46.9, 47.1, 47.2, 47.4, 47.6],
  plus3sd: [37.3, 40.1, 42.1, 43.5, 44.7, 45.7, 46.5, 47.2, 47.8, 48.4, 48.9, 49.3, 49.7, 50.1, 50.4, 50.7, 51.0, 51.3, 51.6, 51.8, 52.1, 52.3, 52.5, 52.8, 53.0],
  plus2sd: [36.2, 38.9, 40.7, 42.0, 43.1, 44.0, 44.8, 45.5, 46.0, 46.6, 47.0, 47.5, 47.9, 48.2, 48.5, 48.8, 49.1, 49.4, 49.6, 49.9, 50.1, 50.3, 50.6, 50.8, 51.0],
  minus2sd: [31.5, 33.9, 35.6, 36.8, 37.8, 38.7, 39.4, 40.0, 40.6, 41.1, 41.5, 41.9, 42.2, 42.5, 42.8, 43.1, 43.3, 43.6, 43.8, 44.0, 44.2, 44.4, 44.6, 44.8, 45.0],
  minus3sd: [30.3, 32.7, 34.3, 35.5, 36.4, 37.2, 37.9, 38.5, 39.1, 39.5, 40.0, 40.3, 40.7, 41.0, 41.3, 41.5, 41.8, 42.0, 42.2, 42.4, 42.6, 42.8, 43.0, 43.2, 43.3]
};

const dataStandarAktif = computed(() => {
  return jenisKelamin.value === 'laki' ? dataStandarLaki : dataStandarPerempuan;
});

const zScore = ref('');
const statusGizi = ref('');

function hitungZScore() {
  if (!formData.value.umur || !formData.value.lingkarKepala) {
    zScore.value = '';
    statusGizi.value = '';
    return;
  }
  
  const umur = parseInt(formData.value.umur);
  const lingkar = parseFloat(formData.value.lingkarKepala);
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
  const z = (lingkar - median) / sd;
  zScore.value = z.toFixed(2);
  
  // Tentukan status berdasarkan lingkar kepala
  if (z > 3) {
    statusGizi.value = 'Makrosefali (Severely Large)';
  } else if (z > 2) {
    statusGizi.value = 'Makrosefali (Large)';
  } else if (z >= -2) {
    statusGizi.value = 'Normal';
  } else if (z >= -3) {
    statusGizi.value = 'Mikrosefali (Small)';
  } else {
    statusGizi.value = 'Mikrosefali (Severely Small)';
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
          data: dataAnak.value.map(d => ({ x: d.umur, y: d.lingkarKepala })),
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
            text: 'Lingkar Kepala (cm)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          min: 30,
          max: 54,
          ticks: {
            stepSize: 2
          }
        }
      }
    }
  });
}

function tambahData() {
  if (!formData.value.umur || !formData.value.lingkarKepala) {
    alert('⚠️ Mohon isi umur dan lingkar kepala terlebih dahulu!');
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
    lingkarKepala: parseFloat(formData.value.lingkarKepala),
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
    lingkarKepala: ''
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