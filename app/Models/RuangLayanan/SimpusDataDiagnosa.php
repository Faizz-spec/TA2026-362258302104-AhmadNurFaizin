<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusDataDiagnosa extends Model
{
    protected $table = 'simpus_data_diagnosa';
    protected $primaryKey = 'idDiagnosa';

    protected $fillable = [
        'kdDiagnosa',
        'nmDiagnosa',
        'diagnosaKasus',
        'keterangan',
        'kdPoli',
        'loketId',
        'pelayananId',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
        'tglDiagnosa',
        'kategori',
        'id_condition',
    ];
    public $timestamps = false;
    public function MasterDiagnosaKasus(){
        return $this->belongsTo(MasterDiagnosaKasus::class, 'diagnosaKasus'); 
    }
     public function SimpusPoliFKTP(){
        return $this->belongsTo(SimpusPoliFKTP::class, 'kdPoli','kdPoli'); 
    }
      public function Loket(){
        return $this->belongsTo(SimpusLoket::class, 'loketId','idLoket'); 
    }

    public function SimpusDiagnosa(){
         return $this->belongsTo(SimpusDiagnosa::class, 'kdDiagnosa','kdDiag'); 

    }
}
