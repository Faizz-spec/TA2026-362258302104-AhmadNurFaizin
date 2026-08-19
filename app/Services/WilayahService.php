<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WilayahService
{
    /**
     * Get pasien data with wilayah names
     */
    public function getPasienWithWilayah($pasienId = null)
    {
        $query = DB::table('simpus_pasien as p')
            ->select(
                'p.*',
                'kec.NAMA_KEC as nama_kecamatan',
                'kel.NAMA_KEL as nama_kelurahan'
            )
            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('kec.NO_KEC', '=', 'p.NO_KEC')
                    ->on('kec.NO_KAB', '=', 'p.NO_KAB')
                    ->on('kec.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('kel.NO_KEL', '=', 'p.NO_KEL')
                    ->on('kel.NO_KEC', '=', 'p.NO_KEC')
                    ->on('kel.NO_KAB', '=', 'p.NO_KAB')
                    ->on('kel.NO_PROP', '=', 'p.NO_PROP');
            });

        if ($pasienId) {
            return $query->where('p.ID', $pasienId)->first();
        }

        return $query;
    }

    /**
     * Get loket data with pasien and wilayah
     */
    public function getLoketWithWilayah($paginate = 10)
    {
        return DB::table('simpus_loket as l')
            ->select(
                'l.*',
                'p.ID as pasien_id',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.ALAMAT',
                'p.NO_RT',
                'p.NO_RW',
                'p.NO_PROP',
                'p.NO_KAB',
                'p.NO_KEC',
                'p.NO_KEL',
                'p.PHONE',
                'p.TGL_LHR',
                DB::raw('TIMESTAMPDIFF(YEAR, p.TGL_LHR, CURDATE()) as umur_tahun'),
                'l.noKartu',
                'l.statusKartu',
                'l.kdProvider',
                'kec.NAMA_KEC as nama_kecamatan',
                'kel.NAMA_KEL as nama_kelurahan',
                'po.nmPoli',
                'ud.nama_unit',
                'mu.kategori as kategori_unit'
            )
            ->leftJoin('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('kec.NO_KEC', '=', 'p.NO_KEC')
                    ->on('kec.NO_KAB', '=', 'p.NO_KAB')
                    ->on('kec.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('kel.NO_KEL', '=', 'p.NO_KEL')
                    ->on('kel.NO_KEC', '=', 'p.NO_KEC')
                    ->on('kel.NO_KAB', '=', 'p.NO_KAB')
                    ->on('kel.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('simpus_poli_fktp as po', 'l.kdPoli', '=', 'po.kdPoli')
            ->leftJoin('data_master_unit_detail as ud', 'l.unitId', '=', 'ud.id_detail')
            ->leftJoin('data_master_unit as mu', 'ud.id_kategori', '=', 'mu.id_kategori')
            ->whereDate('l.tglKunjungan', '=', now()->toDateString())
            ->orderBy('l.tglKunjungan', 'desc')
            ->paginate($paginate);
    }

    /**
     * Search pasien with wilayah
     */
    public function searchPasienWithWilayah($filters = [], $limit = 50)
    {
        $query = $this->getPasienWithWilayah();

        if (!empty($filters['nama'])) {
            $query->where('p.NAMA_LGKP', 'like', '%' . $filters['nama'] . '%');
        }

        if (!empty($filters['nik'])) {
            $query->where('p.NIK', $filters['nik']);
        }

        if (!empty($filters['no_mr'])) {
            $query->where('p.NO_MR', $filters['no_mr']);
        }

        if (!empty($filters['no_bpjs'])) {
            $query->where('p.noKartu', $filters['no_bpjs']);
        }

        return $query->limit($limit)->get();
    }
}
