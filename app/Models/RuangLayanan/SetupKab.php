<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SetupKab extends Model
{
    protected $table = 'setup_kab';
    protected $primaryKey = 'NO_KAB';

    public function SimpusPasien()
    {
        return $this->hasMany(SetupKab::class, 'NO_KAB', 'NO_KAB');
    }
}
