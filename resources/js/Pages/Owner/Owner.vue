<script setup>
import { ref, computed, onMounted, watch, onBeforeUnmount } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

// =============================
// Owner Panel: Manage Users & Roles + Online Puskesmas
// =============================
const loading = ref(false)
const roles = ref([])
const users = ref([])
const total = ref(0)

const search = ref('')
const selectedRole = ref('')
const selectedPuskesmasId = ref('')

const puskesmasList = ref([])

const page = ref(1)
const pageSize = ref(25)

const pollTimer = ref(null)

// ====== Create User state ======
const showCreateModal = ref(false)
const createLoading = ref(false)
const createForm = ref({
  username: '',
  password: '',
  password_confirm: '',
  first_name: '',
  last_name: '',
  email: '',
  puskesmas_id: '',
  roles: [] // array of role names
})
// ===== Edit "Terakhir Password Diubah" =====
const showPwdModal = ref(false)
const pwdLoading = ref(false)
const pwdForm = ref({
  mode: 'now',     // now | date | force
  date: '',        // YYYY-MM-DD (kalau mode=date)
  days_ago: 16,    // kalau mode=force, default 16 hari
})
const targetUser = ref(null)

function openPwdModal(user) {
  targetUser.value = user
  pwdForm.value = { mode: 'now', date: '', days_ago: 16 }
  showPwdModal.value = true
}

async function savePwdChange() {
  if (!targetUser.value) return
  if (pwdForm.value.mode === 'date' && !pwdForm.value.date) {
    alert('Tanggal wajib diisi')
    return
  }
  pwdLoading.value = true
  try {
    await axios.patch(route('owner.users.password_changed', targetUser.value.id), {
      mode: pwdForm.value.mode,
      date: pwdForm.value.mode === 'date' ? pwdForm.value.date : undefined,
      days_ago: pwdForm.value.mode === 'force' ? Number(pwdForm.value.days_ago || 16) : undefined,
    })
    showPwdModal.value = false
    await fetchUsers() // refresh tabel
  } catch (e) {
    alert(e?.response?.data?.message || 'Gagal menyimpan perubahan')
  } finally {
    pwdLoading.value = false
  }
}

function openCreateModal() {
  createForm.value = {
    username: '',
    password: '',
    password_confirm: '',
    first_name: '',
    last_name: '',
    email: '',
    puskesmas_id: selectedPuskesmasId.value || '',
    roles: []
  }
  showCreateModal.value = true
}

function toggleRoleInCreate(roleName) {
  const i = createForm.value.roles.indexOf(roleName)
  if (i === -1) createForm.value.roles.push(roleName)
  else createForm.value.roles.splice(i, 1)
}
// ===== Toasts (Bootstrap-like) =====
const toasts = ref([]) // {id, variant, text}
function showToast(variant, text, timeout = 4000) {
  const id = Date.now() + Math.random()
  toasts.value.push({ id, variant, text })
  setTimeout(() => removeToast(id), timeout)
}
function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id)
}

// ===== Konfirmasi hapus user =====
const showDeleteModal = ref(false)
const deleteTarget = ref(null)
const deleteLoading = ref(false)

function askDelete(user) {
  deleteTarget.value = user
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleteLoading.value = true
  try {
    await axios.delete(route('owner.users.destroy', deleteTarget.value.id))
    showDeleteModal.value = false
    showToast('success', `User "${fmtName(deleteTarget.value)}" berhasil dihapus.`)
    await fetchUsers()
  } catch (e) {
    showToast('danger', e?.response?.data?.message || 'Gagal menghapus user.')
  } finally {
    deleteLoading.value = false
  }
}

async function deleteUser(user) {
  if (!user?.id) return
  if (!confirm(`Hapus user "${fmtName(user)}" (@${user.username})? Tindakan ini tidak bisa dibatalkan.`)) return
  try {
await axios.delete(route('owner.users.destroy', user.id))
    await fetchUsers()
  } catch (e) {
    alert(e?.response?.data?.message || 'Gagal menghapus user.')
  }
}


