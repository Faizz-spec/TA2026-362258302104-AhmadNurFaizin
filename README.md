# SIMPUSWANGI

**Sistem Informasi Manajemen Puskesmas (SIMPUS)** merupakan aplikasi berbasis web yang dikembangkan untuk mendukung proses pelayanan kesehatan di Puskesmas, khususnya pelayanan **Kesehatan Ibu dan Anak (KIA)** pada modul **Manajemen Terpadu Balita Sakit (MTBS)** dan **Manajemen Terpadu Bayi Muda (MTBM)**.

Aplikasi ini dikembangkan sebagai bagian dari Tugas Akhir Program Studi Teknologi Rekayasa Perangkat Lunak, Politeknik Negeri Banyuwangi.

## 👨‍💻 Pengembang

**Nama:** Ahmad Nur Faizin
**NIM:** 362258302104
**Program Studi:** Teknologi Rekayasa Perangkat Lunak
**Institusi:** Politeknik Negeri Banyuwangi
**Tahun:** 2026

---

## 📌 Fitur Utama

SIMPUSWANGI menyediakan beberapa fungsi utama untuk mendukung pelayanan kesehatan di Puskesmas, antara lain:

* Pengelolaan data pasien.
* Pengelolaan pemeriksaan MTBS.
* Pengelolaan pemeriksaan MTBM.
* Pencatatan data subjektif dan objektif pasien.
* Sistem klasifikasi kondisi pasien berbasis **Rule-Based System**.
* Pemberian rekomendasi berdasarkan hasil pemeriksaan.
* Perencanaan dan pencatatan tindakan tenaga kesehatan.
* Pengelolaan imunisasi.
* Pengelolaan rujukan pasien.
* Pengelolaan laporan pelayanan.
* Dashboard informasi pelayanan.
* Integrasi data pelayanan kesehatan dengan **SATUSEHAT**.

---

## 🧠 Rule-Based System

Pada modul MTBS dan MTBM, sistem menggunakan pendekatan **Rule-Based System** untuk membantu proses klasifikasi berdasarkan data pemeriksaan pasien.

Secara umum proses berjalan sebagai berikut:

```text
Data Pemeriksaan Pasien
        ↓
Data Subjektif & Objektif
        ↓
Evaluasi Aturan / Rule
        ↓
Klasifikasi Kondisi
        ↓
Rekomendasi
        ↓
Planning / Tindakan Tenaga Kesehatan
```

Aturan yang digunakan di dalam sistem disusun berdasarkan kombinasi kondisi dari hasil pemeriksaan. Sistem kemudian mencocokkan data pasien dengan rule yang sesuai untuk menghasilkan klasifikasi dan rekomendasi.

Keputusan akhir mengenai diagnosis dan tindakan medis tetap berada pada tenaga kesehatan.

---

## 🏥 Modul MTBS

Modul **Manajemen Terpadu Balita Sakit (MTBS)** digunakan untuk mendukung pemeriksaan dan klasifikasi balita berdasarkan kondisi yang ditemukan selama pelayanan.

Beberapa kelompok pemeriksaan yang tersedia antara lain:

* Tanda bahaya umum.
* Batuk atau kesulitan bernapas.
* Diare.
* Demam.
* Dengue.
* Masalah telinga.
* Anemia.
* Masalah gizi.
* HIV.
* Pemeriksaan dan klasifikasi terkait lainnya.

---

## 👶 Modul MTBM

Modul **Manajemen Terpadu Bayi Muda (MTBM)** digunakan untuk mendukung pelayanan pada bayi muda.

Beberapa pemeriksaan yang tersedia meliputi:

* Infeksi bakteri.
* Diare.
* Ikterus.
* HIV.
* Berat badan.
* Pemberian ASI.
* Kemampuan minum.
* Kondisi bayi muda lainnya.

---

## 🔗 Integrasi SATUSEHAT

Sistem memiliki integrasi dengan platform **SATUSEHAT** menggunakan standar interoperabilitas kesehatan **FHIR (Fast Healthcare Interoperability Resources)**.

Resource yang digunakan pada proses integrasi meliputi:

```text
Organization
Location
Patient
Practitioner
Encounter
Condition
Observation
Procedure
ServiceRequest
QuestionnaireResponse
```

Integrasi memungkinkan data pelayanan yang telah diproses di SIMPUS dikirimkan dalam struktur data yang sesuai dengan standar SATUSEHAT.

---

## 🛠️ Teknologi

Aplikasi dikembangkan menggunakan teknologi berikut:

### Backend

* PHP
* Laravel 10
* MySQL

### Frontend

* Vue.js 3
* Inertia.js
* JavaScript
* Vite

### Integrasi

* REST API
* SATUSEHAT API
* HL7 FHIR

### Tools

* Composer
* NPM
* Git
* GitHub

---

## 📂 Struktur Project

Struktur utama project:

```text
.
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/
│   │   └── Pages/
│   └── views/
│
├── routes/
├── storage/
├── tests/
│
├── artisan
├── composer.json
├── package.json
└── vite.config.js
```

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Faizz-spec/TA2026-362258302104-AhmadNurFaizin.git
```

Masuk ke directory project:

```bash
cd TA2026-362258302104-AhmadNurFaizin
```

### 2. Install Dependency Laravel

```bash
composer install
```

### 3. Install Dependency Frontend

```bash
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example`:

```bash
cp .env.example .env
```

Pada Windows dapat menggunakan:

```powershell
Copy-Item .env.example .env
```

Kemudian sesuaikan konfigurasi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

> Jangan mengunggah file `.env` yang berisi credential, token, password, atau secret ke repository.

### 5. Generate Application Key

```bash
php artisan key:generate
```





### 7. Jalankan Backend

```bash
php artisan serve
```

### 8. Jalankan Frontend

Buka terminal baru:

```bash
npm run dev
```

Aplikasi kemudian dapat diakses melalui alamat lokal yang ditampilkan oleh Laravel.

---

## 🧪 Pengujian

Pengujian dilakukan terhadap rule yang digunakan dalam proses klasifikasi MTBS dan MTBM.

Pengujian mencakup berbagai kombinasi kondisi pemeriksaan untuk memastikan bahwa sistem menghasilkan klasifikasi dan rekomendasi sesuai aturan yang telah ditentukan.

Selain pengujian rule-based, dilakukan pula pengujian terhadap fungsi utama aplikasi dan proses integrasi dengan SATUSEHAT.

---

## 🔒 Keamanan Repository

File yang mengandung informasi sensitif tidak disimpan di repository.

Beberapa file dan directory yang diabaikan melalui `.gitignore` antara lain:

```text
.env
.envneww
/vendor
/node_modules
/public/build
*.zip
*.rar
```

Credential SATUSEHAT, password database, token, client secret, dan informasi sensitif lainnya harus disimpan melalui environment variable.

---

## 🎯 Tujuan Pengembangan

Pengembangan SIMPUSWANGI bertujuan untuk:

* Membantu digitalisasi proses pelayanan kesehatan di Puskesmas.
* Membantu proses pemeriksaan MTBS dan MTBM menjadi lebih terstruktur.
* Membantu tenaga kesehatan memperoleh rekomendasi berdasarkan hasil pemeriksaan.
* Mengurangi inkonsistensi dalam proses pencatatan pelayanan.
* Mendukung interoperabilitas data pelayanan kesehatan dengan SATUSEHAT.

---

## 📄 Lisensi

Project ini dikembangkan untuk keperluan **Tugas Akhir dan akademik**.

Penggunaan, pengembangan, atau distribusi lebih lanjut harus menyesuaikan dengan ketentuan institusi dan pihak terkait.
