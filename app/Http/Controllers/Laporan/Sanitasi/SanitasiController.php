<?php

namespace App\Http\Controllers\Laporan\Sanitasi;  // ✅ baru

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class SanitasiController extends Controller
{
    // Halaman filter + dropdown
    public function index()
    {
        $puskesmasOptions = DB::table('puskesmas')
            ->selectRaw('PUSK_ID as id, PUSK as nama')
            ->orderBy('PUSK')->get();

        $unitOptions = DB::table('data_master_unit')
            ->selectRaw('id_kategori as id, kategori as nama')
            ->orderBy('kategori')->get();

        $subUnitOptions = DB::table('data_master_unit_detail')
            ->selectRaw('id_detail as id, id_kategori as unit_id, nama_unit as nama')
            ->orderBy('nama_unit')->get();

        $desaOptions = DB::table('setup_kel_bwi_new')
            ->selectRaw('NO_KEL as id, NAMA_KEL as nama')
            ->orderBy('NAMA_KEL')->get();

        return Inertia::render('Laporan/Sanitasi/Sanitasi', [
            'puskesmasOptions' => $puskesmasOptions,
            'unitOptions'      => $unitOptions,
            'subUnitOptions'   => $subUnitOptions,
            'desaOptions'      => $desaOptions,
        ]);
    }

    // ====== 1) REGISTER SANITASI ======
    public function registerSanitasi(Request $request)
    {
        $p = $request->validate([
            'pusk_id'     => 'nullable|string',
            'unit_id'     => 'nullable|string',
            'sub_unit_id' => 'nullable|string',
            'desa_id'     => 'nullable|string',
            'awal'        => 'required|date',
            'akhir'       => 'required|date',
            'download'    => 'nullable|string',
        ]);

        $pusk = !empty($p['pusk_id'])
            ? DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first()
            : null;

        $header = [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk?->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];

        $rowsDb = DB::table('center_view_log as cvl')
            ->leftJoin('simpus_anamnesa as ana', 'ana.loketId', '=', 'cvl.loketId')
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->when(!empty($p['awal']) && !empty($p['akhir']),
                fn($q) => $q->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']]))
            ->selectRaw("
                cvl.tgl_pelayanan as tanggal,
                '' as nama, '' as alamat, '' as no_bpjs,
                cvl.kd_pasien as no_pasien,
                cvl.gender_pasien as gender,
                TIMESTAMPDIFF(YEAR, cvl.tgllahir_pasien, cvl.tgl_pelayanan) as umur,
                ana.kondisi as hasil_wawancara,
                ana.terapi  as tindakan_saran,
                NULL as intervensi, NULL as kader_binaan, NULL as kader_risiko
            ")
            ->orderBy('cvl.tgl_pelayanan', 'asc')
            ->get();

        if (($p['download'] ?? null) === 'excel') {
            $headings = [
                'No','Tanggal','No Pasien','Gender','Umur',
                'Hasil Wawancara','Tindakan/Saran','Intervensi','Kader Binaan','Kader Risiko'
            ];
            $rows = [];
            foreach ($rowsDb as $i => $r) {
                $rows[] = [
                    $i + 1,
                    $r->tanggal,
                    $r->no_pasien,
                    $r->gender,
                    $r->umur,
                    $r->hasil_wawancara,
                    $r->tindakan_saran,
                    $r->intervensi,
                    $r->kader_binaan,
                    $r->kader_risiko,
                ];
            }
            $filename = 'laporan_sanitasi_REGISTER_'.$p['awal'].'_sd_'.$p['akhir'];
            return $this->csv($headings, $rows, $filename);
        }

        return Inertia::render('Laporan/Sanitasi/Reports/RegisterSanitasiReport', [
            'header' => $header,
            'rows'   => $rowsDb,
        ]);
    }

    // ====== 2) REKAP (LAPORAN SANITASI) ======
    public function laporanSanitasi(Request $request)
    {
        $p = $request->validate([
            'pusk_id'     => 'nullable|string',
            'unit_id'     => 'nullable|string',
            'sub_unit_id' => 'nullable|string',
            'desa_id'     => 'nullable|string',
            'awal'        => 'required|date',
            'akhir'       => 'required|date',
            'download'    => 'nullable|string',
        ]);

        $pusk = !empty($p['pusk_id'])
            ? DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first()
            : null;

        $header = [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk?->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];

        $agg = DB::table('center_view_log as cvl')
            ->join('san_pasien_desa as map', 'map.kd_pasien', '=', 'cvl.kd_pasien')
            ->leftJoin('simpus_anamnesa as ana', 'ana.loketId', '=', 'cvl.loketId')
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->when(!empty($p['awal']) && !empty($p['akhir']),
                fn($q) => $q->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']]))
            ->selectRaw("
                map.no_kel,
                COUNT(*) AS jml_kunjungan,
                SUM(CASE WHEN cvl.kd_penyakit IS NOT NULL THEN 1 ELSE 0 END) AS kasus_lingkungan,
                SUM(CASE WHEN ana.idAnamnesa IS NOT NULL THEN 1 ELSE 0 END)   AS konsul_klinik
            ")
            ->groupBy('map.no_kel');

        $rowsDb = DB::table('setup_kel_bwi_new as kel')
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

        if (($p['download'] ?? null) === 'excel') {
            $headings = [
                'No','Desa','Jml Kunjungan','Kasus Lingkungan',
                'Konsul Klinik','Keluarga Binaan','Keluarga Risiko','Tindak Lanjut'
            ];
            $rows = [];
            foreach ($rowsDb as $i => $r) {
                $rows[] = [
                    $i + 1,
                    $r->desa,
                    $r->jml_kunjungan,
                    $r->kasus_lingkungan,
                    $r->konsul_klinik,
                    $r->keluarga_binaan,
                    $r->keluarga_risiko,
                    $r->tindak_lanjut,
                ];
            }
            $filename = 'laporan_sanitasi_REKAP_'.$p['awal'].'_sd_'.$p['akhir'];
            return $this->csv($headings, $rows, $filename);
        }

        return Inertia::render('Laporan/Sanitasi/Reports/RekapSanitasiReport', [
            'header' => $header,
            'rows'   => $rowsDb,
        ]);
    }

    // ====== 3) KASUS (per penyakit & gender) ======
    public function laporanKasus(Request $request)
    {
        $p = $request->validate([
            'pusk_id'     => 'nullable|string',
            'unit_id'     => 'nullable|string',
            'sub_unit_id' => 'nullable|string',
            'desa_id'     => 'nullable|string',
            'awal'        => 'required|date',
            'akhir'       => 'required|date',
            'download'    => 'nullable|string',
        ]);

        $pusk = !empty($p['pusk_id'])
            ? DB::table('puskesmas')->where('PUSK_ID', $p['pusk_id'])->first()
            : null;

        $header = [
            'unit'      => 'SEMUA UNIT',
            'nama_unit' => 'REKAP GABUNGAN - ' . ($pusk?->PUSK ?? 'PUSKESMAS'),
            'awal'      => $p['awal'],
            'akhir'     => $p['akhir'],
        ];

        $agg = DB::table('center_view_log as cvl')
            ->join('san_pasien_desa as map', 'map.kd_pasien', '=', 'cvl.kd_pasien')
            ->when(!empty($p['pusk_id']), fn($q) => $q->where('cvl.pusk_id', $p['pusk_id']))
            ->when(!empty($p['awal']) && !empty($p['akhir']),
                fn($q) => $q->whereBetween('cvl.tgl_pelayanan', [$p['awal'], $p['akhir']]))
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

        $rowsDb = DB::table('setup_kel_bwi_new as kel')
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

        if (($p['download'] ?? null) === 'excel') {
            $headings = [
                'No','Desa',
                'Diare L','Diare P','Kecacingan L','Kecacingan P','DHF L','DHF P',
                'Filariasis L','Filariasis P','Malaria L','Malaria P','TB L','TB P',
                'Kusta L','Kusta P','Kulit L','Kulit P','ISPA L','ISPA P',
            ];
            $rows = [];
            foreach ($rowsDb as $i => $r) {
                $rows[] = [
                    $i + 1,
                    $r->desa,
                    $r->DIARE_L, $r->DIARE_P,
                    $r->KECACINGAN_L, $r->KECACINGAN_P,
                    $r->DHF_L, $r->DHF_P,
                    $r->FILARIASIS_L, $r->FILARIASIS_P,
                    $r->MALARIA_L, $r->MALARIA_P,
                    $r->TB_L, $r->TB_P,
                    $r->KUSTA_L, $r->KUSTA_P,
                    $r->KULIT_L, $r->KULIT_P,
                    $r->ISPA_L, $r->ISPA_P,
                ];
            }
            $filename = 'laporan_sanitasi_KASUS_'.$p['awal'].'_sd_'.$p['akhir'];
            return $this->csv($headings, $rows, $filename);
        }

        return Inertia::render('Laporan/Sanitasi/Reports/KasusSanitasiReport', [
            'header' => $header,
            'rows'   => $rowsDb,
        ]);
    }

    /**
     * Stream CSV ke browser (tanpa paket tambahan).
     * - Menulis BOM UTF-8 agar huruf aksen/Indonesia tampil benar di Excel.
     */
    private function csv(array $headings, array $rows, string $baseFilename)
    {
        $filename = $baseFilename.'_'.Carbon::now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');

            // Tulis BOM UTF-8 untuk Excel Windows
            echo "\xEF\xBB\xBF";

            // Header
            fputcsv($out, $headings);

            // Data
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
