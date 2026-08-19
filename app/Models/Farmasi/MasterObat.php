<?php

namespace App\Models\Farmasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterObat extends Model
{
    protected $table = 'simpus_master_obat'; // nama tabel
    protected $primaryKey = 'OBAT_ID';
    public $timestamps = false; // soalnya tabelmu pakai CREATED_DATE, bukan created_at/updated_at

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
        'CREATED_DATE'
    ];
}
