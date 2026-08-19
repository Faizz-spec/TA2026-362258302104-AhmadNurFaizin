<script setup>
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

import AppLayouts from '@/Components/Layouts/AppLayouts.vue'
import FormPelayananSubjective from '@/Components/Layouts/MalSehat/PelayananPasien/FormPelayananSubjective.vue'
import FormPelayananObjective from '@/Components/Layouts/MalSehat/PelayananPasien/FormPelayananObjective.vue'
import FormPelayananAssesment from '@/Components/Layouts/MalSehat/PelayananPasien/FormPelayananAssesment.vue'
import FormPelayananPlanning from '@/Components/Layouts/MalSehat/PelayananPasien/FormPelayananPlanning.vue'
import FormPelayananStatusPasien from '@/Components/Layouts/MalSehat/PelayananPasien/FormPelayananStatusPasien.vue'

defineOptions({ layout: AppLayouts })

// Ambil no_mr dari props (backend) atau dari URL
const page = usePage()
const props = defineProps({
    no_mr: String,
    pasien: Object,
    alergiMakanan: Array,
    alergiObat: Array,
    dokter: Array,
    kasus: Array
})
const pasien = page.props.pasien
// kontrol tab
const currentTab = ref('subjective')
const mulaiMelayani = ref(false)
function mulaiPemeriksaan() {
    mulaiMelayani.value = true
}

function goBack() {
    router.get(route('mal-sehat.promkes.kesehatanpeduliremaja'))
}
</script>

