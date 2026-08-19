<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusAlergiData extends Model
{
    protected $table = 'simpus_alergi_data';
    protected $primaryKey = 'idAlergi';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;


    protected $fillable = [
        'idAlergi',
        'pasienId',
        'kodeAlergiObat',
        'kodeAlergiMakanan',
        'kodeAlergiUdara',
        'keterangan',
        'createdBy',
        'createdDate',
        'idResponAlergiMakanan',
        'idResponAlergiObat',
    ];

    public function alergiObat()
    {
        return $this->belongsTo(MasterAlergi::class, 'kodeAlergiObat', 'kodeSatuSehat');
    }

    public function alergiMakanan()
    {
        return $this->belongsTo(MasterAlergi::class, 'kodeAlergiMakanan', 'kodeSatuSehat');
    }

    public function alergiUdara()
    {
        return $this->belongsTo(MasterAlergi::class, 'kodeAlergiUdara', 'kodeSatuSehat');
    }
}
