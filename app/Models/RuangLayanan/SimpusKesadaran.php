<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusKesadaran extends Model
{
    protected $table = 'simpus_kesadaran';
    protected $primaryKey = 'kdSadar';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kdSadar',
        'nmSadar',
    ];
}
