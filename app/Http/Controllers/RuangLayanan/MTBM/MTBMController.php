<?php

namespace App\Http\Controllers\RuangLayanan\MTBM;

use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\DataMasterUnitDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MTBMController extends Controller
{
    public function index()
    {
        $userAuth = Auth::user();

        $DataUnit = DataMasterUnitDetail::with('DataMasterUnit')
            ->where('id_unit', $userAuth->unit)
            ->orderBy('id_kategori')
            ->get();

        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->leftJoin('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->leftJoin('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')
            ->where('l.kdPoli', '003')
            ->select(
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kdPoli'
            )
            ->get();

        return Inertia::render('Ruang_Layanan/KIA/MTBM/Index', [
            'DataUnit' => $DataUnit,
            'DataPasien' => $DataPasien,
        ]);
    }

    public function pelayanan($id, $idPoli, $idPelayanan)
    {
        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->where('pel.idpelayanan', $idPelayanan)
            ->select(
                'p.ID as ID',
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'poli.nmPoli',
                'p.alamat',
                'l.tglKunjungan',
                'l.kdPoli',
                DB::raw("TIMESTAMPDIFF(YEAR, p.TGL_LHR, l.tglKunjungan) as umur"),
                DB::raw("
                    TIMESTAMPDIFF(
                        MONTH,
                        DATE_ADD(p.TGL_LHR, INTERVAL TIMESTAMPDIFF(YEAR, p.TGL_LHR, l.tglKunjungan) YEAR),
                        l.tglKunjungan
                    ) as umur_bulan
                "),
                DB::raw("
                    DATEDIFF(
                        l.tglKunjungan,
                        DATE_ADD(
                            DATE_ADD(
                                p.TGL_LHR,
                                INTERVAL TIMESTAMPDIFF(YEAR, p.TGL_LHR, l.tglKunjungan) YEAR
                            ),
                            INTERVAL TIMESTAMPDIFF(
                                MONTH,
                                DATE_ADD(p.TGL_LHR, INTERVAL TIMESTAMPDIFF(YEAR, p.TGL_LHR, l.tglKunjungan) YEAR),
                                l.tglKunjungan
                            ) MONTH
                        )
                    ) as umur_hari
                ")
            )
            ->get();

        return Inertia::render('Ruang_Layanan/KIA/MTBM/Pelayanan', [
            'idPelayanan' => $idPelayanan,
            'idPoli' => $idPoli,
            'DataPasien' => $DataPasien,
        ]);
    }

    private function validasi(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422),
            ];
        }

        return [
            'ok' => true,
            'data' => $validator->validated(),
        ];
    }

    private function nullIfEmpty($value)
    {
        return $value === '' ? null : $value;
    }

    private function boolValue($value): bool
    {
        return $value === true || $value === 1 || $value === '1' || $value === 'ya' || $value === 'true';
    }

    /**
     * Mengambil nilai pertama yang tersedia dari object/array.
     * Dipakai agar controller tetap kompatibel dengan nama kolom lama dan
     * kolom MTBM baru yang akan ditambahkan pada tahap Objektif.
     */
    private function firstAvailableValue($source, array $fields, $default = null)
    {
        foreach ($fields as $field) {
            if (is_object($source) && property_exists($source, $field)) {
                $value = $source->{$field};

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }

            if (is_array($source) && array_key_exists($field, $source)) {
                $value = $source[$field];

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return $default;
    }

    private function anyTrueValue($source, array $fields): bool
    {
        foreach ($fields as $field) {
            $value = $this->firstAvailableValue($source, [$field], null);

            if ($value !== null && $this->boolValue($value)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Mengecek apakah salah satu field benar-benar mempunyai nilai input.
     * Nilai 0 tetap dianggap terisi; null dan string kosong dianggap belum diisi.
     */
    private function hasMtbmInputValue($source, array $fields): bool
    {
        foreach ($fields as $field) {
            $exists = false;
            $value = null;

            if (is_object($source) && property_exists($source, $field)) {
                $exists = true;
                $value = $source->{$field};
            } elseif (is_array($source) && array_key_exists($field, $source)) {
                $exists = true;
                $value = $source[$field];
            }

            if ($exists && $value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Menentukan bagian Assessment MTBM yang benar-benar dinilai/di-trigger.
     * Bagian yang tidak pernah diisi akan disimpan sebagai null agar kartu dan
     * klasifikasi hijau bawaan tidak ikut tampil saat melakukan testing kasus lain.
     */
    private function mtbmAssessmentSectionPresence(
        $sub,
        $obj,
        ?int $umurHari = null,
        ?float $bbKg = null
    ): array {
        $infeksiBooleanFields = [
            'biru_sekitar_mulut',
            'sianosis',
            'merintih',
            'napas_cuping_hidung',
            'tarikan_dinding_dada_sangat_kuat',
            'lemah_tidak_mau_mengisap',
            'kejang_saat_ini',
            'tidak_bab_48_jam',
            'muntah_susu_atau_hijau',
            'muntah_hijau',
            'perut_kembung_sulit_bernapas',
            'tidak_ada_lubang_anus',
            'feses_lubang_abnormal',
            'mata_bernanah_banyak',
            'pusar_bernanah',
            'pusar_kemerahan_meluas',
            'mata_bernanah_sedikit',
            'pusar_kemerahan',
            'pustul_kulit',
        ];

        $infeksiDinilai = $this->hasMtbmInputValue($obj, [
                'rr',
                'rr_ulang',
                'suhu',
                'spo2',
                'spo2_tangan_kanan',
                'spo2_kaki_kiri',
                'kesadaran',
                'nadi_status',
            ])
            || $this->anyTrueValue($obj, $infeksiBooleanFields)
            || $this->boolValue($sub->kejang ?? false);

        // Diare hanya diklasifikasikan bila keluhan diare benar-benar aktif.
        // Nilai false/0 bawaan form tidak dianggap sebagai penilaian diare.
        $adaDiareRaw = $this->firstAvailableValue($sub, ['ada_diare'], null);
        $diareDinilai = ($adaDiareRaw !== null && $this->boolValue($adaDiareRaw))
            || (
                isset($sub->diare_lama_hari)
                && $sub->diare_lama_hari !== null
                && is_numeric($sub->diare_lama_hari)
                && (int) $sub->diare_lama_hari > 0
            );

        $jawabanKuning = $this->firstAvailableValue($obj, ['kuning'], null);
        $jawabanKuningTelapak = $this->firstAvailableValue($obj, ['kuning_telapak'], null);

        $ikterusDinilai = $this->anyTrueValue($obj, ['ikterus', 'ikterus_telapak'])
            || in_array($jawabanKuning, ['ya', 'tidak'], true)
            || in_array($jawabanKuningTelapak, ['ya', 'tidak'], true)
            || $this->hasMtbmInputValue($obj, [
                'umur_mulai_kuning_jam',
                'umur_mulai_kuning_hari',
            ]);

        // Boolean ASI bawaan tidak cukup untuk mengaktifkan assessment HIV.
        // Harus ada status ibu atau hasil tes yang benar-benar dipilih.
        $hivDinilai = $this->hasMtbmInputValue($sub, [
            'status_hiv_ibu',
            'tes_virologis_bayi',
            'tes_serologis_bayi',
        ]);

        $beratSangatRendahTerpicu = $umurHari !== null
            && $umurHari < 7
            && $bbKg !== null
            && $bbKg > 0
            && $bbKg < 2;

        $menyusuBbDinilai = $beratSangatRendahTerpicu
            || $this->hasMtbmInputValue($obj, [
                'status_bb_u',
                'zscore_bb_u',
                'frekuensi_asi_24_jam',
            ])
            || $this->anyTrueValue($obj, [
                'menggunakan_botol',
                'makanan_minuman_lain',
                'posisi_menyusu_salah',
                'perlekatan_tidak_baik',
                'mengisap_tidak_efektif',
                'thrush',
                'celah_bibir_langit',
                'minuman_pengganti_tidak_sesuai',
                'jumlah_minuman_tidak_adekuat',
                'penyiapan_minuman_tidak_higienis',
            ]);

        return [
            'infeksi' => $infeksiDinilai,
            'diare' => $diareDinilai,
            'ikterus' => $ikterusDinilai,
            'hiv' => $hivDinilai,
            'menyusu_bb' => $menyusuBbDinilai,
        ];
    }

    /**
     * Menambahkan field Subjektif baru hanya jika kolomnya tersedia.
     * Field tambahan MTBM dapat dipasang bertahap tanpa memicu unknown column.
     */
    private function putSubjectiveColumn(
        array &$payload,
        array $validated,
        string $column,
        string $type = 'raw',
        ?string $requestKey = null
    ): void {
        $requestKey = $requestKey ?: $column;

        if (!array_key_exists($requestKey, $validated)) {
            return;
        }

        if (!Schema::hasColumn('mtbm_subjective', $column)) {
            return;
        }

        $value = $validated[$requestKey];

        if ($type === 'boolean') {
            $payload[$column] = (int) $this->boolValue($value);
            return;
        }

        if ($type === 'numeric' || $type === 'integer') {
            $payload[$column] = ($value === '' || $value === null) ? null : $value;
            return;
        }

        $payload[$column] = $this->nullIfEmpty($value);
    }

    private function putObjectiveColumn(
        array &$payload,
        array $validated,
        string $column,
        string $type = 'raw',
        ?string $requestKey = null
    ): void {
        $requestKey = $requestKey ?: $column;

        if (!array_key_exists($requestKey, $validated)) {
            return;
        }

        if (!Schema::hasColumn('mtbm_objective', $column)) {
            return;
        }

        $value = $validated[$requestKey];

        if ($type === 'boolean') {
            $payload[$column] = (int) $this->boolValue($value);
            return;
        }

        if ($type === 'numeric' || $type === 'integer') {
            $payload[$column] = ($value === '' || $value === null) ? null : $value;
            return;
        }

        $payload[$column] = $this->nullIfEmpty($value);
    }

    /**
     * Konteks klinis yang diperlukan rules MTBM: umur bayi saat kunjungan,
     * berat badan, serta data objektif/subjektif terbaru.
     */
    private function getMtbmClinicalContext(?string $kunjunganId): array
    {
        $context = [
            'umur_hari' => null,
            'bb_kg' => null,
            'objective' => null,
            'subjective' => null,
        ];

        $kunjunganId = trim((string) $kunjunganId);

        if ($kunjunganId === '') {
            return $context;
        }

        $pasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->where('pel.idpelayanan', $kunjunganId)
            ->select('p.TGL_LHR', 'l.tglKunjungan')
            ->first();

        if ($pasien && !empty($pasien->TGL_LHR) && !empty($pasien->tglKunjungan)) {
            try {
                $lahir = Carbon::parse($pasien->TGL_LHR)->startOfDay();
                $kunjungan = Carbon::parse($pasien->tglKunjungan)->startOfDay();
                $selisih = $lahir->diffInDays($kunjungan, false);
                $context['umur_hari'] = $selisih >= 0 ? $selisih : null;
            } catch (\Throwable $e) {
                Log::warning('MTBM gagal menghitung umur hari', [
                    'kunjungan_id' => $kunjunganId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $context['objective'] = DB::table('mtbm_objective')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $context['subjective'] = DB::table('mtbm_subjective')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $bb = $context['objective']->bb ?? null;
        if ($bb !== null && $bb !== '' && is_numeric($bb)) {
            $context['bb_kg'] = (float) $bb;
        }

        return $context;
    }
    public function getStatusPasienOptions()
    {
        $statusPulang = [
            ['value' => 'Berobat Jalan', 'label' => 'Berobat Jalan'],
            ['value' => 'Meninggal', 'label' => 'Meninggal'],
            ['value' => 'Pulang Paksa', 'label' => 'Pulang Paksa'],
            ['value' => 'Rujuk Internal', 'label' => 'Rujuk Internal'],
            ['value' => 'Rujuk Vertikal PCare', 'label' => 'Rujuk Vertikal PCare'],
            ['value' => 'Rujuk Rumah Sakit Bukan BPJS', 'label' => 'Rujuk Rumah Sakit Bukan BPJS'],
            ['value' => 'Rujuk Rumah Sakit', 'label' => 'Rujuk Rumah Sakit'],
        ];

        $poliInternal = [];
        if (Schema::hasTable('simpus_poli_fktp')) {
            $poliInternal = DB::table('simpus_poli_fktp')
                ->select('kdPoli as kode', 'nmPoli as nama')
                ->orderBy('nmPoli')
                ->get();
        }

        $tenagaMedis = [];
        if (Schema::hasTable('users')) {
            $tenagaMedis = DB::table('users')
                ->select('id', DB::raw('name as nama'))
                ->orderBy('name')
                ->get();
        }

        $ppkRujukan = [];
        if (Schema::hasTable('data_master_unit_detail')) {
            $ppkRujukan = DB::table('data_master_unit_detail')
                ->select('id as kode', 'nama as nama')
                ->orderBy('nama')
                ->get();
        }

        return response()->json([
            'message' => 'OK',
            'data' => [
                'status_pulang' => $statusPulang,
                'poli_internal' => $poliInternal,
                'tenaga_medis' => $tenagaMedis,
                'ppk_rujukan' => $ppkRujukan,
            ],
        ]);
    }

    public function getStatusPasien(Request $request)
    {
        $kunjunganId = (string) $request->query('kunjungan_id');

        if (!$kunjunganId) {
            return response()->json([
                'message' => 'kunjungan_id wajib diisi',
                'data' => [],
            ], 422);
        }

        if (!Schema::hasTable('mtbm_status_pasien')) {
            return response()->json([
                'message' => 'Tabel mtbm_status_pasien belum ada',
                'data' => [],
            ], 500);
        }

        $data = DB::table('mtbm_status_pasien')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $data,
        ]);
    }

    public function storeStatusPasien(Request $request)
    {
        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:36'],
            'status_pulang' => ['required', 'string', 'max:100'],
            'tenaga_medis' => ['nullable', 'string', 'max:255'],
            'poli_internal' => ['nullable', 'string', 'max:255'],
            'ppk_rujukan' => ['nullable', 'string', 'max:255'],
            'nama_poli' => ['nullable', 'string', 'max:255'],
            'nama_dokter' => ['nullable', 'string', 'max:255'],
            'spesialis' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'tgl_rencana_berkunjung' => ['nullable', 'date'],
        ]);

        if (!$cek['ok']) return $cek['response'];

        if (!Schema::hasTable('mtbm_status_pasien')) {
            return response()->json([
                'message' => 'Tabel mtbm_status_pasien belum ada',
            ], 500);
        }

        $validated = $cek['data'];
        $kunjunganId = (string) $validated['kunjungan_id'];
        $userId = Auth::id();
        $statusPulang = $validated['status_pulang'];

        $poliTujuan = null;
        if ($statusPulang === 'Rujuk Internal') {
            $poliTujuan = $validated['poli_internal'] ?? null;
        } elseif (
            $statusPulang === 'Rujuk Vertikal PCare' ||
            $statusPulang === 'Rujuk Rumah Sakit Bukan BPJS' ||
            $statusPulang === 'Rujuk Rumah Sakit'
        ) {
            $poliTujuan = $validated['nama_poli'] ?? null;
        }

        $payload = [
            'kunjungan_id' => $kunjunganId,
            'status_pulang' => $statusPulang,
            'tenaga_medis' => $validated['tenaga_medis'] ?? null,
            'asal_poli' => 'MTBM',
            'poli_tujuan' => $poliTujuan,
            'poli_internal' => $validated['poli_internal'] ?? null,
            'ppk_rujukan' => $validated['ppk_rujukan'] ?? null,
            'nama_poli' => $validated['nama_poli'] ?? null,
            'nama_dokter' => $validated['nama_dokter'] ?? null,
            'spesialis' => $validated['spesialis'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'keterangan' => $validated['catatan'] ?? null,
            'tgl_rencana_berkunjung' => $validated['tgl_rencana_berkunjung'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('mtbm_status_pasien')->insert($payload);

        $data = DB::table('mtbm_status_pasien')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Status pasien MTBM berhasil disimpan',
            'data' => $data,
        ]);
    }

    public function getSubjektif($kunjungan_id)
    {
        $row = DB::table('mtbm_subjective')
            ->where('kunjungan_id', (string) $kunjungan_id)
            ->first();

        return response()->json([
            'message' => 'OK',
            'data' => $row,
        ]);
    }

    public function showSubjektif($kunjunganId)
    {
        return $this->getSubjektif($kunjunganId);
    }
    public function storeSubjektif(Request $request)
    {
        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:36'],
            'keluhan_utama' => ['nullable', 'string', 'max:255'],
            'lama_sakit_hari' => ['nullable', 'integer', 'min:0', 'max:365'],
            'bisa_minum_menyusu' => ['nullable', 'boolean'],
            'muntah_semua' => ['nullable', 'boolean'],
            'kejang' => ['nullable', 'boolean'],
            'batuk_lama_hari' => ['nullable', 'integer', 'min:0', 'max:365'],
            'ada_diare' => ['nullable', 'boolean'],
            'diare_lama_hari' => ['nullable', 'integer', 'min:0', 'max:365'],
            'darah_diare' => ['nullable', Rule::in(['ya', 'tidak'])],
            'demam_lama_hari' => ['nullable', 'integer', 'min:0', 'max:365'],
            'demam_tiap_hari' => ['nullable', Rule::in(['ya', 'tidak'])],
            'pernah_malaria' => ['nullable', 'boolean'],
            'minum_obat_malaria' => ['nullable', Rule::in(['ya', 'tidak'])],
            'campak_3_bulan' => ['nullable', Rule::in(['ya', 'tidak'])],
            'nyeri_telinga' => ['nullable', 'boolean'],
            'cairan_telinga' => ['nullable', 'boolean'],
            'riwayat_imunisasi' => ['nullable', 'string'],
            'riwayat_asi_makan' => ['nullable', 'string'],
            'keluhan_lain' => ['nullable', 'string'],

            // Penilaian HIV bayi muda, Buku Bagan MTBS 2022 halaman 43.
            'status_hiv_ibu' => ['nullable', Rule::in(['positif', 'negatif', 'belum_tes'])],
            'tes_virologis_bayi' => ['nullable', Rule::in(['positif', 'negatif', 'belum_tes'])],
            'tes_serologis_bayi' => ['nullable', Rule::in(['positif', 'negatif', 'belum_tes'])],
            'bayi_mendapat_asi' => ['nullable', 'boolean'],
            'bayi_pernah_mendapat_asi' => ['nullable', 'boolean'],
            'berhenti_asi_minggu' => ['nullable', 'integer', 'min:0', 'max:52'],
            'ibu_dalam_art' => ['nullable', 'boolean'],
            'bayi_profilaksis_arv' => ['nullable', 'boolean'],

            // Penilaian pemberian minum untuk ibu HIV positif yang tidak menyusui.
            'jenis_susu_pengganti' => ['nullable', 'string', 'max:255'],
            'frekuensi_minum_24_jam' => ['nullable', 'integer', 'min:0', 'max:100'],
            'jumlah_minum_per_kali_ml' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'tambahan_minuman_pengganti' => ['nullable', 'string', 'max:255'],
        ]);

        if (!$cek['ok']) return $cek['response'];

        $validated = $cek['data'];
        $kunjunganId = (string) $validated['kunjungan_id'];
        $userId = Auth::id();

        $payload = [
            'kunjungan_id' => $kunjunganId,
            'keluhan_utama' => $this->nullIfEmpty($validated['keluhan_utama'] ?? null),
            'lama_sakit_hari' => $validated['lama_sakit_hari'] ?? null,
            'bisa_minum_menyusu' => (int) ((bool) ($validated['bisa_minum_menyusu'] ?? true)),
            'muntah_semua' => (int) ((bool) ($validated['muntah_semua'] ?? false)),
            'kejang' => (int) ((bool) ($validated['kejang'] ?? false)),
            'batuk_lama_hari' => $validated['batuk_lama_hari'] ?? null,
            'diare_lama_hari' => $validated['diare_lama_hari'] ?? null,
            'darah_diare' => $this->nullIfEmpty($validated['darah_diare'] ?? null),
            'demam_lama_hari' => $validated['demam_lama_hari'] ?? null,
            'demam_tiap_hari' => $this->nullIfEmpty($validated['demam_tiap_hari'] ?? null),
            'pernah_malaria' => (int) ((bool) ($validated['pernah_malaria'] ?? false)),
            'minum_obat_malaria' => $this->nullIfEmpty($validated['minum_obat_malaria'] ?? null),
            'campak_3_bulan' => $this->nullIfEmpty($validated['campak_3_bulan'] ?? null),
            'nyeri_telinga' => (int) ((bool) ($validated['nyeri_telinga'] ?? false)),
            'cairan_telinga' => (int) ((bool) ($validated['cairan_telinga'] ?? false)),
            'riwayat_imunisasi' => $this->nullIfEmpty($validated['riwayat_imunisasi'] ?? null),
            'riwayat_asi_makan' => $this->nullIfEmpty($validated['riwayat_asi_makan'] ?? null),
            'keluhan_lain' => $this->nullIfEmpty($validated['keluhan_lain'] ?? null),
            'updated_by' => $userId,
            'updated_at' => now(),
        ];

        foreach ([
            'ada_diare',
            'bayi_mendapat_asi',
            'bayi_pernah_mendapat_asi',
            'ibu_dalam_art',
            'bayi_profilaksis_arv',
        ] as $column) {
            $this->putSubjectiveColumn($payload, $validated, $column, 'boolean');
        }

        foreach ([
            'berhenti_asi_minggu',
            'frekuensi_minum_24_jam',
            'jumlah_minum_per_kali_ml',
        ] as $column) {
            $this->putSubjectiveColumn($payload, $validated, $column, 'numeric');
        }

        foreach ([
            'status_hiv_ibu',
            'tes_virologis_bayi',
            'tes_serologis_bayi',
            'jenis_susu_pengganti',
            'tambahan_minuman_pengganti',
        ] as $column) {
            $this->putSubjectiveColumn($payload, $validated, $column, 'raw');
        }

        $exists = DB::table('mtbm_subjective')
            ->where('kunjungan_id', $kunjunganId)
            ->exists();

        if ($exists) {
            DB::table('mtbm_subjective')
                ->where('kunjungan_id', $kunjunganId)
                ->update($payload);
        } else {
            $payload['created_by'] = $userId;
            $payload['created_at'] = now();
            DB::table('mtbm_subjective')->insert($payload);
        }

        $row = DB::table('mtbm_subjective')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'message' => 'Subjektif MTBM berhasil disimpan',
            'data' => $row,
        ]);
    }

    public function showObjektif($kunjunganId)
    {
        $row = DB::table('mtbm_objective')
            ->where('kunjungan_id', (string) $kunjunganId)
            ->first();

        return response()->json([
            'message' => 'OK',
            'data' => $row,
        ]);
    }
    public function storeObjektif(Request $request)
    {
        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:36'],

            // Field lama yang sudah digunakan form saat ini.
            'kesadaran' => ['nullable', Rule::in(['sadar', 'letargi', 'tidak_sadar'])],
            'kejang_saat_ini' => ['nullable', 'boolean'],
            'tarikan_dinding_dada_umum' => ['nullable', 'boolean'],
            'stridor' => ['nullable', 'boolean'],
            'sianosis' => ['nullable', 'boolean'],
            'nadi_status' => ['nullable', Rule::in(['normal', 'cepat', 'lemah'])],
            'spo2' => ['nullable', 'integer', 'min:0', 'max:100'],
            'rr' => ['nullable', 'integer', 'min:0', 'max:300'],
            'rr_ulang' => ['nullable', 'integer', 'min:0', 'max:300'],
            'tarikan_dinding_dada' => ['nullable', Rule::in(['ya', 'tidak'])],
            'wheezing' => ['nullable', Rule::in(['ya', 'tidak'])],
            'mata_cekung' => ['nullable', Rule::in(['ya', 'tidak'])],
            'haus_minum_lahap' => ['nullable', Rule::in(['ya', 'tidak'])],
            'turgor_kulit' => ['nullable', Rule::in(['normal', 'lambat', 'sangat_lambat'])],
            'suhu' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'kaku_kuduk' => ['nullable', Rule::in(['ya', 'tidak'])],
            'ruam_campak' => ['nullable', Rule::in(['ya', 'tidak'])],
            'dengue_perdarahan' => ['nullable', 'boolean'],
            'dengue_nyeri_perut' => ['nullable', 'boolean'],
            'dengue_muntah_terus' => ['nullable', 'boolean'],
            'bb' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'tb_pb' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lila' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'edema' => ['nullable', Rule::in(['ya', 'tidak'])],
            'status_bb_tb' => ['nullable', Rule::in(['normal', 'kurang', 'buruk', 'obesitas'])],
            'nyeri_tekan_belakang_telinga' => ['nullable', Rule::in(['ya', 'tidak'])],
            'nanah_keluar_telinga' => ['nullable', Rule::in(['ya', 'tidak'])],
            'lama_nanah_hari' => ['nullable', 'integer', 'min:0', 'max:365'],
            'tampak_pucat' => ['nullable', Rule::in(['ya', 'tidak'])],
            'hb' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'statusRingkas' => ['nullable', 'string', 'max:50'],

            // Penyakit sangat berat/infeksi bakteri berat/lokal, halaman 40.
            'biru_sekitar_mulut' => ['nullable', 'boolean'],
            'spo2_tangan_kanan' => ['nullable', 'integer', 'min:0', 'max:100'],
            'spo2_kaki_kiri' => ['nullable', 'integer', 'min:0', 'max:100'],
            'merintih' => ['nullable', 'boolean'],
            'napas_cuping_hidung' => ['nullable', 'boolean'],
            'tarikan_dinding_dada_sangat_kuat' => ['nullable', 'boolean'],
            'lemah_tidak_mau_mengisap' => ['nullable', 'boolean'],
            'tidak_bab_48_jam' => ['nullable', 'boolean'],
            'muntah_susu_atau_hijau' => ['nullable', 'boolean'],
            'muntah_hijau' => ['nullable', 'boolean'],
            'perut_kembung_sulit_bernapas' => ['nullable', 'boolean'],
            'tidak_ada_lubang_anus' => ['nullable', 'boolean'],
            'feses_lubang_abnormal' => ['nullable', 'boolean'],
            'mata_bernanah_banyak' => ['nullable', 'boolean'],
            'mata_bernanah_sedikit' => ['nullable', 'boolean'],
            'pusar_bernanah' => ['nullable', 'boolean'],
            'pusar_kemerahan' => ['nullable', 'boolean'],
            'pusar_kemerahan_meluas' => ['nullable', 'boolean'],
            'pustul_kulit' => ['nullable', 'boolean'],

            // Diare bayi muda, halaman 42.
            'bergerak_hanya_dirangsang' => ['nullable', 'boolean'],
            'tidak_bergerak' => ['nullable', 'boolean'],
            'gelisah_rewel' => ['nullable', 'boolean'],

            // Ikterus, halaman 41.
            'ikterus' => ['nullable', 'boolean'],
            'ikterus_telapak' => ['nullable', 'boolean'],
            'kuning' => ['nullable', Rule::in(['ya', 'tidak'])],
            'kuning_telapak' => ['nullable', Rule::in(['ya', 'tidak'])],
            'umur_mulai_kuning_jam' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'umur_mulai_kuning_hari' => ['nullable', 'integer', 'min:0', 'max:90'],

            // BB/U dan pemberian ASI/minum, halaman 44-45.
            'status_bb_u' => ['nullable', Rule::in(['normal', 'rendah', 'sangat_rendah'])],
            'zscore_bb_u' => ['nullable', 'numeric', 'min:-10', 'max:10'],
            'frekuensi_asi_24_jam' => ['nullable', 'integer', 'min:0', 'max:100'],
            'menggunakan_botol' => ['nullable', 'boolean'],
            'makanan_minuman_lain' => ['nullable', 'boolean'],
            'posisi_menyusu_salah' => ['nullable', 'boolean'],
            'perlekatan_tidak_baik' => ['nullable', 'boolean'],
            'mengisap_tidak_efektif' => ['nullable', 'boolean'],
            'thrush' => ['nullable', 'boolean'],
            'celah_bibir_langit' => ['nullable', 'boolean'],
            'minuman_pengganti_tidak_sesuai' => ['nullable', 'boolean'],
            'jumlah_minuman_tidak_adekuat' => ['nullable', 'boolean'],
            'penyiapan_minuman_tidak_higienis' => ['nullable', 'boolean'],
        ]);

        if (!$cek['ok']) return $cek['response'];

        $validated = $cek['data'];
        $kunjunganId = (string) $validated['kunjungan_id'];
        $userId = Auth::id();

        $payload = [
            'kunjungan_id' => $kunjunganId,
            'kesadaran' => $this->nullIfEmpty($validated['kesadaran'] ?? null),
            'kejang_saat_ini' => (int) ((bool) ($validated['kejang_saat_ini'] ?? false)),
            'tarikan_dinding_dada_umum' => (int) ((bool) ($validated['tarikan_dinding_dada_umum'] ?? false)),
            'stridor' => (int) ((bool) ($validated['stridor'] ?? false)),
            'sianosis' => (int) ((bool) ($validated['sianosis'] ?? false)),
            'nadi_status' => $this->nullIfEmpty($validated['nadi_status'] ?? null),
            'spo2' => $validated['spo2'] ?? null,
            'rr' => $validated['rr'] ?? null,
            'tarikan_dinding_dada' => $this->nullIfEmpty($validated['tarikan_dinding_dada'] ?? null),
            'wheezing' => $this->nullIfEmpty($validated['wheezing'] ?? null),
            'mata_cekung' => $this->nullIfEmpty($validated['mata_cekung'] ?? null),
            'haus_minum_lahap' => $this->nullIfEmpty($validated['haus_minum_lahap'] ?? null),
            'turgor_kulit' => $this->nullIfEmpty($validated['turgor_kulit'] ?? null),
            'suhu' => $validated['suhu'] ?? null,
            'kaku_kuduk' => $this->nullIfEmpty($validated['kaku_kuduk'] ?? null),
            'ruam_campak' => $this->nullIfEmpty($validated['ruam_campak'] ?? null),
            'dengue_perdarahan' => (int) ((bool) ($validated['dengue_perdarahan'] ?? false)),
            'dengue_nyeri_perut' => (int) ((bool) ($validated['dengue_nyeri_perut'] ?? false)),
            'dengue_muntah_terus' => (int) ((bool) ($validated['dengue_muntah_terus'] ?? false)),
            'bb' => $validated['bb'] ?? null,
            'tb_pb' => $validated['tb_pb'] ?? null,
            'lila' => $validated['lila'] ?? null,
            'edema' => $this->nullIfEmpty($validated['edema'] ?? null),
            'status_bb_tb' => $this->nullIfEmpty($validated['status_bb_tb'] ?? null),
            'nyeri_tekan_belakang_telinga' => $this->nullIfEmpty($validated['nyeri_tekan_belakang_telinga'] ?? null),
            'nanah_keluar_telinga' => $this->nullIfEmpty($validated['nanah_keluar_telinga'] ?? null),
            'lama_nanah_hari' => $validated['lama_nanah_hari'] ?? null,
            'tampak_pucat' => $this->nullIfEmpty($validated['tampak_pucat'] ?? null),
            'hb' => $validated['hb'] ?? null,
            'status_ringkas' => $this->nullIfEmpty($validated['statusRingkas'] ?? null),
            'updated_by' => $userId,
            'updated_at' => now(),
        ];

        $booleanColumns = [
            'biru_sekitar_mulut',
            'merintih',
            'napas_cuping_hidung',
            'tarikan_dinding_dada_sangat_kuat',
            'lemah_tidak_mau_mengisap',
            'tidak_bab_48_jam',
            'muntah_susu_atau_hijau',
            'muntah_hijau',
            'perut_kembung_sulit_bernapas',
            'tidak_ada_lubang_anus',
            'feses_lubang_abnormal',
            'mata_bernanah_banyak',
            'mata_bernanah_sedikit',
            'pusar_bernanah',
            'pusar_kemerahan',
            'pusar_kemerahan_meluas',
            'pustul_kulit',
            'bergerak_hanya_dirangsang',
            'tidak_bergerak',
            'gelisah_rewel',
            'ikterus',
            'ikterus_telapak',
            'menggunakan_botol',
            'makanan_minuman_lain',
            'posisi_menyusu_salah',
            'perlekatan_tidak_baik',
            'mengisap_tidak_efektif',
            'thrush',
            'celah_bibir_langit',
            'minuman_pengganti_tidak_sesuai',
            'jumlah_minuman_tidak_adekuat',
            'penyiapan_minuman_tidak_higienis',
        ];

        foreach ($booleanColumns as $column) {
            $this->putObjectiveColumn($payload, $validated, $column, 'boolean');
        }
/*
|--------------------------------------------------------------------------
| Sinkronisasi field Ikterus baru dengan kolom lama
|--------------------------------------------------------------------------
| Mencegah data lama kuning_telapak = 'ya' tetap memicu Ikterus berat
| ketika checkbox ikterus_telapak pada form baru sudah tidak dicentang.
*/

if (
    array_key_exists('ikterus', $validated)
    && Schema::hasColumn('mtbm_objective', 'kuning')
) {
    $payload['kuning'] = $this->boolValue($validated['ikterus'])
        ? 'ya'
        : null;
}

if (
    array_key_exists('ikterus_telapak', $validated)
    && Schema::hasColumn('mtbm_objective', 'kuning_telapak')
) {
    $payload['kuning_telapak'] = $this->boolValue($validated['ikterus_telapak'])
        ? 'ya'
        : null;
}

/*
 * Form sekarang memakai satuan jam.
 * Kosongkan nilai hari lama agar tidak ikut terbaca sebagai fallback.
 */
if (
    array_key_exists('umur_mulai_kuning_jam', $validated)
    && Schema::hasColumn('mtbm_objective', 'umur_mulai_kuning_hari')
) {
    $payload['umur_mulai_kuning_hari'] = null;
}
        foreach ([
            'rr_ulang',
            'spo2_tangan_kanan',
            'spo2_kaki_kiri',
            'umur_mulai_kuning_jam',
            'umur_mulai_kuning_hari',
            'frekuensi_asi_24_jam',
        ] as $column) {
            $this->putObjectiveColumn($payload, $validated, $column, 'integer');
        }

        $this->putObjectiveColumn($payload, $validated, 'zscore_bb_u', 'numeric');

        foreach (['kuning', 'kuning_telapak', 'status_bb_u'] as $column) {
            $this->putObjectiveColumn($payload, $validated, $column, 'raw');
        }

        $exists = DB::table('mtbm_objective')
            ->where('kunjungan_id', $kunjunganId)
            ->exists();

        if ($exists) {
            DB::table('mtbm_objective')
                ->where('kunjungan_id', $kunjunganId)
                ->update($payload);
        } else {
            $payload['created_by'] = $userId;
            $payload['created_at'] = now();
            DB::table('mtbm_objective')->insert($payload);
        }

        $row = DB::table('mtbm_objective')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'message' => 'Objektif MTBM berhasil disimpan',
            'data' => $row,
        ]);
    }

    public function getAssessment($kunjunganId)
    {
        $row = DB::table('mtbm_assessment')
            ->where('kunjungan_id', (string) $kunjunganId)
            ->orderByDesc('id')
            ->first();

        if (!$row) {
            return response()->json(['data' => null], 200);
        }

        return response()->json([
            'data' => $this->formatAssessmentMtbmResponse($row),
        ], 200);
    }
    public function storeAssessment(Request $request)
    {
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
            'pasien_id' => $request->pasien_id === '' ? null : $request->pasien_id,
        ]);

        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:100'],
            'pasien_id' => ['nullable', 'integer', 'min:1'],
            'assessment_mtbm' => ['nullable', 'array'],
            'assessment_mtbm.infeksi' => ['nullable', 'string', 'max:80'],
            'assessment_mtbm.ikterus' => ['nullable', 'string', 'max:80'],
            'assessment_mtbm.diare' => ['nullable', 'string', 'max:80'],
            'assessment_mtbm.hiv' => ['nullable', 'string', 'max:80'],
            'assessment_mtbm.menyusu_bb' => ['nullable', 'string', 'max:80'],
            'klasifikasi' => ['nullable', 'array'],
            'status_kegawatan' => ['nullable', 'string', 'max:80'],
            'catatan_assessment' => ['nullable', 'string'],
        ]);

        if (!$cek['ok']) return $cek['response'];

        try {
            $validated = $cek['data'];
            $assessment = $validated['assessment_mtbm'] ?? [];
            $userId = Auth::id();

            $statusKegawatan = $validated['status_kegawatan'] ?? 'Tidak gawat';
            $warnaGlobal = $this->warnaGlobalFromStatus($statusKegawatan);

            $payload = [
                'kunjungan_id' => (string) $validated['kunjungan_id'],
                'klas_infeksi' => $assessment['infeksi'] ?? null,
                'klas_ikterus' => $assessment['ikterus'] ?? null,
                'klas_diare' => $assessment['diare'] ?? null,
                'klas_menyusu_bb' => $assessment['menyusu_bb'] ?? null,
                'klasifikasi_global' => $warnaGlobal,
                'catatan_assessment' => $validated['catatan_assessment'] ?? null,
                'generated_from' => 'manual',
                'generated_at' => now(),
                'updated_by' => $userId,
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('mtbm_assessment', 'klas_hiv')) {
                $payload['klas_hiv'] = $assessment['hiv'] ?? null;
            }

            if (Schema::hasColumn('mtbm_assessment', 'pasien_id')) {
                $payload['pasien_id'] = $validated['pasien_id'] ?? null;
            }

            if (Schema::hasColumn('mtbm_assessment', 'status_kegawatan')) {
                $payload['status_kegawatan'] = $statusKegawatan;
            }

            $exists = DB::table('mtbm_assessment')
                ->where('kunjungan_id', (string) $validated['kunjungan_id'])
                ->exists();

            if ($exists) {
                DB::table('mtbm_assessment')
                    ->where('kunjungan_id', (string) $validated['kunjungan_id'])
                    ->update($payload);
            } else {
                $payload['created_by'] = $userId;
                $payload['created_at'] = now();
                DB::table('mtbm_assessment')->insert($payload);
            }

            $row = DB::table('mtbm_assessment')
                ->where('kunjungan_id', (string) $validated['kunjungan_id'])
                ->orderByDesc('id')
                ->first();

            return response()->json([
                'message' => 'Assessment MTBM berhasil disimpan',
                'data' => $this->formatAssessmentMtbmResponse($row),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBM storeAssessment error', [
                'msg' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan assessment MTBM',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function autoAssessment(Request $request)
    {
        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:100'],
        ]);

        if (!$cek['ok']) return $cek['response'];

        try {
            $validated = $cek['data'];
            $kunjunganId = (string) $validated['kunjungan_id'];
            $context = $this->getMtbmClinicalContext($kunjunganId);
            $sub = $context['subjective'];
            $obj = $context['objective'];

            // Jangan menghasilkan klasifikasi hijau ketika penilaian belum diisi.
            if (!$sub || !$obj) {
                return response()->json([
                    'message' => 'Subjektif dan objektif MTBM harus diisi sebelum generate assessment.',
                    'errors' => [
                        'subjective' => !$sub ? ['Subjektif MTBM belum diisi.'] : [],
                        'objective' => !$obj ? ['Objektif MTBM belum diisi.'] : [],
                    ],
                ], 422);
            }

            if ($context['umur_hari'] === null) {
                return response()->json([
                    'message' => 'Umur bayi tidak dapat dihitung dari tanggal lahir dan tanggal kunjungan.',
                ], 422);
            }

            // MTBM hanya untuk bayi muda umur 0-59 hari.
            if ((int) $context['umur_hari'] >= 60) {
                return response()->json([
                    'message' => 'Pasien berumur 2 bulan atau lebih. Gunakan pelayanan MTBS, bukan MTBM.',
                    'umur_hari' => (int) $context['umur_hari'],
                ], 422);
            }

            $hasil = $this->hitungKlasifikasiMtbm($sub, $obj);
            $userId = Auth::id();

            $payload = [
                'kunjungan_id' => $kunjunganId,
                'klas_infeksi' => $hasil['infeksi'],
                'klas_ikterus' => $hasil['ikterus'],
                'klas_diare' => $hasil['diare'],
                'klas_menyusu_bb' => $hasil['menyusu_bb'],
                'klasifikasi_global' => $hasil['warna_global'],
                'catatan_assessment' => null,
                'generated_from' => 'auto',
                'generated_at' => now(),
                'updated_by' => $userId,
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('mtbm_assessment', 'klas_hiv')) {
                $payload['klas_hiv'] = $hasil['hiv'];
            }

            if (Schema::hasColumn('mtbm_assessment', 'pasien_id')) {
                $payload['pasien_id'] = $hasil['pasien_id'];
            }

            if (Schema::hasColumn('mtbm_assessment', 'status_kegawatan')) {
                $payload['status_kegawatan'] = $hasil['status_kegawatan'];
            }

            $exists = DB::table('mtbm_assessment')
                ->where('kunjungan_id', $kunjunganId)
                ->exists();

            if ($exists) {
                DB::table('mtbm_assessment')
                    ->where('kunjungan_id', $kunjunganId)
                    ->update($payload);
            } else {
                $payload['created_by'] = $userId;
                $payload['created_at'] = now();
                DB::table('mtbm_assessment')->insert($payload);
            }

            $row = DB::table('mtbm_assessment')
                ->where('kunjungan_id', $kunjunganId)
                ->orderByDesc('id')
                ->first();

            return response()->json([
                'message' => 'Assessment MTBM berhasil digenerate otomatis.',
                'data' => $this->formatAssessmentMtbmResponse($row),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBM autoAssessment error', [
                'msg' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal generate Assessment MTBM',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function hitungKlasifikasiMtbm($sub, $obj): array
    {
        $kunjunganId = (string) ($sub->kunjungan_id ?? $obj->kunjungan_id ?? '');
        $context = $this->getMtbmClinicalContext($kunjunganId);

        $pasienId = null;
        if ($kunjunganId !== '') {
            $pasien = DB::table('simpus_pelayanan as pel')
                ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
                ->where('pel.idpelayanan', $kunjunganId)
                ->select('l.pasienId')
                ->first();

            $pasienId = $pasien->pasienId ?? null;
        }

        $umurHari = $context['umur_hari'];
        $bbKg = $context['bb_kg'];

        $sectionPresence = $this->mtbmAssessmentSectionPresence(
            $sub,
            $obj,
            $umurHari !== null ? (int) $umurHari : null,
            $bbKg !== null ? (float) $bbKg : null
        );

        /*
        |--------------------------------------------------------------------------
        | 1. PENYAKIT SANGAT BERAT / INFEKSI BAKTERI BERAT / LOKAL
        | Buku Bagan MTBS 2022 - Bayi Muda Kurang dari 2 Bulan, halaman 40.
        |--------------------------------------------------------------------------
        */
        $rrPertama = $this->firstAvailableValue($obj, ['rr'], null);
        $rrUlang = $this->firstAvailableValue($obj, ['rr_ulang'], null);
        $rrDinilai = $rrUlang !== null && is_numeric($rrUlang) && (int) $rrUlang > 0
            ? (int) $rrUlang
            : ($rrPertama !== null && is_numeric($rrPertama) ? (int) $rrPertama : null);

        $rrBahaya = $rrDinilai !== null
            && $rrDinilai > 0
            && ($rrDinilai >= 60 || $rrDinilai < 40);

        $spo2Kanan = $this->firstAvailableValue($obj, ['spo2_tangan_kanan', 'spo2'], null);
        $spo2Kaki = $this->firstAvailableValue($obj, ['spo2_kaki_kiri'], null);

        $spo2Rendah = false;
        foreach ([$spo2Kanan, $spo2Kaki] as $nilaiSpo2) {
            if ($nilaiSpo2 !== null && is_numeric($nilaiSpo2)) {
                $nilaiSpo2 = (int) $nilaiSpo2;
                if ($nilaiSpo2 > 0 && $nilaiSpo2 < 95) {
                    $spo2Rendah = true;
                }
            }
        }

        $selisihSpo2LebihTiga = $spo2Kanan !== null
            && $spo2Kaki !== null
            && is_numeric($spo2Kanan)
            && is_numeric($spo2Kaki)
            && abs((int) $spo2Kanan - (int) $spo2Kaki) > 3;

        $suhu = $this->firstAvailableValue($obj, ['suhu'], null);
        $suhuBahaya = $suhu !== null
            && is_numeric($suhu)
            && ((float) $suhu > 37.5 || (float) $suhu < 36.5);

        // Buku mensyaratkan tarikan dinding dada yang SANGAT KUAT.
        $tarikanSangatKuat = $this->anyTrueValue($obj, [
            'tarikan_dinding_dada_sangat_kuat',
        ]);

        // Jangan mengganti tanda ini dengan "tidak bisa minum" milik MTBS balita.
        $lemahTidakMengisap = $this->anyTrueValue($obj, [
            'lemah_tidak_mau_mengisap',
        ]);

        $infeksiMerah =
            $this->anyTrueValue($obj, ['biru_sekitar_mulut', 'sianosis'])
            || $spo2Rendah
            || $selisihSpo2LebihTiga
            || $rrBahaya
            || $this->anyTrueValue($obj, ['merintih'])
            || $this->anyTrueValue($obj, ['napas_cuping_hidung'])
            || $tarikanSangatKuat
            || $lemahTidakMengisap
            || $this->boolValue($obj->kejang_saat_ini ?? false)
            || $this->boolValue($sub->kejang ?? false)
            || $suhuBahaya
            || $this->anyTrueValue($obj, ['tidak_bab_48_jam'])
            || $this->anyTrueValue($obj, ['muntah_susu_atau_hijau', 'muntah_hijau'])
            || $this->anyTrueValue($obj, ['perut_kembung_sulit_bernapas'])
            || $this->anyTrueValue($obj, ['tidak_ada_lubang_anus', 'feses_lubang_abnormal'])
            || $this->anyTrueValue($obj, ['mata_bernanah_banyak'])
            || $this->anyTrueValue($obj, ['pusar_bernanah'])
            || $this->anyTrueValue($obj, ['pusar_kemerahan_meluas']);

        $infeksiKuning = !$infeksiMerah && $this->anyTrueValue($obj, [
            'mata_bernanah_sedikit',
            'pusar_kemerahan',
            'pustul_kulit',
        ]);

        $infeksi = null;
        if ($sectionPresence['infeksi']) {
            $infeksi = $infeksiMerah
                ? 'penyakit_sangat_berat_infeksi_berat'
                : ($infeksiKuning ? 'infeksi_bakteri_lokal' : 'mungkin_bukan_infeksi');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. DIARE BAYI MUDA - halaman 42.
        |--------------------------------------------------------------------------
        */
        $adaDiareRaw = $this->firstAvailableValue($sub, ['ada_diare'], null);
        $adaDiare = $adaDiareRaw !== null
            ? $this->boolValue($adaDiareRaw)
            : (($sub->diare_lama_hari ?? null) !== null && (int) $sub->diare_lama_hari > 0);

        $punyaFieldGerak = property_exists($obj, 'bergerak_hanya_dirangsang')
            || property_exists($obj, 'tidak_bergerak');

        $letargi = $this->anyTrueValue($obj, [
            'bergerak_hanya_dirangsang',
            'tidak_bergerak',
        ]);

        // Kompatibilitas data lama sebelum field gerakan bayi tersedia.
        if (!$punyaFieldGerak) {
            $letargi = in_array($obj->kesadaran ?? null, ['letargi', 'tidak_sadar'], true);
        }

        $gelisahRewel = $this->anyTrueValue($obj, ['gelisah_rewel']);
        $mataCekung = ($obj->mata_cekung ?? null) === 'ya';
        $turgor = $obj->turgor_kulit ?? null;

        $tandaDehidrasiBerat = 0;
        if ($letargi) $tandaDehidrasiBerat++;
        if ($mataCekung) $tandaDehidrasiBerat++;
        if ($turgor === 'sangat_lambat') $tandaDehidrasiBerat++;

        $tandaDehidrasiRingan = 0;
        if ($gelisahRewel) $tandaDehidrasiRingan++;
        if ($mataCekung) $tandaDehidrasiRingan++;
        if ($turgor === 'lambat') $tandaDehidrasiRingan++;

        $diareMerah = $adaDiare && $tandaDehidrasiBerat >= 2;
        $diareKuning = $adaDiare && !$diareMerah && $tandaDehidrasiRingan >= 2;

        $diare = null;
        if ($sectionPresence['diare']) {
            $diare = $diareMerah
                ? 'diare_dehidrasi_berat'
                : ($diareKuning
                    ? 'diare_dehidrasi_ringan_sedang'
                    : 'diare_tanpa_dehidrasi');
        }

        /*
        |--------------------------------------------------------------------------
      /*
|--------------------------------------------------------------------------
| 3. IKTERUS - halaman 41.
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| 3. IKTERUS - halaman 41.
|--------------------------------------------------------------------------
*/

// Prioritaskan field baru.
// Field lama hanya digunakan sebagai fallback jika field baru tidak tersedia.
$adaKuningRaw = $this->firstAvailableValue(
    $obj,
    ['ikterus', 'kuning'],
    null
);

$adaKuning = $adaKuningRaw !== null
    && (
        $this->boolValue($adaKuningRaw)
        || $adaKuningRaw === 'ya'
    );

$kuningTelapakRaw = $this->firstAvailableValue(
    $obj,
    ['ikterus_telapak', 'kuning_telapak'],
    null
);

$kuningTelapak = $kuningTelapakRaw !== null
    && (
        $this->boolValue($kuningTelapakRaw)
        || $kuningTelapakRaw === 'ya'
    );

// Ambil umur bayi saat pertama kali mulai tampak kuning dalam satuan jam.
$umurMulaiKuningJam = $this->firstAvailableValue(
    $obj,
    ['umur_mulai_kuning_jam'],
    null
);

// Kompatibilitas data lama yang masih menyimpan umur mulai kuning dalam hari.
if (
    ($umurMulaiKuningJam === null || $umurMulaiKuningJam === '')
    && property_exists($obj, 'umur_mulai_kuning_hari')
    && $obj->umur_mulai_kuning_hari !== null
    && $obj->umur_mulai_kuning_hari !== ''
    && is_numeric($obj->umur_mulai_kuning_hari)
) {
    $umurMulaiKuningJam = (int) $obj->umur_mulai_kuning_hari * 24;
}

// Ikterus berat jika bayi mulai kuning sebelum berumur 24 jam.
// Tepat 24 jam sudah tidak masuk kondisi ini.
$mulaiKuningSebelum24Jam = $umurMulaiKuningJam !== null
    && $umurMulaiKuningJam !== ''
    && is_numeric($umurMulaiKuningJam)
    && (int) $umurMulaiKuningJam < 24;

// Ikterus berat jika bayi masih tampak kuning setelah berumur 14 hari.
$masihKuningLebih14Hari = $adaKuning
    && $umurHari !== null
    && (int) $umurHari > 14;

$ikterus = null;

if ($sectionPresence['ikterus']) {
    if (!$adaKuning) {
        $ikterus = 'tidak_ikterus';
    } elseif (
        $mulaiKuningSebelum24Jam
        || $kuningTelapak
        || $masihKuningLebih14Hari
    ) {
        $ikterus = 'ikterus_berat';
    } else {
        /*
         * Ikterus biasa:
         * - Mulai kuning pada umur 24 jam atau lebih.
         * - Umur bayi saat diperiksa maksimal 14 hari.
         * - Kuning tidak mencapai telapak tangan atau kaki.
         */
        $ikterus = 'ikterus';
    }
}

        /*
        |--------------------------------------------------------------------------
        | 4. PENILAIAN HIV BAYI MUDA - halaman 43.
        | Nilai null berarti form HIV belum diterapkan/diisi.
        |--------------------------------------------------------------------------
        */
        $statusHivIbu = $this->firstAvailableValue($sub, ['status_hiv_ibu'], null);
        $tesVirologisBayi = $this->firstAvailableValue($sub, ['tes_virologis_bayi'], null);
        $tesSerologisBayi = $this->firstAvailableValue($sub, ['tes_serologis_bayi'], null);
        $bayiMendapatAsiRaw = $this->firstAvailableValue($sub, ['bayi_mendapat_asi'], null);
        $bayiPernahAsiRaw = $this->firstAvailableValue($sub, ['bayi_pernah_mendapat_asi'], null);
        $berhentiAsiMinggu = $this->firstAvailableValue($sub, ['berhenti_asi_minggu'], null);

        $adaDataHiv = $sectionPresence['hiv'];

        $hiv = null;
        if ($adaDataHiv) {
            $bayiMendapatAsi = $bayiMendapatAsiRaw !== null
                ? $this->boolValue($bayiMendapatAsiRaw)
                : false;
            $bayiPernahAsi = $bayiPernahAsiRaw !== null
                ? $this->boolValue($bayiPernahAsiRaw)
                : false;
            $berhentiKurangEnamMinggu = $berhentiAsiMinggu !== null
                && is_numeric($berhentiAsiMinggu)
                && (int) $berhentiAsiMinggu < 6;

            // Riwayat pernah ASI tetap disimpan untuk dokumentasi. Nilai
            // berhenti ASI < 6 minggu sudah cukup sebagai tanda pajanan.
            $bayiPernahAsi = $bayiPernahAsi || $berhentiKurangEnamMinggu;

            if ($tesVirologisBayi === 'positif') {
                $hiv = 'infeksi_hiv_terkonfirmasi';
            } elseif (
                $tesSerologisBayi === 'positif'
                || (
                    $statusHivIbu === 'positif'
                    && (
                        $bayiMendapatAsi
                        || ($tesVirologisBayi === 'negatif' && $berhentiKurangEnamMinggu)
                        || in_array($tesVirologisBayi, [null, '', 'belum_tes'], true)
                    )
                )
            ) {
                $hiv = 'terpajan_hiv_mungkin_infeksi';
            } elseif (
                $statusHivIbu === 'belum_tes'
                || $tesVirologisBayi === 'belum_tes'
                || ($statusHivIbu === null && $tesVirologisBayi === null)
            ) {
                $hiv = 'infeksi_hiv_tidak_diketahui';
            } elseif ($statusHivIbu === 'negatif' || $tesVirologisBayi === 'negatif') {
                $hiv = 'bukan_infeksi_hiv';
            } else {
                $hiv = 'infeksi_hiv_tidak_diketahui';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. BERAT BADAN MENURUT UMUR / MASALAH PEMBERIAN ASI ATAU MINUM
        | Halaman 44-45.
        |--------------------------------------------------------------------------
        */
        $beratSangatRendah = $umurHari !== null
            && $umurHari < 7
            && $bbKg !== null
            && $bbKg < 2;

        $zscoreBbU = $this->firstAvailableValue($obj, ['zscore_bb_u'], null);
        $statusBbU = $this->firstAvailableValue($obj, ['status_bb_u'], null);

        // Jangan memakai BB/TB sebagai pengganti BB/U.
        $beratRendah = ($zscoreBbU !== null && is_numeric($zscoreBbU) && (float) $zscoreBbU < -2)
            || in_array($statusBbU, ['rendah', 'sangat_rendah'], true);

        $frekuensiAsi = $this->firstAvailableValue($obj, ['frekuensi_asi_24_jam'], null);
        $asiKurangDelapanKali = $frekuensiAsi !== null
            && is_numeric($frekuensiAsi)
            && (int) $frekuensiAsi < 8;

        $masalahPemberianAsi = $asiKurangDelapanKali
            || $this->anyTrueValue($obj, [
                'menggunakan_botol',
                'makanan_minuman_lain',
                'posisi_menyusu_salah',
                'perlekatan_tidak_baik',
                'mengisap_tidak_efektif',
                'thrush',
                'celah_bibir_langit',
            ]);

        $ibuHivPositif = $statusHivIbu === 'positif';
        $bayiMendapatAsi = $bayiMendapatAsiRaw !== null
            ? $this->boolValue($bayiMendapatAsiRaw)
            : true;
        $jalurPemberianMinum = $ibuHivPositif && !$bayiMendapatAsi;

        $masalahPemberianMinum = $this->anyTrueValue($obj, [
            'minuman_pengganti_tidak_sesuai',
            'jumlah_minuman_tidak_adekuat',
            'penyiapan_minuman_tidak_higienis',
            'menggunakan_botol',
            'thrush',
            'celah_bibir_langit',
        ]);

        $menyusuMerah = $beratSangatRendah;
        $menyusuKuning = !$menyusuMerah && (
            $beratRendah
            || ($jalurPemberianMinum ? $masalahPemberianMinum : $masalahPemberianAsi)
        );

        $menyusuBb = null;
        if ($sectionPresence['menyusu_bb']) {
            if ($menyusuMerah) {
                $menyusuBb = 'bb_sangat_rendah_menurut_umur';
            } elseif ($jalurPemberianMinum) {
                $menyusuBb = $menyusuKuning
                    ? 'bb_rendah_masalah_pemberian_minum'
                    : 'bb_tidak_rendah_tidak_ada_masalah_minum';
            } else {
                $menyusuBb = $menyusuKuning
                    ? 'bb_rendah_masalah_pemberian_asi'
                    : 'bb_tidak_rendah_tidak_ada_masalah_asi';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. STATUS PRIORITAS + WARNA GLOBAL
        |--------------------------------------------------------------------------
        */
        $hivPerluTatalaksana = in_array($hiv, [
            'infeksi_hiv_terkonfirmasi',
            'terpajan_hiv_mungkin_infeksi',
            'infeksi_hiv_tidak_diketahui',
        ], true);

        $adaKlasifikasiAktif = collect([
            $infeksi,
            $diare,
            $ikterus,
            $hiv,
            $menyusuBb,
        ])->contains(fn ($value) => $value !== null && $value !== '');

        $statusKegawatan = $adaKlasifikasiAktif
            ? 'Tidak gawat'
            : 'Belum dinilai';

        if ($infeksiMerah || $diareMerah || $ikterus === 'ikterus_berat' || $menyusuMerah) {
            $statusKegawatan = 'Perlu rujukan segera';
        } elseif ($infeksiKuning || $diareKuning || $ikterus === 'ikterus' || $menyusuKuning || $hivPerluTatalaksana) {
            $statusKegawatan = 'Perlu tatalaksana / observasi';
        }

        $warnaGlobal = $adaKlasifikasiAktif
            ? $this->warnaGlobalFromStatus($statusKegawatan)
            : null;
        $klasifikasiGlobal = $this->buildKlasifikasiGlobalMtbm(
            $infeksi,
            $ikterus,
            $diare,
            $menyusuBb,
            $hiv
        );

        return [
            'pasien_id' => $pasienId,
            'infeksi' => $infeksi,
            'ikterus' => $ikterus,
            'diare' => $diare,
            'hiv' => $hiv,
            'menyusu_bb' => $menyusuBb,
            'warna_global' => $warnaGlobal,
            'klasifikasi_global' => $klasifikasiGlobal,
            'status_kegawatan' => $statusKegawatan,
        ];
    }
    private function warnaGlobalFromStatus($status): string
    {
        if (in_array($status, ['Perlu rujukan segera', 'Penyakit sangat berat'], true)) {
            return 'merah';
        }

        if ($status === 'Perlu tatalaksana / observasi') {
            return 'kuning';
        }

        return 'hijau';
    }
    private function statusFromWarnaGlobal($warna): string
    {
        if ($warna === 'merah') return 'Perlu rujukan segera';
        if ($warna === 'kuning') return 'Perlu tatalaksana / observasi';
        return 'Tidak gawat';
    }
    private function buildKlasifikasiGlobalMtbm(
        $infeksi,
        $ikterus,
        $diare,
        $menyusuBb,
        $hiv = null
    ): array {
        $global = [];

        if ($infeksi === 'penyakit_sangat_berat_infeksi_berat') {
            $global[] = 'Penyakit sangat berat / infeksi bakteri berat';
        } elseif ($infeksi === 'infeksi_bakteri_lokal') {
            $global[] = 'Infeksi bakteri lokal';
        } elseif (in_array($infeksi, ['tidak_ada_infeksi', 'mungkin_bukan_infeksi'], true)) {
            $global[] = 'Mungkin bukan infeksi';
        }

        if ($diare === 'diare_dehidrasi_berat') {
            $global[] = 'Diare dehidrasi berat';
        } elseif ($diare === 'diare_dehidrasi_ringan_sedang') {
            $global[] = 'Diare dehidrasi ringan/sedang';
        } elseif ($diare === 'diare_tanpa_dehidrasi') {
            $global[] = 'Diare tanpa dehidrasi';
        }

        if ($ikterus === 'ikterus_berat') {
            $global[] = 'Ikterus berat';
        } elseif ($ikterus === 'ikterus') {
            $global[] = 'Ikterus';
        } elseif ($ikterus === 'tidak_ikterus') {
            $global[] = 'Tidak ada ikterus';
        }

        if ($hiv === 'infeksi_hiv_terkonfirmasi') {
            $global[] = 'Infeksi HIV terkonfirmasi';
        } elseif ($hiv === 'terpajan_hiv_mungkin_infeksi') {
            $global[] = 'Terpajan HIV / mungkin infeksi HIV';
        } elseif ($hiv === 'infeksi_hiv_tidak_diketahui') {
            $global[] = 'Infeksi HIV tidak diketahui';
        } elseif ($hiv === 'bukan_infeksi_hiv') {
            $global[] = 'Bukan infeksi HIV';
        }

        if (in_array($menyusuBb, [
            'masalah_menyusu_berat_bb_sangat_rendah',
            'bb_sangat_rendah_menurut_umur',
        ], true)) {
            $global[] = 'Berat badan sangat rendah menurut umur';
        } elseif (in_array($menyusuBb, [
            'masalah_menyusu_bb_rendah',
            'bb_rendah_masalah_pemberian_asi',
        ], true)) {
            $global[] = 'Berat badan rendah menurut umur dan/atau masalah pemberian ASI';
        } elseif ($menyusuBb === 'bb_rendah_masalah_pemberian_minum') {
            $global[] = 'Berat badan rendah menurut umur dan/atau masalah pemberian minum';
        } elseif (in_array($menyusuBb, [
            'menyusu_baik',
            'bb_tidak_rendah_tidak_ada_masalah_asi',
        ], true)) {
            $global[] = 'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian ASI';
        } elseif ($menyusuBb === 'bb_tidak_rendah_tidak_ada_masalah_minum') {
            $global[] = 'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian minum';
        }

        return array_values(array_unique($global));
    }
    private function formatAssessmentMtbmResponse($row): array
    {
        $infeksi = $row->klas_infeksi ?? null;
        $ikterus = $row->klas_ikterus ?? null;
        $diare = $row->klas_diare ?? null;
        $hiv = $row->klas_hiv ?? null;
        $menyusuBb = $row->klas_menyusu_bb ?? null;
        $warnaGlobal = $row->klasifikasi_global ?? null;

        // Bersihkan juga hasil lama yang sebelumnya sudah tersimpan sebagai
        // klasifikasi hijau default, tetapi bagian tersebut sebenarnya tidak diisi.
        $kunjunganId = trim((string) ($row->kunjungan_id ?? ''));
        if ($kunjunganId !== '') {
            $context = $this->getMtbmClinicalContext($kunjunganId);
            $sub = $context['subjective'];
            $obj = $context['objective'];

            if ($sub && $obj) {
                $presence = $this->mtbmAssessmentSectionPresence(
                    $sub,
                    $obj,
                    $context['umur_hari'] !== null ? (int) $context['umur_hari'] : null,
                    $context['bb_kg'] !== null ? (float) $context['bb_kg'] : null
                );

                if (!$presence['infeksi']) $infeksi = null;
                if (!$presence['ikterus']) $ikterus = null;
                if (!$presence['diare']) $diare = null;
                if (!$presence['hiv']) $hiv = null;
                if (!$presence['menyusu_bb']) $menyusuBb = null;
            }
        }

        $adaKlasifikasiAktif = collect([
            $infeksi,
            $ikterus,
            $diare,
            $hiv,
            $menyusuBb,
        ])->contains(fn ($value) => $value !== null && $value !== '');

        $status = !empty($row->status_kegawatan)
            ? $row->status_kegawatan
            : ($adaKlasifikasiAktif
                ? $this->statusFromWarnaGlobal($warnaGlobal)
                : 'Belum dinilai');

        if (!$adaKlasifikasiAktif) {
            $status = 'Belum dinilai';
            $warnaGlobal = null;
        }

        // Normalisasi data lama agar label merah tidak selalu bernama
        // "Penyakit sangat berat", karena merah juga dapat berasal dari
        // ikterus berat, dehidrasi berat, atau BB sangat rendah.
        if ($status === 'Penyakit sangat berat') {
            $status = 'Perlu rujukan segera';
        }

        return [
            'pasien_id' => $row->pasien_id ?? null,
            'infeksi' => $infeksi,
            'ikterus' => $ikterus,
            'diare' => $diare,
            'hiv' => $hiv,
            'menyusu_bb' => $menyusuBb,
            'klasifikasi_global' => $this->buildKlasifikasiGlobalMtbm(
                $infeksi,
                $ikterus,
                $diare,
                $menyusuBb,
                $hiv
            ),
            'warna_global' => $warnaGlobal,
            'status_kegawatan' => $status,
            'catatan_assessment' => $row->catatan_assessment ?? null,
            'generated_from' => $row->generated_from ?? null,
            'generated_at' => $row->generated_at ?? null,
        ];
    }

    /**
     * Rekomendasi Planning MTBM berdasarkan hasil klasifikasi Assessment.
     * Format respons dipisah supaya Vue dapat menampilkan tab Tindakan dan
     * Pengobatan tanpa menebak jenis rekomendasi dari kata kunci:
     * [
     *   ['klasifikasi' => '...', 'tindakan' => [...], 'pengobatan' => [...]],
     * ]
     */
    public function rekomendasiPlanning($kunjunganId)
    {
        $kunjunganId = trim((string) $kunjunganId);

        if ($kunjunganId === '') {
            return response()->json([
                'message' => 'kunjungan_id tidak boleh kosong',
                'data' => [],
            ], 422);
        }

        try {
            $assessment = DB::table('mtbm_assessment')
                ->where('kunjungan_id', $kunjunganId)
                ->orderByDesc('id')
                ->first();

            if (!$assessment) {
                return response()->json([
                    'message' => 'Assessment MTBM belum ada',
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'message' => 'OK',
                'data' => $this->buildRekomendasiPlanningMtbm($assessment),
                'assessment' => $this->formatAssessmentMtbmResponse($assessment),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBM rekomendasiPlanning error', [
                'kunjungan_id' => $kunjunganId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal mengambil rekomendasi Planning MTBM',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Menampilkan Planning MTBM yang sudah disimpan.
     * Bentuk respons disamakan dengan Vue Planning MTBS.
     */
    public function getPlanning($kunjunganId)
    {
        $kunjunganId = trim((string) $kunjunganId);

        if ($kunjunganId === '') {
            return response()->json([
                'message' => 'kunjungan_id tidak boleh kosong',
                'data' => null,
            ], 422);
        }

        try {
            $row = DB::table('mtbm_planning')
                ->where('kunjungan_id', $kunjunganId)
                ->orderByDesc('id')
                ->first();

            if (!$row) {
                return response()->json([
                    'message' => 'Planning MTBM belum ada',
                    'data' => null,
                ], 200);
            }

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

            $kunjunganUlang = null;
            if (isset($row->kontrol_ulang) && $row->kontrol_ulang !== '' && is_numeric($row->kontrol_ulang)) {
                $kunjunganUlang = (int) $row->kontrol_ulang;
            }

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'tindakanSegera' => $decodeArray($row->tindakan_items ?? null),
                    'pengobatan' => $decodeArray($row->resep_items ?? null),
                    'edukasi' => $decodeArray($row->konseling_edukasi ?? null),
                    'catatanEdukasi' => $row->catatan_planning ?? '',
                    'kunjunganUlang' => $kunjunganUlang,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBM getPlanning error', [
                'kunjungan_id' => $kunjunganId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal mengambil Planning MTBM',
                'error' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Menyimpan Planning MTBM dengan payload yang sama seperti Planning MTBS.
     */
    public function storePlanning(Request $request)
    {
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === ''
                ? null
                : $request->kunjungan_id,
            'kunjunganUlang' => $request->kunjunganUlang === ''
                ? null
                : $request->kunjunganUlang,
        ]);

        $cek = $this->validasi($request, [
            'kunjungan_id' => ['required', 'string', 'max:100'],

            'tindakanSegera' => ['nullable', 'array'],
            'tindakanSegera.*' => ['array'],
            'tindakanSegera.*.id' => ['nullable'],
            'tindakanSegera.*.kode' => ['required', 'string', 'max:100'],
            'tindakanSegera.*.nama' => ['required', 'string', 'max:500'],
            'tindakanSegera.*.nama_ind' => ['nullable', 'string', 'max:500'],
            'tindakanSegera.*.harga' => ['nullable'],
            'tindakanSegera.*.bayar' => ['nullable'],
            'tindakanSegera.*.keterangan' => ['nullable', 'string'],
            'tindakanSegera.*.poli' => ['nullable', 'string', 'max:100'],

            'pengobatan' => ['nullable', 'array'],
            'pengobatan.*.obat_id' => ['nullable'],
            'pengobatan.*.kode_obat' => ['nullable', 'string', 'max:100'],
            'pengobatan.*.nama' => ['required', 'string', 'max:255'],
            'pengobatan.*.satuan' => ['nullable', 'string', 'max:100'],
            'pengobatan.*.dosis' => ['nullable', 'string', 'max:255'],
            'pengobatan.*.cara' => [
                'nullable',
                Rule::in(['oral', 'suntik', 'infus']),
            ],
            'pengobatan.*.lama' => ['nullable', 'integer', 'min:0', 'max:365'],

            'edukasi' => ['nullable', 'array'],
            'edukasi.*' => ['string', 'max:255'],

            'catatanEdukasi' => ['nullable', 'string'],
            'kunjunganUlang' => [
                'nullable',
                'integer',
                Rule::in([2, 3, 5, 7, 14]),
            ],
        ]);

        if (!$cek['ok']) {
            return $cek['response'];
        }

        $validated = $cek['data'];
        $kunjunganId = (string) $validated['kunjungan_id'];
        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $assessment = DB::table('mtbm_assessment')
                ->where('kunjungan_id', $kunjunganId)
                ->orderByDesc('id')
                ->first();

            $warnaGlobal = $assessment->klasifikasi_global ?? null;
            $statusKegawatan = $assessment->status_kegawatan
                ?? $this->statusFromWarnaGlobal($warnaGlobal);

            $keputusan = $warnaGlobal === 'merah'
                ? 'rujuk'
                : 'rawat_jalan';

            $rekomendasiSnapshot = $assessment
                ? $this->buildRekomendasiPlanningMtbm($assessment)
                : [];

            $payload = [
                'kunjungan_id' => $kunjunganId,
                'klasifikasi_global' => $warnaGlobal,
                'keputusan' => $keputusan,
                'tindakan_items' => json_encode(
                    $validated['tindakanSegera'] ?? [],
                    JSON_UNESCAPED_UNICODE
                ),
                'resep_items' => json_encode(
                    $validated['pengobatan'] ?? [],
                    JSON_UNESCAPED_UNICODE
                ),
                'rekomendasi_obat' => json_encode(
                    $rekomendasiSnapshot,
                    JSON_UNESCAPED_UNICODE
                ),
                'catatan_planning' => $validated['catatanEdukasi'] ?? null,
                'konseling_edukasi' => json_encode(
                    $validated['edukasi'] ?? [],
                    JSON_UNESCAPED_UNICODE
                ),
                'kontrol_ulang' => $validated['kunjunganUlang'] ?? null,
                'rujuk_alasan' => $warnaGlobal === 'merah'
                    ? $statusKegawatan
                    : null,
                'updated_by' => $userId,
                'updated_at' => now(),
            ];

            $exists = DB::table('mtbm_planning')
                ->where('kunjungan_id', $kunjunganId)
                ->exists();

            if ($exists) {
                DB::table('mtbm_planning')
                    ->where('kunjungan_id', $kunjunganId)
                    ->update($payload);
            } else {
                $payload['created_by'] = $userId;
                $payload['created_at'] = now();

                DB::table('mtbm_planning')->insert($payload);
            }

            $updatedPelayanan = DB::table('simpus_pelayanan')
                ->where('idpelayanan', $kunjunganId)
                ->update([
                    'sudahDilayani' => 1,
                    'tglPelayanan' => now(),
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Planning MTBM berhasil disimpan',
                'updatedPelayanan' => $updatedPelayanan,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MTBM storePlanning error', [
                'kunjungan_id' => $kunjunganId,
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan Planning MTBM',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

public function cariDiagnosaMedis(Request $request)
{
    $q = trim($request->q ?? '');
    $limit = (int) ($request->limit ?? 10);
    $limit = $limit > 0 ? min($limit, 10) : 10;

    $data = DB::table('simpus_diagnosa')
        ->select(
            'id',
            'kdDiag',
            'nmDiag',
            'kunjSehat',
            'klb',
            'klb_kategori',
            'kategori_penyakit'
        )
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('kdDiag', 'like', '%' . $q . '%')
                    ->orWhere('nmDiag', 'like', '%' . $q . '%');
            });
        })
        ->orderBy('nmDiag', 'asc')
        ->limit($limit)
        ->get();

    return response()->json([
        'data' => $data,
    ], 200);
}

public function storeDiagnosaMedis(Request $request)
{
    $cek = $this->validasi($request, [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'pasien_id' => ['nullable', 'integer'],
        'diagnosa_id' => ['required', 'integer'],
        'kodeDiagnosa' => ['required', 'string', 'max:255'],
        'namaDiagnosa' => ['required', 'string', 'max:255'],
        'keterangan' => ['nullable', 'string'],
        'kasus' => ['required', Rule::in(['baru', 'lama'])],
        'poli' => ['nullable', 'string', 'max:100'],
    ]);

    if (!$cek['ok']) return $cek['response'];

    if (!Schema::hasTable('mtbm_diagnosa_medis')) {
        return response()->json([
            'message' => 'Tabel mtbm_diagnosa_medis belum ada',
        ], 500);
    }

    try {
        $validated = $cek['data'];
        $user = Auth::user();
        $createdBy = $user ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas') : 'Petugas';

        DB::table('mtbm_diagnosa_medis')->insert([
            'kunjungan_id' => $validated['kunjungan_id'],
            'pasien_id' => $validated['pasien_id'] ?? null,
            'diagnosa_id' => $validated['diagnosa_id'],
            'kdDiag' => $validated['kodeDiagnosa'],
            'nmDiag' => $validated['namaDiagnosa'],
            'kasus' => $validated['kasus'],
            'keterangan' => $validated['keterangan'] ?? null,
            'poli' => $validated['poli'] ?? null,
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Diagnosa medis MTBM berhasil disimpan',
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBM storeDiagnosaMedis error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan diagnosa medis MTBM',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function showDiagnosaMedis($kunjunganId)
{
    if (!Schema::hasTable('mtbm_diagnosa_medis')) {
        return response()->json([
            'message' => 'Tabel mtbm_diagnosa_medis belum ada',
            'data' => [],
        ], 200);
    }

    try {
        $data = DB::table('mtbm_diagnosa_medis')
            ->where('kunjungan_id', (string) $kunjunganId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'diagnosaId' => $row->diagnosa_id,
                    'kodeDiagnosa' => $row->kdDiag,
                    'namaDiagnosa' => $row->nmDiag,
                    'keterangan' => $row->keterangan,
                    'kasus' => $row->kasus,
                    'poli' => $row->poli,
                ];
            });

        return response()->json([
            'data' => $data,
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBM showDiagnosaMedis error', [
            'msg' => $e->getMessage(),
            'kunjungan_id' => $kunjunganId,
        ]);

        return response()->json([
            'message' => 'Gagal mengambil diagnosa medis MTBM',
            'error' => $e->getMessage(),
            'data' => [],
        ], 500);
    }
}

public function deleteDiagnosaMedis($id)
{
    if (!Schema::hasTable('mtbm_diagnosa_medis')) {
        return response()->json([
            'message' => 'Tabel mtbm_diagnosa_medis belum ada',
        ], 500);
    }

    DB::table('mtbm_diagnosa_medis')->where('id', $id)->delete();

    return response()->json([
        'message' => 'Diagnosa medis MTBM berhasil dihapus',
    ], 200);
}
    /**
     * Pencarian master tindakan ICD-9-CM untuk Planning MTBM.
     * Sumber data: simpus_master_tindakan.
     */
    public function cariTindakan(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        try {
            $rows = DB::table('simpus_master_tindakan')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('kode', 'like', '%' . $q . '%')
                            ->orWhere('nama_tindakan', 'like', '%' . $q . '%')
                            ->orWhere('nama_tindakan_indonesia', 'like', '%' . $q . '%')
                            ->orWhere('deskripsi', 'like', '%' . $q . '%');
                    });
                })
                ->orderBy('kode')
                ->limit(10)
                ->get();

            return response()->json([
                'message' => 'OK',
                'data' => $rows->map(function ($row) {
                    return [
                        'id' => $row->id ?? null,
                        'kode' => $row->kode ?? '',
                        'nama' => $row->nama_tindakan ?? '',
                        'nama_ind' => $row->nama_tindakan_indonesia ?? '',
                        'harga' => $row->harga ?? null,
                        'bayar' => $row->simTarif ?? null,
                        'keterangan' => $row->deskripsi ?? '',
                        'peraturan' => $row->nilai_normal ?? '',
                        'poli' => 'MTBM',
                    ];
                })->values(),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBM cariTindakan error', [
                'query' => $q,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal mencari master tindakan MTBM',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Pencarian master obat untuk Planning MTBM.
     */
    public function getObatMtbm(Request $request)
    {
        $q = trim((string) ($request->q ?? ''));

        $data = DB::table('simpus_master_obat')
            ->select(
                'OBAT_ID as obat_id',
                'KODE_OBAT as kode_obat',
                'NAMA as nama',
                'SATUAN as satuan'
            )
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('NAMA', 'like', '%' . $q . '%')
                        ->orWhere('KODE_OBAT', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('NAMA')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * Rule rekomendasi khusus MTBM.
     * Teks dibuat sebagai bantuan klinis; pemilihan obat, dosis, dan keputusan
     * final tetap dilakukan dokter/petugas sesuai SOP yang berlaku.
     */
    private function buildRekomendasiPlanningMtbm($assessment): array
    {
        $rekomendasi = [];

        $normalisasi = static function (array $items): array {
            return array_values(array_unique(array_filter(
                array_map(static fn ($item) => trim((string) $item), $items),
                static fn ($item) => $item !== ''
            )));
        };

        $tambah = static function (
            string $klasifikasi,
            array $tindakan = [],
            array $pengobatan = []
        ) use (&$rekomendasi, $normalisasi): void {
            $tindakan = $normalisasi($tindakan);
            $pengobatan = $normalisasi($pengobatan);

            if ($tindakan === [] && $pengobatan === []) {
                return;
            }

            $rekomendasi[] = [
                'klasifikasi' => $klasifikasi,
                'tindakan' => $tindakan,
                'pengobatan' => $pengobatan,
            ];
        };

        $infeksi = $assessment->klas_infeksi ?? null;
        $ikterus = $assessment->klas_ikterus ?? null;
        $diare = $assessment->klas_diare ?? null;
        $hiv = $assessment->klas_hiv ?? null;
        $menyusuBb = $assessment->klas_menyusu_bb ?? null;
        $kunjunganId = (string) ($assessment->kunjungan_id ?? '');

        $context = $this->getMtbmClinicalContext($kunjunganId);
        $obj = $context['objective'];
        $sub = $context['subjective'];
        $umurHari = $context['umur_hari'];

        /* 1. INFEKSI - halaman 40 dan tindakan pra-rujukan halaman 46. */
        if ($infeksi === 'penyakit_sangat_berat_infeksi_berat') {
            $gentamisin = $umurHari !== null
                ? ($umurHari < 7
                    ? 'Gentamisin IM dosis pertama 5 mg/kgBB karena umur bayi kurang dari 7 hari'
                    : 'Gentamisin IM dosis pertama 7,5 mg/kgBB karena umur bayi 7 hari atau lebih')
                : 'Gentamisin IM dosis pertama sesuai umur dan berat badan';

            $tambah(
                'Penyakit sangat berat / infeksi bakteri berat',
                [
                    'Pastikan jalan napas bebas, bayi memiliki usaha bernapas, dan sirkulasi terjaga',
                    'Jika bayi kejang, lakukan penanganan kejang sesuai pedoman',
                    'Jika ada tanda sumbatan saluran cerna, lakukan dekompresi dengan pipa orogastrik ujung terbuka',
                    'Jaga tubuh bayi tetap hangat',
                    'Jika ada gangguan napas, berikan oksigen 1 L/menit melalui kateter nasal atau nasal prongs',
                    'Lakukan komunikasi dengan orang tua dan fasilitas rujukan',
                    'Rujuk segera setelah bayi stabil',
                ],
                [
                    'Infus Dekstrosa 10% sebanyak 60 ml/kgBB/24 jam sesuai kondisi klinis dan kewenangan petugas',
                    'Ampisilin IM dosis pertama 50 mg/kgBB',
                    $gentamisin,
                    'Obat anti kejang sesuai pedoman bila bayi kejang',
                ]
            );
        } elseif ($infeksi === 'infeksi_bakteri_lokal') {
            $tindakan = [
                'Ajari ibu cara mengobati infeksi bakteri lokal di rumah',
                'Lakukan asuhan dasar bayi muda',
                'Kunjungan ulang 2 hari',
                'Nasihati kapan harus kembali segera',
            ];
            $pengobatan = [];

            if ($obj && $this->anyTrueValue($obj, ['mata_bernanah_sedikit'])) {
                $pengobatan[] = 'Salep mata antibiotik untuk mata bernanah sesuai prosedur';
            }

            if ($obj && $this->anyTrueValue($obj, ['pusar_kemerahan'])) {
                $pengobatan[] = 'Antiseptik untuk pusar yang kemerahan';
            }

            if ($obj && $this->anyTrueValue($obj, ['pustul_kulit'])) {
                $pengobatan[] = 'Antiseptik untuk pustul kulit';
            }

            if ($pengobatan === []) {
                $pengobatan = [
                    'Salep mata antibiotik bila mata bernanah sedikit',
                    'Antiseptik bila pusar kemerahan atau terdapat pustul kulit',
                ];
            }

            $tambah('Infeksi bakteri lokal', $tindakan, $pengobatan);
        } elseif (in_array($infeksi, ['tidak_ada_infeksi', 'mungkin_bukan_infeksi'], true)) {
            $tambah(
                'Mungkin bukan infeksi',
                [
                    'Lakukan asuhan dasar bayi muda',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        }

        /* 2. DIARE - halaman 42. */
        if ($diare === 'diare_dehidrasi_berat') {
            $tambah(
                'Diare dehidrasi berat',
                [
                    'Jika tidak terdapat klasifikasi berat lain, tangani sesuai Rencana Terapi C',
                    'Jika terdapat klasifikasi berat lain, rujuk segera setelah memenuhi syarat rujukan',
                    'ASI tetap diberikan jika memungkinkan',
                    'Jaga bayi tetap hangat selama perjalanan',
                ],
                [
                    'Berikan oralit sedikit demi sedikit selama perjalanan bila bayi mampu minum',
                ]
            );
        } elseif ($diare === 'diare_dehidrasi_ringan_sedang') {
            $tambah(
                'Diare dehidrasi ringan/sedang',
                [
                    'Jika tidak terdapat klasifikasi berat lain, tangani sesuai Rencana Terapi B',
                    'Jika terdapat klasifikasi berat lain, rujuk segera setelah memenuhi syarat rujukan',
                    'ASI tetap diberikan jika memungkinkan',
                    'Lakukan asuhan dasar bayi muda',
                    'Kunjungan ulang 1 hari',
                    'Nasihati kapan harus kembali segera',
                ],
                [
                    'Berikan larutan oralit sedikit demi sedikit sesuai Rencana Terapi B atau selama perjalanan rujukan',
                ]
            );
        } elseif ($diare === 'diare_tanpa_dehidrasi') {
            $tambah(
                'Diare tanpa dehidrasi',
                [
                    'Tangani sesuai Rencana Terapi A',
                    'Lakukan asuhan dasar bayi muda',
                    'Kunjungan ulang 1 hari',
                    'Nasihati kapan harus kembali segera',
                ],
                [
                    'Berikan oralit sesuai Rencana Terapi A',
                ]
            );
        }

        /* 3. IKTERUS - halaman 41. */
        if ($ikterus === 'ikterus_berat') {
            $tambah(
                'Ikterus berat',
                [
                    'Pertahankan asupan ASI agar bayi tidak kekurangan cairan',
                    'Jaga tubuh bayi tetap hangat',
                    'Rujuk segera',
                ]
            );
        } elseif ($ikterus === 'ikterus') {
            $tambah(
                'Ikterus',
                [
                    'Lakukan asuhan dasar bayi muda',
                    'Anjurkan bayi menyusu lebih sering',
                    'Jika memungkinkan, rujuk untuk penentuan kadar bilirubin dan tata laksana yang sesuai',
                    'Nasihati untuk menginformasikan hasil pemeriksaan bilirubin',
                    'Kunjungan ulang 1 hari',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        }

        /* 4. HIV BAYI MUDA - halaman 43. */
        if ($hiv === 'infeksi_hiv_terkonfirmasi') {
            $tambah(
                'Infeksi HIV terkonfirmasi',
                [
                    'Rujuk untuk terapi antiretroviral dan perawatan HIV',
                    'Rujuk atau mulai terapi antiretroviral pada ibu jika belum pengobatan',
                    'Edukasi ibu untuk perawatan di rumah',
                    'Tindak lanjut sesuai pedoman nasional',
                ],
                [
                    'Profilaksis kotrimoksazol mulai umur 6 minggu',
                ]
            );
        } elseif ($hiv === 'terpajan_hiv_mungkin_infeksi') {
            $tambah(
                'Terpajan HIV / mungkin infeksi HIV',
                [
                    'Lakukan tes virologis pada bayi',
                    'Mulai terapi antiretroviral pada ibu jika belum pengobatan atau rujuk',
                    'Edukasi ibu untuk perawatan di rumah',
                    'Tindak lanjut rutin sesuai pedoman nasional',
                    'Nasihati kapan harus kembali segera',
                ],
                [
                    'Profilaksis kotrimoksazol mulai umur 6 minggu',
                    'Mulai profilaksis antiretroviral bila umur kurang dari 72 jam atau lanjutkan berdasarkan penilaian risiko',
                ]
            );
        } elseif ($hiv === 'infeksi_hiv_tidak_diketahui') {
            $tambah(
                'Infeksi HIV tidak diketahui',
                [
                    'Inisiasi tes HIV dan konseling',
                    'Lakukan tes HIV pada ibu; jika positif, lakukan tes virologis pada bayi',
                    'Lakukan tes virologis pada bayi jika ibu tidak ada',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        } elseif ($hiv === 'bukan_infeksi_hiv') {
            $tambah(
                'Bukan infeksi HIV',
                [
                    'Obati dan tindak lanjut jika ada infeksi lain',
                    'Edukasi ibu tentang asupan makanan dan kesehatan ibu',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        }

        /* 5. BB/U DAN PEMBERIAN ASI/MINUM - halaman 44-45. */
        if (in_array($menyusuBb, [
            'masalah_menyusu_berat_bb_sangat_rendah',
            'bb_sangat_rendah_menurut_umur',
        ], true)) {
            $tambah(
                'Berat badan sangat rendah menurut umur',
                [
                    'Rujuk ke rumah sakit dengan Metode Kanguru',
                    'Cegah gula darah tidak turun',
                    'Nasihati cara menjaga bayi tetap hangat selama perjalanan',
                ]
            );
        } elseif (in_array($menyusuBb, [
            'masalah_menyusu_bb_rendah',
            'bb_rendah_masalah_pemberian_asi',
        ], true)) {
            $tindakan = ['Lakukan asuhan dasar bayi muda'];
            $pengobatan = [];

            $frekuensiAsi = $obj
                ? $this->firstAvailableValue($obj, ['frekuensi_asi_24_jam'], null)
                : null;

            if ($frekuensiAsi !== null && is_numeric($frekuensiAsi) && (int) $frekuensiAsi < 8) {
                $tindakan[] = 'Nasihati ibu untuk menyusui lebih sering sesuai keinginan bayi, siang dan malam';
            }

            if ($obj && $this->anyTrueValue($obj, ['menggunakan_botol'])) {
                $tindakan[] = 'Ajari ibu menggunakan cangkir dan hentikan penggunaan botol';
            }

            if ($obj && $this->anyTrueValue($obj, ['makanan_minuman_lain'])) {
                $tindakan[] = 'Nasihati ibu untuk relaktasi';

                $statusHivIbu = $sub
                    ? $this->firstAvailableValue($sub, ['status_hiv_ibu'], null)
                    : null;
                if ($statusHivIbu === 'positif') {
                    $tindakan[] = 'Rujuk ke bagian gizi karena ibu HIV positif mencampur ASI dengan makanan/minuman lain';
                }
            }

            if ($obj && $this->anyTrueValue($obj, [
                'posisi_menyusu_salah',
                'perlekatan_tidak_baik',
                'mengisap_tidak_efektif',
            ])) {
                $tindakan[] = 'Ajari ibu memperbaiki posisi, perlekatan, dan cara bayi mengisap';
            }

            if ($obj && $this->anyTrueValue($obj, ['thrush'])) {
                $pengobatan[] = 'Suspensi nistatin untuk bercak putih atau thrush sesuai pedoman';
            }

            if ($obj && $this->anyTrueValue($obj, ['celah_bibir_langit'])) {
                $tindakan[] = 'Nasihati alternatif pemberian minum untuk celah bibir atau langit-langit';
            }

            $tindakan[] = 'Kunjungan ulang 2 hari untuk masalah pemberian ASI atau thrush';
            $tindakan[] = 'Kunjungan ulang 7 hari untuk berat badan rendah menurut umur';
            $tindakan[] = 'Nasihati kapan harus kembali segera';

            $tambah(
                'Berat badan rendah menurut umur dan/atau masalah pemberian ASI',
                $tindakan,
                $pengobatan
            );
        } elseif ($menyusuBb === 'bb_rendah_masalah_pemberian_minum') {
            $tindakan = [
                'Ajarkan ibu memberikan minum dengan benar',
                'Jelaskan tata cara pemberian minuman pengganti yang aman',
                'Identifikasi masalah pada ibu dan keluarga mengenai pemberian minum',
                'Kunjungan ulang 2 hari untuk masalah pemberian minum atau thrush',
                'Kunjungan ulang 7 hari untuk berat badan rendah menurut umur',
                'Nasihati kapan harus kembali segera',
            ];
            $pengobatan = [];

            if ($obj && $this->anyTrueValue($obj, ['menggunakan_botol'])) {
                $tindakan[] = 'Ajarkan penggunaan cangkir dan hentikan penggunaan botol';
            }

            if ($obj && $this->anyTrueValue($obj, ['thrush'])) {
                $pengobatan[] = 'Suspensi nistatin untuk bercak putih atau thrush sesuai pedoman';
            }

            if ($obj && $this->anyTrueValue($obj, ['celah_bibir_langit'])) {
                $tindakan[] = 'Nasihati alternatif pemberian minum untuk celah bibir atau langit-langit';
            }

            $tambah(
                'Berat badan rendah menurut umur dan/atau masalah pemberian minum',
                $tindakan,
                $pengobatan
            );
        } elseif (in_array($menyusuBb, [
            'menyusu_baik',
            'bb_tidak_rendah_tidak_ada_masalah_asi',
        ], true)) {
            $tambah(
                'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian ASI',
                [
                    'Pujilah ibu karena telah memberikan ASI kepada bayinya dengan benar',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        } elseif ($menyusuBb === 'bb_tidak_rendah_tidak_ada_masalah_minum') {
            $tambah(
                'Berat badan tidak rendah menurut umur dan tidak ada masalah pemberian minum',
                [
                    'Nasihati ibu melanjutkan pemberian minum dan memastikan higiene yang baik',
                    'Pujilah ibu karena telah memberikan minum kepada bayi dengan benar',
                    'Nasihati kapan harus kembali segera',
                ]
            );
        }

        if ($rekomendasi === []) {
            $tambah(
                'Planning MTBM',
                [
                    'Belum ada rekomendasi otomatis',
                    'Pastikan Assessment MTBM sudah digenerate dan disimpan',
                ]
            );
        }

        return $rekomendasi;
    }



    /**
     * Menghapus data inti MTBM untuk kebutuhan pengujian pada kunjungan yang sama.
     *
     * Data pasien, loket, dan pelayanan tidak dihapus sehingga tester dapat
     * mengisi ulang Subjektif dan Objektif tanpa mendaftarkan pasien kembali.
     */
    public function hapusDataTesting(string $kunjunganId)
    {
        $kunjunganId = trim($kunjunganId);

        if ($kunjunganId === '') {
            return response()->json([
                'message' => 'ID kunjungan wajib diisi.',
            ], 422);
        }

        $tables = [
            'assessment' => 'mtbm_assessment',
            'gizi' => 'mtbm_gizi',
            'objektif' => 'mtbm_objective',
            'subjektif' => 'mtbm_subjective',
        ];

        DB::beginTransaction();

        try {
            $deleted = [];
            $skipped = [];

            // Hapus data turunan lebih dahulu, kemudian data sumber.
            foreach ($tables as $label => $table) {
                if (!Schema::hasTable($table)) {
                    $deleted[$label] = 0;
                    $skipped[$label] = "Tabel {$table} tidak tersedia";
                    continue;
                }

                if (!Schema::hasColumn($table, 'kunjungan_id')) {
                    $deleted[$label] = 0;
                    $skipped[$label] = "Kolom kunjungan_id tidak tersedia pada {$table}";
                    continue;
                }

                $deleted[$label] = DB::table($table)
                    ->where('kunjungan_id', $kunjunganId)
                    ->delete();
            }

            DB::commit();

            Log::warning('Data inti MTBM dihapus untuk testing', [
                'kunjungan_id' => $kunjunganId,
                'deleted' => $deleted,
                'skipped' => $skipped,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Data Subjektif, Objektif, Assessment, dan Gizi MTBM berhasil direset.',
                'data' => [
                    'kunjungan_id' => $kunjunganId,
                    'deleted' => $deleted,
                    'skipped' => $skipped,
                    'total_deleted' => array_sum($deleted),
                ],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MTBM hapusDataTesting error', [
                'kunjungan_id' => $kunjunganId,
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Gagal mereset data testing MTBM.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

}
