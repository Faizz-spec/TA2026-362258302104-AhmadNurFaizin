<template>
<div class="card-header shadow-sm border rounded-4 rounded-bottom-0 p-4 bg-white border-bottom">
  <div class="d-flex align-items-center flex-wrap gap-3">
    <div class="d-flex position-relative align-items-center">
      <div class="d-flex position-relative gap-2">
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'subjective' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="changeTab('subjective')">
          Subjective
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'objective' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="changeTab('objective')">
          Objective
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'assesment' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="changeTab('assesment')">
          Assesment
        </a>
        
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'planning' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="changeTab('planning')">
          Planning
        </a>
        <a href="#" 
           class="text-decoration-none btn tab-item px-3 py-2" 
           :class="currentTab === 'status_pasien' ? 'active text-primary' : 'text-muted'" 
           @click.prevent="changeTab('status_pasien')">
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
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
  currentTab: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['change-currentTab']);

function changeTab(tabName) {
  emit('change-currentTab', tabName);
}

const indicatorStyle = ref({ display: 'none' });

function updateIndicator() {
  const tabs = {
    subjective: 0,
    objective: 1,
    assesment: 2,
    planning: 3,
    status_pasien: 4
  };
  
  const activeIndex = tabs[props.currentTab];
  const tabElements = document.querySelectorAll('.tab-item');
  
  if (tabElements.length > 0 && activeIndex >= 0) {
    const activeTab = tabElements[activeIndex];
    indicatorStyle.value = {
      width: `${activeTab.offsetWidth}px`,
      transform: `translateX(${activeTab.offsetLeft}px)`,
      transition: 'all 0.3s ease'
    };
  } else {
    indicatorStyle.value = { display: 'none' };
  }
}

onMounted(() => {
  updateIndicator();
});

watch(() => props.currentTab, () => {
  updateIndicator();
});
</script>
