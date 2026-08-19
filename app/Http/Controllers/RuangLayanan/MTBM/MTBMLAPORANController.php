<?php

namespace App\Http\Controllers\RuangLayanan\MTBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Carbon\Carbon;
class MTBMLAPORANController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'keyword',
            'klasifikasi_global',
            'sudahDilayani',
            'kdPoli',
            'per_page',
        ]);

        return Inertia::render('Laporan/MTBM/LaporanMTBM', [
            'filters' => $filters,
        ]);
    }

    private function joinMTBM($q): void
    {
        // join MTBM hanya kalau tabelnya ada (biar gak 500 kalau DB dev belum lengkap)
        if (Schema::hasTable('mtbm_subjective')) {
            $q->leftJoin('mtbm_subjective as s', 's.kunjungan_id', '=', 'pel.idpelayanan');
        }
        if (Schema::hasTable('mtbm_objective')) {
            $q->leftJoin('mtbm_objective as o', 'o.kunjungan_id', '=', 'pel.idpelayanan');
        }
        if (Schema::hasTable('mtbm_assessment')) {
            $q->leftJoin('mtbm_assessment as a', 'a.kunjungan_id', '=', 'pel.idpelayanan');
        }
        if (Schema::hasTable('mtbm_planning')) {
            $q->leftJoin('mtbm_planning as pl', 'pl.kunjungan_id', '=', 'pel.idpelayanan');
        }
    }

