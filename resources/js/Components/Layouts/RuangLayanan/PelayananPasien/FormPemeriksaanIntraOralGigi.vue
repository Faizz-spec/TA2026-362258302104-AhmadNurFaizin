<template>
    <div class="row card-body">
        <div class="col-6">
            <div class="mb-3">
                <label for="tanggal-anamnesa" class="form-label fw-bold">Perkusi</label>
                <select type="text" id="tanggal-anamnesa" class="form-control" v-model="props.form.perkusi">
                    <option value="" selected>--Pilih--</option>
                    <option value="1">+</option>
                    <option value="0">-</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggal-anamnesa" class="form-label fw-bold">Druk</label>
                <select type="text" id="tanggal-anamnesa" class="form-control" v-model="props.form.druk">
                    <option value="" selected>--Pilih--</option>
                    <option value="1">+</option>
                    <option value="0">-</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggal-anamnesa" class="form-label fw-bold">Palpasi</label>
                <select type="text" id="tanggal-anamnesa" class="form-control" v-model="props.form.palpasi">
                    <option value="" selected>--Pilih--</option>
                    <option value="1">+</option>
                    <option value="0">-</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggal-anamnesa" class="form-label fw-bold">Luxasi</label>
                <input type="text" class="form-control" v-model="props.form.luxasi">

            </div>
            <div class="mb-3">
                <label for="tanggal-anamnesa" class="form-label fw-bold">Vitalitas</label>
                <select type="text" id="tanggal-anamnesa" class="form-control" v-model="props.form.vitalitas">
                    <option value="" selected>--Pilih--</option>
                    <option value="1">+</option>
                    <option value="0">-</option>
                </select>
            </div>

        </div>

    </div>
</template>
<script setup>
const props = defineProps({
    form: Object
});
</script>