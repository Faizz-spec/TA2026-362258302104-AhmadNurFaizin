<template>
  <AppLayout title="Home">
    <div class="db">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="topbar-left">
          <div class="topbar-eyebrow">
            <span class="pulse-dot"></span>
            Rawat Jalan · Live
          </div>

          <div class="topbar-title-row">
            <div class="topbar-title">{{ dashboardTitle }}</div>

            <div class="dashboard-switcher" role="tablist" aria-label="Jenis dashboard">
              <button
                type="button"
                class="dashboard-switcher-btn"
                :class="{ 'dashboard-switcher-btn-active': activeDashboard === 'kunjungan' }"
                :aria-selected="activeDashboard === 'kunjungan'"
                @click="setDashboard('kunjungan')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Kunjungan
              </button>

              <button
                type="button"
                class="dashboard-switcher-btn dashboard-switcher-btn-ptm"
                :class="{ 'dashboard-switcher-btn-active': activeDashboard === 'ptm' }"
                :aria-selected="activeDashboard === 'ptm'"
                @click="setDashboard('ptm')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                PTM
                <span class="dashboard-switcher-badge">8 Penyakit</span>
              </button>

              <button
                type="button"
                class="dashboard-switcher-btn dashboard-switcher-btn-mtbs"
                :class="{ 'dashboard-switcher-btn-active': activeDashboard === 'mtbs' }"
                :aria-selected="activeDashboard === 'mtbs'"
                @click="setDashboard('mtbs')"
              >
                MTBM + MTBS
              </button>
            </div>
          </div>
        </div>

        <div class="topbar-right">
          <div class="filter-row">
            <div class="date-group">
              <span class="date-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2"/>
                  <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
              </span>
              <input type="date" class="date-input" v-model="filters.start_date" />
              <span class="sep">→</span>
              <input type="date" class="date-input" v-model="filters.end_date" />
            </div>

            <button class="btn-apply" :disabled="!isRangeValid" @click="applyFilter">
              Tampilkan
            </button>

            <button class="btn-reset" @click="resetFilter">
              Reset
            </button>
          </div>

          <span v-if="validationMsg" class="filter-error">
            {{ validationMsg }}
          </span>
        </div>
      </div>

      <!-- PERIOD LABEL -->
      <div class="period-label">
        <span class="period-tag">{{ appliedFilters.start_date }}</span>
        <span class="period-divider">–</span>
        <span class="period-tag">{{ appliedFilters.end_date }}</span>
      </div>

      <!-- DASHBOARD KUNJUNGAN -->
      <div v-show="activeDashboard === 'kunjungan'">
        <!-- METRIC CARDS -->
        <div class="metrics">
          <div class="metric-card metric-main">
            <div class="metric-icon-wrap main-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>

            <div class="metric-body">
              <div class="metric-label">Total Kunjungan</div>
              <div class="metric-value">{{ totalVisit.toLocaleString('id-ID') }}</div>
              <div class="metric-sub">dalam periode ini</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(55,138,221,0.12); color:#378ADD">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
              </svg>
            </div>

            <div class="metric-body">
              <div class="metric-label">Laki-laki</div>
              <div class="metric-value" style="color:#378ADD">{{ gender.male.toLocaleString('id-ID') }}</div>
              <div class="metric-sub">{{ pct(gender.male, totalVisit) }}% dari total</div>
            </div>

            <div class="metric-bar-track">
              <div class="metric-bar-fill" :style="{ width: pct(gender.male, totalVisit) + '%', background: '#378ADD' }"></div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(212,83,126,0.12); color:#D4537E">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
              </svg>
            </div>

            <div class="metric-body">
              <div class="metric-label">Perempuan</div>
              <div class="metric-value" style="color:#D4537E">{{ gender.female.toLocaleString('id-ID') }}</div>
              <div class="metric-sub">{{ pct(gender.female, totalVisit) }}% dari total</div>
            </div>

            <div class="metric-bar-track">
              <div class="metric-bar-fill" :style="{ width: pct(gender.female, totalVisit) + '%', background: '#D4537E' }"></div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(59,109,17,0.12); color:#3B6D11">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 12l2 2 4-4"/>
                <rect x="3" y="3" width="18" height="18" rx="3"/>
              </svg>
            </div>

            <div class="metric-body">
              <div class="metric-label">BPJS</div>
              <div class="metric-value" style="color:#3B6D11">{{ payment.bpjs.toLocaleString('id-ID') }}</div>
              <div class="metric-sub">{{ pct(payment.bpjs, totalVisit) }}% dari total</div>
            </div>

            <div class="metric-bar-track">
              <div class="metric-bar-fill" :style="{ width: pct(payment.bpjs, totalVisit) + '%', background: '#3B6D11' }"></div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(186,117,23,0.12); color:#BA7517">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="9"/>
              </svg>
            </div>

            <div class="metric-body">
              <div class="metric-label">Pasien Baru</div>
              <div class="metric-value" style="color:#BA7517">{{ visit.baru.toLocaleString('id-ID') }}</div>
              <div class="metric-sub">{{ pct(visit.baru, totalVisit) }}% dari total</div>
            </div>

            <div class="metric-bar-track">
              <div class="metric-bar-fill" :style="{ width: pct(visit.baru, totalVisit) + '%', background: '#BA7517' }"></div>
            </div>
          </div>
        </div>

        <!-- BAR CHART -->
        <div class="section">
          <div class="panel chart-panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">Kunjungan Per Hari</div>
                <div class="panel-sub">Distribusi harian berdasarkan jenis kelamin</div>
              </div>

              <div class="chart-legend">
                <div class="legend-pill" style="--c:#378ADD">
                  <span class="legend-dot"></span>
                  Laki-laki
                  <strong>{{ totalMaleRange }}</strong>
                </div>

                <div class="legend-pill" style="--c:#D4537E">
                  <span class="legend-dot"></span>
                  Perempuan
                  <strong>{{ totalFemaleRange }}</strong>
                </div>
              </div>
            </div>

            <div class="chart-wrap">
              <canvas ref="barRef"></canvas>
            </div>
          </div>
        </div>

        <!-- DONUT CARDS -->
        <div class="donuts-row">
          <div class="donut-card">
            <div class="donut-header">
              <div class="donut-title">Jenis Kelamin</div>
              <div class="donut-total">{{ (gender.male + gender.female).toLocaleString('id-ID') }}</div>
            </div>

            <div class="donut-body">
              <div class="donut-wrap">
                <canvas ref="donutGenderRef"></canvas>
              </div>

              <div class="donut-legend">
                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#378ADD"></span>
                  <span class="dleg-label">Laki-laki</span>
                  <span class="dleg-val">{{ gender.male.toLocaleString('id-ID') }}</span>
                </div>

                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#D4537E"></span>
                  <span class="dleg-label">Perempuan</span>
                  <span class="dleg-val">{{ gender.female.toLocaleString('id-ID') }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="donut-card">
            <div class="donut-header">
              <div class="donut-title">Pembiayaan</div>
              <div class="donut-total">{{ (payment.bpjs + payment.non_bpjs).toLocaleString('id-ID') }}</div>
            </div>

            <div class="donut-body">
              <div class="donut-wrap">
                <canvas ref="donutPaymentRef"></canvas>
              </div>

              <div class="donut-legend">
                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#3B6D11"></span>
                  <span class="dleg-label">BPJS</span>
                  <span class="dleg-val">{{ payment.bpjs.toLocaleString('id-ID') }}</span>
                </div>

                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#888780"></span>
                  <span class="dleg-label">Non-BPJS</span>
                  <span class="dleg-val">{{ payment.non_bpjs.toLocaleString('id-ID') }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="donut-card">
            <div class="donut-header">
              <div class="donut-title">Tipe Kunjungan</div>
              <div class="donut-total">{{ (visit.baru + visit.lama).toLocaleString('id-ID') }}</div>
            </div>

            <div class="donut-body">
              <div class="donut-wrap">
                <canvas ref="donutVisitRef"></canvas>
              </div>

              <div class="donut-legend">
                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#185FA5"></span>
                  <span class="dleg-label">Baru</span>
                  <span class="dleg-val">{{ visit.baru.toLocaleString('id-ID') }}</span>
                </div>

                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#5DCAA5"></span>
                  <span class="dleg-label">Lama</span>
                  <span class="dleg-val">{{ visit.lama.toLocaleString('id-ID') }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="donut-card">
            <div class="donut-header">
              <div class="donut-title">Status Rujukan</div>
              <div class="donut-total">{{ (referral.internal + referral.rujukan).toLocaleString('id-ID') }}</div>
            </div>

            <div class="donut-body">
              <div class="donut-wrap">
                <canvas ref="donutReferralRef"></canvas>
              </div>

              <div class="donut-legend">
                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#534AB7"></span>
                  <span class="dleg-label">Internal</span>
                  <span class="dleg-val">{{ referral.internal.toLocaleString('id-ID') }}</span>
                </div>

                <div class="donut-leg-item">
                  <span class="dleg-color" style="background:#EF9F27"></span>
                  <span class="dleg-label">Rujukan</span>
                  <span class="dleg-val">{{ referral.rujukan.toLocaleString('id-ID') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TOP 10 PENYAKIT -->
        <div class="section">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="panel-title">10 Penyakit Terbesar</div>
                <div class="panel-sub">Berdasarkan jumlah kunjungan dalam periode ini</div>
              </div>
            </div>

            <div class="disease-list">
              <div v-for="(row, idx) in topDiseases" :key="idx" class="disease-row">
                <div class="dis-rank" :class="idx < 3 ? 'top-' + (idx + 1) : ''">
                  {{ idx + 1 }}
                </div>

                <div class="dis-code">{{ row.kode }}</div>

                <div class="dis-info">
                  <div class="dis-name">{{ row.nama }}</div>
                  <div class="dis-bar-track">
                    <div class="dis-bar-fill" :style="{ width: barWidth(row.total) + '%' }"></div>
                  </div>
                </div>

                <div class="dis-total">{{ row.total.toLocaleString('id-ID') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- DASHBOARD PTM -->
      <div v-show="activeDashboard === 'ptm'" class="section">
        <div class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">Dashboard PTM</div>
              <div class="panel-sub">Tab PTM sudah stay di halaman ini. Component PTM bisa dipanggil di sini nanti.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- DASHBOARD MTBM + MTBS (SUDAH DIGABUNG LANGSUNG) -->
      <div v-show="activeDashboard === 'mtbs'" class="section mtbs-inline">
        <div class="dashboard-wrap">
              <div v-if="mtbsError" class="alert alert-danger py-2 px-3 mb-3" role="alert">
                {{ mtbsError }}
              </div>
              <!-- HERO HEADER -->
              <div class="card hero-card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                <div class="hero-bg p-3 p-md-4">
                  <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                      <div class="hero-icon d-flex align-items-center justify-content-center">
                        <i class="bi bi-activity fs-4"></i>
                      </div>
                      <div>
                        <h4 class="mb-1 fw-bold text-white">Dashboard MTBM + MTBS</h4>
                        <div class="text-white-50 small">
                          Ringkasan cepat: KPI • Tren • Breakdown • Kasus Prioritas
                        </div>
                      </div>
                    </div>
        
                    <div class="d-flex gap-2 flex-wrap">
                      <button class="btn btn-light btn-sm fw-semibold" @click="fetchMtbsData" :disabled="mtbsLoading">
                        <span v-if="mtbsLoading" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="bi bi-arrow-repeat me-2"></i>
                        {{ mtbsLoading ? 'Memuat...' : 'Refresh' }}
                      </button>
                    </div>
                  </div>
        
                  <div class="mt-3 d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill text-bg-dark bg-opacity-25">
                      <i class="bi bi-calendar3 me-1"></i>
                      {{ mtbsFilters.date_from || '-' }} s/d {{ mtbsFilters.date_to || '-' }}
                    </span>
                    <span class="badge rounded-pill text-bg-dark bg-opacity-25">
                      <i class="bi bi-diagram-3 me-1"></i>
                      Poli: {{ mtbsFilters.kdPoli || '-' }}
                    </span>
                    <span class="badge rounded-pill text-bg-dark bg-opacity-25">
                      <i class="bi bi-hospital me-1"></i>
                      Pusk: {{ mtbsSelectedPuskesmasName }}
                    </span>
                    <span class="badge rounded-pill text-bg-dark bg-opacity-25">
                      <i class="bi bi-check2-square me-1"></i>
                      Dilayani: {{ mtbsFilters.served }}
                    </span>
                  </div>
                </div>
              </div>
        
              <!-- FILTERS -->
              <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                  <div class="fw-semibold d-flex align-items-center gap-2">
                    <span class="filter-dot"></span>
                    Filter
                  </div>
                  <div class="text-muted small">
                    Enter di keyword = apply cepat
                  </div>
                </div>
        
                <div class="row g-2 align-items-end">
                  <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" v-model="mtbsFilters.date_from" />
                  </div>
        
                  <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" v-model="mtbsFilters.date_to" />
                  </div>
        
                  <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Poli</label>
                    <input type="text" class="form-control form-control-sm" v-model="mtbsFilters.kdPoli" />
                  </div>
        
                  <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Puskesmas</label>
                    <select
                      v-model="mtbsFilters.puskId"
                      class="form-select form-select-sm"
                      :disabled="mtbsLoading || mtbsPuskesmasOptions.length === 0"
                      @change="applyMtbsFilter"
                    >
                      <option value="">Semua Puskesmas</option>
                      <option
                        v-for="u in mtbsPuskesmasOptions"
                        :key="u.value"
                        :value="u.value"
                      >
                        {{ u.label }}
                      </option>
                    </select>
                    <small v-if="mtbsPuskesmasOptions.length === 0" class="text-danger">
                      Daftar puskesmas belum ditemukan dari tabel unit_profiles.
                    </small>
                  </div>
        
                  <div class="col-12 col-md-1">
                    <label class="form-label fw-semibold">Dilayani</label>
                    <select class="form-select form-select-sm" v-model="mtbsFilters.served">
                      <option value="all">Semua</option>
                      <option value="served">Sudah</option>
                      <option value="unserved">Belum</option>
                    </select>
                  </div>
        
                  <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Keyword</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                      <input
                        type="text"
                        class="form-control"
                        v-model="mtbsFilters.keyword"
                        placeholder="Nama / MR / NIK"
                        @keyup.enter="applyMtbsFilter"
                      />
                    </div>
                  </div>
        
                  <div class="col-12 d-flex gap-2 mt-2 flex-wrap">
                    <button class="btn btn-primary btn-sm fw-semibold" @click="applyMtbsFilter" :disabled="mtbsLoading">
                      <i class="bi bi-funnel me-2"></i>Terapkan
                    </button>
                    <button class="btn btn-outline-danger btn-sm fw-semibold" @click="resetMtbsFilter" :disabled="mtbsLoading">
                      <i class="bi bi-x-circle me-2"></i>Reset
                    </button>
                  </div>
                </div>
        
                <div class="text-muted small mt-2" v-if="mtbsDebug">
                  <i class="bi bi-bug me-1"></i>
                  Periode: {{ mtbsDebug.date_from }} s/d {{ mtbsDebug.date_to }}
                  | Poli: {{ mtbsDebug.kdPoli }}
                  | PuskId: {{ mtbsDebug.puskId || '-' }}
                  | Dilayani: {{ mtbsDebug.served }}
                  | Opsi Puskesmas: {{ mtbsDebug.puskesmas_count ?? mtbsPuskesmasOptions.length }}
                </div>
              </div>
        
              <!-- KPI -->
              <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-blue border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">Total Kunjungan</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.total ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-bar-chart-line"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-indigo border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">MTBS Terisi</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.mtbs_filled ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-clipboard2-check"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-green border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">MTBM Terisi</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.mtbm_filled ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-red border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">Belum Dilayani</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.unserved ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-sky border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">Laki-laki</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.laki_laki ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-gender-male"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-pink border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">Perempuan</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.perempuan ?? 0 }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-gender-female"></i></div>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-md-3">
                  <div class="card stat-card stat-amber border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <div class="text-muted small fw-semibold">Rata-rata Umur</div>
                        <div class="fs-3 fw-bold">{{ mtbsKpi.avg_umur ?? '-' }}</div>
                      </div>
                      <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                  </div>
                </div>
        
        
              </div>
        
              <!-- CHARTS: TREN -->
              <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Tren Kunjungan per Hari (MTBS vs MTBM)</div>
                      <span class="badge rounded-pill text-bg-light border">Line</span>
                    </div>
                    <div class="ratio ratio-16x9 chart-wrap">
                      <canvas ref="chartTrendRef"></canvas>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-lg-6">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Tren Kegawatan per Hari (Stacked)</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-16x9 chart-wrap">
                      <canvas ref="chartSevRef"></canvas>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- BREAKDOWN / TOP -->
              <div class="row g-3 mb-3">
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top 10 Puskesmas</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartTopPuskRef"></canvas>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top 10 Klasifikasi Global MTBS</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartTopMtbsRef"></canvas>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top 10 Keluhan Utama (MTBM)</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartTopKeluhanRef"></canvas>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- MTBM DETAIL TOP 3 -->
              <div class="row g-3 mb-3">
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top Klas Infeksi (MTBM)</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartMtbmInfeksiRef"></canvas>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top Klas Diare (MTBM)</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartMtbmDiareRef"></canvas>
                    </div>
                  </div>
                </div>
        
                <div class="col-12 col-lg-4">
                  <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="fw-semibold">Top Klas Menyusu/BB (MTBM)</div>
                      <span class="badge rounded-pill text-bg-light border">Bar</span>
                    </div>
                    <div class="ratio ratio-4x3 chart-wrap">
                      <canvas ref="chartMtbmMenyusuRef"></canvas>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- TABLE PRIORITAS -->
              <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                  <div class="fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-diamond text-danger"></i>
                    Kasus Prioritas (MTBM merah / MTBS berat)
                  </div>
                  <div class="text-muted small">Max 50 data terbaru</div>
                </div>
        
                <div class="table-responsive table-modern">
                  <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-head-sticky">
                      <tr>
                        <th>Tanggal</th>
                        <th>MR</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Poli</th>
                        <th>Puskesmas</th>
                        <th>RR</th>
                        <th>Suhu</th>
                        <th>SpO2</th>
                        <th>MTBM Global</th>
                        <th>MTBS Status</th>
                        <th>Sudah Dilayani</th>
                      </tr>
                    </thead>
        
                    <tbody>
                      <tr v-for="r in mtbsPrioritas" :key="r.kunjungan_id">
                        <td class="text-nowrap">{{ formatMtbsDate(r.tglPelayanan) }}</td>
                        <td class="text-nowrap">{{ r.NO_MR }}</td>
                        <td class="fw-semibold">{{ r.NAMA_LGKP }}</td>
                        <td class="text-nowrap">{{ r.NIK }}</td>
                        <td class="text-nowrap">{{ r.nmPoli ?? '-' }}</td>
                        <td>{{ r.puskesmas ?? '-' }}</td>
                        <td class="text-nowrap">{{ mtbsPickAny(r, ['mtbs_rr', 'mtbm_rr']) }}</td>
                        <td class="text-nowrap">{{ mtbsPickAny(r, ['mtbs_suhu', 'mtbm_suhu']) }}</td>
                        <td class="text-nowrap">{{ mtbsPickAny(r, ['mtbs_spo2', 'mtbm_spo2']) }}</td>
                        <td class="text-nowrap">
                          <span class="badge" :class="mtbsBadgeClass(r.mtbm_global)">
                            {{ r.mtbm_global ?? '-' }}
                          </span>
                        </td>
                        <td>
                          <span class="badge bg-info text-dark">{{ r.mtbs_status ?? '-' }}</span>
                        </td>
                        <td class="text-nowrap">
                          <span class="badge" :class="r.sudahDilayani ? 'bg-success' : 'bg-secondary'">
                            <i class="bi" :class="r.sudahDilayani ? 'bi-check2-circle' : 'bi-clock'"></i>
                            {{ r.sudahDilayani ? 'Sudah' : 'Belum' }}
                          </span>
                        </td>
                      </tr>
        
                      <tr v-if="mtbsPrioritas.length === 0">
                        <td colspan="12" class="text-center text-muted py-4">
                          <i class="bi bi-inbox me-2"></i> Tidak ada kasus mtbsPrioritas.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
        
                <div class="text-muted small mt-2" v-if="mtbsDebug">
                  Collation: {{ mtbsDebug.collation_forced }}
                </div>
              </div>
            </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import AppLayout from '@/Components/Layouts/AppLayouts.vue'

const page = usePage()
const initial = page.props

const DASHBOARD_TAB_KEY = 'simpus.home.activeDashboard'
const allowedDashboards = ['kunjungan', 'ptm', 'mtbs']
const activeDashboard = ref('kunjungan')

let pageAlive = true
let ChartLib = null

function setDashboard(tab) {
  if (!allowedDashboards.includes(tab)) return

  activeDashboard.value = tab

  try {
    window.sessionStorage.setItem(DASHBOARD_TAB_KEY, tab)
  } catch (error) {
    console.warn('Gagal menyimpan tab dashboard:', error)
  }
}

const dashboardTitle = computed(() => {
  if (activeDashboard.value === 'ptm') return 'Dashboard PTM'
  if (activeDashboard.value === 'mtbs') return 'Dashboard MTBM + MTBS'
  return 'Dashboard Kunjungan'
})

function localDateString(date = new Date()) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const today = initial.serverNow ?? localDateString()
const initialStart = initial.filters?.start_date ?? today
const initialEnd = initial.filters?.end_date ?? today

const filters = ref({
  start_date: initialStart,
  end_date: initialEnd,
})

const appliedFilters = ref({
  start_date: initialStart,
  end_date: initialEnd,
})

const validationMsg = ref('')

const isRangeValid = computed(() => {
  const { start_date: startDate, end_date: endDate } = filters.value
  return Boolean(startDate && endDate && startDate <= endDate)
})

const perDayAll = ref(initial.perDayAll ?? [])
const gender = ref(initial.gender ?? { male: 0, female: 0 })
const payment = ref(initial.payment ?? { bpjs: 0, non_bpjs: 0 })
const visit = ref(initial.visit ?? { baru: 0, lama: 0 })
const referral = ref(initial.referral ?? { internal: 0, rujukan: 0 })
const topDiseases = ref(initial.topDiseases ?? [])

const perDay = computed(() => perDayAll.value)
const totalMaleRange = computed(() => (
  perDay.value.reduce((total, item) => total + Number(item.male || 0), 0)
))
const totalFemaleRange = computed(() => (
  perDay.value.reduce((total, item) => total + Number(item.female || 0), 0)
))
const totalVisit = computed(() => Number(gender.value.male || 0) + Number(gender.value.female || 0))

const maxDiseaseTotal = computed(() => {
  if (!topDiseases.value.length) return 1
  return Math.max(...topDiseases.value.map((item) => Number(item.total || 0)), 1)
})

function pct(value, total) {
  if (!total) return '0,0'
  return ((Number(value || 0) / Number(total)) * 100).toFixed(1).replace('.', ',')
}

function barWidth(total) {
  return Math.round((Number(total || 0) / maxDiseaseTotal.value) * 100)
}

function applyFilter() {
  validationMsg.value = ''

  if (!isRangeValid.value) {
    validationMsg.value = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
    return
  }

  const { start_date: startDate, end_date: endDate } = filters.value

  router.get(route('home.home'), {
    start_date: startDate,
    end_date: endDate,
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: async () => {
      const currentProps = page.props
      const appliedStart = currentProps.filters?.start_date ?? startDate
      const appliedEnd = currentProps.filters?.end_date ?? endDate

      filters.value = {
        start_date: appliedStart,
        end_date: appliedEnd,
      }

      appliedFilters.value = {
        start_date: appliedStart,
        end_date: appliedEnd,
      }

      perDayAll.value = currentProps.perDayAll ?? []
      gender.value = currentProps.gender ?? { male: 0, female: 0 }
      payment.value = currentProps.payment ?? { bpjs: 0, non_bpjs: 0 }
      visit.value = currentProps.visit ?? { baru: 0, lama: 0 }
      referral.value = currentProps.referral ?? { internal: 0, rujukan: 0 }
      topDiseases.value = currentProps.topDiseases ?? []

      await nextTick()

      if (activeDashboard.value === 'kunjungan') {
        buildMainCharts()
      }
    },
    onError: () => {
      validationMsg.value = 'Dashboard gagal dimuat. Silakan coba lagi.'
    },
  })
}

function resetFilter() {
  filters.value = {
    start_date: today,
    end_date: today,
  }
  applyFilter()
}

// =========================================================
// CHART DASHBOARD KUNJUNGAN
// =========================================================
const barRef = ref(null)
const donutGenderRef = ref(null)
const donutPaymentRef = ref(null)
const donutVisitRef = ref(null)
const donutReferralRef = ref(null)

let barChart = null
let donutGenderChart = null
let donutPaymentChart = null
let donutVisitChart = null
let donutReferralChart = null

function destroyMainCharts() {
  barChart?.destroy()
  donutGenderChart?.destroy()
  donutPaymentChart?.destroy()
  donutVisitChart?.destroy()
  donutReferralChart?.destroy()

  barChart = null
  donutGenderChart = null
  donutPaymentChart = null
  donutVisitChart = null
  donutReferralChart = null
}

function dynamicBarSizing(count) {
  const barPct = Math.max(0.15, Math.min(0.85, 12 / Math.max(1, count)))
  const catPct = Math.max(0.4, Math.min(0.9, 18 / Math.max(1, count)))
  const maxTicks = Math.min(14, Math.max(6, Math.floor(120 / Math.log2(Math.max(4, count)))))
  return { barPct, catPct, maxTicks }
}

function buildBarConfig() {
  const count = perDay.value.length
  const { barPct, catPct, maxTicks } = dynamicBarSizing(count)
  const gridColor = 'rgba(0,0,0,0.05)'
  const tickColor = 'rgba(0,0,0,0.38)'

  return {
    type: 'bar',
    data: {
      labels: perDay.value.map((item) => item.date),
      datasets: [
        {
          label: 'Laki-laki',
          data: perDay.value.map((item) => Number(item.male || 0)),
          backgroundColor: '#378ADD',
          borderWidth: 0,
          barPercentage: barPct,
          categoryPercentage: catPct,
          stack: 'v',
          borderRadius: 0,
        },
        {
          label: 'Perempuan',
          data: perDay.value.map((item) => Number(item.female || 0)),
          backgroundColor: '#D4537E',
          borderWidth: 0,
          barPercentage: barPct,
          categoryPercentage: catPct,
          stack: 'v',
          borderRadius: { topLeft: 5, topRight: 5 },
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15,23,42,0.92)',
          titleColor: '#e2e8f0',
          bodyColor: '#94a3b8',
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            footer: (items) => `Total: ${items.reduce((sum, item) => sum + item.parsed.y, 0)}`,
          },
        },
      },
      scales: {
        x: {
          stacked: true,
          grid: { color: gridColor },
          border: { display: false },
          ticks: {
            color: tickColor,
            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
            autoSkip: true,
            maxTicksLimit: maxTicks,
            maxRotation: 0,
            callback(value, index) {
              const step = Math.max(1, Math.ceil(count / maxTicks))
              return index % step === 0 ? (perDay.value[index]?.date ?? '') : ''
            },
          },
        },
        y: {
          stacked: true,
          beginAtZero: true,
          grace: '8%',
          grid: { color: gridColor },
          border: { display: false },
          ticks: {
            color: tickColor,
            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
            precision: 0,
          },
        },
      },
      animation: { duration: 400, easing: 'easeOutQuart' },
    },
  }
}

function donutOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '72%',
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: 'rgba(15,23,42,0.92)',
        titleColor: '#e2e8f0',
        bodyColor: '#94a3b8',
        padding: 10,
        cornerRadius: 8,
        callbacks: {
          label: (context) => ` ${context.label}: ${context.parsed.toLocaleString('id-ID')}`,
        },
      },
    },
    animation: { duration: 400, easing: 'easeOutQuart' },
  }
}

function createDonutChart(canvas, labels, data, colors) {
  if (!ChartLib || !canvas) return null

  return new ChartLib(canvas, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: colors,
        borderWidth: 0,
        hoverOffset: 4,
      }],
    },
    options: donutOptions(),
  })
}

