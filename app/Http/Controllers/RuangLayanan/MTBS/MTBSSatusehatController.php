<?php

namespace App\Http\Controllers\RuangLayanan\MTBS;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * MTBSSatusehatController
 * -------------------------------------------------------------------------
 * Controller pengiriman data MTBS (usia 2 bulan - 5 tahun) ke SATUSEHAT.
 *
 * Direvisi berdasarkan cross-check terhadap:
 *  - Postman Collection resmi "12. Use Case - MTBS Prioritas"
 *  - Buku Panduan SATUSEHAT (Playbook) MTBS Prioritas v1.0, 18 Nov 2024
 *  - Struktur database lokal (mtbs_subjektif, mtbs_objektif, mtbs_assessment,
 *    mtbs_gizi, mtbs_planning, mtbs_diagnosa_medis, mtbs_statuspasien)
 *
 * CATATAN PENTING (baca sebelum deploy ke produksi):
 *
 * 1. Method publik masih bernama sendSatusehatDummy() dan satusehatPreview()
 *    supaya route + Vue (Satusehat/Preview.vue) yang sudah ada tidak perlu
 *    diubah. Secara isi, method ini SEKARANG mengirim data asli (bukan
 *    dummy) - pertimbangkan rename bertahap (route + method + Vue) di
 *    iterasi berikutnya.
 *
 * 2. Klasifikasi Campak, Dengue, Telinga, dan HIV BELUM bisa dikirim karena
 *    kolom-nya belum disimpan di tabel mtbs_assessment (lihat catatan di
 *    method mapMtbsKlasifikasiObservationCandidates()). Kodenya sudah siap
 *    (dormant) - begitu migration + patch storeAssessmentAuto() di
 *    MTBSController dijalankan, observation ini otomatis mulai terkirim
 *    tanpa perlu ubah file ini lagi.
 *
 * 3. "Status Pertumbuhan" (PB/U, TB/U, LK/U dalam Z-score) SENGAJA di-skip.
 *    Playbook mensyaratkan Observation.valueQuantity berupa Z-score WHO
 *    Child Growth Standards (length/height-for-age dan head-circumference-
 *    for-age), yang butuh tabel referensi LMS per usia (bukan per panjang
 *    seperti tabel who_wfl_wfh_lms yang sudah ada untuk modul Gizi). Tabel
 *    itu tidak tersedia di kode yang dibagikan, jadi lebih aman di-skip
 *    dengan log yang jelas daripada mengirim Z-score palsu ke SATUSEHAT.
 *
 * 4. RelatedPerson (orang tua/wali pasien) tetap di-skip. Tidak ada form/
 *    tabel lokal yang menyimpan NIK, nama, dan No HP orang tua pasien MTBS,
 *    jadi resource ini belum bisa dibangun dari data yang ada.
 *
 * 5. Pemetaan SNOMED untuk "Condition - Keluhan Utama" hanya mencakup
 *    keluhan yang paling umum (lihat KELUHAN_SNOMED_MAP). Keluhan di luar
 *    daftar tetap dikirim (sebagai Condition dengan `text` saja, tanpa
 *    `coding`) supaya datanya tidak hilang, tapi sebaiknya daftar ini terus
 *    dilengkapi.
 *
 * 6. Kode SNOMED untuk "kondisi saat pulang: stabil" (359746009) diambil
 *    dari Playbook (dipakai juga sebagai salah satu nilai klasifikasi
 *    status kegawatan). Perlu diperhatikan: contoh "Condition - Stabil" di
 *    Postman collection justru memakai kode 162668006 ("Patient's condition
 *    unstable") - kemungkinan salah tempel di contoh resmi mereka. Sebelum
 *    produksi, konfirmasi ke tim Juknis SATUSEHAT kode mana yang benar.
 */
class MTBSSatusehatController extends Controller
{
    // =========================================================================
    // SYSTEM URI CONSTANTS
    // =========================================================================
    private const SYS_KEMKES_TERM = 'http://terminology.kemkes.go.id/CodeSystem/clinical-term';
    private const SYS_SNOMED = 'http://snomed.info/sct';
    private const SYS_LOINC = 'http://loinc.org';
    private const SYS_UCUM = 'http://unitsofmeasure.org';
    private const SYS_OBS_CATEGORY = 'http://terminology.hl7.org/CodeSystem/observation-category';
    private const SYS_COND_CLINICAL = 'http://terminology.hl7.org/CodeSystem/condition-clinical';
    private const SYS_COND_CATEGORY = 'http://terminology.hl7.org/CodeSystem/condition-category';
    private const SYS_ICD10 = 'http://hl7.org/fhir/sid/icd-10';
    private const SYS_ICD9CM = 'http://hl7.org/fhir/sid/icd-9-cm';
    private const SYS_DIAGNOSIS_ROLE = 'http://terminology.hl7.org/CodeSystem/diagnosis-role';
    private const SYS_ACT_CODE = 'http://terminology.hl7.org/CodeSystem/v3-ActCode';
    private const SYS_PARTICIPATION = 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType';
    private const QUESTIONNAIRE_MTBS_URL = 'https://fhir.kemkes.go.id/Questionnaire/Q0027';

    // =========================================================================
    // TERMINOLOGY HELPERS
    // =========================================================================

    private function kemkesCode(string $code, string $display): array
    {
        return ['system' => self::SYS_KEMKES_TERM, 'code' => $code, 'display' => $display];
    }

    private function snomedCode(string $code, string $display): array
    {
        return ['system' => self::SYS_SNOMED, 'code' => $code, 'display' => $display];
    }

    private function loincCode(string $code, string $display): array
    {
        return ['system' => self::SYS_LOINC, 'code' => $code, 'display' => $display];
    }

    /**
     * Pemetaan kata kunci keluhan utama (Bahasa Indonesia, bebas) ke SNOMED CT.
     * Daftar awal - silakan ditambah. Kalau tidak ada yang cocok, Condition
     * tetap dikirim dengan `text` saja (tanpa `coding`), supaya keluhan tidak
     * hilang begitu saja.
     */
    private function keluhanUtamaSnomedMap(): array
    {
        return [
            'batuk' => $this->snomedCode('49727002', 'Cough'),
            'demam' => $this->snomedCode('386661006', 'Fever'),
            'panas' => $this->snomedCode('386661006', 'Fever'),
            'diare' => $this->snomedCode('62315008', 'Diarrhea'),
            'mencret' => $this->snomedCode('62315008', 'Diarrhea'),
            'muntah' => $this->snomedCode('422400008', 'Vomiting'),
            'sesak napas' => $this->snomedCode('267036007', 'Dyspnea'),
            'sesak nafas' => $this->snomedCode('267036007', 'Dyspnea'),
            'sukar bernapas' => $this->snomedCode('267036007', 'Dyspnea'),
            'sukar bernafas' => $this->snomedCode('267036007', 'Dyspnea'),
            'kejang' => $this->snomedCode('91175000', 'Convulsion'),
            'tidak mau makan' => $this->snomedCode('49233005', 'Anorexia'),
            'tidak nafsu makan' => $this->snomedCode('49233005', 'Anorexia'),
            'nyeri telinga' => $this->snomedCode('16001004', 'Earache'),
            'sakit telinga' => $this->snomedCode('16001004', 'Earache'),
            'ruam' => $this->snomedCode('271807003', 'Eruption of skin'),
        ];
    }

    /**
     * Tabel spesifikasi 10 klasifikasi MTBS 2 bulan - 5 tahun.
     * Sumber: Playbook SATUSEHAT MTBS Prioritas v1.0, bagian 5.3.1 - 5.3.11
     * (kecuali status pertumbuhan / 5.3.9, lihat catatan class-level di atas).
     *
     * 'source'            -> nama properti pada $assessment (baris mtbs_assessment)
     * 'obs_code'           -> Observation.code.coding
     * 'obs_category'       -> Observation.category.coding.code ('exam' | 'laboratory')
     * 'qr_top'              -> [linkId, text] item level teratas
     * 'qr_gate'             -> [linkId, text] "apakah dilakukan pemeriksaan...?"
     * 'qr_middle'           -> [linkId, text] "apakah anak menderita...?" (null kalau tidak ada)
     * 'qr_result'           -> [linkId, text] "hasil pemeriksaan..."
     * 'values'              -> peta nilai lokal -> Observation.valueCodeableConcept.coding
     * 'negative_finding'    -> subset dari keys 'values' yang berarti "diperiksa, hasil negatif"
     *                          (dipakai untuk menentukan boolean qr_middle)
     */
    private function mtbsClassificationSpec(): array
    {
        return [
            'status_kegawatan' => [
                'source' => 'status_kegawatan',
                'obs_code' => $this->kemkesCode('OC000089', 'Klasifikasi status kegawatan'),
                'obs_category' => 'exam',
                'qr_top' => ['1', 'Pemeriksaan Tanda Bahaya Umum (Status Kegawatan) Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['1.1', 'Apakah dilakukan pemeriksaan tanda bahaya umum (status kegawatan)?'],
                'qr_middle' => null,
                'qr_result' => ['1.1.1', 'Hasil pemeriksaan tanda bahaya umum (status kegawatan)'],
                'negative_finding' => ['Stabil'],
                'values' => [
                    'Gagal jantung paru' => $this->snomedCode('410429000', 'Cardiac arrest'),
                    'Penyakit sangat berat' => $this->kemkesCode('OV000137', 'Penyakit sangat berat'),
                    'Stabil' => $this->snomedCode('359746009', "Patient's condition stable"),
                ],
            ],
            'batuk' => [
                'source' => 'batuk',
                'obs_code' => $this->kemkesCode('OC000091', 'Klasifikasi batuk dan/atau sukar bernapas'),
                'obs_category' => 'exam',
                'qr_top' => ['2', 'Pemeriksaan Batuk dan/atau Sukar Bernapas Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['2.1', 'Apakah dilakukan pemeriksaan batuk dan/atau sukar bernapas?'],
                'qr_middle' => ['2.1.1', 'Apakah anak menderita batuk dan/atau sukar bernapas?'],
                'qr_result' => ['2.1.1.1', 'Hasil pemeriksaan batuk dan/atau sukar bernapas'],
                'negative_finding' => [],
                'values' => [
                    'pneumonia_berat' => $this->kemkesCode('OV000287', 'Pneumonia berat'),
                    'pneumonia' => $this->snomedCode('233604007', 'Pneumonia'),
                    'batuk_bukan_pneumonia' => $this->kemkesCode('OV000288', 'Batuk bukan pneumonia'),
                ],
            ],
            'diare' => [
                'source' => 'diare',
                'obs_code' => $this->kemkesCode('OC000098', 'Klasifikasi diare'),
                'obs_category' => 'exam',
                'qr_top' => ['3', 'Pemeriksaan Diare Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['3.1', 'Apakah dilakukan pemeriksaan diare?'],
                'qr_middle' => ['3.1.1', 'Apakah anak diare?'],
                'qr_result' => ['3.1.1.1', 'Hasil pemeriksaan diare'],
                'negative_finding' => ['tidak_diare'],
                'values' => [
                    'tidak_diare' => $this->snomedCode('162104009', 'Diarrhea not present'),
                    'diare_dehidrasi_berat' => $this->kemkesCode('OV000214', 'Diare dehidrasi berat'),
                    'diare_dehidrasi_ringan_sedang' => $this->kemkesCode('OV000215', 'Diare dehidrasi ringan/sedang'),
                    'diare_tanpa_dehidrasi' => $this->kemkesCode('OV000186', 'Diare tanpa dehidrasi'),
                ],
            ],
            'campak' => [
                'source' => 'campak',
                'obs_code' => $this->kemkesCode('OC000102', 'Klasifikasi campak'),
                'obs_category' => 'exam',
                'qr_top' => ['5', 'Pemeriksaan Campak Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['5.1', 'Apakah dilakukan pemeriksaan campak?'],
                'qr_middle' => ['5.1.1', 'Apakah anak campak saat ini?'],
                'qr_result' => ['5.1.1.1', 'Hasil pemeriksaan campak'],
                'negative_finding' => [],
                'values' => [
                    // NB: playbook membedakan komplikasi mata-saja / mulut-saja / mata+mulut
                    // (OV000230 / OV000231 / OV000232). Rule engine lokal saat ini hanya
                    // menghasilkan satu nilai gabungan 'campak_dengan_komplikasi_mata_mulut',
                    // jadi disamakan ke kode "mata dan mulut" (OV000232) sebagai pendekatan
                    // paling aman. Perbaiki di hitungKlasifikasiMtbs() kalau butuh presisi.
                    'campak_dengan_komplikasi_berat' => $this->kemkesCode('OV000229', 'Campak dengan komplikasi berat'),
                    'campak_dengan_komplikasi_mata_mulut' => $this->kemkesCode('OV000232', 'Campak dengan komplikasi pada mata dan mulut'),
                    'campak' => $this->snomedCode('14189004', 'Measles'),
                ],
            ],
            'dengue' => [
                'source' => 'dengue',
                'obs_code' => $this->kemkesCode('OC000105', 'Klasifikasi infeksi dengue'),
                'obs_category' => 'exam',
                'qr_top' => ['6', 'Pemeriksaan Infeksi Dengue Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['6.1', 'Apakah dilakukan pemeriksaan infeksi dengue?'],
                'qr_middle' => null,
                'qr_result' => ['6.1.1', 'Hasil pemeriksaan infeksi dengue'],
                'negative_finding' => [],
                'values' => [
                    'dengue_berat' => $this->snomedCode('20927009', 'Dengue hemorrhagic fever'),
                    'dengue_dengan_warning_signs' => $this->snomedCode('722863008', 'Dengue with warning signs'),
                    'dengue_tanpa_warning_signs' => $this->snomedCode('722862003', 'Dengue without warning signs'),
                    'demam_mungkin_bukan_dengue' => $this->kemkesCode('OV000129', 'Demam mungkin bukan dengue'),
                ],
            ],
            'telinga' => [
                'source' => 'telinga',
                'obs_code' => $this->kemkesCode('OC000107', 'Klasifikasi masalah telinga'),
                'obs_category' => 'exam',
                'qr_top' => ['7', 'Pemeriksaan Masalah Telinga Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['7.1', 'Apakah dilakukan pemeriksaan masalah telinga?'],
                'qr_middle' => null,
                'qr_result' => ['7.1.1', 'Hasil pemeriksaan masalah telinga'],
                'negative_finding' => ['tidak_ada_infeksi_telinga'],
                'values' => [
                    'mastoiditis' => $this->snomedCode('52404001', 'Mastoiditis'),
                    'infeksi_telinga_akut' => $this->kemkesCode('OV000241', 'Infeksi telinga akut'),
                    'infeksi_telinga_kronis' => $this->kemkesCode('OV000289', 'Infeksi telinga kronis'),
                    'tidak_ada_infeksi_telinga' => $this->kemkesCode('OV000242', 'Tidak ada infeksi telinga'),
                ],
            ],
            'gizi' => [
                'source' => 'gizi',
                'obs_code' => $this->kemkesCode('OC000108', 'Klasifikasi status gizi'),
                'obs_category' => 'exam',
                'qr_top' => ['8', 'Pemeriksaan Status Gizi Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['8.1', 'Apakah dilakukan pemeriksaan status gizi?'],
                'qr_middle' => null,
                'qr_result' => ['8.1.1', 'Hasil pemeriksaan status gizi'],
                'negative_finding' => ['gizi_baik'],
                'values' => [
                    'gizi_buruk_dengan_komplikasi' => $this->kemkesCode('OV000254', 'Gizi buruk dengan komplikasi berat'),
                    'gizi_buruk_tanpa_komplikasi' => $this->kemkesCode('OV000255', 'Gizi buruk tanpa komplikasi'),
                    'gizi_kurang' => $this->snomedCode('65404009', 'Undernutrition'),
                    'gizi_baik' => $this->snomedCode('248324001', 'Well nourished'),
                    'berisiko_gizi_lebih' => $this->kemkesCode('OV000258', 'Risiko Gizi Lebih'),
                    'gizi_lebih' => $this->snomedCode('302872003', 'Overnutrition'),
                    'obesitas' => $this->snomedCode('414915002', 'Obese'),
                ],
            ],
            'anemia' => [
                'source' => 'anemia',
                'obs_code' => $this->kemkesCode('OC000111', 'Klasifikasi anemia'),
                'obs_category' => 'exam',
                'qr_top' => ['10', 'Pemeriksaan Anemia Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['10.1', 'Apakah dilakukan pemeriksaan anemia?'],
                'qr_middle' => null,
                'qr_result' => ['10.1.1', 'Hasil pemeriksaan anemia'],
                'negative_finding' => ['tidak_anemia'],
                'values' => [
                    'anemia_berat' => $this->kemkesCode('OV000272', 'Anemia berat'),
                    'anemia' => $this->snomedCode('271737000', 'Anemia'),
                    'tidak_anemia' => $this->snomedCode('860635002', 'No anemia present'),
                ],
            ],
            'hiv' => [
                'source' => 'hiv',
                'obs_code' => $this->snomedCode('254387007', 'Human immunodeficiency virus infection classification systems'),
                'obs_category' => 'laboratory',
                'qr_top' => ['11', 'Pemeriksaan HIV Usia 2 Bulan - 5 Tahun'],
                'qr_gate' => ['11.1', 'Apakah dilakukan pemeriksaan HIV?'],
                'qr_middle' => null,
                'qr_result' => ['11.1.1', 'Hasil pemeriksaan HIV'],
                'negative_finding' => ['mungkin_bukan_infeksi_hiv'],
                'values' => [
                    'infeksi_hiv_terkonfirmasi' => $this->snomedCode('165816005', 'Human immunodeficiency virus positive'),
                    'terpajan_hiv' => $this->kemkesCode('OV000187', 'Terpajan HIV: mungkin infeksi HIV'),
                    'mungkin_bukan_infeksi_hiv' => $this->kemkesCode('OV000189', 'Bukan infeksi HIV'),
                ],
            ],
            // 'demam' SENGAJA tidak dimasukkan ke tabel generik ini karena struktur
            // QuestionnaireResponse-nya berbeda (ada sub-gate "dilakukan pemeriksaan
            // malaria?" di linkId 4.1.1.1 yang sejajar dengan hasil di 4.1.1.2).
            // Lihat buildDemamObservationAndQrBranch().
        ];
    }

    // =========================================================================
    // PREVIEW PAGE (tidak diubah - Vue Satusehat/Preview.vue bergantung pada
    // bentuk data ini persis seperti sebelumnya)
    // =========================================================================

    public function satusehatPreview($idPoli, $idPelayanan)
    {
        $pasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->where('pel.idpelayanan', $idPelayanan)
            ->select(
                'p.ID as pasien_id',
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'poli.nmPoli',
                'p.alamat',
                'l.tglKunjungan',
                'l.kdPoli',
                'l.idLoket'
            )
            ->first();

        if (!$pasien) {
            abort(404, 'Data pasien tidak ditemukan');
        }

        $kunjunganId = (string) $idPelayanan;

        $subjektif = DB::table('mtbs_subjektif')->where('kunjungan_id', $kunjunganId)->first();
        $objektif = DB::table('mtbs_objektif')->where('kunjungan_id', $kunjunganId)->first();
        $assessment = DB::table('mtbs_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
        $planning = DB::table('mtbs_planning')->where('kunjungan_id', $kunjunganId)->first();
        $gizi = DB::table('mtbs_gizi')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();

        $giziKlasifikasi = ($assessment->gizi ?? null) ?: ($gizi->klasifikasi ?? null);

        $statusPasien = DB::table('mtbs_statuspasien')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $dokter = null;

        if ($statusPasien && !empty($statusPasien->tenaga_medis)) {
            $tenagaMedis = trim((string) $statusPasien->tenaga_medis);

            $dokter = DB::table('master_dokter')
                ->where(function ($q) use ($tenagaMedis) {
                    $q->where('idDokter', $tenagaMedis)
                        ->orWhere('kdDokter', $tenagaMedis)
                        ->orWhere('nmDokter', $tenagaMedis);
                })
                ->first();
        }

        $namaPractitioner = $dokter->nmDokter
            ?? ($statusPasien->tenaga_medis ?? null)
            ?? '-';

        $ihsPractitioner = $dokter->ihs_nakes ?? null;

        $keluhanUtama = $subjektif ? (json_decode($subjektif->keluhan_utama ?? '[]', true) ?: []) : [];
        $tandaBahaya = $objektif ? (json_decode($objektif->tanda_bahaya ?? '[]', true) ?: []) : [];
        $pemeriksaanKhusus = $objektif ? (json_decode($objektif->pemeriksaan_khusus ?? '[]', true) ?: []) : [];
        $klasifikasiGlobal = $assessment ? (json_decode($assessment->klasifikasi_global ?? '[]', true) ?: []) : [];
        $tindakanSegera = $planning ? (json_decode($planning->tindakan_segera ?? '[]', true) ?: []) : [];
        $pengobatan = $planning ? (json_decode($planning->pengobatan ?? '[]', true) ?: []) : [];
        $edukasi = $planning ? (json_decode($planning->edukasi ?? '[]', true) ?: []) : [];

        $preview = [
            'header' => [
                'nama_pasien'       => $pasien->NAMA_LGKP ?? '-',
                'no_rm'             => $pasien->NO_MR ?? '-',
                'nik'               => $pasien->NIK ?? '-',
                'bpjs'              => '-',
                'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan ?? null,
                'jenis_kunjungan'   => $subjektif->jenis_kunjungan ?? 'pertama',
                'poli'              => $pasien->nmPoli ?? 'MTBS',
                'status_layanan'    => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
                'ihs_pasien'        => null,
                'ihs_label'         => 'Belum dicari ke SATUSEHAT',
            ],

            'kunjungan_mtbs' => [
                'encounter_id' => null,
                'location'     => env('SATUSEHAT_LOCATION_NAME', $pasien->nmPoli ?? 'Poli MTBS'),
                'practitioner' => $namaPractitioner,
                'practitioner_ihs' => $ihsPractitioner,
                'practitioner_source' => $dokter ? 'master_dokter' : 'mtbs_statuspasien',
                'status_history' => [
                    'arrived'  => $pasien->tglKunjungan ?? null,
                    'progress' => $pasien->tglPelayanan ?? null,
                    'finished' => !empty($pasien->sudahDilayani) ? now()->format('Y-m-d H:i:s') : null,
                ],
                'keluhan_utama'      => $keluhanUtama,
                'batuk'              => $assessment->batuk ?? null,
                'diare'              => $assessment->diare ?? null,
                'demam'              => $assessment->demam ?? null,
                'gizi'               => $giziKlasifikasi,
                'anemia'             => $assessment->anemia ?? null,
                'klasifikasi_global' => $klasifikasiGlobal,
                'status_kegawatan'   => $assessment->status_kegawatan ?? null,
            ],

            'observasi_mtbs' => [
                'rr'                 => $objektif->rr ?? null,
                'suhu'               => $objektif->suhu ?? null,
                'spo2'               => $objektif->spo2 ?? null,
                'bb'                 => $objektif->bb ?? null,
                'tb'                 => $objektif->tb ?? null,
                'lila'               => $objektif->lila ?? null,
                'lk'                 => $objektif->lk ?? null,
                'status_saga'        => $objektif->status_saga ?? null,
                'tanda_bahaya'       => $tandaBahaya,
                'pemeriksaan_khusus' => $pemeriksaanKhusus,
            ],

            'tatalaksana_mtbs' => [
                'tindakan_segera' => $tindakanSegera,
                'pengobatan'      => $pengobatan,
            ],

            'edukasi_mtbs' => [
                'edukasi'         => $edukasi,
                'catatan'         => $planning->catatan_edukasi ?? null,
                'kunjungan_ulang' => $planning->kunjungan_ulang_hari ?? null,
            ],
        ];

        return Inertia::render('Ruang_Layanan/KIA/MTBS/Satusehat/Preview', [
            'idPelayanan' => $idPelayanan,
            'idPoli' => $idPoli,
            'preview' => $preview,
        ]);
    }

    // =========================================================================
    // ORCHESTRATOR
    // =========================================================================

    public function sendSatusehatDummy(Request $request, $idPoli, $idPelayanan)
    {
        try {
            $bundle = $this->buildSatusehatLocalBundle($idPelayanan);

            if (!$bundle['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => $bundle['message'],
                    'logs' => $bundle['logs'] ?? [],
                ], 422);
            }

            $logs = [];
            $result = [
                'token' => null,
                'organization_id' => null,
                'location_id' => null,
                'patient_id' => null,
                'practitioner_id' => null,
                'encounter_id' => null,
                'condition_ids' => [],
                'observation_ids' => [],
                'procedure_ids' => [],
                'questionnaire_response_id' => null,
                'service_request_id' => null,
            ];

            // =====================================================
            // PHASE 1 - AUTH
            // =====================================================
            $tokenResp = $this->ssGetAccessToken();
            $logs[] = $tokenResp['log'];

            if (!$tokenResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate token SATUSEHAT',
                    'logs' => $logs,
                    'result' => $result,
                ], 500);
            }

            $token = $tokenResp['token'];
            $result['token'] = 'OK';

            // =====================================================
            // PHASE 2 - REFERENCE (Organization, Location, Patient, Practitioner)
            // =====================================================
            $orgId = env('SATUSEHAT_ORG_ID');
            $result['organization_id'] = $orgId;

            $logs[] = $this->makeStepLog('organization', true, 'Organization ID dari env digunakan.', [
                'organization_id' => $orgId,
            ]);

            $locationResp = $this->ssResolveLocation($token);
            $logs[] = $locationResp['log'];

            if (!$locationResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location SATUSEHAT tidak ditemukan',
                    'logs' => $logs,
                    'result' => $result,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $locationId = $locationResp['id'];
            $result['location_id'] = $locationId;

            $patientResp = $this->ssResolvePatient($token, $bundle['data']['pasien']);
            $logs[] = $patientResp['log'];

            if (!$patientResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient SATUSEHAT tidak ditemukan',
                    'logs' => $logs,
                    'result' => $result,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $patientId = $patientResp['id'];
            $result['patient_id'] = $patientId;

            $practitionerResp = $this->ssResolvePractitioner($token, $bundle['data']['nakes']);
            $logs[] = $practitionerResp['log'];

            if (!$practitionerResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Practitioner SATUSEHAT tidak ditemukan',
                    'logs' => $logs,
                    'result' => $result,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $practitionerId = $practitionerResp['id'];
            $result['practitioner_id'] = $practitionerId;

            // RelatedPerson: belum ada form/tabel lokal yang menyimpan NIK, nama,
            // dan No HP orang tua/wali pasien MTBS, jadi resource ini belum bisa
            // dibangun. Lihat catatan class-level poin 4.
            $logs[] = $this->makeStepLog('related_person', true, 'RelatedPerson di-skip: belum ada tabel lokal untuk data orang tua/wali pasien (NIK, nama, No HP, hubungan keluarga).', [
                'status' => 'skipped',
                'butuh' => 'Form + tabel baru untuk data wali pasien MTBS',
            ]);

            // =====================================================
            // PHASE 3 - CORE (Encounter: arrived -> in-progress)
            // =====================================================
            $encounterArrivedResp = $this->ssRegisterEncounterArrived(
                $token,
                $bundle,
                $patientId,
                $practitionerId,
                $locationId,
                $orgId
            );

            $logs[] = $encounterArrivedResp['log'];

            if (!$encounterArrivedResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encounter (arrived) SATUSEHAT gagal dibuat',
                    'logs' => $logs,
                    'result' => $result,
                    'encounter_payload' => $encounterArrivedResp['payload'] ?? null,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $encounterId = $encounterArrivedResp['id'];
            $result['encounter_id'] = $encounterId;

            $encounterInProgressResp = $this->ssStartEncounterInProgress(
                $token,
                $bundle,
                $encounterId,
                $patientId,
                $practitionerId,
                $locationId,
                $orgId
            );

            $logs[] = $encounterInProgressResp['log'];

            if (!$encounterInProgressResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encounter (in-progress) SATUSEHAT gagal diperbarui',
                    'logs' => $logs,
                    'result' => $result,
                    'encounter_payload' => $encounterInProgressResp['payload'] ?? null,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            // =====================================================
            // PHASE 4 - CLINICAL
            // =====================================================

            // ---- 4A. Condition - Keluhan Utama (category: problem-list-item) ----
            $keluhanCandidates = $this->mapMtbsKeluhanUtamaCandidates($bundle);

            if (count($keluhanCandidates) === 0) {
                $logs[] = $this->makeStepLog('condition_keluhan_utama', true, 'Condition keluhan utama di-skip. Tidak ada keluhan utama tercatat.', [
                    'status' => 'skipped',
                ]);
            } else {
                foreach ($keluhanCandidates as $keluhan) {
                    $keluhanResp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                        'category_code' => 'problem-list-item',
                        'category_display' => 'Problem List Item',
                        'code' => $keluhan['code'] ?? null,
                        'text' => $keluhan['text'],
                        'note' => 'Keluhan utama dari mtbs_subjektif.keluhan_utama',
                    ]);

                    $logs[] = $keluhanResp['log'];

                    if ($keluhanResp['ok']) {
                        $result['condition_ids'][] = $keluhanResp['id'];
                    }
                    // Kegagalan kirim keluhan utama TIDAK menghentikan alur -
                    // ini pelengkap anamnesis, bukan data yang memblokir seperti
                    // diagnosis/Encounter.
                }
            }

            // ---- 4B. Observation - Tanda Vital & Antropometri ----
            $vitalCandidates = $this->mapMtbsVitalAntropometriObservations($bundle);
            $observationIdsByKey = [];

            foreach ($vitalCandidates as $key => $obs) {
                $obsResp = $this->ssCreateObservation($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $obs);
                $logs[] = $obsResp['log'];

                if (!$obsResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Observation ({$key}) SATUSEHAT gagal dibuat",
                        'logs' => $logs,
                        'result' => $result,
                        'observation_payload' => $obsResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['observation_ids'][] = $obsResp['id'];
                $observationIdsByKey[$key] = $obsResp['id'];
            }

            if (count($vitalCandidates) === 0) {
                $logs[] = $this->makeStepLog('observation_vital', true, 'Observation tanda vital/antropometri di-skip. Data objektif kosong.', [
                    'status' => 'skipped',
                ]);
            }

            // ---- 4C. Observation - 10 Klasifikasi MTBS (+ Demam/Malaria khusus) ----
            $classificationCandidates = $this->mapMtbsKlasifikasiObservationCandidates($bundle);

            foreach ($classificationCandidates as $key => $obs) {
                $obsResp = $this->ssCreateObservation($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $obs);
                $logs[] = $obsResp['log'];

                if (!$obsResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Observation klasifikasi ({$key}) SATUSEHAT gagal dibuat",
                        'logs' => $logs,
                        'result' => $result,
                        'observation_payload' => $obsResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['observation_ids'][] = $obsResp['id'];
                $observationIdsByKey[$key] = $obsResp['id'];
            }

            if (count($classificationCandidates) === 0) {
                $logs[] = $this->makeStepLog('observation_klasifikasi', true, 'Observation klasifikasi MTBS di-skip. Tidak ada assessment yang bisa dipetakan.', [
                    'status' => 'skipped',
                ]);
            }

            // ---- 4D. QuestionnaireResponse - MTBS (mereferensikan Observation di atas) ----
            $questionnaireItems = $this->buildMtbsQuestionnaireResponseItems($bundle, $observationIdsByKey);

            if (count($questionnaireItems) === 0) {
                $logs[] = $this->makeStepLog('questionnaire_response', true, 'QuestionnaireResponse di-skip. Tidak ada klasifikasi yang bisa direferensikan.', [
                    'status' => 'skipped',
                ]);
            } else {
                $questionnaireResp = $this->ssCreateQuestionnaireResponse(
                    $token,
                    $bundle,
                    $patientId,
                    $encounterId,
                    $practitionerId,
                    $questionnaireItems
                );

                $logs[] = $questionnaireResp['log'];

                if (!$questionnaireResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QuestionnaireResponse SATUSEHAT gagal dibuat',
                        'logs' => $logs,
                        'result' => $result,
                        'questionnaire_payload' => $questionnaireResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['questionnaire_response_id'] = $questionnaireResp['id'];
            }

            // ---- 4E. Condition - Diagnosis (dari mtbs_diagnosa_medis) ----
            $conditionRefs = [];
            $conditionPayloads = [];

            $conditionCandidates = $this->mapMtbsConditionCandidates($bundle);

            if (count($conditionCandidates) === 0) {
                $logs[] = $this->makeStepLog('condition_diagnosis', true, 'Condition diagnosis di-skip. Tidak ada diagnosis MTBS yang bisa dipetakan.', [
                    'status' => 'skipped',
                ]);
            } else {
                foreach ($conditionCandidates as $idx => $diagnosis) {
                    $conditionResp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                        'category_code' => 'encounter-diagnosis',
                        'category_display' => 'Encounter Diagnosis',
                        'code' => ['system' => self::SYS_ICD10, 'code' => $diagnosis['code'], 'display' => $diagnosis['display']],
                        'text' => $diagnosis['display'],
                        'note' => 'MTBS diagnosis dari mtbs_diagnosa_medis - rank ' . ($idx + 1),
                    ]);

                    $logs[] = $conditionResp['log'];
                    $conditionPayloads[] = $conditionResp['payload'] ?? null;

                    if (!$conditionResp['ok']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Condition diagnosis SATUSEHAT gagal dibuat',
                            'logs' => $logs,
                            'result' => $result,
                            'condition_payloads' => $conditionPayloads,
                            'local_preview' => $bundle['data'],
                        ], 422);
                    }

                    $result['condition_ids'][] = $conditionResp['id'];
                    $conditionRefs[] = [
                        'id' => $conditionResp['id'],
                        'display' => $diagnosis['display'],
                        'rank' => $idx + 1,
                    ];
                }
            }

            // ---- 4F. Condition - Kondisi Saat Meninggalkan Faskes ----
            $dischargeCandidate = $this->mapMtbsDischargeConditionCandidate($bundle);

            if ($dischargeCandidate === null) {
                $logs[] = $this->makeStepLog('condition_discharge', true, 'Condition kondisi-pulang di-skip. mtbs_statuspasien belum diisi.', [
                    'status' => 'skipped',
                ]);
            } else {
                $dischargeResp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                    'category_code' => 'problem-list-item',
                    'category_display' => 'Problem List Item',
                    'code' => $dischargeCandidate['code'],
                    'text' => $dischargeCandidate['code']['display'],
                    'note' => 'Kondisi saat meninggalkan faskes (lihat catatan class-level poin 6 soal ambiguitas kode SNOMED).',
                ]);

                $logs[] = $dischargeResp['log'];

                if ($dischargeResp['ok']) {
                    $result['condition_ids'][] = $dischargeResp['id'];
                }
            }

            // ---- 4G. Finish Encounter (status: finished + diagnosis[]) ----
            if (count($conditionRefs) > 0) {
                $finishEncounterResp = $this->ssFinishEncounter(
                    $token,
                    $bundle,
                    $encounterId,
                    $patientId,
                    $practitionerId,
                    $locationId,
                    $orgId,
                    $conditionRefs
                );

                $logs[] = $finishEncounterResp['log'];

                if (!$finishEncounterResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Encounter SATUSEHAT gagal di-finish',
                        'logs' => $logs,
                        'result' => $result,
                        'encounter_payload' => $finishEncounterResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }
            } else {
                $logs[] = $this->makeStepLog('finish_encounter', true, 'Finish Encounter di-skip karena tidak ada Condition diagnosis.', [
                    'status' => 'skipped',
                ]);
            }

            // ---- 4H. Procedure ----
            $procedureCandidates = $this->mapMtbsProcedureCandidates($bundle);

            if (count($procedureCandidates) === 0) {
                $logs[] = $this->makeStepLog('procedure', true, 'Procedure di-skip. Tidak ada tindakan_segera yang bisa dipetakan.', [
                    'status' => 'skipped',
                ]);
            } else {
                foreach ($procedureCandidates as $proc) {
                    $procedureResp = $this->ssCreateProcedure(
                        $token,
                        $bundle,
                        $patientId,
                        $encounterId,
                        $practitionerId,
                        $orgId,
                        $proc
                    );

                    $logs[] = $procedureResp['log'];

                    if (!$procedureResp['ok']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Procedure SATUSEHAT gagal dibuat',
                            'logs' => $logs,
                            'result' => $result,
                            'procedure_payload' => $procedureResp['payload'] ?? null,
                            'local_preview' => $bundle['data'],
                        ], 422);
                    }

                    $result['procedure_ids'][] = $procedureResp['id'];
                }
            }

            // ---- 4I. ServiceRequest (kontrol ulang) ----
            $serviceRequestCandidate = $this->mapMtbsServiceRequestCandidate($bundle, $conditionRefs);

            if (!$serviceRequestCandidate) {
                $logs[] = $this->makeStepLog('service_request', true, 'ServiceRequest di-skip. Tidak ada kontrol ulang / tindak lanjut yang bisa dikirim.', [
                    'status' => 'skipped',
                ]);
            } else {
                $serviceRequestResp = $this->ssCreateServiceRequest(
                    $token,
                    $bundle,
                    $patientId,
                    $encounterId,
                    $practitionerId,
                    $locationId,
                    $orgId,
                    $serviceRequestCandidate
                );

                $logs[] = $serviceRequestResp['log'];

                if (!$serviceRequestResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ServiceRequest SATUSEHAT gagal dibuat',
                        'logs' => $logs,
                        'result' => $result,
                        'service_request_payload' => $serviceRequestResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['service_request_id'] = $serviceRequestResp['id'];
            }

            return response()->json([
                'success' => true,
                'message' => 'SATUSEHAT berhasil dijalankan sampai seluruh alur utama dari data database MTBS.',
                'logs' => $logs,
                'result' => $result,
                'local_preview' => $bundle['data'],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('MTBS sendSatusehat error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'idPelayanan' => $idPelayanan,
                'idPoli' => $idPoli,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat kirim data MTBS ke SATUSEHAT',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // LOCAL DATA BUNDLE
    // =========================================================================

    private function buildSatusehatLocalBundle($idPelayanan): array
    {
        $pasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->where('pel.idpelayanan', $idPelayanan)
            ->select(
                'p.ID as pasien_id',
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.JENIS_KLMIN',
                'p.TGL_LHR',
                'poli.nmPoli',
                'p.alamat',
                'l.tglKunjungan',
                'l.kdPoli',
                'l.idLoket'
            )
            ->first();

        if (!$pasien) {
            return [
                'ok' => false,
                'message' => 'Data pasien lokal tidak ditemukan.',
                'logs' => [
                    $this->makeStepLog('load_local', false, 'Data pasien lokal tidak ditemukan.', [
                        'idPelayanan' => $idPelayanan,
                    ])
                ],
            ];
        }

        $kunjunganId = (string) $idPelayanan;

        $subjektif = DB::table('mtbs_subjektif')->where('kunjungan_id', $kunjunganId)->first();
        $objektif = DB::table('mtbs_objektif')->where('kunjungan_id', $kunjunganId)->first();
        $assessment = DB::table('mtbs_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
        $gizi = DB::table('mtbs_gizi')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();

        // Pastikan assessment.gizi tetap terisi dari mtbs_gizi.klasifikasi walaupun
        // mtbs_assessment.gizi masih null (storeGizi() dan storeAssessmentAuto() bisa
        // berjalan tidak berurutan).
        if ($assessment) {
            $assessment->gizi = ($assessment->gizi ?? null) ?: ($gizi->klasifikasi ?? null);
        } else {
            $assessment = (object) [
                'batuk' => null,
                'diare' => null,
                'demam' => null,
                'campak' => null,   // dormant - lihat catatan class-level poin 2
                'dengue' => null,   // dormant
                'telinga' => null,  // dormant
                'gizi' => $gizi->klasifikasi ?? null,
                'anemia' => null,
                'hiv' => null,      // dormant
                'klasifikasi_global' => json_encode([]),
                'status_kegawatan' => null,
            ];
        }

        $planning = DB::table('mtbs_planning')->where('kunjungan_id', $kunjunganId)->first();

        $diagnosaMedis = DB::table('mtbs_diagnosa_medis')
            ->where('kunjungan_id', $kunjunganId)
            ->orderBy('id')
            ->get();

        $alergi = DB::table('mtbs_alergi')->where('kunjungan_id', $kunjunganId)->first();

        // Practitioner/nakes diambil dari status pasien (mtbs_statuspasien.tenaga_medis),
        // dicocokkan ke master_dokter untuk mendapatkan ihs_nakes / kdDokter / nama.
        $statusPasien = DB::table('mtbs_statuspasien')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $dokter = null;

        if ($statusPasien && !empty($statusPasien->tenaga_medis)) {
            $tenagaMedis = trim((string) $statusPasien->tenaga_medis);

            $dokter = DB::table('master_dokter')
                ->where(function ($q) use ($tenagaMedis) {
                    $q->where('idDokter', $tenagaMedis)
                        ->orWhere('kdDokter', $tenagaMedis)
                        ->orWhere('nmDokter', $tenagaMedis);
                })
                ->first();
        }

        $data = [
            'pasien' => [
                'pasien_id' => $pasien->pasien_id,
                'nama' => $pasien->NAMA_LGKP,
                'nik' => $pasien->NIK,
                'no_rm' => $pasien->NO_MR,
                'alamat' => $pasien->alamat,
                'jenis_kelamin' => $pasien->JENIS_KLMIN,
                'birth_date' => $pasien->TGL_LHR ? Carbon::parse($pasien->TGL_LHR)->format('Y-m-d') : null,
            ],
            'nakes' => [
                'source' => 'mtbs_statuspasien.master_dokter',
                'tenaga_medis_raw' => $statusPasien->tenaga_medis ?? null,
                'id' => $dokter->idDokter ?? null,
                'nama' => $dokter->nmDokter ?? ($statusPasien->tenaga_medis ?? null),
                'nik' => $dokter->kdDokter ?? null,
                'kode' => $dokter->kdDokter ?? null,
                'ihs' => $dokter->ihs_nakes ?? null,
                'birth_date' => null,
            ],
            'kunjungan' => [
                'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan,
                'poli' => $pasien->nmPoli,
                'kdPoli' => $pasien->kdPoli,
                'id_loket' => $pasien->idLoket,
                'status_layanan' => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
            ],
            'subjektif' => $subjektif,
            'objektif' => $objektif,
            'assessment' => $assessment,
            'gizi' => $gizi,
            'planning' => $planning,
            'diagnosa_medis' => $diagnosaMedis,
            'alergi' => $alergi,
            'status_pasien' => $statusPasien,
            'dokter' => $dokter,
            'kunjungan_id' => $kunjunganId,
            'id_pelayanan' => $idPelayanan,
        ];

        return [
            'ok' => true,
            'message' => 'Data lokal siap dari database MTBS.',
            'data' => $data,
        ];
    }

    // =========================================================================
    // PHASE 1-2: AUTH & REFERENCE RESOLUTION
    // =========================================================================

    private function ssGetAccessToken(): array
    {
        try {
            $url = rtrim(env('SATUSEHAT_AUTH_URL'), '/') . '/accesstoken?grant_type=client_credentials';

            $res = Http::asForm()->post($url, [
                'client_id' => env('SATUSEHAT_CLIENT_ID'),
                'client_secret' => env('SATUSEHAT_CLIENT_SECRET'),
            ]);

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'token' => null,
                    'log' => $this->makeStepLog('token', false, 'Generate token gagal.', [
                        'status' => $res->status(),
                        'body' => $res->json(),
                    ]),
                ];
            }

            $json = $res->json();

            return [
                'ok' => true,
                'token' => $json['access_token'] ?? null,
                'log' => $this->makeStepLog('token', true, 'Generate token berhasil.', [
                    'token_type' => $json['token_type'] ?? null,
                    'expires_in' => $json['expires_in'] ?? null,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'token' => null,
                'log' => $this->makeStepLog('token', false, 'Generate token exception.', [
                    'error' => $e->getMessage(),
                ]),
            ];
        }
    }

    private function ssResolveLocation(string $token): array
    {
        try {
            $locationIdFromEnv = env('SATUSEHAT_LOCATION_ID');

            if (!empty($locationIdFromEnv)) {
                return [
                    'ok' => true,
                    'id' => $locationIdFromEnv,
                    'log' => $this->makeStepLog('location', true, 'Location ID dari env digunakan.', [
                        'location_id' => $locationIdFromEnv,
                    ]),
                ];
            }

            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $locationName = env('SATUSEHAT_LOCATION_NAME', 'Poli Tumbuh Kembang');
            $orgId = env('SATUSEHAT_ORG_ID');

            $res = Http::withToken($token)
                ->acceptJson()
                ->get($base . '/Location', [
                    'name' => $locationName,
                    'organization' => $orgId,
                ]);

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('location', false, 'Search Location gagal.', [
                        'status' => $res->status(),
                        'body' => $res->json(),
                    ]),
                ];
            }

            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;

            if (!$entry) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('location', false, 'Location tidak ditemukan.', [
                        'name' => $locationName,
                        'organization' => $orgId,
                        'total' => $json['total'] ?? 0,
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $entry['id'] ?? null,
                'log' => $this->makeStepLog('location', true, 'Location ditemukan.', [
                    'location_id' => $entry['id'] ?? null,
                    'location_name' => $entry['name'] ?? null,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('location', false, 'Search Location exception.', [
                    'error' => $e->getMessage(),
                ]),
            ];
        }
    }

    private function ssResolvePatient(string $token, array $pasien): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $nikLokal  = $pasien['nik'] ?? null;
            $namaLokal = $pasien['nama'] ?? null;

            if (empty($nikLokal)) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('patient', false, 'NIK pasien lokal kosong. Patient SATUSEHAT tidak bisa dicari.', [
                        'source' => 'database',
                        'nama' => $namaLokal,
                        'nik' => $nikLokal,
                    ]),
                ];
            }

            $res = Http::withToken($token)
                ->acceptJson()
                ->get($base . '/Patient', [
                    'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nikLokal,
                ]);

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('patient', false, 'Search Patient by NIK database gagal.', [
                        'source' => 'database',
                        'nik_dipakai' => $nikLokal,
                        'nama_lokal' => $namaLokal,
                        'status' => $res->status(),
                        'body' => $res->json(),
                    ]),
                ];
            }

            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;

            if (!$entry || empty($entry['id'])) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('patient', false, 'Patient tidak ditemukan di SATUSEHAT berdasarkan NIK database.', [
                        'source' => 'database',
                        'nik_dipakai' => $nikLokal,
                        'nama_lokal' => $namaLokal,
                        'total' => $json['total'] ?? 0,
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $entry['id'],
                'log' => $this->makeStepLog('patient', true, 'Patient ditemukan by NIK database.', [
                    'source' => 'database',
                    'nik_dipakai' => $nikLokal,
                    'patient_id' => $entry['id'],
                    'patient_name' => $entry['name'][0]['text'] ?? null,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('patient', false, 'Search Patient by NIK database exception.', [
                    'source' => 'database',
                    'nik_dipakai' => $pasien['nik'] ?? null,
                    'error' => $e->getMessage(),
                ]),
            ];
        }
    }

    /**
     * FIX vs versi sebelumnya: kalau IHS dari master_dokter ternyata sudah tidak
     * valid (404 / bukan resource ID SATUSEHAT), sekarang JATUH KE pencarian NIK
     * alih-alih langsung dianggap gagal total. Ini menghindari kegagalan seluruh
     * pengiriman hanya karena kolom ihs_nakes di master_dokter basi/salah isi.
     */
    private function ssResolvePractitioner(string $token, array $nakes): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $ihsLokal  = $nakes['ihs'] ?? null;
            $nikLokal  = $nakes['nik'] ?? null;
            $namaLokal = $nakes['nama'] ?? null;
            $rawInput  = $nakes['tenaga_medis_raw'] ?? null;

            // PRIORITAS 1: IHS dari master_dokter (kalau ada dan valid).
            if (!empty($ihsLokal)) {
                $res = Http::withToken($token)->acceptJson()->get($base . '/Practitioner/' . $ihsLokal);

                if ($res->successful()) {
                    $json = $res->json();

                    if (!empty($json['id'])) {
                        return [
                            'ok' => true,
                            'id' => $json['id'],
                            'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by IHS dari master_dokter.', [
                                'source' => 'master_dokter.ihs_nakes',
                                'ihs_dipakai' => $ihsLokal,
                                'practitioner_id' => $json['id'],
                                'practitioner_name' => $json['name'][0]['text'] ?? null,
                            ]),
                        ];
                    }
                }

                // IHS gagal/invalid -> jangan langsung menyerah, coba NIK di bawah.
            }

            // PRIORITAS 2: cari by NIK/kdDokter dari master_dokter.
            if (empty($nikLokal)) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('practitioner', false, 'IHS tidak valid dan NIK/kode dokter lokal kosong. Practitioner SATUSEHAT tidak bisa dicari.', [
                        'source' => 'mtbs_statuspasien.master_dokter',
                        'tenaga_medis_raw' => $rawInput,
                        'nama_lokal' => $namaLokal,
                        'ihs_lokal' => $ihsLokal,
                    ]),
                ];
            }

            $res = Http::withToken($token)
                ->acceptJson()
                ->get($base . '/Practitioner', [
                    'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nikLokal,
                ]);

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('practitioner', false, 'Search Practitioner by NIK/kode dokter gagal.', [
                        'source' => 'master_dokter.kdDokter',
                        'ihs_dicoba_dulu' => $ihsLokal,
                        'nik_dipakai' => $nikLokal,
                        'nama_lokal' => $namaLokal,
                        'status' => $res->status(),
                        'body' => $res->json(),
                    ]),
                ];
            }

            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;

            if (!$entry || empty($entry['id'])) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('practitioner', false, 'Practitioner tidak ditemukan berdasarkan IHS maupun NIK/kode dokter.', [
                        'source' => 'master_dokter.kdDokter',
                        'ihs_dicoba_dulu' => $ihsLokal,
                        'nik_dipakai' => $nikLokal,
                        'nama_lokal' => $namaLokal,
                        'total' => $json['total'] ?? 0,
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $entry['id'],
                'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by NIK/kode dokter (fallback dari IHS).', [
                    'source' => 'master_dokter.kdDokter',
                    'nik_dipakai' => $nikLokal,
                    'nama_lokal' => $namaLokal,
                    'practitioner_id' => $entry['id'],
                    'practitioner_name' => $entry['name'][0]['text'] ?? null,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('practitioner', false, 'Search Practitioner exception.', [
                    'error' => $e->getMessage(),
                ]),
            ];
        }
    }

    // =========================================================================
    // PHASE 3: ENCOUNTER (arrived -> in-progress -> finished)
    // =========================================================================

    private function baseEncounterPayload(array $bundle, string $patientId, string $practitionerId, string $locationId, string $orgId, string $status, string $start, ?string $end = null): array
    {
        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
        $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';

        return [
            'resourceType' => 'Encounter',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => $status,
            'class' => [
                'system' => self::SYS_ACT_CODE,
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => self::SYS_PARTICIPATION,
                                    'code' => 'ATND',
                                    'display' => 'attender',
                                ],
                            ],
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . $practitionerId,
                        'display' => $nakesNama,
                    ],
                ],
            ],
            'period' => array_filter([
                'start' => $start,
                'end' => $end,
            ]),
            'location' => [
                [
                    'location' => [
                        'reference' => 'Location/' . $locationId,
                        'display' => $poliNama,
                    ],
                ],
            ],
            'serviceProvider' => [
                'reference' => 'Organization/' . $orgId,
            ],
        ];
    }

    /**
     * Step 1/2: registrasi kunjungan baru dengan status "arrived".
     * (Sebelumnya controller ini langsung membuat Encounter dengan status
     * "in-progress" dalam 1 POST - tidak sesuai urutan resmi Postman collection
     * yang memisahkan "Pendaftaran Kunjungan Baru" (arrived) dan "Masuk Ruang
     * Pemeriksaan" (in-progress) sebagai 2 transaksi terpisah.)
     */
    private function ssRegisterEncounterArrived(
        string $token,
        array $bundle,
        string $patientId,
        string $practitionerId,
        string $locationId,
        string $orgId
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'arrived', $start);
            $payload['statusHistory'] = [
                ['status' => 'arrived', 'period' => ['start' => $start]],
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/Encounter', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('encounter_arrived', false, 'Registrasi Encounter (arrived) gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('encounter_arrived', true, 'Registrasi Encounter (arrived) berhasil.', [
                    'encounter_id' => $json['id'] ?? null,
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('encounter_arrived', false, 'Registrasi Encounter (arrived) exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    /**
     * Step 2/2: PUT Encounter -> status "in-progress" ("Masuk Ruang Pemeriksaan").
     * CATATAN: karena skema lokal belum mencatat waktu "masuk ruang periksa"
     * secara terpisah dari waktu registrasi (tglKunjungan), start/end di sini
     * memakai timestamp yang sama dengan step arrived. Kalau nanti ada kolom
     * waktu tersendiri untuk ini, tinggal disambungkan di $start bawah.
     */
    private function ssStartEncounterInProgress(
        string $token,
        array $bundle,
        string $encounterId,
        string $patientId,
        string $practitionerId,
        string $locationId,
        string $orgId
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');
            $arrivedEnd = $start;

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'in-progress', $start);
            $payload['id'] = $encounterId;
            $payload['statusHistory'] = [
                ['status' => 'arrived', 'period' => ['start' => $start, 'end' => $arrivedEnd]],
                ['status' => 'in-progress', 'period' => ['start' => $start]],
            ];

            $res = Http::withToken($token)->acceptJson()->put($base . '/Encounter/' . $encounterId, $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('encounter_in_progress', false, 'Update Encounter (in-progress) gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? $encounterId,
                'payload' => $payload,
                'log' => $this->makeStepLog('encounter_in_progress', true, 'Update Encounter (in-progress) berhasil.', [
                    'encounter_id' => $json['id'] ?? $encounterId,
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('encounter_in_progress', false, 'Update Encounter (in-progress) exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    private function ssFinishEncounter(
        string $token,
        array $bundle,
        string $encounterId,
        string $patientId,
        string $practitionerId,
        string $locationId,
        string $orgId,
        array $conditionRefs
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');
            $end = Carbon::parse($tanggalKunjungan)->copy()->addMinutes(15)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $diagnosis = [];
            foreach ($conditionRefs as $idx => $cond) {
                $diagnosis[] = [
                    'condition' => [
                        'reference' => 'Condition/' . $cond['id'],
                        'display' => $cond['display'],
                    ],
                    'use' => [
                        'coding' => [
                            [
                                'system' => self::SYS_DIAGNOSIS_ROLE,
                                'code' => $idx === 0 ? 'AD' : 'DD',
                                'display' => $idx === 0 ? 'Admission diagnosis' : 'Discharge diagnosis',
                            ],
                        ],
                    ],
                    'rank' => $cond['rank'] ?? ($idx + 1),
                ];
            }

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'finished', $start, $end);
            $payload['id'] = $encounterId;
            $payload['statusHistory'] = [
                ['status' => 'arrived', 'period' => ['start' => $start, 'end' => $start]],
                ['status' => 'in-progress', 'period' => ['start' => $start, 'end' => $end]],
                ['status' => 'finished', 'period' => ['start' => $end, 'end' => $end]],
            ];
            $payload['diagnosis'] = $diagnosis;

            $res = Http::withToken($token)->acceptJson()->put($base . '/Encounter/' . $encounterId, $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? $encounterId,
                'payload' => $payload,
                'log' => $this->makeStepLog('finish_encounter', true, 'Finish Encounter berhasil.', [
                    'encounter_id' => $json['id'] ?? $encounterId,
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // CONDITION
    // =========================================================================

    private function mapMtbsConditionCandidates(array $bundle): array
    {
        $diagnosaMedis = $bundle['data']['diagnosa_medis'] ?? collect();

        if ($diagnosaMedis instanceof \Illuminate\Support\Collection) {
            $diagnosaMedis = $diagnosaMedis->all();
        }

        $items = [];

        foreach ($diagnosaMedis as $row) {
            $code = trim((string) ($row->kdDiag ?? ''));
            $display = trim((string) ($row->nmDiag ?? ''));

            if ($code === '' || $display === '') {
                continue;
            }

            $items[$code . '|' . $display] = [
                'code' => $code,
                'display' => $display,
            ];
        }

        return array_values($items);
    }

    /**
     * Condition - Keluhan Utama (category: problem-list-item).
     * Sumber: mtbs_subjektif.keluhan_utama (array JSON) + keluhan_lain (teks bebas).
     * Kode SNOMED diambil dari keluhanUtamaSnomedMap(); kalau tidak ada yang
     * cocok, Condition tetap dikirim dengan `text` saja.
     */
    private function mapMtbsKeluhanUtamaCandidates(array $bundle): array
    {
        $sub = $bundle['data']['subjektif'] ?? null;
        if (!$sub) {
            return [];
        }

        $keluhanList = json_decode($sub->keluhan_utama ?? '[]', true) ?: [];

        if (!empty($sub->keluhan_lain)) {
            $keluhanList[] = (string) $sub->keluhan_lain;
        }

        $map = $this->keluhanUtamaSnomedMap();
        $items = [];

        foreach (array_unique(array_filter($keluhanList)) as $keluhanText) {
            $keluhanText = trim((string) $keluhanText);
            if ($keluhanText === '') {
                continue;
            }

            $lower = Str::lower($keluhanText);
            $code = null;

            foreach ($map as $keyword => $coding) {
                if (Str::contains($lower, $keyword)) {
                    $code = $coding;
                    break;
                }
            }

            $items[] = [
                'text' => $keluhanText,
                'code' => $code, // null = kirim tanpa coding (text-only), tetap valid FHIR
            ];
        }

        return $items;
    }

    /**
     * Condition - Kondisi Saat Meninggalkan Faskes (category: problem-list-item).
     * Diturunkan dari mtbs_assessment.status_kegawatan. Lihat catatan class-level
     * poin 6 soal ambiguitas kode SNOMED stabil/tidak-stabil antara Playbook dan
     * contoh Postman collection.
     */
    private function mapMtbsDischargeConditionCandidate(array $bundle): ?array
    {
        $statusPasien = $bundle['data']['status_pasien'] ?? null;
        if (!$statusPasien) {
            // Belum ada input Status Pasien (discharge) sama sekali -> jangan
            // menebak kondisi pulang.
            return null;
        }

        $assessment = $bundle['data']['assessment'] ?? null;
        $statusKegawatan = $assessment->status_kegawatan ?? null;

        if ($statusKegawatan === 'Gagal jantung paru' || $statusKegawatan === 'Penyakit sangat berat') {
            return ['code' => $this->snomedCode('162668006', "Patient's condition unstable")];
        }

        // Default (termasuk kalau status_kegawatan belum sempat dihitung, tapi
        // proses discharge sudah dijalankan): anggap stabil, memakai kode yang
        // sama seperti nilai "Stabil" pada klasifikasi status kegawatan.
        return ['code' => $this->snomedCode('359746009', "Patient's condition stable")];
    }

    /**
     * Generic Condition sender. $spec = ['category_code','category_display','code' (nullable coding array),'text','note']
     */
    private function ssCreateCondition(
        string $token,
        array $bundle,
        string $patientId,
        string $encounterId,
        string $practitionerId,
        array $spec
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $recordedDate = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $codeBlock = ['text' => $spec['text']];
            if (!empty($spec['code'])) {
                $codeBlock['coding'] = [[
                    'system' => $spec['code']['system'],
                    'code' => $spec['code']['code'],
                    'display' => $spec['code']['display'],
                ]];
            }

            $payload = [
                'resourceType' => 'Condition',
                'clinicalStatus' => [
                    'coding' => [[
                        'system' => self::SYS_COND_CLINICAL,
                        'code' => 'active',
                        'display' => 'Active',
                    ]],
                ],
                'category' => [[
                    'coding' => [[
                        'system' => self::SYS_COND_CATEGORY,
                        'code' => $spec['category_code'],
                        'display' => $spec['category_display'],
                    ]],
                ]],
                'code' => $codeBlock,
                'subject' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $encounterId,
                ],
                'recordedDate' => $recordedDate,
                'recorder' => [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
                'asserter' => [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
            ];

            if (!empty($spec['note'])) {
                $payload['note'] = [['text' => $spec['note']]];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/Condition', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('condition', false, 'Create Condition gagal.', [
                        'category' => $spec['category_code'],
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('condition', true, 'Create Condition berhasil.', [
                    'category' => $spec['category_code'],
                    'condition_id' => $json['id'] ?? null,
                    'condition_text' => $spec['text'],
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('condition', false, 'Create Condition exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // OBSERVATION - Tanda Vital & Antropometri (valueQuantity)
    // =========================================================================

    private function mapMtbsVitalAntropometriObservations(array $bundle): array
    {
        $objektif = $bundle['data']['objektif'] ?? null;
        if (!$objektif) {
            return [];
        }

        $items = [];

        $pushQuantity = function (
            string $key,
            $value,
            array $code,
            string $unit,
            string $ucumCode,
            string $categoryCode,
            string $categoryDisplay
        ) use (&$items) {
            if ($value === null || $value === '') {
                return;
            }

            $items[$key] = [
                'value_type' => 'quantity',
                'value' => (float) $value,
                'code' => $code,
                'unit' => ['system' => self::SYS_UCUM, 'code' => $ucumCode, 'unit' => $unit],
                'category' => ['code' => $categoryCode, 'display' => $categoryDisplay],
            ];
        };

        // Suhu tubuh - satu-satunya tanda vital yang WAJIB per Playbook 5.1.
        $pushQuantity('suhu', $objektif->suhu ?? null, $this->loincCode('8310-5', 'Body temperature'), 'Cel', 'Cel', 'vital-signs', 'Vital Signs');

        // Tambahan tanda vital (opsional, tidak diwajibkan Playbook, tapi tidak
        // merugikan untuk dikirim kalau datanya ada).
        $pushQuantity('rr', $objektif->rr ?? null, $this->loincCode('9279-1', 'Respiratory rate'), 'breaths/minute', '/min', 'vital-signs', 'Vital Signs');
        $pushQuantity('spo2', $objektif->spo2 ?? null, $this->loincCode('59408-5', 'Oxygen saturation in Arterial blood by Pulse oximetry'), '%', '%', 'vital-signs', 'Vital Signs');

        // Antropometri (4 wajib per Playbook 5.1: BB, PB/TB, LiLA, lingkar kepala).
        $pushQuantity('bb', $objektif->bb ?? null, $this->loincCode('29463-7', 'Body weight'), 'kg', 'kg', 'vital-signs', 'Vital Signs');
        $pushQuantity('tb', $objektif->tb ?? null, $this->loincCode('8302-2', 'Body height'), 'cm', 'cm', 'vital-signs', 'Vital Signs');
        $pushQuantity('lila', $objektif->lila ?? null, $this->snomedCode('284473002', 'Mid upper arm circumference'), 'cm', 'cm', 'exam', 'Exam');
        $pushQuantity('lk', $objektif->lk ?? null, $this->loincCode('8287-5', 'Head Occipital-frontal circumference by Tape measure'), 'cm', 'cm', 'exam', 'Exam');

        return $items;
    }

    // =========================================================================
    // OBSERVATION - 10 Klasifikasi MTBS (valueCodeableConcept)
    // =========================================================================

    private function mapMtbsKlasifikasiObservationCandidates(array $bundle): array
    {
        $assessment = $bundle['data']['assessment'] ?? null;
        if (!$assessment) {
            return [];
        }

        $items = [];
        $spec = $this->mtbsClassificationSpec();

        foreach ($spec as $key => $def) {
            $localValue = $assessment->{$def['source']} ?? null;

            if ($localValue === null || $localValue === '') {
                continue; // dormant (kolom belum ada) atau memang belum dinilai
            }

            if (!isset($def['values'][$localValue])) {
                continue; // nilai lokal tidak (atau belum) punya kode SATUSEHAT
            }

            $items[$key] = [
                'value_type' => 'codeable_concept',
                'value_coding' => $def['values'][$localValue],
                'code' => $def['obs_code'],
                'category' => ['code' => $def['obs_category'], 'display' => ucfirst($def['obs_category'])],
            ];
        }

        // --- Demam & Malaria: penanganan khusus (lihat mtbsClassificationSpec()) ---
        $demamValue = $assessment->demam ?? null;
        $demamMap = [
            'penyakit_berat_dengan_demam' => $this->kemkesCode('OV000223', 'Penyakit berat dengan demam'),
            'malaria' => $this->snomedCode('61462000', 'Malaria'),
            'demam_mungkin_bukan_malaria' => $this->kemkesCode('OV000224', 'Demam mungkin bukan malaria'),
            'demam_bukan_malaria' => $this->kemkesCode('OV000226', 'Demam bukan malaria'),
        ];

        if ($demamValue !== null && isset($demamMap[$demamValue])) {
            $items['demam'] = [
                'value_type' => 'codeable_concept',
                'value_coding' => $demamMap[$demamValue],
                'code' => $this->kemkesCode('OC000100', 'Klasifikasi demam'),
                'category' => ['code' => 'exam', 'display' => 'Exam'],
            ];
        }

        return $items;
    }

    /**
     * Generic Observation sender - menangani valueQuantity DAN valueCodeableConcept.
     * (Versi sebelumnya hanya bisa mengirim valueQuantity, sehingga tidak mungkin
     * dipakai untuk 10 klasifikasi MTBS yang butuh valueCodeableConcept.)
     */
    private function ssCreateObservation(
        string $token,
        array $bundle,
        string $patientId,
        string $encounterId,
        string $practitionerId,
        string $orgId,
        array $obs
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $effectiveDateTime = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'Observation',
                'identifier' => [[
                    'system' => 'http://sys-ids.kemkes.go.id/observation/' . $orgId,
                    'value' => (string) Str::uuid(),
                ]],
                'status' => 'final',
                'category' => [[
                    'coding' => [[
                        'system' => self::SYS_OBS_CATEGORY,
                        'code' => $obs['category']['code'],
                        'display' => $obs['category']['display'],
                    ]],
                ]],
                'code' => [
                    'coding' => [[
                        'system' => $obs['code']['system'],
                        'code' => $obs['code']['code'],
                        'display' => $obs['code']['display'],
                    ]],
                    'text' => $obs['code']['display'],
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $encounterId,
                ],
                'effectiveDateTime' => $effectiveDateTime,
                'performer' => [[
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ]],
            ];

            if ($obs['value_type'] === 'codeable_concept') {
                $payload['valueCodeableConcept'] = [
                    'coding' => [[
                        'system' => $obs['value_coding']['system'],
                        'code' => $obs['value_coding']['code'],
                        'display' => $obs['value_coding']['display'],
                    ]],
                ];
            } else {
                $payload['valueQuantity'] = [
                    'value' => $obs['value'],
                    'unit' => $obs['unit']['unit'],
                    'system' => $obs['unit']['system'],
                    'code' => $obs['unit']['code'],
                ];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/Observation', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('observation', false, 'Create Observation gagal.', [
                        'observation_code' => $obs['code']['code'],
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('observation', true, 'Create Observation berhasil.', [
                    'observation_id' => $json['id'] ?? null,
                    'observation_code' => $obs['code']['code'],
                    'observation_display' => $obs['code']['display'],
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('observation', false, 'Create Observation exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // PROCEDURE
    // =========================================================================

    /**
     * FIX: sebelumnya category selalu di-hardcode "Diagnostic procedure" untuk
     * semua tindakan. Contoh resmi (Procedure - Terapetik - Nebulisasi) memakai
     * category SNOMED 277132007 "Therapeutic procedure" - beda dari X-Ray yang
     * memang diagnostic. Sekarang category ikut jenis tindakannya.
     */
    private function mapMtbsProcedureCandidates(array $bundle): array
    {
        $planning = $bundle['data']['planning'] ?? null;
        if (!$planning) {
            return [];
        }

        $tindakan = json_decode($planning->tindakan_segera ?? '[]', true) ?: [];
        if (!is_array($tindakan) || count($tindakan) === 0) {
            return [];
        }

        $diagnosticCategory = $this->snomedCode('103693007', 'Diagnostic procedure');
        $therapeuticCategory = $this->snomedCode('277132007', 'Therapeutic procedure');

        $items = [];

        foreach ($tindakan as $raw) {
            $text = trim((string) $raw);
            $lower = Str::lower($text);

            if ($text === '') {
                continue;
            }

            if (Str::contains($lower, ['rontgen', 'x-ray', 'xray', 'radiologi'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $diagnosticCategory,
                    'code' => '87.44',
                    'display' => 'Routine chest X-ray, so described',
                    'performed_text' => 'Diagnostic X-Ray',
                ];
                continue;
            }

            if (Str::contains($lower, ['oksigen', 'oxygen'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $therapeuticCategory,
                    'code' => '93.96',
                    'display' => 'Administration of oxygen',
                    'performed_text' => 'Pemberian oksigen',
                ];
                continue;
            }

            if (Str::contains($lower, ['infus', 'iv', 'intravena'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $therapeuticCategory,
                    'code' => '99.18',
                    'display' => 'Injection or infusion of electrolytes',
                    'performed_text' => 'Pemasangan / pemberian infus',
                ];
                continue;
            }

            if (Str::contains($lower, ['nebul', 'nebulizer', 'nebulisasi'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $therapeuticCategory,
                    'code' => '93.94',
                    'display' => 'Respiratory medication administered by nebulizer',
                    'performed_text' => 'Nebulisasi',
                ];
                continue;
            }

            if (Str::contains($lower, ['rujuk', 'refer'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $therapeuticCategory,
                    'code' => '89.59',
                    'display' => 'Other referral for care',
                    'performed_text' => 'Rujukan pasien',
                ];
                continue;
            }

            if (Str::contains($lower, ['edukasi', 'konseling', 'konsultasi'])) {
                $items[] = [
                    'source_text' => $text,
                    'category' => $therapeuticCategory,
                    'code' => '94.09',
                    'display' => 'Other individual psychotherapy',
                    'performed_text' => 'Edukasi / konseling',
                ];
                continue;
            }

            $items[] = [
                'source_text' => $text,
                'category' => $therapeuticCategory,
                'code' => '99.99',
                'display' => 'Other miscellaneous procedures',
                'performed_text' => $text,
            ];
        }

        $unique = [];
        foreach ($items as $item) {
            $key = $item['code'] . '|' . $item['performed_text'];
            $unique[$key] = $item;
        }

        return array_values($unique);
    }

    private function ssCreateProcedure(
        string $token,
        array $bundle,
        string $patientId,
        string $encounterId,
        string $practitionerId,
        string $orgId,
        array $procedure
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $performedDateTime = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'Procedure',
                'identifier' => [[
                    'system' => 'http://sys-ids.kemkes.go.id/procedure/' . $orgId,
                    'value' => (string) Str::uuid(),
                ]],
                'status' => 'completed',
                'category' => [
                    'coding' => [[
                        'system' => $procedure['category']['system'],
                        'code' => $procedure['category']['code'],
                        'display' => $procedure['category']['display'],
                    ]],
                ],
                'code' => [
                    'coding' => [[
                        'system' => self::SYS_ICD9CM,
                        'code' => $procedure['code'],
                        'display' => $procedure['display'],
                    ]],
                    'text' => $procedure['performed_text'],
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $encounterId,
                ],
                'performedDateTime' => $performedDateTime,
                'performer' => [[
                    'actor' => [
                        'reference' => 'Practitioner/' . $practitionerId,
                        'display' => $nakesNama,
                    ],
                ]],
                'note' => [[
                    'text' => 'Mapping dari database mtbs_planning.tindakan_segera: ' . ($procedure['source_text'] ?? $procedure['performed_text']),
                ]],
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/Procedure', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('procedure', false, 'Create Procedure gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('procedure', true, 'Create Procedure berhasil.', [
                    'procedure_id' => $json['id'] ?? null,
                    'procedure_code' => $procedure['code'],
                    'procedure_display' => $procedure['display'],
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('procedure', false, 'Create Procedure exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // SERVICE REQUEST
    // =========================================================================

    /**
     * FIX: menambahkan priority, category, dan reasonCode yang sebelumnya tidak
     * ada (padahal muncul di contoh resmi ServiceRequest - Kontrol Kembali).
     * reasonCode diambil dari diagnosis pertama (kalau ada) supaya ServiceRequest
     * terhubung ke alasan klinis kontrol ulang.
     */
    private function mapMtbsServiceRequestCandidate(array $bundle, array $conditionRefs = []): ?array
    {
        $planning = $bundle['data']['planning'] ?? null;
        $kunjungan = $bundle['data']['kunjungan'] ?? [];

        if (!$planning || empty($planning->kunjungan_ulang_hari)) {
            return null;
        }

        $hariKontrol = (int) $planning->kunjungan_ulang_hari;
        $catatan = $planning->catatan_edukasi ?: 'Kontrol ulang pasien MTBS untuk evaluasi lanjutan';

        $tanggalKunjungan = $kunjungan['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $occurrence = Carbon::parse($tanggalKunjungan)->copy()->addDays($hariKontrol);

        $reasonCode = null;
        if (!empty($conditionRefs)) {
            // Diagnosis pertama (rank 1) dipakai sebagai alasan klinis kontrol ulang.
            // Catatan: kita hanya punya display text di sini, bukan kode ICD-10 asli,
            // karena $conditionRefs hanya membawa id+display+rank. Kalau butuh kode
            // ICD-10 di reasonCode, kirim juga 'code' dari mapMtbsConditionCandidates()
            // ke sini.
            $reasonCode = $conditionRefs[0]['display'] ?? null;
        }

        return [
            'code_system' => self::SYS_SNOMED,
            'code' => '185389009',
            'display' => 'Follow-up visit',
            'text' => 'Kontrol ulang MTBS',
            'note' => $catatan,
            'hari_kontrol' => $hariKontrol,
            'occurrence' => $occurrence->timezone('UTC')->format('Y-m-d\TH:i:sP'),
            'reason_text' => $reasonCode,
        ];
    }

    private function ssCreateServiceRequest(
        string $token,
        array $bundle,
        string $patientId,
        string $encounterId,
        string $practitionerId,
        string $locationId,
        string $orgId,
        array $serviceRequest
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
            $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $authoredOn = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'ServiceRequest',
                'identifier' => [[
                    'system' => 'http://sys-ids.kemkes.go.id/servicerequest/' . $orgId,
                    'value' => (string) Str::uuid(),
                ]],
                'status' => 'active',
                'intent' => 'original-order',
                'priority' => 'routine',
                'category' => [[
                    'coding' => [[
                        'system' => self::SYS_SNOMED,
                        'code' => '3457005',
                        'display' => 'Patient referral',
                    ]],
                ]],
                'subject' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $encounterId,
                ],
                'authoredOn' => $authoredOn,
                'requester' => [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
                'performer' => [[
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ]],
                'locationReference' => [[
                    'reference' => 'Location/' . $locationId,
                    'display' => $poliNama,
                ]],
                'code' => [
                    'coding' => [[
                        'system' => $serviceRequest['code_system'],
                        'code' => $serviceRequest['code'],
                        'display' => $serviceRequest['display'],
                    ]],
                    'text' => $serviceRequest['text'] ?? 'Kontrol ulang MTBS',
                ],
                'occurrenceDateTime' => $serviceRequest['occurrence'],
                'note' => [[
                    'text' => $serviceRequest['note'] ?? 'Tindak lanjut / kontrol ulang pasien MTBS',
                ]],
            ];

            if (!empty($serviceRequest['reason_text'])) {
                $payload['reasonCode'] = [[
                    'text' => $serviceRequest['reason_text'],
                ]];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/ServiceRequest', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('service_request', true, 'Create ServiceRequest berhasil.', [
                    'service_request_id' => $json['id'] ?? null,
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // QUESTIONNAIRE RESPONSE - MTBS (Q0027)
    // =========================================================================

    /**
     * REWRITE TOTAL vs versi sebelumnya.
     *
     * Versi lama mengirim `item` FLAT dengan linkId bebas ("1.1", "2.5", dst)
     * berisi value langsung (valueString/valueBoolean/dll) - tidak sesuai
     * struktur resmi Q0027 di Playbook.
     *
     * Struktur resmi: nested per kategori, linkId berjenjang TETAP (bukan bebas),
     * dan level paling dalam bukan value langsung tapi `valueReference` yang
     * menunjuk ke Observation klasifikasi yang sudah dibuat di step sebelumnya.
     * Karena itu method ini WAJIB dipanggil SETELAH seluruh Observation
     * klasifikasi berhasil dibuat ($observationIdsByKey berisi id-id tersebut).
     *
     * Kategori yang belum punya Observation (karena kolom di mtbs_assessment
     * belum ada / nilai tidak bisa dipetakan) otomatis di-skip di sini juga -
     * QuestionnaireResponse tidak pernah mereferensikan Observation yang tidak
     * benar-benar dibuat.
     */
    private function buildMtbsQuestionnaireResponseItems(array $bundle, array $observationIdsByKey): array
    {
        $assessment = $bundle['data']['assessment'] ?? null;
        $spec = $this->mtbsClassificationSpec();
        $items = [];

        foreach ($spec as $key => $def) {
            if (!isset($observationIdsByKey[$key])) {
                continue;
            }

            $observationId = $observationIdsByKey[$key];
            $localValue = $assessment->{$def['source']} ?? null;

            [$gateLink, $gateText] = $def['qr_gate'];
            [$resultLink, $resultText] = $def['qr_result'];
            [$topLink, $topText] = $def['qr_top'];

            $resultItem = [
                'linkId' => $resultLink,
                'text' => $resultText,
                'answer' => [[
                    'valueReference' => ['reference' => 'Observation/' . $observationId],
                ]],
            ];

            if ($def['qr_middle'] === null) {
                // Struktur 3 level: top -> gate(true) -> hasil
                $gateItem = [
                    'linkId' => $gateLink,
                    'text' => $gateText,
                    'answer' => [[
                        'valueBoolean' => true,
                        'item' => [$resultItem],
                    ]],
                ];
            } else {
                // Struktur 4 level: top -> gate(true) -> "anak menderita X?" -> hasil
                [$middleLink, $middleText] = $def['qr_middle'];
                $isNegativeFinding = in_array($localValue, $def['negative_finding'] ?? [], true);

                $middleItem = [
                    'linkId' => $middleLink,
                    'text' => $middleText,
                    'answer' => [[
                        'valueBoolean' => !$isNegativeFinding,
                        'item' => [$resultItem],
                    ]],
                ];

                $gateItem = [
                    'linkId' => $gateLink,
                    'text' => $gateText,
                    'answer' => [[
                        'valueBoolean' => true,
                        'item' => [$middleItem],
                    ]],
                ];
            }

            $items[] = [
                'linkId' => $topLink,
                'text' => $topText,
                'item' => [$gateItem],
            ];
        }

        // Demam & Malaria: struktur khusus (lihat buildDemamQuestionnaireBranch).
        if (isset($observationIdsByKey['demam'])) {
            $items[] = $this->buildDemamQuestionnaireBranch($observationIdsByKey['demam']);
        }

        // Urutkan berdasarkan nomor linkId teratas (1, 2, 3, ... 11) supaya rapi
        // dan konsisten dengan urutan di Playbook.
        usort($items, static function (array $a, array $b): int {
            return ((float) $a['linkId']) <=> ((float) $b['linkId']);
        });

        return $items;
    }

    private function buildDemamQuestionnaireBranch(string $observationId): array
    {
        $resultItem = [
            'linkId' => '4.1.1.2',
            'text' => 'Hasil pemeriksaan demam dan malaria',
            'answer' => [[
                'valueReference' => ['reference' => 'Observation/' . $observationId],
            ]],
        ];

        // Boolean "dilakukan pemeriksaan malaria?" - lihat catatan di
        // mapMtbsKlasifikasiObservationCandidates(): rule engine lokal tidak
        // mengembalikan sinyal terpisah untuk ini, jadi disederhanakan jadi
        // true setiap kali cabang demam ini terkirim.
        $malariaGateItem = [
            'linkId' => '4.1.1.1',
            'text' => 'Apakah dilakukan pemeriksaan malaria?',
            'answer' => [[
                'valueBoolean' => true,
            ]],
        ];

        $anakDemamItem = [
            'linkId' => '4.1.1',
            'text' => 'Apakah anak demam?',
            'answer' => [[
                'valueBoolean' => true,
                'item' => [$malariaGateItem, $resultItem],
            ]],
        ];

        $gateItem = [
            'linkId' => '4.1',
            'text' => 'Apakah dilakukan pemeriksaan demam?',
            'answer' => [[
                'valueBoolean' => true,
                'item' => [$anakDemamItem],
            ]],
        ];

        return [
            'linkId' => '4',
            'text' => 'Pemeriksaan Demam dan Malaria Usia 2 Bulan - 5 Tahun',
            'item' => [$gateItem],
        ];
    }

    private function ssCreateQuestionnaireResponse(
        string $token,
        array $bundle,
        string $patientId,
        string $encounterId,
        string $practitionerId,
        array $items
    ): array {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBS');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

            $authored = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'QuestionnaireResponse',
                'questionnaire' => env('SATUSEHAT_MTBS_QUESTIONNAIRE_URL', self::QUESTIONNAIRE_MTBS_URL),
                'status' => 'completed',
                'subject' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $encounterId,
                ],
                'authored' => $authored,
                'author' => [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
                'source' => [
                    'reference' => 'Patient/' . $patientId,
                    'display' => $pasienNama,
                ],
                'item' => $items,
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/QuestionnaireResponse', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return [
                    'ok' => false,
                    'id' => null,
                    'payload' => $payload,
                    'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            return [
                'ok' => true,
                'id' => $json['id'] ?? null,
                'payload' => $payload,
                'log' => $this->makeStepLog('questionnaire_response', true, 'Create QuestionnaireResponse berhasil.', [
                    'questionnaire_response_id' => $json['id'] ?? null,
                    'jumlah_kategori' => count($items),
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => null,
                'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // MISC
    // =========================================================================

    private function makeStepLog(string $step, bool $success, string $message, array $meta = []): array
    {
        return [
            'step' => $step,
            'success' => $success,
            'ok' => $success,
            'message' => $message,
            'meta' => $meta,
            'data' => $meta,
            'time' => now()->toDateTimeString(),
        ];
    }
}
