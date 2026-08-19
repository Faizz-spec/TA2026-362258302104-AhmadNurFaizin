<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class NakesProfesi extends Model
{
    protected $table = 'nakes_profesi';
    public $timestamps = false;
    protected $primaryKey = 'id_profesi';
    protected $fillable = [
        'kode',
        'nama_profesi',
        'organisasi_profesi_id',
        'kategori',
        'sub_kategori',
        'aktif',
    ];

}