function buildMainCharts() {
  if (!pageAlive || !ChartLib || activeDashboard.value !== 'kunjungan') return
  if (!barRef.value || !donutGenderRef.value || !donutPaymentRef.value || !donutVisitRef.value || !donutReferralRef.value) return

  destroyMainCharts()

  barChart = new ChartLib(barRef.value, buildBarConfig())
  donutGenderChart = createDonutChart(
    donutGenderRef.value,
    ['Laki-laki', 'Perempuan'],
    [Number(gender.value.male || 0), Number(gender.value.female || 0)],
    ['#378ADD', '#D4537E'],
  )
  donutPaymentChart = createDonutChart(
    donutPaymentRef.value,
    ['BPJS', 'Non-BPJS'],
    [Number(payment.value.bpjs || 0), Number(payment.value.non_bpjs || 0)],
    ['#3B6D11', '#888780'],
  )
  donutVisitChart = createDonutChart(
    donutVisitRef.value,
    ['Baru', 'Lama'],
    [Number(visit.value.baru || 0), Number(visit.value.lama || 0)],
    ['#185FA5', '#5DCAA5'],
  )
  donutReferralChart = createDonutChart(
    donutReferralRef.value,
    ['Internal', 'Rujukan'],
    [Number(referral.value.internal || 0), Number(referral.value.rujukan || 0)],
    ['#534AB7', '#EF9F27'],
  )
}

