<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class MasterDiagnosaKasus extends Model
{
    protected $table = 'master_diagnosa_kasus';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kasus'
    ];
}
