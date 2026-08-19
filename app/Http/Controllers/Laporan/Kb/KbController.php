<?php

namespace App\Http\Controllers\Laporan\Kb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Laporan\Kb\Kb as KbModel; // ← ganti import model

class KbController extends Controller
{
    public function index(Request $req)
    {
        // === Dropdowns ===
        $puskesmas = KbModel::getList();
        $units     = KbModel::getUnits();
        $subunits  = $req->filled('unit_id')
            ? KbModel::getSubUnits($req->unit_id, $req->pusk_id)
            : [];

        $filters = [
            'pusk_id' => $req->pusk_id,
            'awal'    => $req->awal,
            'akhir'   => $req->akhir,
            'unit_id' => $req->unit_id,
            'sub_id'  => $req->sub_id,
        ];

        // === Export CSV (?export=1) ===
        if ($req->boolean('export')) {
            $rows = $this->queryResults($req)->get();
            $filename = 'register_kb_' . date('Ymd_His') . '.csv';

            return new StreamedResponse(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'Tanggal','No MR','NIK','Nama','Umur','Sex','Alamat',
                    'Sistole','Diastole','RR','HR','Keluhan','Keluhan Tambahan',
                    'Kategori','Kasus','Status Pulang','Rujuk'
                ]);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->tanggal, $r->no_mr, $r->nik, $r->nama, $r->umur, $r->sex, $r->alamat,
                        $r->sistole, $r->diastole, $r->rr, $r->hr, $r->keluhan, $r->keluhanTambahan,
                        $r->kategori, $r->kasus, $r->statusPulang, $r->rujukKe
                    ]);
                }
                fclose($out);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        // === Results saat POST ===
        $results = [];
        if ($req->isMethod('post')) {
            $req->validate([
                'pusk_id'  => 'required',
                'awal'     => 'required|date',
                'akhir'    => 'required|date|after_or_equal:awal',
            ]);

            $results = $this->queryResults($req)->get();
        }

        return Inertia::render('Laporan/Kb/Kb', [
            'dropdowns' => [
                'puskesmas' => $puskesmas,
                'units'     => $units,
                'subunits'  => $subunits,
            ],
            'filters' => $filters,
            'results' => $results,
        ]);
    }

    /** Pilih nama kolom yang tersedia di tabel */
    private function pickCol(string $table, array $candidates, $fallback = null)
    {
        foreach ($candidates as $c) {
            if (Schema::hasColumn($table, $c)) {
                return $c;
            }
        }
        return $fallback;
    }

    /** Cek semua kolom ada di tabel */
    private function hasColumns(string $table, array $cols): bool
    {
        foreach ($cols as $c) {
            if (! Schema::hasColumn($table, $c)) return false;
        }
        return true;
    }

    /** Query hasil laporan: deteksi kolom dinamis */
    private function queryResults(Request $req)
    {
        $tbl = 'simpus_loket';

        // kolom inti di loket
        $colDate = $this->pickCol($tbl, ['tglKunjungan','tgl_kunjungan','tanggal']);
        $colPusk = $this->pickCol($tbl, ['puskId','pusk_id']);
        $colMR   = $this->pickCol($tbl, ['pasienId','pasien_id','no_mr']);

        if (!$colDate || !$colPusk || !$colMR) {
            abort(500, "Skema tabel $tbl tidak lengkap (perlu kolom tanggal/pusk_id/pasien).");
        }

        // filter opsional
        $colSvc    = $this->pickCol($tbl, ['id_layanan','layanan_id','unit_id']);
        $colSvcSub = $this->pickCol($tbl, ['id_sub_layanan','id_subunit','subunit_id']);
        $colFaskes = $this->pickCol($tbl, ['id_unit','unitId','id_faskes']);

        $q = DB::table("$tbl as l")
            ->leftJoin('simpus_anamnesa as a', 'a.loketId', '=', 'l.idLoket')
            ->where("l.$colPusk", $req->pusk_id)
            ->whereBetween(DB::raw("DATE(l.$colDate)"), [$req->awal, $req->akhir]);

        if ($colSvc && $req->filled('unit_id') &&
            Schema::hasTable('master_layanan') &&
            DB::table('master_layanan')->where('id', $req->unit_id)->exists()) {
            $q->where("l.$colSvc", $req->unit_id);
        }

        if ($colSvcSub && $req->filled('sub_id') &&
            Schema::hasTable('master_sub_layanan') &&
            DB::table('master_sub_layanan')->where('id', $req->sub_id)->exists()) {
            $q->where("l.$colSvcSub", $req->sub_id);
        }

        if ($colFaskes && $req->filled('sub_id') &&
            Schema::hasTable('data_master_unit_detail') &&
            DB::table('data_master_unit_detail')->where('id_detail', $req->sub_id)->exists()) {
            $q->where("l.$colFaskes", $req->sub_id);
        }

        // KATEGORI: BPJS / NON-BPJS
        $colJns   = $this->pickCol($tbl, ['jnsPeserta','jns_peserta','jenisPeserta']);
        $colProv  = $this->pickCol($tbl, ['kdProvider','kd_provider','providerKartu','provider_kartu']);
        $colKartu = $this->pickCol($tbl, ['noKartu','no_kartu']);

        $kategoriSql = "'NON-BPJS' as kategori";
        if ($colJns || $colProv || $colKartu) {
            $checks = [];
            if ($colJns)   $checks[] = "COALESCE(l.$colJns,'')<>''";
            if ($colProv)  $checks[] = "COALESCE(l.$colProv,'')<>''";
            if ($colKartu) $checks[] = "COALESCE(l.$colKartu,'')<>''";
            $kategoriSql = "CASE WHEN ".implode(' OR ', $checks)." THEN 'BPJS' ELSE 'NON-BPJS' END as kategori";
        }

        // KASUS: baru / lama
        $colKasus = $this->pickCol($tbl, ['kasusBaru','kasus_baru','kunjBaru','kunj_baru']);
        $kasusSql = "'' as kasus";
        if ($colKasus) {
            $kasusSql = "CASE
                WHEN (l.$colKasus IN ('1','Y','y') OR UPPER(l.$colKasus) IN ('BARU','YA','Y'))
                     THEN 'baru'
                ELSE 'lama'
            END as kasus";
        }

        // SELECT
        $selects = [
            "l.$colDate as tanggal",
            "l.$colMR as no_mr",
            "a.sistole",
            "a.diastole",
            "a.respRate as rr",
            "a.heartRate as hr",
            "a.keluhan",
            "a.keluhanTambahan",
            $kategoriSql,
            $kasusSql,
            "'' as statusPulang",
            "'' as rujukKe",
        ];

        // JOIN simpus_pasien (opsional)
        if (Schema::hasTable('simpus_pasien') && Schema::hasColumn('simpus_pasien','ID')) {
            $selects = array_merge($selects, [
                "sp.NIK as nik",
                "sp.NAMA_LGKP as nama",
                "TIMESTAMPDIFF(YEAR, sp.TGL_LHR, CURDATE()) as umur",
                "sp.JENIS_KLMIN as sex",
                "sp.ALAMAT as alamat",
            ]);
            $q->leftJoin('simpus_pasien as sp', 'sp.ID', '=', "l.$colMR");
        } else {
            $selects = array_merge($selects, [
                "'' as nik","'' as nama","NULL as umur","'' as sex","'' as alamat",
            ]);
        }

        return $q->selectRaw(implode(",\n", $selects))
                 ->orderByRaw("DATE(l.$colDate) asc")
                 ->when(Schema::hasColumn($tbl,'idLoket'), fn($qq)=>$qq->orderBy('l.idLoket','asc'));
    }
}
