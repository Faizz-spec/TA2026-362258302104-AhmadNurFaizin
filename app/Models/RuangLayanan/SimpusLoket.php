<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusLoket extends Model
{


    protected $table = 'simpus_loket';
    protected $primaryKey = 'idLoket';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idLoket',
        'pasienId',
        'noUrut',
        'noKartu',
        'kdProvider',
        'statusKartu',
        'providerKartu',
        'jnsPeserta',
        'kdTkp',
        'noKunjungan',
        'tglKunjungan',
        'kunjSakit',
        'kdPoli',
        'kdKegiatan',
        'keluhan',
        'puskId',
        'unitId',
        'ukkId',
        'wilayah',
        'kunjBaru',
        'kelUmur',
        'umur',
        'umur_bulan',
        'umur_hari',
        'jknPbi',
        'statusPasien',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
        'kategoriUnitId',
        'PROP',
        'KAB',
        'statusResponSatuData',
        'idResponSatuData',
        'timeRespon',
        'no_antrian',
        'resep_diambil',
        'sample_resep',
        'statusLayananLab',
        'startTImeLab',
        'endTimeLab',
        'startTimeResep',
        'endTimeResep',
        'tenagaMedisApotek',
        'PHONE',
        'kdDokter',
        'resp_add',
        'resp_panggil',
        'idResponRekamMedik',
    ];

    public function SimpusPasien()
    {
        return $this->belongsTo(SimpusPasien::class, 'pasienId', 'ID');
    }

    public function SimpusPoli()
    {
        return $this->belongsTo(SimpusPoliFKTP::class, 'kdPoli', 'kdPoli');
    }
    public function anamnesa()
    {
        return $this->hasMany(SimpusAnamnesa::class, 'loketId', 'idLoket');
    }
    public function Diagnosa()
    {
        return $this->hasMany(SimpusDataDiagnosa::class, 'loketId', 'idLoket');
    }
    public function Tindakan()
    {
        return $this->hasMany(SimpusTindakan::class, 'loketId', 'idLoket')
            ->where('deskripsi', 'icd9cm');
    }
    public function TindakanLab()
    {
        return $this->hasMany(SimpusTindakan::class, 'loketId', 'idLoket')
            ->where('deskripsi', 'lab');
    }

    public function ResepObat()
    {
        return $this->hasMany(SimpusResepObat::class, 'loketId', 'idLoket');

    }
    public function unitprofile()
    {
        return $this->belongsTo(UnitProfile::class, 'unitId', 'unit_id');
    }
    public function pelayanan()
    {
        return $this->hasMany(SimpusPelayanan::class, 'loketId', 'idLoket');
    }
     public function pelayananAwal()
    {
        return $this->belongsTo(SimpusPelayanan::class, 'loketId', 'idLoket')
        ->where('pelIdSebelum', 0);
    }

}
