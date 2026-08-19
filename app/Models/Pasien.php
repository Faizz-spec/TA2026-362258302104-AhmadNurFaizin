<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'simpus_pasien';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'NIK',
        'NO_MR',
        'SEQ',
        'noKartu',
        'kdProvider',
        'NAMA_LGKP',
        'TMPT_LHR',
        'TGL_LHR',
        'JENIS_KLMIN',
        'AGAMA',
        'STAT_KWN',
        'STAT_HBKEL',
        'NO_KK',
        'NAMA_LGKP_AYAH',
        'NO_PROP',
        'NO_KAB',
        'NO_KEC',
        'NO_KEL',
        'ALAMAT',
        'NO_RT',
        'NO_RW',
        'PHONE',
        'IHS_NUMBER',
        'JENIS_PKRJN'
    ];

    protected $casts = [
        'JENIS_KLMIN' => 'integer',
        'STAT_HBKEL' => 'integer',
        'JENIS_PKRJN' => 'integer',
        'NO_RT' => 'integer',
        'NO_RW' => 'integer',
        'NO_PROP' => 'integer',
        'NO_KAB' => 'integer',
        'NO_KEC' => 'integer',
        'NO_KEL' => 'integer'
    ];

    public function loket()
    {
        return $this->hasMany(Loket::class, 'pasienId', 'ID');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'NO_PROP', 'NO_PROP');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'NO_KAB', 'NO_KAB')
            ->whereColumn('simpus_pasien.NO_PROP', 'setup_kab.NO_PROP');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'NO_KEC', 'NO_KEC')
            ->whereColumn('simpus_pasien.NO_PROP', 'setup_kec.NO_PROP')
            ->whereColumn('simpus_pasien.NO_KAB', 'setup_kec.NO_KAB');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'NO_KEL', 'NO_KEL')
            ->whereColumn('simpus_pasien.NO_PROP', 'setup_kel.NO_PROP')
            ->whereColumn('simpus_pasien.NO_KAB', 'setup_kel.NO_KAB')
            ->whereColumn('simpus_pasien.NO_KEC', 'setup_kel.NO_KEC');
    }

    public function hubunganKeluarga()
    {
        return $this->belongsTo(MKeluarga::class, 'STAT_HBKEL', 'KODE');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'JENIS_PKRJN', 'NO');
    }
}
