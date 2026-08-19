<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top" >
    <div class="container-fluid">
      <div class="d-flex flex-grow-1">
        <!-- Brand di kiri -->
        <Link href="/" class="navbar-brand fw-bold d-flex align-items-center">
          <span class="bg-primary bg-gradient p-2 rounded me-2">
            <i class="bi bi-app-indicator text-white"></i>
          </span>
          <span class="text-gradient">SIMPUSWANGI</span>
        </Link>

        <!-- Toggle button di kanan -->
        <button
          class="navbar-toggler ms-auto"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Nav items di tengah -->
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <Link :href="route('home')" class="nav-link d-flex align-items-center">
              <i class="bi bi-house-door me-2"></i> Home
            </Link>
          </li>

          <!-- Dashboard: owner, kapus -->
          <li class="nav-item" v-if="can(['owner','kapus'])">
            <Link :href="route('home.home')" class="nav-link d-flex align-items-center">
              <i class="bi bi-ticket-perforated me-2"></i> Dashboard
            </Link>
          </li>

          <!-- Loket: owner, admin, loket -->
          <li class="nav-item" v-if="can(['owner','admin','loket'])">
            <Link :href="route('loket.index')" class="nav-link d-flex align-items-center">
              <i class="bi bi-ticket-perforated me-2"></i> Loket
            </Link>
          </li>

          <!-- Farmasi: dibiarkan tampil -->
          <li class="nav-item">
            <Link class="nav-link" href="/farmasi">
              <i class="bi bi-capsule me-1"></i> Farmasi
            </Link>
          </li>

          <!-- Ruang Layanan: owner, admin, pelayanan -->
          <li class="nav-item" v-if="can(['owner','admin','pelayanan'])">
            <Link :href="route('ruang-layanan.poli')" class="nav-link d-flex align-items-center">
              <i class="bi bi-ticket-perforated me-2"></i> Ruang Layanan
            </Link>
          </li>

          <!-- Mal Sehat: owner, admin -->
          <li class="nav-item" v-if="can(['owner','admin'])">
            <Link :href="route('mal-sehat.index')" class="nav-link d-flex align-items-center">
              <i class="bi bi-people me-2"></i> Mal Sehat
            </Link>
          </li>

          <!-- Filter: dibiarkan tampil -->
          <li class="nav-item">
            <Link :href="route('filter')" class="nav-link d-flex align-items-center">
              <i class="bi bi-filter-square-fill me-2"></i> Filter
            </Link>
          </li>

          <!-- Laporan (dropdown): owner, admin -->
          <li class="nav-item dropdown" v-if="can(['owner','admin'])">
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
              <li><Link :href="route('laporan.rawat-inap')" class="dropdown-item">Rawat Inap</Link></li>
              <li><Link :href="route('laporan.sanitasi')" class="dropdown-item">Sanitasi</Link></li>
              <li><Link :href="route('laporan.kunjungan-sehat')" class="dropdown-item">Kunjungan Sehat</Link></li>
            </ul>
          </li>
        </ul>
      </div>

      <!-- User dropdown di kanan -->
      <div class="d-none d-lg-flex ms-auto">
        <div class="dropdown">
          <button
            class="btn btn-outline-light rounded-pill dropdown-toggle d-flex align-items-center"
            type="button"
            id="userDropdown"
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-person-circle me-2"></i>
            <span class="d-none d-sm-inline">{{ roleLabel }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow">
            <li>
              <Link class="dropdown-item d-flex align-items-center" href="/profile">
                <i class="bi bi-person me-2"></i> Profile
              </Link>
            </li>
            <li>
              <hr class="dropdown-divider mx-3 my-1" />
            </li>
            <li>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="dropdown-item d-flex align-items-center text-danger w-100 text-start"
              >
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </Link>
            </li>
          </ul>
        </div>
      </div>

      <!-- User dropdown versi mobile -->
      <div class="d-lg-none">
        <div class="dropdown">
          <button
            class="btn btn-outline-light rounded-pill dropdown-toggle d-flex align-items-center"
            type="button"
            id="userDropdownMobile"
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-person-circle me-2"></i>
            <span class="d-none d-sm-inline">User</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow">
            <li>
              <Link class="dropdown-item d-flex align-items-center" href="/profile">
                <i class="bi bi-person me-2"></i> Profile
              </Link>
            </li>
            <li>
              <hr class="dropdown-divider mx-3 my-1" />
            </li>
            <li>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="dropdown-item d-flex align-items-center text-danger w-100 text-start"
              >
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { computed } from 'vue'

const page = usePage()
// roles ke lowercase supaya perbandingan konsisten
const roles = computed(() => (page.props.auth?.roles ?? []).map(r => String(r).toLowerCase()))

// helper izin: true kalau user punya salah satu role pada 'allowed'
const can = (allowed = []) => {
  const allow = allowed.map(a => String(a).toLowerCase())
  return roles.value.some(r => allow.includes(r))
}

// label kecil di kanan
const roleLabel = computed(() => roles.value.length ? roles.value.join(', ') : 'User')
</script>

<style scoped>
  .navbar {
    padding: 0.5rem 1rem;
    background-color: #1a1a1a !important;
  }

  .nav-link {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .nav-link:hover,
  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
  }

  .text-gradient {
    background: linear-gradient(90deg, #0d6efd, #00b4ff);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .dropdown-menu {
    min-width: 200px;
    background-color: #2a2a2a;
  }

  .dropdown-item {
    color: #e0e0e0;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin: 0.15rem 0.5rem;
  }

  .dropdown-item:hover {
    background-color: #3a3a3a;
    color: white;
  }

  .btn-outline-light {
    border-width: 1.5px;
    padding: 0.375rem 1rem;
  }

  .navbar-brand {
    font-size: 1.25rem;
    letter-spacing: 0.5px;
  }

  @media (min-width: 992px) {
    .navbar-collapse {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }
  }
</style>
