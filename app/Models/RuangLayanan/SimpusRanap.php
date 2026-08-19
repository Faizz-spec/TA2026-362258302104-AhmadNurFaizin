<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusRanap extends Model
{
    protected $table = 'simpus_ranap';
    protected $primaryKey = 'pelayananId';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'pelayananId',
        'kamarId',
        'bedId',
        'tglMasuk',
        'tglKeluar',
        'ranapStatus',
        'pulangStatus',
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
