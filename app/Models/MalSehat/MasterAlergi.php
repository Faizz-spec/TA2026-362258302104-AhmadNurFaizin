<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class MasterAlergi extends Model
{
    protected $table = 'master_alergi';
    protected $fillable = [
        'kodeSatuSehat',
        'kodeBpjs',
        'namaAlergiSatuSehat',
        'namaAlergiBpjs',
        'codesystem',
        'category',
        'deskripsi',
        'status',
    ];
    public $timestamps = false; // jika tabel tidak pakai created_at & updated_at
}
