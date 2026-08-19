<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Farmasi\MasterObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class MasterObatController extends Controller
{
    // 📌 Ambil semua data obat
    public function index()
    {
        $data = DB::table('simpus_master_obat')
            ->select('OBAT_ID', 'KODE_OBAT', 'NAMA', 'SATUAN')
            ->orderBy('NAMA', 'asc')
            ->get();

        return Inertia::render('Farmasi/MasterObat', [
            'obatList' => $data,
            'today'    => Carbon::now()->format('d-m-Y')
        ]);
    }


    public function create()
    {
        return Inertia::render('Farmasi/MasterObat-Tambah');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'KODE_OBAT' => 'required|unique:simpus_master_obat,KODE_OBAT',
                'NAMA'      => 'required',
                'SATUAN'    => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            MasterObat::create($validator->validated());

            return redirect()->route('farmasi.master-obat.data')
                ->with('success', 'Data obat berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        $query = DB::table('simpus_peng_langsung as p')
            ->leftJoin('data_master_unit as u', 'p.unitId', '=', 'u.id_kategori')
            ->leftJoin('data_master_unit_detail as d', 'p.pengMasterId', '=', 'd.id_kategori')
            ->select(
                'p.id_peng_langsung as id',
                'p.tanggal',
                'u.kategori as unit_nama',
                'd.nama_unit as sub_unit_nama',
                'p.keterangan',
                'p.obat_id',
                'p.kode_obat',
                'p.nama_obat',
                'p.jumlah'
            );

        if ($request->unit) {
            $query->where('p.unitId', $request->unit);
        }
        if ($request->subunit) {
            $query->where('p.pengMasterId', $request->subunit);
        }
        if ($request->keperluan) {
            $query->where('p.keterangan', 'like', "%{$request->keperluan}%");
        }
        if ($request->periode) {
            $query->whereDate('p.tanggal', $request->periode);
        }

        $result = $query->orderBy('p.tanggal', 'desc')->get();

        return response()->json($result);
    }


    // 📌 Tampilkan detail obat berdasarkan id
    public function show($id)
    {
        $obat = MasterObat::findOrFail($id);
        return response()->json($obat, 200);
    }

    // 📌 Update data obat
    public function update(Request $request, $id)
    {
        $obat = MasterObat::findOrFail($id);

        $request->validate([
            'KODE_OBAT' => 'sometimes|required|string|max:50',
            'NAMA' => 'sometimes|required|string|max:255',
            'SATUAN' => 'nullable|string|max:100',
            'JENIS' => 'nullable|string|max:100',
            'GOLONGAN' => 'nullable|string|max:100',
            'SUMBER' => 'nullable|string|max:100',
            'TAHUN' => 'nullable|integer',
            'HARGA' => 'nullable|numeric',
            'ISI' => 'nullable|string|max:50',
            'REK' => 'nullable|string|max:50',
            'AKTIF' => 'nullable|boolean',
            'CREATED_DATE' => 'nullable|date',
        ]);

        $obat->update($request->all());

        return response()->json([
            'message' => 'Obat berhasil diperbarui',
            'data' => $obat
        ]);
    }

    // 📌 Hapus obat
    public function destroy($id)
    {
        MasterObat::destroy($id);
        return response()->json(['message' => 'Obat berhasil dihapus'], 200);
    }
}
