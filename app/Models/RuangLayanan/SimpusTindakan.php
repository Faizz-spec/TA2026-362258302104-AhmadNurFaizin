<?php

namespace App\Models\RuangLayanan;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SimpusTindakan extends Model
{
    protected $table = 'simpus_tindakan';
    protected $primaryKey = 'idTindakan';
    public $timestamps = false;

    protected $fillable = [
        'permohonanId',
        'pemeriksaanId',
        'kdTindakan',
        'nmTindakan',
        'nmTindakanInd',
        'peraturan',
        'harga',
        'bayar',
        'kdPoli',
        'kdPoliLab',
        'loketId',
        'idPelayanan',
        'keterangan',
        'ketGigi',
        'nilaiLab',
        'statusNilaiKritis',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
        'tglTindakan',
        'kategori',
        'deskripsi',
        'id_procedure'
    ];
    public function SimpusPoli()
    {
        return $this->belongsTo(SimpusPoliFKTP::class, 'kdPoli', 'kdPoli');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }

    public function parameteruji()
    {
        return $this->belongsTo(ParameterUji::class, 'kdTindakan', 'kode_parameter');
    }

}

