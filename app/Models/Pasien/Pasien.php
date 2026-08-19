<?php

namespace App\Models\Pasien;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'simpus_pasien';   // nama tabel
    protected $primaryKey = 'ID';         // sesuaikan dengan kolom PK di DB
    public $timestamps = false;
    protected $fillable = [
        'NAMA_LGKP',
        'NIK',
        'ALAMAT',
        'TGL_LHR',
        'TMPT_LHR',
        'JENIS_KLMIN',
        'NO_PROP',
        'NO_KAB',
        'NO_KEC',
        'NO_KEL',
        'NO_RT',
        'NO_RW',
        'NO_KK',
        'noKartu',
        'kdProvider',
        'AGAMA',
    ];

    public function kecamatan()
    {
        return $this->hasOne(Kecamatan::class, 'NO_KEC', 'NO_KEC')
            ->whereColumn('setup_kec.NO_KAB', 'simpus_pasien.NO_KAB')
            ->whereColumn('setup_kec.NO_PROP', 'simpus_pasien.NO_PROP');
    }

    public function kelurahan()
    {
        return $this->hasOne(Kelurahan::class, 'NO_KEL', 'NO_KEL')
            ->whereColumn('setup_kel.NO_KEC', 'simpus_pasien.NO_KEC')
            ->whereColumn('setup_kel.NO_KAB', 'simpus_pasien.NO_KAB')
            ->whereColumn('setup_kel.NO_PROP', 'simpus_pasien.NO_PROP');
    }
}
