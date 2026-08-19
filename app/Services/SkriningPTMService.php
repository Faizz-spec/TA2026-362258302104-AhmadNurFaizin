<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RuangLayanan\DataMasterUnitDetail;

class SkriningPTMService
{
    public function getDataUnitByAuth()
    {
        $userAuth = Auth::user();
        // dd($userAuth->NO_KEC);
        // dd($userAuth);
        // dd(Auth::user()->getAttributes());

        return DataMasterUnitDetail::with('DataMasterUnit')
            ->where('id_unit', $userAuth->unit)
            ->orderBy('id_kategori')
            ->get();
    }

    public function getDataPasien($request)
    {
        $query = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->join('data_master_unit_detail as unit', 'unit.id_detail', '=', 'l.unitId')
            ->join('data_master_unit as fk', 'fk.id_kategori', '=', 'unit.id_kategori')

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
            ->where('l.kdPoli', '006')
            ->orderByDesc('l.tglKunjungan');

        // dd($query->get('prop.NO_PROP'));

        // Filter berdasarkan inputan pengguna
        if ($request->tanggal_kunjungan) {
            $query->whereDate('l.tglKunjungan', $request->tanggal_kunjungan);
        }
        if ($request->nama_unit) {
            $query->where('unit.nama_unit', $request->nama_unit);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('p.NAMA_LGKP', 'like', '%' . $request->search . '%')
                    ->orWhere('p.NIK', 'like', '%' . $request->search . '%')
                    ->orWhere('p.NO_MR', 'like', '%' . $request->search . '%');
            });
        }

        return $query->select(
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
            'l.kdPoli',
            'pel.startTime',
            'pel.progressTime',
            'unit.id_detail',
            'unit.id_kategori',
            'unit.nama_unit',
            'fk.id_kategori',
            'fk.kategori',
        )->paginate($request->per_page ?? 10)->withQueryString();
    }

    public function getAllData($request)
    {
        return [
            'DataUnit' => $this->getDataUnitByAuth(),
            'DataPasien' => $this->getDataPasien($request),
        ];
    }
}