public function data(Request $request)
{
    try {
        $COLL = 'utf8mb4_general_ci';

        // =========
        // INPUT
        // =========
        $kdPoli    = (string) $request->get('kdPoli', '003');
        $yearFrom  = (int) $request->get('year_from', (int) now()->format('Y'));
        $yearTo    = (int) $request->get('year_to', (int) now()->format('Y'));

        if ($yearFrom <= 0) $yearFrom = (int) now()->format('Y');
        if ($yearTo <= 0) $yearTo = (int) now()->format('Y');
        if ($yearFrom > $yearTo) { $tmp = $yearFrom; $yearFrom = $yearTo; $yearTo = $tmp; }

        $dateFrom = Carbon::create($yearFrom, 1, 1)->toDateString();
        $dateTo   = Carbon::create($yearTo, 12, 31)->toDateString();

        // =========
        // GENDER COLUMN DETECT
        // =========
        $genderCol = null;
        $genderCandidates = ['JENIS_KLMIN','JENIS_KELAMIN','jenis_kelamin','jk','JK','GENDER','gender'];
        foreach ($genderCandidates as $col) {
            if (Schema::hasColumn('simpus_pasien', $col)) { $genderCol = $col; break; }
        }

        // =========
        // BASE QUERY: pel -> loket -> pasien
        // + join MTBM/MTBS kalau ada (biar bisa "gabungan")
        // =========
        $q = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l',
                DB::raw("pel.loketId COLLATE {$COLL}"),
                '=',
                DB::raw("l.idLoket COLLATE {$COLL}")
            )
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->where('l.kdPoli', (int) $kdPoli)
            ->whereRaw("DATE(pel.tglPelayanan) BETWEEN ? AND ?", [$dateFrom, $dateTo]);

        // JOIN MTBM (opsional, kalau tabel ada)
        if (Schema::hasTable('mtbm_subjective')) {
            $q->leftJoin('mtbm_subjective as mtbm_s',
                DB::raw("mtbm_s.kunjungan_id COLLATE {$COLL}"),
                '=',
                DB::raw("pel.idpelayanan COLLATE {$COLL}")
            );
        }

        // JOIN MTBS (opsional, kamu sesuaikan nama tabel kalau beda)
        // Aku bikin fleksibel: coba beberapa kandidat nama tabel.
        $mtbsJoined = false;
        $mtbsTableCandidates = [
            'mtbs_subjective',
            'mtbs_assessment',
            'mtbs_objective',
            'mtbs_planning',
            'mtbs' // kalau ada tabel utama
        ];

        foreach ($mtbsTableCandidates as $t) {
            if (Schema::hasTable($t)) {
                // asumsi kolom kunjungan_id ada dan tipe varchar mirip MTBM
                $q->leftJoin("{$t} as mtbs_any",
                    DB::raw("mtbs_any.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
                $mtbsJoined = true;
                break;
            }
        }

        // =========
        // DEFINISI "GABUNGAN MTBM + MTBS"
        // =========
        // - kalau mtbs ada: hitung pasien yang punya record MTBM atau MTBS
        // - kalau mtbs nggak ada: minimal MTBM
        // - kalau MTBM juga nggak ada: fallback semua kunjungan poli (biar nggak 0 total)
        $qGabungan = clone $q;

        $hasMTBM = Schema::hasTable('mtbm_subjective');
        $hasMTBS = $mtbsJoined;

        if ($hasMTBM && $hasMTBS) {
            $qGabungan->where(function ($w) {
                $w->whereNotNull('mtbm_s.kunjungan_id')
                  ->orWhereNotNull('mtbs_any.kunjungan_id');
            });
        } elseif ($hasMTBM) {
            $qGabungan->whereNotNull('mtbm_s.kunjungan_id');
        } elseif ($hasMTBS) {
            $qGabungan->whereNotNull('mtbs_any.kunjungan_id');
        } // else: fallback semua kunjungan poli (nggak ditambah where)

        // =========
        // COUNT: jumlah BALITA (anggap "balita" = pasien unik), bukan jumlah kunjungan
        // =========
        $total = (clone $qGabungan)->distinct('l.pasienId')->count('l.pasienId');

        $laki = 0;
        $perempuan = 0;

        if ($genderCol) {
            // antisipasi value bisa 1/2 atau 'L'/'P'
            $lakiValues = [1, '1', 'L', 'l', 'LK', 'lk', 'LAKI', 'Laki-laki', 'LAKI-LAKI'];
            $perValues  = [2, '2', 'P', 'p', 'PR', 'pr', 'PEREMPUAN', 'Perempuan'];

            $laki = (clone $qGabungan)
                ->whereIn("p.$genderCol", $lakiValues)
                ->distinct('l.pasienId')->count('l.pasienId');

            $perempuan = (clone $qGabungan)
                ->whereIn("p.$genderCol", $perValues)
                ->distinct('l.pasienId')->count('l.pasienId');
        }

        return response()->json([
            'message' => 'OK',
            'data' => [
                'aggregat' => [
                    'laki_laki' => (int) $laki,
                    'perempuan' => (int) $perempuan,
                    'total' => (int) $total,
                ],
                'debug' => [
                    'kdPoli' => $kdPoli,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'gender_col_used' => $genderCol,
                    'mtbm_used' => $hasMTBM,
                    'mtbs_used' => $hasMTBS,
                    'collation_forced' => $COLL,
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
public function export(Request $request)
{
    try {
        $COLL = 'utf8mb4_general_ci';

        $kdPoli   = (string) $request->get('kdPoli', '003');
        $keyword  = trim((string) $request->get('keyword', ''));
        $klasGlob = $request->get('klasifikasi_global');
        $sudah    = $request->get('sudahDilayani');

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if (!$dateFrom || !$dateTo) {
            $dateTo = now()->toDateString();
            $dateFrom = now()->subDays(30)->toDateString();
        }

        $applyFilters = function ($q) use ($kdPoli, $dateFrom, $dateTo, $keyword, $klasGlob, $sudah) {
            $q->where('l.kdPoli', (int) $kdPoli);
            $q->whereRaw("DATE(pel.tglPelayanan) BETWEEN ? AND ?", [$dateFrom, $dateTo]);

            if ($keyword !== '') {
                $q->where(function ($w) use ($keyword) {
                    $w->where('p.NO_MR', 'like', "%{$keyword}%")
                      ->orWhere('p.NAMA_LGKP', 'like', "%{$keyword}%")
                      ->orWhere('p.NIK', 'like', "%{$keyword}%");
                });
            }

            if ($klasGlob !== null && $klasGlob !== '') {
                $q->where('a.klasifikasi_global', $klasGlob);
            }

            if ($sudah !== null && $sudah !== '') {
                $q->where('pel.sudahDilayani', (int) $sudah);
            }

            return $q;
        };

        // cari kolom gender yang tersedia
        $genderCol = null;
$genderCandidates = ['JENIS_KLMIN','JENIS_KELAMIN', 'jenis_kelamin', 'jk', 'JK', 'GENDER', 'gender'];
        foreach ($genderCandidates as $col) {
            if (Schema::hasColumn('simpus_pasien', $col)) {
                $genderCol = $col;
                break;
            }
        }

        $makeBase = function () use ($COLL) {
            $q = DB::table('simpus_pelayanan as pel')
                ->join('simpus_loket as l',
                    DB::raw("pel.loketId COLLATE {$COLL}"),
                    '=',
                    DB::raw("l.idLoket COLLATE {$COLL}")
                )
                ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID');

            // join MTBM hanya kalau tabel ada
            if (Schema::hasTable('mtbm_subjective')) {
                $q->leftJoin('mtbm_subjective as s',
                    DB::raw("s.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
            } else {
                $q->leftJoin(DB::raw("(select null as kunjungan_id, null as keluhan_utama) as s"), DB::raw("1"), "=", DB::raw("0"));
            }

            if (Schema::hasTable('mtbm_objective')) {
                $q->leftJoin('mtbm_objective as o',
                    DB::raw("o.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
            } else {
                $q->leftJoin(DB::raw("(select null as kunjungan_id, null as rr, null as suhu, null as spo2) as o"), DB::raw("1"), "=", DB::raw("0"));
            }

            if (Schema::hasTable('mtbm_assessment')) {
                $q->leftJoin('mtbm_assessment as a',
                    DB::raw("a.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
            } else {
                $q->leftJoin(DB::raw("(select null as kunjungan_id, null as klasifikasi_global, null as klas_infeksi, null as klas_diare, null as klas_menyusu_bb) as a"), DB::raw("1"), "=", DB::raw("0"));
            }

            if (Schema::hasTable('mtbm_planning')) {
                $q->leftJoin('mtbm_planning as pl',
                    DB::raw("pl.kunjungan_id COLLATE {$COLL}"),
                    '=',
                    DB::raw("pel.idpelayanan COLLATE {$COLL}")
                );
            } else {
                $q->leftJoin(DB::raw("(select null as kunjungan_id, null as keputusan) as pl"), DB::raw("1"), "=", DB::raw("0"));
            }

            return $q;
        };

        // total sakit (semua lolos filter)
        $qAll = $makeBase();
        $applyFilters($qAll);

// hitung L/P sakit
$sakitL = 0; $sakitP = 0;
if ($genderCol) {
  $sakitL = (clone $qAll)->where("p.$genderCol", 1)->count(); // 1 = L
  $sakitP = (clone $qAll)->where("p.$genderCol", 2)->count(); // 2 = P
}
$sakitN = (clone $qAll)->count();

// MTBS: contoh definisi = ada assessment (a.kunjungan_id not null)
$qMtbs = $makeBase();
$applyFilters($qMtbs);
$qMtbs->whereNotNull('a.kunjungan_id');

// hitung L/P MTBS (INI YANG KAMU SALAH TADI)
$mtbsL = 0; $mtbsP = 0;
if ($genderCol) {
  $mtbsL = (clone $qMtbs)->where("p.$genderCol", 1)->count(); // 1 = L
  $mtbsP = (clone $qMtbs)->where("p.$genderCol", 2)->count(); // 2 = P
}
$mtbsN = (clone $qMtbs)->count();


        

        $pct = function ($num, $den) {
            if (!$den) return 0.0;
            return round(($num / $den) * 100, 1);
        };

        $aggregat = [
            'sakitL' => $sakitL,
            'sakitP' => $sakitP,
            'sakitN' => $sakitN,
            'mtbsL' => $mtbsL,
            'mtbsP' => $mtbsP,
            'mtbsN' => $mtbsN,
            'mtbsPctL' => $pct($mtbsL, $sakitL),
            'mtbsPctP' => $pct($mtbsP, $sakitP),
            'mtbsPctN' => $pct($mtbsN, $sakitN),
            'gender_col_used' => $genderCol,
        ];

        // detail (batasi biar aman)
        $detailQuery = $makeBase();
        $applyFilters($detailQuery);

        $detail = $detailQuery
            ->select([
                'pel.idpelayanan as kunjungan_id',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                's.keluhan_utama',
                'o.rr',
                'o.suhu',
                'o.spo2',
                'a.klasifikasi_global',
                'a.klas_infeksi',
                'a.klas_diare',
                'a.klas_menyusu_bb',
                'pl.keputusan',
            ])
            ->orderByDesc('pel.tglPelayanan')
            ->limit(20000)
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => [
                'aggregat' => $aggregat,
                'detail' => $detail,
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
