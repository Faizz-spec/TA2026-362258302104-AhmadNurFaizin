<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusPoliFKTP extends Model
{
    protected $table = 'simpus_poli_fktp';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kdPoli',
        'nmPoli',
        'poliSakit',
        'rujukIntern',
        'pelayanan',
        'color',
        'nmController',
        'seksi',
        'bpjs',
        'active',
        'ruang_layanan_id',
        'deskripsi',
        'satuSehat',
    ];

    public function SimpusDataDiagnosa()
    {
        return $this->hasMany(SimpusDataDiagnosa::class, 'kdPoli', 'kdPoli');
    }
     public function SimpusLoket(){
        return $this->belongsTo(SimpusLoket::class, 'kdPoli', 'kdPoli');
    }
    public function pelayanans()
{
    return $this->hasMany(SimpusPelayanan::class, 'kdPoli', 'kdPoli');
}

}
