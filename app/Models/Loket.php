<?php

namespace App\Models;

use App\Models\RuangLayanan\SimpusPelayanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loket extends Model
{
    protected $table = 'simpus_loket';
    protected $primaryKey = 'idLoket';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID untuk idLoket jika belum ada
            if (empty($model->idLoket)) {
                $model->idLoket = (string) Str::uuid();
            }
        });
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasienId', 'ID');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'kdPoli', 'kdPoli');
    }

    public function unitDetail()
    {
        return $this->belongsTo(UnitDetail::class, 'unitId', 'id_detail');
    }

    // Relasi ke pelayanan
    public function pelayanan()
    {
        return $this->hasMany(SimpusPelayanan::class, 'loketId', 'idLoket');
    }
}
