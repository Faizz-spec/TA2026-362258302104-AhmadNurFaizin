<?php

namespace App\Http\Controllers\Laporan\Ugd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Laporan\Ugd\UgdReport;

class UgdController extends Controller
{
    public function index(Request $request)
    {
        // default tanggal: hari ini kalau kosong
        $awal  = $request->input('awal')  ?: now()->toDateString();
        $akhir = $request->input('akhir') ?: now()->toDateString();

        $filters = [
            'pusk_id'    => $request->integer('pusk_id'),
            'unit_id'    => $request->integer('unit_id'),
            'subunit_id' => $request->integer('subunit_id'),
            'awal'       => $awal,
            'akhir'      => $akhir,
            'jenis'      => $request->input('jenis', 'register-ugd'),
        ];

        // dropdowns
        $puskesmasOptions = DB::table('puskesmas')
            ->selectRaw('PUSK_ID as id, PUSK as nama')
            ->where('AKTIF', 1)
            ->orderBy('PUSK')->get();

        $unitOptions = DB::table('data_master_unit')
            ->selectRaw('id_kategori as id, kategori as nama')
            ->orderBy('kategori')->get();

        $subUnitOptions = DB::table('data_master_unit_detail')
            ->selectRaw('id_detail as id, id_kategori as unit_id, nama_unit as nama')
            ->orderBy('nama_unit')->get();

        // data
        $rows = UgdReport::search($filters);

        // export excel (opsional — sementara JSON biar kelihatan respons)
        if ($request->string('export')->toString() === 'excel') {
            return response()->json($rows);
        }

        // tentukan komponen: filter vs print
        $component = $request->input('view') === 'print'
            ? 'Laporan/Ugd/UgdPrint'
            : 'Laporan/Ugd/Ugd';

        return Inertia::render($component, [
            'filters'          => $filters,
            'puskesmasOptions' => $puskesmasOptions,
            'unitOptions'      => $unitOptions,
            'subUnitOptions'   => $subUnitOptions,
            'rows'             => $rows,
        ]);
    }
}
