<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterDiagnosaKasus extends Model
{
    protected $table = 'master_diagnosa_kasus';
    protected $primaryKey = 'id';
    public function SimpusDataDiagnosa(){
        return $this->hasMany(SimpusDataDiagnosa::class, 'diagnosaKasus');
    }
}
