<?php

namespace App\Http\Controllers\RuangLayanan\MTBM;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * MTBMSatusehatController
 * -------------------------------------------------------------------------
 * Controller pengiriman data MTBM (bayi muda usia 0 - <2 bulan) ke SATUSEHAT.
 * Dibangun sejajar dengan MTBSSatusehatController (untuk usia 2 bulan - 5
 * tahun), tapi MTBM punya beberapa perbedaan struktural penting - lihat
 * catatan di tiap bagian.
 *
 * PRASYARAT MIGRATION (jalankan sebelum pakai file ini):
 *
 *   Schema::create('mtbm_episode_of_care', function (Blueprint $table) {
 *       $table->id();
 *       $table->unsignedBigInteger('pasien_id')->unique();
 *       $table->string('episode_of_care_id', 100);   // ID SATUSEHAT
 *       $table->string('patient_satusehat_id', 100)->nullable();
 *       $table->timestamps();
 *   });
 *
 * Alasan: Playbook SATUSEHAT (bagian C.3) mewajibkan EpisodeOfCare dikirim
 * SATU KALI per bayi (bukan per kunjungan) - ID balikannya dipakai ulang di
 * Encounter.episodeOfCare selama bayi masih dalam masa neonatus. Tidak ada
 * tabel lokal untuk menyimpan ini sebelumnya, jadi tabel baru ditambahkan.
 *
 * CATATAN PENTING LAIN:
 * 1. MTBM TIDAK punya klasifikasi "Status Kegawatan" terpisah seperti MTBS -
 *    tanda bahaya bayi muda sudah menyatu ke dalam klasifikasi "Infeksi".
 * 2. Section "5. BB Rendah + ASI" dan "6. BB Rendah + Minum" saling eksklusif
 *    (cuma salah satu yang terkirim per kunjungan), tergantung status HIV ibu
 *    dan apakah bayi masih diberi ASI - lihat resolveJalurMenyusuBb().
 * 3. Lingkar kepala (lk) TIDAK dikirim - mtbm_objective tidak punya kolom ini.
 * 4. MedicationRequest DIIMPLEMENTASIKAN (beda dari versi MTBS yang skip total)
 *    karena mtbm_planning.resep_items sudah berupa data obat terstruktur.
 *    "Pengkajian Resep" (QuestionnaireResponse) dan MedicationDispense TETAP
 *    di-skip - itu representasi alur kerja farmasi terpisah yang belum ada
 *    datanya di form Planning dokter.
 */
class MTBMSatusehatController extends Controller
{
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
    private const SYS_EPISODE_TYPE = 'http://terminology.kemkes.go.id/CodeSystem/episodeofcare-type';
    private const QUESTIONNAIRE_MTBM_URL = 'https://fhir.kemkes.go.id/Questionnaire/Q0011';

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

    private function keluhanUtamaSnomedMap(): array
    {
        return [
            'batuk' => $this->snomedCode('49727002', 'Cough'),
            'demam' => $this->snomedCode('386661006', 'Fever'),
            'panas' => $this->snomedCode('386661006', 'Fever'),
            'diare' => $this->snomedCode('62315008', 'Diarrhea'),
            'mencret' => $this->snomedCode('62315008', 'Diarrhea'),
            'muntah' => $this->snomedCode('422400008', 'Vomiting'),
            'kejang' => $this->snomedCode('91175000', 'Convulsion'),
            'kuning' => $this->snomedCode('18165001', 'Jaundice'),
            'tidak mau menyusu' => $this->snomedCode('49233005', 'Anorexia'),
            'tidak mau mengisap' => $this->snomedCode('49233005', 'Anorexia'),
            'sesak napas' => $this->snomedCode('267036007', 'Dyspnea'),
            'sesak nafas' => $this->snomedCode('267036007', 'Dyspnea'),
        ];
    }

    /**
     * Tabel spesifikasi klasifikasi MTBM (0 - <2 bulan).
     * Sumber: Playbook SATUSEHAT MTBS Prioritas v1.0, bagian 5.2.1 - 5.2.4
     * (5.2.5 dan 5.2.6 ditangani terpisah - lihat resolveJalurMenyusuBb() dan
     * buildBbMinumQuestionnaireBranch() karena strukturnya saling eksklusif).
     */
    private function mtbmClassificationSpec(): array
    {
        return [
            'infeksi' => [
                'source' => 'infeksi',
                'obs_code' => $this->kemkesCode('OC000126', 'Klasifikasi infeksi'),
                'obs_category' => 'exam',
                'qr_top' => ['1', 'Pemeriksaan Infeksi 0-<2 Bulan'],
                'qr_gate' => ['1.1', 'Apakah dilakukan pemeriksaan infeksi?'],
                'qr_middle' => null,
                'qr_result' => ['1.1.1', 'Hasil pemeriksaan infeksi'],
                'negative_finding' => ['mungkin_bukan_infeksi'],
                'values' => [
                    'penyakit_sangat_berat_infeksi_berat' => $this->kemkesCode('OV000134', 'Infeksi bakteri berat'),
                    'infeksi_bakteri_lokal' => $this->kemkesCode('OV000135', 'Infeksi bakteri lokal'),
                    'mungkin_bukan_infeksi' => $this->kemkesCode('OV000136', 'Mungkin bukan infeksi'),
                ],
            ],
            'ikterus' => [
                'source' => 'ikterus',
                'obs_code' => $this->kemkesCode('OC000129', 'Klasifikasi ikterus'),
                'obs_category' => 'exam',
                'qr_top' => ['2', 'Pemeriksaan Ikterus 0-<2 Bulan'],
                'qr_gate' => ['2.1', 'Apakah dilakukan pemeriksaan ikterus?'],
                'qr_middle' => null,
                'qr_result' => ['2.1.1', 'Hasil pemeriksaan ikterus'],
                'negative_finding' => ['tidak_ikterus'],
                'values' => [
                    'ikterus_berat' => $this->kemkesCode('OV000179', 'Ikterus berat'),
                    'ikterus' => $this->snomedCode('18165001', 'Jaundice'),
                    'tidak_ikterus' => $this->kemkesCode('OV000180', 'Tidak ada ikterus'),
                ],
            ],
            'diare' => [
                'source' => 'diare',
                'obs_code' => $this->kemkesCode('OC000098', 'Klasifikasi diare'),
                'obs_category' => 'exam',
                'qr_top' => ['3', 'Pemeriksaan Diare 0-<2 Bulan'],
                'qr_gate' => ['3.1', 'Apakah dilakukan pemeriksaan diare?'],
                'qr_middle' => ['3.1.1', 'Apakah bayi diare?'],
                'qr_result' => ['3.1.1.1', 'Hasil pemeriksaan diare'],
                'negative_finding' => [],
                'values' => [
                    'diare_dehidrasi_berat' => $this->kemkesCode('OV000184', 'Diare dehidrasi berat'),
                    'diare_dehidrasi_ringan_sedang' => $this->kemkesCode('OV000185', 'Diare dehidrasi ringan/sedang'),
                    'diare_tanpa_dehidrasi' => $this->kemkesCode('OV000186', 'Diare tanpa dehidrasi'),
                ],
            ],
            'hiv' => [
                'source' => 'hiv',
                'obs_code' => $this->snomedCode('254387007', 'Human immunodeficiency virus infection classification systems'),
                'obs_category' => 'laboratory',
                'qr_top' => ['4', 'Pemeriksaan HIV 0-<2 Bulan'],
                'qr_gate' => ['4.1', 'Apakah dilakukan pemeriksaan HIV?'],
                'qr_middle' => null,
                'qr_result' => ['4.1.1', 'Hasil pemeriksaan HIV'],
                'negative_finding' => ['bukan_infeksi_hiv'],
                'values' => [
                    'infeksi_hiv_terkonfirmasi' => $this->snomedCode('165816005', 'Human immunodeficiency virus positive'),
                    'terpajan_hiv_mungkin_infeksi' => $this->kemkesCode('OV000187', 'Terpajan HIV: mungkin infeksi HIV'),
                    'infeksi_hiv_tidak_diketahui' => $this->kemkesCode('OV000188', 'Infeksi HIV tidak diketahui'),
                    'bukan_infeksi_hiv' => $this->kemkesCode('OV000189', 'Bukan infeksi HIV'),
                ],
            ],
            'bb_asi' => [
                'source' => 'menyusu_bb',
                'obs_code' => $this->kemkesCode('OC000085', 'Klasifikasi berat badan menurut umur dan/atau masalah pemberian ASI'),
                'obs_category' => 'exam',
                'qr_top' => ['5', 'Pemeriksaan Kemungkinan Berat Badan Rendah Menurut Umur dan Masalah Pemberian ASI 0-<2 Bulan'],
                'qr_gate' => ['5.1', 'Apakah dilakukan pemeriksaan kemungkinan berat badan rendah menurut umur dan masalah pemberian ASI?'],
                'qr_middle' => null,
                'qr_result' => ['5.1.1', 'Hasil pemeriksaan berat badan menurut umur dan/atau masalah pemberian ASI'],
                'negative_finding' => ['bb_tidak_rendah_tidak_ada_masalah_asi'],
                'values' => [
                    'bb_sangat_rendah_menurut_umur' => $this->kemkesCode('OV000201', 'Berat badan sangat rendah menurut umur'),
                    'bb_rendah_masalah_pemberian_asi' => $this->kemkesCode('OV000202', 'Berat badan rendah menurut umur dan/atau masalah pemberian ASI'),
                    'bb_tidak_rendah_tidak_ada_masalah_asi' => $this->kemkesCode('OV000203', 'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian ASI'),
                ],
            ],
            // 'bb_minum' SENGAJA tidak dimasukkan ke tabel generik ini - qr-nya
            // punya item pra-kondisi (6.1 "Ibu HIV positif dan tidak menyusui?")
            // yang jadi SIBLING dari gate (6.2), bukan nested biasa. Lihat
            // buildBbMinumQuestionnaireBranch().
        ];
    }

