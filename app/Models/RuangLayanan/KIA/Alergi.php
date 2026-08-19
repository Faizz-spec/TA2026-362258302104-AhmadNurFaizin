<?php

namespace App\Models\RuangLayanan\KIA;

use Illuminate\Database\Eloquent\Model;

class Alergi extends Model
{
    protected $table = 'master_alergi';
    protected $primaryKey = 'kodeBpjs';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
