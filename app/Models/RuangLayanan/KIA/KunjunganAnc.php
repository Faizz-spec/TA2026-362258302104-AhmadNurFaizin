<?php

namespace App\Models\RuangLayanan\KIA;

use Illuminate\Database\Eloquent\Model;

class KunjunganAnc extends Model
{
    protected $table = 'kunjungan_kia_anc';
    public $timestamps = false;
    protected $fillable = [
        'loketId',
        'pasienId',
        'tanggal_kunjungan',
        'usia_kehamilan',
        'trimester',
    ];
    //
}
