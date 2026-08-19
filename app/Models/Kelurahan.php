<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = 'setup_kel';
    protected $primaryKey = 'NO_KEL';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = ['NO_PROP', 'NO_KAB', 'NO_KEC', 'NO_KEL', 'NAMA_KEL'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'NO_KEC', 'NO_KEC')
            ->where('NO_PROP', $this->NO_PROP)
            ->where('NO_KAB', $this->NO_KAB);
    }
}
