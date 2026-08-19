<template>
  <nav class="navbar navbar-expand-lg shadow-sm sticky-top">
    <div class="container-fluid">
      <div class="d-flex flex-grow-1">
        <!-- Brand di kiri -->
        <Link href="/" class="navbar-brand fw-bold d-flex align-items-center gap-2">
          <!-- Logo -->
          <img
            src="../../../../public/images/Pukesmas.webp"
            alt="Logo SIMPUSWANGI"
            style="
              height: 42px;
              width: auto;
              border-radius: 8px;
              box-shadow: 0 2px 8px rgba(24, 95, 165, 0.15);
            "
          />
          <span class="text-gradient d-none d-sm-inline">SIMPUSWANGI</span>
        </Link>

        <!-- Toggle button di kanan -->
        <button
          class="navbar-toggler ms-auto"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Nav items di tengah -->
      <div class="collapse navbar-collapse w-100" id="navbarNav">
        <ul class="navbar-nav mx-lg-auto">
          <!-- mx-lg-auto = auto margin kiri-kanan di layar besar -->

          <!-- Dashboard -->
          <li class="nav-item" v-if="show('dashboard')">
            <Link :href="route('home.home')" class="nav-link d-flex align-items-center">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </Link>
          </li>

          <!-- Loket -->
          <li class="nav-item" v-if="show('loket')">
            <Link :href="route('loket.index')" class="nav-link d-flex align-items-center">
              <i class="bi bi-ticket-perforated me-2"></i> Loket
            </Link>
          </li>
          <li class="nav-item">
            <Link class="nav-link" href="/farmasi">
              <i class="bi bi-capsule me-1"></i> Farmasi
            </Link>
          </li>

          <!-- Ruang Layanan -->
          <li class="nav-item" v-if="show('ruangLayanan')">
            <Link :href="route('ruang-layanan.poli')" class="nav-link d-flex align-items-center">
              <i class="bi bi-hospital me-2"></i> Ruang Layanan
            </Link>
          </li>
          <li class="nav-item" v-if="show('malSehat')">
            <Link :href="route('mal-sehat.index')" class="nav-link d-flex align-items-center">
              <i class="bi bi-people me-2"></i> Mal Sehat
            </Link>
          </li>

          <li class="nav-item" v-if="show('filter')">
            <Link :href="route('filter')" class="nav-link d-flex align-items-center">
              <i class="bi bi-filter-square-fill me-2"></i> Filter
            </Link>
          </li>

          <!-- Dropdown Laporan -->
          <li class="nav-item dropdown" v-if="show('laporan')">
            <a
              class="nav-link dropdown-toggle d-flex align-items-center"
              href="#"
              id="laporanDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
            </a>
            <ul class="dropdown-menu" aria-labelledby="laporanDropdown">
              <li><Link :href="route('laporan.loket')" class="dropdown-item">Loket</Link></li>
              <li><Link :href="route('laporan.rujukan')" class="dropdown-item">Rujukan</Link></li>
              <li><Link :href="route('laporan.umum')" class="dropdown-item">Umum</Link></li>
              <li><Link :href="route('laporan.gigi')" class="dropdown-item">Gigi</Link></li>
              <li><Link :href="route('laporan.kia')" class="dropdown-item">KIA</Link></li>
              <li><Link :href="route('laporan.lab')" class="dropdown-item">Lab</Link></li>
              <li><Link :href="route('laporan.kb')" class="dropdown-item">KB</Link></li>
              <li><Link :href="route('laporan.ugd')" class="dropdown-item">UGD</Link></li>
              <li>
                <Link :href="route('laporan.rawat-inap')" class="dropdown-item">Rawat Inap</Link>
              </li>
              <li><Link :href="route('laporan.sanitasi')" class="dropdown-item">Sanitasi</Link></li>
              <li>
                <Link :href="route('laporan.kunjungan-sehat')" class="dropdown-item"
                  >Kunjungan Sehat</Link
                >
              </li>
              <!-- <li><Link :href="route('laporan.mtbm')" class="dropdown-item">MTBM</Link></li> -->
              <li><Link :href="route('laporan.mtbm_mtbs')" class="dropdown-item">MTBM + MTBS</Link></li>
