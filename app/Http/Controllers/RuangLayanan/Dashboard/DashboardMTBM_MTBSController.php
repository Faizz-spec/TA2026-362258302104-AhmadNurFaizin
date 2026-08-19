<?php

namespace App\Http\Controllers\RuangLayanan\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardMTBM_MTBSController extends Controller
{
    private string $COLL = 'utf8mb4_general_ci';

    /**
     * Kode poli bawaan MTBS/MTBM pada SIMPUS.
     *
     * Ketika filter poli dikosongkan, dashboard tetap memasukkan kunjungan
     * poli ini serta kunjungan dari poli lain yang sudah memiliki data
     * MTBS/MTBM.
     */
    private string $DEFAULT_KD_POLI = '003';


    /**
     * Daftar puskesmas untuk dropdown dashboard.
     * Nilai filter memakai unit_profiles.unit_id karena simpus_loket.puskId
     * menyimpan ID unit puskesmas.
     */
    private function getPuskesmasOptions(): array
    {
        if (
            !Schema::hasTable('unit_profiles')
            || !Schema::hasColumn('unit_profiles', 'unit_id')
            || !Schema::hasColumn('unit_profiles', 'nama_unit')
        ) {
            return [];
        }

        return DB::table('unit_profiles as up')
            ->select([
                'up.unit_id',
                'up.nama_unit',
                'up.kode_puskesmas',
            ])
            ->whereNotNull('up.unit_id')
            ->whereNotNull('up.nama_unit')
            ->whereRaw("TRIM(up.nama_unit) <> ''")
            ->orderBy('up.nama_unit')
            ->get()
            ->map(static function ($row) {
                return [
                    'unit_id' => (int) $row->unit_id,
                    'nama_unit' => (string) $row->nama_unit,
                    'kode_puskesmas' => $row->kode_puskesmas,
                    // Alias standar agar Vue tetap mudah dinormalisasi.
                    'value' => (string) $row->unit_id,
                    'label' => (string) $row->nama_unit,
                ];
            })
            ->values()
            ->all();
    }

