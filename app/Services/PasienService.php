<?php

namespace App\Services;

use App\Models\Pasien\Pasien;
use App\Models\Pasien\Agama;
use App\Models\Pasien\HubKeluarga;
use App\Models\Pasien\Provinsi;
use App\Models\Pasien\Kabupaten;
use App\Models\Pasien\Kecamatan;
use App\Models\Pasien\Kelurahan;
use App\Models\Pasien\MasterPekerjaan;

class PasienService
{
    public function getPasien($search = null)
    {
        return Pasien::query()
            ->leftJoin('setup_kec', function ($join) {
                $join->on('setup_kec.NO_KEC', '=', 'simpus_pasien.NO_KEC')
                    ->on('setup_kec.NO_KAB', '=', 'simpus_pasien.NO_KAB')
                    ->on('setup_kec.NO_PROP', '=', 'simpus_pasien.NO_PROP');
            })
            ->leftJoin('setup_kel', function ($join) {
                $join->on('setup_kel.NO_KEL', '=', 'simpus_pasien.NO_KEL')
                    ->on('setup_kel.NO_KEC', '=', 'simpus_pasien.NO_KEC')
                    ->on('setup_kel.NO_KAB', '=', 'simpus_pasien.NO_KAB')
                    ->on('setup_kel.NO_PROP', '=', 'simpus_pasien.NO_PROP');
            })
            ->select(
                'simpus_pasien.*',
                'setup_kec.NAMA_KEC as nama_kecamatan',
                'setup_kel.NAMA_KEL as nama_kelurahan',
            )
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('simpus_pasien.NAMA_LNGKP', 'like', "%$search%")
                        ->orWhere('simpus_pasien.NO_MR', 'like', "%$search%")
                        ->orWhere('simpus_pasien.NIK', 'like', "%$search%");
                });
            })
            ->get();
    }
    public function getEditData($id)
    {
        $pasien = Pasien::findOrFail($id);

        return [
            'pasien' => $pasien,
            'agama' => Agama::all(),
            'pekerjaan' => MasterPekerjaan::all(),
            'hubKeluarga' => HubKeluarga::all(),
            'provinsi' => Provinsi::all(),
            'kabupaten' => Kabupaten::where('NO_PROP', $pasien->NO_PROP)->get(),

            'kecamatan' => Kecamatan::where('NO_PROP', $pasien->NO_PROP)
                ->where('NO_KAB', $pasien->NO_KAB)
                ->get(),

            'kelurahan' => Kelurahan::where('NO_PROP', $pasien->NO_PROP)
                ->where('NO_KAB', $pasien->NO_KAB)
                ->where('NO_KEC', $pasien->NO_KEC)
                ->get(),
        ];
    }

    public function updatePasien($id, array $data)
    {
        $pasien = Pasien::findOrFail($id);

        $pasien->update([
            'NAMA_LGKP' => $data['nama'],
            'NIK' => $data['nik'],
            'ALAMAT' => $data['alamat'],
            'TGL_LHR' => $data['tanggal_lahir'],
            'NO_PROP' => $data['provinsi'],
            'NO_KAB' => $data['kabupaten'],
            'NO_KEC' => $data['kecamatan'],
            'NO_KEL' => $data['kelurahan'],
        ]);

        return $pasien;
    }

    public function getKabupatenByProvinsi($provinsi)
    {
        return Kabupaten::where('NO_PROP', $provinsi)->get();
    }
    public function getKecamatanByKabupaten($provinsi, $kabupaten)
    {
        return Kecamatan::where('NO_PROP', $provinsi)
            ->where('NO_KAB', $kabupaten)
            ->get();
    }
    public function getKelurahanByKecamatan($provinsi, $kabupaten, $kecamatan)
    {
        return Kelurahan::where('NO_PROP', $provinsi)
            ->where('NO_KAB', $kabupaten)
            ->where('NO_KEC', $kecamatan)
            ->get();
    }
}
