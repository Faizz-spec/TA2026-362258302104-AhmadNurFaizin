<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\RuangLayanan\SimpusTindakan;

class TindakanService
{
    public function getTindakanPelayanan($idPelayanan)
    {
        $dataTindakan = SimpusTindakan::where('idPelayanan', $idPelayanan)
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'simpus_tindakan.kdPoli')
            ->select('simpus_tindakan.*', 'poli.nmPoli')
            ->get();

        return $dataTindakan;
    }

    public function svTindakan($data)
    {
        $dataTindakan = [
            'idPelayanan' => $data['idpelayanan'],
            'kdTindakan' => $data['kode_tindakan'],
            'nmTindakan' => $data['nama_tindakan'],
            'nmTindakanInd' => $data['nama_tindakan_ind'],
            'keterangan' => $data['keterangan'],
            'loketId' => $data['loketId'],
            'kdPoli' => $data['kdPoli'],
            'deskripsi' => 'icd9cm',
            'createdDate' => now(),
        ];
        // dd($dataTindakan);
        return SimpusTindakan::create($dataTindakan);
    }
    public function hapusTindakan($id)
    {
        $tindakan = SimpusTindakan::find($id);

        if (!$tindakan) {
            return [
                'success' => false,
                'message' => 'Data tindakan tidak ditemukan.',
            ];
        }

        $tindakan->delete();

        return [
            'success' => true,
            'message' => 'Data tindakan berhasil dihapus.',
        ];
    }
}
