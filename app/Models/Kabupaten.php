<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'setup_kab';
    protected $primaryKey = 'NO_KAB';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['NO_PROP', 'NO_KAB', 'NAMA_KAB'];

    // Relasi ke provinsi
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'NO_PROP', 'NO_PROP');
    }

    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'NO_KAB', 'NO_KAB')
            ->where('NO_PROP', $this->NO_PROP);
    }
}
