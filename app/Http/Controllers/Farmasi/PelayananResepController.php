<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PelayananResepController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar Unit
        $units = DB::table('data_master_unit')
            ->select('id_kategori as id', 'kategori as nama')
            ->get();

        // Ambil daftar Sub Unit
        $subUnits = DB::table('data_master_unit_detail')
            ->select('id_detail as id', 'nama_unit as nama', 'id_kategori')
            ->where('no_kec', '=', '18')
            ->get();

        // Query data pasien + resep
        $query = DB::table('simpus_pakai_obat as po')
            ->join('simpus_pasien as p', 'p.ID', '=', 'po.pelayananId')
            ->join('simpus_loket as l', 'l.idLoket', '=', 'po.loketId')
            ->join('simpus_obat_stok_unit as su', 'su.id_stok_unit', '=', 'po.id_stok_unit')
            ->select(
                'po.id_pakai as id',
                'p.NO_MR as no_rm',
                'p.NAMA_LGKP as pasien',
                'p.ALAMAT as alamat',
                'l.kdPoli as poli',
                'po.nama_obat',
                'po.jumlah',
                'po.dosis_pakai as dosis',
                'su.jumlah as stok_unit',
                'po.diambil as status_resep',
                'po.createdDate as created_at'
            );

        // Filter Unit
        if ($request->filled('unit')) {
            $query->where('l.unitId', $request->unit);
        }

        // Filter Sub Unit
        if ($request->filled('sub_unit')) {
            $query->where('l.ukkId', $request->sub_unit);
        }

        // Filter Periode (tanggal resep)
        if ($request->filled('periode')) {
            $query->whereDate('po.createdDate', $request->periode);
        }

        $data = $query->get();

        return Inertia::render('Farmasi/PelayananResep', [
            'units'    => $units,
            'subUnits' => $subUnits,
            'data'     => $data,
            'filters'  => $request->only(['unit', 'sub_unit', 'periode'])
        ]);
    }
}
