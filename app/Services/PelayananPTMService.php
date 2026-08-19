<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\RuangLayanan\SimpusTindakan;
use App\Models\RuangLayanan\MasterDokter;
use App\Models\RuangLayanan\SkriningPTM\KunjunganPTM;
use App\Models\RuangLayanan\SkriningPTM\FaktorRisiko;
use App\Models\RuangLayanan\SkriningPTM\AssessmentPTM;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PelayananPTMService
{
    public function getData($id, $idPoli)
    {
        return DB::table('simpus_loket as l')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_pelayanan as pel', 'l.idLoket', '=', 'pel.loketId')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->leftJoin('unit_profiles as up', 'up.unit_id', '=','l.unitId')
            ->leftJoin('master_dokter as dokter', 'up.unit_id', '=', 'dokter.pusk_id')

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

            ->where('l.kdPoli', $idPoli)
            ->where('l.idLoket', $id)

            ->select(
                'p.ID',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.IHS_NUMBER',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'l.kdPoli',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'p.jenis_klmin',
                'l.umur',
                'l.umur_bulan',
                'l.umur_hari',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kunjBaru',
                'pel.idpelayanan',
                'pel.sudahDilayani',
                'pel.startTime',
                'pel.progressTime',
                'up.nama_unit',
                'dokter.kdDokter',
                'dokter.nmDokter'
            )
            ->first();
    }

    public function getMasterData()
    {
        $SimpusTindakan = SimpusTindakan::select('kdTindakan', 'nmTindakan', 'nmTindakanInd')
            ->where('deskripsi', 'icd9cm')
            ->groupBy('kdTindakan', 'nmTindakan', 'nmTindakanInd')
            ->get();
        
        $TenagaMedis = MasterDokter::select('nmDokter', 'kdDokter')
            ->where('profesi_id', [23, 24])
            ->get();

            // dd($TenagaMedis);
        // dd($SimpusTindakan);
        return [
            'tindakan' => $SimpusTindakan,
            'TenagaMedis' => $TenagaMedis
        ];
    }
    public function updateStatusPelayanan($idPelayanan, $status)
    {
        DB::table('simpus_pelayanan')
            ->where('idpelayanan', $idPelayanan)
            ->update([
                'sudahDilayani' => $status,
                'startTime' => now(),
            ]);
    }
    public function addKunjunganPTM($data)
    {
        return DB::transaction(function () use ($data) {
            $kunjungan = KunjunganPTM::create([
                'idSkrining' => (string) Str::uuid(),
                'idPelayanan' => $data['idpelayanan'],
                'idLoket' => $data['idLoket'],
                'nikPasien' => $data['nikPasien'],
                'tanggal_skrining' => $data['tanggal_skrining'],
                'dokter' => $data['dokter'],
                'fasyankes' => $data['fasyankes'],
                'jenis_kunjungan' => $data['jenis_kunjungan'],
                'keluhan_utama' => $data['keluhan_utama'],
            ]);

            FaktorRisiko::create([
                'skriningID' => $kunjungan->idSkrining,
                'pelayananId' => $data['idpelayanan'],
                'merokok' => $data['merokok'] ?? null,
                'status_merokok' => $data['status_merokok'] ?? null,
                'btg_rokok' => $data['btg_rokok'] ?? null,
                'lama_rokok' => $data['lama_rokok'] ?? null,
                'paparan_rokok' => $data['paparan_rokok'] ?? null,
                'gula' => $data['gula'] ?? null,
                'garam' => $data['garam'] ?? null,
                'minyak' => $data['minyak'] ?? null,
                'sayur' => $data['sayur'] ?? null,
                'aktivitas' => $data['aktivitas'] ?? null,
                'alkohol' => $data['alkohol'] ?? null,
                'r_pribadi_htn' => $data['r_pribadi_htn'] ?? false,
                'r_pribadi_dm' => $data['r_pribadi_dm'] ?? false,
                'r_pribadi_stroke' => $data['r_pribadi_stroke'] ?? false,
                'r_pribadi_jantung' => $data['r_pribadi_jantung'] ?? false,
                'r_keluarga_htn' => $data['r_keluarga_htn'] ?? false,
                'r_keluarga_dm' => $data['r_keluarga_dm'] ?? false,
                'r_keluarga_stroke' => $data['r_keluarga_stroke'] ?? false,
                'r_keluarga_jantung' => $data['r_keluarga_jantung'] ?? false,
                'obat' => $data['obat'] ?? null,
                'kesiapan' => $data['kesiapan'] ?? null,
                'dukung' => $data['dukung'] ?? null,
                'skor_faktor_risiko' => $data['skor_faktor_risiko'] ?? null,
                'kategori_faktor_risiko' => $data['kategori_faktor_risiko'] ?? null,
                'detail_faktor_risiko' => $data['detail_faktor_risiko'] ?? null,
            ]);

            return $kunjungan;
        });
    }   

    public function kunjunganPTMExists($idPelayanan, $idLoket)
    {
        return KunjunganPTM::where('idPelayanan', $idPelayanan)
            ->orWhere('idLoket', $idLoket)
            ->exists();
    }

    public function addAssessmentPTM($data)
    {
        $skriningPtmId = $data['skrining_ptm_id'] ?? DB::table('skrining_ptm')
            ->where('idpelayanan', $data['idpelayanan'] ?? null)
            ->value('id');

        if (!$skriningPtmId) {
            throw ValidationException::withMessages([
                'skrining_ptm_id' => 'Data skrining PTM belum ditemukan. Simpan data kunjungan terlebih dahulu.',
            ]);
        }

        return AssessmentPTM::updateOrCreate([
            'skrining_ptm_id' => $skriningPtmId,
        ], [
            'masalah_hasil_skrining' => $data['masalah_hasil_skrining'] ?? null,
            'ringkasan_temuan' => $data['ringkasan_temuan'] ?? null,
            'diagnosis_utama' => $data['diagnosis_utama'] ?? null,
            'diagnosis_utama_saran' => $data['diagnosis_utama_saran'] ?? null,
            'status_klinis' => $data['status_klinis'] ?? null,
            'catatan_diagnosis' => $data['catatan_diagnosis'] ?? null,
            'kategori_risiko' => $data['kategori_risiko'] ?? null,
            'ringkasan_klinis' => $data['ringkasan_klinis'] ?? null,
            'catatan_assessment' => $data['catatan_assessment'] ?? null,
        ]);
    }
}
