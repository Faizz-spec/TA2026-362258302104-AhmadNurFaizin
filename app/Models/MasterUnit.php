<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUnit extends Model
{
    use HasFactory;

    protected $table = 'data_master_unit';
    protected $primaryKey = 'id_kategori';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori',
        'kategori',
    ];

    // Relasi ke unit details
    public function unitDetails()
    {
        return $this->hasMany(UnitDetail::class, 'id_kategori', 'id_kategori');
    }

    // Scope untuk exclude kategori 1
    public function scopeExcludeKategori1($query)
    {
        return $query->where('id_kategori', '<>', 1);
    }
}
