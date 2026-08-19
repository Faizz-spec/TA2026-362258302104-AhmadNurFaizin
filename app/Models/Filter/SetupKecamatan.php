<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SetupKecamatan extends Model
{
    protected $table = 'setup_kec';
    protected $primaryKey = 'NO_KEC';
    public $incrementing = false;
    protected $keyType = 'string';

    public function pasien() {
        return $this->belongsTo(SimpusPasien::class, 'NO_KEC', 'NO_KEC');
    }
}
