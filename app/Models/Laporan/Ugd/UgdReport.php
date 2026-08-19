<?php

namespace App\Models\Laporan\Ugd;

use Illuminate\Support\Facades\DB;

class UgdReport
{
    public static function search(array $filters)
    {
        $awal  = $filters['awal']  ?? now()->toDateString();
        $akhir = $filters['akhir'] ?? $awal;

        $q = DB::table('simpus_loket as sl')
            ->leftJoin('simpus_anamnesa as sa', 'sa.loketId', '=', 'sl.idLoket')
            ->leftJoin('pemeriksaan_record_detail as prd', 'prd.id_loket', '=', 'sl.idLoket')
            ->leftJoin('pemeriksaan_record as pr', 'pr.id', '=', 'prd.id_pemeriksaan_record')
            ->selectRaw("
                sl.tglKunjungan AS tanggal,
                sl.pasienId     AS no_mr,
                NULL            AS nik,
                '-'             AS nama_pasien,
                sl.umur         AS umur,
                '-'             AS sex,
                '-'             AS alamat,
                sa.kdSadar      AS kesadaran,
                sa.sistole,
                sa.diastole,
                sa.respRate     AS rr,
                sa.heartRate    AS hr,
                sl.keluhan      AS keluhan_utama,
                sl.jnsPeserta   AS kategori_bayar,
                CASE WHEN sl.kunjBaru='Baru' THEN 'BARU' ELSE 'LAMA' END AS kasus,
                '-'             AS status_pulang,
                pr.reason       AS diagnosis
            ")
            ->whereBetween('sl.tglKunjungan', [$awal, $akhir]);

        if (!empty($filters['pusk_id'])) {
            $q->where('sl.puskId', $filters['pusk_id']);
        }
        if (!empty($filters['unit_id'])) {
            $q->where('sl.unitId', $filters['unit_id']);
        }
        // subunit_id belum ada di skema sl → abaikan dulu

        return $q->orderBy('sl.tglKunjungan')
                 ->limit(5000)
                 ->get()
                 ->map(function ($r) {
                     return [
                        'tanggal'        => $r->tanggal,
                        'no_mr'          => (string) $r->no_mr,
                        'nik'            => $r->nik,
                        'nama_pasien'    => $r->nama_pasien,
                        'umur'           => (string) $r->umur,
                        'sex'            => $r->sex,
                        'alamat'         => $r->alamat,
                        'kesadaran'      => $r->kesadaran,
                        'sistole'        => $r->sistole,
                        'diastole'       => $r->diastole,
                        'rr'             => $r->rr,
                        'hr'             => $r->hr,
                        'keluhan_utama'  => $r->keluhan_utama,
                        'kategori_bayar' => $r->kategori_bayar,
                        'kasus'          => $r->kasus,
                        'status_pulang'  => $r->status_pulang,
                        'diagnosis'      => $r->diagnosis,
                     ];
                 })
                 ->toArray();
    }
}
