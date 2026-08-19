<?php

// app/Http/Controllers/Laporan/LaporanLoketController.php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laporan\Loket;
use App\Models\Laporan\Pasien;

class LaporanLoketController extends Controller
{
    public function index()
    {
        return Inertia::render('Laporan/Loket/Loket');
    }

public function tampil(Request $request)
{
    // Ambil query parameter dari Vue
    $jenis = $request->query('jenis');
    $tglAwal = $request->query('tglAwal');
    $tglAkhir = $request->query('tglAkhir');
    $unit = $request->query('unit');
    $subunit = $request->query('subunit');
    $desa = $request->query('desa');

    // Ambil data Loket + relasi Pasien
    $query = Loket::with('pasien')->latest('tglKunjungan');

    // Filter tanggal kunjungan
    if ($tglAwal && $tglAkhir) {
        $query->whereBetween('tglKunjungan', [$tglAwal, $tglAkhir]);
    }

    // Filter unit jika dipilih
    if ($unit && $unit !== '- Pilih -') {
        $query->where('unit', $unit);
    }

    // Filter sub unit jika dipilih
    if ($subunit && $subunit !== '- Pilih -') {
        $query->where('subunit', $subunit);
    }

    // Filter desa jika dipilih
    if ($desa && $desa !== '- SEMUA -') {
        $query->where('desa', $desa);
    }

    // Ambil 50 data terbaru
    $dataKunjungan = $query->take(50)->get()->map(function ($loket) {
        $pasien = $loket->pasien;

        return [
            'tanggal_kunjungan' => $loket->tglKunjungan,
            'nama' => $pasien?->NAMA_LGKP ?? '-',
            'ALAMAT' => $pasien?->ALAMAT ?? '-',
            'no_bpjs' => $loket->noKartu,
            'no_pasien' => $loket->pasienId,
            'jenis_kelamin' => $pasien?->JENIS_KLMIN == 1 ? 'L' : ($pasien?->JENIS_KLMIN == 2 ? 'P' : '-'),
            'golongan_umur' => $this->hitungGolonganUmur($loket->umur),
            'status' => strtoupper($loket->kunjBaru) === 'BARU' ? 'BARU' : 'LAMA',
        ];
    });

    return Inertia::render('Laporan/Loket/Tampilkan-laporan-loket/Tampilkan-laporan-loket', [
        'jenis' => $jenis,
        'dataKunjungan' => $dataKunjungan,
    ]);
}


    private function hitungGolonganUmur($umur)
    {
        if ($umur < 1) return '<1';
        elseif ($umur <= 4) return '1-4';
        elseif ($umur <= 9) return '5-9';
        elseif ($umur <= 14) return '10-14';
        elseif ($umur <= 19) return '15-19';
        elseif ($umur <= 44) return '20-44';
        elseif ($umur <= 54) return '45-54';
        elseif ($umur <= 59) return '55-59';
        elseif ($umur <= 69) return '60-69';
        else return '>70';
    }

