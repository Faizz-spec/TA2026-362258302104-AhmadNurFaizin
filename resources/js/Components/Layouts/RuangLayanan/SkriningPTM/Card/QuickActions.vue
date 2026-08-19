<template>
  <section class="quick-actions">
    <div class="quick-header">
      <div>
        <p class="quick-kicker">Aksi cepat</p>
        <h2>Dokumen dan Pemeriksaan</h2>
      </div>
      <span class="status-pill" :class="{ active: sudahMulai }">
        {{ sudahMulai ? 'Pemeriksaan aktif' : 'Menunggu mulai' }}
      </span>
    </div>

    <div class="action-grid">
      <button type="button" class="action-item">
        <span class="action-icon">
          <i class="bi bi-file-earmark-text"></i>
        </span>
        <span class="action-copy">
          <strong>Surat Keterangan</strong>
          <small>Dokumen keterangan pasien</small>
        </span>
      </button>

      <button type="button" class="action-item">
        <span class="action-icon">
          <i class="bi bi-send"></i>
        </span>
        <span class="action-copy">
          <strong>Surat Rujukan</strong>
          <small>Rujukan layanan lanjutan</small>
        </span>
      </button>

      <button type="button" class="action-item">
        <span class="action-icon">
          <i class="bi bi-clock-history"></i>
        </span>
        <span class="action-copy">
          <strong>Riwayat Pasien</strong>
          <small>Kunjungan dan pelayanan</small>
        </span>
      </button>

      <Link :href="cpptHref" class="action-item">
        <span class="action-icon">
          <i class="bi bi-file-text"></i>
        </span>
        <span class="action-copy">
          <strong>CPPT</strong>
          <small>Catatan perkembangan pasien</small>
        </span>
      </Link>

      <button
        v-if="!sudahMulai"
        type="button"
        class="action-item start-action"
        @click="$emit('mulai-pemeriksaan')"
      >
        <span class="action-icon">
          <i class="bi bi-play-circle"></i>
        </span>
        <span class="action-copy">
          <strong>Mulai Pemeriksaan</strong>
          <small>Buka form pemeriksaan PTM</small>
        </span>
      </button>

      <div v-else class="action-item started-action">
        <span class="action-icon">
          <i class="bi bi-check-circle"></i>
        </span>
        <span class="action-copy">
          <strong>Pemeriksaan Dimulai</strong>
          <small>Form pemeriksaan sudah aktif</small>
        </span>
      </div>
    </div>
  </section>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import { route } from 'ziggy-js';

  const props = defineProps({
    DataPasien: {
      type: Object,
      default: () => ({}),
    },
    sudahMulai: Boolean,
    title: String,
  });

  defineEmits(['mulai-pemeriksaan']);

  const cpptHref = computed(() => {
    return route('ruang-layanan.cppt', {
      idPoli: props.DataPasien?.kdPoli,
      idPasien: props.DataPasien?.ID,
    });
  });
</script>

<style scoped>
  .quick-actions {
    margin-top: 16px;
    margin-bottom: 16px;
    overflow: hidden;
    border: 1px solid #d9e5df;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
  }

  .quick-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    padding: 16px 18px;
    border-bottom: 1px solid #e5edf0;
    background: #f8fafc;
  }

  .quick-kicker {
    margin: 0 0 4px;
    color: #64748b;
    font-size: 0.76rem;
    font-weight: 750;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .quick-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 750;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #fff7ed;
    color: #9a3412;
    font-size: 0.8rem;
    font-weight: 750;
  }

  .status-pill.active {
    background: #dcfce7;
    color: #166534;
  }

  .action-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    padding: 16px;
  }

  .action-item {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    min-height: 78px;
    padding: 13px;
    border: 1px solid #e3ebef;
    border-radius: 8px;
    background: #fbfdff;
    color: inherit;
    text-align: left;
    text-decoration: none;
    transition:
      border-color 0.15s ease-in-out,
      box-shadow 0.15s ease-in-out,
      transform 0.15s ease-in-out;
  }

  button.action-item {
    width: 100%;
    cursor: pointer;
  }

  .action-item:hover {
    border-color: #b9dccd;
    background: #f6fbf8;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
    transform: translateY(-1px);
  }

  .action-icon {
    display: grid;
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    place-items: center;
    border-radius: 8px;
    background: #e7f5ef;
    color: #0f6b4c;
    font-size: 1.1rem;
  }

  .action-copy {
    min-width: 0;
  }

  .action-copy strong,
  .action-copy small {
    display: block;
  }

  .action-copy strong {
    color: #1e293b;
    font-size: 0.9rem;
    font-weight: 750;
    line-height: 1.35;
  }

  .action-copy small {
    margin-top: 3px;
    color: #64748b;
    font-size: 0.78rem;
    line-height: 1.35;
  }

  .start-action {
    border-color: #bbf7d0;
    background: #f0fdf4;
  }

  .start-action .action-icon,
  .started-action .action-icon {
    background: #0f6b4c;
    color: #ffffff;
  }

  .started-action {
    border-color: #bbf7d0;
    background: #f0fdf4;
    cursor: default;
  }

  .started-action:hover {
    box-shadow: none;
    transform: none;
  }

  .info-box strong {
    display: block;
    color: #1e293b;
    font-size: 0.9rem;
    font-weight: 700;
    line-height: 1.45;
    overflow-wrap: anywhere;
  }

  

  @media (max-width: 992px) {
    .action-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 576px) {
    .quick-header {
      align-items: stretch;
      flex-direction: column;
    }

    .action-grid {
      grid-template-columns: 1fr;
      padding: 14px;
    }
  }
</style>
