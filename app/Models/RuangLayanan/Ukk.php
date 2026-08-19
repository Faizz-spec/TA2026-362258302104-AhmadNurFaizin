<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class Ukk extends Model
{
     protected $table = 'simpus_ukk'; 

    protected $primaryKey = 'ukk_id'; 

    public $incrementing = false; 

    protected $keyType = 'string'; 
    protected $fillable = [
        'ukk_id',
        'loketId',
        'pekerjaan',
        'jenisUKK',
        'tipeKerja',
        'tempatKerja',
        'lamaKerja',
        'createdBy',
        'modifiedBy',
    ];
    public $timestamps = false;

    protected $dates = [
        'createdDate',
        'modifiedDate',
    ];
}
