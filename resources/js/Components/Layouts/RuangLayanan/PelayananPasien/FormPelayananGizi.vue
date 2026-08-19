<template>
    <div class="container py-4">
        <div class="card shadow-sm border-0 rounded-4">
            <form @submit.prevent="submit" class="card-body">
                <!-- ====== BARIS 1 ====== -->
                <div class="row g-4 mb-4">
                    <!-- Kolom 1: Status Gizi -->
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-3 text-decoration-underline">Status Gizi</h5>
                        <div class="mb-3">
                            <label class="form-label">BB</label>
                            <input type="number" class="form-control" readonly :value="props.dataAnamnesa.beratBadan" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">TB</label>
                            <input type="number" class="form-control" readonly
                                :value="props.dataAnamnesa.tinggiBadan" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Umur</label>
                            <input type="number" class="form-control" readonly :value="props.dataPasien.umur" />
                        </div>

                        <div class="row">
                            <div class="col-12" v-for="(value, key) in statusRasio" :key="key">
                                <label class="form-label">{{ key }}</label>
                                <input :value="value" class="form-control mb-3" readonly />
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Kebutuhan Energi -->
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-3 text-decoration-underline">Kebutuhan Energi</h5>

                        <div class="mb-3">
                            <label class="form-label">Energi</label>
                            <input v-model="form.energi" type="number" class="form-control" placeholder="Kkal" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Protein</label>
                            <input v-model="form.protein" type="number" class="form-control" placeholder="gr" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lemak</label>
                            <input v-model="form.lemak" type="number" class="form-control" placeholder="gr" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Karbohidrat</label>
                            <input v-model="form.karbohidrat" type="number" class="form-control" placeholder="gr" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Biokimia</label>
                            <textarea class="form-control mt-2" rows="2" placeholder="Biokimia"
                                v-model="form.biokimia"></textarea>
                        </div>

                    </div>


                    <!-- Kolom 3: Klinis / Fisik -->
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-3 text-decoration-underline">Klinis / Fisik</h5>

                        <div class="mb-2">
                            <label class="form-label">Sulit menelan</label>
                            <div>
                                <label class="me-3">
                                    <input type="radio" name="sulitMenelan" value="Ya" v-model="form.sulitMenelan" /> Ya
                                </label>
                                <label>
                                    <input type="radio" name="sulitMenelan" value="Tidak" v-model="form.sulitMenelan" />
                                    Tidak
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label ">Sulit mengunyah</label>
                            <div>
                                <label class="me-3">
                                    <input type="radio" name="sulitMengunyah" value="Ya"
                                        v-model="form.sulitMengunyah" /> Ya
                                </label>
                                <label>
                                    <input type="radio" name="sulitMengunyah" value="Tidak"
                                        v-model="form.sulitMengunyah" /> Tidak
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Mual</label>
                            <div>
                                <label class="me-3">
                                    <input type="radio" name="mual" value="Ya" v-model="form.mual" /> Ya
                                </label>
                                <label>
                                    <input type="radio" name="mual" value="Tidak" v-model="form.mual" /> Tidak
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Konstipasi</label>
                            <div>
                                <label class="me-3">
                                    <input type="radio" name="konstipasi" value="Ya" v-model="form.konstipasi" /> Ya
                                </label>
                                <label>
                                    <input type="radio" name="konstipasi" value="Tidak" v-model="form.konstipasi" />
                                    Tidak
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Nafsu makan</label>
                            <div>
                                <label class="me-3">
                                    <input type="radio" name="nafsuMakan" value="+" v-model="form.nafsuMakan" /> +
                                </label>
                                <label>
                                    <input type="radio" name="nafsuMakan" value="-" v-model="form.nafsuMakan" /> -
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ====== BARIS 2 ====== -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-decoration-underline">Riwayat Gizi</h5>
                    <div class="mb-3">
                        <label class="form-label">Pola makan di rumah</label>
                        <textarea class="form-control" rows="2" v-model="form.polaMakan"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pantangan makanan</label><br />
                        <label class="me-3"><input type="radio" value="Ya" v-model="form.pantangan" /> Ya</label>
                        <label><input type="radio" value="Tidak" v-model="form.pantangan" /> Tidak</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Riwayat personal</label>
                        <textarea class="form-control" rows="2" v-model="form.riwayatPersonal"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Suplemen gizi</label><br />
                        <label class="me-3"><input type="radio" value="Ya" v-model="form.suplemen" /> Ya</label>
                        <label><input type="radio" value="Tidak" v-model="form.suplemen" /> Tidak</label>
                    </div>
                </div>

                <!-- ====== BARIS 3 ====== -->
                <div>
                    <h5 class="fw-bold mb-3 text-decoration-underline">Nutrisi / Intervensi</h5>
                    <div class="mb-3">
                        <label class="form-label">Diet</label>
                        <textarea class="form-control" rows="2" v-model="form.diet"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tujuan edukasi</label>
                        <textarea class="form-control" rows="2" v-model="form.tujuanEdukasi"></textarea>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Kontrol ulang</label>
                            <select class="form-select" v-model="form.kontrolUlang">
                                <option disabled value="">- pilih -</option>
                                <option>1 minggu</option>
                                <option>2 minggu</option>
                                <option>3 minggu</option>
                                <option>1 bulan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenaga Medis Gizi</label>
                            <select name="" id="" class="form-control" v-model="form.TenagaMedisAskep">
                                <option :value="item.idDokter" v-for="item in TenagaMedisAskep">{{ item.nmDokter }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="text-end mt-4">
                    <button class="btn btn-success px-4 rounded-3" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { route } from "ziggy-js";
import Swal from "sweetalert2";

const emit = defineEmits(['dataAnamnesa-update']);
const props = defineProps({
    dataAnamnesa: Object,
    dataPasien: Object,
    TenagaMedisAskep: Object,
    idLoket: String,
    gizi: Object
})
console.log('gizi untuk', props.gizi)

const form = useForm({
    energi: props.gizi?.energi ?? "",
    protein: props.gizi?.protein ?? "",
    lemak: props.gizi?.lemak ?? "",
    karbohidrat: props.gizi?.karbohidrat ?? "",
    biokimia: props.gizi?.biokimia ?? "",

    sulitMenelan: props.gizi?.sulitMenelan ?? "",
    sulitMengunyah: props.gizi?.sulitMengunyah ?? "",
    mual: props.gizi?.mual ?? "",
    konstipasi: props.gizi?.konstipasi ?? "",
    nafsuMakan: props.gizi?.nafsuMakan ?? "",

    polaMakan: props.gizi?.polaMakan ?? "",
    pantangan: props.gizi?.pantangan ?? "",
    riwayatPersonal: props.gizi?.riwayatPersonal ?? "",
    suplemen: props.gizi?.suplemenGizi ?? "",

    diet: props.gizi?.diet ?? "",
    tujuanEdukasi: props.gizi?.tujuanEdukasi ?? "",
    kontrolUlang: props.gizi?.kontrolUlang ?? "",
    TenagaMedisAskep: props.gizi?.tenagaMedisGizi ?? ""
});

function submit() {
    form.post(
        route('ruang-layanan.simpan-gizi', props.dataPasien.idLoket),
        {
            preserveScroll: false,
            onSuccess: () => {
                Swal.fire({
                    title: 'Sukses',
                    icon: 'success',
                    text: 'Data Gizi Tersimpan!',
                    timer: 1600,
                    showConfirmButton: false
                });
             emit('dataAnamnesa-update');
            }
        }
    );
}


const statusRasio = computed(() => ({
    "BB/TB": props.dataAnamnesa.beratBadan ? (props.dataAnamnesa.beratBadan / props.dataAnamnesa.tinggiBadan).toFixed(2) : "",
    "BB/U": props.dataPasien.umur ? (props.dataAnamnesa.beratBadan / (props.dataPasien.umur * 0.5)).toFixed(2) : "",
    "TB/U": props.dataPasien.umur ? (props.dataAnamnesa.tinggiBadan / (props.dataPasien.umur * 0.5)).toFixed(2) : ""
}));
</script>

<style scoped>
.form-label {
    font-weight: 600;
}
</style>
