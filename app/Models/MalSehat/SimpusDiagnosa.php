<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class SimpusDiagnosa extends Model
{
    protected $table = 'simpus_diagnosa'; // sesuai nama tabel

    protected $primaryKey = 'id'; // kolom primary key

    public $timestamps = false; // kalau tabel tidak ada created_at/updated_at

    protected $fillable = [
        'kdDiag',
        'nmDiag',
    ];
}
