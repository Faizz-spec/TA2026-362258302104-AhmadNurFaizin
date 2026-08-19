<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MKeluarga extends Model
{
    protected $table = 'm_keluarga';
    protected $primaryKey = 'KODE';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'KODE',
        'KET',
        'KODE_LAMA',
        'KODE_BDT'
    ];
}
