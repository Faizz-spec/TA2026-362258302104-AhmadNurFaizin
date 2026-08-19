<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pkrjn_master';
    protected $primaryKey = 'NO';
    protected $keyType = 'decimal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NO',
        'DESCRIP',
        'GR_CODE',
        'UKK'
    ];
}
