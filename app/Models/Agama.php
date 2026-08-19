<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'agama_master';
    protected $primaryKey = 'NO';
    public $incrementing = false;
    protected $keyType = 'decimal';
    public $timestamps = false;

    protected $fillable = [
        'NO',
        'DESCRIP'
    ];
}
