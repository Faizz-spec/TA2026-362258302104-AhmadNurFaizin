<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusPelayanan extends Model
{
    protected $table = 'simpus_pelayanan';
    protected $primaryKey = 'idpelayanan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'idpelayanan',
        'tglPelayanan',
        'pelIdSebelum',
        'loketId',
        'kdPoli',
        'kdKegiatanPel',
        'kunjSakitPel',
        'kdStatusPulang',
        'kdStatusPasienAskep',
        'tujuanPoli',
        'tujuanKdKeg',
        'tujuanKunjSakit',
        'tglPindah',
        'sudahDilayani',
        'kdTacc',
        'alasanTacc',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
        'noKunjungan',
        'id_encounter',
        'tenagaMedis',
        'startTime',
        'progressTime',
        'endTIme',
        'responPutEncounter',
    ];
    public function SimpusLoket()
    {
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
       public function SimpusPoli(){
        return $this->belongsTo(SimpusPoliFKTP::class, 'kdPoli', 'kdPoli');
    }
       public function StatusPulang()
    {
        return $this->belongsTo(StatusPulang::class, 'kdStatusPulang', 'kdStatusPulang');
    }

    public function poliTujuan(){

        return $this->belongsTo(SimpusPoliFKTP::class, 'tujuanPoli', 'kdPoli');
    }


}
