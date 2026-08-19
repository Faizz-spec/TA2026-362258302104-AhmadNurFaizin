<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterObstetri extends Model
{
    protected $table = 'obstetri_master';
    public $timestamps = false;
    protected $fillable = [
        'pasienId',
        'gravida',
        'partus',
        'abortus',
        'tphtDate',
        'bbSebelumHamil',
        'tinggiBadan',
        'bb_target',
        'imt',
        'status_imt',
        'jarak_hamil',
        'imunisasiTtStatus',
        'imunisasi_doss_1',
        'imunisasi_doss_2',
        'imunisasi_doss_3',
        'imunisasi_doss_4',
        'imunisasi_doss_5',
    ];
    //
}
