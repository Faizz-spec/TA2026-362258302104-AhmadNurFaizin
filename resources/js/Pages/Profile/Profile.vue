<template>
  <div class="container py-4">
    <div class="row g-3 align-items-center mb-3">
      <div class="col-auto">
        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:56px;height:56px">
          <i class="bi bi-hospital fs-4 text-primary"></i>
        </div>
      </div>
      <div class="col">
        <h4 class="mb-0">Profil Puskesmas</h4>
        <small class="text-muted">
          {{ fromUser?.puskesmas_nama ?? '-' }} <span v-if="fromUser?.puskesmas_kode">• {{ fromUser.puskesmas_kode }}</span>
        </small>
      </div>
    </div>

    <div v-if="!profile" class="alert alert-warning d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      Data profil tidak ditemukan untuk puskesmas yang sedang login.
    </div>

    <div v-else class="row g-3">
      <!-- Kartu Identitas -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="card-title mb-3">Identitas</h6>

            <div class="row">
              <div class="col-5 text-muted small">Nama Unit</div>
              <div class="col-7 fw-semibold">{{ safe(profile.nama_unit) }}</div>

              <div class="col-5 text-muted small mt-2">Kode Puskesmas</div>
              <div class="col-7 mt-2">{{ safe(profile.kode_puskesmas) }}</div>

              <div class="col-5 text-muted small mt-2">Kategori</div>
              <div class="col-7 mt-2">{{ safe(profile.kategori) }}</div>

              <div class="col-5 text-muted small mt-2">Telepon</div>
              <div class="col-7 mt-2">{{ safe(profile.telp) }}</div>

              <div class="col-5 text-muted small mt-2">Fax</div>
              <div class="col-7 mt-2">{{ safe(profile.fax) }}</div>

              <div class="col-5 text-muted small mt-2">Email</div>
              <div class="col-7 mt-2">
                <a v-if="profile.email" :href="`mailto:${profile.email}`">{{ profile.email }}</a>
                <span v-else>-</span>
              </div>

              <div class="col-5 text-muted small mt-2">Website</div>
              <div class="col-7 mt-2">
                <a v-if="profile.site" :href="normalizeUrl(profile.site)" target="_blank" rel="noopener">
                  {{ profile.site }}
                </a>
                <span v-else>-</span>
              </div>
            </div>
          </div>
          <div class="card-footer bg-transparent small text-muted">
            Terakhir diperbarui: {{ safe(profile.last_update) }}
          </div>
        </div>
      </div>

      <!-- Kartu Alamat -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="card-title mb-3">Alamat</h6>

            <div class="mb-2">
              <div class="text-muted small">Alamat Lengkap</div>
              <div class="fw-semibold">{{ formatAlamat(profile) }}</div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="text-muted small">RT / RW</div>
                <div>{{ joinSlash(profile.rt, profile.rw) }}</div>
              </div>
              <div class="col-6">
                <div class="text-muted small">Kel / Kec</div>
                <div>{{ joinSlash(profile.no_kel, profile.no_kec) }}</div>
              </div>
              <div class="col-6 mt-2">
                <div class="text-muted small">Kab / Prov</div>
                <div>{{ joinSlash(profile.no_kab, profile.no_prov) }}</div>
              </div>
              <div class="col-6 mt-2">
                <div class="text-muted small">Kode Pos</div>
                <div>{{ safe(profile.kode_pos) }}</div>
              </div>
            </div>

            <div class="mt-3">
              <div class="text-muted small">Koordinat</div>
              <div>
                <template v-if="profile.lat && profile.lng">
                  {{ profile.lat }}, {{ profile.lng }}
                  <a
                    class="btn btn-sm btn-outline-primary ms-2"
                    :href="`https://www.google.com/maps?q=${profile.lat},${profile.lng}`"
                    target="_blank" rel="noopener"
                  >
                    <i class="bi bi-geo-alt me-1"></i> Lihat di Maps
                  </a>
                </template>
                <span v-else>-</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kartu Pimpinan -->
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="card-title mb-3">Pimpinan</h6>
            <div class="row">
              <div class="col-md-4">
                <div class="text-muted small">Nama</div>
                <div class="fw-semibold">{{ safe(profile.nama_pimpinan) }}</div>
              </div>
              <div class="col-md-4 mt-2 mt-md-0">
                <div class="text-muted small">Jabatan</div>
                <div>{{ safe(profile.jabatan_pimpinan) }}</div>
              </div>
              <div class="col-md-4 mt-2 mt-md-0">
                <div class="text-muted small">NIP</div>
                <div>{{ safe(profile.nip_pimpinan) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /row -->
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const profile = page.props.profile
const fromUser = page.props.fromUser

const safe = (v) => v ?? '-'
const joinSlash = (a, b) => {
  const left = a ?? ''
  const right = b ?? ''
  const s = [left, right].filter(Boolean).join(' / ')
  return s || '-'
}
const normalizeUrl = (u) => {
  if (!u) return '#'
  if (/^https?:\/\//i.test(u)) return u
  return 'http://' + u
}
const formatAlamat = (p) => {
  if (!p) return '-'
  const parts = [p.alamat, (p.rt || p.rw) ? `RT ${p.rt || '-'} / RW ${p.rw || '-'}` : null]
  return parts.filter(Boolean).join(', ')
}
</script>

<style scoped>
.card-title { letter-spacing: .2px; }
</style>
