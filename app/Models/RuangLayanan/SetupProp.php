<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SetupProp extends Model
{
    protected $table = 'setup_prop';
    protected $primaryKey = 'NO_PROP';
     public function SimpusPasien()
    {
        return $this->hasMany(SetupProp::class, 'NO_PROP: ', 'NO_PROP');
    }
}
