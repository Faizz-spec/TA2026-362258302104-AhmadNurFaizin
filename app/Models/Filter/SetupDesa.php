<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SetupDesa extends Model
{
    protected $table = 'setup_kel';
    protected $primaryKey = 'NO_KEL';
    public $incrementing = false;
    protected $keyType = 'string';

    public function pasien() {
        return $this->belongsTo(SimpusPasien::class, 'NO_KEL', 'NO_KEL');
    }
    //
}
