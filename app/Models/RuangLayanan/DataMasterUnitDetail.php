<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class DataMasterUnitDetail extends Model
{
    protected $table = 'data_master_unit_detail';
    protected $primaryKey = 'id_detail';

    public function DataMasterUnit(){
        return $this->belongsTo(DataMasterUnit::class, 'id_kategori', 'id_kategori');
    }
}
