<?php

namespace App\Models;

use App\Models\UnitProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitDetail extends Model
{
    use HasFactory;

    protected $table = 'data_master_unit_detail';
    protected $primaryKey = 'id_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail',
        'id_kategori',
        'id_unit',
        'nama_unit',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function masterUnit()
    {
        return $this->belongsTo(MasterUnit::class, 'id_kategori', 'id_kategori');
    }

    // Relasi ke unit profile
    public function unitProfile()
    {
        return $this->belongsTo(UnitProfile::class, 'id_unit', 'unit_id');
    }

    // Scope aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }

    // Scope by unit
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('id_unit', $unitId);
    }
}
