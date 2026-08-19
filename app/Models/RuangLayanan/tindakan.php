<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class tindakan extends Model
{
    protected $table = 'simpus_tindakan';
    protected $primaryKey = 'idTindakan';
    public $timestamps = false;


    protected $fillable = [
        'idPelayanan',
        'loketId',
        'kdTindakan',
        'nmTindakan',
        'nmTindakanInd',
        'keterangan',
    ];
    //
}
