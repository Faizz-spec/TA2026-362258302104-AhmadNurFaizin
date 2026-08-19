<?php

namespace App\Http\Controllers\Filter;

use App\Http\Controllers\Controller;

use App\Models\Filter\SimpusLoket;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;



class FilterController extends Controller
{
    
    public function index()
    {
         $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')

            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })

            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })

            ->leftJoin('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })

            ->leftJoin('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')

            ->where('l.kdPoli', '003')

            ->select(
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kdPoli'
            )
            ->get();
        // $DataPasien = DB::table("simpus_loket as l")
        // ->join
        // $providers = SimpusProvider::all();
        // $data_master_unit = DataMasterUnit::with(['detail'])->get();
        // $data_master_unit_detail = DataMasterUnitDetail::all();
        // $kunj_kasus = DiagnosaKasus::all();
        // $asal = MasterWilayah::all();
        // $kecamatan = SetupKecamatan::where('no_kab', 10)
        //     ->where('no_prop', 35)
        //     ->get();
        // $desa = SetupDesa::where('no_kab', 10)
        //     ->where('no_prop', 35)
        //     ->get();
        // $unit = UnitFilter::where('pelayanan', 'TRUE')->get();
        // $kunjungan = SimpusKunjungan::all();
        $data = SimpusLoket::with([
            'pasien',
            'anamnesa',
            'obat',
            'kunjungan'
        ])->get();

        // $diagnosa = SimpusDiagnosa::all();
        // $kecamatan = SimpusDiagnosa::where('id', 1)
        //     ->get();

        // dd($diagnosa);

        // dd($data);
        // dd($data->first()->toArray());

        // dd($DataPasien);

        return Inertia::render('Filter/card', [
            'rekamMedis' => $data,
            'dataPasien' => $DataPasien
        ]);
    }

    // public function index()
    // {
    //     $providers = SimpusProvider::all();
    //     $data_master_unit = DataMasterUnit::with(['detail'])->get();
    //     $data_master_unit_detail = DataMasterUnitDetail::all();
    //     $kunj_kasus = DiagnosaKasus::all();
    //     $asal = MasterWilayah::all();
    //     $kecamatan = SetupKecamatan::where('no_kab', 10)
    //         ->where('no_prop', 35)
    //         ->get();
    //     $desa = SetupDesa::where('no_kab', 10)
    //         ->where('no_prop', 35)
    //         ->get();
    //     $unit = UnitFilter::where('pelayanan', 'TRUE')->get();
    //     $kunjungan = SimpusKunjungan::all();
    //     $data = SimpusLoket::with([
    //         'pasien',
    //         'anamnesa',
    //         'obat',
    //         'kunjungan'
    //     ])->get();
    //     // $loket = \App\Models\Filter\SimpusLoket::with('anamnesa')->first();
    //     // dd($loket->idLoket, $loket->anamnesa);
    //     // dd(SimpusLoket::with('anamnesa')->first()->toArray());

    //     return Inertia::render('Filter/Index', [
    //         'providers' => $providers,
    //         'tempat_kunjungan' => $data_master_unit,
    //         'detail_tempat_kunjungan' => $data_master_unit_detail,
    //         'kunj_kasus' => $kunj_kasus,
    //         'asal' => $asal,
    //         'kecamatan' => $kecamatan,
    //         'desa' => $desa,
    //         'unit' => $unit,
    //         'kunjungan' => $kunjungan,
    //         'rekamMedis' => $data
    //     ]);
    // }
    //
    // public function modal()
    // {
    //     return Inertia::render('Filter/Modal');
    // }
}
