<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class MasterDokter extends Model
{
    protected $table = 'master_dokter';
    protected $primaryKey = 'idDokter';
    public $timestamps = false;

    protected $fillable = [
        'NIK',
        'NIP',
        'ihs_nakes',
        'keterangan',
        'kdDokter',
        'nmDokter',
        'kategori'
    ];
}