// =========================================================
// DASHBOARD MTBM + MTBS — SUDAH MENYATU DI FILE INI
// =========================================================
const mtbsLoading = ref(false)
const mtbsError = ref('')
const mtbsDebug = ref(null)
const mtbsLoaded = ref(false)
const mtbsPuskesmasFromApi = ref([])

function normalizePuskesmas(items) {
  if (!Array.isArray(items)) return []

  const unique = new Map()

  items.forEach((item) => {
    const rawValue = item?.value ?? item?.unit_id ?? item?.id
    const rawLabel = item?.label ?? item?.nama_unit ?? item?.nama

    if (
      rawValue === undefined
      || rawValue === null
      || String(rawValue).trim() === ''
      || rawLabel === undefined
      || rawLabel === null
      || String(rawLabel).trim() === ''
    ) {
      return
    }

    const value = String(rawValue).trim()

    unique.set(value, {
      value,
      label: String(rawLabel).trim(),
      unit_id: item?.unit_id ?? item?.id ?? rawValue,
      kode_puskesmas: item?.kode_puskesmas ?? null,
    })
  })

  return Array.from(unique.values()).sort((first, second) => (
    first.label.localeCompare(second.label, 'id')
  ))
}

const mtbsPuskesmasOptions = computed(() => {
  const candidates = [
    ...mtbsPuskesmasFromApi.value,
    ...(Array.isArray(page.props?.puskesmas) ? page.props.puskesmas : []),
  ]

  return normalizePuskesmas(candidates)
})

