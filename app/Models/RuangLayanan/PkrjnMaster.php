<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class PkrjnMaster extends Model
{
    protected $table = 'pkrjn_master';
    protected $primaryKey = 'NO';

    protected $fillable= [
        'DESCRIP',
        'GR_CODE',
        'UKK'
    ];
}