<template>
    <div class="card m-2 rounded-4 rounded-bottom-0 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center p-3"
            style="background: linear-gradient(135deg, #4682B4, #8EB6C3);">
            <h1 class="fs-5 text-white fw-bold m-0">Pelayanan Kesehatan Peduli Remaja</h1>
            <button @click="goBack"
                class="btn bg-white bg-opacity-25 text-white fw-semibold btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm border-0">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
        </div>

        <!-- Info Pasien -->
        <div class="card m-4 shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Info Pasien Header -->
            <div class="card-header p-4 d-flex align-items-center justify-content-between rounded-4 rounded-bottom-0"
                style="background: linear-gradient(135deg, #4682B4, #8EB6C3);">
                <h5 class="text-white m-0 d-flex align-items-center">
                    <i class="fas fa-user-circle me-2 text-warning"></i> Informasi Pasien
                </h5>
                <span class="bg-white bg-opacity-25 px-3 py-1 rounded-pill small text-white">
                    <i class="fas fa-id-card me-2"></i>{{ pasien.no_mr }}
                </span>
            </div>

            <!-- Body -->
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="col-md-3 py-2 text-secondary">Nama</td>
                            <td class="col-md-3 fw-semibold">{{ pasien.nama }}</td>
                            <td class="col-md-3 text-secondary">Alamat</td>
                            <td class="col-md-3 fw-semibold">{{ pasien.alamat }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="py-2 text-secondary">JK / Umur</td>
                            <td class="fw-semibold">{{ pasien.jk || '-' }} / {{ pasien.umur || '-' }}</td>
                            <td class="text-secondary">Poli</td>
                            <td class="fw-semibold">{{ pasien.poli || '-' }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="py-2 text-secondary">Tanggal Kunjungan</td>
                            <td class="fw-semibold">{{ pasien.tanggal }}</td>
                            <td class="text-secondary">BPJS</td>
                            <td class="fw-semibold">{{ pasien.bpjs || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-secondary">No. RM / NIK</td>
                            <td colspan="3" class="fw-semibold">{{ pasien.no_mr }} / {{ pasien.nik }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="card-footer bg-light d-flex flex-wrap gap-2 justify-content-end p-3 rounded-bottom-4">
                <Link href="/mal-sehat/promkes/surat-keterangan"
                    class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                <i class="fas fa-file-alt"></i> Surat Keterangan
                </Link>
                <button
                    class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-file-medical"></i> Surat Rujukan
                </button>
                <button
                    class="btn btn-outline-success btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-history"></i> Riwayat Pasien
                </button>
                <button
                    class="btn btn-outline-success btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-notes-medical"></i> CPPT
                </button>
                <button
                    class="btn btn-outline-success btn-sm d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-user-md"></i> UKK
                </button>
                <button v-if="!mulaiMelayani"
                    class="btn btn-success btn-sm d-flex align-items-center gap-2 px-4 fw-semibold text-white shadow-sm rounded-pill"
                    style="background: linear-gradient(135deg, #4682B4, #8EB6C3); border: none;"
                    @click="mulaiPemeriksaan">
                    <i class="fas fa-stethoscope"></i> Mulai Pemeriksaan
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div v-if="mulaiMelayani" class="card shadow-sm border-0 rounded-4">
            <div class="card-header p-3 bg-white border-bottom rounded-top-4">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="d-flex position-relative gap-2">
                        <a href="#" class="btn tab-item px-3 py-2 rounded-pill"
                            :class="currentTab === 'subjective' ? 'text-white' : 'text-muted'"
                            :style="currentTab === 'subjective' ? 'background: linear-gradient(135deg, #4682B4, #5A9BD5);' : 'background: #f8f9fa;'"
                            @click.prevent="currentTab = 'subjective'">Subjective</a>
                        <a href="#" class="btn tab-item px-3 py-2 rounded-pill"
                            :class="currentTab === 'objective' ? 'text-white' : 'text-muted'"
                            :style="currentTab === 'objective' ? 'background: linear-gradient(135deg, #4682B4, #5A9BD5);' : 'background: #f8f9fa;'"
                            @click.prevent="currentTab = 'objective'">Objective</a>
                        <a href="#" class="btn tab-item px-3 py-2 rounded-pill"
                            :class="currentTab === 'assesment' ? 'text-white' : 'text-muted'"
                            :style="currentTab === 'assesment' ? 'background: linear-gradient(135deg, #4682B4, #5A9BD5);' : 'background: #f8f9fa;'"
                            @click.prevent="currentTab = 'assesment'">Assesment</a>
                        <a href="#" class="btn tab-item px-3 py-2 rounded-pill"
                            :class="currentTab === 'planning' ? 'text-white' : 'text-muted'"
                            :style="currentTab === 'planning' ? 'background: linear-gradient(135deg, #4682B4, #5A9BD5);' : 'background: #f8f9fa;'"
                            @click.prevent="currentTab = 'planning'">Planning</a>
                        <a href="#" class="btn tab-item px-3 py-2 rounded-pill"
                            :class="currentTab === 'status_pasien' ? 'text-white' : 'text-muted'"
                            :style="currentTab === 'status_pasien' ? 'background: linear-gradient(135deg, #4682B4, #5A9BD5);' : 'background: #f8f9fa;'"
                            @click.prevent="currentTab = 'status_pasien'">Status Pasien</a>
                    </div>
                    <div class="ms-auto">
                        <Link href="#" class="btn btn-sm rounded-pill px-3 shadow-sm text-white"
                            style="background: linear-gradient(135deg, #4682B4, #5A9BD5);">
                        <i class="fas fa-paper-plane me-2"></i> Kirim RME v.1 ke SATU SEHAT
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="card-body p-4">
                <FormPelayananSubjective v-if="currentTab === 'subjective'" :data="pasien.subjective"
                    :alergi-makanan="alergiMakanan" :alergi-obat="alergiObat" />
                <FormPelayananObjective v-if="currentTab === 'objective'" :data="pasien.objective" :dokter="dokter" />
                <FormPelayananAssesment v-if="currentTab === 'assesment'" :data="pasien.assesment" :kasus="kasus" />
                <FormPelayananPlanning v-if="currentTab === 'planning'" :data="pasien.planning" />
                <FormPelayananStatusPasien v-if="currentTab === 'status_pasien'" :data="pasien.status_pasien" />
            </div>
        </div>
    </div>
</template>
