<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusLogKamar extends Model
{
    protected $table = 'simpus_log_kamar';
    protected $primaryKey = 'idLog';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'pelayananId',
        'kamarId',
        'bedId',
        'tglMskKmr',
        'tglPndKmr',
        'tglKlrKmr',
        'createdBy',
        'createdDate',
        'deskripsi',
    ];
    public function pelayanan()
    {
        return $this->belongsTo(SimpusPelayanan::class, 'pelayananId', 'idpelayanan');
    }
    public function kamar()
    {
        return $this->belongsTo(MasterKamar::class, 'kamarId');
    }
    public function bed()
    {
        return $this->belongsTo(MasterBed::class, 'bedId');
    }
}
