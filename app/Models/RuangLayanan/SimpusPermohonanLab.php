<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusPermohonanLab extends Model
{
    protected $table = 'simpus_permohonan_lab';
    protected $primaryKey = 'idPermohonan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'idPermohonan',
        'pasienId',
        'loketId',
        'pelayananId',
        'nmPoli',
        'tglDibuat',
        'tenagaMedisPengirim',
        'tenagaMedisPenerima',
        'tglDiterima',
        'alasanDirujuk',
        'hasilLabLuarFaskes',
        'sampleDiambil',
        'sampleSelesai',
        'statusLayanan',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate'
    ];
    public function tenagaMedis()
    {
        return $this->belongsTo(MasterDokter::class, 'tenagaMedisPengirim', 'idDokter');
    }

}
