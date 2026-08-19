<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SetupProvinsi extends Model
{
    protected $table = 'setup_prop';
    protected $primaryKey = 'NO_PROP';
    public $incrementing = false;
    protected $keyType = 'string';

    public function pasien() {
        return $this->belongsTo(SimpusPasien::class, 'NO_PROP', 'NO_PROP');
    }
    //
}