const mtbsKpi = ref({
  total: 0,
  mtbs_filled: 0,
  mtbm_filled: 0,
  unserved: 0,
  laki_laki: 0,
  perempuan: 0,
  avg_umur: null,
})

const mtbsTrend = ref([])
const mtbsSeverity = ref([])
const mtbsTop = ref({
  puskesmas: [],
  mtbs_klasifikasi_global: [],
  mtbm_infeksi: [],
  mtbm_diare: [],
  mtbm_menyusu_bb: [],
  keluhan_utama: [],
})
const mtbsPrioritas = ref([])

const initialMtbsFilters = initial.filters ?? {}
const mtbsFilters = ref({
  date_from: appliedFilters.value.start_date,
  date_to: appliedFilters.value.end_date,
  kdPoli: String(initialMtbsFilters.kdPoli ?? '003'),
  puskId: initialMtbsFilters.puskId === null || initialMtbsFilters.puskId === undefined
    ? ''
    : String(initialMtbsFilters.puskId),
  served: String(initialMtbsFilters.served ?? 'all'),
  keyword: initialMtbsFilters.keyword ?? '',
})

const mtbsSelectedPuskesmasName = computed(() => {
  if (!mtbsFilters.value.puskId) return 'Semua Puskesmas'

  return mtbsPuskesmasOptions.value.find(
    (item) => item.value === String(mtbsFilters.value.puskId),
  )?.label || `Unit ID ${mtbsFilters.value.puskId}`
})

