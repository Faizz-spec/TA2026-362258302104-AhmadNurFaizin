<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Services\PasienService;

class PasienController extends Controller
{
    protected $pasienService;
    public function __construct(PasienService $pasienService)
    {
        $this->pasienService = $pasienService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $pasien = $this->pasienService->getPasien($search);

        return Inertia::render('Loket/SearchPasien', [
            'pasien' => $pasien,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }
    public function edit($id)
    {
        return Inertia::render('Loket/EditPasien', $this->pasienService->getEditData($id));
    }
    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:60',
            'nik' => 'required|string|max:16',
            'alamat' => 'required|string|max:160',
            'tanggal_lahir' => 'required|date',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
        ])->validate();
        $this->pasienService->updatePasien($id, $validated);
        return Inertia::location(route('loket.search'));
    }
    
    public function getKabupaten($provinsi){
        return response()->json(
            $this->pasienService->getKabupatenByProvinsi($provinsi)
        );
    }

    public function getKecamatan($provinsi, $kabupaten){
        return response()->json(
            $this->pasienService->getKecamatanByKabupaten($provinsi, $kabupaten)
        );
    }

    public function getKelurahan($provinsi, $kabupaten, $kecamatan){
        return response()->json(
            $this->pasienService->getKelurahanByKecamatan($provinsi, $kabupaten, $kecamatan)
        );
    }
}