async function saveCreateUser() {
  if (!createForm.value.username) return alert('Username wajib diisi')
  if (!createForm.value.password) return alert('Password wajib diisi')
  if (createForm.value.password !== createForm.value.password_confirm) return alert('Konfirmasi password tidak cocok')
  if (!createForm.value.puskesmas_id) return alert('Puskesmas wajib dipilih')

  createLoading.value = true
  try {
    await axios.post('/api/owner/users', {
      username: createForm.value.username,
      password: createForm.value.password,
      first_name: createForm.value.first_name || null,
      last_name: createForm.value.last_name || null,
      email: createForm.value.email || null,
      puskesmas_id: createForm.value.puskesmas_id,
      roles: createForm.value.roles
    })
    showCreateModal.value = false
    await fetchUsers()
  } catch (e) {
    alert(e?.response?.data?.message || 'Gagal membuat user baru')
  } finally {
    createLoading.value = false
  }
}

// Modal state
const showRoleModal = ref(false)
const editingUser = ref(null)
const roleForm = ref({})
const originalRoles = ref([])

const onlineWindowMinutes = 5

function fmtName(u) {
  const a = [u?.first_name, u?.last_name].filter(Boolean).join(' ')
  return a || u?.username || '-'
}
function shortRoles(rs) {
  if (!rs || !rs.length) return '-'
  return rs.join(', ')
}
const filteredUsers = computed(() => users.value)
function onlineBadge(u) {
  if (u.is_online) return { text: 'Online', cls: 'badge bg-success' }
  return { text: 'Offline', cls: 'badge bg-secondary' }
}

async function fetchRoles() {
  const { data } = await axios.get('/api/owner/roles')
  roles.value = data || []
}
async function fetchPuskesmas() {
  const { data } = await axios.get('/api/owner/puskesmas')
  puskesmasList.value = data || []
}
async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/owner/users', {
      params: {
        search: search.value || undefined,
        role: selectedRole.value || undefined,
        puskesmas_id: selectedPuskesmasId.value || undefined,
        page: page.value,
        page_size: pageSize.value,
      }
    })
    users.value = data?.data || []
    total.value = data?.meta?.total || users.value.length
  } finally {
    loading.value = false
  }
}

function openRoleModal(user) {
  editingUser.value = JSON.parse(JSON.stringify(user))
  originalRoles.value = [...(user.roles || [])]
  const model = {}
  for (const r of roles.value) model[r.name] = originalRoles.value.includes(r.name)
  roleForm.value = model
  showRoleModal.value = true
}
function computeRoleDiff() {
  const chosen = Object.entries(roleForm.value)
    .filter(([k, v]) => !!v)
    .map(([k]) => k)
  const add = chosen.filter(r => !originalRoles.value.includes(r))
  const remove = originalRoles.value.filter(r => !chosen.includes(r))
  return { add, remove }
}
async function saveRoles() {
  if (!editingUser.value) return
  const { add, remove } = computeRoleDiff()
  try {
    await axios.patch(`/api/owner/users/${editingUser.value.id}/roles`, { add, remove })
    await fetchUsers()
    showRoleModal.value = false
  } catch (e) {
    alert('Gagal menyimpan role.')
  }
}
async function forceLogout(user) {
  if (!confirm(`Paksa logout ${fmtName(user)}?`)) return
  try {
    await axios.post(`/api/owner/users/${user.id}/force-logout`)
    await fetchUsers()
  } catch (e) {
    alert('Gagal force logout')
  }
}

function startPolling() {
  stopPolling()
  pollTimer.value = setInterval(fetchUsers, 30000)
}
function stopPolling() {
  if (pollTimer.value) clearInterval(pollTimer.value)
  pollTimer.value = null
}
onMounted(async () => {
  await Promise.all([fetchRoles(), fetchPuskesmas()])
  await fetchUsers()
  startPolling()
})
onBeforeUnmount(() => stopPolling())
watch([search, selectedRole, selectedPuskesmasId, pageSize], async () => {
  page.value = 1
  await fetchUsers()
})

// Puskesmas online aggregation
const puskesmasOnline = computed(() => {
  const m = new Map()
  for (const u of users.value) {
    const key = u?.puskesmas?.id || 0
    const nama = u?.puskesmas?.nama || '—'
    if (!m.has(key)) m.set(key, { id: key, nama, total: 0, online: 0, logged_in: 0 })
    const row = m.get(key)
    row.total++
    if (u.is_online) row.online++
    if (u.is_logged_in) row.logged_in++
  }
  return Array.from(m.values()).sort((a,b)=> a.nama.localeCompare(b.nama))
})
</script>