<li><Link :href="route('dashboard.mtbm_mtbs')" class="dropdown-item">Dashboard MTBM + MTBS</Link></li>


            </ul>
          </li>
        </ul>
      </div>

      <!-- Kanan: indikator ping + user dropdown -->
      <div class="d-flex align-items-center ms-auto gap-2">
        <!-- === Indikator Ping/Sinyal (desktop & mobile) === -->
        <div
          class="d-flex align-items-center px-2 py-1 rounded-3 ping-badge"
          :class="badgeClass"
          :title="tooltipTitle"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
        >
          <i :class="iconClass" class="me-1"></i>
          <small class="fw-semibold">{{ pingLabel }}</small>
        </div>

        <!-- User dropdown (desktop) -->
        <div class="dropdown d-none d-lg-block">
          <button
            class="btn btn-outline-light rounded-pill dropdown-toggle d-flex align-items-center gap-2"
            type="button"
            id="userDropdown"
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-sm-inline">{{ roleLabel }}</span>
          </button>
          <ul
            class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow"
            aria-labelledby="userDropdown"
          >
            <!-- Profile -->
            <li>
              <Link class="dropdown-item d-flex align-items-center" :href="route('profile.index')">
                <i class="bi bi-person me-2"></i> Profile
              </Link>
            </li>

            <!-- Divider -->
            <li><hr class="dropdown-divider mx-3 my-1" /></li>

            <!-- Info Puskesmas -->
            <li class="px-3 py-2 text-center">
              <div class="d-flex flex-column align-items-center justify-content-center">
                <div
                  class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-2 shadow-sm"
                  style="width: 42px; height: 42px; background-color: rgba(24, 95, 165, 0.1)"
                >
                  <i class="bi bi-hospital text-primary fs-5" style="color: #185fa5 !important"></i>
                </div>
                <div class="fw-semibold small" style="color: #212529">
                  {{ puskesmasNama }}
                </div>
                <span class="text-secondary small">Puskesmas</span>
              </div>
            </li>

            <!-- Divider -->
            <li><hr class="dropdown-divider mx-3 my-1" /></li>

            <!-- Logout -->
            <li>
              <button
                @click="doLogout"
                class="dropdown-item d-flex align-items-center text-danger w-100 text-start"
              >
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </button>
            </li>
          </ul>
        </div>

        <!-- User dropdown (mobile) -->
        <div class="dropdown d-lg-none">
          <button
            class="btn btn-outline-light rounded-pill dropdown-toggle d-flex align-items-center gap-2"
            type="button"
            id="userDropdownMobile"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-sm-inline">{{ roleLabel }}</span>
          </button>
          <ul
            class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow"
            aria-labelledby="userDropdownMobile"
          >
            <li>
              <Link class="dropdown-item d-flex align-items-center" href="/profile">
                <i class="bi bi-person me-2"></i> Profile
              </Link>
            </li>
            <li><hr class="dropdown-divider mx-3 my-1" /></li>
            <li>
              <button
                @click="doLogout"
                class="dropdown-item d-flex align-items-center text-danger w-100 text-start"
              >
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
  import { Link, usePage, router } from '@inertiajs/vue3';
  import { route } from 'ziggy-js';
  import { ref, computed, onMounted, onBeforeUnmount, nextTick, watchEffect } from 'vue';

  const page = usePage();

  // Role dari Laravel/Inertia kadang berupa string dan kadang object.
  // Contoh:
  // ['owner']
  // [{ id: 1, name: 'owner' }]
  // Normalisasi keduanya agar menu tidak hilang saat pindah halaman.
  const lastKnownRoles = ref([]);

  const normalizeRoles = (items) => {
    if (!Array.isArray(items)) return [];

    return [...new Set(
      items
        .map((role) => {
          if (typeof role === 'string') return role;

          return role?.name
            ?? role?.role
            ?? role?.role_name
            ?? role?.nama
            ?? '';
        })
        .map((role) => String(role).trim().toLowerCase())
        .filter(Boolean)
    )];
  };

  watchEffect(() => {
    const currentRoles = normalizeRoles(page.props.auth?.user?.roles);

    // Pertahankan role terakhir yang valid ketika Inertia sedang mengganti props.
    // Ini mencegah menu berkedip atau menghilang sesaat saat navigasi.
    if (currentRoles.length > 0) {
      lastKnownRoles.value = currentRoles;
    }
  });

  const roles = computed(() => {
    const currentRoles = normalizeRoles(page.props.auth?.user?.roles);

    return currentRoles.length > 0
      ? currentRoles
      : lastKnownRoles.value;
  });

  const roleLabel = computed(() => {
    const rawRole = page.props.auth?.user?.roles?.[0];

    if (typeof rawRole === 'string') return rawRole;

    return rawRole?.name
      ?? rawRole?.role
      ?? rawRole?.role_name
      ?? rawRole?.nama
      ?? roles.value?.[0]
      ?? 'User';
  });
  const puskesmasNama = computed(() => page.props.auth?.user?.puskesmas?.nama || '-');

  // Akses menu
  const ALLOW = {
    dashboard: ['owner', 'kapus'],
    loket: ['owner', 'loket', 'admin'],
    pasien: ['loket'],
    ruangLayanan: ['owner', 'pelayanan', 'admin', 'laborat'],
    malSehat: ['owner', 'admin'],
    laborat: ['pelayanan', 'laborat'],
    laporan: ['owner', 'loket', 'pelayanan', 'admin'],
    farmasi: ['owner', 'admin'],
    filter: ['owner', 'admin'],
  };
  const show = (menuKey) => {
    const allowed = ALLOW[menuKey];
    if (!allowed) return false;
    return roles.value.some((r) => allowed.includes(r));
  };

  const doLogout = () => {
    router.post(
      route('logout'),
      {},
      { onSuccess: () => router.visit(route('login'), { replace: true }) }
    );
  };

  /* ================================
   PING / INDICATOR LOGIC
   - Poll setiap 15 detik
   - Threshold:
     good < 100ms (hijau)
     ok   < 250ms (kuning)
     bad  >= 250ms atau timeout (merah)
   - Fetch ke /favicon.ico tanpa cache (nyaris selalu ada)
================================ */
  const latencyMs = ref(null); // number | null
  const quality = ref('checking'); // 'checking' | 'good' | 'ok' | 'bad' | 'down'
  const lastAt = ref(null); // Date | null
  const intervalMs = 3000;
  let ticker = null;

  const PING_URL = '/favicon.ico';
  const TIMEOUT_MS = 5000;

  async function measurePing() {
    const start = performance.now();
    const ctrl = new AbortController();
    const timeout = setTimeout(() => ctrl.abort('timeout'), TIMEOUT_MS);

    try {
      // Gunakan GET + no-store agar tidak tersangkut cache/proxy
      const res = await fetch(PING_URL, { cache: 'no-store', signal: ctrl.signal, method: 'GET' });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const end = performance.now();
      const ms = Math.max(1, Math.round(end - start));
      latencyMs.value = ms;
      lastAt.value = new Date();

      if (ms < 100) quality.value = 'good';
      else if (ms < 250) quality.value = 'ok';
      else quality.value = 'bad';
    } catch (e) {
      latencyMs.value = null;
      quality.value = 'down';
      lastAt.value = new Date();
    } finally {
      clearTimeout(timeout);
    }
  }

  const pingLabel = computed(() => {
    switch (quality.value) {
      case 'good':
        return '🟢 BAGUS';
      case 'ok':
        return '🟡 SEDANG';
      case 'bad':
        return '🔴 JELEK';
      case 'down':
        return '⚫ OFFLINE';
      default:
        return '⏳ CEK...';
    }
  });

  const iconClass = computed(() => {
    switch (quality.value) {
      case 'good':
        return 'bi bi-reception-4';
      case 'ok':
        return 'bi bi-reception-2';
      case 'bad':
        return 'bi bi-reception-0';
      case 'down':
        return 'bi bi-wifi-off';
      default:
        return 'bi bi-reception-1';
    }
  });

  const badgeClass = computed(() => {
    switch (quality.value) {
      case 'good':
        return 'bg-success-subtle text-success-emphasis border border-success-subtle';
      case 'ok':
        return 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
      case 'bad':
        return 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
      case 'down':
        return 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
      default:
        return 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle';
    }
  });

  const tooltipTitle = computed(() => {
    if (!lastAt.value) return 'Mengukur koneksi…';
    const ts = lastAt.value.toLocaleTimeString();
    if (quality.value === 'down') return `Terakhir cek ${ts} • koneksi bermasalah`;
    if (quality.value === 'good') return `Terakhir cek ${ts} • koneksi bagus`;
    if (quality.value === 'ok') return `Terakhir cek ${ts} • koneksi sedang`;
    if (quality.value === 'bad') return `Terakhir cek ${ts} • koneksi jelek`;
    return `Terakhir cek ${ts}`;
  });

  onMounted(async () => {
    await nextTick();
    // Bootstrap tooltip (opsional, kalau Bootstrap JS sudah diimport global)
    if (window.bootstrap) {
      const els = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      els.forEach((el) => new window.bootstrap.Tooltip(el));
    }
    // Ping awal + interval
    measurePing();
    ticker = setInterval(measurePing, intervalMs);
  });

  onBeforeUnmount(() => {
    if (ticker) clearInterval(ticker);
  });
