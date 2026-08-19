<?php

namespace App\Models\Sanitasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RegisterSanitasi extends Model
{
    public $timestamps = false;

    // ========= DROPDOWNS =========
    public static function getPuskesmas()
    {
        return DB::table('puskesmas')
            ->selectRaw('PUSK_ID as id, PUSK as nama')
            ->orderBy('PUSK')
            ->get(); // 
    }

    public static function getUnits()
    {
        return DB::table('data_master_unit')
            ->selectRaw('id_kategori as id, kategori as nama')
            ->orderBy('kategori')
            ->get(); // 
    }

    public static function getSubUnits($unitId = null)
    {
        $q = DB::table('data_master_unit_detail')
            ->selectRaw('id_detail as id, id_kategori as unit_id, nama_unit as nama')
            ->orderBy('nama_unit');
        if ($unitId) $q->where('id_kategori', $unitId);
        return $q->get(); // 
    }

    public static function getDesa()
    {
        return DB::table('setup_kel_bwi_new')
            ->selectRaw('id, NO_KEL as kode, NAMA_KEL as nama, NO_KEC as no_kec')
            ->orderBy('NAMA_KEL')
            ->get(); // 
    }

    public static function headerInfo(array $p)
    {
        $pusk = null;
        if (!empty($p['pusk_id'])) {
            $pusk = DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first(); // 
        }
        return [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];
    }

    // ========= DATA REGISTER =========
    public static function getData(array $p)
    {
        $q = DB::table('center_view_log as cvl') // 
            ->leftJoin('simpus_anamnesa as ana', 'ana.loketId', '=', 'cvl.loketId') // 
            // TODO: kalau ada tabel pasien, join di sini:
            // ->leftJoin('simpus_pasien as ps', 'ps.no_rm', '=', 'cvl.kd_pasien')
            ->when(!empty($p['pusk_id']), fn($qq) => $qq->where('cvl.pusk_id', $p['pusk_id']))
            ->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']])
            ->selectRaw("
                cvl.tgl_pelayanan as tanggal,
                '' as nama,
                '' as alamat,
                '' as no_bpjs,
                cvl.kd_pasien as no_pasien,
                cvl.gender_pasien as gender,
                TIMESTAMPDIFF(YEAR, cvl.tgllahir_pasien, cvl.tgl_pelayanan) as umur,
                ana.kondisi as hasil_wawancara,
                ana.terapi  as tindakan_saran,
                NULL as intervensi,
                NULL as kader_binaan,
                NULL as kader_risiko
            ")
            ->orderBy('cvl.tgl_pelayanan', 'asc');

        return $q->get();
    }
}
