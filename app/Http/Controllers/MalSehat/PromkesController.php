<?php

namespace App\Http\Controllers\MalSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MalSehat\UnitProfile;
use App\Models\MalSehat\MasterAlergi;
use App\Models\MalSehat\MasterDokter;
use App\Models\MalSehat\MasterDiagnosaKasus;
use App\Models\MalSehat\SimpusPasien;
use App\Models\MalSehat\SimpusDiagnosa;
use App\Models\MalSehat\SimpusMasterTindakan;

class PromkesController extends Controller
{
    public function kesehatanPeduliRemaja()
    {
        $units = UnitProfile::where('id_unit', 1)
            ->select('nama_unit')
            ->get();

        $pasien = SimpusPasien::where('ACTIVE', 1)
            ->select(
                'ID',
                'created',
                'NO_MR as no_mr',
                'NIK as nik',
                'NAMA_LGKP as nama',
                'ALAMAT as alamat',
                'NO_KEL as desa',
                'noKartu as bpjs'
            )
            ->limit(50)
            ->get();

        return Inertia::render('MalSehat/Promkes/KesehatanPeduliRemaja', [
            'units'  => $units,
            'pasien' => $pasien
        ]);
    }

    public function pelayanan($no_mr)
    {
        $pasien = SimpusPasien::where('NO_MR', $no_mr)
            ->select(
                'NO_MR as no_mr',
                'NIK as nik',
                'NAMA_LGKP as nama',
                'ALAMAT as alamat',
                'NO_KEL as desa',
                'noKartu as bpjs',
                'PHONE as phone',
                'TGL_LHR as tgl_lahir',
                'JENIS_KLMIN as jenis_kelamin',
                'ALERGI as alergi',
                'created as tanggal'
            )
            ->first();

        if ($pasien) {
            $birthDate = new \DateTime($pasien->tgl_lahir);
            $today     = new \DateTime();
            $age       = $today->diff($birthDate)->y;

            $jk = ($pasien->jenis_kelamin == 1) ? 'L' : 'P';

            $pasien->umur = $age . ' tahun';
            $pasien->jk   = $jk;
        }

        $alergiMakanan = MasterAlergi::where('category', 1)->get();
        $alergiObat    = MasterAlergi::where('category', 2)->get();
        $dokter        = MasterDokter::select('idDokter', 'nmDokter', 'kdDokter')->get();
        $kasus         = MasterDiagnosaKasus::select('id', 'kasus')->get();

        return Inertia::render('MalSehat/Promkes/Pelayanan', [
            'no_mr'         => $no_mr,
            'alergiMakanan' => $alergiMakanan,
            'alergiObat'    => $alergiObat,
            'dokter'        => $dokter,
            'kasus'         => $kasus,
            'pasien'        => $pasien
        ]);
    }

    public function getDiagnosa(Request $request)
    {
        // ambil query string (?q=xxx)
        $q = $request->get('q');

        $query = SimpusDiagnosa::select(
            'id',
            'kdDiag as kode',
            'nmDiag as nama',
            'kategori_penyakit as keterangan'
        );

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('kdDiag', 'like', "%{$q}%")
                    ->orWhere('nmDiag', 'like', "%{$q}%");
            });
        }

        $diagnosa = $query->limit(27436)->get();

        return response()->json($diagnosa);
    }

    public function getTindakan(Request $request)
    {
        $query = SimpusMasterTindakan::query();

        // optional filter by search
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where('kode', 'like', "%{$search}%")
                ->orWhere('nama_tindakan', 'like', "%{$search}%")
                ->orWhere('nama_tindakan_indonesia', 'like', "%{$search}%");
        }

        $tindakan = $query->orderBy('kode')->get();

        return response()->json($tindakan);
    }
}