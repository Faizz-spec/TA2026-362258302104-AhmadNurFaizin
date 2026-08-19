<?php

namespace App\Models\Farmasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLangsung extends Model
{    
    protected $table = 'simpus_peng_langsung';
    protected $primaryKey = 'id_peng_langsung';
    public $timestamps = false;
    
    protected $fillable = [
        'tanggal',
        'unitId',
        'pengMasterId',
        'keterangan',
        'obat_id',
        'kode_obat',
        'nama_obat',
        'jumlah',
        'createdDate',
        'createdBy',
        'modified_date',
        'modified_by',
        'keperluan'
    ];
}
