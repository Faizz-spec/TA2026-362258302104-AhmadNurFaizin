<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class DataMasterUnit extends Model
{
    protected $table = "data_master_unit";

    public function detail(): HasMany{
        return $this->hasMany(DataMasterUnitDetail::class, 'id_kategori', 'id_kategori');
    }
    //
}
