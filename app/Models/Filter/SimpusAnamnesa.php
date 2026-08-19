<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SimpusAnamnesa extends Model
{
    protected $table = 'simpus_anamnesa';
    protected $primaryKey = 'idAnamnesa';
    public $incrementing = false;
    protected $keyType = 'string';

    public function loket()
    {
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
    //
}
