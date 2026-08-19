<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SetupKel extends Model
{
    protected $table = 'setup_kel';
    protected $primaryKey = 'NO_KEL';
    public function SimpusPasien()
    {
        return $this->hasMany(SetupKel::class, 'NO_KEL', 'NO_KEL');
    }
}
