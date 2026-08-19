<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\DataMasterUnitDetail;
use App\Models\RuangLayanan\MasterRiwayat;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\RuangLayanan\SimpusDataDiagnosa;
use App\Models\RuangLayanan\SimpusDiagnosa;
use App\Models\RuangLayanan\SimpusLoket;
use App\Models\RuangLayanan\SimpusPelayanan;
use App\Models\RuangLayanan\SimpusTindakan;
use App\Models\RuangLayanan\tindakan;
use Inertia\Inertia;

class PoliKIAController extends Controller
{
    // public function index()
    // {
    //     $DataUnit = DataMasterUnitDetail::with('DataMasterUnit')
    //         ->where('no_kec', 18)
    //         ->orderBy('id_kategori')
    //         ->get();

    //     $DataPasien = DB::table('simpus_loket as l')
    //         ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
    //         ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
    //         ->join('setup_kel as kel', function ($join) {
    //             $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
    //                 ->on('p.NO_KEC', '=', 'kel.NO_KEC')
    //                 ->on('p.NO_KAB', '=', 'kel.NO_KAB')
    //                 ->on('p.NO_PROP', '=', 'kel.NO_PROP');
    //         })
    //         ->join('setup_kec as kec', function ($join) {
    //             $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
    //                 ->on('p.NO_KAB', '=', 'kec.NO_KAB')
    //                 ->on('p.NO_PROP', '=', 'kec.NO_PROP');
    //         })
    //         ->join('setup_kab as kab', function ($join) {
    //             $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
    //                 ->on('p.NO_PROP', '=', 'kab.NO_PROP');
    //         })
    //         ->join('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')
    //         ->where('l.kdPoli', '=', '003')
    //         ->select(
    //             'p.NO_MR',
    //             'p.NAMA_LGKP',
    //             'p.NIK',
    //             'kel.nama_kel',
    //             'kec.nama_kec',
    //             'kab.nama_kab',
    //             'prop.nama_prop',
    //             'poli.nmPoli',
    //             'p.alamat',
    //             'p.no_rt',
    //             'p.no_rw',
    //             'l.tglKunjungan',
    //             'l.idLoket'
    //         )
    //         ->take(100)
    //         ->get();

    //     // dd($DataPasien);

    //     return Inertia::render('Ruang_Layanan/KIA/index', [
    //         'DataUnit' => $DataUnit,
    //         'DataPasien' => $DataPasien,
    //     ]);
    // }
    public function index()
    {
        $totalPasienKIA = SimpusPelayanan::where('kdPoli', '003')
            ->whereHas('SimpusLoket')
            ->count();


        // dd($totalPasienKIA);

        return Inertia::render('Ruang_Layanan/KIA/index', [
            'totalPasienKIA' => $totalPasienKIA,
        ]);
    }
}
