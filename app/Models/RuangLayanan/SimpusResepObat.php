<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusResepObat extends Model
{
    protected $table = 'simpus_resep_obat';

    protected $primaryKey = 'id_resep';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_resep',
        'kode_resep',
        'loketId',
        'pelayananId',
        'kategori',
        'nama_puyer',
        'jumlah_puyer',
        'dosis_pakai_puyer',
        'tiapJam',
        'waktu',
        'kondisi',
        'diambil',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
        'keperluan',
        'namaPasien',
        'unit_details',
        'tgl_pengeluaran',
    ];

    public function DetailResepObat(){
        return $this->hasMany(SimpusDetailResepObat::class, 'resep_id', 'id_resep');
    }
    public function Loket(){
        return $this->belongsTo(SimpusLoket::class, 'loketId', 'idLoket');
    }
 
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function ($resep) {
    //         $resep->SimpusDetailResepObat()->delete();
    //     });
    // }
}