const formatMtbsDate = (date) => (date ? String(date).slice(0, 10) : '-')

function mtbsPickAny(object, keys, fallback = '-') {
  for (const key of keys) {
    const value = object?.[key]
    if (value !== null && value !== undefined && value !== '') return value
  }

  return fallback
}

function mtbsBadgeClass(value) {
  const normalized = String(value || '').toLowerCase()

  if (normalized === 'merah') return 'bg-danger'
  if (normalized === 'kuning') return 'bg-warning text-dark'
  if (normalized === 'hijau') return 'bg-success'

  return 'bg-secondary'
}

const chartTrendRef = ref(null)
const chartSevRef = ref(null)
const chartTopPuskRef = ref(null)
const chartTopMtbsRef = ref(null)
const chartTopKeluhanRef = ref(null)
const chartMtbmInfeksiRef = ref(null)
const chartMtbmDiareRef = ref(null)
const chartMtbmMenyusuRef = ref(null)

let cTrend = null
let cSev = null
let cTopPusk = null
let cTopMtbs = null
let cTopKeluhan = null
let cInf = null
let cDia = null
let cMen = null

const mtbsChartRefs = [
  chartTrendRef,
  chartSevRef,
  chartTopPuskRef,
  chartTopMtbsRef,
  chartTopKeluhanRef,
  chartMtbmInfeksiRef,
  chartMtbmDiareRef,
  chartMtbmMenyusuRef,
]

function destroyMtbsCharts() {
  ;[cTrend, cSev, cTopPusk, cTopMtbs, cTopKeluhan, cInf, cDia, cMen].forEach((chart) => {
    chart?.destroy()
  })

  cTrend = null
  cSev = null
  cTopPusk = null
  cTopMtbs = null
  cTopKeluhan = null
  cInf = null
  cDia = null
  cMen = null
}

function areMtbsCanvasesReady() {
  return pageAlive && mtbsChartRefs.every((canvasRef) => Boolean(canvasRef.value))
}

function createSafeMtbsChart(canvasRef, config) {
  if (!pageAlive || !ChartLib || !canvasRef?.value) return null
  return new ChartLib(canvasRef.value, config)
}

function mtbsBaseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { labels: { boxWidth: 10, usePointStyle: true } },
      tooltip: { intersect: false, mode: 'index' },
    },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } },
    },
  }
}

