<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SimpusPakaiObat extends Model
{
    protected $table = 'simpus_pakai_obat';
    protected $primaryKey = 'id';

    public function loket(){
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
    //
}