        // ============= 2) REKAP (LAPORAN SANITASI) =============
    public function laporanSanitasi(Request $request)
    {
        $p = $request->validate([
            'pusk_id'     => 'nullable|string',
            'unit_id'     => 'nullable|string',
            'sub_unit_id' => 'nullable|string',
            'desa_id'     => 'nullable|string',
            'awal'        => 'required|date',
            'akhir'       => 'required|date',
        ]);

        $pusk = !empty($p['pusk_id'])
            ? DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first()
            : null;

        $header = [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];

        // agregat per-desa dari log + mapping pasien->desa + (opsional) anamnesa
        $agg = DB::table('center_view_log as cvl')
            ->join('san_pasien_desa as map', 'map.kd_pasien', '=', 'cvl.kd_pasien')
            ->leftJoin('simpus_anamnesa as ana', 'ana.loketId', '=', 'cvl.loketId')
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']])
            ->selectRaw("
                map.no_kel,
                COUNT(*) AS jml_kunjungan,
                SUM(CASE WHEN cvl.kd_penyakit IS NOT NULL THEN 1 ELSE 0 END) AS kasus_lingkungan,
                SUM(CASE WHEN ana.idAnamnesa IS NOT NULL THEN 1 ELSE 0 END)   AS konsul_klinik
            ")
            ->groupBy('map.no_kel');

        $rows = DB::table('setup_kel_bwi_new as kel')
            ->leftJoinSub($agg, 'a', 'a.no_kel', '=', 'kel.NO_KEL')
            ->when(!empty($p['desa_id']), fn($q) => $q->where('kel.NO_KEL', $p['desa_id']))
            ->selectRaw("
                kel.NAMA_KEL AS desa,
                COALESCE(a.jml_kunjungan, 0)    AS jml_kunjungan,
                COALESCE(a.kasus_lingkungan, 0) AS kasus_lingkungan,
                COALESCE(a.konsul_klinik, 0)    AS konsul_klinik,
                0 AS keluarga_binaan,
                0 AS keluarga_risiko,
                0 AS tindak_lanjut
            ")
            ->orderBy('kel.NAMA_KEL')
            ->get();

        return Inertia::render('Laporan/Sanitasi/Reports/RekapSanitasiReport', [
            'header' => $header,
            'rows'   => $rows,
        ]);
    }

    // ============= 3) KASUS (PER PENYAKIT & GENDER) =============
    public function laporanKasus(Request $request)
    {
        $p = $request->validate([
            'pusk_id'     => 'nullable|string',
            'unit_id'     => 'nullable|string',
            'sub_unit_id' => 'nullable|string',
            'desa_id'     => 'nullable|string',
            'awal'        => 'required|date',
            'akhir'       => 'required|date',
        ]);

        $pusk = !empty($p['pusk_id'])
            ? DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first()
            : null;

        $header = [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];

        $agg = DB::table('center_view_log as cvl')
            ->join('san_pasien_desa as map', 'map.kd_pasien', '=', 'cvl.kd_pasien')
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']])
            ->selectRaw("
                map.no_kel,
                SUM(CASE WHEN cvl.kd_penyakit='DIARE'      AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS DIARE_L,
                SUM(CASE WHEN cvl.kd_penyakit='DIARE'      AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS DIARE_P,
                SUM(CASE WHEN cvl.kd_penyakit='KECACINGAN' AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS KECACINGAN_L,
                SUM(CASE WHEN cvl.kd_penyakit='KECACINGAN' AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS KECACINGAN_P,
                SUM(CASE WHEN cvl.kd_penyakit='DHF'        AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS DHF_L,
                SUM(CASE WHEN cvl.kd_penyakit='DHF'        AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS DHF_P,
                SUM(CASE WHEN cvl.kd_penyakit='FILARIASIS' AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS FILARIASIS_L,
                SUM(CASE WHEN cvl.kd_penyakit='FILARIASIS' AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS FILARIASIS_P,
                SUM(CASE WHEN cvl.kd_penyakit='MALARIA'    AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS MALARIA_L,
                SUM(CASE WHEN cvl.kd_penyakit='MALARIA'    AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS MALARIA_P,
                SUM(CASE WHEN cvl.kd_penyakit='TB'         AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS TB_L,
                SUM(CASE WHEN cvl.kd_penyakit='TB'         AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS TB_P,
                SUM(CASE WHEN cvl.kd_penyakit='KUSTA'      AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS KUSTA_L,
                SUM(CASE WHEN cvl.kd_penyakit='KUSTA'      AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS KUSTA_P,
                SUM(CASE WHEN cvl.kd_penyakit='KULIT'      AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS KULIT_L,
                SUM(CASE WHEN cvl.kd_penyakit='KULIT'      AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS KULIT_P,
                SUM(CASE WHEN cvl.kd_penyakit='ISPA'       AND cvl.gender_pasien='L' THEN 1 ELSE 0 END) AS ISPA_L,
                SUM(CASE WHEN cvl.kd_penyakit='ISPA'       AND cvl.gender_pasien='P' THEN 1 ELSE 0 END) AS ISPA_P
            ")
            ->groupBy('map.no_kel');

        $rows = DB::table('setup_kel_bwi_new as kel')
            ->leftJoinSub($agg, 'a', 'a.no_kel', '=', 'kel.NO_KEL')
            ->when(!empty($p['desa_id']), fn($q) => $q->where('kel.NO_KEL', $p['desa_id']))
            ->selectRaw("
                kel.NAMA_KEL AS desa,
                COALESCE(a.DIARE_L,0)      AS DIARE_L,      COALESCE(a.DIARE_P,0)      AS DIARE_P,
                COALESCE(a.KECACINGAN_L,0) AS KECACINGAN_L, COALESCE(a.KECACINGAN_P,0) AS KECACINGAN_P,
                COALESCE(a.DHF_L,0)        AS DHF_L,        COALESCE(a.DHF_P,0)        AS DHF_P,
                COALESCE(a.FILARIASIS_L,0) AS FILARIASIS_L, COALESCE(a.FILARIASIS_P,0) AS FILARIASIS_P,
                COALESCE(a.MALARIA_L,0)    AS MALARIA_L,    COALESCE(a.MALARIA_P,0)    AS MALARIA_P,
                COALESCE(a.TB_L,0)         AS TB_L,         COALESCE(a.TB_P,0)         AS TB_P,
                COALESCE(a.KUSTA_L,0)      AS KUSTA_L,      COALESCE(a.KUSTA_P,0)      AS KUSTA_P,
                COALESCE(a.KULIT_L,0)      AS KULIT_L,      COALESCE(a.KULIT_P,0)      AS KULIT_P,
                COALESCE(a.ISPA_L,0)       AS ISPA_L,       COALESCE(a.ISPA_P,0)       AS ISPA_P
            ")
            ->orderBy('kel.NAMA_KEL')
            ->get();

        return Inertia::render('Laporan/Sanitasi/Reports/KasusSanitasiReport', [
            'header' => $header,
            'rows'   => $rows,
        ]);
    }

}
