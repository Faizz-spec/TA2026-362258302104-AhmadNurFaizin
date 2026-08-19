<template>
    <AppLayouts>
    <div class="card m-4  rounded-4 rounded-bottom-0">
    <div class="card-header bg-info d-flex justify-content-between p-3  rounded-4 rounded-bottom-0"   style="background: linear-gradient(135deg, #3b82f6, #10b981);">
        <h1 class="fs-5 text-white">BP GIGI</h1>
        <Link :href="backRoute" class="btn bg-white bg-opacity-25 border border-1 btn-sm text-white">
        <i class="fas fa-arrow-left me-1 text-white"></i> Kembali
      </Link>
      </div>
    <div class="card-body">
    <PelayananPasien :isMelayani="isMelayani" @ubah-melayani="handleMelayani">
      <div class="card">
   <div class="card-header p-4 bg-white border-bottom">
  <div class="d-flex align-items-center flex-wrap gap-3">
    <!-- Tab Navigation -->
    <div class="d-flex position-relative align-items-center">
      <div class="d-flex position-relative gap-2">
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'subjective' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="currentTab = 'subjective'">
          Subjective
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'objective' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="currentTab = 'objective'">
          Objective
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'assesment' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="currentTab = 'assesment'">
          Assesment
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'planning' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="currentTab = 'planning'">
          Planning
        </a>
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'planning' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="currentTab = 'status_pasien'">
          Status Pasien
        </a>
        
        <!-- Active Indicator Line -->
        <div class="tab-indicator" :style="indicatorStyle"></div>
      </div>
    </div>

    <!-- Action Button -->
    <div class="ms-auto">
      <Link href="#" class="btn btn-success btn-sm">
        <i class="fas fa-paper-plane me-2"></i> Kirim RME v.1 ke SATU SEHAT
      </Link>
    </div>
  </div>
</div>

    <div class="m-4 row gx-5">
    <FormPelayananSubjective v-if="currentTab === 'subjective'">
    </FormPelayananSubjective>
    <FormPelayananObjective v-if="currentTab === 'objective'" :currrentSub=true halaman="gigi">
    </FormPelayananObjective>
    <FormPelayananAssesment v-if="currentTab === 'assesment'">
    </FormPelayananAssesment>
    <FormPelayananPlanning v-if="currentTab === 'planning'">
          <div class="mb-3 row">
            <div class="col-sm-6">
              <div class="border p-3">
                <div
                  v-for="(baris, index) in layoutKursi"
                  :key="index"
                  class="border border-dark d-flex justify-content-center mb-2 flex-wrap"
                >
                  <div
                    v-for="(kursi, i) in baris"
                    :key="i"
                    class="d-flex flex-column align-items-center my-1 fw-bold"
                    style="width: 40px"
                  >
                    <input
                      type="checkbox"
                      class="form-check-input mb-1"
                      :id="'kursi-' + kursi.nomor"
                      v-model="kursi.terpilih"
                    />
                    <label :for="'kursi-' + kursi.nomor" style="font-size: 12px">{{
                      kursi.nomor
                    }}</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3 row align-items-center" >
            <label class="col-sm-2 col-form-label text-end fw-bold">Keterangan Gigi</label>
            <div class="col-sm-4">
              <input type="text" class="form-control bg-light" :value="keterangangigi" disabled />
            </div>
          </div>
    </FormPelayananPlanning>
    <FormPelayananStatusPasien v-if="currentTab === 'status_pasien'">
    </FormPelayananStatusPasien>
    </div>
    </div>
    </PelayananPasien>
    </div>
   </div>
    </AppLayouts>
</template>
<script setup>
import AppLayouts from '../../../Components/Layouts/AppLayouts.vue';
import PelayananPasien from '../../../Components/Layouts/RuangLayanan/PelayananPasien/PelayananPasien.vue';
import FormPelayananSubjective from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananSubjective.vue';
import FormPelayananObjective from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananObjective.vue';
import FormPelayananAssesment from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananAssesment.vue';
import FormPelayananPlanning from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananPlanning.vue';
import FormPelayananStatusPasien from '../../../Components/Layouts/RuangLayanan/PelayananPasien/FormPelayananStatusPasien.vue';
import { ref , computed} from 'vue';
import { watch } from 'vue'

