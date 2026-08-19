<?php

namespace App\Models\RuangLayanan\SkriningPTM;

use Illuminate\Database\Eloquent\Model;

class KunjunganPTM extends Model
{
    protected $table = 'tabel_kunjungan_ptm';
    protected $primaryKey = 'id';
    //
    protected $fillable = [
        'idSkrining',
        'idPelayanan',
        'idLoket',
        'nikPasien',
        'tanggal_skrining',
        'dokter',
        'fasyankes',
        'jenis_kunjungan',
        'keluhan_utama'
    ];
}
