<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterAlergi extends Model
{
    protected $table = 'master_alergi';
    protected $primaryKey = 'kodeSatuSehat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'kodeSatuSehat',
        'kodeBpjs',
        'namaAlergiSatuSehat',
        'namaAlergiBpjs',
        'codesystem',
        'category',
        'deskripsi',
        'status',
    ];
    public function alergiObatData()
    {
        return $this->hasMany(SimpusAlergiData::class, 'kodeAlergiObat', 'kodeSatuSehat');
    }

    public function alergiMakananData()
    {
        return $this->hasMany(SimpusAlergiData::class, 'kodeAlergiMakanan', 'kodeSatuSehat');
    }

    public function alergiUdaraData()
    {
        return $this->hasMany(SimpusAlergiData::class, 'kodeAlergiUdara', 'kodeSatuSehat');
    }
}