const isMelayani = ref(false)
function handleMelayani(val) {
  isMelayani.value = val
}
const currentTab = ref('subjective')
const layoutKursi = ref([
  // Baris 1
  [
    { nomor: 18, terpilih: false },
    { nomor: 17, terpilih: false },
    { nomor: 16, terpilih: false },
    { nomor: 15, terpilih: false },
    { nomor: 14, terpilih: false },
    { nomor: 13, terpilih: false },
    { nomor: 12, terpilih: false },
    { nomor: 11, terpilih: false },
    { nomor: 21, terpilih: false },
    { nomor: 22, terpilih: false },
    { nomor: 23, terpilih: false },
    { nomor: 24, terpilih: false },
    { nomor: 25, terpilih: false },
    { nomor: 26, terpilih: false },
    { nomor: 27, terpilih: false },
    { nomor: 28, terpilih: false },
  ],
  // Baris 2
  [
    { nomor: 55, terpilih: false },
    { nomor: 54, terpilih: false },
    { nomor: 53, terpilih: false },
    { nomor: 52, terpilih: false },
    { nomor: 51, terpilih: false },
    { nomor: 61, terpilih: false },
    { nomor: 62, terpilih: false },
    { nomor: 63, terpilih: false },
    { nomor: 64, terpilih: false },
    { nomor: 65, terpilih: false },
  ],
  // Baris 3
  [
    { nomor: 85, terpilih: false },
    { nomor: 84, terpilih: false },
    { nomor: 83, terpilih: false },
    { nomor: 82, terpilih: false },
    { nomor: 81, terpilih: false },
    { nomor: 71, terpilih: false },
    { nomor: 72, terpilih: false },
    { nomor: 73, terpilih: false },
    { nomor: 74, terpilih: false },
    { nomor: 75, terpilih: false },
  ],
  // Baris 4
  [
    { nomor: 48, terpilih: false },
    { nomor: 47, terpilih: false },
    { nomor: 46, terpilih: false },
    { nomor: 45, terpilih: false },
    { nomor: 44, terpilih: false },
    { nomor: 43, terpilih: false },
    { nomor: 42, terpilih: false },
    { nomor: 41, terpilih: false },
    { nomor: 31, terpilih: false },
    { nomor: 32, terpilih: false },
    { nomor: 33, terpilih: false },
    { nomor: 34, terpilih: false },
    { nomor: 35, terpilih: false },
    { nomor: 36, terpilih: false },
    { nomor: 37, terpilih: false },
    { nomor: 38, terpilih: false },
  ],
])
console.log('ini kursi', layoutKursi.value.flat());

const keterangangigi = ref('');

const gigiTerpilih = computed(() => {
  return layoutKursi.value
    .flat() // gabungkan semua baris menjadi satu array
    .filter(item => item.terpilih) // hanya yang terpilih
    .map(item => item.nomor) // ambil nomornya
    .join(', ') // gabungkan jadi string
})
watch(gigiTerpilih, (baru) => {
  keterangangigi.value = baru,
  console.log('dipilih', baru)
});
const indicatorStyle = computed(() => {
  const tabs = {
    subjective: 0,
    objective: 1,
    assesment: 2,
    planning: 3,
    status_pasien : 4
  };
  
  const activeIndex = tabs[currentTab.value];
  const tabElements = document.querySelectorAll('.tab-item');
  
  if (tabElements.length > 0 && activeIndex >= 0) {
    const activeTab = tabElements[activeIndex];
    return {
      width: `${activeTab.offsetWidth}px`,
      transform: `translateX(${activeTab.offsetLeft}px)`
    };
  }
  
  return { display: 'none' };
});
</script>

<style>
.tab-item {
  border: none;
  background: transparent;
  font-weight: 500;
  border-radius: 0;
  position: relative;
  transition: all 0.3s ease;
  color: #6c757d;
}

.tab-item:hover {
  color: #495057 !important;
}

.tab-item.active {
  color: #0d6efd !important;
  font-weight: 600;
}

.tab-indicator {
  position: absolute;
  bottom: -16px; /* Adjust based on your padding */
  left: 0;
  height: 3px;
  background-color: #0d6efd;
  transition: all 0.3s ease;
  z-index: 1;
  border-radius: 3px 3px 0 0;
}
</style>