    /**
     * Menentukan jalur "menyusu_bb": ASI (default) atau Minum (ibu HIV+ dan
     * bayi tidak diberi ASI). Mirror persis dari $jalurPemberianMinum di
     * MTBMController::hitungKlasifikasiMtbm() - kalau logic itu berubah,
     * ubah juga di sini.
     */
    private function resolveJalurMenyusuBb(array $bundle): string
    {
        $sub = $bundle['data']['subjektif'] ?? null;
        if (!$sub) {
            return 'asi';
        }

        $statusHivIbu = $sub->status_hiv_ibu ?? null;
        $bayiMendapatAsiRaw = $sub->bayi_mendapat_asi ?? null;
        $bayiMendapatAsi = $bayiMendapatAsiRaw === null ? true : (bool) $bayiMendapatAsiRaw;

        return ($statusHivIbu === 'positif' && !$bayiMendapatAsi) ? 'minum' : 'asi';
    }

    // =========================================================================
    // PREVIEW PAGE
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

        $subjektif = DB::table('mtbm_subjective')->where('kunjungan_id', $kunjunganId)->first();
        $objektif = DB::table('mtbm_objective')->where('kunjungan_id', $kunjunganId)->first();
        $assessment = DB::table('mtbm_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
        $planning = DB::table('mtbm_planning')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();

        $statusPasien = DB::table('mtbm_statuspasien')
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

        $episode = DB::table('mtbm_episode_of_care')
            ->where('pasien_id', $pasien->pasien_id)
            ->first();

        $decodeArray = static function ($value): array {
            if (is_array($value)) {
                return $value;
            }
            if ($value === null || $value === '') {
                return [];
            }
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        };

        $preview = [
            'header' => [
                'nama_pasien'       => $pasien->NAMA_LGKP ?? '-',
                'no_rm'             => $pasien->NO_MR ?? '-',
                'nik'               => $pasien->NIK ?? '-',
                'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan ?? null,
                'poli'              => $pasien->nmPoli ?? 'MTBM',
                'status_layanan'    => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
            ],

            'episode_of_care' => [
                'ada' => (bool) $episode,
                'episode_of_care_id' => $episode->episode_of_care_id ?? null,
                'label' => $episode
                    ? 'Sudah terdaftar sebagai episode neonatus'
                    : 'Belum terdaftar - akan dibuat otomatis saat kirim pertama kali',
            ],

            'kunjungan_mtbm' => [
                'encounter_id' => null,
                'location'     => env('SATUSEHAT_LOCATION_NAME', $pasien->nmPoli ?? 'Poli MTBM'),
                'practitioner' => $dokter->nmDokter ?? ($statusPasien->tenaga_medis ?? null) ?? '-',
                'keluhan_utama' => $subjektif->keluhan_utama ?? null,
                'infeksi'    => $assessment->klas_infeksi ?? null,
                'ikterus'    => $assessment->klas_ikterus ?? null,
                'diare'      => $assessment->klas_diare ?? null,
                'hiv'        => $assessment->klas_hiv ?? null,
                'menyusu_bb' => $assessment->klas_menyusu_bb ?? null,
                'status_kegawatan' => $assessment->status_kegawatan ?? null,
                'klasifikasi_global' => $assessment
                    ? $decodeArray($assessment->klasifikasi_global ?? '[]')
                    : [],
            ],

            'observasi_mtbm' => [
                'rr'   => $objektif->rr ?? null,
                'suhu' => $objektif->suhu ?? null,
                'spo2' => $objektif->spo2 ?? $objektif->spo2_tangan_kanan ?? null,
                'bb'   => $objektif->bb ?? null,
                'tb_pb' => $objektif->tb_pb ?? null,
                'lila' => $objektif->lila ?? null,
            ],

            'tatalaksana_mtbm' => [
                'tindakan_items' => $planning ? $decodeArray($planning->tindakan_items ?? '[]') : [],
                'resep_items'    => $planning ? $decodeArray($planning->resep_items ?? '[]') : [],
            ],

            'edukasi_mtbm' => [
                'konseling_edukasi' => $planning ? $decodeArray($planning->konseling_edukasi ?? '[]') : [],
                'catatan'           => $planning->catatan_planning ?? null,
                'kontrol_ulang'     => $planning->kontrol_ulang ?? null,
            ],
        ];

        return Inertia::render('Ruang_Layanan/KIA/MTBM/Satusehat/Preview', [
            'idPelayanan' => $idPelayanan,
            'idPoli' => $idPoli,
            'preview' => $preview,
        ]);
    }

    // =========================================================================
    // ORCHESTRATOR
    // =========================================================================

