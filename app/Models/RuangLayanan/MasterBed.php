<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterBed extends Model
{
    protected $table = 'master_bed';
    protected $primaryKey = 'id_bed';
    public $timestamps = false;
    protected $fillable = [
        'id_kamar',
        'nama_bed',
        'status',
        'digunakan',
    ];
    public function kamar()
    {
        return $this->belongsTo(MasterKamar::class, 'id_kamar', 'id_kamar');
    }
}
