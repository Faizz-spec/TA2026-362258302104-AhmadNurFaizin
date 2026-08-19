<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterKamar extends Model
{
    protected $table = 'master_kamar';
    protected $primaryKey = 'id_kamar';
    public $timestamps = false;
    protected $fillable = [
        'nama_kamar',
        'seq',
        'biaya',
        'pusk_id',
    ];
    public function beds()
    {
        return $this->hasMany(MasterBed::class, 'id_kamar', 'id_kamar');
    }
}
