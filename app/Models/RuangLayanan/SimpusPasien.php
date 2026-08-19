<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusPasien extends Model
{

    protected $table = 'simpus_pasien';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'IHS_NUMBER',
        'NO_MR',
        'SEQ',
        'PUSK_ID',
        'NO_MR_LAMA',
        'NIK',
        'NAMA_LGKP',
        'JENIS_KLMIN',
        'TMPT_LHR',
        'TGL_LHR',
        'AKTA_LHR',
        'NO_AKTA_LHR',
        'KEL_UMUR',
        'GOL_DRH',
        'AGAMA',
        'STAT_KWN',
        'AKTA_KWN',
        'NO_AKTA_KWN',
        'TGL_KWN',
        'STAT_HBKEL',
        'KLAIN_FSK',
        'PNYDNG_CCT',
        'PDDK_AKH',
        'JENIS_PKRJN',
        'NIK_IBU',
        'NAMA_LGKP_IBU',
        'NIK_AYAH',
        'NAMA_LGKP_AYAH',
        'NO_KK',
        'NO_PROP',
        'NO_KAB',
        'NO_KEC',
        'NO_KEL',
        'ALAMAT',
        'NO_RT',
        'NO_RW',
        'created',
        'created_by',
        'modified',
        'modified_by',
        'RegNo',
        'noKartu',
        'kdProvider',
        'PHONE',
        'ALERGI',
        'ACTIVE',
        'keterangan_ihs',
    ];

    public function SimpusLoket()
    {
        return $this->hasMany(SimpusLoket::class, 'pasienId', 'ID');
    }

    public function SetupKel()
    {
        return $this->belongsTo(SetupKel::class, 'NO_KEL', 'NO_KEL');
    }
    
    public function SetupKec()
    {
        return $this->belongsTo(SetupKec::class, 'NO_KEC', 'NO_KEC');
    }
    public function SetupKab()
    {
        return $this->belongsTo(SetupKab::class, 'NO_KAB', 'NO_KAB');
    }
    public function SetupProp()
    {
        return $this->belongsTo(SetupProp::class, 'NO_PROP', 'NO_PROP');
    }

    public function pkrjnmaster(){

        return $this->belongsTo(PkrjnMaster::class, 'JENIS_PKRJN', 'NO');

    }
}
