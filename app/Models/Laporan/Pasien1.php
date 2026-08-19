<?php


// app/Models/Pasien.php

namespace App\Models\Laporan;

use Illuminate\Database\Eloquent\Model;

class Pasien1 extends Model
{
    protected $table = 'simpus_pasien';
    protected $primaryKey = 'ID'; // ✅ sesuai dengan field di database kamu
    public $timestamps = false;
}
