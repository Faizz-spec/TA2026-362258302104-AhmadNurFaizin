<template>
  <div class="card-header shadow-sm border rounded-4 rounded-bottom-0 p-4 bg-white border-bottom">
    <div class="d-flex align-items-center flex-wrap gap-3">
      <div class="d-flex position-relative align-items-center">
        <div class="d-flex position-relative gap-2">
          <a href="#" class="text-decoration-none btn tab-item px-3 py-2"
            :class="currentTab === 'anamnesis' ? 'active text-primary' : 'text-muted'"
            @click.prevent="changeTab('anamnesis')">
            Anamnesis Rawat Inap
          </a>

          <a href="#" class="text-decoration-none btn tab-item px-3 py-2"
            :class="currentTab === 'assesment' ? 'active text-primary' : 'text-muted'"
            @click.prevent="changeTab('assesment')">
            Assesment
          </a>

          <a href="#" class="text-decoration-none btn tab-item px-3 py-2"
            :class="currentTab === 'planning' ? 'active text-primary' : 'text-muted'"
            @click.prevent="changeTab('planning')">
            Planning
          </a>

          <a href="#" class="text-decoration-none btn tab-item px-3 py-2"
            :class="currentTab === 'visit' ? 'active text-primary' : 'text-muted'"
            @click.prevent="changeTab('visit')">
            Visit
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
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  currentTab: {
    type: String,
    required: true,
  },
  kdPoli: String
});

const emit = defineEmits(['change-currentTab']);

function changeTab(tabName) {
  emit('change-currentTab', tabName);
}

const indicatorStyle = ref({ display: 'none' });

function updateIndicator() {
  const tabElements = document.querySelectorAll('.tab-item');
  const activeTab = Array.from(tabElements).find(
    el => el.textContent.trim().toLowerCase().includes(props.currentTab.toLowerCase())
  );

  if (activeTab) {
    // indicatorStyle.value = {
    //   width: `${activeTab.offsetWidth}px`,
    //   transform: `translateX(${activeTab.offsetLeft}px)`,
    //   transition: 'all 0.3s ease'
    // };
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

<style scoped>
.tab-item {
  position: relative;
  transition: all 0.3s ease;
}

.tab-item.active {
  font-weight: 600;
}

.tab-indicator {
  position: absolute;
  bottom: -2px;
  height: 2px;
  background: #007bff;
  border-radius: 1px;
}
</style>
