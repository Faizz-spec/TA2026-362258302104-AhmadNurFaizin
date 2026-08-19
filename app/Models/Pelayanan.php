<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    protected $table = 'simpus_pelayanan';
    protected $primaryKey = 'idpelayanan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'idpelayanan',
        'loketId',
        'kdPoli',
        'kdKegiatanPel',
        'kunjSakitPel',
        'kdStatusPulang',
        'tujuanPoli',
        'tglPindah',
        'kdTacc'
    ];

    public function loket()
    {
        return $this->belongsTo(Loket::class, 'loketId', 'idLoket');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'kdPoli', 'kdPoli');
    }
}
