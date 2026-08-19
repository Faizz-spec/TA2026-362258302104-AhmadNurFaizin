<?php

namespace App\Models\Home;

use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class DashboardHome
{
    /**
     * Per-hari L/P pada rentang [startDate, endDate], zero-fill tanggal yang tidak ada data.
     */
    public static function perDayAll(string $startDate, string $endDate): array
    {
        // Ambil agregasi yang ada datanya
        $rows = DB::table('simpus_loket as l')
            ->leftJoin('simpus_pasien as p', 'p.ID', '=', 'l.pasienId')
            ->selectRaw("
                DATE(l.tglKunjungan) AS `date`,
                SUM(CASE WHEN p.JENIS_KLMIN = 1 THEN 1 ELSE 0 END) AS male,
                SUM(CASE WHEN p.JENIS_KLMIN = 2 THEN 1 ELSE 0 END) AS female
            ")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // untuk lookup cepat

        // Zero-fill seluruh tanggal dalam rentang
        $period = CarbonPeriod::create($startDate, $endDate);
        $result = [];
        foreach ($period as $d) {
            $key = $d->format('Y-m-d');
            $r   = $rows->get($key);
            $result[] = [
                'date'   => $key,
                'male'   => (int)($r->male   ?? 0),
                'female' => (int)($r->female ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Total L/P pada rentang.
     */
    public static function genderTotals(string $startDate, string $endDate): array
    {
        $row = DB::table('simpus_loket as l')
            ->leftJoin('simpus_pasien as p', 'p.ID', '=', 'l.pasienId')
            ->selectRaw("
                SUM(CASE WHEN p.JENIS_KLMIN = 1 THEN 1 ELSE 0 END) AS male,
                SUM(CASE WHEN p.JENIS_KLMIN = 2 THEN 1 ELSE 0 END) AS female
            ")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->first();

        return [
            'male'   => (int)($row->male ?? 0),
            'female' => (int)($row->female ?? 0),
        ];
        }

    /**
     * Total pembiayaan BPJS vs Non-BPJS pada rentang.
     * Sesuaikan kolom 'jknPbi' sesuai skema kamu.
     */
    public static function paymentTotals(string $startDate, string $endDate): array
    {
        $row = DB::table('simpus_loket as l')
            ->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(l.jknPbi)) IN ('BPJS','JKN','PBI') THEN 1 ELSE 0 END) AS bpjs,
                SUM(CASE WHEN UPPER(TRIM(l.jknPbi)) IN ('BPJS','JKN','PBI') THEN 0 ELSE 1 END) AS non_bpjs
            ")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->first();

        return [
            'bpjs'     => (int)($row->bpjs ?? 0),
            'non_bpjs' => (int)($row->non_bpjs ?? 0),
        ];
    }

    /**
     * Total kunjungan baru vs lama pada rentang.
     * Sesuaikan kolom 'kunjBaru' (YA = baru).
     */
    public static function visitTotals(string $startDate, string $endDate): array
    {
        $row = DB::table('simpus_loket as l')
            ->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(l.kunjBaru)) = 'YA' THEN 1 ELSE 0 END) AS baru,
                SUM(CASE WHEN UPPER(TRIM(l.kunjBaru)) = 'YA' THEN 0 ELSE 1 END) AS lama
            ")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->first();

        return [
            'baru' => (int)($row->baru ?? 0),
            'lama' => (int)($row->lama ?? 0),
        ];
    }

    /**
     * Total rujukan (dummy contoh).
     * Kalau punya kolom status rujukan asli, ganti CASE WHEN sesuai.
     */
    public static function referralTotals(string $startDate, string $endDate): array
    {
        $row = DB::table('simpus_loket as l')
            ->selectRaw("
                SUM(1) AS internal, 
                SUM(0) AS rujukan
            ")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->first();

        return [
            'internal' => (int)($row->internal ?? 0),
            'rujukan'  => (int)($row->rujukan  ?? 0),
        ];
    }

    /**
     * Top penyakit dari kolom keluhan (placeholder kode Kxx).
     * Sesuaikan kolom 'keluhan' atau mapping ICD jika ada.
     */
    public static function topDiseases(string $startDate, string $endDate, int $limit = 10): array
    {
        $rows = DB::table('simpus_loket as l')
            ->selectRaw("TRIM(COALESCE(l.jnsPeserta, 'PBI')) AS nama, COUNT(*) AS total")
            ->whereBetween(DB::raw('DATE(l.tglKunjungan)'), [$startDate, $endDate])
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $ranked = [];
        $i = 1;
        foreach ($rows as $r) {
            $ranked[] = [
                'kode'  => sprintf('K%02d', $i++), // placeholder
                'nama'  => $r->nama,
                'total' => (int)$r->total,
            ];
        }
        return $ranked;
    }
}
