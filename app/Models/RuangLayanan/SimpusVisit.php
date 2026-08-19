<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusVisit extends Model
{
    protected $table = 'simpus_visit';
    protected $primaryKey = 'idVisit';
    public $timestamps = false;
    protected $fillable = [
        'idVisit',
        'pelayananId',
        'loketId',
        'tenagaMedis',
        'tanggalVisit',
        'jamVisit',
        'instruksi',
        'keterangan',
        'createdBy',
        'createdDate',
    ];
    public function pelayanan()
    {
        return $this->belongsTo(SimpusPelayanan::class, 'pelayananId', 'idpelayanan');
    }
    public function loket()
    {
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
    public function tenagaMedis()
    {
        return $this->belongsTo(MasterDokter::class, 'tenagaMedis', 'idDokter');
    }
}
