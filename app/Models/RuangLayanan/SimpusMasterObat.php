<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusMasterObat extends Model
{
    protected $table = 'simpus_master_obat';
    protected $primaryKey = 'OBAT_ID';
    public $timestamps = false;
    protected $fillable = [
        'KODE_OBAT',
        'NAMA',
        'SATUAN',
        'JENIS',
        'GOLONGAN',
        'SUMBER',
        'TAHUN',
        'HARGA',
        'ISI',
        'REK',
        'AKTIF',
        'CREATED_DATE',
    ];
}
