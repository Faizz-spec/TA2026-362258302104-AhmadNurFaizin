<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'simpus_poli_fktp';
    protected $primaryKey = 'kdPoli';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

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
        'satuSehat'
    ];

    public function scopeSakit($query)
    {
        return $query->where('poliSakit', 'true');
    }

    public function scopeSehat($query)
    {
        return $query->where('poliSakit', 'false');
    }

    public function scopeAktif($query)
    {
        return $query->where('active', 1);
    }
}
