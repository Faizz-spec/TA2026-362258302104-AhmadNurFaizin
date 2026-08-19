<?php

namespace App\Http\Controllers\Laporan\Rujukan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\Laporan\Rujukan\Rujukan as RujukanModel;

class RujukanController extends Controller
{
    public function index(Request $request)
    {
        // dropdowns
        $puskesmas = DB::table('puskesmas')
            ->select('PUSK_ID as value', 'PUSK as label')
            ->orderBy('PUSK')
            ->get();

        $units = DB::table('simpus_provider')
            ->select('kdProvider as value', 'nmProvider as label')
            ->orderBy('nmProvider')
            ->get();

        // filters
        $filters = [
            'pusk_id'      => $request->query('pusk_id'),
            'tgl'          => $request->query('tgl'),
            'kepesertaan'  => $request->query('kepesertaan'),
            'unit'         => $request->query('unit'),
            'search'       => $request->query('search'),
            'per_page'     => (int)($request->query('per_page', 10)),
        ];

        return Inertia::render('Laporan/Rujukan/Rujukan', [
            'puskesmasOptions' => $puskesmas,
            'unitOptions'      => $units,
            'filters'          => $filters,
            'rujukan'          => RujukanModel::rujukanPaginate($filters),
        ]);
    }
}
