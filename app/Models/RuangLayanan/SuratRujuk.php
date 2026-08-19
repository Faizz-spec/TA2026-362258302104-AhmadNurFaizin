<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SuratRujuk extends Model
{
    protected $table = 'surat_rujukan';
    protected $primaryKey = 'id_surat_rujukan';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'tgl_rujuk',
        'no_surat',
        'id_pelayanan',
        'no_hp',
        'created_date',
        'created_by',
        'modified_date',
        'modified_by',
        'kdppk',
        'kdPoliRujLan',
        'tenagaMedis',
        'nama_unit',
        'alamat',
        'respon_id',
    ];
    public function tenagaMedis()
    {
        return $this->belongsTo(MasterDokter::class, 'tenagaMedis', 'idDokter');
    }
    public function provider()
    {
        return $this->belongsTo(SimpusProvider::class, 'kdppk', 'kdProvider');
    }
    public function poliRujukan()
    {
        return $this->belongsTo(SimpusPoliFKTL::class, 'kdPoliRujLan', 'kdPoli');
    }
     public function pelayanan()
    {
        return $this->belongsTo(SimpusPelayanan::class, 'id_pelayanan','idpelayanan');
    }
}
