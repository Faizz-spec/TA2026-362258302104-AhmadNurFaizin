<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusProvider extends Model
{
    protected $table = 'simpus_provider';
    protected $primaryKey = 'kdProvider';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kdProvider',
        'nmProvider',
        'stts',
        'bridging',
        'idKategori',
        'faskes',
        'no_kec',
        'no_kel',
    ];
    public $timestamps = false;
}