<template>
  <Head title="Super Admin — Manajemen User & Role" />
  <div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="m-0">Super Admin— Manajemen User & Role</h2>

      <div class="d-flex gap-2">
        <!-- TOMBOL LIHAT LOG -->
        <Link href="/owner/logs/loket-delete" class="btn btn-warning btn-sm">
          <i class="bi bi-clipboard-data me-1"></i> Lihat Log Loket
        </Link>
        <Link href="/" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </Link>
        <button class="btn btn-success btn-sm" @click="openCreateModal()">
          <i class="bi bi-person-plus me-1"></i> Tambah User
        </button>
      </div>
    </div>

    <!-- ================== Modal Tambah User ================== -->
    <div class="modal fade" tabindex="-1" :class="{ show: showCreateModal }" style="display: block;" v-if="showCreateModal" @click.self="showCreateModal=false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title">Tambah User Baru</h5>
            <button type="button" class="btn-close" @click="showCreateModal=false"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Username</label>
                <input v-model.trim="createForm.username" type="text" class="form-control" placeholder="mis. wongsorejo_admin" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Email (opsional)</label>
                <input v-model.trim="createForm.email" type="email" class="form-control" placeholder="user@domain" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Puskesmas</label>
                <select v-model="createForm.puskesmas_id" class="form-select">
                  <option value="">Pilih Puskesmas</option>
                  <option v-for="p in puskesmasList" :key="p.id" :value="p.id">{{ p.nama }}</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input v-model="createForm.password" type="password" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Konfirmasi Password</label>
                <input v-model="createForm.password_confirm" type="password" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Nama Depan (opsional)</label>
                <input v-model.trim="createForm.first_name" type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Nama Belakang (opsional)</label>
                <input v-model.trim="createForm.last_name" type="text" class="form-control" />
              </div>

              <div class="col-12">
                <label class="form-label d-block mb-1">Role</label>
                <div class="d-flex flex-wrap gap-2">
                  <label v-for="r in roles" :key="r.id" class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" :value="r.name"
                           :id="`create-role-${r.name}`"
                           :checked="createForm.roles.includes(r.name)"
                           @change="toggleRoleInCreate(r.name)">
                    <span class="form-check-label" :for="`create-role-${r.name}`">{{ r.name }}</span>
                  </label>
                </div>
                <div class="form-text">Centang role sesuai kebutuhan (owner/loket/pelayanan/kapus/admin).</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" @click="showCreateModal=false">Batal</button>
            <button class="btn btn-primary" :disabled="createLoading" @click="saveCreateUser">
              <i class="bi bi-save me-1"></i> {{ createLoading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <!-- ================== KIRI: Tabel User ================== -->
      <div class="col-lg-8">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-header p-3 rounded-4 rounded-bottom-0" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
              <h5 class="text-white m-0">Daftar User</h5>
              <div class="text-white-50 small">Total: {{ total }}</div>
            </div>
          </div>

          <div class="card-body">
            <div class="row g-2 mb-3">
              <div class="col-md-4">
                <input v-model.trim="search" type="text" class="form-control" placeholder="Cari nama/username/email" />
              </div>
              <div class="col-md-4">
                <select v-model="selectedPuskesmasId" class="form-select">
                  <option value="">Semua Puskesmas</option>
                  <option v-for="p in puskesmasList" :key="p.id" :value="p.id">{{ p.nama }}</option>
                </select>
              </div>
              <div class="col-md-3">
                <select v-model="selectedRole" class="form-select">
                  <option value="">Semua Role</option>
                  <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
                </select>
              </div>
              <div class="col-md-1">
                <select v-model.number="pageSize" class="form-select">
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                </select>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th style="width: 36px;"></th>
                    <th>User</th>
                    <th>Puskesmas</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="loading">
                    <td colspan="6">Memuat data...</td>
                  </tr>
                  <tr v-else-if="!filteredUsers.length">
                    <td colspan="6">Tidak ada data.</td>
                  </tr>
                  <tr v-for="u in filteredUsers" :key="u.id">
                    <td>
                      <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="bi bi-person text-primary"></i>
                      </div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ fmtName(u) }}</div>
                      <div class="text-muted small">@{{ u.username }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ u?.puskesmas?.nama || '—' }}</div>
                    </td>
                    <td>
                      <div class="d-flex flex-wrap gap-1">
                        <span v-for="r in u.roles" :key="r" class="badge rounded-pill text-bg-light border">{{ r }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="onlineBadge(u).cls">{{ onlineBadge(u).text }}</span>
                      <div class="small text-muted" v-if="u.last_seen">Last seen: {{ new Date(u.last_seen).toLocaleString() }}</div>
                      <div class="small" :class="u.is_logged_in ? 'text-success' : 'text-muted'">{{ u.is_logged_in ? 'Sedang login' : 'Tidak login' }}</div>
                    </td>
                    <td class="text-end">
                      <div class="btn-group">
                        <button class="btn btn-sm btn-primary" @click="openRoleModal(u)"><i class="bi bi-shield-lock me-1"></i> Role</button>
                        <!-- <button class="btn btn-sm btn-outline-danger" @click="forceLogout(u)" :disabled="!u.is_logged_in"><i class="bi bi-box-arrow-right me-1"></i> Force Logout</button> -->
                      </div>

                          <button class="btn btn-sm btn-outline-warning" @click="openPwdModal(u)">
                            <i class="bi bi-key me-1"></i> Edit Tgl Password
                          </button>

                        <button class="btn btn-sm btn-outline-danger" @click="askDelete(u)">
                          <i class="bi bi-trash me-1"></i> Hapus
                        </button>




                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small">Menampilkan {{ filteredUsers.length }} dari {{ total }}</div>
              <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm" :disabled="page<=1" @click="page--; fetchUsers()">Prev</button>
                <button class="btn btn-outline-secondary btn-sm" @click="page++; fetchUsers()">Next</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================== KANAN: Puskesmas Online ================== -->
      <div class="col-lg-4">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-header p-3 rounded-4 rounded-bottom-0" style="background: linear-gradient(135deg, #10b981, #3b82f6);">
            <h5 class="text-white m-0">Status Puskesmas</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="small text-muted">Jendela online: ≤ {{ onlineWindowMinutes }} menit</div>
              <button class="btn btn-sm btn-outline-secondary" @click="fetchUsers()"><i class="bi bi-arrow-clockwise"></i></button>
            </div>

            <div v-if="!puskesmasOnline.length" class="text-muted">Belum ada data.</div>

            <div class="list-group list-group-flush">
              <div v-for="p in puskesmasOnline" :key="p.id" class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ p.nama }}</div>
                  <div class="small text-muted">User: {{ p.total }} | Online: {{ p.online }} | Login: {{ p.logged_in }}</div>
                </div>
                <span :class="['badge', p.online>0 ? 'bg-success' : 'bg-secondary']">{{ p.online>0 ? 'Online' : 'Offline' }}</span>
              </div>
            </div>

          </div>
        </div>

        <div class="mt-3 small text-muted">
          <strong>Catatan:</strong> Status <em>Online</em> dihitung dari <code>last_seen</code> ≤ 5 menit. Status <em>Sedang Login</em> berdasarkan sesi aktif.
        </div>
      </div>
    </div>

    <!-- ================== Modal Role ================== -->
    <div class="modal fade" tabindex="-1" :class="{ show: showRoleModal }" style="display: block;" v-if="showRoleModal" @click.self="showRoleModal=false">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title">Atur Role — {{ fmtName(editingUser) }}</h5>
            <button type="button" class="btn-close" @click="showRoleModal=false"></button>
          </div>
          <div class="modal-body">
            <div v-if="!roles.length" class="text-muted">Tidak ada role terdaftar.</div>
            <div v-else class="row g-2">
              <div v-for="r in roles" :key="r.id" class="col-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" :id="'role-'+r.name" v-model="roleForm[r.name]" />
                  <label class="form-check-label" :for="'role-'+r.name">{{ r.name }}</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" @click="showRoleModal=false">Batal</button>
            <button class="btn btn-primary" @click="saveRoles"><i class="bi bi-save me-1"></i> Simpan</button>
          </div>
        </div>
      </div>
    </div>
