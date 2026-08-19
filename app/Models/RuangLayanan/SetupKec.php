<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SetupKec extends Model
{
    protected $table = 'setup_kec';
    protected $primaryKey = 'NO_KEC';

     public function SimpusPasien()
    {
        return $this->hasMany(SetupKec::class, 'NO_KEC', 'NO_KEC');
    }
}