async function buildMtbsCharts() {
  await nextTick()

  if (
    activeDashboard.value !== 'mtbs'
    || !ChartLib
    || !areMtbsCanvasesReady()
  ) {
    return
  }

  destroyMtbsCharts()

  const trendLabels = mtbsTrend.value.map((item) => item.tgl)

  cTrend = createSafeMtbsChart(chartTrendRef, {
    type: 'line',
    data: {
      labels: trendLabels,
      datasets: [
        {
          label: 'MTBS',
          data: mtbsTrend.value.map((item) => Number(item.mtbs || 0)),
          tension: 0.25,
          borderColor: '#0d6efd',
          backgroundColor: 'rgba(13,110,253,.15)',
          pointRadius: 2,
          fill: true,
        },
        {
          label: 'MTBM',
          data: mtbsTrend.value.map((item) => Number(item.mtbm || 0)),
          tension: 0.25,
          borderColor: '#198754',
          backgroundColor: 'rgba(25,135,84,.15)',
          pointRadius: 2,
          fill: true,
        },
      ],
    },
    options: mtbsBaseOptions(),
  })

  cSev = createSafeMtbsChart(chartSevRef, {
    type: 'bar',
    data: {
      labels: mtbsSeverity.value.map((item) => item.tgl),
      datasets: [
        {
          label: 'MTBM Merah',
          data: mtbsSeverity.value.map((item) => Number(item.mtbm_merah || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(220,53,69,.65)',
        },
        {
          label: 'MTBM Kuning',
          data: mtbsSeverity.value.map((item) => Number(item.mtbm_kuning || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(255,193,7,.70)',
        },
        {
          label: 'MTBM Hijau',
          data: mtbsSeverity.value.map((item) => Number(item.mtbm_hijau || 0)),
          stack: 'mtbm',
          backgroundColor: 'rgba(25,135,84,.65)',
        },
        {
          label: 'MTBS Berat',
          data: mtbsSeverity.value.map((item) => Number(item.mtbs_berat || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(102,16,242,.65)',
        },
        {
          label: 'MTBS Sedang',
          data: mtbsSeverity.value.map((item) => Number(item.mtbs_sedang || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(13,202,240,.60)',
        },
        {
          label: 'MTBS Ringan',
          data: mtbsSeverity.value.map((item) => Number(item.mtbs_ringan || 0)),
          stack: 'mtbs',
          backgroundColor: 'rgba(253,126,20,.65)',
        },
      ],
    },
    options: {
      ...mtbsBaseOptions(),
      scales: {
        x: { stacked: true, ticks: { maxRotation: 0, autoSkip: true } },
        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } },
      },
    },
  })

  function createTopBar(canvasRef, labels, values, labelName, color) {
    return createSafeMtbsChart(canvasRef, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: labelName,
          data: values,
          backgroundColor: color,
          borderRadius: 8,
        }],
      },
      options: {
        ...mtbsBaseOptions(),
        plugins: {
          ...mtbsBaseOptions().plugins,
          legend: { display: false },
        },
        scales: {
          x: { ticks: { autoSkip: true, maxRotation: 0 } },
          y: { beginAtZero: true, ticks: { precision: 0 } },
        },
      },
    })
  }

  cTopPusk = createTopBar(
    chartTopPuskRef,
    (mtbsTop.value.puskesmas || []).map((item) => item.puskesmas),
    (mtbsTop.value.puskesmas || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(13,110,253,.65)',
  )

  cTopMtbs = createTopBar(
    chartTopMtbsRef,
    (mtbsTop.value.mtbs_klasifikasi_global || []).map((item) => item.label),
    (mtbsTop.value.mtbs_klasifikasi_global || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(102,16,242,.60)',
  )

  cTopKeluhan = createTopBar(
    chartTopKeluhanRef,
    (mtbsTop.value.keluhan_utama || []).map((item) => item.label),
    (mtbsTop.value.keluhan_utama || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(214,51,132,.60)',
  )

  cInf = createTopBar(
    chartMtbmInfeksiRef,
    (mtbsTop.value.mtbm_infeksi || []).map((item) => item.label),
    (mtbsTop.value.mtbm_infeksi || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(25,135,84,.65)',
  )

  cDia = createTopBar(
    chartMtbmDiareRef,
    (mtbsTop.value.mtbm_diare || []).map((item) => item.label),
    (mtbsTop.value.mtbm_diare || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(255,193,7,.70)',
  )

  cMen = createTopBar(
    chartMtbmMenyusuRef,
    (mtbsTop.value.mtbm_menyusu_bb || []).map((item) => item.label),
    (mtbsTop.value.mtbm_menyusu_bb || []).map((item) => Number(item.total || 0)),
    'Total',
    'rgba(13,202,240,.60)',
  )
}

let mtbsRequestController = null
let mtbsRequestNumber = 0

async function fetchMtbsData() {
  mtbsError.value = ''

  if (!mtbsFilters.value.date_from || !mtbsFilters.value.date_to) {
    mtbsError.value = 'Tanggal dashboard MTBS/MTBM belum lengkap.'
    return
  }

  if (mtbsFilters.value.date_from > mtbsFilters.value.date_to) {
    mtbsError.value = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
    return
  }

  mtbsRequestController?.abort()
  mtbsRequestController = new AbortController()
  const currentRequest = ++mtbsRequestNumber

  mtbsLoading.value = true

  try {
    const response = await axios.get(route('dashboard.mtbm_mtbs.data'), {
      signal: mtbsRequestController.signal,
      params: {
        ...mtbsFilters.value,
        puskId: mtbsFilters.value.puskId || '',
        kdPoli: String(mtbsFilters.value.kdPoli || '003'),
      },
    })

    const payload = response?.data?.data ?? response?.data ?? {}

    if (!pageAlive || currentRequest !== mtbsRequestNumber) return

    if (Array.isArray(payload.puskesmas)) {
      mtbsPuskesmasFromApi.value = payload.puskesmas
    }

    mtbsKpi.value = payload.kpi || {
      total: 0,
      mtbs_filled: 0,
      mtbm_filled: 0,
      unserved: 0,
      laki_laki: 0,
      perempuan: 0,
      avg_umur: null,
    }

    mtbsTrend.value = payload.trend || []
    mtbsSeverity.value = payload.severity || []
    mtbsTop.value = {
      puskesmas: payload.top?.puskesmas || [],
      mtbs_klasifikasi_global: payload.top?.mtbs_klasifikasi_global || [],
      mtbm_infeksi: payload.top?.mtbm_infeksi || [],
      mtbm_diare: payload.top?.mtbm_diare || [],
      mtbm_menyusu_bb: payload.top?.mtbm_menyusu_bb || [],
      keluhan_utama: payload.top?.keluhan_utama || [],
    }
    mtbsPrioritas.value = payload.prioritas || []
    mtbsDebug.value = payload.debug || null
    mtbsLoaded.value = true

    if (activeDashboard.value === 'mtbs') {
      await buildMtbsCharts()
    }
  } catch (error) {
    if (error?.code === 'ERR_CANCELED' || error?.name === 'CanceledError') return

    console.error('DASHBOARD MTBS/MTBM ERROR:', error)
    console.log('SERVER DATA:', error?.response?.data ?? null)
    mtbsError.value = error?.response?.data?.message
      || 'Gagal memuat dashboard MTBS/MTBM. Cek console dan response server.'
  } finally {
    if (pageAlive && currentRequest === mtbsRequestNumber) {
      mtbsLoading.value = false
    }
  }
}

function applyMtbsFilter() {
  fetchMtbsData()
}

function resetMtbsFilter() {
  mtbsFilters.value = {
    date_from: appliedFilters.value.start_date,
    date_to: appliedFilters.value.end_date,
    kdPoli: '003',
    puskId: '',
    served: 'all',
    keyword: '',
  }

  fetchMtbsData()
}

async function renderActiveDashboard() {
  await nextTick()

  if (!ChartLib || !pageAlive) return

  if (activeDashboard.value === 'kunjungan') {
    buildMainCharts()
    return
  }

  if (activeDashboard.value === 'mtbs') {
    if (!mtbsLoaded.value) {
      await fetchMtbsData()
    } else {
      await buildMtbsCharts()
    }
  }
}

watch(activeDashboard, () => {
  renderActiveDashboard()
})

watch(
  () => [appliedFilters.value.start_date, appliedFilters.value.end_date],
  ([newStart, newEnd], [oldStart, oldEnd]) => {
    if (!newStart || !newEnd) return
    if (newStart === oldStart && newEnd === oldEnd) return

    mtbsFilters.value.date_from = newStart
    mtbsFilters.value.date_to = newEnd
    mtbsLoaded.value = false

    if (activeDashboard.value === 'mtbs') {
      fetchMtbsData()
    }
  },
)

onMounted(async () => {
  pageAlive = true

  try {
    const savedTab = window.sessionStorage.getItem(DASHBOARD_TAB_KEY)

    if (allowedDashboards.includes(savedTab)) {
      activeDashboard.value = savedTab
    }
  } catch (error) {
    console.warn('Gagal membaca tab dashboard:', error)
  }

  ChartLib = (await import('chart.js/auto')).default
  await renderActiveDashboard()
})

onBeforeUnmount(() => {
  pageAlive = false
  mtbsRequestController?.abort()
  destroyMainCharts()
  destroyMtbsCharts()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

.db {
  font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
  color: #0f172a;
  background: #f5f6fa;
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

.topbar-left {
  flex: 1 1 520px;
  min-width: 0;
}

.topbar-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  min-width: 0;
}

.dashboard-switcher {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 4px;
  width: fit-content;
  max-width: 100%;
  overflow: visible;
  background: #f1f5f9;
  border: 0;
  border-radius: 10px;
  padding: 3px;
}

.dashboard-switcher-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
  gap: 5px;
  min-height: 32px;
  font-family: inherit;
  font-size: 12.5px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #64748b;
  text-decoration: none;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
  user-select: none;
  visibility: visible;
  opacity: 1;
}

.dashboard-switcher-btn:hover:not(.dashboard-switcher-btn-active) {
  background: rgba(255,255,255,0.7);
  color: #334155;
}

.dashboard-switcher-btn-active {
  background: #fff;
  color: #185FA5;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

.dashboard-switcher-btn-ptm:hover {
  background: #fff;
  color: #E05C5C;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

.dashboard-switcher-btn-mtbs {
  display: inline-flex !important;
  visibility: visible !important;
  opacity: 1 !important;
}

.dashboard-switcher-badge {
  font-size: 10px;
  font-weight: 700;
  background: rgba(224,92,92,0.12);
  color: #E05C5C;
  padding: 1px 6px;
  border-radius: 20px;
  letter-spacing: .2px;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  padding: 1.25rem 1.75rem;
  background: #fff;
  border-bottom: 1px solid #e8eaf0;
  position: relative;
  top: auto;
  z-index: 1;
  overflow: visible;
}

.topbar-eyebrow {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .8px;
  text-transform: uppercase;
  color: #64748b;
  margin-bottom: 4px;
}

.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 0 rgba(34,197,94,0.4);
  animation: pulse 2s infinite;
  flex-shrink: 0;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
  70% { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
  100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}

.topbar-title {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.4px;
  color: #0f172a;
}

.topbar-right {
  display: flex;
  flex: 0 1 auto;
  min-width: 0;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.filter-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.date-group {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 12px;
}

.date-icon {
  color: #94a3b8;
  display: flex;
  align-items: center;
}

.date-input {
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  border: none;
  background: transparent;
  color: #334155;
  outline: none;
  padding: 0;
}

.date-input:focus {
  color: #185FA5;
}

.sep {
  color: #94a3b8;
  font-size: 12px;
  font-weight: 600;
}

.btn-apply {
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  padding: 7px 18px;
  border-radius: 9px;
  border: none;
  background: #185FA5;
  color: #fff;
  cursor: pointer;
  transition: background .15s, opacity .15s, transform .1s;
  letter-spacing: .2px;
}

.btn-apply:not(:disabled):hover {
  background: #1a6fbc;
  transform: translateY(-1px);
}

.btn-apply:not(:disabled):active {
  transform: translateY(0);
}

.btn-apply:disabled {
  opacity: .4;
  cursor: not-allowed;
}

.btn-reset {
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  padding: 7px 14px;
  border-radius: 9px;
  border: 1px solid #e2e8f0;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  transition: all .15s;
}

.btn-reset:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.filter-error {
  font-size: 11.5px;
  color: #D4537E;
  font-weight: 500;
}

.period-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 1.75rem;
}

.period-tag {
  font-size: 12px;
  font-weight: 600;
  color: #185FA5;
  background: rgba(24,95,165,0.08);
  padding: 3px 10px;
  border-radius: 6px;
}

.period-divider {
  color: #94a3b8;
  font-size: 13px;
}

.metrics {
  display: grid;
  grid-template-columns: 1.35fr repeat(4, 1fr);
  gap: 12px;
  padding: 0 1.75rem 1.25rem;
}

@media (max-width: 900px) {
  .topbar {
    align-items: stretch;
  }

  .topbar-left,
  .topbar-right {
    width: 100%;
  }

  .topbar-right {
    align-items: stretch;
  }

  .filter-row {
    width: 100%;
  }

  .date-group {
    flex: 1 1 280px;
  }
}

@media (max-width: 620px) {
  .topbar {
    padding: 1rem;
  }

  .topbar-title-row {
    align-items: flex-start;
  }

  .dashboard-switcher {
    width: 100%;
  }

  .dashboard-switcher-btn {
    flex: 1 1 auto;
  }
}

@media (max-width: 760px) {
  .topbar-title-row {
    width: 100%;
  }

  .dashboard-switcher {
    display: grid;
    grid-template-columns: 1fr;
    width: 100%;
  }

  .dashboard-switcher-btn {
    width: 100%;
  }
}

@media (max-width: 1100px) {
  .metrics {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 700px) {
  .metrics {
    grid-template-columns: 1fr 1fr;
  }
}

.metric-card {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: box-shadow .2s, transform .2s;
  position: relative;
  overflow: hidden;
}

.metric-card:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.07);
  transform: translateY(-2px);
}

.metric-main {
  flex-direction: row;
  align-items: center;
  gap: 14px;
  background: linear-gradient(135deg, #185FA5 0%, #1a79d4 100%);
  border-color: transparent;
}

.metric-main::after {
  content: '';
  position: absolute;
  right: -20px;
  top: -20px;
  width: 100px;
  height: 100px;
  background: rgba(255,255,255,0.07);
  border-radius: 50%;
}

.metric-main .metric-label {
  color: rgba(255,255,255,0.75);
}

.metric-main .metric-value {
  color: #fff;
  font-size: 32px;
}

.metric-main .metric-sub {
  color: rgba(255,255,255,0.6);
}

.metric-icon-wrap {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.main-icon {
  background: rgba(255,255,255,0.18);
  color: #fff;
}

.metric-body {
  flex: 1;
  min-width: 0;
}

.metric-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #64748b;
  margin-bottom: 4px;
}

.metric-value {
  font-size: 26px;
  font-weight: 700;
  letter-spacing: -1px;
  line-height: 1;
  color: #0f172a;
}

.metric-sub {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 3px;
}

.metric-bar-track {
  width: 100%;
  height: 3px;
  background: #f1f5f9;
  border-radius: 99px;
  overflow: hidden;
}

.metric-bar-fill {
  height: 100%;
  border-radius: 99px;
  transition: width .5s ease;
}

.section {
  padding: 0 1.75rem 1.25rem;
}

.donuts-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  padding: 0 1.75rem 1.25rem;
}

@media (max-width: 1100px) {
  .donuts-row {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .donuts-row {
    grid-template-columns: 1fr;
  }
}

.panel {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 16px;
  overflow: hidden;
}

.panel-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  padding: 16px 20px 14px;
  border-bottom: 1px solid #f1f5f9;
}

.panel-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.2px;
}

.panel-sub {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}

.chart-panel {
  height: 100%;
}

.chart-wrap {
  position: relative;
  width: 100%;
  height: 260px;
  padding: 12px 16px 8px;
}

.chart-wrap canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}

.chart-legend {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.legend-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 4px 10px;
}

.legend-pill .legend-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--c);
  flex-shrink: 0;
}

.legend-pill strong {
  font-weight: 700;
  color: #334155;
  margin-left: 2px;
}

.donut-card {
  background: #fff;
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  padding: 14px 16px;
  transition: box-shadow .2s;
}

.donut-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

.donut-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 10px;
}

.donut-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #64748b;
}

.donut-total {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.donut-body {
  display: flex;
  align-items: center;
  gap: 12px;
}

.donut-wrap {
  position: relative;
  width: 80px;
  height: 80px;
  flex-shrink: 0;
}

.donut-wrap canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}

.donut-legend {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.donut-leg-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
}

.dleg-color {
  width: 8px;
  height: 8px;
  border-radius: 3px;
  flex-shrink: 0;
}

.dleg-label {
  flex: 1;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dleg-val {
  font-weight: 700;
  color: #0f172a;
  font-size: 12px;
}

.disease-list {
  padding: 8px 0;
}

.disease-row {
  display: grid;
  grid-template-columns: 32px 68px 1fr 64px;
  align-items: center;
  gap: 10px;
  padding: 9px 20px;
  transition: background .15s;
}

.disease-row:hover {
  background: #f8fafc;
}

.dis-rank {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
}

.top-1 {
  background: linear-gradient(135deg, #f59e0b, #fbbf24);
  color: #fff;
}

.top-2 {
  background: linear-gradient(135deg, #64748b, #94a3b8);
  color: #fff;
}

.top-3 {
  background: linear-gradient(135deg, #b45309, #d97706);
  color: #fff;
}

.dis-code {
  font-size: 11px;
  font-family: 'Courier New', monospace;
  font-weight: 700;
  background: #eff6ff;
  color: #185FA5;
  padding: 3px 8px;
  border-radius: 6px;
  text-align: center;
  white-space: nowrap;
}

.dis-info {
  min-width: 0;
}

.dis-name {
  font-size: 13px;
  font-weight: 500;
  color: #334155;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dis-bar-track {
  height: 3px;
  background: #f1f5f9;
  border-radius: 99px;
  overflow: hidden;
  margin-top: 5px;
}

.dis-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #185FA5, #378ADD);
  border-radius: 99px;
  transition: width .4s ease;
}

.dis-total {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  text-align: right;
}



/* =========================================================
   MTBM + MTBS INLINE
   ========================================================= */
.mtbs-inline .dashboard-wrap {
  background:
    radial-gradient(1000px 400px at 10% 0%, rgba(13,110,253,.08), transparent 60%),
    radial-gradient(900px 350px at 90% 10%, rgba(25,135,84,.08), transparent 55%),
    radial-gradient(800px 300px at 40% 100%, rgba(214,51,132,.07), transparent 55%);
  border-radius: 16px;
}

.mtbs-inline .hero-bg {
  background: linear-gradient(135deg, #0d6efd 0%, #6610f2 55%, #d63384 100%);
}

.mtbs-inline .hero-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(255,255,255,.18);
  color: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,.12);
}

.mtbs-inline .filter-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: linear-gradient(135deg, #0d6efd, #20c997);
  display: inline-block;
}

.mtbs-inline .stat-card {
  position: relative;
  overflow: hidden;
  border-left: 6px solid rgba(0,0,0,.08);
}

.mtbs-inline .stat-card::after {
  content: '';
  position: absolute;
  inset: auto -30% -60% auto;
  width: 220px;
  height: 220px;
  border-radius: 999px;
  opacity: .18;
  transform: rotate(15deg);
}

.mtbs-inline .stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  color: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,.12);
}

.mtbs-inline .stat-blue { border-left-color: #0d6efd; }
.mtbs-inline .stat-blue::after { background: #0d6efd; }
.mtbs-inline .stat-blue .stat-icon { background: linear-gradient(135deg,#0d6efd,#20c997); }

.mtbs-inline .stat-indigo { border-left-color: #6610f2; }
.mtbs-inline .stat-indigo::after { background: #6610f2; }
.mtbs-inline .stat-indigo .stat-icon { background: linear-gradient(135deg,#6610f2,#0dcaf0); }

.mtbs-inline .stat-green { border-left-color: #198754; }
.mtbs-inline .stat-green::after { background: #198754; }
.mtbs-inline .stat-green .stat-icon { background: linear-gradient(135deg,#198754,#0dcaf0); }

.mtbs-inline .stat-red { border-left-color: #dc3545; }
.mtbs-inline .stat-red::after { background: #dc3545; }
.mtbs-inline .stat-red .stat-icon { background: linear-gradient(135deg,#dc3545,#fd7e14); }

.mtbs-inline .stat-sky { border-left-color: #0dcaf0; }
.mtbs-inline .stat-sky::after { background: #0dcaf0; }
.mtbs-inline .stat-sky .stat-icon { background: linear-gradient(135deg,#0dcaf0,#0d6efd); }

.mtbs-inline .stat-pink { border-left-color: #d63384; }
.mtbs-inline .stat-pink::after { background: #d63384; }
.mtbs-inline .stat-pink .stat-icon { background: linear-gradient(135deg,#d63384,#fd7e14); }

.mtbs-inline .stat-amber { border-left-color: #ffc107; }
.mtbs-inline .stat-amber::after { background: #ffc107; }
.mtbs-inline .stat-amber .stat-icon { background: linear-gradient(135deg,#ffc107,#20c997); }

.mtbs-inline .stat-gray { border-left-color: #6c757d; }
.mtbs-inline .stat-gray::after { background: #6c757d; }
.mtbs-inline .stat-gray .stat-icon { background: linear-gradient(135deg,#6c757d,#0d6efd); }

.mtbs-inline .chart-wrap {
  height: auto;
  min-height: 260px;
  padding: 0;
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,.06);
  background: linear-gradient(180deg, rgba(13,110,253,.03), rgba(25,135,84,.02));
}

.mtbs-inline .table-modern {
  border-radius: 14px;
  overflow: auto;
  border: 1px solid rgba(0,0,0,.06);
}

.mtbs-inline .table-head-sticky th {
  position: sticky;
  top: 0;
  z-index: 1;
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.mtbs-inline .table-hover tbody tr:hover {
  background: rgba(13,110,253,.04);
}
</style>