<!-- ================== Modal Edit Tanggal Password ================== -->
<div class="modal fade" tabindex="-1" :class="{ show: showPwdModal }" style="display: block;" v-if="showPwdModal" @click.self="showPwdModal=false">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Edit “Terakhir Password Diubah” — {{ fmtName(targetUser) }}</h5>
        <button type="button" class="btn-close" @click="showPwdModal=false"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label d-block">Mode Perubahan</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" id="pwd-now" value="now" v-model="pwdForm.mode">
            <label class="form-check-label" for="pwd-now">Set ke SEKARANG</label>
          </div>
          <div class="form-check mt-1">
            <input class="form-check-input" type="radio" id="pwd-date" value="date" v-model="pwdForm.mode">
            <label class="form-check-label" for="pwd-date">Set ke TANGGAL tertentu</label>
          </div>
          <div class="form-check mt-1">
            <input class="form-check-input" type="radio" id="pwd-force" value="force" v-model="pwdForm.mode">
            <label class="form-check-label" for="pwd-force">PAKSA GANTI (Set mundur 16 hari)</label>
          </div>
        </div>

        <div class="mb-3" v-if="pwdForm.mode === 'date'">
          <label class="form-label">Pilih Tanggal</label>
          <input type="date" class="form-control" v-model="pwdForm.date">
          <div class="form-text">Format: YYYY-MM-DD (waktu akan dianggap 00:00:00 WIB).</div>
        </div>

        <div class="mb-3" v-if="pwdForm.mode === 'force'">
          <label class="form-label">Mundur berapa hari?</label>
          <input type="number" min="1" class="form-control" v-model.number="pwdForm.days_ago">
          <div class="form-text">Default 16 hari (agar logika ≥ 15 hari langsung aktif).</div>
        </div>

        <div class="alert alert-warning mb-0">
          Perubahan ini <strong>tidak</strong> mengganti password user—hanya mengubah timestamp “terakhir diubah”.  
          Gunakan opsi <em>Paksa Ganti</em> agar pengguna diminta mengganti password di login berikutnya.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" @click="showPwdModal=false">Batal</button>
        <button class="btn btn-primary" :disabled="pwdLoading" @click="savePwdChange">
          <i class="bi bi-save me-1"></i> {{ pwdLoading ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </div>
    </div>
  </div>
</div>
<!-- ================== Modal Konfirmasi Hapus User ================== -->
<div class="modal fade" tabindex="-1" :class="{ show: showDeleteModal }"
     style="display: block;" v-if="showDeleteModal" @click.self="showDeleteModal=false">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" @click="showDeleteModal=false"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">
          Apakah Anda <strong>yakin</strong> ingin menghapus user berikut?
        </p>
        <ul class="mb-3">
          <li><strong>Nama:</strong> {{ fmtName(deleteTarget) }}</li>
          <li><strong>Username:</strong> @{{ deleteTarget?.username }}</li>
          <li><strong>Puskesmas:</strong> {{ deleteTarget?.puskesmas?.nama || '—' }}</li>
        </ul>
        <div class="alert alert-warning mb-0">
          Tindakan ini <strong>tidak dapat dibatalkan</strong>.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" @click="showDeleteModal=false">Batal</button>
        <button class="btn btn-danger" :disabled="deleteLoading" @click="confirmDelete">
          <i class="bi bi-trash me-1"></i> {{ deleteLoading ? 'Menghapus...' : 'Hapus' }}
        </button>
      </div>
    </div>
  </div>
</div>
<!-- ================== Toast Container ================== -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
  <div v-for="t in toasts" :key="t.id"
       class="toast show text-white"
       :class="{
         'bg-success': t.variant==='success',
         'bg-danger' : t.variant==='danger',
         'bg-info'   : t.variant==='info',
         'bg-warning': t.variant==='warning'
       }">
    <div class="d-flex">
      <div class="toast-body">{{ t.text }}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
              @click="removeToast(t.id)"></button>
    </div>
  </div>
</div>

  </div>
</template>

<style scoped>
.modal { background: rgba(0,0,0,0.35); }
</style>
