<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{

    protected $table = 'master_wilayah';
    protected $primaryKey = 'id_wilayah';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'wilayah',
    ];
}
