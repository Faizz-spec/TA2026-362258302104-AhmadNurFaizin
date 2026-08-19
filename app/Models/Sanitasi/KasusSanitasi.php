<?php

namespace App\Models\Sanitasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KasusSanitasi extends Model
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
     * Hitung kasus per desa & jenis penyakit, dipisah L/P.
     * Mapping kode penyakit -> kolom (Diare, Kecacingan, DHF, ...).
     * Ganti KODE_* sesuai kode di `kd_penyakit`.
     */
    public static function getData(array $p)
    {
        $map = [
            'DIARE'       => ['Diare'],
            'KECACINGAN'  => ['Kecacingan'],
            'DHF'         => ['DHF'],
            'FILARIASIS'  => ['Filariasis'],
            'MALARIA'     => ['Malaria'],
            'TB'          => ['TB Paru'],
            'KUSTA'       => ['Kusta'],
            'KULIT'       => ['Kulit'],
            'ISPA'        => ['ISPA'],
        ];

        // ambil desa list dulu
        $desaList = DB::table('setup_kel_bwi_new')
            ->selectRaw('NAMA_KEL as desa')
            ->orderBy('NAMA_KEL')
            ->get(); // 

        // init nol
        $rows = [];
        foreach ($desaList as $d) {
            $base = [
                'desa' => $d->desa,
                'total_L' => 0, 'total_P' => 0,
            ];
            foreach ($map as $k => $label) {
                $base[$k.'_L'] = 0;
                $base[$k.'_P'] = 0;
            }
            $rows[$d->desa] = $base;
        }

        // Ambil data log (belum ada kolom desa di log -> butuh mapping)
        $q = DB::table('center_view_log as cvl')
            ->when(!empty($p['pusk_id']), fn($qq) => $qq->where('cvl.pusk_id', $p['pusk_id']))
            ->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']])
            ->selectRaw("cvl.kd_penyakit, cvl.gender_pasien as gender, '' as desa") // TODO: isi desa dari tabel pasien
            ->get(); // 

        foreach ($q as $r) {
            $desa = $r->desa ?: ($desaList[0]->desa ?? ''); // fallback biar gak error
            $genderKey = ($r->gender === 'L') ? '_L' : '_P';

            // mapping penyakit -> kolom
            foreach ($map as $code => $label) {
                if ($r->kd_penyakit && stripos($r->kd_penyakit, $code) !== false) {
                    $rows[$desa][$code.$genderKey]++;
                    $rows[$desa]['total'. $genderKey]++;
                }
            }
        }

        return array_values($rows);
    }
}
