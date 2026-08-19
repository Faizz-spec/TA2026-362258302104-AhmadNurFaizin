<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;      // ✅ penting
use App\Models\Profile\UnitProfile;       // ✅ model di folder Profile
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $pusk = $user->puskesmas ?? null;

        $kode = $pusk->kode_puskesmas ?? $pusk->kode ?? null;
        $nama = $pusk->nama ?? null;

        $query = UnitProfile::query();

        if ($kode) {
            $query->where('kode_puskesmas', $kode);
        } elseif ($nama) {
            $query->where('nama_unit', $nama);
        }

        $profile = $query->first();

        $payload = $profile ? [
            'unit_id'          => $profile->unit_id,
            'kode_puskesmas'   => $profile->kode_puskesmas,
            'nama_unit'        => $profile->nama_unit,
            'alamat'           => $profile->alamat,
            'rt'               => $profile->rt,
            'rw'               => $profile->rw,
            'no_kel'           => $profile->no_kel,
            'no_kec'           => $profile->no_kec,
            'no_kab'           => $profile->no_kab,
            'no_prov'          => $profile->no_prov,
            'kode_pos'         => $profile->kode_pos,
            'telp'             => $profile->telp,
            'fax'              => $profile->fax,
            'email'            => $profile->email,
            'site'             => $profile->site,
            'kategori'         => $profile->kategori,
            'lat'              => $profile->lat,
            'lng'              => $profile->lng,
            'jabatan_pimpinan' => $profile->jabatan_pimpinan,
            'nama_pimpinan'    => $profile->nama_pimpinan,
            'nip_pimpinan'     => $profile->nip_pimpinan,
            'last_update'      => optional($profile->last_update)->format('Y-m-d H:i:s'),
        ] : null;

        return Inertia::render('Profile/Profile', [
            'profile'   => $payload,
            'fromUser'  => [
                'puskesmas_nama' => $nama,
                'puskesmas_kode' => $kode,
            ],
        ]);
    }
}
