<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PengeluaranLangsungController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->unit;

        // 1) Daftar unit
        $units = DB::table('data_master_unit')
            ->select('id_kategori as id', 'kategori as nama')
            ->get();

        // Ambil daftar Sub Unit
        $subUnits = DB::table('data_master_unit_detail')
            ->select('id_detail as id', 'nama_unit as nama', 'id_kategori', 'no_kec')
            ->orderBy('nama_unit');
        
        if ($unitId) {
            $unit = DB::table('data_master_unit')
                ->where('id_kategori', $unitId)
                ->first();

            if ($unit) {
                $subUnits->where('no_kec', $unit->no_kec)
                            ->where('id_kategori', $unitId);
            }
        }

        $subUnits = $subUnits->get();

        // 3) Keperluan dari tabel master_kategori_tindakan
        $keperluanList = DB::table('master_kategori_tindakan')
            ->select('id_kategori as id', 'kategori as nama')
            ->orderBy('kategori')
            ->get();

        // 4) Query utama data pengeluaran
        $query = DB::table('simpus_pakai_obat as spo')
            ->select(
                DB::raw('DATE(spo.createdDate) as tanggal'),
                'u.kategori as unit',
                'spo.sumber as keperluan',
                DB::raw('COUNT(spo.id_pakai) as jumlah_obat')
            )
            ->leftJoin('data_master_unit as u', 'spo.loketId', '=', 'u.id_kategori')
            ->groupBy('tanggal', 'unit', 'keperluan')
            ->orderByDesc('tanggal');

        if ($request->filled('unit')) {
            $query->where('spo.loketId', $request->unit);
        }
        if ($request->filled('keperluan')) {
            $query->where('spo.sumber', $request->keperluan);
        }
        if ($request->filled('periode')) {
            $query->whereMonth('spo.createdDate', substr($request->periode, 5, 2))
                  ->whereYear('spo.createdDate', substr($request->periode, 0, 4));
        }

        $data = $query->get();

        return Inertia::render('Farmasi/PengeluaranLangsung', [
            'units'     => $units,
            'subUnits'  => $subUnits,
            'keperluan' => $keperluanList,
            'dataTable' => $data,
            'filters'   => [
                'unit'      => $request->unit ?? '',
                'subunit'   => $request->subunit ?? '',
                'periode'   => $request->periode ?? '',
                'keperluan' => $request->keperluan ?? '',
            ],
        ]);
    }

    public function getSubUnits(Request $request)
    {
        $unitId = $request->input('unit');

         // Ambil unit utama (agar tahu no_kec-nya)
        $unit = DB::table('data_master_unit')
            ->where('id_kategori', $unitId)
            ->first();

        if (!$unit) {
            return response()->json([]);
        }

        $subUnits = DB::table('data_master_unit_detail')
            ->where('no_kec', $unit->no_kec)
            ->select('id_detail', 'nama_unit')
            ->orderBy('nama_unit')
            ->get();

        return response()->json($subUnits);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal'       => 'required|date',
                'unitId'        => 'required',
                'pengMasterId'  => 'required',
                'keperluan'     => 'required|string',
                'dataObat'      => 'required|array',
            ]);

            foreach ($validated['dataObat'] as $item) {
                DB::table('simpus_pakai_obat')->insert([
                    'resep_id'       => null,
                    'loketId'        => $validated['unitId'],
                    'pelayananId'    => $validated['pengMasterId'],
                    'id_stok_unit'   => null,
                    'obat_id'        => $item['obat_id'] ?? null,
                    'kode_obat'      => $item['kode_obat'] ?? null,
                    'nama_obat'      => $item['nama_obat'] ?? null,
                    'sumber'         => $validated['keperluan'] ?? '-',
                    'tahun'          => date('Y', strtotime($validated['tanggal'])),
                    'harga'          => $item['harga'] ?? 0,
                    'jumlah'         => $item['jumlah'] ?? 0,
                    'dosis_pakai'    => $item['dosis_pakai'] ?? null,
                    'puyer'          => $item['puyer'] ?? null,
                    'nama_puyer'     => $item['nama_puyer'] ?? null,
                    'jumlah_puyer'   => $item['jumlah_puyer'] ?? null,
                    'diambil'        => $item['diambil'] ?? 0,
                    'sample'         => $item['sample'] ?? 0,
                    'setujui'        => $item['setujui'] ?? 1,
                    'createdDate'    => $validated['tanggal'],
                    'createdBy'      => auth()->user()->name ?? 'system',
                    'modifiedDate'   => now(),
                    'modifiedBy'     => auth()->user()->name ?? 'system',
                ]);
            }

            // Ambil data terbaru setelah insert
            $latest = DB::table('simpus_pakai_obat')
                ->orderByDesc('id_pakai')
                ->first();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pengeluaran langsung berhasil disimpan ke tabel simpus_pakai_obat.',
                'data'    => $latest,
            ]);
        } catch (\Throwable $e) {
            \Log::error('GAGAL SIMPAN PENGELUARAN LANGSUNG:', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
