<template>
  <h6 class="mb-3 fw-semibold text-success">Riwayat Terkait Makanan</h6>
  <form @submit.prevent="saveForm">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="sarapan" class="form-label fw-semibold">1. Sarapan</label>
            <select class="form-select">
              <option disabled>Select..</option>
              <option>Setiap Hari</option>
              <option>Tidak Setiap Hari</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="sarapan" class="form-label fw-semibold">2. Makan Siang</label>
            <select class="form-select">
              <option disabled>Select..</option>
              <option>Setiap Hari</option>
              <option>Tidak Setiap Hari</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="sarapan" class="form-label fw-semibold">3. Makan Malam</label>
            <select class="form-select">
              <option disabled>Select..</option>
              <option>Setiap Hari</option>
              <option>Tidak Setiap Hari</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="cemilan" class="form-label fw-semibold">4. Cemilan</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Sering</option>
              <option>Kadang-Kadang</option>
              <option>Jarang</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="karbohidrat" class="form-label fw-semibold">5. Karbohidrat</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Sering</option>
              <option>Kadang-Kadang</option>
              <option>Jarang</option>
              <option>Select...</option>
              <option>Nasi</option>
              <option>Roti</option>
              <option>kentang</option>
              <option>jagung</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="protein" class="form-label fw-semibold">6. Protein</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Daging</option>
              <option>Ayam</option>
              <option>Ikan</option>
              <option>Kacang-Kacangan</option>
              <option>Tahu/Tempe</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="sayuran" class="form-label fw-semibold">7. Sayuran</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Sayuran Hijau</option>
              <option>Wortel</option>
              <option>Lagu</option>
              <option>Tomat</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-section">
          <div class="mb-3">
            <label for="buah-buahan" class="form-label fw-semibold">8. Buah-Buahan</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Apel</option>
              <option>Pisang</option>
              <option>Jeruk</option>
              <option>Pepaya</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="produkSusu" class="form-label fw-semibold">9. Produk Susu</label>
            <select class="form-select">
              <option>Select...</option>
              <option>Susu</option>
              <option>Keju</option>
              <option>Yogurt</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-3">
            <label for="konsumsiAir" class="form-label fw-semibold"
              >10. Konsumsi Air Putih per Hari</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Kurang dari 1 liter</option>
              <option>1-2 liter</option>
              <option>Lebih dari 2 liter</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="konsumsiMinumanManis" class="form-label fw-semibold"
              >11. Konsumsi Minuman Manis</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Harian</option>
              <option>Mingguan</option>
              <option>Jarang</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="penyakitGizi" class="form-label fw-semibold"
              >12. Konsumsi Minuman Berkafein</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Harian</option>
              <option>Mingguan</option>
              <option>Jarang</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="alergiMakanan" class="form-label fw-semibold"
              >13. Apakah pasien memiliki alergi terhadap makanan tertentu</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Iya</option>
              <option>Tidak</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="penyakitGizi" class="form-label fw-semibold"
              >14. Apakah pasien menjalani diet khusus</label
            >
            <select class="form-select">
              <option>Select...</option>
              <option>Vegetarian</option>
              <option>Vegan</option>
              <option>Diet Rendah Karbohidrat</option>
              <option>Diet Bebas Gluten</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" rows="2" placeholder="Deskripsi..."></textarea>
          </div>
          <div class="mb-2 text-start">
            <button type="submit" class="btn btn-success w-100 px-4 shadow-sm mt-2">
              <i class="bi bi-save me-1"></i> Simpan Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
  import { ref } from 'vue';

  const saveForm = () => {
    alert('Data disimpan!');
  };
</script>
