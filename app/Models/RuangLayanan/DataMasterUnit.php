<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class DataMasterUnit extends Model
{
    protected $table = 'data_master_unit';
    protected $primaryKey = 'id_kategori';
    public function DataMasterUnitDetail(){
        return $this->hasMany(DataMasterUnitDetail::class, 'id_kategori', 'id_kategori');
    }
}
