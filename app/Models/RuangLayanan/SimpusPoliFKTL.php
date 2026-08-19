<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusPoliFKTL extends Model
{
    protected $table = 'simpus_poli_fktl';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'kdPoli',
        'nmPoli',
        'poliSakit',
    ];
    public $timestamps = false;
}
