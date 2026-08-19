<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SetupKabupaten extends Model
{
    protected $table = 'setup_kab';
    protected $primaryKey = 'NO_KAB';
    public $incrementing = false;
    protected $keyType = 'string';

    public function pasien() {
        return $this->belongsTo(SimpusPasien::class, 'NO_KAB', 'NO_KAB');
    }
    //
}
