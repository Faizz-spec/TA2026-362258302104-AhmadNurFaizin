<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpusMasterTindakan extends Model
{
    use HasFactory;

    protected $table = 'simpus_master_tindakan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama_tindakan',
        'nama_tindakan_indonesia',
        'harga',
        'nilai_normal',
        'simTarif',
        'kategori',
        'header',
        'deskripsi',
    ];
}
