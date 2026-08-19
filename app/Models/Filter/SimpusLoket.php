<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SimpusLoket extends Model
{
    protected $table = 'simpus_loket';
    protected $primaryKey = 'idLoket';
    public $incrementing = false;
    protected $keyType = 'string';
    // protected $timestamps = false;

    public function pasien()
    {
        return $this->belongsTo(SimpusPasien::class, 'pasienId', 'ID');
    }
    public function anamnesa()
    {
        return $this->hasOne(SimpusAnamnesa::class, 'loketId', 'idLoket');
    }
    public function obat()
    {
        return $this->hasMany(SimpusPakaiObat::class, 'loketId', 'idLoket');
    }
    public function kunjungan()
    {
        return $this->hasOne(SimpusKunjungan::class, 'pasien_id', 'pasienId');
    }
    //
}
