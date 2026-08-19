<?php

namespace App\Http\Controllers\MalSehat;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\MalSehat\UnitProfile;
use App\Models\MalSehat\MasterAlergi;
use App\Models\MalSehat\MasterDokter;
use App\Models\MalSehat\MasterDiagnosaKasus;
use App\Models\MalSehat\SimpusPasien;

class BiakesController extends Controller
{
    /**
     * Halaman daftar Pembiayaan Jaminan Sehat
     */
    public function pembiayaanJaminanSehat()
    {
        // ✅ contoh filter unit
        $units = UnitProfile::where('id_unit', 1)
            ->select('nama_unit')
            ->get();

        // ✅ ambil pasien aktif
        $pasien = SimpusPasien::where('ACTIVE', 1)
            ->select(
                'ID',
                'created',
                'NO_MR as no_mr',
                'NIK as nik',
                'NAMA_LGKP as nama',
                'ALAMAT as alamat',
                'NO_KEL as desa',
                'noKartu as bpjs'
            )
            ->limit(50)
            ->get();

        return Inertia::render('MalSehat/Biakes/PembiayaanJaminanSehat', [
            'units' => $units,
            'pasien' => $pasien
        ]);
    }

    /**
     * Halaman pelayanan pasien berdasarkan no_mr
     */
    public function pelayanan($no_mr)
    {
        // ✅ Ambil data pasien
        $pasien = SimpusPasien::where('NO_MR', $no_mr)
            ->select(
                'NO_MR as no_mr',
                'NIK as nik',
                'NAMA_LGKP as nama',
                'ALAMAT as alamat',
                'NO_KEL as desa',
                'noKartu as bpjs',
                'PHONE as phone',
                'TGL_LHR as tgl_lahir',
                'JENIS_KLMIN as jenis_kelamin',
                'created as tanggal'
            )
            ->first();

        if ($pasien) {
            // 🔹 Hitung umur
            $birthDate = new \DateTime($pasien->tgl_lahir);
            $today     = new \DateTime();
            $age       = $today->diff($birthDate)->y;

            // 🔹 Mapping jenis kelamin
            $jk = ($pasien->jenis_kelamin == 1) ? 'L' : 'P';

            $pasien->umur = $age . ' tahun';
            $pasien->jk   = $jk;
        }

        // alergi
        $alergiMakanan = MasterAlergi::where('category', 1)->get();
        $alergiObat    = MasterAlergi::where('category', 2)->get();

        // dokter
        $dokter = MasterDokter::select('idDokter', 'nmDokter', 'kdDokter')->get();

        // kasus
        $kasus  = MasterDiagnosaKasus::select('id', 'kasus')->get();

        // pastikan mapping jk tetap ada
        if ($pasien) {
            $pasien->jk = $pasien->jenis_kelamin == 1 ? 'L' : 'P';
        }

        return Inertia::render('MalSehat/Biakes/Pelayanan', [
            'no_mr'         => $no_mr,
            'alergiMakanan' => $alergiMakanan,
            'alergiObat'    => $alergiObat,
            'dokter'        => $dokter,
            'kasus'         => $kasus,
            'pasien'        => $pasien
        ]);
    }
}