    public function sendSatusehatMtbm(Request $request, $idPoli, $idPelayanan)
    {
        try {
            // FIX: kalau frontend salah kirim parameter (link/route yang rusak),
            // Laravel tetap menerima "undefined"/"null" sebagai STRING literal,
            // bukan null PHP. Tanpa guard ini, errornya nyasar jadi "pasien
            // tidak ditemukan" yang menyesatkan (padahal ID-nya yang rusak).
            if (
                $idPelayanan === null
                || $idPelayanan === ''
                || in_array(strtolower((string) $idPelayanan), ['undefined', 'null', 'nan'], true)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => "Parameter idPelayanan tidak valid (diterima: \"{$idPelayanan}\"). Ini kemungkinan besar bug di link/route frontend yang mengarah ke halaman ini, bukan data pasien yang hilang.",
                    'logs' => [$this->makeStepLog('validate_params', false, 'idPelayanan tidak valid.', [
                        'idPoli' => $idPoli,
                        'idPelayanan' => $idPelayanan,
                    ])],
                ], 422);
            }

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
                'episode_of_care_id' => null,
                'encounter_id' => null,
                'condition_ids' => [],
                'observation_ids' => [],
                'procedure_ids' => [],
                'medication_request_ids' => [],
                'questionnaire_response_id' => null,
                'service_request_id' => null,
            ];

            // ---- PHASE 1: AUTH ----
            $tokenResp = $this->ssGetAccessToken();
            $logs[] = $tokenResp['log'];
            if (!$tokenResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Gagal generate token SATUSEHAT', 'logs' => $logs, 'result' => $result], 500);
            }
            $token = $tokenResp['token'];
            $result['token'] = 'OK';

            // ---- PHASE 2: REFERENCE ----
            $orgId = env('SATUSEHAT_ORG_ID');
            $result['organization_id'] = $orgId;
            $logs[] = $this->makeStepLog('organization', true, 'Organization ID dari env digunakan.', ['organization_id' => $orgId]);

            $locationResp = $this->ssResolveLocation($token);
            $logs[] = $locationResp['log'];
            if (!$locationResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Location SATUSEHAT tidak ditemukan', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
            }
            $locationId = $locationResp['id'];
            $result['location_id'] = $locationId;

            $patientResp = $this->ssResolvePatient($token, $bundle['data']['pasien']);
            $logs[] = $patientResp['log'];
            if (!$patientResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Patient SATUSEHAT tidak ditemukan', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
            }
            $patientId = $patientResp['id'];
            $result['patient_id'] = $patientId;

            $practitionerResp = $this->ssResolvePractitioner($token, $bundle['data']['nakes']);
            $logs[] = $practitionerResp['log'];
            if (!$practitionerResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Practitioner SATUSEHAT tidak ditemukan', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
            }
            $practitionerId = $practitionerResp['id'];
            $result['practitioner_id'] = $practitionerId;

            $logs[] = $this->makeStepLog('related_person', true, 'RelatedPerson di-skip: belum ada tabel lokal untuk data orang tua/wali pasien.', ['status' => 'skipped']);

            // ---- PHASE 2B: EPISODE OF CARE (KHUSUS MTBM) ----
            $episodeResp = $this->ssResolveOrCreateEpisodeOfCare($token, $bundle, $patientId, $orgId);
            $logs[] = $episodeResp['log'];
            if (!$episodeResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'EpisodeOfCare SATUSEHAT gagal dibuat/ditemukan',
                    'logs' => $logs,
                    'result' => $result,
                    'local_preview' => $bundle['data'],
                ], 422);
            }
            $episodeOfCareId = $episodeResp['id'];
            $result['episode_of_care_id'] = $episodeOfCareId;

            // ---- PHASE 3: ENCOUNTER (arrived -> in-progress), dengan episodeOfCare ----
            $encounterArrivedResp = $this->ssRegisterEncounterArrived($token, $bundle, $patientId, $practitionerId, $locationId, $orgId, $episodeOfCareId);
            $logs[] = $encounterArrivedResp['log'];
            if (!$encounterArrivedResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Encounter (arrived) SATUSEHAT gagal dibuat', 'logs' => $logs, 'result' => $result, 'encounter_payload' => $encounterArrivedResp['payload'] ?? null, 'local_preview' => $bundle['data']], 422);
            }
            $encounterId = $encounterArrivedResp['id'];
            $result['encounter_id'] = $encounterId;

            $encounterInProgressResp = $this->ssStartEncounterInProgress($token, $bundle, $encounterId, $patientId, $practitionerId, $locationId, $orgId, $episodeOfCareId);
            $logs[] = $encounterInProgressResp['log'];
            if (!$encounterInProgressResp['ok']) {
                return response()->json(['success' => false, 'message' => 'Encounter (in-progress) SATUSEHAT gagal diperbarui', 'logs' => $logs, 'result' => $result, 'encounter_payload' => $encounterInProgressResp['payload'] ?? null, 'local_preview' => $bundle['data']], 422);
            }

            // ---- PHASE 4A: Condition - Keluhan Utama ----
            $keluhanCandidates = $this->mapMtbmKeluhanUtamaCandidates($bundle);
            if (count($keluhanCandidates) === 0) {
                $logs[] = $this->makeStepLog('condition_keluhan_utama', true, 'Condition keluhan utama di-skip. Tidak ada keluhan utama tercatat.', ['status' => 'skipped']);
            } else {
                foreach ($keluhanCandidates as $keluhan) {
                    $resp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                        'category_code' => 'problem-list-item',
                        'category_display' => 'Problem List Item',
                        'code' => $keluhan['code'] ?? null,
                        'text' => $keluhan['text'],
                        'note' => 'Keluhan utama dari mtbm_subjective.keluhan_utama',
                    ]);
                    $logs[] = $resp['log'];
                    if ($resp['ok']) {
                        $result['condition_ids'][] = $resp['id'];
                    }
                }
            }

            // ---- PHASE 4B: Observation - Tanda Vital & Antropometri ----
            $vitalCandidates = $this->mapMtbmVitalAntropometriObservations($bundle);
            $observationIdsByKey = [];

            foreach ($vitalCandidates as $key => $obs) {
                $obsResp = $this->ssCreateObservation($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $obs);
                $logs[] = $obsResp['log'];
                if (!$obsResp['ok']) {
                    return response()->json(['success' => false, 'message' => "Observation ({$key}) SATUSEHAT gagal dibuat", 'logs' => $logs, 'result' => $result, 'observation_payload' => $obsResp['payload'] ?? null, 'local_preview' => $bundle['data']], 422);
                }
                $result['observation_ids'][] = $obsResp['id'];
                $observationIdsByKey[$key] = $obsResp['id'];
            }
            if (count($vitalCandidates) === 0) {
                $logs[] = $this->makeStepLog('observation_vital', true, 'Observation tanda vital/antropometri di-skip. Data objektif kosong.', ['status' => 'skipped']);
            }

            // ---- PHASE 4C: Observation - Klasifikasi MTBM ----
            $classificationCandidates = $this->mapMtbmKlasifikasiObservationCandidates($bundle);
            foreach ($classificationCandidates as $key => $obs) {
                $obsResp = $this->ssCreateObservation($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $obs);
                $logs[] = $obsResp['log'];
                if (!$obsResp['ok']) {
                    return response()->json(['success' => false, 'message' => "Observation klasifikasi ({$key}) SATUSEHAT gagal dibuat", 'logs' => $logs, 'result' => $result, 'observation_payload' => $obsResp['payload'] ?? null, 'local_preview' => $bundle['data']], 422);
                }
                $result['observation_ids'][] = $obsResp['id'];
                $observationIdsByKey[$key] = $obsResp['id'];
            }
            if (count($classificationCandidates) === 0) {
                $logs[] = $this->makeStepLog('observation_klasifikasi', true, 'Observation klasifikasi MTBM di-skip. Tidak ada assessment yang bisa dipetakan.', ['status' => 'skipped']);
            }

            // ---- PHASE 4D: QuestionnaireResponse ----
            $questionnaireItems = $this->buildMtbmQuestionnaireResponseItems($bundle, $observationIdsByKey);
            if (count($questionnaireItems) === 0) {
                $logs[] = $this->makeStepLog('questionnaire_response', true, 'QuestionnaireResponse di-skip. Tidak ada klasifikasi yang bisa direferensikan.', ['status' => 'skipped']);
            } else {
                $qrResp = $this->ssCreateQuestionnaireResponse($token, $bundle, $patientId, $encounterId, $practitionerId, $questionnaireItems);
                $logs[] = $qrResp['log'];
                if (!$qrResp['ok']) {
                    return response()->json(['success' => false, 'message' => 'QuestionnaireResponse SATUSEHAT gagal dibuat', 'logs' => $logs, 'result' => $result, 'questionnaire_payload' => $qrResp['payload'] ?? null, 'local_preview' => $bundle['data']], 422);
                }
                $result['questionnaire_response_id'] = $qrResp['id'];
            }

            // ---- PHASE 4E: Condition - Diagnosis ----
            $conditionRefs = [];
            $conditionCandidates = $this->mapMtbmConditionCandidates($bundle);
            if (count($conditionCandidates) === 0) {
                $logs[] = $this->makeStepLog('condition_diagnosis', true, 'Condition diagnosis di-skip. Tidak ada diagnosis MTBM yang bisa dipetakan.', ['status' => 'skipped']);
            } else {
                foreach ($conditionCandidates as $idx => $diagnosis) {
                    $resp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                        'category_code' => 'encounter-diagnosis',
                        'category_display' => 'Encounter Diagnosis',
                        'code' => ['system' => self::SYS_ICD10, 'code' => $diagnosis['code'], 'display' => $diagnosis['display']],
                        'text' => $diagnosis['display'],
                        'note' => 'MTBM diagnosis dari mtbm_diagnosa_medis - rank ' . ($idx + 1),
                    ]);
                    $logs[] = $resp['log'];
                    if (!$resp['ok']) {
                        return response()->json(['success' => false, 'message' => 'Condition diagnosis SATUSEHAT gagal dibuat', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
                    }
                    $result['condition_ids'][] = $resp['id'];
                    $conditionRefs[] = ['id' => $resp['id'], 'display' => $diagnosis['display'], 'rank' => $idx + 1];
                }
            }

            // ---- PHASE 4F: Condition - Kondisi Saat Meninggalkan Faskes ----
            $dischargeCandidate = $this->mapMtbmDischargeConditionCandidate($bundle);
            if ($dischargeCandidate === null) {
                $logs[] = $this->makeStepLog('condition_discharge', true, 'Condition kondisi-pulang di-skip. mtbm_statuspasien belum diisi.', ['status' => 'skipped']);
            } else {
                $resp = $this->ssCreateCondition($token, $bundle, $patientId, $encounterId, $practitionerId, [
                    'category_code' => 'problem-list-item',
                    'category_display' => 'Problem List Item',
                    'code' => $dischargeCandidate['code'],
                    'text' => $dischargeCandidate['code']['display'],
                    'note' => 'Kondisi saat meninggalkan faskes.',
                ]);
                $logs[] = $resp['log'];
                if ($resp['ok']) {
                    $result['condition_ids'][] = $resp['id'];
                }
            }

            // ---- PHASE 4G: Finish Encounter ----
            if (count($conditionRefs) > 0) {
                $finishResp = $this->ssFinishEncounter($token, $bundle, $encounterId, $patientId, $practitionerId, $locationId, $orgId, $conditionRefs, $episodeOfCareId);
                $logs[] = $finishResp['log'];
                if (!$finishResp['ok']) {
                    return response()->json(['success' => false, 'message' => 'Encounter SATUSEHAT gagal di-finish', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
                }
            } else {
                $logs[] = $this->makeStepLog('finish_encounter', true, 'Finish Encounter di-skip karena tidak ada Condition diagnosis.', ['status' => 'skipped']);
            }

            // ---- PHASE 4H: Procedure ----
            $procedureCandidates = $this->mapMtbmProcedureCandidates($bundle);
            if (count($procedureCandidates) === 0) {
                $logs[] = $this->makeStepLog('procedure', true, 'Procedure di-skip. Tidak ada tindakan_items yang bisa dipetakan.', ['status' => 'skipped']);
            } else {
                foreach ($procedureCandidates as $proc) {
                    $resp = $this->ssCreateProcedure($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $proc);
                    $logs[] = $resp['log'];
                    if (!$resp['ok']) {
                        return response()->json(['success' => false, 'message' => 'Procedure SATUSEHAT gagal dibuat', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
                    }
                    $result['procedure_ids'][] = $resp['id'];
                }
            }

            // ---- PHASE 4I: MedicationRequest ----
            $medicationCandidates = $this->mapMtbmMedicationRequestCandidates($bundle);
            if (count($medicationCandidates) === 0) {
                $logs[] = $this->makeStepLog('medication_request', true, 'MedicationRequest di-skip. Tidak ada resep_items yang bisa dipetakan.', ['status' => 'skipped']);
            } else {
                foreach ($medicationCandidates as $med) {
                    $resp = $this->ssCreateMedicationRequest($token, $bundle, $patientId, $encounterId, $practitionerId, $orgId, $med);
                    $logs[] = $resp['log'];
                    if ($resp['ok']) {
                        $result['medication_request_ids'][] = $resp['id'];
                    }
                    // Kegagalan satu obat tidak menghentikan seluruh alur.
                }
            }

            // ---- PHASE 4J: ServiceRequest ----
            $serviceRequestCandidate = $this->mapMtbmServiceRequestCandidate($bundle, $conditionRefs);
            if (!$serviceRequestCandidate) {
                $logs[] = $this->makeStepLog('service_request', true, 'ServiceRequest di-skip. Tidak ada kontrol ulang yang bisa dikirim.', ['status' => 'skipped']);
            } else {
                $resp = $this->ssCreateServiceRequest($token, $bundle, $patientId, $encounterId, $practitionerId, $locationId, $orgId, $serviceRequestCandidate);
                $logs[] = $resp['log'];
                if (!$resp['ok']) {
                    return response()->json(['success' => false, 'message' => 'ServiceRequest SATUSEHAT gagal dibuat', 'logs' => $logs, 'result' => $result, 'local_preview' => $bundle['data']], 422);
                }
                $result['service_request_id'] = $resp['id'];
            }

            return response()->json([
                'success' => true,
                'message' => 'SATUSEHAT berhasil dijalankan sampai seluruh alur utama dari data database MTBM.',
                'logs' => $logs,
                'result' => $result,
                'local_preview' => $bundle['data'],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('MTBM sendSatusehatMtbm error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'idPelayanan' => $idPelayanan,
                'idPoli' => $idPoli,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat kirim data MTBM ke SATUSEHAT',
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
                'p.ID as pasien_id', 'pel.idpelayanan', 'pel.tglPelayanan', 'pel.sudahDilayani',
                'p.NO_MR', 'p.NAMA_LGKP', 'p.NIK', 'p.JENIS_KLMIN', 'p.TGL_LHR',
                'poli.nmPoli', 'p.alamat', 'l.tglKunjungan', 'l.kdPoli', 'l.idLoket'
            )
            ->first();

        if (!$pasien) {
            return [
                'ok' => false,
                'message' => 'Data pasien lokal tidak ditemukan.',
                'logs' => [$this->makeStepLog('load_local', false, 'Data pasien lokal tidak ditemukan.', ['idPelayanan' => $idPelayanan])],
            ];
        }

        $kunjunganId = (string) $idPelayanan;

        $subjektif = DB::table('mtbm_subjective')->where('kunjungan_id', $kunjunganId)->first();
        $objektif = DB::table('mtbm_objective')->where('kunjungan_id', $kunjunganId)->first();
        $assessment = DB::table('mtbm_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
        $planning = DB::table('mtbm_planning')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();

        $diagnosaMedis = Schema::hasTable('mtbm_diagnosa_medis')
            ? DB::table('mtbm_diagnosa_medis')->where('kunjungan_id', $kunjunganId)->orderBy('id')->get()
            : collect();

        $statusPasien = DB::table('mtbm_statuspasien')
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
                'tenaga_medis_raw' => $statusPasien->tenaga_medis ?? null,
                'id' => $dokter->idDokter ?? null,
                'nama' => $dokter->nmDokter ?? ($statusPasien->tenaga_medis ?? null),
                'nik' => $dokter->kdDokter ?? null,
                'ihs' => $dokter->ihs_nakes ?? null,
            ],
            'kunjungan' => [
                'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan,
                'poli' => $pasien->nmPoli,
                'status_layanan' => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
            ],
            'subjektif' => $subjektif,
            'objektif' => $objektif,
            'assessment' => $assessment,
            'planning' => $planning,
            'diagnosa_medis' => $diagnosaMedis,
            'status_pasien' => $statusPasien,
            'dokter' => $dokter,
            'kunjungan_id' => $kunjunganId,
            'id_pelayanan' => $idPelayanan,
        ];

        return ['ok' => true, 'message' => 'Data lokal siap dari database MTBM.', 'data' => $data];
    }

    // =========================================================================
    // PHASE 1-2: AUTH & REFERENCE (identik pola MTBS)
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
                return ['ok' => false, 'token' => null, 'log' => $this->makeStepLog('token', false, 'Generate token gagal.', ['status' => $res->status(), 'body' => $res->json()])];
            }
            $json = $res->json();
            return ['ok' => true, 'token' => $json['access_token'] ?? null, 'log' => $this->makeStepLog('token', true, 'Generate token berhasil.', ['token_type' => $json['token_type'] ?? null])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'token' => null, 'log' => $this->makeStepLog('token', false, 'Generate token exception.', ['error' => $e->getMessage()])];
        }
    }

    private function ssResolveLocation(string $token): array
    {
        try {
            $locationIdFromEnv = env('SATUSEHAT_LOCATION_ID');
            if (!empty($locationIdFromEnv)) {
                return ['ok' => true, 'id' => $locationIdFromEnv, 'log' => $this->makeStepLog('location', true, 'Location ID dari env digunakan.', ['location_id' => $locationIdFromEnv])];
            }

            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $locationName = env('SATUSEHAT_LOCATION_NAME', 'Poli Tumbuh Kembang');
            $orgId = env('SATUSEHAT_ORG_ID');

            $res = Http::withToken($token)->acceptJson()->get($base . '/Location', ['name' => $locationName, 'organization' => $orgId]);
            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('location', false, 'Search Location gagal.', ['status' => $res->status(), 'body' => $res->json()])];
            }
            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;
            if (!$entry) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('location', false, 'Location tidak ditemukan.', ['name' => $locationName, 'total' => $json['total'] ?? 0])];
            }
            return ['ok' => true, 'id' => $entry['id'] ?? null, 'log' => $this->makeStepLog('location', true, 'Location ditemukan.', ['location_id' => $entry['id'] ?? null])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('location', false, 'Search Location exception.', ['error' => $e->getMessage()])];
        }
    }

    private function ssResolvePatient(string $token, array $pasien): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $nikLokal = $pasien['nik'] ?? null;
            $namaLokal = $pasien['nama'] ?? null;

            if (empty($nikLokal)) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('patient', false, 'NIK pasien lokal kosong.', ['nama' => $namaLokal])];
            }

            $res = Http::withToken($token)->acceptJson()->get($base . '/Patient', ['identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nikLokal]);
            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('patient', false, 'Search Patient gagal.', ['nik_dipakai' => $nikLokal, 'status' => $res->status(), 'body' => $res->json()])];
            }
            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;
            if (!$entry || empty($entry['id'])) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('patient', false, 'Patient tidak ditemukan berdasarkan NIK database.', ['nik_dipakai' => $nikLokal, 'total' => $json['total'] ?? 0])];
            }
            return ['ok' => true, 'id' => $entry['id'], 'log' => $this->makeStepLog('patient', true, 'Patient ditemukan by NIK.', ['patient_id' => $entry['id']])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('patient', false, 'Search Patient exception.', ['error' => $e->getMessage()])];
        }
    }

    private function ssResolvePractitioner(string $token, array $nakes): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $ihsLokal = $nakes['ihs'] ?? null;
            $nikLokal = $nakes['nik'] ?? null;

            if (!empty($ihsLokal)) {
                $res = Http::withToken($token)->acceptJson()->get($base . '/Practitioner/' . $ihsLokal);
                if ($res->successful()) {
                    $json = $res->json();
                    if (!empty($json['id'])) {
                        return ['ok' => true, 'id' => $json['id'], 'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by IHS.', ['ihs_dipakai' => $ihsLokal])];
                    }
                }
                // Jatuh ke pencarian NIK kalau IHS gagal/invalid - jangan langsung menyerah.
            }

            if (empty($nikLokal)) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('practitioner', false, 'IHS tidak valid dan NIK/kode dokter lokal kosong.', ['ihs_lokal' => $ihsLokal])];
            }

            $res = Http::withToken($token)->acceptJson()->get($base . '/Practitioner', ['identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nikLokal]);
            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('practitioner', false, 'Search Practitioner by NIK gagal.', ['nik_dipakai' => $nikLokal, 'status' => $res->status()])];
            }
            $json = $res->json();
            $entry = $json['entry'][0]['resource'] ?? null;
            if (!$entry || empty($entry['id'])) {
                return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('practitioner', false, 'Practitioner tidak ditemukan.', ['nik_dipakai' => $nikLokal, 'total' => $json['total'] ?? 0])];
            }
            return ['ok' => true, 'id' => $entry['id'], 'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by NIK (fallback dari IHS).', ['practitioner_id' => $entry['id']])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'log' => $this->makeStepLog('practitioner', false, 'Search Practitioner exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // EPISODE OF CARE (KHUSUS MTBM - lihat catatan class-level)
    // =========================================================================

    /**
     * CATATAN: struktur payload EpisodeOfCare di bawah mengikuti konvensi FHIR +
     * SATUSEHAT standar (resourceType, status, type, patient, managingOrganization,
     * period) yang konsisten dengan resource lain yang SUDAH terverifikasi
     * (Encounter, Condition, dst). Field `type.coding` (system/code/display)
     * diverifikasi langsung dari Playbook bagian C.3. Belum ada contoh worked
     * JSON lengkap dari Postman collection untuk resource ini secara spesifik -
     * kalau SATUSEHAT menolak payload ini, cek field yang diminta di respons
     * error dan sesuaikan.
     */
    private function ssResolveOrCreateEpisodeOfCare(string $token, array $bundle, string $patientId, string $orgId): array
    {
        $pasienId = $bundle['data']['pasien']['pasien_id'] ?? null;

        if ($pasienId && Schema::hasTable('mtbm_episode_of_care')) {
            $existing = DB::table('mtbm_episode_of_care')->where('pasien_id', $pasienId)->first();
            if ($existing) {
                return [
                    'ok' => true,
                    'id' => $existing->episode_of_care_id,
                    'log' => $this->makeStepLog('episode_of_care', true, 'EpisodeOfCare sudah ada, dipakai ulang (tidak membuat baru).', [
                        'episode_of_care_id' => $existing->episode_of_care_id,
                        'pasien_id' => $pasienId,
                    ]),
                ];
            }
        }

        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'EpisodeOfCare',
                'status' => 'active',
                'type' => [[
                    'coding' => [[
                        'system' => self::SYS_EPISODE_TYPE,
                        'code' => 'Neonate',
                        'display' => 'Neonate',
                    ]],
                ]],
                'patient' => ['reference' => 'Patient/' . $patientId],
                'managingOrganization' => ['reference' => 'Organization/' . $orgId],
                'period' => ['start' => $start],
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/EpisodeOfCare', $payload);
            $json = $res->json();

            if (!$res->successful() || empty($json['id'])) {
                return [
                    'ok' => false,
                    'id' => null,
                    'log' => $this->makeStepLog('episode_of_care', false, 'Create EpisodeOfCare gagal.', [
                        'status' => $res->status(),
                        'payload' => $payload,
                        'response' => $json,
                        'raw_body' => $res->body(),
                    ]),
                ];
            }

            $episodeId = $json['id'];

            if ($pasienId && Schema::hasTable('mtbm_episode_of_care')) {
                DB::table('mtbm_episode_of_care')->insert([
                    'pasien_id' => $pasienId,
                    'episode_of_care_id' => $episodeId,
                    'patient_satusehat_id' => $patientId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return [
                'ok' => true,
                'id' => $episodeId,
                'log' => $this->makeStepLog('episode_of_care', true, 'EpisodeOfCare baru berhasil dibuat dan disimpan lokal.', [
                    'episode_of_care_id' => $episodeId,
                    'payload' => $payload,
                    'response' => $json,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('episode_of_care', false, 'Create EpisodeOfCare exception.', [
                    'error' => $e->getMessage(),
                    'trace_line' => $e->getLine(),
                ]),
            ];
        }
    }

    // =========================================================================
    // ENCOUNTER (arrived -> in-progress -> finished), dengan episodeOfCare
    // =========================================================================

    private function baseEncounterPayload(array $bundle, string $patientId, string $practitionerId, string $locationId, string $orgId, string $status, string $start, ?string $end, ?string $episodeOfCareId): array
    {
        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
        $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';

        $payload = [
            'resourceType' => 'Encounter',
            'identifier' => [[
                'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $orgId,
                'value' => (string) Str::uuid(),
            ]],
            'status' => $status,
            'class' => ['system' => self::SYS_ACT_CODE, 'code' => 'AMB', 'display' => 'ambulatory'],
            'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
            'participant' => [[
                'type' => [['coding' => [['system' => self::SYS_PARTICIPATION, 'code' => 'ATND', 'display' => 'attender']]]],
                'individual' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
            ]],
            'period' => array_filter(['start' => $start, 'end' => $end]),
            'location' => [['location' => ['reference' => 'Location/' . $locationId, 'display' => $poliNama]]],
            'serviceProvider' => ['reference' => 'Organization/' . $orgId],
        ];

        // Wajib untuk MTBM: menandai kunjungan ini bagian dari episode neonatus.
        if (!empty($episodeOfCareId)) {
            $payload['episodeOfCare'] = [['reference' => 'EpisodeOfCare/' . $episodeOfCareId]];
        }

        return $payload;
    }

    private function ssRegisterEncounterArrived(string $token, array $bundle, string $patientId, string $practitionerId, string $locationId, string $orgId, ?string $episodeOfCareId): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'arrived', $start, null, $episodeOfCareId);
            $payload['statusHistory'] = [['status' => 'arrived', 'period' => ['start' => $start]]];

            $res = Http::withToken($token)->acceptJson()->post($base . '/Encounter', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('encounter_arrived', false, 'Registrasi Encounter (arrived) gagal.', ['status' => $res->status(), 'payload' => $payload, 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('encounter_arrived', true, 'Registrasi Encounter (arrived) berhasil.', ['encounter_id' => $json['id'] ?? null])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('encounter_arrived', false, 'Registrasi Encounter (arrived) exception.', ['error' => $e->getMessage()])];
        }
    }

    private function ssStartEncounterInProgress(string $token, array $bundle, string $encounterId, string $patientId, string $practitionerId, string $locationId, string $orgId, ?string $episodeOfCareId): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'in-progress', $start, null, $episodeOfCareId);
            $payload['id'] = $encounterId;
            $payload['statusHistory'] = [
                ['status' => 'arrived', 'period' => ['start' => $start, 'end' => $start]],
                ['status' => 'in-progress', 'period' => ['start' => $start]],
            ];

            $res = Http::withToken($token)->acceptJson()->put($base . '/Encounter/' . $encounterId, $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('encounter_in_progress', false, 'Update Encounter (in-progress) gagal.', ['status' => $res->status(), 'payload' => $payload, 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? $encounterId, 'payload' => $payload, 'log' => $this->makeStepLog('encounter_in_progress', true, 'Update Encounter (in-progress) berhasil.', ['encounter_id' => $json['id'] ?? $encounterId])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('encounter_in_progress', false, 'Update Encounter (in-progress) exception.', ['error' => $e->getMessage()])];
        }
    }

    private function ssFinishEncounter(string $token, array $bundle, string $encounterId, string $patientId, string $practitionerId, string $locationId, string $orgId, array $conditionRefs, ?string $episodeOfCareId): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $start = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');
            $end = Carbon::parse($tanggalKunjungan)->copy()->addMinutes(15)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $diagnosis = [];
            foreach ($conditionRefs as $idx => $cond) {
                $diagnosis[] = [
                    'condition' => ['reference' => 'Condition/' . $cond['id'], 'display' => $cond['display']],
                    'use' => ['coding' => [['system' => self::SYS_DIAGNOSIS_ROLE, 'code' => $idx === 0 ? 'AD' : 'DD', 'display' => $idx === 0 ? 'Admission diagnosis' : 'Discharge diagnosis']]],
                    'rank' => $cond['rank'] ?? ($idx + 1),
                ];
            }

            $payload = $this->baseEncounterPayload($bundle, $patientId, $practitionerId, $locationId, $orgId, 'finished', $start, $end, $episodeOfCareId);
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
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter gagal.', ['status' => $res->status(), 'payload' => $payload, 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? $encounterId, 'payload' => $payload, 'log' => $this->makeStepLog('finish_encounter', true, 'Finish Encounter berhasil.', ['encounter_id' => $json['id'] ?? $encounterId])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // CONDITION
    // =========================================================================

    private function mapMtbmConditionCandidates(array $bundle): array
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
            $items[$code . '|' . $display] = ['code' => $code, 'display' => $display];
        }
        return array_values($items);
    }

    /**
     * Condition - Keluhan Utama. Beda dari MTBS: mtbm_subjective.keluhan_utama
     * adalah STRING tunggal, bukan array JSON.
     */
    private function mapMtbmKeluhanUtamaCandidates(array $bundle): array
    {
        $sub = $bundle['data']['subjektif'] ?? null;
        if (!$sub || empty($sub->keluhan_utama)) {
            return [];
        }

        $keluhanText = trim((string) $sub->keluhan_utama);
        if ($keluhanText === '') {
            return [];
        }

        $lower = Str::lower($keluhanText);
        $map = $this->keluhanUtamaSnomedMap();
        $code = null;
        foreach ($map as $keyword => $coding) {
            if (Str::contains($lower, $keyword)) {
                $code = $coding;
                break;
            }
        }

        return [['text' => $keluhanText, 'code' => $code]];
    }

    /**
     * Condition - Kondisi Saat Meninggalkan Faskes, diturunkan dari
     * mtbm_assessment.status_kegawatan ('Perlu rujukan segera' dianggap
     * tidak stabil; selain itu dianggap stabil).
     */
    private function mapMtbmDischargeConditionCandidate(array $bundle): ?array
    {
        $statusPasien = $bundle['data']['status_pasien'] ?? null;
        if (!$statusPasien) {
            return null;
        }

        $assessment = $bundle['data']['assessment'] ?? null;
        $statusKegawatan = $assessment->status_kegawatan ?? null;

        if ($statusKegawatan === 'Perlu rujukan segera') {
            return ['code' => $this->snomedCode('162668006', "Patient's condition unstable")];
        }

        return ['code' => $this->snomedCode('359746009', "Patient's condition stable")];
    }

    private function ssCreateCondition(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, array $spec): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $recordedDate = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $codeBlock = ['text' => $spec['text']];
            if (!empty($spec['code'])) {
                $codeBlock['coding'] = [['system' => $spec['code']['system'], 'code' => $spec['code']['code'], 'display' => $spec['code']['display']]];
            }

            $payload = [
                'resourceType' => 'Condition',
                'clinicalStatus' => ['coding' => [['system' => self::SYS_COND_CLINICAL, 'code' => 'active', 'display' => 'Active']]],
                'category' => [['coding' => [['system' => self::SYS_COND_CATEGORY, 'code' => $spec['category_code'], 'display' => $spec['category_display']]]]],
                'code' => $codeBlock,
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'recordedDate' => $recordedDate,
                'recorder' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
                'asserter' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
            ];
            if (!empty($spec['note'])) {
                $payload['note'] = [['text' => $spec['note']]];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/Condition', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('condition', false, 'Create Condition gagal.', ['category' => $spec['category_code'], 'status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('condition', true, 'Create Condition berhasil.', ['category' => $spec['category_code'], 'condition_id' => $json['id'] ?? null, 'condition_text' => $spec['text']])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('condition', false, 'Create Condition exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // OBSERVATION - Tanda Vital & Antropometri
    // =========================================================================

    private function mapMtbmVitalAntropometriObservations(array $bundle): array
    {
        $objektif = $bundle['data']['objektif'] ?? null;
        if (!$objektif) {
            return [];
        }

        $items = [];
        $pushQuantity = function (string $key, $value, array $code, string $unit, string $ucumCode, string $categoryCode, string $categoryDisplay) use (&$items) {
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

        // Suhu - satu-satunya tanda vital wajib per Playbook 5.1.
        $pushQuantity('suhu', $objektif->suhu ?? null, $this->loincCode('8310-5', 'Body temperature'), 'Cel', 'Cel', 'vital-signs', 'Vital Signs');

        // rr_ulang (repeat, dipakai rules lokal untuk keputusan RR bahaya) diprioritaskan
        // di atas rr pertama kalau ada dan valid, mirip logic $rrDinilai di MTBMController.
        $rrTerpakai = null;
        if (isset($objektif->rr_ulang) && $objektif->rr_ulang !== null && (int) $objektif->rr_ulang > 0) {
            $rrTerpakai = $objektif->rr_ulang;
        } elseif (isset($objektif->rr) && $objektif->rr !== null) {
            $rrTerpakai = $objektif->rr;
        }
        $pushQuantity('rr', $rrTerpakai, $this->loincCode('9279-1', 'Respiratory rate'), 'breaths/minute', '/min', 'vital-signs', 'Vital Signs');

        // spo2: MTBM punya beberapa titik ukur (tangan kanan/kaki kiri) - pakai
        // tangan kanan (pre-ductal, titik acuan standar) dulu, fallback ke field umum.
        $spo2Terpakai = $objektif->spo2_tangan_kanan ?? ($objektif->spo2 ?? null);
        $pushQuantity('spo2', $spo2Terpakai, $this->loincCode('59408-5', 'Oxygen saturation in Arterial blood by Pulse oximetry'), '%', '%', 'vital-signs', 'Vital Signs');

        $pushQuantity('bb', $objektif->bb ?? null, $this->loincCode('29463-7', 'Body weight'), 'kg', 'kg', 'vital-signs', 'Vital Signs');
        $pushQuantity('tb', $objektif->tb_pb ?? null, $this->loincCode('8302-2', 'Body height'), 'cm', 'cm', 'vital-signs', 'Vital Signs');
        $pushQuantity('lila', $objektif->lila ?? null, $this->snomedCode('284473002', 'Mid upper arm circumference'), 'cm', 'cm', 'exam', 'Exam');

        // Lingkar kepala TIDAK dikirim - mtbm_objective tidak punya kolom ini.

        return $items;
    }

    // =========================================================================
    // OBSERVATION - Klasifikasi MTBM
    // =========================================================================

    private function mapMtbmKlasifikasiObservationCandidates(array $bundle): array
    {
        $assessment = $bundle['data']['assessment'] ?? null;
        if (!$assessment) {
            return [];
        }

        $items = [];
        $spec = $this->mtbmClassificationSpec();

        foreach ($spec as $key => $def) {
            $sourceColumn = 'klas_' . ($def['source'] === 'menyusu_bb' ? 'menyusu_bb' : $def['source']);
            $localValue = $assessment->{$sourceColumn} ?? null;

            if ($localValue === null || $localValue === '') {
                continue;
            }
            if (!isset($def['values'][$localValue])) {
                continue;
            }

            $items[$key] = [
                'value_type' => 'codeable_concept',
                'value_coding' => $def['values'][$localValue],
                'code' => $def['obs_code'],
                'category' => ['code' => $def['obs_category'], 'display' => ucfirst($def['obs_category'])],
            ];
        }

        // --- BB Rendah + Masalah Minum: penanganan khusus (jalur eksklusif dari bb_asi) ---
        $menyusuBb = $assessment->klas_menyusu_bb ?? null;
        if ($menyusuBb !== null && $this->resolveJalurMenyusuBb($bundle) === 'minum') {
            $minumMap = [
                'bb_sangat_rendah_menurut_umur' => $this->kemkesCode('OV000201', 'Berat badan sangat rendah menurut umur'),
                'bb_rendah_masalah_pemberian_minum' => $this->kemkesCode('OV000204', 'Berat badan rendah menurut umur dan/atau masalah pemberian minum'),
                'bb_tidak_rendah_tidak_ada_masalah_minum' => $this->kemkesCode('OV000205', 'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian minum'),
            ];

            if (isset($minumMap[$menyusuBb])) {
                // Hapus kemungkinan entri 'bb_asi' yang salah alamat (menyusu_bb
                // dengan jalur ASI sudah ditangani di loop generik di atas -
                // untuk jalur minum, ganti dengan OC000086, bukan OC000085).
                unset($items['bb_asi']);

                $items['bb_minum'] = [
                    'value_type' => 'codeable_concept',
                    'value_coding' => $minumMap[$menyusuBb],
                    'code' => $this->kemkesCode('OC000086', 'Klasifikasi Berat Badan Menurut Umur dan/atau Masalah Pemberian Minum'),
                    'category' => ['code' => 'exam', 'display' => 'Exam'],
                ];
            }
        }

        return $items;
    }

    private function ssCreateObservation(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, string $orgId, array $obs): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $effectiveDateTime = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'Observation',
                'identifier' => [['system' => 'http://sys-ids.kemkes.go.id/observation/' . $orgId, 'value' => (string) Str::uuid()]],
                'status' => 'final',
                'category' => [['coding' => [['system' => self::SYS_OBS_CATEGORY, 'code' => $obs['category']['code'], 'display' => $obs['category']['display']]]]],
                'code' => ['coding' => [['system' => $obs['code']['system'], 'code' => $obs['code']['code'], 'display' => $obs['code']['display']]], 'text' => $obs['code']['display']],
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'effectiveDateTime' => $effectiveDateTime,
                'performer' => [['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama]],
            ];

            if ($obs['value_type'] === 'codeable_concept') {
                $payload['valueCodeableConcept'] = ['coding' => [['system' => $obs['value_coding']['system'], 'code' => $obs['value_coding']['code'], 'display' => $obs['value_coding']['display']]]];
            } else {
                $payload['valueQuantity'] = ['value' => $obs['value'], 'unit' => $obs['unit']['unit'], 'system' => $obs['unit']['system'], 'code' => $obs['unit']['code']];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/Observation', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('observation', false, 'Create Observation gagal.', ['observation_code' => $obs['code']['code'], 'status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('observation', true, 'Create Observation berhasil.', ['observation_id' => $json['id'] ?? null, 'observation_code' => $obs['code']['code']])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('observation', false, 'Create Observation exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // PROCEDURE (tindakan_items MTBM sudah terstruktur - tidak perlu keyword matching)
    // =========================================================================

    private function mapMtbmProcedureCandidates(array $bundle): array
    {
        $planning = $bundle['data']['planning'] ?? null;
        if (!$planning) {
            return [];
        }

        $tindakan = json_decode($planning->tindakan_items ?? '[]', true) ?: [];
        if (!is_array($tindakan) || count($tindakan) === 0) {
            return [];
        }

        $diagnosticCategory = $this->snomedCode('103693007', 'Diagnostic procedure');
        $therapeuticCategory = $this->snomedCode('277132007', 'Therapeutic procedure');

        $items = [];
        foreach ($tindakan as $row) {
            if (!is_array($row)) {
                continue;
            }

            $kode = trim((string) ($row['kode'] ?? ''));
            $nama = trim((string) ($row['nama_ind'] ?? $row['nama'] ?? ''));

            if ($kode === '' || $nama === '') {
                continue;
            }

            // simpus_master_tindakan tidak punya flag diagnostic/therapeutic
            // eksplisit - tebak dari nama, default ke diagnostic (paling umum
            // untuk tindakan pemeriksaan penunjang bayi muda).
            $lower = Str::lower($nama);
            $isTherapeutic = Str::contains($lower, ['nebul', 'oksigen', 'infus', 'suntik', 'resusitasi', 'kanguru', 'konseling', 'edukasi']);

            $items[] = [
                'source_text' => $nama,
                'category' => $isTherapeutic ? $therapeuticCategory : $diagnosticCategory,
                'code' => $kode,
                'display' => $nama,
                'performed_text' => $nama,
            ];
        }

        return $items;
    }

    private function ssCreateProcedure(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, string $orgId, array $procedure): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $performedDateTime = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'Procedure',
                'identifier' => [['system' => 'http://sys-ids.kemkes.go.id/procedure/' . $orgId, 'value' => (string) Str::uuid()]],
                'status' => 'completed',
                'category' => ['coding' => [['system' => $procedure['category']['system'], 'code' => $procedure['category']['code'], 'display' => $procedure['category']['display']]]],
                'code' => ['coding' => [['system' => self::SYS_ICD9CM, 'code' => $procedure['code'], 'display' => $procedure['display']]], 'text' => $procedure['performed_text']],
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'performedDateTime' => $performedDateTime,
                'performer' => [['actor' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama]]],
                'note' => [['text' => 'Dari mtbm_planning.tindakan_items: ' . $procedure['source_text']]],
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/Procedure', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('procedure', false, 'Create Procedure gagal.', ['status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('procedure', true, 'Create Procedure berhasil.', ['procedure_id' => $json['id'] ?? null])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('procedure', false, 'Create Procedure exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // MEDICATION REQUEST (baru - data resep_items MTBM sudah terstruktur)
    // =========================================================================

    private function mapMtbmMedicationRequestCandidates(array $bundle): array
    {
        $planning = $bundle['data']['planning'] ?? null;
        if (!$planning) {
            return [];
        }

        $resep = json_decode($planning->resep_items ?? '[]', true) ?: [];
        if (!is_array($resep) || count($resep) === 0) {
            return [];
        }

        $items = [];
        foreach ($resep as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nama = trim((string) ($row['nama'] ?? ''));
            if ($nama === '') {
                continue;
            }

            $items[] = [
                'kode_obat' => trim((string) ($row['kode_obat'] ?? '')),
                'nama' => $nama,
                'dosis' => trim((string) ($row['dosis'] ?? '')),
                'cara' => trim((string) ($row['cara'] ?? '')),
                'lama' => $row['lama'] ?? null,
                'satuan' => trim((string) ($row['satuan'] ?? '')),
            ];
        }

        return $items;
    }

    /**
     * CATATAN: Playbook memakai KFA (Kode Farmasi Nasional,
     * http://sys-ids.kemkes.go.id/kfa) untuk Medication.code, bersumber dari
     * Kamus KFA resmi Kemkes. simpus_master_obat.KODE_OBAT kemungkinan besar
     * BUKAN kode KFA (kode obat internal apotek Puskesmas) - jadi di sini
     * kode itu dikirim sebagai identifier lokal, BUKAN sebagai
     * Medication.code.coding. Kalau Puskesmas kamu sudah memetakan obat ke
     * KFA, sambungkan mapping-nya di sini sebelum produksi.
     */
    private function ssCreateMedicationRequest(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, string $orgId, array $med): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $authoredOn = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $medicationId = (string) Str::uuid();
            $dosisTeks = trim($med['dosis'] . ' ' . $med['cara'] . (($med['lama'] ?? null) ? ' selama ' . $med['lama'] . ' hari' : ''));

            $payload = [
                'resourceType' => 'MedicationRequest',
                'contained' => [[
                    'resourceType' => 'Medication',
                    'id' => $medicationId,
                    'identifier' => array_filter([
                        !empty($med['kode_obat']) ? [
                            'system' => 'http://sys-ids.kemkes.go.id/medication-local/' . $orgId,
                            'use' => 'official',
                            'value' => $med['kode_obat'],
                        ] : null,
                    ]),
                    'code' => ['text' => $med['nama']],
                    'status' => 'active',
                ]],
                'status' => 'active',
                'intent' => 'order',
                'medicationReference' => ['reference' => '#' . $medicationId, 'display' => $med['nama']],
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'authoredOn' => $authoredOn,
                'requester' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
                'dosageInstruction' => [array_filter([
                    'text' => $dosisTeks !== '' ? $dosisTeks : null,
                    'patientInstruction' => $med['dosis'] !== '' ? $med['dosis'] : null,
                ])],
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/MedicationRequest', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('medication_request', false, 'Create MedicationRequest gagal.', ['obat' => $med['nama'], 'status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('medication_request', true, 'Create MedicationRequest berhasil.', ['medication_request_id' => $json['id'] ?? null, 'obat' => $med['nama']])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('medication_request', false, 'Create MedicationRequest exception.', ['error' => $e->getMessage(), 'obat' => $med['nama'] ?? null])];
        }
    }

    // =========================================================================
    // SERVICE REQUEST
    // =========================================================================

    private function mapMtbmServiceRequestCandidate(array $bundle, array $conditionRefs = []): ?array
    {
        $planning = $bundle['data']['planning'] ?? null;
        $kunjungan = $bundle['data']['kunjungan'] ?? [];

        if (!$planning || empty($planning->kontrol_ulang)) {
            return null;
        }

        $hariKontrol = (int) $planning->kontrol_ulang;
        $catatan = $planning->catatan_planning ?: 'Kontrol ulang pasien MTBM untuk evaluasi lanjutan';
        $tanggalKunjungan = $kunjungan['tanggal_kunjungan'] ?? now()->toDateTimeString();
        $occurrence = Carbon::parse($tanggalKunjungan)->copy()->addDays($hariKontrol);

        return [
            'code_system' => self::SYS_SNOMED,
            'code' => '185389009',
            'display' => 'Follow-up visit',
            'text' => 'Kontrol ulang MTBM',
            'note' => $catatan,
            'occurrence' => $occurrence->timezone('UTC')->format('Y-m-d\TH:i:sP'),
            'reason_text' => $conditionRefs[0]['display'] ?? null,
        ];
    }

    private function ssCreateServiceRequest(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, string $locationId, string $orgId, array $serviceRequest): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $authoredOn = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'ServiceRequest',
                'identifier' => [['system' => 'http://sys-ids.kemkes.go.id/servicerequest/' . $orgId, 'value' => (string) Str::uuid()]],
                'status' => 'active',
                'intent' => 'original-order',
                'priority' => 'routine',
                'category' => [['coding' => [['system' => self::SYS_SNOMED, 'code' => '3457005', 'display' => 'Patient referral']]]],
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'authoredOn' => $authoredOn,
                'requester' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
                'performer' => [['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama]],
                'locationReference' => [['reference' => 'Location/' . $locationId, 'display' => $poliNama]],
                'code' => ['coding' => [['system' => $serviceRequest['code_system'], 'code' => $serviceRequest['code'], 'display' => $serviceRequest['display']]], 'text' => $serviceRequest['text']],
                'occurrenceDateTime' => $serviceRequest['occurrence'],
                'note' => [['text' => $serviceRequest['note']]],
            ];

            if (!empty($serviceRequest['reason_text'])) {
                $payload['reasonCode'] = [['text' => $serviceRequest['reason_text']]];
            }

            $res = Http::withToken($token)->acceptJson()->post($base . '/ServiceRequest', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest gagal.', ['status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('service_request', true, 'Create ServiceRequest berhasil.', ['service_request_id' => $json['id'] ?? null])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest exception.', ['error' => $e->getMessage()])];
        }
    }

    // =========================================================================
    // QUESTIONNAIRE RESPONSE - MTBM (Q0011)
    // =========================================================================

    private function buildMtbmQuestionnaireResponseItems(array $bundle, array $observationIdsByKey): array
    {
        $assessment = $bundle['data']['assessment'] ?? null;
        $spec = $this->mtbmClassificationSpec();
        $items = [];

        foreach ($spec as $key => $def) {
            if (!isset($observationIdsByKey[$key])) {
                continue;
            }

            $sourceColumn = 'klas_' . ($def['source'] === 'menyusu_bb' ? 'menyusu_bb' : $def['source']);
            $localValue = $assessment->{$sourceColumn} ?? null;
            $observationId = $observationIdsByKey[$key];

            [$gateLink, $gateText] = $def['qr_gate'];
            [$resultLink, $resultText] = $def['qr_result'];
            [$topLink, $topText] = $def['qr_top'];

            $resultItem = [
                'linkId' => $resultLink,
                'text' => $resultText,
                'answer' => [['valueReference' => ['reference' => 'Observation/' . $observationId]]],
            ];

            if ($def['qr_middle'] === null) {
                $gateItem = [
                    'linkId' => $gateLink,
                    'text' => $gateText,
                    'answer' => [['valueBoolean' => true, 'item' => [$resultItem]]],
                ];
            } else {
                [$middleLink, $middleText] = $def['qr_middle'];
                $isNegativeFinding = in_array($localValue, $def['negative_finding'] ?? [], true);

                $middleItem = [
                    'linkId' => $middleLink,
                    'text' => $middleText,
                    'answer' => [['valueBoolean' => !$isNegativeFinding, 'item' => [$resultItem]]],
                ];
                $gateItem = [
                    'linkId' => $gateLink,
                    'text' => $gateText,
                    'answer' => [['valueBoolean' => true, 'item' => [$middleItem]]],
                ];
            }

            $items[] = ['linkId' => $topLink, 'text' => $topText, 'item' => [$gateItem]];
        }

        // BB Rendah + Masalah Minum: struktur khusus (item 6.1 pra-kondisi
        // sejajar dengan gate 6.2, bukan nested biasa).
        if (isset($observationIdsByKey['bb_minum'])) {
            $items[] = $this->buildBbMinumQuestionnaireBranch($bundle, $observationIdsByKey['bb_minum']);
        }

        usort($items, static function (array $a, array $b): int {
            return ((float) $a['linkId']) <=> ((float) $b['linkId']);
        });

        return $items;
    }

    private function buildBbMinumQuestionnaireBranch(array $bundle, string $observationId): array
    {
        $sub = $bundle['data']['subjektif'] ?? null;
        $ibuHivPositifTidakMenyusui = $this->resolveJalurMenyusuBb($bundle) === 'minum';

        $item61 = [
            'linkId' => '6.1',
            'text' => 'Apakah ibu HIV positif dan tidak menyusui?',
            'answer' => [['valueBoolean' => $ibuHivPositifTidakMenyusui]],
        ];

        $resultItem = [
            'linkId' => '6.2.1',
            'text' => 'Hasil pemeriksaan berat badan menurut umur dan/atau masalah pemberian minum',
            'answer' => [['valueReference' => ['reference' => 'Observation/' . $observationId]]],
        ];

        $item62 = [
            'linkId' => '6.2',
            'text' => 'Apakah dilakukan pemeriksaan kemungkinan berat badan rendah menurut umur dan masalah pemberian minum?',
            'answer' => [['valueBoolean' => true, 'item' => [$resultItem]]],
        ];

        return [
            'linkId' => '6',
            'text' => 'Pemeriksaan Kemungkinan Berat Badan Rendah Menurut Umur dan Masalah Pemberian Minum 0-<2 Bulan',
            'item' => [$item61, $item62],
        ];
    }

    private function ssCreateQuestionnaireResponse(string $token, array $bundle, string $patientId, string $encounterId, string $practitionerId, array $items): array
    {
        try {
            $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
            $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Petugas MTBM');
            $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();
            $authored = Carbon::parse($tanggalKunjungan)->timezone('UTC')->format('Y-m-d\TH:i:sP');

            $payload = [
                'resourceType' => 'QuestionnaireResponse',
                'questionnaire' => env('SATUSEHAT_MTBM_QUESTIONNAIRE_URL', self::QUESTIONNAIRE_MTBM_URL),
                'status' => 'completed',
                'subject' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'encounter' => ['reference' => 'Encounter/' . $encounterId],
                'authored' => $authored,
                'author' => ['reference' => 'Practitioner/' . $practitionerId, 'display' => $nakesNama],
                'source' => ['reference' => 'Patient/' . $patientId, 'display' => $pasienNama],
                'item' => $items,
            ];

            $res = Http::withToken($token)->acceptJson()->post($base . '/QuestionnaireResponse', $payload);
            $json = $res->json();

            if (!$res->successful()) {
                return ['ok' => false, 'id' => null, 'payload' => $payload, 'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse gagal.', ['status' => $res->status(), 'response' => $json, 'raw_body' => $res->body()])];
            }
            return ['ok' => true, 'id' => $json['id'] ?? null, 'payload' => $payload, 'log' => $this->makeStepLog('questionnaire_response', true, 'Create QuestionnaireResponse berhasil.', ['questionnaire_response_id' => $json['id'] ?? null, 'jumlah_kategori' => count($items)])];
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'payload' => null, 'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse exception.', ['error' => $e->getMessage()])];
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
