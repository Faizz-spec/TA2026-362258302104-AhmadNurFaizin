<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'setup_kec';
    protected $primaryKey = 'NO_KEC';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = ['NO_PROP', 'NO_KAB', 'NO_KEC', 'NAMA_KEC'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'NO_KAB', 'NO_KAB')
            ->where('NO_PROP', $this->NO_PROP);
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'NO_KEC', 'NO_KEC')
            ->where('NO_PROP', $this->NO_PROP)
            ->where('NO_KAB', $this->NO_KAB);
    }
}
