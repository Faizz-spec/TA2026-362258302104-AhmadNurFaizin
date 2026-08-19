<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'setup_prop';
    protected $primaryKey = 'NO_PROP';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['NO_PROP', 'NAMA_PROP'];

    public function kabupatens()
    {
        return $this->hasMany(Kabupaten::class, 'NO_PROP', 'NO_PROP');
    }
}
