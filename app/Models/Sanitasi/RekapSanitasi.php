<?php

namespace App\Models\Sanitasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RekapSanitasi extends Model
{
    public $timestamps = false;

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

    /**
     * Rekap per desa:
     * - Jumlah Kunjungan Pasien (count distinct kd_pasien per desa)
     * - Kasus berbasis lingkungan (contoh: kd_penyakit IN (...) -> silakan sesuaikan)
     * - Pasien yg Konsul ke Klinik Sanitasi (proxy: ada data anamnesa)
     * - Kolom % = pembilang / kunjungan * 100
     * - Kolom luar wilayah & luar Banyuwangi bisa diisi aturan sesuai kode desa (placeholder).
     */
    public static function getData(array $p)
    {
        // subquery jumlah kunjungan per desa
        // Catatan: di schema yang tampak, tidak ada mapping eksplisit KD_PASIEN -> DESA.
        // Jika ada tabel pasien, ubah join di bawah.
        $kunjungan = DB::table('center_view_log as cvl') // 
            ->leftJoin('simpus_anamnesa as ana', 'ana.loketId', '=', 'cvl.loketId') // 
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']])
            ->selectRaw("
                '' as desa, -- TODO: ganti jika ada mapping pasien -> desa
                COUNT(*) as jml_kunjungan,
                SUM(CASE WHEN cvl.kd_penyakit IS NOT NULL THEN 1 ELSE 0 END) as kasus_lingkungan,
                SUM(CASE WHEN ana.idAnamnesa IS NOT NULL THEN 1 ELSE 0 END) as konsul_klinik
            ")
            ->groupBy('desa');

        // ambil daftar desa resmi agar urutan sesuai
        $desaList = DB::table('setup_kel_bwi_new')
            ->selectRaw('NAMA_KEL as desa')
            ->orderBy('NAMA_KEL')
            ->get(); // 

        // gabungkan manual (karena kita tidak punya mapping desa di log)
        $rows = [];
        foreach ($desaList as $d) {
            $rows[] = [
                'desa'               => $d->desa,
                'jml_kunjungan'      => 0,
                'kasus_lingkungan'   => 0,
                'persen_kasus'       => 0.0,
                'konsul_klinik'      => 0,
                'persen_konsul'      => 0.0,
                'keluarga_binaan'    => 0,
                'keluarga_risiko'    => 0,
                'tindak_lanjut'      => 0,
                'persen_tindak'      => 0.0,
            ];
        }

        // NOTE:
        // Begitu kamu punya kolom desa pada pasien/kunjungan, ganti query $kunjungan dengan join ke tabel itu,
        // lalu merge hasilnya ke $rows sesuai nama desa.

        return $rows;
    }
}
