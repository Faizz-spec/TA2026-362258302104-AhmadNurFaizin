<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SimpusKunjungan extends Model
{
    protected $table = 'simpus_kunjungan';
    protected $primaryKey = 'id';

    public function pasien(){
        return $this->belongsTo(SimpusPasien::class, 'pasien_id', 'ID');
    }
    //
}
