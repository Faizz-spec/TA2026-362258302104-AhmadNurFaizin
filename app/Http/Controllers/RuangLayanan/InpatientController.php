<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InpatientController extends Controller
{
    // Validasi idKunjungan dan ambil puskId dari loket; pastikan kdPoli=098
    private function validateEpisodeAndGetPusk(string $idKunjungan): array
    {
        $pel = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->where('pel.idpelayanan', $idKunjungan)
            ->select('pel.kdPoli', 'l.puskId')
            ->first();
        if (!$pel) {
            abort(422, 'Pelayanan (idKunjungan) tidak ditemukan');
        }
        if ((string)$pel->kdPoli !== '098') {
            abort(422, 'idKunjungan bukan rawat inap (kdPoli 098)');
        }
        return ['puskId' => $pel->puskId];
    }

    // Refs dropdown
    public function refsKesadaran()
    {
        $rows = DB::table('simpus_kesadaran')->select('kdSadar', 'nmSadar')->orderBy('nmSadar')->get();
        return response()->json($rows);
    }

    public function refsDokter()
    {
        $rows = DB::table('simpus_dokter')->select('kdDokter', 'nmDokter')->orderBy('nmDokter')->get();
        return response()->json($rows);
    }

    public function refsDiagnosaKasus()
    {
        $rows = DB::table('master_diagnosa_kasus')->select('id', 'kasus')->orderBy('kasus')->get();
        return response()->json($rows);
    }

    // Store Anamnesis
    public function storeAnamnesis(Request $request)
    {
        $data = $request->validate([
            'idKunjungan' => 'required',
            'tglAnamnesis' => 'required|date_format:Y-m-d H:i:s',
            'kdSadar' => 'nullable',
            'tenagaMedisAskep' => 'nullable',
            'tinggiBadan' => 'nullable|numeric',
            'beratBadan' => 'nullable|numeric',
            'lingkarPerut' => 'nullable|numeric',
            'imt' => 'nullable|numeric',
            'imtKet' => 'nullable|string',
            'suhu' => 'nullable|numeric',
            'sistole' => 'nullable|integer',
            'diastole' => 'nullable|integer',
            'respRate' => 'nullable|integer',
            'heartRate' => 'nullable|integer',
            'keluhan' => 'nullable|string',
            'thoraxJantung' => 'nullable|string',
            'thoraxJantungKet' => 'nullable|string',
            'thoraxPulmo' => 'nullable|string',
            'thoraxPulmoKet' => 'nullable|string',
            'abdomanAtas' => 'nullable|string',
            'abdomanAtasKet' => 'nullable|string',
            'abdomanBawah' => 'nullable|string',
            'abdomanBawahKet' => 'nullable|string',
            'extrimitasAtas' => 'nullable|string',
            'extrimitasAtasKet' => 'nullable|string',
            'extrimitasBawah' => 'nullable|string',
            'extrimitasBawahKet' => 'nullable|string',
            'alergiMakanan' => 'nullable|string',
            'alergiObat' => 'nullable|string',
            'keteranganAlergi' => 'nullable|string',
        ]);

        $meta = $this->validateEpisodeAndGetPusk($data['idKunjungan']);

        // Validasi referensi jika diisi
        if (!empty($data['kdSadar'])) {
            $exists = DB::table('simpus_kesadaran')->where('kdSadar', $data['kdSadar'])->exists();
            if (!$exists) abort(422, 'Kesadaran tidak valid');
        }
        if (!empty($data['tenagaMedisAskep'])) {
            $exists = DB::table('simpus_dokter')->where('kdDokter', $data['tenagaMedisAskep'])->exists();
            if (!$exists) abort(422, 'Dokter tidak valid');
        }

        // Get loketId from pelayanan table
        $loketId = DB::table('simpus_pelayanan')
            ->where('idpelayanan', $data['idKunjungan'])
            ->value('loketId');

        DB::table('simpus_anamnesa')->insert([
            'loketId' => $loketId,
            'idKunjungan' => $data['idKunjungan'],
            'tglAnamnesis' => $data['tglAnamnesis'],
            'kdSadar' => $data['kdSadar'] ?? null,
            'tenagaMedisAskep' => $data['tenagaMedisAskep'] ?? null,
            'tinggiBadan' => $data['tinggiBadan'] ?? null,
            'beratBadan' => $data['beratBadan'] ?? null,
            'lingkarPerut' => $data['lingkarPerut'] ?? null,
            'imt' => $data['imt'] ?? null,
            'imtKet' => $data['imtKet'] ?? null,
            'suhu' => $data['suhu'] ?? null,
            'sistole' => $data['sistole'] ?? null,
            'diastole' => $data['diastole'] ?? null,
            'respRate' => $data['respRate'] ?? null,
            'heartRate' => $data['heartRate'] ?? null,
            'keluhan' => $data['keluhan'] ?? null,
            'thoraxJantung' => $data['thoraxJantung'] ?? null,
            'thoraxJantungKet' => $data['thoraxJantungKet'] ?? null,
            'thoraxPulmo' => $data['thoraxPulmo'] ?? null,
            'thoraxPulmoKet' => $data['thoraxPulmoKet'] ?? null,
            'abdomanAtas' => $data['abdomanAtas'] ?? null,
            'abdomanAtasKet' => $data['abdomanAtasKet'] ?? null,
            'abdomanBawah' => $data['abdomanBawah'] ?? null,
            'abdomanBawahket' => $data['abdomanBawahKet'] ?? null,
            'extrimitasAtas' => $data['extrimitasAtas'] ?? null,
            'extrimitasAtasKet' => $data['extrimitasAtasKet'] ?? null,
            'extrimitasBawah' => $data['extrimitasBawah'] ?? null,
            'extrimitasBawahKet' => $data['extrimitasBawahKet'] ?? null,
            'alergiMakanan' => $data['alergiMakanan'] ?? null,
            'alergiObat' => $data['alergiObat'] ?? null,
            'keteranganAlergi' => $data['keteranganAlergi'] ?? null,
            'createdDate' => now(),
            'CreatedBy' => Auth::id(),
            'ranap' => 1, // Mark as inpatient anamnesis
        ]);

        return response()->json(['status' => 'success']);
    }

    // Store Diagnosis
    public function storeDiagnosis(Request $request)
    {
        $data = $request->validate([
            'idKunjungan' => 'required',
            'tglDiagnosa' => 'required|date_format:Y-m-d H:i:s',
            'diagnosaKasus' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $this->validateEpisodeAndGetPusk($data['idKunjungan']);

        $exists = DB::table('master_diagnosa_kasus')->where('id', $data['diagnosaKasus'])->exists();
        if (!$exists) abort(422, 'Jenis kasus diagnosa tidak valid');

        DB::table('simpus_ranap_diagnosa')->insert([
            'idKunjungan' => $data['idKunjungan'],
            'tglDiagnosa' => $data['tglDiagnosa'],
            'diagnosaKasus' => $data['diagnosaKasus'],
            'keterangan' => $data['keterangan'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    // Store Procedure (Tindakan)
    public function storeProcedure(Request $request)
    {
        $data = $request->validate([
            'idKunjungan' => 'required',
            'tglTindakan' => 'required|date_format:Y-m-d H:i:s',
            'kodeTindakan' => 'nullable|string',
            'uraianTindakan' => 'required|string',
        ]);

        $this->validateEpisodeAndGetPusk($data['idKunjungan']);

        DB::table('simpus_ranap_tindakan')->insert([
            'idKunjungan' => $data['idKunjungan'],
            'tglTindakan' => $data['tglTindakan'],
            'kodeTindakan' => $data['kodeTindakan'] ?? null,
            'uraianTindakan' => $data['uraianTindakan'],
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    // Store Visit
    public function storeVisit(Request $request)
    {
        $data = $request->validate([
            'idKunjungan' => 'required',
            'tglVisit' => 'required|date_format:Y-m-d H:i:s',
            'kdDokter' => 'required',
            'catatanVisit' => 'nullable|string',
        ]);

        $this->validateEpisodeAndGetPusk($data['idKunjungan']);

        $exists = DB::table('simpus_dokter')->where('kdDokter', $data['kdDokter'])->exists();
        if (!$exists) abort(422, 'Dokter tidak valid');

        DB::table('simpus_ranap_visit')->insert([
            'idKunjungan' => $data['idKunjungan'],
            'tglVisit' => $data['tglVisit'],
            'kdDokter' => $data['kdDokter'],
            'catatanVisit' => $data['catatanVisit'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    // List Anamnesis
    public function listAnamnesis(Request $request)
    {
        $idKunjungan = $request->query('idKunjungan');
        if (!$idKunjungan) abort(422, 'idKunjungan required');

        $this->validateEpisodeAndGetPusk($idKunjungan);

        $rows = DB::table('simpus_anamnesa')
            ->leftJoin('simpus_kesadaran', 'simpus_anamnesa.kdSadar', '=', 'simpus_kesadaran.kdSadar')
            ->leftJoin('simpus_dokter', 'simpus_anamnesa.tenagaMedisAskep', '=', 'simpus_dokter.kdDokter')
            ->select('simpus_anamnesa.*', 'simpus_kesadaran.nmSadar', 'simpus_dokter.nmDokter')
            ->where('simpus_anamnesa.idKunjungan', $idKunjungan)
            ->where('simpus_anamnesa.ranap', 1) // Only inpatient anamnesis
            ->orderBy('simpus_anamnesa.tglAnamnesis', 'desc')
            ->get();

        return response()->json($rows);
    }

    // Delete Anamnesis
    public function deleteAnamnesis(Request $request, $idAnamnesis)
    {
        $anamnesis = DB::table('simpus_anamnesa')->where('idAnamnesis', $idAnamnesis)->first();
        if (!$anamnesis) abort(404, 'Anamnesis tidak ditemukan');

        $this->validateEpisodeAndGetPusk($anamnesis->idKunjungan);

        DB::table('simpus_anamnesa')->where('idAnamnesis', $idAnamnesis)->delete();

        return response()->json(['status' => 'success']);
    }

    // Refs Alergi
    public function refsAlergi(Request $request)
    {
        $idPasien = $request->query('idPasien');
        if (!$idPasien) abort(422, 'idPasien required');

        // Return empty allergy data since patient-specific allergy data is not stored in existing tables
        return response()->json([
            'namaAlergiMakanan' => '',
            'namaAlergiObat' => '',
            'keterangan' => ''
        ]);
    }
}
