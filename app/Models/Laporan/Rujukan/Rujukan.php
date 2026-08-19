<?php

namespace App\Models\Laporan\Rujukan;

use Illuminate\Support\Facades\DB;

class Rujukan
{
    /**
     * Data tabel rujukan (paginate) berdasarkan filter.
     * JOIN: surat_rujukan (sr) -> pelayanan (pl) -> loket (lk) -> pasien (ps, via ps.ID = lk.pasienId)
     * + provider (pr) + poli_fktl (pf)
     */
    public static function rujukanPaginate(array $filters)
    {
        $perPage = max((int)($filters['per_page'] ?? 10), 1);

        $q = DB::table('surat_rujukan as sr')
            ->leftJoin('simpus_pelayanan as pl', 'pl.idpelayanan', '=', 'sr.id_pelayanan')
            ->leftJoin('simpus_loket as lk', 'lk.idLoket', '=', 'pl.loketId')
            // kolom kunci pasien = simpus_pasien.ID  (hasil DESCRIBE yang kamu kirim)
            ->leftJoin('simpus_pasien as ps', 'ps.ID', '=', 'lk.pasienId')
            ->leftJoin('simpus_provider as pr', 'pr.kdProvider', '=', 'sr.kdppk')
            ->leftJoin('simpus_poli_fktl as pf', 'pf.kdPoli', '=', 'sr.kdPoliRujLan')
            ->selectRaw("
                sr.tgl_rujuk as tgl_rujuk,
                COALESCE(ps.NIK, '') as nik,
                COALESCE(ps.NAMA_LGKP, '') as nama,
                pr.nmProvider as nama_rs,
                pf.nmPoli as poli_tujuan,
                CASE
                  WHEN (lk.jnsPeserta = 'BPJS' OR (lk.noKartu IS NOT NULL AND lk.noKartu <> '')) THEN 'BPJS'
                  ELSE 'UMUM'
                END as kepesertaan,
                COALESCE(sr.respon_id, '-') as status,
                CASE WHEN COALESCE(pl.sudahDilayani,0) = 1 THEN 'Ya' ELSE 'Tidak' END as dilayani
            ");

        // wajib ada tanggal, else balikin paginator kosong
        if (!empty($filters['tgl'])) {
            $q->whereDate('sr.tgl_rujuk', '=', $filters['tgl']);
        } else {
            return (object)[
                'data' => [],
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'from' => null,
                'to' => null,
                'prev_page_url' => null,
                'next_page_url' => null,
            ];
        }

        if (!empty($filters['pusk_id'])) {
            $q->where('lk.puskId', $filters['pusk_id']);
        }

        if (($filters['kepesertaan'] ?? null) === 'UMUM') {
            $q->where(function ($w) {
                $w->whereNull('lk.noKartu')->orWhere('lk.noKartu', '=', '');
            });
        } elseif (($filters['kepesertaan'] ?? null) === 'BPJS') {
            $q->where(function ($w) {
                $w->whereNotNull('lk.noKartu')->where('lk.noKartu', '<>', '');
            });
        }

        if (!empty($filters['unit'])) {
            $q->where('sr.kdppk', $filters['unit']);
        }

        if (!empty($filters['search'])) {
            $s = '%'.$filters['search'].'%';
            $q->where(function ($w) use ($s) {
                $w->where('ps.NIK', 'like', $s)
                  ->orWhere('ps.NAMA_LGKP', 'like', $s)
                  ->orWhere('pr.nmProvider', 'like', $s)
                  ->orWhere('pf.nmPoli', 'like', $s);
            });
        }

        return $q->orderBy('sr.tgl_rujuk', 'desc')
                 ->paginate($perPage)
                 ->withQueryString();
    }
}
