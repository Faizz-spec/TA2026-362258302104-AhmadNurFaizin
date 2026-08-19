<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusAnamnesa extends Model
{
    protected $table = 'simpus_anamnesa';
    protected $primaryKey = 'idAnamnesa';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'idAnamnesa',
        'loketId',
        'kondisi',
        'terapi',
        'kdSadar',
        'sistole',
        'diastole',
        'suhu',
        'beratBadan',
        'tinggiBadan',
        'lingkarTangan',
        'lingkarPerut',
        'tinggiLutut',
        'imt',
        'imtKet',
        'respRate',
        'heartRate',
        'catatan',
        'thoraxJantung',
        'thoraxJantungKet',
        'thoraxPulmo',
        'thoraxPulmoKet',
        'abdomanAtas',
        'abdomanAtasKet',
        'abdomanBawah',
        'abdomanBawahket',
        'extrimitasAtas',
        'extrimitasAtasKet',
        'extrimitasBawah',
        'extrimitasBawahKet',
        'kepala',
        'kepalaKet',
        'mata',
        'mataKet',
        'telinga',
        'telingaKet',
        'leher',
        'leherKet',
        'gravida',
        'partus',
        'abortus',
        'tinggiFundusUteri',
        'denyutJantungJanin',
        'letakJanin',
        'tanggalHpht',
        'tanggalHpl',
        'perkusi',
        'druk',
        'palpasi',
        'luxasi',
        'vitalitas',
        'alergi',
        'statusPasien',
        'tenagaMedis',
        'tenagaMedisAskep',
        'createdDate',
        'CreatedBy',
        'modifiedDate',
        'modifiedBy',
        'ranap',
        'tglAnamnesa',
        'rapidTes',
        'igm',
        'igg',
        'keluhan',
        'keluhanTambahan',
        'riwayatPenyakitSekarang',
        'riwayatPenyakitDahulu',
        'riwayatPenyakitKeluarga',
        'terapiYangPernahDijalani',
        'obatSeringDigunakan',
        'obatSeringDikonsumsi',
        'keadaanUmum',
        'idObservasiHr',
        'idObservasiRr',
        'idObservasiSistole',
        'idObservasiDiastole',
        'idObservasiSuhu'
    ];
    public function loket()
    {
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
      public function kesadaran()
    {
        return $this->belongsTo(SimpusKesadaran::class, 'kdSadar', 'kdSadar');
    }
}