    // =========================
    // INDEX (inertia page + dropdown puskesmas)
    // =========================
    public function index(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'kdPoli',
            'puskId',
            'keyword',
            'per_page',
            'served',
        ]);

        // Konsistenkan tipe value select di Vue sebagai string.
        $filters['puskId'] = isset($filters['puskId'])
            ? trim((string) $filters['puskId'])
            : '';
        $filters['kdPoli'] = isset($filters['kdPoli'])
            ? trim((string) $filters['kdPoli'])
            : '';
        $filters['served'] = isset($filters['served'])
            ? trim((string) $filters['served'])
            : 'all';

        return Inertia::render('Dashboard/MTBM_MTBS/DashboardMTBM_MTBS', [
            'filters' => $filters,
            'puskesmas' => $this->getPuskesmasOptions(),
        ]);
    }

    // =========================
    // INFO: table/column availability (biar query gak 500)
    // =========================
    private function info(): array
    {
        $info = [];

        $info['loket_has_pusk_id'] = Schema::hasTable('simpus_loket') && Schema::hasColumn('simpus_loket', 'puskId');
        $info['has_unit_profiles'] = Schema::hasTable('unit_profiles')
            && Schema::hasColumn('unit_profiles', 'unit_id')
            && Schema::hasColumn('unit_profiles', 'nama_unit')
            && $info['loket_has_pusk_id'];

        $pairs = [
            'mtbm_s' => ['mtbm_subjective', 'keluhan_utama'],
            'mtbm_o' => ['mtbm_objective', 'rr'],
            'mtbm_a' => ['mtbm_assessment', 'klasifikasi_global'],
            'mtbm_pl' => ['mtbm_planning', 'id'],

            'mtbs_s' => ['mtbs_subjektif', 'id'],
            'mtbs_o' => ['mtbs_objektif', 'rr'],
            'mtbs_a' => ['mtbs_assessment', 'status_kegawatan'],
            'mtbs_pl' => ['mtbs_planning', 'id'],
        ];

        foreach ($pairs as $alias => [$table, $sampleCol]) {
            $info["has_$alias"] = Schema::hasTable($table);
        }

        // column existence checks (yang dipakai dashboard)
        $info['mtbm_has_keluhan_utama'] = Schema::hasTable('mtbm_subjective') && Schema::hasColumn('mtbm_subjective', 'keluhan_utama');

        $info['mtbm_has_klas_infeksi'] = Schema::hasTable('mtbm_assessment') && Schema::hasColumn('mtbm_assessment', 'klas_infeksi');
        $info['mtbm_has_klas_diare'] = Schema::hasTable('mtbm_assessment') && Schema::hasColumn('mtbm_assessment', 'klas_diare');
        $info['mtbm_has_klas_menyusu_bb'] = Schema::hasTable('mtbm_assessment') && Schema::hasColumn('mtbm_assessment', 'klas_menyusu_bb');
        $info['mtbm_has_klas_global'] = Schema::hasTable('mtbm_assessment') && Schema::hasColumn('mtbm_assessment', 'klasifikasi_global');

        $info['mtbs_has_status_kegawatan'] = Schema::hasTable('mtbs_assessment') && Schema::hasColumn('mtbs_assessment', 'status_kegawatan');
        $info['mtbs_has_klasifikasi_global'] = Schema::hasTable('mtbs_assessment') && Schema::hasColumn('mtbs_assessment', 'klasifikasi_global');

        $info['loket_has_umur'] = Schema::hasTable('simpus_loket')
            && Schema::hasColumn('simpus_loket', 'umur');

        $info['loket_has_tgl_kunjungan'] = Schema::hasTable('simpus_loket')
            && Schema::hasColumn('simpus_loket', 'tglKunjungan');

        $info['pelayanan_has_tgl_pelayanan'] = Schema::hasTable('simpus_pelayanan')
            && Schema::hasColumn('simpus_pelayanan', 'tglPelayanan');

        $info['pasien_has_tgl_lahir'] = Schema::hasTable('simpus_pasien')
            && Schema::hasColumn('simpus_pasien', 'TGL_LHR');

        $info['has_mtbs_status'] = Schema::hasTable('mtbs_statuspasien')
            && Schema::hasColumn('mtbs_statuspasien', 'kunjungan_id')
            && Schema::hasColumn('mtbs_statuspasien', 'status_pulang');

        $info['has_mtbm_status'] = Schema::hasTable('mtbm_statuspasien')
            && Schema::hasColumn('mtbm_statuspasien', 'kunjungan_id')
            && Schema::hasColumn('mtbm_statuspasien', 'status_pulang');

        $info['has_mtbs_rujukan'] = Schema::hasTable('mtbs_rujukan')
            && Schema::hasColumn('mtbs_rujukan', 'kunjungan_id');

        return $info;
    }

    // =========================
    // Helper: deteksi gender col
    // =========================
    private function detectGenderColumn(): ?string
    {
        $candidates = ['JENIS_KLMIN', 'JENIS_KELAMIN', 'jenis_kelamin', 'jk', 'JK', 'GENDER', 'gender'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('simpus_pasien', $col)) return $col;
        }
        return null;
    }

    // =========================
    // BASE QUERY: join semua (yang ada)
    // =========================
    /**
     * Join satu baris terbaru dari tabel modul berdasarkan kunjungan_id.
     * Ini mencegah satu kunjungan berlipat ketika tabel lama memiliki beberapa row.
     */
    private function leftJoinLatestByKunjungan(
        $query,
        string $table,
        string $alias
    ): void {
        $COLL = $this->COLL;
        $latestAlias = "{$alias}_latest";

        $latest = DB::table($table)
            ->select([
                'kunjungan_id',
                DB::raw('MAX(id) as latest_id'),
            ])
            ->whereNotNull('kunjungan_id')
            ->groupBy('kunjungan_id');

        $query->leftJoinSub(
            $latest,
            $latestAlias,
            function ($join) use ($latestAlias, $COLL) {
                $join->on(
                    DB::raw("{$latestAlias}.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
            }
        );

        $query->leftJoin(
            "{$table} as {$alias}",
            "{$alias}.id",
            '=',
            "{$latestAlias}.latest_id"
        );
    }

    // =========================
    // BASE QUERY: satu row per kunjungan
    // =========================
    private function baseQuery(array $info)
    {
        $COLL = $this->COLL;

        $q = DB::table('simpus_pelayanan as pel')
            ->join(
                'simpus_loket as l',
                DB::raw("pel.loketId COLLATE {$COLL}"),
                '=',
                DB::raw("l.idLoket COLLATE {$COLL}")
            )
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli');

        if ($info['has_unit_profiles']) {
            $q->leftJoin('unit_profiles as up', 'up.unit_id', '=', 'l.puskId');
        }

        $tableMap = [
            'has_mtbm_s' => ['mtbm_subjective', 'mtbm_s'],
            'has_mtbm_o' => ['mtbm_objective', 'mtbm_o'],
            'has_mtbm_a' => ['mtbm_assessment', 'mtbm_a'],
            'has_mtbm_pl' => ['mtbm_planning', 'mtbm_pl'],

            'has_mtbs_s' => ['mtbs_subjektif', 'mtbs_s'],
            'has_mtbs_o' => ['mtbs_objektif', 'mtbs_o'],
            'has_mtbs_a' => ['mtbs_assessment', 'mtbs_a'],
            'has_mtbs_pl' => ['mtbs_planning', 'mtbs_pl'],
        ];

        foreach ($tableMap as $flag => [$table, $alias]) {
            if ($info[$flag] ?? false) {
                $this->leftJoinLatestByKunjungan($q, $table, $alias);
            }
        }

        if ($info['has_mtbs_status']) {
            $this->leftJoinLatestByKunjungan(
                $q,
                'mtbs_statuspasien',
                'mtbs_st'
            );
        }

        if ($info['has_mtbm_status']) {
            $this->leftJoinLatestByKunjungan(
                $q,
                'mtbm_statuspasien',
                'mtbm_st'
            );
        }

        if ($info['has_mtbs_rujukan']) {
            $this->leftJoinLatestByKunjungan(
                $q,
                'mtbs_rujukan',
                'mtbs_rj'
            );
        }

        return $q;
    }

    // =========================
    // build SQL condition for "form filled"
    // ex: (mtbs_s.kunjungan_id IS NOT NULL OR mtbs_a.kunjungan_id IS NOT NULL ...)
    // =========================
    private function buildFilledCondition(array $aliases): string
    {
        $parts = [];
        foreach ($aliases as $a) {
            $parts[] = "{$a}.kunjungan_id IS NOT NULL";
        }
        if (!count($parts)) return "0=1";
        return '(' . implode(' OR ', $parts) . ')';
    }

    /**
     * Ekspresi tanggal dashboard.
     *
     * Harus mengikuti sumber data yang dipakai MTBSController::index(), yaitu
     * simpus_loket.tglKunjungan. Jangan memakai COALESCE dengan tglPelayanan,
     * karena tglPelayanan dapat berisi waktu mulai/selesai layanan yang berbeda
     * dan akhirnya membuat kunjungan hari ini tidak masuk filter.
     */
    private function dashboardDateExpression(array $info): string
    {
        if ($info['loket_has_tgl_kunjungan']) {
            return 'l.tglKunjungan';
        }

        // Fallback hanya untuk instalasi lama yang tidak punya tglKunjungan.
        if ($info['pelayanan_has_tgl_pelayanan']) {
            return 'pel.tglPelayanan';
        }

        throw new \RuntimeException(
            'Kolom tanggal dashboard tidak ditemukan pada simpus_loket maupun simpus_pelayanan.'
        );
    }

    /**
     * Semua alias tabel MTBS/MTBM yang tersedia.
     */
    private function allMtbsmAliases(array $info): array
    {
        $aliases = [];

        foreach ([
            'mtbm_s',
            'mtbm_o',
            'mtbm_a',
            'mtbm_pl',
            'mtbs_s',
            'mtbs_o',
            'mtbs_a',
            'mtbs_pl',
        ] as $alias) {
            if ($info["has_{$alias}"] ?? false) {
                $aliases[] = $alias;
            }
        }

        return $aliases;
    }

    /**
     * Normalisasi kode poli agar input 3, 03, dan 003 dibaca sebagai 003.
     */
    private function normalizeKdPoli(string $value): string
    {
        $value = trim($value);

        if ($value === '' || strtolower($value) === 'all') {
            return '';
        }

        if (ctype_digit($value)) {
            return str_pad((string) ((int) $value), 3, '0', STR_PAD_LEFT);
        }

        return $value;
    }

    /**
     * Terapkan semua filter dashboard.
     *
     * Bila kdPoli diisi, query mengikuti poli tersebut.
     * Bila kdPoli kosong, scope otomatis adalah:
     * - poli bawaan 003; ATAU
     * - kunjungan dari poli lain yang sudah mempunyai data MTBS/MTBM.
     *
     * Dengan cara ini pasien yang belum mengisi form tetapi sudah masuk poli
     * MTBS/MTBM tetap dihitung, tanpa ikut mengambil seluruh kunjungan poli lain.
     */
    private function applyFilters($q, Request $request, array $info): array
    {
        $kdPoli = $this->normalizeKdPoli((string) $request->get('kdPoli', ''));
        $puskId = trim((string) $request->get('puskId', ''));
        $keyword = trim((string) $request->get('keyword', ''));
        $served = trim((string) $request->get('served', 'all'));

        $dateFrom = trim((string) $request->get('date_from', ''));
        $dateTo = trim((string) $request->get('date_to', ''));

        if ($dateFrom === '' || $dateTo === '') {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        }

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $dateExpression = $this->dashboardDateExpression($info);

        $q->whereRaw(
            "DATE({$dateExpression}) BETWEEN ? AND ?",
            [$dateFrom, $dateTo]
        );

        if ($kdPoli !== '' && strtolower($kdPoli) !== 'all') {
            $q->where('l.kdPoli', $kdPoli);
        } else {
            $aliases = $this->allMtbsmAliases($info);

            $q->where(function ($scope) use ($aliases) {
                $scope->where('l.kdPoli', $this->DEFAULT_KD_POLI);

                foreach ($aliases as $alias) {
                    $scope->orWhereNotNull("{$alias}.kunjungan_id");
                }
            });
        }

        if (
            $info['loket_has_pusk_id']
            && $puskId !== ''
            && strtolower($puskId) !== 'all'
        ) {
            $q->where('l.puskId', $puskId);
        }

        if ($keyword !== '') {
            $q->where(function ($search) use ($keyword) {
                $search->where('p.NO_MR', 'like', "%{$keyword}%")
                    ->orWhere('p.NAMA_LGKP', 'like', "%{$keyword}%")
                    ->orWhere('p.NIK', 'like', "%{$keyword}%");
            });
        }

        $servedCondition = $this->servedCondition($info);

        if ($served === 'served') {
            $q->whereRaw($servedCondition);
        } elseif ($served === 'unserved') {
            $q->whereRaw("NOT {$servedCondition}");
        }

        return [
            $dateFrom,
            $dateTo,
            $kdPoli,
            $puskId,
            $served,
            $keyword,
        ];
    }

    /**
     * Selesai pelayanan mengikuti sumber asli modul:
     * simpus_pelayanan.sudahDilayani atau sudah ada status pasien.
     */
    private function servedCondition(array $info): string
    {
        $parts = ['COALESCE(pel.sudahDilayani, 0) = 1'];

        if ($info['has_mtbs_status']) {
            $parts[] = 'mtbs_st.kunjungan_id IS NOT NULL';
        }

        if ($info['has_mtbm_status']) {
            $parts[] = 'mtbm_st.kunjungan_id IS NOT NULL';
        }

        return '(' . implode(' OR ', $parts) . ')';
    }

    // =========================
    // parse JSON list (MTBS klasifikasi global sering json array)
    // =========================
    private function parseJsonList($v): array
    {
        if ($v === null || $v === '') return [];
        if (is_array($v)) return $v;

        $s = trim((string)$v);
        if ($s === '') return [];

        try {
            $x = json_decode($s, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($x)) return $x;
        } catch (\Throwable $e) {}

        if (str_contains($s, ',')) {
            return array_values(array_filter(array_map('trim', explode(',', $s))));
        }

        return [$s];
    }

    private function mtbsSeverityBucket(?string $status): string
    {
        $s = strtolower(trim((string) $status));
        if ($s === '') return 'lain';

        if (
            str_contains($s, 'tidak perlu rujuk')
            || str_contains($s, 'tidak gawat')
            || str_contains($s, 'stabil')
        ) {
            return 'ringan';
        }

        if (
            str_contains($s, 'sangat berat')
            || str_contains($s, 'berat')
            || str_contains($s, 'rujuk')
            || str_contains($s, 'gawat')
            || str_contains($s, 'segera')
        ) {
            return 'berat';
        }

        if (str_contains($s, 'sedang')) return 'sedang';
        if (str_contains($s, 'ringan')) return 'ringan';

        return 'lain';
    }

    // =========================
    // DATA: dashboard payload lengkap
    // =========================
    public function data(Request $request)
    {
        try {
            $info = $this->info();
            $genderCol = $this->detectGenderColumn();
            $puskesmasOptions = $this->getPuskesmasOptions();

            // =========================
            // KPI (total, mtbs filled, mtbm filled, unserved, gender, avg umur)
            // =========================
            $qAgg = $this->baseQuery($info);
            [$dateFrom, $dateTo, $kdPoli, $puskId, $served, $keyword] = $this->applyFilters($qAgg, $request, $info);

            $total = (clone $qAgg)->distinct('pel.idpelayanan')->count('pel.idpelayanan');

            $servedCondition = $this->servedCondition($info);

            $unserved = (clone $qAgg)
                ->whereRaw("NOT {$servedCondition}")
                ->distinct('pel.idpelayanan')
                ->count('pel.idpelayanan');

            $mtbsAliases = [];
            if ($info['has_mtbs_s']) $mtbsAliases[] = 'mtbs_s';
            if ($info['has_mtbs_o']) $mtbsAliases[] = 'mtbs_o';
            if ($info['has_mtbs_a']) $mtbsAliases[] = 'mtbs_a';
            if ($info['has_mtbs_pl']) $mtbsAliases[] = 'mtbs_pl';

            $mtbmAliases = [];
            if ($info['has_mtbm_s']) $mtbmAliases[] = 'mtbm_s';
            if ($info['has_mtbm_o']) $mtbmAliases[] = 'mtbm_o';
            if ($info['has_mtbm_a']) $mtbmAliases[] = 'mtbm_a';
            if ($info['has_mtbm_pl']) $mtbmAliases[] = 'mtbm_pl';

            $mtbsFilled = 0;
            if (count($mtbsAliases)) {
                $cond = $this->buildFilledCondition($mtbsAliases);
                $mtbsFilled = (clone $qAgg)
                    ->whereRaw($cond)
                    ->distinct('pel.idpelayanan')
                    ->count('pel.idpelayanan');
            }

            $mtbmFilled = 0;
            if (count($mtbmAliases)) {
                $cond = $this->buildFilledCondition($mtbmAliases);
                $mtbmFilled = (clone $qAgg)
                    ->whereRaw($cond)
                    ->distinct('pel.idpelayanan')
                    ->count('pel.idpelayanan');
            }

            $laki = 0; $perempuan = 0;
            if ($genderCol) {
                $laki = (clone $qAgg)->where("p.$genderCol", 1)->distinct('pel.idpelayanan')->count('pel.idpelayanan');
                $perempuan = (clone $qAgg)->where("p.$genderCol", 2)->distinct('pel.idpelayanan')->count('pel.idpelayanan');
            }

            $avgUmur = null;
            if ($info['pasien_has_tgl_lahir']) {
                $umurRow = (clone $qAgg)
                    ->whereNotNull('p.TGL_LHR')
                    ->selectRaw(
                        'AVG(TIMESTAMPDIFF(DAY, p.TGL_LHR, l.tglKunjungan) / 365.2425) as avg_umur'
                    )
                    ->first();

                $avgUmur = isset($umurRow->avg_umur)
                    ? round((float) $umurRow->avg_umur, 2)
                    : null;
            } elseif ($info['loket_has_umur']) {
                $avgUmur = (clone $qAgg)->avg('l.umur');
                $avgUmur = $avgUmur !== null
                    ? round((float) $avgUmur, 2)
                    : null;
            }

            // =========================
            // Trend per hari (total, mtbs, mtbm)
            // =========================
            $qTrend = $this->baseQuery($info);
            $this->applyFilters($qTrend, $request, $info);

            $condMtbs = count($mtbsAliases) ? $this->buildFilledCondition($mtbsAliases) : "0=1";
            $condMtbm = count($mtbmAliases) ? $this->buildFilledCondition($mtbmAliases) : "0=1";

            $dateExpression = $this->dashboardDateExpression($info);

            $trend = $qTrend
                ->selectRaw("DATE({$dateExpression}) as tgl")
                ->selectRaw("COUNT(DISTINCT pel.idpelayanan) as total")
                ->selectRaw("COUNT(DISTINCT CASE WHEN {$condMtbs} THEN pel.idpelayanan END) as mtbs")
                ->selectRaw("COUNT(DISTINCT CASE WHEN {$condMtbm} THEN pel.idpelayanan END) as mtbm")
                ->groupByRaw("DATE({$dateExpression})")
                ->orderBy("tgl")
                ->get();

            // =========================
            // Severity per hari (MTBM merah/kuning/hijau + MTBS bucket)
            // (ambil row minimal lalu agregasi di PHP biar aman lintas versi MySQL)
            // =========================
            $qSev = $this->baseQuery($info);
            $this->applyFilters($qSev, $request, $info);

            $sevSelect = [
                DB::raw("DATE({$dateExpression}) as tgl"),
                'pel.idpelayanan as kunjungan_id',
            ];

            // kalau tabel/kolom gak ada, kasih NULL
            if ($info['mtbm_has_klas_global'] && $info['has_mtbm_a']) {
                $sevSelect[] = 'mtbm_a.klasifikasi_global as mtbm_global';
            } else {
                $sevSelect[] = DB::raw("NULL as mtbm_global");
            }

            if ($info['mtbs_has_status_kegawatan'] && $info['has_mtbs_a']) {
                $sevSelect[] = 'mtbs_a.status_kegawatan as mtbs_status';
            } else {
                $sevSelect[] = DB::raw("NULL as mtbs_status");
            }

            $sevRows = $qSev
                ->select($sevSelect)
                ->distinct()
                ->get();

            $sevAgg = [];
            $sevSeen = [];

            foreach ($sevRows as $r) {
                $visitKey = (string) $r->kunjungan_id;

                if (isset($sevSeen[$visitKey])) {
                    continue;
                }

                $sevSeen[$visitKey] = true;
                $tgl = (string)$r->tgl;
                if (!isset($sevAgg[$tgl])) {
                    $sevAgg[$tgl] = [
                        'tgl' => $tgl,
                        'mtbm_merah' => 0,
                        'mtbm_kuning' => 0,
                        'mtbm_hijau' => 0,
                        'mtbm_lain' => 0,
                        'mtbs_berat' => 0,
                        'mtbs_sedang' => 0,
                        'mtbs_ringan' => 0,
                        'mtbs_lain' => 0,
                    ];
                }

                $g = strtolower(trim((string)$r->mtbm_global));
                if ($g === 'merah') $sevAgg[$tgl]['mtbm_merah']++;
                elseif ($g === 'kuning') $sevAgg[$tgl]['mtbm_kuning']++;
                elseif ($g === 'hijau') $sevAgg[$tgl]['mtbm_hijau']++;
                elseif ($g !== '') $sevAgg[$tgl]['mtbm_lain']++;

                $bucket = $this->mtbsSeverityBucket($r->mtbs_status);
                $sevAgg[$tgl]["mtbs_{$bucket}"]++;
            }

            $severity = array_values($sevAgg);
            usort($severity, fn($a, $b) => strcmp($a['tgl'], $b['tgl']));

            // =========================
            // Top puskesmas (FIXED: groupBy bener, no SQL syntax error)
            // =========================
            $topPuskesmas = [];
            if ($info['loket_has_pusk_id']) {
                $qTopPusk = $this->baseQuery($info);
                $this->applyFilters($qTopPusk, $request, $info);

                if ($info['has_unit_profiles']) {
                    $topPuskesmas = $qTopPusk
                        ->select([
                            'l.puskId as puskId',
                            DB::raw("COALESCE(up.nama_unit, CONCAT('PUSK ID ', l.puskId)) as puskesmas"),
                            DB::raw("COUNT(DISTINCT pel.idpelayanan) as total"),
                        ])
                        ->groupBy('l.puskId')
                        ->groupBy('up.nama_unit')
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
                } else {
                    $topPuskesmas = $qTopPusk
                        ->select([
                            'l.puskId as puskId',
                            DB::raw("CONCAT('PUSK ID ', l.puskId) as puskesmas"),
                            DB::raw("COUNT(DISTINCT pel.idpelayanan) as total"),
                        ])
                        ->groupBy('l.puskId')
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
                }
            }


            // =========================
            // Top keluhan utama (MTBM subjective)
            // =========================
            $topKeluhan = [];
            if ($info['mtbm_has_keluhan_utama'] && $info['has_mtbm_s']) {
                $qKel = $this->baseQuery($info);
                $this->applyFilters($qKel, $request, $info);

                $topKeluhan = $qKel
                    ->selectRaw("mtbm_s.keluhan_utama as label")
                    ->selectRaw("COUNT(DISTINCT pel.idpelayanan) as total")
                    ->whereNotNull('mtbm_s.keluhan_utama')
                    ->whereRaw("TRIM(mtbm_s.keluhan_utama) <> ''")
                    ->groupBy('mtbm_s.keluhan_utama')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get();
            }

            // =========================
            // Top MTBM detail (infeksi/diare/menyusu)
            // =========================
            $topMtbmInfeksi = [];
            $topMtbmDiare = [];
            $topMtbmMenyusu = [];

            if ($info['has_mtbm_a']) {
                if ($info['mtbm_has_klas_infeksi']) {
                    $q = $this->baseQuery($info);
                    $this->applyFilters($q, $request, $info);
                    $topMtbmInfeksi = $q
                        ->selectRaw("mtbm_a.klas_infeksi as label")
                        ->selectRaw("COUNT(DISTINCT pel.idpelayanan) as total")
                        ->whereNotNull('mtbm_a.klas_infeksi')
                        ->whereRaw("TRIM(mtbm_a.klas_infeksi) <> ''")
                        ->groupBy('mtbm_a.klas_infeksi')
                        ->orderByDesc('total')
                        ->limit(10)->get();
                }

                if ($info['mtbm_has_klas_diare']) {
                    $q = $this->baseQuery($info);
                    $this->applyFilters($q, $request, $info);
                    $topMtbmDiare = $q
                        ->selectRaw("mtbm_a.klas_diare as label")
                        ->selectRaw("COUNT(DISTINCT pel.idpelayanan) as total")
                        ->whereNotNull('mtbm_a.klas_diare')
                        ->whereRaw("TRIM(mtbm_a.klas_diare) <> ''")
                        ->groupBy('mtbm_a.klas_diare')
                        ->orderByDesc('total')
                        ->limit(10)->get();
                }

                if ($info['mtbm_has_klas_menyusu_bb']) {
                    $q = $this->baseQuery($info);
                    $this->applyFilters($q, $request, $info);
                    $topMtbmMenyusu = $q
                        ->selectRaw("mtbm_a.klas_menyusu_bb as label")
                        ->selectRaw("COUNT(DISTINCT pel.idpelayanan) as total")
                        ->whereNotNull('mtbm_a.klas_menyusu_bb')
                        ->whereRaw("TRIM(mtbm_a.klas_menyusu_bb) <> ''")
                        ->groupBy('mtbm_a.klas_menyusu_bb')
                        ->orderByDesc('total')
                        ->limit(10)->get();
                }
            }

            // =========================
            // Top MTBS klasifikasi global (JSON array) => hitung di PHP
            // =========================
            $topMtbsKlas = [];
            if ($info['mtbs_has_klasifikasi_global'] && $info['has_mtbs_a']) {
                $q = $this->baseQuery($info);
                $this->applyFilters($q, $request, $info);

                $rowsKg = $q
                    ->select([
                        'pel.idpelayanan as kunjungan_id',
                        'mtbs_a.klasifikasi_global as kg',
                    ])
                    ->whereNotNull('mtbs_a.klasifikasi_global')
                    ->distinct()
                    ->get();

                $counter = [];
                foreach ($rowsKg as $r) {
                    $items = $this->parseJsonList($r->kg);
                    foreach ($items as $it) {
                        $label = trim((string)$it);
                        if ($label === '') continue;
                        $counter[$label] = ($counter[$label] ?? 0) + 1;
                    }
                }

                arsort($counter);
                $slice = array_slice($counter, 0, 10, true);

                $tmp = [];
                foreach ($slice as $label => $n) {
                    $tmp[] = ['label' => $label, 'total' => (int)$n];
                }
                $topMtbsKlas = $tmp;
            }

            // =========================
            // Prioritas: MTBM merah OR MTBS berat
            // =========================
            $qPri = $this->baseQuery($info);
            $this->applyFilters($qPri, $request, $info);

            // Prioritas mengikuti isi sebenarnya pada assessment:
            // warna merah, status rujukan/gawat, atau klasifikasi berat.
            $priorityParts = [];

            if ($info['mtbm_has_klas_global'] && $info['has_mtbm_a']) {
                $priorityParts[] = "(
                    LOWER(CAST(mtbm_a.klasifikasi_global AS CHAR)) LIKE '%merah%'
                    OR LOWER(CAST(mtbm_a.klasifikasi_global AS CHAR)) LIKE '%berat%'
                    OR LOWER(CAST(mtbm_a.klasifikasi_global AS CHAR)) LIKE '%rujuk%'
                )";
            }

            if ($info['mtbm_has_klas_infeksi'] && $info['has_mtbm_a']) {
                $priorityParts[] = "LOWER(CAST(mtbm_a.klas_infeksi AS CHAR)) LIKE '%berat%'";
            }

            if ($info['mtbm_has_klas_diare'] && $info['has_mtbm_a']) {
                $priorityParts[] = "LOWER(CAST(mtbm_a.klas_diare AS CHAR)) LIKE '%berat%'";
            }

            if ($info['mtbm_has_klas_menyusu_bb'] && $info['has_mtbm_a']) {
                $priorityParts[] = "(
                    LOWER(CAST(mtbm_a.klas_menyusu_bb AS CHAR)) LIKE '%berat%'
                    OR LOWER(CAST(mtbm_a.klas_menyusu_bb AS CHAR)) LIKE '%sangat rendah%'
                )";
            }

            if ($info['mtbs_has_status_kegawatan'] && $info['has_mtbs_a']) {
                $priorityParts[] = "(
                    LOWER(CAST(mtbs_a.status_kegawatan AS CHAR)) LIKE '%berat%'
                    OR LOWER(CAST(mtbs_a.status_kegawatan AS CHAR)) LIKE '%rujuk%'
                    OR LOWER(CAST(mtbs_a.status_kegawatan AS CHAR)) LIKE '%gawat%'
                    OR LOWER(CAST(mtbs_a.status_kegawatan AS CHAR)) LIKE '%segera%'
                )";
            }

            if ($info['mtbs_has_klasifikasi_global'] && $info['has_mtbs_a']) {
                $priorityParts[] = "(
                    LOWER(CAST(mtbs_a.klasifikasi_global AS CHAR)) LIKE '%berat%'
                    OR LOWER(CAST(mtbs_a.klasifikasi_global AS CHAR)) LIKE '%gagal jantung paru%'
                    OR LOWER(CAST(mtbs_a.klasifikasi_global AS CHAR)) LIKE '%gizi buruk dengan komplikasi%'
                    OR LOWER(CAST(mtbs_a.klasifikasi_global AS CHAR)) LIKE '%rujuk%'
                )";
            }

            if ($info['has_mtbs_status']) {
                $priorityParts[] = "(
                    LOWER(CAST(mtbs_st.status_pulang AS CHAR)) LIKE '%rujuk%'
                    OR LOWER(CAST(mtbs_st.status_pulang AS CHAR)) LIKE '%rawat inap%'
                    OR LOWER(CAST(mtbs_st.status_pulang AS CHAR)) LIKE '%meninggal%'
                )";
            }

            if ($info['has_mtbs_rujukan']) {
                $priorityParts[] = 'mtbs_rj.kunjungan_id IS NOT NULL';
            }

            if (count($priorityParts)) {
                $qPri->whereRaw('(' . implode(' OR ', $priorityParts) . ')');
            } else {
                $qPri->whereRaw('0=1');
            }

            $selectPri = [
                'pel.idpelayanan as kunjungan_id',
                DB::raw("{$dateExpression} as tglPelayanan"),
                DB::raw(
                    "CASE WHEN {$servedCondition} THEN 1 ELSE 0 END as sudahDilayani"
                ),
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'poli.nmPoli',
            ];

            if ($info['has_unit_profiles']) {
                $selectPri[] = DB::raw(
                    "COALESCE(up.nama_unit, CONCAT('PUSK ID ', l.puskId)) as puskesmas"
                );
            } elseif ($info['loket_has_pusk_id']) {
                $selectPri[] = DB::raw("CONCAT('PUSK ID ', l.puskId) as puskesmas");
            } else {
                $selectPri[] = DB::raw("NULL as puskesmas");
            }

            // vitals (ambil dari mtbs_o dulu lalu mtbm_o di vue)
            if ($info['has_mtbs_o'] && Schema::hasColumn('mtbs_objektif', 'rr')) $selectPri[] = 'mtbs_o.rr as mtbs_rr'; else $selectPri[] = DB::raw("NULL as mtbs_rr");
            if ($info['has_mtbs_o'] && Schema::hasColumn('mtbs_objektif', 'suhu')) $selectPri[] = 'mtbs_o.suhu as mtbs_suhu'; else $selectPri[] = DB::raw("NULL as mtbs_suhu");
            if ($info['has_mtbs_o'] && Schema::hasColumn('mtbs_objektif', 'spo2')) $selectPri[] = 'mtbs_o.spo2 as mtbs_spo2'; else $selectPri[] = DB::raw("NULL as mtbs_spo2");

            if ($info['has_mtbm_o'] && Schema::hasColumn('mtbm_objective', 'rr')) $selectPri[] = 'mtbm_o.rr as mtbm_rr'; else $selectPri[] = DB::raw("NULL as mtbm_rr");
            if ($info['has_mtbm_o'] && Schema::hasColumn('mtbm_objective', 'suhu')) $selectPri[] = 'mtbm_o.suhu as mtbm_suhu'; else $selectPri[] = DB::raw("NULL as mtbm_suhu");
            if ($info['has_mtbm_o'] && Schema::hasColumn('mtbm_objective', 'spo2')) $selectPri[] = 'mtbm_o.spo2 as mtbm_spo2'; else $selectPri[] = DB::raw("NULL as mtbm_spo2");

            // mtbm fields
            if ($info['mtbm_has_klas_global'] && $info['has_mtbm_a']) $selectPri[] = 'mtbm_a.klasifikasi_global as mtbm_global'; else $selectPri[] = DB::raw("NULL as mtbm_global");
            if ($info['mtbm_has_klas_infeksi'] && $info['has_mtbm_a']) $selectPri[] = 'mtbm_a.klas_infeksi as mtbm_infeksi'; else $selectPri[] = DB::raw("NULL as mtbm_infeksi");
            if ($info['mtbm_has_klas_diare'] && $info['has_mtbm_a']) $selectPri[] = 'mtbm_a.klas_diare as mtbm_diare'; else $selectPri[] = DB::raw("NULL as mtbm_diare");
            if ($info['mtbm_has_klas_menyusu_bb'] && $info['has_mtbm_a']) $selectPri[] = 'mtbm_a.klas_menyusu_bb as mtbm_menyusu_bb'; else $selectPri[] = DB::raw("NULL as mtbm_menyusu_bb");

            // mtbs fields
            if ($info['mtbs_has_status_kegawatan'] && $info['has_mtbs_a']) {
                if ($info['has_mtbs_status']) {
                    $selectPri[] = DB::raw(
                        "CONCAT_WS(' | ', NULLIF(mtbs_a.status_kegawatan, ''), NULLIF(mtbs_st.status_pulang, '')) as mtbs_status"
                    );
                } else {
                    $selectPri[] = 'mtbs_a.status_kegawatan as mtbs_status';
                }
            } elseif ($info['has_mtbs_status']) {
                $selectPri[] = 'mtbs_st.status_pulang as mtbs_status';
            } elseif ($info['has_mtbs_rujukan']) {
                $selectPri[] = DB::raw("'Rujukan MTBS' as mtbs_status");
            } else {
                $selectPri[] = DB::raw("NULL as mtbs_status");
            }

            if ($info['mtbs_has_klasifikasi_global'] && $info['has_mtbs_a']) {
                $selectPri[] = 'mtbs_a.klasifikasi_global as mtbs_kg';
            } else {
                $selectPri[] = DB::raw("NULL as mtbs_kg");
            }

            $prioritas = $qPri
                ->select($selectPri)
                ->orderByDesc(DB::raw($dateExpression))
                ->limit(50)
                ->get();

            // =========================
            // DEBUG BERJENJANG
            // Membantu melihat pada tahap mana satu kunjungan tersaring.
            // =========================
            $debugBaseToday = DB::table('simpus_pelayanan as pel')
                ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
                ->whereDate('l.tglKunjungan', '>=', $dateFrom)
                ->whereDate('l.tglKunjungan', '<=', $dateTo);

            $debugAllPoli = (clone $debugBaseToday)
                ->distinct('pel.idpelayanan')
                ->count('pel.idpelayanan');

            $debugPoli003 = (clone $debugBaseToday)
                ->where('l.kdPoli', $this->DEFAULT_KD_POLI)
                ->distinct('pel.idpelayanan')
                ->count('pel.idpelayanan');

            $debugLatestPoli003 = (clone $debugBaseToday)
                ->where('l.kdPoli', $this->DEFAULT_KD_POLI)
                ->select([
                    'pel.idpelayanan',
                    'l.idLoket',
                    'l.kdPoli',
                    'l.tglKunjungan',
                    'pel.tglPelayanan',
                    'pel.sudahDilayani',
                ])
                ->orderByDesc('l.tglKunjungan')
                ->orderByDesc('l.idLoket')
                ->limit(5)
                ->get();

            $debugMtbsAssessmentRows = $info['has_mtbs_a']
                ? DB::table('mtbs_assessment')->count()
                : 0;

            $debugMtbsAssessmentVisits = $info['has_mtbs_a']
                ? DB::table('mtbs_assessment')
                    ->whereNotNull('kunjungan_id')
                    ->distinct()
                    ->count('kunjungan_id')
                : 0;

            $debugMtbsStatusRows = $info['has_mtbs_status']
                ? DB::table('mtbs_statuspasien')->count()
                : 0;

            $debugMtbsRujukanRows = $info['has_mtbs_rujukan']
                ? DB::table('mtbs_rujukan')->count()
                : 0;

            // =========================
            // RESPONSE
            // =========================
            return response()->json([
                'message' => 'OK',
                'data' => [
                    // Dropdown juga dikirim dari endpoint data agar tetap tampil
                    // walaupun komponen dipasang melalui parent/wrapper lain.
                    'puskesmas' => $puskesmasOptions,
                    'kpi' => [
                        'total' => (int)$total,
                        'mtbs_filled' => (int)$mtbsFilled,
                        'mtbm_filled' => (int)$mtbmFilled,
                        'unserved' => (int)$unserved,
                        'laki_laki' => (int)$laki,
                        'perempuan' => (int)$perempuan,
                        'avg_umur' => $avgUmur,
                        'gender_col_used' => $genderCol,
                    ],
                    'trend' => $trend,
                    'severity' => $severity,
                    'top' => [
                        'puskesmas' => $topPuskesmas,
                        'mtbs_klasifikasi_global' => $topMtbsKlas,
                        'mtbm_infeksi' => $topMtbmInfeksi,
                        'mtbm_diare' => $topMtbmDiare,
                        'mtbm_menyusu_bb' => $topMtbmMenyusu,
                        'keluhan_utama' => $topKeluhan,
                    ],
                    'prioritas' => $prioritas,
                    'debug' => [
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'kdPoli' => $kdPoli,
                        'puskId' => $puskId,
                        'served' => $served,
                        'keyword' => $keyword,
                        'scope' => $kdPoli !== ''
                            ? "Poli {$kdPoli}"
                            : "Poli {$this->DEFAULT_KD_POLI} atau memiliki data MTBS/MTBM",
                        'date_expression' => $dateExpression,
                        'collation_forced' => $this->COLL,
                        'puskesmas_count' => count($puskesmasOptions),
                        'has_unit_profiles' => $info['has_unit_profiles'],
                        'loket_has_pusk_id' => $info['loket_has_pusk_id'],
                        'raw_all_poli_in_period' => (int) $debugAllPoli,
                        'raw_poli_003_in_period' => (int) $debugPoli003,
                        'latest_poli_003' => $debugLatestPoli003,
                        'mtbs_assessment_rows' => $debugMtbsAssessmentRows,
                        'mtbs_assessment_distinct_kunjungan' => $debugMtbsAssessmentVisits,
                        'mtbs_status_rows' => $debugMtbsStatusRows,
                        'mtbs_rujukan_rows' => $debugMtbsRujukanRows,
                        'priority_count' => $prioritas->count(),
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'ERROR',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}