</script>

<style scoped>
  /* ── Base navbar ── */
  .navbar {
    padding: 0.75rem 1.5rem;
    z-index: 1030;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-bottom: 2px solid #e9ecef;
    font-family: 'DM Sans', 'Segoe UI', sans-serif;
  }

  /* ── Brand & logo ── */
  .navbar-brand {
    font-size: 1.3rem;
    letter-spacing: 0.3px;
    font-weight: 700;
    color: #185fa5 !important;
    display: flex !important;
    align-items: center !important;
    transition: opacity 0.2s ease;
  }
  .navbar-brand:hover {
    opacity: 0.85;
  }

  .text-gradient {
    background: linear-gradient(135deg, #185fa5, #0d6efd);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
  }

  /* ── Navigation links ── */
  .nav-link {
    padding: 0.6rem 1rem !important;
    border-radius: 8px;
    transition: all 0.25s ease;
    font-weight: 500;
    font-size: 14px;
    color: #495057 !important;
  }

  .nav-link:hover {
    background-color: rgba(24, 95, 165, 0.08);
    color: #185fa5 !important;
    transform: translateY(-1px);
  }

  .nav-link.active {
    background-color: rgba(24, 95, 165, 0.15);
    color: #185fa5 !important;
    border-bottom: 3px solid #185fa5;
  }

  /* ── Dropdown menu ── */
  .dropdown-menu {
    min-width: 220px;
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .dropdown-item {
    color: #495057;
    padding: 0.65rem 1rem;
    border-radius: 8px;
    margin: 4px 0;
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .dropdown-item:hover {
    background-color: rgba(24, 95, 165, 0.1);
    color: #185fa5;
    transform: translateX(2px);
  }

  .dropdown-item.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    transform: translateX(2px);
  }

  .dropdown-divider {
    border-color: #e9ecef !important;
  }

  /* ── User buttons ── */
  .btn-outline-light {
    border-width: 1.5px;
    padding: 0.5rem 1rem;
    border-color: #dee2e6;
    color: #495057;
    transition: all 0.2s ease;
  }

  .btn-outline-light:hover {
    background-color: #185fa5;
    border-color: #185fa5;
    color: #fff;
  }

  .btn-outline-light.dropdown-toggle::after {
    border-color: #185fa5;
  }

  /* ── Navbar toggler (mobile) ── */
  .navbar-toggler {
    border-color: #dee2e6;
    padding: 0.5rem;
  }

  .navbar-toggler:focus {
    box-shadow: 0 0 0 0.25rem rgba(24, 95, 165, 0.25);
    border-color: #185fa5;
  }

  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23185FA5' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }

  /* ── Ping badge ── */
  .ping-badge {
    line-height: 1;
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    transition: all 0.3s ease;
  }

  .ping-badge:hover {
    transform: scale(1.05);
  }

  /* ── Responsive ── */
  @media (max-width: 991px) {
    .navbar {
      padding: 0.6rem 1rem;
    }

    .navbar-collapse {
      padding-top: 1rem;
      border-top: 1px solid #e9ecef;
      margin-top: 1rem;
    }

    .nav-link {
      padding: 0.7rem 0.5rem !important;
    }
  }

  @media (min-width: 992px) {
    .navbar-collapse {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      border-top: none;
      margin-top: 0;
      padding-top: 0;
    }
  }
</style>
